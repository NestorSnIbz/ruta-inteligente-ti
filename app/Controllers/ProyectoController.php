<?php

final class ProyectoController
{
    public function index(): void
    {
        // Protege el módulo: solo usuarios autenticados pueden gestionar proyectos.
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $error = Session::getFlash('error');
        $success = Session::getFlash('success');

        $format = strtolower((string) ($_GET['format'] ?? ''));
        $wantsJson = $format === 'json' || str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');
        $recentOnly = ((string) ($_GET['recent'] ?? '')) === '1';

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $totalProyectos = 0;
        $totalPages = 1;

        $search = trim((string) ($_GET['q'] ?? ''));
        $sort = (string) ($_GET['sort'] ?? 'recent');

        $sortOptions = [
            'recent' => 'id_proyecto.desc',
            'oldest' => 'id_proyecto.asc',
            'name_asc' => 'nombre.asc',
            'name_desc' => 'nombre.desc',
        ];

        $order = $sortOptions[$sort] ?? $sortOptions['recent'];

        try {
            $supabase = new SupabaseClient();
            $idPersona = (int) ($authUser['id_persona'] ?? 0);
            $ids = [];
            $miembroRows = ProyectoMiembro::listProyectoIdsByPersona($supabase, $idPersona);
            foreach ($miembroRows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $pid = (int) ($row['id_proyecto'] ?? 0);
                if ($pid > 0) {
                    $ids[$pid] = true;
                }
            }

            $creadorRows = Proyecto::listByCreador($supabase, $idPersona);
            foreach ($creadorRows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $pid = (int) ($row['id_proyecto'] ?? 0);
                if ($pid > 0) {
                    $ids[$pid] = true;
                }
            }

            $idList = array_keys($ids);
            $totalProyectos = Proyecto::countByIds($supabase, $idList, $search);
            $totalPages = max(1, (int) ceil($totalProyectos / $perPage));
            $page = min($page, $totalPages);
            $offset = ($page - 1) * $perPage;

            if ($wantsJson && $recentOnly) {
                $rows = Proyecto::listByIdsPaged($supabase, $idList, 3, 0, 'id_proyecto.desc');
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(
                    [
                        'ok' => true,
                        'projects' => array_map(
                            fn ($p) => [
                                'id_proyecto' => (int) ($p['id_proyecto'] ?? 0),
                                'nombre' => (string) ($p['nombre'] ?? ''),
                            ],
                            array_values(array_filter($rows, fn ($p) => is_array($p) && (int) ($p['id_proyecto'] ?? 0) > 0))
                        ),
                    ],
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                );
                exit;
            }

            $proyectos = Proyecto::listByIdsPaged($supabase, $idList, $perPage, $offset, $order, $search);
            $proyectos = $this->attachProjectTokens($proyectos);
        } catch (Throwable $e) {
            $proyectos = [];
            $error = $error ?: $this->friendlySupabaseError($e, 'No se pudo cargar la lista de proyectos.');
            if ($wantsJson && $recentOnly) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'error' => 'No se pudieron cargar los proyectos.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
        }

        require dirname(__DIR__) . '/Views/proyectos/index.php';
    }

    public function createForm(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $error = Session::getFlash('error');
        $success = Session::getFlash('success');
        $proyectos = [];

        try {
            $supabase = new SupabaseClient();
            $memberRows = ProyectoMiembro::listProyectoIdsByPersona($supabase, (int) ($authUser['id_persona'] ?? 0));
            $idList = [];
            foreach ($memberRows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $id = (int) ($row['id_proyecto'] ?? 0);
                if ($id > 0) {
                    $idList[] = $id;
                }
            }
            $idList = array_values(array_unique($idList));
            if (!empty($idList)) {
                $proyectos = Proyecto::listByIdsPaged($supabase, $idList, 10, 0, 'recent', '');
                $proyectos = $this->attachProjectTokens($proyectos);
            }
        } catch (Throwable $e) {
            $proyectos = [];
        }

        require dirname(__DIR__) . '/Views/proyectos/nuevo-proyecto.php';
    }

    public function store(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        // Validación mínima antes de insertar en la tabla proyecto.
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        if ($nombre === '' || mb_strlen($nombre, 'UTF-8') < 3) {
            Session::flash('error', 'El nombre del plan estratégico es obligatorio (mínimo 3 caracteres).');
            $this->redirect('/nuevo-proyecto.php');
        }

        try {
            $supabase = new SupabaseClient();
            $idProyecto = Proyecto::create($supabase, (int) $authUser['id_persona'], $nombre);
            try {
                ProyectoMiembro::createCreador($supabase, (int) $idProyecto, (int) $authUser['id_persona']);
            } catch (Throwable $e) {
                if ($this->isDebug()) {
                    error_log('[proyecto_store] No se pudo registrar creador en proyecto_miembro. id_proyecto=' . (int) $idProyecto . ' id_persona=' . (int) $authUser['id_persona'] . ' error=' . $e->getMessage());
                }
            }
        } catch (Throwable $e) {
            Session::flash('error', $this->friendlySupabaseError($e, 'No se pudo crear el plan estratégico.'));
            $this->redirect('/nuevo-proyecto.php');
        }

        Session::flash('success', 'Plan estratégico creado correctamente.');
        $token = $this->issueProjectToken($idProyecto);
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token));
    }

    public function show(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $error = Session::getFlash('error');
        $success = Session::getFlash('success');

        $token = trim((string) ($_GET['t'] ?? ''));
        $partial = trim((string) ($_GET['partial'] ?? ''));
        $section = trim((string) ($_GET['section'] ?? 'overview'));
        $export = trim((string) ($_GET['export'] ?? ''));
        $isExportOverviewPdf = $export === 'overview_pdf';
        $allowedSections = ['overview', 'mision', 'vision', 'valores', 'objetivos', 'cadena', 'perfil_competitivo', 'pest', 'estrategias', 'came', 'bgg'];
        $requestedSection = $partial !== '' ? $partial : ($section !== '' ? $section : 'overview');
        if (!in_array($requestedSection, $allowedSections, true)) {
            $requestedSection = 'overview';
        }
        $renderOnlySection = ($partial !== '' && !$isExportOverviewPdf) ? $requestedSection : '';
        $initialPanel = $requestedSection;
        $idProyecto = 0;

        if ($renderOnlySection !== '' && $token === '') {
            http_response_code(400);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Proyecto inválido.';
            exit;
        }

        if ($token !== '') {
            $idProyecto = $this->projectIdFromToken($token);
            if ($idProyecto <= 0) {
                if ($renderOnlySection !== '') {
                    http_response_code(401);
                    header('Content-Type: text/plain; charset=utf-8');
                    echo 'Enlace inválido o expirado.';
                    exit;
                }
                Session::flash('error', 'Enlace inválido o expirado. Regresa a la lista de proyectos.');
                $this->redirect('/proyectos.php');
            }
        } else {
            $idProyecto = (int) ($_GET['id'] ?? 0);
            if ($idProyecto <= 0) {
                Session::flash('error', 'Proyecto inválido.');
                $this->redirect('/proyectos.php');
            }
        }

        try {
            $supabase = new SupabaseClient();
            $idPersona = (int) ($authUser['id_persona'] ?? 0);
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, $idPersona);
            if ($proyecto === null) {
                if ($renderOnlySection !== '') {
                    http_response_code(403);
                    header('Content-Type: text/plain; charset=utf-8');
                    echo 'No tienes acceso a este proyecto.';
                    exit;
                }
                Session::flash('error', 'No tienes acceso a este proyecto.');
                $this->redirect('/proyectos.php');
            }

            $isCreador = $this->isCreadorProyecto($proyecto, $idPersona);

            $dataSection = $renderOnlySection !== '' ? $renderOnlySection : 'overview';
            if ($isExportOverviewPdf) {
                $dataSection = 'overview';
            }
            $mision = null;
            $vision = null;
            $valores = [];
            $misionTexto = '';
            $visionTexto = '';
            $overviewConclusionTexto = '';

            if ($dataSection === 'overview' || $dataSection === 'mision') {
                $mision = Mision::findByProyecto($supabase, $idProyecto);
                $misionTexto = is_array($mision) ? (string) ($mision['descripcion'] ?? '') : '';
            }
            if ($dataSection === 'overview' || $dataSection === 'vision') {
                $vision = Vision::findByProyecto($supabase, $idProyecto);
                $visionTexto = is_array($vision) ? (string) ($vision['descripcion'] ?? '') : '';
            }
            if ($dataSection === 'overview' || $dataSection === 'valores') {
                $valores = Valor::listByProyecto($supabase, $idProyecto);
            }
            if ($dataSection === 'overview') {
                try {
                    $planConclusion = PlanEstrategicoConclusion::findByProyecto($supabase, $idProyecto);
                    $overviewConclusionTexto = is_array($planConclusion) ? trim((string) ($planConclusion['descripcion'] ?? '')) : '';
                } catch (Throwable $e) {
                    $overviewConclusionTexto = '';
                }
            }

            $miembros = [];
            try {
                if ($dataSection === 'overview') {
                    $miembrosRows = ProyectoMiembro::listByProyectoWithPersona($supabase, $idProyecto);
                    if (empty($miembrosRows)) {
                        $miembrosRows = ProyectoMiembro::listByProyecto($supabase, $idProyecto);
                    }
                    $ids = [];
                    foreach ($miembrosRows as $row) {
                        if (!is_array($row)) {
                            continue;
                        }
                        $pid = (int) ($row['id_persona'] ?? 0);
                        if ($pid > 0) {
                            $ids[$pid] = true;
                        }
                    }
                    $creadorId = (int) ($proyecto['creador_id'] ?? 0);
                    if ($creadorId > 0) {
                        $ids[$creadorId] = true;
                    }

                    $personas = [];
                    foreach ($miembrosRows as $row) {
                        if (!is_array($row)) {
                            continue;
                        }
                        $p = $row['persona'] ?? null;
                        if (!is_array($p)) {
                            continue;
                        }
                        $pid = (int) ($row['id_persona'] ?? 0);
                        if ($pid > 0) {
                            $personas[] = ['id_persona' => $pid, 'nombre' => $p['nombre'] ?? null, 'email' => $p['email'] ?? null];
                        }
                    }
                    $byId = [];
                    foreach ($personas as $p) {
                        if (!is_array($p)) {
                            continue;
                        }
                        $pid = (int) ($p['id_persona'] ?? 0);
                        if ($pid > 0) {
                            $byId[$pid] = $p;
                        }
                    }
                    $missingIds = [];
                    foreach (array_keys($ids) as $pid) {
                        if (!isset($byId[$pid])) {
                            $missingIds[] = (int) $pid;
                        }
                    }
                    if (!empty($missingIds)) {
                        $personasExtra = Persona::listByIds($supabase, $missingIds);
                        foreach ($personasExtra as $p) {
                            if (!is_array($p)) {
                                continue;
                            }
                            $pid = (int) ($p['id_persona'] ?? 0);
                            if ($pid > 0) {
                                $byId[$pid] = $p;
                            }
                        }
                    }

                    if ($creadorId > 0 && isset($byId[$creadorId])) {
                        $creator = $byId[$creadorId];
                        $miembros[] = [
                            'id_persona' => $creadorId,
                            'nombre' => (string) ($creator['nombre'] ?? ''),
                            'email' => (string) ($creator['email'] ?? ''),
                            'rol' => 'CREADOR',
                        ];
                    }

                    foreach ($miembrosRows as $row) {
                        if (!is_array($row)) {
                            continue;
                        }
                        $pid = (int) ($row['id_persona'] ?? 0);
                        if ($pid <= 0) {
                            continue;
                        }
                        if ($pid === $creadorId) {
                            continue;
                        }
                        $persona = $byId[$pid] ?? null;
                        $miembros[] = [
                            'id_persona' => $pid,
                            'nombre' => (string) (is_array($persona) ? ($persona['nombre'] ?? '') : ''),
                            'email' => (string) (is_array($persona) ? ($persona['email'] ?? '') : ''),
                            'rol' => (string) (($row['rol'] ?? '') ?: 'INVITADO'),
                        ];
                    }
                }
            } catch (Throwable $e) {
                $miembros = [];
            }
        } catch (Throwable $e) {
            Session::flash('error', $this->friendlySupabaseError($e, 'No se pudo cargar el proyecto.'));
            $this->redirect('/proyectos.php');
        }

        $objetivosEstrategicos = [];
        $objetivosEspecificosByEstrategico = [];
        $objetivosError = '';
        $cadenaPreguntas = [];
        $cadenaRespuestas = [];
        $cadenaCalc = [
            'sum' => 0,
            'valid' => 0,
            'count' => 0,
            'missing' => 0,
            'potential' => null,
        ];
        $fodaFortalezas = [];
        $fodaDebilidades = [];
        $bcgFortalezas = [];
        $bcgDebilidades = [];
        $pcOportunidades = [];
        $pcAmenazas = [];
        $pestOportunidades = [];
        $pestAmenazas = [];
        $fodaCruzadaFactors = [];
        $fodaCruzadaAnswers = [];
        $fodaCruzadaCalc = [
            'ready' => false,
            'counts' => [
                'fortalezas' => 0,
                'debilidades' => 0,
                'oportunidades' => 0,
                'amenazas' => 0,
            ],
            'total_cells' => 0,
            'answered' => 0,
            'missing' => 0,
            'complete' => false,
            'matrices' => [],
            'summary' => [],
            'predominant' => null,
            'executive_conclusion' => null,
        ];
        $cameAcciones = ['C' => [], 'A' => [], 'M' => [], 'E' => []];
        $cameCalc = [
            'counts' => ['C' => 0, 'A' => 0, 'M' => 0, 'E' => 0],
            'total_actions' => 0,
            'categories_used' => 0,
            'empty' => true,
        ];
        $cameFactors = [
            'groups' => [
                'FORTALEZA' => [],
                'DEBILIDAD' => [],
                'OPORTUNIDAD' => [],
                'AMENAZA' => [],
            ],
        ];
        $cadenaOverview = [];
        $bcgOverview = [];
        $perfilOverview = [];
        $pestOverview = [];
        $fodaOverview = [];

        $shouldLoadObjetivos = ($renderOnlySection === '' || $renderOnlySection === 'overview' || $renderOnlySection === 'objetivos');
        if ($shouldLoadObjetivos) {
            try {
                $objetivosEstrategicos = ObjetivoEstrategico::listByProyecto($supabase, $idProyecto);
                $objetivosEstrategicos = $this->attachObjetivoEstrategicoTokens($objetivosEstrategicos);

                $objetivosEspecificos = ObjetivoEspecifico::listByProyecto($supabase, $idProyecto);
                $objetivosEspecificosByEstrategico = $this->groupObjetivosEspecificosByEstrategicoWithTokens($objetivosEspecificos);

                $objetivosEstrategicos = $this->attachEspecificosCountToObjetivosEstrategicos($objetivosEstrategicos, $objetivosEspecificosByEstrategico);
            } catch (Throwable $e) {
                $objetivosError = $this->isDebug() ? ('No se pudieron cargar los objetivos. Detalle: ' . $e->getMessage()) : 'No se pudieron cargar los objetivos.';
            }
        }

        $shouldLoadOverviewAnalytics = ($renderOnlySection === '' || $renderOnlySection === 'overview' || $isExportOverviewPdf);
        if ($shouldLoadOverviewAnalytics) {
            $headers = $this->supabaseRestHeaders($supabase);

            try {
                $res = $supabase->request(
                    'GET',
                    '/rest/v1/cadena_valor_resultado',
                    [
                        'select' => 'suma,potencial,updated_at',
                        'id_proyecto' => 'eq.' . $idProyecto,
                        'limit' => 1,
                    ],
                    $headers
                );

                $row = null;
                if (($res['status'] ?? 500) < 400 && is_array($res['data'] ?? null) && !empty($res['data'])) {
                    $row = is_array($res['data'][0] ?? null) ? $res['data'][0] : null;
                }

                $sum = is_array($row) ? (int) ($row['suma'] ?? 0) : null;
                $potential = is_array($row) && isset($row['potencial']) && is_numeric($row['potencial']) ? (float) $row['potencial'] : null;
                $updatedAt = is_array($row) ? (string) ($row['updated_at'] ?? '') : '';

                $badgeClass = 'bg-neutral-100 text-neutral-700';
                $statusLabel = 'Sin evaluación';
                $statusSub = 'Completa la Cadena de valor para ver resultados.';
                if ($potential !== null) {
                    $pct = max(0, min(100, (int) round($potential * 100)));
                    if ($pct <= 33) {
                        $badgeClass = 'bg-emerald-50 text-emerald-800 border border-emerald-200';
                        $statusLabel = 'Buen desempeño';
                        $statusSub = 'Bajo potencial de mejora.';
                    } elseif ($pct <= 66) {
                        $badgeClass = 'bg-amber-50 text-amber-900 border border-amber-200';
                        $statusLabel = 'Oportunidad moderada';
                        $statusSub = 'Potencial de mejora medio.';
                    } else {
                        $badgeClass = 'bg-red-50 text-red-800 border border-red-200';
                        $statusLabel = 'Alta oportunidad';
                        $statusSub = 'Alto potencial de mejora.';
                    }
                }

                $cadenaOverview = [
                    'sum' => $sum,
                    'potential' => $potential,
                    'updated_at' => $updatedAt,
                    'status_label' => $statusLabel,
                    'status_sub' => $statusSub,
                    'badge_class' => $badgeClass,
                ];
            } catch (Throwable $e) {
                $cadenaOverview = [];
            }

            try {
                $res = $supabase->request(
                    'GET',
                    '/rest/v1/bcg_producto',
                    [
                        'select' => 'id_producto_bcg,nombre,ventas_empresa,porcentaje_ventas,tcm,prm,clasificacion,updated_at',
                        'id_proyecto' => 'eq.' . $idProyecto,
                        'order' => 'porcentaje_ventas.desc',
                        'limit' => 200,
                    ],
                    $headers
                );

                $productos = (($res['status'] ?? 500) < 400 && is_array($res['data'] ?? null)) ? (array) $res['data'] : [];
                $counts = [
                    'ESTRELLA' => 0,
                    'VACA' => 0,
                    'INTERROGANTE' => 0,
                    'PERRO' => 0,
                ];
                $byClass = [
                    'ESTRELLA' => [],
                    'VACA' => [],
                    'INTERROGANTE' => [],
                    'PERRO' => [],
                ];

                $top = [];
                foreach ($productos as $p) {
                    if (!is_array($p)) {
                        continue;
                    }
                    $c = strtoupper(trim((string) ($p['clasificacion'] ?? '')));
                    if (!isset($counts[$c])) {
                        $c = 'PERRO';
                    }
                    $counts[$c] += 1;
                    $pn = trim((string) ($p['nombre'] ?? ''));
                    if ($pn !== '') {
                        $byClass[$c][] = $pn;
                    }
                    if (count($top) < 3) {
                        $top[] = [
                            'nombre' => (string) ($p['nombre'] ?? ''),
                            'clasificacion' => $c,
                            'porcentaje_ventas' => isset($p['porcentaje_ventas']) && is_numeric($p['porcentaje_ventas']) ? (float) $p['porcentaje_ventas'] : null,
                            'tcm' => isset($p['tcm']) && is_numeric($p['tcm']) ? (float) $p['tcm'] : null,
                            'prm' => isset($p['prm']) && is_numeric($p['prm']) ? (float) $p['prm'] : null,
                        ];
                    }
                }

                $total = count($productos);
                $statusLabel = $total > 0 ? 'Calculado' : 'Sin datos';
                $statusSub = $total > 0 ? 'Clasificación por producto disponible.' : 'Registra productos para ver el resumen.';
                $badgeClass = $total > 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-neutral-100 text-neutral-700';

                $bcgOverview = [
                    'total' => $total,
                    'counts' => $counts,
                    'by_class' => $byClass,
                    'top' => $top,
                    'status_label' => $statusLabel,
                    'status_sub' => $statusSub,
                    'badge_class' => $badgeClass,
                ];
            } catch (Throwable $e) {
                $bcgOverview = [];
            }

            try {
                $res = $supabase->request(
                    'GET',
                    '/rest/v1/perfil_competitivo_resultado',
                    [
                        'select' => 'total,conclusion_code,conclusion_text,updated_at',
                        'id_proyecto' => 'eq.' . $idProyecto,
                        'limit' => 1,
                    ],
                    $headers
                );

                $row = null;
                if (($res['status'] ?? 500) < 400 && is_array($res['data'] ?? null) && !empty($res['data'])) {
                    $row = is_array($res['data'][0] ?? null) ? $res['data'][0] : null;
                }

                $total = is_array($row) ? (int) ($row['total'] ?? 0) : null;
                $code = is_array($row) ? (int) ($row['conclusion_code'] ?? 0) : 0;
                $text = is_array($row) ? trim((string) ($row['conclusion_text'] ?? '')) : '';
                $updatedAt = is_array($row) ? (string) ($row['updated_at'] ?? '') : '';

                $badgeClass = 'bg-neutral-100 text-neutral-700';
                $statusLabel = 'Sin evaluación';
                $statusSub = 'Completa el Perfil competitivo para ver resultados.';
                if ($total !== null && $code >= 1 && $code <= 4) {
                    if ($code <= 1) {
                        $badgeClass = 'bg-red-50 text-red-800 border border-red-200';
                        $statusLabel = 'Entorno hostil';
                    } elseif ($code === 2) {
                        $badgeClass = 'bg-amber-50 text-amber-900 border border-amber-200';
                        $statusLabel = 'Entorno moderado';
                    } elseif ($code === 3) {
                        $badgeClass = 'bg-emerald-50 text-emerald-800 border border-emerald-200';
                        $statusLabel = 'Entorno favorable';
                    } else {
                        $badgeClass = 'bg-emerald-50 text-emerald-800 border border-emerald-200';
                        $statusLabel = 'Entorno muy favorable';
                    }
                    $statusSub = $text !== '' ? $text : 'Conclusión calculada.';
                }

                $perfilOverview = [
                    'total' => $total,
                    'conclusion_code' => $code,
                    'conclusion_text' => $text,
                    'updated_at' => $updatedAt,
                    'status_label' => $statusLabel,
                    'status_sub' => $statusSub,
                    'badge_class' => $badgeClass,
                ];
            } catch (Throwable $e) {
                $perfilOverview = [];
            }

            try {
                $res = $supabase->request(
                    'GET',
                    '/rest/v1/pest_resultado',
                    [
                        'select' => 'sociales_pct,medioambientales_pct,politicos_pct,economicos_pct,tecnologicos_pct,updated_at',
                        'id_proyecto' => 'eq.' . $idProyecto,
                        'limit' => 1,
                    ],
                    $headers
                );

                $row = null;
                if (($res['status'] ?? 500) < 400 && is_array($res['data'] ?? null) && !empty($res['data'])) {
                    $row = is_array($res['data'][0] ?? null) ? $res['data'][0] : null;
                }

                $pct = null;
                if (is_array($row)) {
                    $pct = [
                        'SOCIALES' => (int) ($row['sociales_pct'] ?? 0),
                        'MEDIOAMBIENTALES' => (int) ($row['medioambientales_pct'] ?? 0),
                        'POLITICOS' => (int) ($row['politicos_pct'] ?? 0),
                        'ECONOMICOS' => (int) ($row['economicos_pct'] ?? 0),
                        'TECNOLOGICOS' => (int) ($row['tecnologicos_pct'] ?? 0),
                    ];
                }
                $updatedAt = is_array($row) ? (string) ($row['updated_at'] ?? '') : '';

                $badgeClass = 'bg-neutral-100 text-neutral-700';
                $statusLabel = 'Sin evaluación';
                $statusSub = 'Completa el P.E.S.T. para ver resultados.';
                if (is_array($pct)) {
                    $badgeClass = 'bg-emerald-50 text-emerald-800 border border-emerald-200';
                    $statusLabel = 'Calculado';
                    $avg = (int) round(((int) $pct['SOCIALES'] + (int) $pct['MEDIOAMBIENTALES'] + (int) $pct['POLITICOS'] + (int) $pct['ECONOMICOS'] + (int) $pct['TECNOLOGICOS']) / 5);
                    $statusSub = 'Promedio de impacto: ' . (string) $avg . '%.';
                }

                $pestOverview = [
                    'pct' => $pct,
                    'updated_at' => $updatedAt,
                    'status_label' => $statusLabel,
                    'status_sub' => $statusSub,
                    'badge_class' => $badgeClass,
                ];
            } catch (Throwable $e) {
                $pestOverview = [];
            }

            try {
                $fuentes = [
                    'CADENA_VALOR_INTERNA' => 'Cadena de valor',
                    'AUTODIAGNOSTICO_BCG' => 'Matriz BCG',
                    'PERFIL_COMPETITIVO' => 'Perfil competitivo',
                    'PEST' => 'P.E.S.T.',
                ];
                $out = [];
                foreach ($fuentes as $fuente => $label) {
                    $rows = Foda::listByProyectoFuente($supabase, $idProyecto, $fuente);
                    $bucket = [
                        'label' => $label,
                        'FORTALEZA' => [],
                        'DEBILIDAD' => [],
                        'OPORTUNIDAD' => [],
                        'AMENAZA' => [],
                    ];
                    foreach ($rows as $r) {
                        if (!is_array($r)) {
                            continue;
                        }
                        $tipo = strtoupper(trim((string) ($r['tipo'] ?? '')));
                        $desc = trim((string) ($r['descripcion'] ?? ''));
                        if ($desc === '') {
                            continue;
                        }
                        if (!isset($bucket[$tipo])) {
                            continue;
                        }
                        $bucket[$tipo][] = $desc;
                    }
                    $out[$fuente] = $bucket;
                }
                $fodaOverview = $out;
            } catch (Throwable $e) {
                $fodaOverview = [];
            }

            try {
                $fodaFactorRows = FodaCruzada::listFactorRows($supabase, $idProyecto);
                $fodaCruzadaFactors = FodaCruzada::buildFactorSet($fodaFactorRows);
                $fodaCruzadaAnswers = FodaCruzada::listEvaluacionesByProyecto($supabase, $idProyecto);
                $fodaCruzadaCalc = FodaCruzada::compute($fodaCruzadaFactors, $fodaCruzadaAnswers);
            } catch (Throwable $e) {
                $fodaCruzadaFactors = [];
                $fodaCruzadaAnswers = [];
                $fodaCruzadaCalc = [
                    'ready' => false,
                    'counts' => [
                        'fortalezas' => 0,
                        'debilidades' => 0,
                        'oportunidades' => 0,
                        'amenazas' => 0,
                    ],
                    'total_cells' => 0,
                    'answered' => 0,
                    'missing' => 0,
                    'complete' => false,
                    'matrices' => [],
                    'summary' => [],
                    'predominant' => null,
                    'executive_conclusion' => null,
                ];
            }

            try {
                $cameAcciones = Came::listAccionesByProyecto($supabase, $idProyecto);
                $cameCalc = Came::compute($cameAcciones);
            } catch (Throwable $e) {
                $cameAcciones = ['C' => [], 'A' => [], 'M' => [], 'E' => []];
                $cameCalc = Came::compute($cameAcciones);
            }
        }

        if ($renderOnlySection === 'cadena') {
            try {
                CadenaValor::ensureSeeded($supabase);
                $cadenaPreguntas = CadenaValor::listPreguntas($supabase);
                $cadenaRespuestas = CadenaValor::listRespuestasByProyecto($supabase, $idProyecto);
                $cadenaCalc = CadenaValor::compute($cadenaPreguntas, $cadenaRespuestas);
            } catch (Throwable $e) {
            }

            try {
                $rows = Foda::listByProyectoFuente($supabase, $idProyecto, 'CADENA_VALOR_INTERNA');
                foreach ($rows as $r) {
                    if (!is_array($r)) {
                        continue;
                    }
                    $tipo = (string) ($r['tipo'] ?? '');
                    $desc = trim((string) ($r['descripcion'] ?? ''));
                    if ($desc === '') {
                        continue;
                    }
                    if ($tipo === 'FORTALEZA') {
                        $fodaFortalezas[] = $desc;
                    } elseif ($tipo === 'DEBILIDAD') {
                        $fodaDebilidades[] = $desc;
                    }
                }
            } catch (Throwable $e) {
            }
        }

        if ($renderOnlySection === 'perfil_competitivo') {
            try {
                PerfilCompetitivo::ensureSeeded($supabase);
                $perfilFactores = PerfilCompetitivo::listFactores($supabase);
                $perfilRespuestas = PerfilCompetitivo::listRespuestasByProyecto($supabase, $idProyecto);
                $perfilCalc = PerfilCompetitivo::compute($perfilFactores, $perfilRespuestas);
            } catch (Throwable $e) {
            }

            try {
                $rows = Foda::listByProyectoFuente($supabase, $idProyecto, 'PERFIL_COMPETITIVO');
                foreach ($rows as $r) {
                    if (!is_array($r)) {
                        continue;
                    }
                    $tipo = (string) ($r['tipo'] ?? '');
                    $desc = trim((string) ($r['descripcion'] ?? ''));
                    if ($desc === '') {
                        continue;
                    }
                    if ($tipo === 'OPORTUNIDAD') {
                        $pcOportunidades[] = $desc;
                    } elseif ($tipo === 'AMENAZA') {
                        $pcAmenazas[] = $desc;
                    }
                }
            } catch (Throwable $e) {
            }
        }

        if ($renderOnlySection === 'pest') {
            try {
                Pest::ensureSeeded($supabase);
                $pestPreguntas = Pest::listPreguntas($supabase);
                $pestRespuestas = Pest::listRespuestasByProyecto($supabase, $idProyecto);
                $pestCalc = Pest::compute($pestPreguntas, $pestRespuestas);
            } catch (Throwable $e) {
            }

            try {
                $rows = Foda::listByProyectoFuente($supabase, $idProyecto, 'PEST');
                foreach ($rows as $r) {
                    if (!is_array($r)) {
                        continue;
                    }
                    $tipo = (string) ($r['tipo'] ?? '');
                    $desc = trim((string) ($r['descripcion'] ?? ''));
                    if ($desc === '') {
                        continue;
                    }
                    if ($tipo === 'OPORTUNIDAD') {
                        $pestOportunidades[] = $desc;
                    } elseif ($tipo === 'AMENAZA') {
                        $pestAmenazas[] = $desc;
                    }
                }
            } catch (Throwable $e) {
            }
        }

        if ($renderOnlySection === 'estrategias') {
            try {
                $fodaFactorRows = FodaCruzada::listFactorRows($supabase, $idProyecto);
                $fodaCruzadaFactors = FodaCruzada::buildFactorSet($fodaFactorRows);
                $fodaCruzadaAnswers = FodaCruzada::listEvaluacionesByProyecto($supabase, $idProyecto);
                $fodaCruzadaCalc = FodaCruzada::compute($fodaCruzadaFactors, $fodaCruzadaAnswers);
            } catch (Throwable $e) {
            }
        }

        if ($renderOnlySection === 'came') {
            try {
                $cameAcciones = Came::listAccionesByProyecto($supabase, $idProyecto);
                $cameCalc = Came::compute($cameAcciones);
                $cameFactorRows = FodaCruzada::listFactorRows($supabase, $idProyecto);
                $cameFactors = FodaCruzada::buildFactorSet($cameFactorRows);
            } catch (Throwable $e) {
                $cameAcciones = ['C' => [], 'A' => [], 'M' => [], 'E' => []];
                $cameCalc = Came::compute($cameAcciones);
            }
        }

        if ($renderOnlySection === 'bgg') {
            try {
                $rows = Foda::listByProyectoFuente($supabase, $idProyecto, 'AUTODIAGNOSTICO_BCG');
                foreach ($rows as $r) {
                    if (!is_array($r)) {
                        continue;
                    }
                    $tipo = (string) ($r['tipo'] ?? '');
                    $desc = trim((string) ($r['descripcion'] ?? ''));
                    if ($desc === '') {
                        continue;
                    }
                    if ($tipo === 'FORTALEZA') {
                        $bcgFortalezas[] = $desc;
                    } elseif ($tipo === 'DEBILIDAD') {
                        $bcgDebilidades[] = $desc;
                    }
                }
            } catch (Throwable $e) {
            }
        }

        if ($token === '') {
            $token = $this->issueProjectToken($idProyecto);
            $query = [];
            if (isset($_GET['edit'])) {
                $query['edit'] = (string) $_GET['edit'];
            }
            if (isset($_GET['section'])) {
                $query['section'] = (string) $_GET['section'];
            }
            if (isset($_GET['oe_edit'])) {
                $query['oe_edit'] = (string) $_GET['oe_edit'];
            }
            if (isset($_GET['oesp_edit'])) {
                $query['oesp_edit'] = (string) $_GET['oesp_edit'];
            }
            $qs = http_build_query(array_filter($query, fn ($v) => $v !== ''), '', '&', PHP_QUERY_RFC3986);
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . ($qs ? ('&' . $qs) : ''));
        }

        $projectToken = $token;
        $edit = (string) ($_GET['edit'] ?? '');
        $oeEditToken = trim((string) ($_GET['oe_edit'] ?? ''));
        $oespEditToken = trim((string) ($_GET['oesp_edit'] ?? ''));

        if ($isExportOverviewPdf) {
            $fileProjectName = is_array($proyecto ?? null) ? (string) ($proyecto['nombre'] ?? '') : 'proyecto';
            $safeName = $this->safePdfFilename($fileProjectName);
            $stamp = date('Ymd-His');
            $filename = 'overview-' . ($safeName !== '' ? $safeName : 'proyecto') . '-' . $stamp . '.pdf';

            $pdf = $this->buildOverviewPdf(
                $fileProjectName,
                is_array($miembros ?? null) ? $miembros : [],
                $overviewConclusionTexto ?? '',
                $misionTexto ?? '',
                $visionTexto ?? '',
                is_array($valores ?? null) ? $valores : [],
                is_array($objetivosEstrategicos ?? null) ? $objetivosEstrategicos : [],
                is_array($objetivosEspecificosByEstrategico ?? null) ? $objetivosEspecificosByEstrategico : [],
                is_array($cadenaOverview ?? null) ? $cadenaOverview : [],
                is_array($bcgOverview ?? null) ? $bcgOverview : [],
                is_array($perfilOverview ?? null) ? $perfilOverview : [],
                is_array($pestOverview ?? null) ? $pestOverview : [],
                is_array($fodaOverview ?? null) ? $fodaOverview : [],
                is_array($fodaCruzadaCalc ?? null) ? $fodaCruzadaCalc : [],
                is_array($cameCalc ?? null) ? $cameCalc : [],
                is_array($cameAcciones ?? null) ? $cameAcciones : []
            );

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
            echo $pdf;
            exit;
        }

        if ($renderOnlySection !== '') {
            header('Content-Type: text/html; charset=utf-8');
            $panel = $renderOnlySection;
            require dirname(__DIR__) . '/Views/proyectos/detalle-panel.php';
            exit;
        }

        require dirname(__DIR__) . '/Views/proyectos/detalle-proyecto.php';
    }

    private function supabaseRestHeaders(SupabaseClient $supabase): array
    {
        $serverKey = $supabase->getServiceRoleKey();
        $apiKey = $serverKey ?: $supabase->getAnonKey();
        $authBearer = $serverKey ?: $supabase->getAnonKey();

        return [
            'apikey' => $apiKey,
            'Authorization' => 'Bearer ' . $authBearer,
        ];
    }

    private function buildOverviewPdf(
        string $projectName,
        array $miembros,
        string $overviewConclusion,
        string $mision,
        string $vision,
        array $valores,
        array $objetivosEstrategicos,
        array $objetivosEspecificosByEstrategico,
        array $cadenaOverview,
        array $bcgOverview,
        array $perfilOverview,
        array $pestOverview,
        array $fodaOverview,
        array $fodaCruzadaCalc,
        array $cameCalc,
        array $cameAcciones
    ): string {
        $lines = [];
        $empresaNombre = trim($projectName) !== '' ? trim($projectName) : 'Sin nombre';
        $miembrosList = [];
        foreach ($miembros as $miembro) {
            if (!is_array($miembro)) {
                continue;
            }
            $nombre = trim((string) ($miembro['nombre'] ?? ''));
            $email = trim((string) ($miembro['email'] ?? ''));
            $rol = strtoupper(trim((string) ($miembro['rol'] ?? '')));
            $label = $nombre !== '' ? $nombre : $email;
            if ($label === '') {
                continue;
            }
            if ($rol === 'CREADOR') {
                $label .= ' (Creador)';
            } elseif ($rol !== '') {
                $label .= ' (' . ucfirst(strtolower($rol)) . ')';
            }
            $miembrosList[] = $label;
        }
        $miembrosList = array_values(array_unique($miembrosList));

        $lines[] = ['font' => 'F2', 'size' => 18, 'text' => 'Resumen ejecutivo del plan estratégico (' . $empresaNombre . ')'];
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Nombre de la empresa: ' . $empresaNombre];
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Fecha de elaboración: ' . date('d/m/Y')];
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Emprendedores/promotores: ' . (!empty($miembrosList) ? implode(', ', $miembrosList) : 'Sin registros.')];
        $lines[] = ['spacer' => 10];

        $lines = array_merge($lines, $this->pdfSection('Misión', $mision !== '' ? $mision : 'Sin registros.'));
        $lines = array_merge($lines, $this->pdfSection('Visión', $vision !== '' ? $vision : 'Sin registros.'));

        $valoresList = [];
        foreach ($valores as $v) {
            if (!is_array($v)) continue;
            $txt = trim((string) ($v['descripcion'] ?? ''));
            if ($txt !== '') $valoresList[] = $txt;
        }
        $valRows = [];
        foreach ($valoresList as $i => $txt) {
            $valRows[] = [(string) ($i + 1), (string) $txt];
        }
        $lines[] = ['font' => 'F2', 'size' => 13, 'text' => 'Valores'];
        $lines[] = ['spacer' => 4];
        $lines[] = [
            'table' => true,
            'columns' => [
                ['header' => '#', 'width' => 36, 'align' => 'C'],
                ['header' => 'Valor', 'width' => 468, 'align' => 'L'],
            ],
            'rows' => $valRows,
        ];
        $lines[] = ['spacer' => 8];

        $lines[] = ['spacer' => 6];
        $lines[] = ['font' => 'F2', 'size' => 13, 'text' => 'Objetivos estratégicos'];
        $lines[] = ['spacer' => 4];
        $misionLabel = trim($mision) !== '' ? trim($mision) : 'Sin misión registrada.';
        $objectiveGroups = [];
        if (!empty($objetivosEstrategicos)) {
            foreach ($objetivosEstrategicos as $obj) {
                if (!is_array($obj)) continue;
                $idObjEst = (int) ($obj['id_objetivo_est'] ?? 0);
                $descEst = trim((string) ($obj['descripcion'] ?? ''));
                if ($descEst === '') continue;

                $esps = $objetivosEspecificosByEstrategico[$idObjEst] ?? [];
                $esps = is_array($esps) ? $esps : [];
                $espList = [];
                foreach ($esps as $esp) {
                    if (!is_array($esp)) continue;
                    $t = trim((string) ($esp['descripcion'] ?? ''));
                    if ($t !== '') $espList[] = $t;
                }
                $objectiveGroups[] = [
                    'estrategico' => $descEst,
                    'especificos' => empty($espList) ? ['Sin objetivos específicos.'] : $espList,
                ];
            }
        }
        $lines[] = [
            'objectives_table' => true,
            'mission' => $misionLabel,
            'groups' => $objectiveGroups,
        ];
        $lines[] = ['spacer' => 8];

        $lines[] = ['spacer' => 10];
        $lines[] = ['font' => 'F2', 'size' => 13, 'text' => 'Cadena de Valor (resumen)'];
        $cSum = $cadenaOverview['sum'] ?? null;
        $cPotential = $cadenaOverview['potential'] ?? null;
        $cStatus = (string) ($cadenaOverview['status_label'] ?? 'Sin evaluación');
        $cPotentialText = ($cPotential !== null && is_numeric($cPotential)) ? number_format((float) $cPotential, 2, '.', '') : '—';
        $cPotentialPct = ($cPotential !== null && is_numeric($cPotential)) ? ((string) round(((float) $cPotential) * 100) . '%') : '';
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Suma: ' . (($cSum === null) ? '—' : (string) ((int) $cSum))];
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Potencial de mejora: ' . $cPotentialText . ($cPotentialPct !== '' ? (' (' . $cPotentialPct . ')') : '')];
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Estado: ' . $cStatus];

        $lines[] = ['spacer' => 10];
        $lines[] = ['font' => 'F2', 'size' => 13, 'text' => 'Matriz BCG (resumen)'];
        $bTotal = (int) ($bcgOverview['total'] ?? 0);
        $bCounts = is_array($bcgOverview['counts'] ?? null) ? (array) $bcgOverview['counts'] : [];
        $bByClass = is_array($bcgOverview['by_class'] ?? null) ? (array) $bcgOverview['by_class'] : [];
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Productos: ' . (string) $bTotal];
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Estrella: ' . (string) ((int) ($bCounts['ESTRELLA'] ?? 0)) . ' | Vaca: ' . (string) ((int) ($bCounts['VACA'] ?? 0)) . ' | Interrogante: ' . (string) ((int) ($bCounts['INTERROGANTE'] ?? 0)) . ' | Perro: ' . (string) ((int) ($bCounts['PERRO'] ?? 0))];
        $lines[] = ['spacer' => 4];
        $bcgRows = [];
        foreach (['ESTRELLA' => 'Estrella', 'VACA' => 'Vaca', 'INTERROGANTE' => 'Interrogante', 'PERRO' => 'Perro'] as $key => $label) {
            $names = is_array($bByClass[$key] ?? null) ? array_values(array_filter(array_map('trim', (array) $bByClass[$key]))) : [];
            $bcgRows[] = [
                $label,
                (string) ((int) ($bCounts[$key] ?? 0)),
                empty($names) ? 'Sin productos clasificados.' : implode("\n", $names),
            ];
        }
        $lines[] = [
            'table' => true,
            'columns' => [
                ['header' => 'Clasificación', 'width' => 120, 'align' => 'L'],
                ['header' => 'Cantidad', 'width' => 72, 'align' => 'C'],
                ['header' => 'Productos', 'width' => 312, 'align' => 'L'],
            ],
            'rows' => $bcgRows,
        ];

        $bTop = is_array($bcgOverview['top'] ?? null) ? (array) $bcgOverview['top'] : [];
        $lines[] = ['spacer' => 4];
        $lines[] = ['font' => 'F2', 'size' => 11, 'text' => 'Top productos'];
        if (empty($bTop)) {
            $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Sin registros.'];
        } else {
            foreach ($bTop as $p) {
                if (!is_array($p)) continue;
                $pn = trim((string) ($p['nombre'] ?? ''));
                $pc = trim((string) ($p['clasificacion'] ?? ''));
                $pp = isset($p['porcentaje_ventas']) && is_numeric($p['porcentaje_ventas']) ? round(((float) $p['porcentaje_ventas']) * 100, 1) . '%' : '—';
                $label = ($pn !== '' ? $pn : 'Producto') . ' — ' . ($pc !== '' ? $pc : '—') . ' — Ventas: ' . $pp;
                $lines[] = ['font' => 'F1', 'size' => 11, 'text' => '- ' . $label];
            }
        }

        $lines[] = ['spacer' => 10];
        $lines[] = ['font' => 'F2', 'size' => 13, 'text' => 'Perfil Competitivo (resumen)'];
        $pcTotal = $perfilOverview['total'] ?? null;
        $pcCode = (int) ($perfilOverview['conclusion_code'] ?? 0);
        $pcText = trim((string) ($perfilOverview['conclusion_text'] ?? ''));
        $pcStatus = (string) ($perfilOverview['status_label'] ?? 'Sin evaluación');
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Total: ' . (($pcTotal === null) ? '—' : (string) ((int) $pcTotal))];
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Conclusión (' . ($pcCode > 0 ? (string) $pcCode : '—') . '): ' . ($pcText !== '' ? $pcText : $pcStatus)];

        $lines[] = ['spacer' => 10];
        $lines[] = ['font' => 'F2', 'size' => 13, 'text' => 'P.E.S.T. (resumen)'];
        $pPct = is_array($pestOverview['pct'] ?? null) ? (array) $pestOverview['pct'] : null;
        if (!is_array($pPct)) {
            $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Sin evaluación.'];
        } else {
            $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Sociales: ' . (string) ((int) ($pPct['SOCIALES'] ?? 0)) . '% | Medioambientales: ' . (string) ((int) ($pPct['MEDIOAMBIENTALES'] ?? 0)) . '%'];
            $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Políticos: ' . (string) ((int) ($pPct['POLITICOS'] ?? 0)) . '% | Económicos: ' . (string) ((int) ($pPct['ECONOMICOS'] ?? 0)) . '% | Tecnológicos: ' . (string) ((int) ($pPct['TECNOLOGICOS'] ?? 0)) . '%'];
        }

        $lines[] = ['spacer' => 10];
        $lines[] = ['font' => 'F2', 'size' => 13, 'text' => 'Análisis FODA'];
        $fuentes = [
            'CADENA_VALOR_INTERNA' => 'Cadena de valor',
            'AUTODIAGNOSTICO_BCG' => 'Matriz BCG',
            'PERFIL_COMPETITIVO' => 'Perfil competitivo',
            'PEST' => 'P.E.S.T.',
        ];
        $fodaGroups = [];
        foreach ($fuentes as $fuente => $label) {
            $block = is_array($fodaOverview[$fuente] ?? null) ? (array) $fodaOverview[$fuente] : [];
            $fort = is_array($block['FORTALEZA'] ?? null) ? (array) $block['FORTALEZA'] : [];
            $deb = is_array($block['DEBILIDAD'] ?? null) ? (array) $block['DEBILIDAD'] : [];
            $opp = is_array($block['OPORTUNIDAD'] ?? null) ? (array) $block['OPORTUNIDAD'] : [];
            $ame = is_array($block['AMENAZA'] ?? null) ? (array) $block['AMENAZA'] : [];
            if ($fuente === 'CADENA_VALOR_INTERNA' || $fuente === 'AUTODIAGNOSTICO_BCG') {
                $fodaGroups[] = [
                    'source' => $label,
                    'types' => [
                        ['label' => 'Fortalezas', 'items' => $fort],
                        ['label' => 'Debilidades', 'items' => $deb],
                    ],
                ];
                continue;
            }
            $fodaGroups[] = [
                'source' => $label,
                'types' => [
                    ['label' => 'Oportunidades', 'items' => $opp],
                    ['label' => 'Amenazas', 'items' => $ame],
                ],
            ];
        }
        $lines[] = [
            'foda_table' => true,
            'groups' => $fodaGroups,
        ];

        $lines[] = ['spacer' => 10];
        $lines[] = ['font' => 'F2', 'size' => 13, 'text' => 'Identificación de estrategia'];
        $estrategiasSummary = is_array($fodaCruzadaCalc['summary'] ?? null) ? (array) $fodaCruzadaCalc['summary'] : [];

        $maxTotal = null;
        $topRows = [];
        foreach ($estrategiasSummary as $row) {
            if (!is_array($row)) {
                continue;
            }
            $total = (int) ($row['total'] ?? 0);
            if ($maxTotal === null || $total > $maxTotal) {
                $maxTotal = $total;
                $topRows = [$row];
            } elseif ($total === $maxTotal) {
                $topRows[] = $row;
            }
        }

        if ($maxTotal === null || empty($topRows)) {
            $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Sin resultados acumulados.'];
        } else {
            $relationToText = [
                'FO' => 'Deberá adoptar estrategias de crecimiento.',
                'FA' => 'La empresa está preparada para enfrentarse a las amenazas.',
                'DA' => 'Se enfrenta a amenazas externas sin las fortalezas necesarias para luchar con la competencia.',
                'DO' => 'La empresa no puede aprovechar las oportunidades porque carece de preparación adecuada.',
            ];
            $texts = [];
            foreach ($topRows as $row) {
                $relation = trim((string) ($row['relation'] ?? ''));
                $texts[] = $relationToText[$relation] ?? '';
            }
            $texts = array_values(array_filter(array_unique(array_map('trim', $texts)), static fn ($t) => $t !== ''));
            if (empty($texts)) {
                $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Sin resultados acumulados.'];
            } elseif (count($texts) === 1) {
                $lines[] = ['font' => 'F1', 'size' => 11, 'text' => $texts[0]];
            } else {
                $lines[] = ['font' => 'F1', 'size' => 11, 'text' => implode(' | ', $texts)];
            }
        }

        $lines[] = ['spacer' => 10];
        $lines[] = ['font' => 'F2', 'size' => 13, 'text' => 'Matriz CAME (resumen)'];
        $cameCounts = is_array($cameCalc['counts'] ?? null) ? (array) $cameCalc['counts'] : [];
        $cameTotalActions = (int) ($cameCalc['total_actions'] ?? 0);
        $cameCategoriesUsed = (int) ($cameCalc['categories_used'] ?? 0);
        $lines[] = ['font' => 'F2', 'size' => 13, 'text' => 'Acciones competitivas'];
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Acciones registradas: ' . (string) $cameTotalActions];
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'Categorías utilizadas: ' . (string) $cameCategoriesUsed . '/4'];
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => 'C=' . (string) ((int) ($cameCounts['C'] ?? 0)) . ' | A=' . (string) ((int) ($cameCounts['A'] ?? 0)) . ' | M=' . (string) ((int) ($cameCounts['M'] ?? 0)) . ' | E=' . (string) ((int) ($cameCounts['E'] ?? 0))];
        $lines[] = ['font' => 'F1', 'size' => 11, 'text' => $cameTotalActions > 0 ? 'Estado: Con acciones registradas.' : 'Estado: Sin acciones registradas.'];

        $accionesCompetitivas = [];
        foreach (['C', 'A', 'M', 'E'] as $cat) {
            $rows = is_array($cameAcciones[$cat] ?? null) ? (array) $cameAcciones[$cat] : [];
            usort($rows, static function ($a, $b): int {
                $ap = is_array($a) ? (int) ($a['position'] ?? 0) : 0;
                $bp = is_array($b) ? (int) ($b['position'] ?? 0) : 0;
                return $ap <=> $bp;
            });
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $desc = trim((string) ($row['description'] ?? ''));
                if ($desc === '') {
                    continue;
                }
                $accionesCompetitivas[] = $desc;
            }
        }

        $actionColumns = [
            ['header' => '#', 'width' => 42, 'align' => 'C'],
            ['header' => 'Acción competitiva', 'width' => 462, 'align' => 'L'],
        ];
        $actionRows = [];
        if (empty($accionesCompetitivas)) {
            $actionRows[] = ['1', 'Sin acciones competitivas registradas.'];
        } else {
            foreach ($accionesCompetitivas as $index => $accion) {
                $actionRows[] = [(string) ($index + 1), $accion];
            }
        }
        $lines[] = ['spacer' => 4];
        $lines[] = [
            'table' => true,
            'columns' => $actionColumns,
            'rows' => $actionRows,
        ];

        $conclusionPdf = trim($overviewConclusion);
        if ($conclusionPdf === '') {
            $conclusionPdf = 'Aún no hay suficiente información para elaborar una conclusión general del plan estratégico. Complete la matriz FODA, la identificación de estrategia y las acciones competitivas para generar una conclusión más precisa.';
        }
        $lines[] = ['spacer' => 10];
        $lines = array_merge($lines, $this->pdfSection('Conclusión', $conclusionPdf));

        return $this->simplePdfFromLines($lines);
    }

    private function fodaRowsForTable(string $sourceLabel, string $tipoLabel, array $items): array
    {
        $clean = [];
        foreach ($items as $t) {
            $t = trim((string) $t);
            if ($t !== '') $clean[] = $t;
        }
        if (empty($clean)) {
            return [[$sourceLabel, $tipoLabel, 'Sin registros.']];
        }
        $rows = [];
        foreach ($clean as $txt) {
            $rows[] = [$sourceLabel, $tipoLabel, $txt];
        }
        return $rows;
    }

    private function pdfSection(string $title, string $body): array
    {
        $out = [];
        $out[] = ['font' => 'F2', 'size' => 13, 'text' => $title];
        $out[] = ['spacer' => 2];
        foreach ($this->wrapPdfText($body, 95) as $line) {
            $out[] = ['font' => 'F1', 'size' => 11, 'text' => $line];
        }
        $out[] = ['spacer' => 8];
        return $out;
    }

    private function pdfSectionList(string $title, array $items): array
    {
        $out = [];
        $out[] = ['font' => 'F2', 'size' => 13, 'text' => $title];
        $out[] = ['spacer' => 2];
        if (empty($items)) {
            $out[] = ['font' => 'F1', 'size' => 11, 'text' => 'Sin registros.'];
        } else {
            foreach ($items as $txt) {
                $txt = trim((string) $txt);
                if ($txt === '') continue;
                $wrapped = $this->wrapPdfText($txt, 90);
                if (empty($wrapped)) continue;
                $first = array_shift($wrapped);
                $out[] = ['font' => 'F1', 'size' => 11, 'text' => '• ' . $first];
                foreach ($wrapped as $rest) {
                    $out[] = ['font' => 'F1', 'size' => 11, 'indent' => 14, 'text' => $rest];
                }
            }
        }
        $out[] = ['spacer' => 8];
        return $out;
    }

    private function pdfBulletLines(array $items, int $indent): array
    {
        $out = [];
        $clean = [];
        foreach ($items as $t) {
            $t = trim((string) $t);
            if ($t !== '') $clean[] = $t;
        }
        if (empty($clean)) {
            $out[] = ['font' => 'F1', 'size' => 11, 'indent' => $indent, 'text' => '- Sin registros.'];
            return $out;
        }
        foreach ($clean as $txt) {
            $wrapped = $this->wrapPdfText($txt, 88);
            if (empty($wrapped)) continue;
            $first = array_shift($wrapped);
            $out[] = ['font' => 'F1', 'size' => 11, 'indent' => $indent, 'text' => '- ' . $first];
            foreach ($wrapped as $rest) {
                $out[] = ['font' => 'F1', 'size' => 11, 'indent' => $indent + 14, 'text' => $rest];
            }
        }
        return $out;
    }

    private function wrapPdfText(string $text, int $maxLen): array
    {
        $text = trim(preg_replace('/\s+/u', ' ', (string) $text));
        if ($text === '') return [];
        $words = preg_split('/\s+/u', $text) ?: [];
        $lines = [];
        $line = '';
        foreach ($words as $w) {
            $w = (string) $w;
            if ($line === '') {
                $line = $w;
                continue;
            }
            $candidate = $line . ' ' . $w;
            $len = function_exists('mb_strlen') ? mb_strlen($candidate, 'UTF-8') : strlen($candidate);
            if ($len <= $maxLen) {
                $line .= ' ' . $w;
            } else {
                $lines[] = $line;
                $line = $w;
            }
        }
        if ($line !== '') $lines[] = $line;
        return $lines;
    }

    private function simplePdfFromLines(array $lines): string
    {
        $pageWidth = 612;
        $pageHeight = 792;
        $marginX = 54;
        $marginTop = 54;
        $marginBottom = 54;
        $y = $pageHeight - $marginTop;
        $pages = [];
        $current = '';

        $lineHeight = 14;
        foreach ($lines as $row) {
            if (is_array($row) && array_key_exists('objectives_table', $row) && $row['objectives_table'] === true) {
                $mission = (string) ($row['mission'] ?? '');
                $groups = is_array($row['groups'] ?? null) ? (array) $row['groups'] : [];
                [$current, $y, $pages] = $this->pdfRenderObjectivesTable($current, $y, $pages, $mission, $groups, $pageWidth, $pageHeight, $marginX, $marginTop, $marginBottom);
                continue;
            }
            if (is_array($row) && array_key_exists('foda_table', $row) && $row['foda_table'] === true) {
                $groups = is_array($row['groups'] ?? null) ? (array) $row['groups'] : [];
                [$current, $y, $pages] = $this->pdfRenderFodaTable($current, $y, $pages, $groups, $pageWidth, $pageHeight, $marginX, $marginTop, $marginBottom);
                continue;
            }
            if (is_array($row) && array_key_exists('table', $row) && $row['table'] === true) {
                $columns = is_array($row['columns'] ?? null) ? (array) $row['columns'] : [];
                $rows = is_array($row['rows'] ?? null) ? (array) $row['rows'] : [];
                if (!empty($columns)) {
                    [$current, $y, $pages] = $this->pdfRenderTable($current, $y, $pages, $columns, $rows, $pageWidth, $pageHeight, $marginX, $marginTop, $marginBottom);
                }
                continue;
            }
            if (is_array($row) && array_key_exists('spacer', $row)) {
                $y -= (int) $row['spacer'];
                if ($y <= $marginBottom) {
                    $pages[] = $current;
                    $current = '';
                    $y = $pageHeight - $marginTop;
                }
                continue;
            }
            if (!is_array($row)) continue;
            $text = (string) ($row['text'] ?? '');
            $font = (string) ($row['font'] ?? 'F1');
            $size = (int) ($row['size'] ?? 11);
            $indent = (int) ($row['indent'] ?? 0);

            $y -= $lineHeight;
            if ($y <= $marginBottom) {
                $pages[] = $current;
                $current = '';
                $y = $pageHeight - $marginTop - $lineHeight;
            }

            $x = $marginX + $indent;
            $current .= $this->pdfTextCmd($x, $y, $font, $size, $text);
        }
        $pages[] = $current;

        $catalogId = 1;
        $pagesId = 2;
        $fontRegularId = 3;
        $fontBoldId = 4;
        $nextId = 5;

        $objs = [];
        $objs[$fontRegularId] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>";
        $objs[$fontBoldId] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>";

        $pageIds = [];
        foreach ($pages as $content) {
            $contentId = $nextId++;
            $pageId = $nextId++;
            $pageIds[] = $pageId;
            $stream = "stream\n" . $content . "endstream";
            $objs[$contentId] = "<< /Length " . strlen($content) . " >>\n" . $stream;
            $objs[$pageId] = "<< /Type /Page /Parent {$pagesId} 0 R /MediaBox [0 0 {$pageWidth} {$pageHeight}] /Resources << /Font << /F1 {$fontRegularId} 0 R /F2 {$fontBoldId} 0 R >> >> /Contents {$contentId} 0 R >>";
        }

        $kids = implode(' ', array_map(fn ($id) => $id . " 0 R", $pageIds));
        $objs[$pagesId] = "<< /Type /Pages /Kids [ {$kids} ] /Count " . count($pageIds) . " >>";
        $objs[$catalogId] = "<< /Type /Catalog /Pages {$pagesId} 0 R >>";

        $maxId = max(array_keys($objs));
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        $offsets[0] = 0;

        for ($id = 1; $id <= $maxId; $id++) {
            if (!isset($objs[$id])) continue;
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $objs[$id] . "\nendobj\n";
        }

        $xrefPos = strlen($pdf);
        $pdf .= "xref\n";
        $pdf .= "0 " . ($maxId + 1) . "\n";
        $pdf .= sprintf("%010d %05d f \n", 0, 65535);
        for ($id = 1; $id <= $maxId; $id++) {
            $off = $offsets[$id] ?? 0;
            $pdf .= sprintf("%010d %05d n \n", $off, 0);
        }
        $pdf .= "trailer\n";
        $pdf .= "<< /Size " . ($maxId + 1) . " /Root {$catalogId} 0 R >>\n";
        $pdf .= "startxref\n";
        $pdf .= $xrefPos . "\n";
        $pdf .= "%%EOF";

        return $pdf;
    }

    private function pdfTextCmd(int $x, int $y, string $font, int $size, string $text): string
    {
        $safe = $this->pdfEscapeString($this->toPdfWinAnsi($text));
        $font = ($font === 'F2') ? 'F2' : 'F1';
        $size = max(8, min(24, $size));
        return "BT /{$font} {$size} Tf {$x} {$y} Td ({$safe}) Tj ET\n";
    }

    private function pdfEscapeString(string $s): string
    {
        $s = str_replace(["\\", "(", ")", "\r", "\n"], ["\\\\", "\\(", "\\)", ' ', ' '], $s);
        return $s;
    }

    private function toPdfWinAnsi(string $s): string
    {
        $s = (string) $s;
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $s);
            if (is_string($converted)) {
                return $converted;
            }
        }
        return utf8_decode($s);
    }

    private function pdfRenderTable(
        string $current,
        int $y,
        array $pages,
        array $columns,
        array $rows,
        int $pageWidth,
        int $pageHeight,
        int $marginX,
        int $marginTop,
        int $marginBottom
    ): array {
        $x0 = $marginX;

        $fontHeader = 'F2';
        $fontBody = 'F1';
        $sizeHeader = 10;
        $sizeBody = 10;
        $pad = 3;
        $lh = 12;
        $stroke = "0 G 0.7 w\n";
        $fillHeader = "0.95 g\n";
        $resetGray = "0 g\n";

        $wrapCell = function (string $text, int $width, int $size) use ($pad): array {
            $text = (string) $text;
            $parts = preg_split("/\r\n|\n|\r/u", $text) ?: [];
            $out = [];
            $maxChars = max(8, (int) floor(($width - ($pad * 2)) / max(1, (int) round($size * 0.55))));
            foreach ($parts as $p) {
                $p = trim((string) $p);
                if ($p === '') {
                    $out[] = '';
                    continue;
                }
                foreach ($this->wrapPdfText($p, $maxChars) as $line) {
                    $out[] = $line;
                }
            }
            if (empty($out)) $out[] = '';
            return $out;
        };

        $renderRow = function (array $cells, string $font, int $size, bool $isHeader) use (
            &$current,
            &$y,
            &$pages,
            $columns,
            $x0,
            $pad,
            $lh,
            $marginBottom,
            $pageHeight,
            $marginTop,
            $stroke,
            $fillHeader,
            $resetGray,
            $wrapCell
        ) {
            $wrapped = [];
            $maxLines = 1;
            foreach ($columns as $idx => $c) {
                $w = (int) ($c['width'] ?? 0);
                $t = (string) ($cells[$idx] ?? '');
                $lines = $wrapCell($t, $w, $size);
                $wrapped[$idx] = $lines;
                $maxLines = max($maxLines, count($lines));
            }
            $rowHeight = ($maxLines * $lh) + ($pad * 2);

            if (($y - $rowHeight) <= $marginBottom) {
                $pages[] = $current;
                $current = '';
                $y = $pageHeight - $marginTop;
            }

            $yTop = $y;
            $yBottom = $yTop - $rowHeight;

            $x = $x0;
            $current .= $stroke;
            foreach ($columns as $idx => $c) {
                $w = (int) ($c['width'] ?? 0);
                if ($isHeader) {
                    $current .= $fillHeader;
                    $current .= $this->pdfRectCmd($x, (int) $yBottom, $w, (int) $rowHeight, true);
                    $current .= $resetGray;
                    $current .= $this->pdfRectCmd($x, (int) $yBottom, $w, (int) $rowHeight, false);
                } else {
                    $current .= $this->pdfRectCmd($x, (int) $yBottom, $w, (int) $rowHeight, false);
                }

                $align = strtoupper(trim((string) ($c['align'] ?? 'L')));
                $lines = $wrapped[$idx] ?? [''];
                $textY = (int) round($yTop - $pad - $size);
                foreach ($lines as $line) {
                    $line = (string) $line;
                    $tx = $x + $pad;
                    if ($align === 'C') {
                        $tx = $x + (int) floor($w / 2) - (int) floor((min(40, strlen($line)) * (int) round($size * 0.28)));
                    } elseif ($align === 'R') {
                        $tx = $x + $w - $pad - (int) floor((min(60, strlen($line)) * (int) round($size * 0.55)));
                    }
                    $current .= $this->pdfTextCmd((int) $tx, (int) $textY, $font, $size, $line);
                    $textY -= $lh;
                }

                $x += $w;
            }

            $y = (int) $yBottom;
        };

        $headerCells = [];
        foreach ($columns as $c) {
            $headerCells[] = (string) ($c['header'] ?? '');
        }
        $renderRow($headerCells, $fontHeader, $sizeHeader, true);

        if (empty($rows)) {
            $empty = array_fill(0, count($columns), '');
            if (count($empty) >= 2) {
                $empty[1] = 'Sin registros.';
            } elseif (count($empty) === 1) {
                $empty[0] = 'Sin registros.';
            }
            $renderRow($empty, $fontBody, $sizeBody, false);
            return [$current, $y, $pages];
        }

        foreach ($rows as $r) {
            if (!is_array($r)) continue;
            $cells = [];
            foreach ($columns as $idx => $_c) {
                $cells[] = (string) ($r[$idx] ?? '');
            }
            $renderRow($cells, $fontBody, $sizeBody, false);
        }

        return [$current, $y, $pages];
    }

    private function pdfRectCmd(int $x, int $y, int $w, int $h, bool $fill): string
    {
        $op = $fill ? 'f' : 'S';
        return "{$x} {$y} {$w} {$h} re {$op}\n";
    }

    private function pdfRenderObjectivesTable(
        string $current,
        int $y,
        array $pages,
        string $mission,
        array $groups,
        int $pageWidth,
        int $pageHeight,
        int $marginX,
        int $marginTop,
        int $marginBottom
    ): array {
        $columns = [
            ['header' => 'Misión', 'width' => 168],
            ['header' => 'Objetivo estratégico', 'width' => 168],
            ['header' => 'Objetivo específico', 'width' => 168],
        ];

        $segments = [];
        foreach ($groups as $group) {
            if (!is_array($group)) {
                continue;
            }
            $estrategico = trim((string) ($group['estrategico'] ?? ''));
            if ($estrategico === '') {
                continue;
            }
            $especificos = is_array($group['especificos'] ?? null) ? (array) $group['especificos'] : [];
            $clean = [];
            foreach ($especificos as $item) {
                $item = trim((string) $item);
                if ($item !== '') {
                    $clean[] = $item;
                }
            }
            if (empty($clean)) {
                $clean[] = 'Sin objetivos específicos.';
            }
            $segments[] = [
                'estrategico' => $estrategico,
                'especificos' => $clean,
            ];
        }

        if (empty($segments)) {
            return $this->pdfRenderTable(
                $current,
                $y,
                $pages,
                $columns,
                [['Sin misión registrada.', 'Sin objetivos estratégicos.', 'Sin objetivos específicos.']],
                $pageWidth,
                $pageHeight,
                $marginX,
                $marginTop,
                $marginBottom
            );
        }

        $fontHeader = 'F2';
        $fontBody = 'F1';
        $sizeHeader = 10;
        $sizeBody = 10;
        $pad = 3;
        $lh = 12;
        $stroke = "0 G 0.7 w\n";
        $fillHeader = "0.95 g\n";
        $resetGray = "0 g\n";
        $colWidths = array_map(fn ($c) => (int) $c['width'], $columns);
        $totalWidth = array_sum($colWidths);

        $wrapForWidth = function (string $text, int $width, int $size) use ($pad): array {
            $text = trim((string) $text);
            if ($text === '') {
                return [''];
            }
            $parts = preg_split("/\r\n|\n|\r/u", $text) ?: [];
            $out = [];
            $maxChars = max(8, (int) floor(($width - ($pad * 2)) / max(1, (int) round($size * 0.55))));
            foreach ($parts as $part) {
                $part = trim((string) $part);
                if ($part === '') {
                    $out[] = '';
                    continue;
                }
                foreach ($this->wrapPdfText($part, $maxChars) as $line) {
                    $out[] = $line;
                }
            }
            return empty($out) ? [''] : $out;
        };

        $renderHeader = function () use (&$current, &$y, &$pages, $columns, $marginX, $pageHeight, $marginTop, $marginBottom, $pad, $lh, $fontHeader, $sizeHeader, $stroke, $fillHeader, $resetGray, $wrapForWidth) {
            $wrapped = [];
            $maxLines = 1;
            foreach ($columns as $idx => $c) {
                $lines = $wrapForWidth((string) ($c['header'] ?? ''), (int) $c['width'], $sizeHeader);
                $wrapped[$idx] = $lines;
                $maxLines = max($maxLines, count($lines));
            }
            $rowHeight = ($maxLines * $lh) + ($pad * 2);
            if (($y - $rowHeight) <= $marginBottom) {
                $pages[] = $current;
                $current = '';
                $y = $pageHeight - $marginTop;
            }
            $yTop = $y;
            $yBottom = $yTop - $rowHeight;
            $x = $marginX;
            $current .= $stroke;
            foreach ($columns as $idx => $c) {
                $w = (int) $c['width'];
                $current .= $fillHeader;
                $current .= $this->pdfRectCmd($x, (int) $yBottom, $w, (int) $rowHeight, true);
                $current .= $resetGray;
                $current .= $this->pdfRectCmd($x, (int) $yBottom, $w, (int) $rowHeight, false);
                $textY = (int) round($yTop - $pad - $sizeHeader);
                foreach ($wrapped[$idx] as $line) {
                    $current .= $this->pdfTextCmd($x + $pad, $textY, $fontHeader, $sizeHeader, (string) $line);
                    $textY -= $lh;
                }
                $x += $w;
            }
            $y = (int) $yBottom;
        };

        $fitTextBlock = function (array $lines, int $availableHeight) use ($lh, $pad): int {
            $needed = (count($lines) * $lh) + ($pad * 2);
            return max($availableHeight, $needed);
        };

        $drawTextBlock = function (int $x, int $yTop, int $width, int $height, string $text, bool $centerVertically = false) use (&$current, $pad, $lh, $fontBody, $sizeBody, $wrapForWidth) {
            $lines = $wrapForWidth($text, $width, $sizeBody);
            $contentHeight = (count($lines) * $lh);
            $textY = $yTop - $pad - $sizeBody;
            if ($centerVertically) {
                $free = max(0, $height - ($contentHeight + ($pad * 2)));
                $textY = $yTop - $pad - $sizeBody - (int) floor($free / 2);
            }
            foreach ($lines as $line) {
                $current .= $this->pdfTextCmd($x + $pad, $textY, $fontBody, $sizeBody, (string) $line);
                $textY -= $lh;
            }
        };

        $groupIndex = 0;
        $specificIndex = 0;

        while ($groupIndex < count($segments)) {
            $renderHeader();
            $rowsOnPage = [];
            $pageTopY = $y;
            $remainingHeight = $y - $marginBottom;

            while ($groupIndex < count($segments)) {
                $group = $segments[$groupIndex];
                $specifics = $group['especificos'];
                $addedAnyForGroup = false;

                while ($specificIndex < count($specifics)) {
                    $specificText = (string) $specifics[$specificIndex];
                    $specificLines = $wrapForWidth($specificText, $colWidths[2], $sizeBody);
                    $rowHeight = (count($specificLines) * $lh) + ($pad * 2);
                    if (($remainingHeight - $rowHeight) < 0 && !empty($rowsOnPage)) {
                        break 2;
                    }
                    if (($remainingHeight - $rowHeight) < 0 && empty($rowsOnPage)) {
                        $rowHeight = max(24, min($remainingHeight, $rowHeight));
                    }
                    $rowsOnPage[] = [
                        'group_index' => $groupIndex,
                        'estrategico' => (string) $group['estrategico'],
                        'specifico' => $specificText,
                        'height' => $rowHeight,
                    ];
                    $remainingHeight -= $rowHeight;
                    $specificIndex++;
                    $addedAnyForGroup = true;
                }

                if ($specificIndex >= count($specifics)) {
                    $groupIndex++;
                    $specificIndex = 0;
                    continue;
                }

                if (!$addedAnyForGroup) {
                    break;
                }
            }

            if (empty($rowsOnPage)) {
                break;
            }

            $rowsByGroup = [];
            foreach ($rowsOnPage as $idx => $rowInfo) {
                $g = (int) $rowInfo['group_index'];
                if (!isset($rowsByGroup[$g])) {
                    $rowsByGroup[$g] = [];
                }
                $rowsByGroup[$g][] = $idx;
            }

            foreach ($rowsByGroup as $g => $rowIndexes) {
                $groupHeight = 0;
                foreach ($rowIndexes as $rowIdx) {
                    $groupHeight += (int) $rowsOnPage[$rowIdx]['height'];
                }
                $needed = $fitTextBlock($wrapForWidth((string) $rowsOnPage[$rowIndexes[0]]['estrategico'], $colWidths[1], $sizeBody), $groupHeight);
                if ($needed > $groupHeight) {
                    $extra = $needed - $groupHeight;
                    $last = $rowIndexes[count($rowIndexes) - 1];
                    $rowsOnPage[$last]['height'] += $extra;
                }
            }

            $pageBodyHeight = 0;
            foreach ($rowsOnPage as $rowInfo) {
                $pageBodyHeight += (int) $rowInfo['height'];
            }
            $neededMission = $fitTextBlock($wrapForWidth($mission, $colWidths[0], $sizeBody), $pageBodyHeight);
            if ($neededMission > $pageBodyHeight) {
                $extra = $neededMission - $pageBodyHeight;
                $last = count($rowsOnPage) - 1;
                $rowsOnPage[$last]['height'] += $extra;
                $pageBodyHeight += $extra;
            }

            $bodyTopY = $y;
            $current .= $stroke;
            $current .= $this->pdfRectCmd($marginX, (int) ($bodyTopY - $pageBodyHeight), $colWidths[0], (int) $pageBodyHeight, false);
            $drawTextBlock($marginX, $bodyTopY, $colWidths[0], (int) $pageBodyHeight, $mission, true);

            $xStrategic = $marginX + $colWidths[0];
            $xSpecific = $xStrategic + $colWidths[1];
            $groupStartY = $bodyTopY;
            foreach ($rowsByGroup as $g => $rowIndexes) {
                $groupHeight = 0;
                foreach ($rowIndexes as $rowIdx) {
                    $groupHeight += (int) $rowsOnPage[$rowIdx]['height'];
                }
                $current .= $this->pdfRectCmd($xStrategic, (int) ($groupStartY - $groupHeight), $colWidths[1], (int) $groupHeight, false);
                $drawTextBlock($xStrategic, $groupStartY, $colWidths[1], (int) $groupHeight, (string) $rowsOnPage[$rowIndexes[0]]['estrategico'], true);

                $rowY = $groupStartY;
                foreach ($rowIndexes as $rowIdx) {
                    $rowHeight = (int) $rowsOnPage[$rowIdx]['height'];
                    $current .= $this->pdfRectCmd($xSpecific, (int) ($rowY - $rowHeight), $colWidths[2], $rowHeight, false);
                    $drawTextBlock($xSpecific, $rowY, $colWidths[2], $rowHeight, (string) $rowsOnPage[$rowIdx]['specifico'], false);
                    $rowY -= $rowHeight;
                }

                $groupStartY -= $groupHeight;
            }

            $current .= $this->pdfRectCmd($marginX, (int) ($bodyTopY - $pageBodyHeight), $totalWidth, (int) $pageBodyHeight, false);
            $y = (int) ($bodyTopY - $pageBodyHeight);

            if ($groupIndex < count($segments)) {
                $pages[] = $current;
                $current = '';
                $y = $pageHeight - $marginTop;
            }
        }

        return [$current, $y, $pages];
    }

    private function pdfRenderFodaTable(
        string $current,
        int $y,
        array $pages,
        array $groups,
        int $pageWidth,
        int $pageHeight,
        int $marginX,
        int $marginTop,
        int $marginBottom
    ): array {
        $columns = [
            ['header' => 'Procedencia', 'width' => 132],
            ['header' => 'Tipo', 'width' => 120],
            ['header' => 'Descripción', 'width' => 252],
        ];

        $segments = [];
        foreach ($groups as $group) {
            if (!is_array($group)) {
                continue;
            }
            $source = trim((string) ($group['source'] ?? ''));
            if ($source === '') {
                continue;
            }
            $types = is_array($group['types'] ?? null) ? (array) $group['types'] : [];
            $typeSegments = [];
            foreach ($types as $type) {
                if (!is_array($type)) {
                    continue;
                }
                $label = trim((string) ($type['label'] ?? ''));
                if ($label === '') {
                    continue;
                }
                $items = is_array($type['items'] ?? null) ? (array) $type['items'] : [];
                $clean = [];
                foreach ($items as $item) {
                    $item = trim((string) $item);
                    if ($item !== '') {
                        $clean[] = $item;
                    }
                }
                if (empty($clean)) {
                    $clean[] = 'Sin registros.';
                }
                $typeSegments[] = [
                    'label' => $label,
                    'items' => $clean,
                ];
            }
            if (!empty($typeSegments)) {
                $segments[] = [
                    'source' => $source,
                    'types' => $typeSegments,
                ];
            }
        }

        if (empty($segments)) {
            return $this->pdfRenderTable(
                $current,
                $y,
                $pages,
                $columns,
                [['Sin registros.', '', '']],
                $pageWidth,
                $pageHeight,
                $marginX,
                $marginTop,
                $marginBottom
            );
        }

        $fontHeader = 'F2';
        $fontBody = 'F1';
        $sizeHeader = 10;
        $sizeBody = 10;
        $pad = 3;
        $lh = 12;
        $stroke = "0 G 0.7 w\n";
        $fillHeader = "0.95 g\n";
        $resetGray = "0 g\n";
        $colWidths = array_map(fn ($c) => (int) $c['width'], $columns);

        $wrapForWidth = function (string $text, int $width, int $size) use ($pad): array {
            $text = trim((string) $text);
            if ($text === '') {
                return [''];
            }
            $parts = preg_split("/\r\n|\n|\r/u", $text) ?: [];
            $out = [];
            $maxChars = max(8, (int) floor(($width - ($pad * 2)) / max(1, (int) round($size * 0.55))));
            foreach ($parts as $part) {
                $part = trim((string) $part);
                if ($part === '') {
                    $out[] = '';
                    continue;
                }
                foreach ($this->wrapPdfText($part, $maxChars) as $line) {
                    $out[] = $line;
                }
            }
            return empty($out) ? [''] : $out;
        };

        $renderHeader = function () use (&$current, &$y, &$pages, $columns, $marginX, $pageHeight, $marginTop, $marginBottom, $pad, $lh, $fontHeader, $sizeHeader, $stroke, $fillHeader, $resetGray, $wrapForWidth) {
            $wrapped = [];
            $maxLines = 1;
            foreach ($columns as $idx => $c) {
                $lines = $wrapForWidth((string) ($c['header'] ?? ''), (int) $c['width'], $sizeHeader);
                $wrapped[$idx] = $lines;
                $maxLines = max($maxLines, count($lines));
            }
            $rowHeight = ($maxLines * $lh) + ($pad * 2);
            if (($y - $rowHeight) <= $marginBottom) {
                $pages[] = $current;
                $current = '';
                $y = $pageHeight - $marginTop;
            }
            $yTop = $y;
            $yBottom = $yTop - $rowHeight;
            $x = $marginX;
            $current .= $stroke;
            foreach ($columns as $idx => $c) {
                $w = (int) $c['width'];
                $current .= $fillHeader;
                $current .= $this->pdfRectCmd($x, (int) $yBottom, $w, (int) $rowHeight, true);
                $current .= $resetGray;
                $current .= $this->pdfRectCmd($x, (int) $yBottom, $w, (int) $rowHeight, false);
                $textY = (int) round($yTop - $pad - $sizeHeader);
                foreach ($wrapped[$idx] as $line) {
                    $current .= $this->pdfTextCmd($x + $pad, $textY, $fontHeader, $sizeHeader, (string) $line);
                    $textY -= $lh;
                }
                $x += $w;
            }
            $y = (int) $yBottom;
        };

        $drawTextBlock = function (int $x, int $yTop, int $width, int $height, string $text, bool $centerVertically = false) use (&$current, $pad, $lh, $fontBody, $sizeBody, $wrapForWidth) {
            $lines = $wrapForWidth($text, $width, $sizeBody);
            $contentHeight = count($lines) * $lh;
            $textY = $yTop - $pad - $sizeBody;
            if ($centerVertically) {
                $free = max(0, $height - ($contentHeight + ($pad * 2)));
                $textY = $yTop - $pad - $sizeBody - (int) floor($free / 2);
            }
            foreach ($lines as $line) {
                $current .= $this->pdfTextCmd($x + $pad, $textY, $fontBody, $sizeBody, (string) $line);
                $textY -= $lh;
            }
        };

        $sourceIndex = 0;
        $typeIndex = 0;
        $itemIndex = 0;

        while ($sourceIndex < count($segments)) {
            $renderHeader();
            $rowsOnPage = [];
            $remainingHeight = $y - $marginBottom;

            while ($sourceIndex < count($segments)) {
                $source = $segments[$sourceIndex];
                $types = $source['types'];
                $addedAnyForSource = false;

                while ($typeIndex < count($types)) {
                    $type = $types[$typeIndex];
                    $items = $type['items'];
                    $addedAnyForType = false;

                    while ($itemIndex < count($items)) {
                        $itemText = (string) $items[$itemIndex];
                        $itemLines = $wrapForWidth($itemText, $colWidths[2], $sizeBody);
                        $rowHeight = (count($itemLines) * $lh) + ($pad * 2);

                        if (($remainingHeight - $rowHeight) < 0 && !empty($rowsOnPage)) {
                            break 3;
                        }
                        if (($remainingHeight - $rowHeight) < 0 && empty($rowsOnPage)) {
                            $rowHeight = max(24, min($remainingHeight, $rowHeight));
                        }

                        $rowsOnPage[] = [
                            'source_index' => $sourceIndex,
                            'source' => (string) $source['source'],
                            'type_index' => $typeIndex,
                            'type' => (string) $type['label'],
                            'description' => $itemText,
                            'height' => $rowHeight,
                        ];
                        $remainingHeight -= $rowHeight;
                        $itemIndex++;
                        $addedAnyForType = true;
                        $addedAnyForSource = true;
                    }

                    if ($itemIndex >= count($items)) {
                        $typeIndex++;
                        $itemIndex = 0;
                        continue;
                    }

                    if (!$addedAnyForType) {
                        break 2;
                    }
                }

                if ($typeIndex >= count($types)) {
                    $sourceIndex++;
                    $typeIndex = 0;
                    $itemIndex = 0;
                    continue;
                }

                if (!$addedAnyForSource) {
                    break;
                }
            }

            if (empty($rowsOnPage)) {
                break;
            }

            $rowsBySource = [];
            $rowsBySourceType = [];
            foreach ($rowsOnPage as $idx => $rowInfo) {
                $s = (int) $rowInfo['source_index'];
                $t = (int) $rowInfo['type_index'];
                $rowsBySource[$s][] = $idx;
                $rowsBySourceType[$s . ':' . $t][] = $idx;
            }

            foreach ($rowsBySourceType as $key => $rowIndexes) {
                $typeHeight = 0;
                foreach ($rowIndexes as $rowIdx) {
                    $typeHeight += (int) $rowsOnPage[$rowIdx]['height'];
                }
                $typeLabel = (string) $rowsOnPage[$rowIndexes[0]]['type'];
                $needed = max($typeHeight, (count($wrapForWidth($typeLabel, $colWidths[1], $sizeBody)) * $lh) + ($pad * 2));
                if ($needed > $typeHeight) {
                    $extra = $needed - $typeHeight;
                    $last = $rowIndexes[count($rowIndexes) - 1];
                    $rowsOnPage[$last]['height'] += $extra;
                }
            }

            foreach ($rowsBySource as $s => $rowIndexes) {
                $sourceHeight = 0;
                foreach ($rowIndexes as $rowIdx) {
                    $sourceHeight += (int) $rowsOnPage[$rowIdx]['height'];
                }
                $sourceLabel = (string) $rowsOnPage[$rowIndexes[0]]['source'];
                $needed = max($sourceHeight, (count($wrapForWidth($sourceLabel, $colWidths[0], $sizeBody)) * $lh) + ($pad * 2));
                if ($needed > $sourceHeight) {
                    $extra = $needed - $sourceHeight;
                    $last = $rowIndexes[count($rowIndexes) - 1];
                    $rowsOnPage[$last]['height'] += $extra;
                }
            }

            $bodyTopY = $y;
            $xSource = $marginX;
            $xType = $xSource + $colWidths[0];
            $xDesc = $xType + $colWidths[1];
            $current .= $stroke;

            $sourceStartY = $bodyTopY;
            foreach ($rowsBySource as $s => $rowIndexes) {
                $sourceHeight = 0;
                foreach ($rowIndexes as $rowIdx) {
                    $sourceHeight += (int) $rowsOnPage[$rowIdx]['height'];
                }
                $current .= $this->pdfRectCmd($xSource, (int) ($sourceStartY - $sourceHeight), $colWidths[0], (int) $sourceHeight, false);
                $drawTextBlock($xSource, $sourceStartY, $colWidths[0], (int) $sourceHeight, (string) $rowsOnPage[$rowIndexes[0]]['source'], true);

                $typeStartY = $sourceStartY;
                $typeGroupsForSource = [];
                foreach ($rowIndexes as $rowIdx) {
                    $typeKey = $rowsOnPage[$rowIdx]['source_index'] . ':' . $rowsOnPage[$rowIdx]['type_index'];
                    $typeGroupsForSource[$typeKey][] = $rowIdx;
                }

                foreach ($typeGroupsForSource as $typeRows) {
                    $typeHeight = 0;
                    foreach ($typeRows as $rowIdx) {
                        $typeHeight += (int) $rowsOnPage[$rowIdx]['height'];
                    }
                    $current .= $this->pdfRectCmd($xType, (int) ($typeStartY - $typeHeight), $colWidths[1], (int) $typeHeight, false);
                    $drawTextBlock($xType, $typeStartY, $colWidths[1], (int) $typeHeight, (string) $rowsOnPage[$typeRows[0]]['type'], true);

                    $rowY = $typeStartY;
                    foreach ($typeRows as $rowIdx) {
                        $rowHeight = (int) $rowsOnPage[$rowIdx]['height'];
                        $current .= $this->pdfRectCmd($xDesc, (int) ($rowY - $rowHeight), $colWidths[2], $rowHeight, false);
                        $drawTextBlock($xDesc, $rowY, $colWidths[2], $rowHeight, (string) $rowsOnPage[$rowIdx]['description'], false);
                        $rowY -= $rowHeight;
                    }

                    $typeStartY -= $typeHeight;
                }

                $sourceStartY -= $sourceHeight;
            }

            $totalBodyHeight = 0;
            foreach ($rowsOnPage as $rowInfo) {
                $totalBodyHeight += (int) $rowInfo['height'];
            }
            $current .= $this->pdfRectCmd($marginX, (int) ($bodyTopY - $totalBodyHeight), array_sum($colWidths), (int) $totalBodyHeight, false);
            $y = (int) ($bodyTopY - $totalBodyHeight);

            if ($sourceIndex < count($segments)) {
                $pages[] = $current;
                $current = '';
                $y = $pageHeight - $marginTop;
            }
        }

        return [$current, $y, $pages];
    }

    private function safePdfFilename(string $name): string
    {
        $name = trim((string) $name);
        if ($name === '') return '';
        $name = preg_replace('/\s+/u', '-', $name);
        $name = preg_replace('/[^A-Za-z0-9\-_]+/', '', (string) $name);
        $name = trim((string) $name, '-_');
        return substr((string) $name, 0, 60);
    }

    public function saveFodaCadena(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $payloadRaw = (string) ($_POST['payload'] ?? '');

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $decoded = json_decode($payloadRaw, true);
        if (!is_array($decoded)) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $fortalezas = $decoded['fortalezas'] ?? [];
        $debilidades = $decoded['debilidades'] ?? [];
        if (!is_array($fortalezas) || !is_array($debilidades)) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $max = 50;
        $items = [];
        $now = gmdate('Y-m-d H:i:s');

        $i = 0;
        foreach ($fortalezas as $txt) {
            $txt = trim((string) $txt);
            if ($txt === '') {
                continue;
            }
            $i++;
            if ($i > $max) {
                break;
            }
            $items[] = [
                'tipo' => 'FORTALEZA',
                'posicion' => $i,
                'descripcion' => $txt,
                'updated_at' => $now,
            ];
        }

        $j = 0;
        foreach ($debilidades as $txt) {
            $txt = trim((string) $txt);
            if ($txt === '') {
                continue;
            }
            $j++;
            if ($j > $max) {
                break;
            }
            $items[] = [
                'tipo' => 'DEBILIDAD',
                'posicion' => $j,
                'descripcion' => $txt,
                'updated_at' => $now,
            ];
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                echo json_encode(['ok' => false, 'error' => 'No tienes acceso a este proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $ok = Foda::replaceByProyectoFuente($supabase, $idProyecto, 'CADENA_VALOR_INTERNA', $items);
            if (!$ok) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el FODA.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            echo json_encode(['ok' => true, 'updated_at' => gmdate('c')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el FODA.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function saveFodaBcg(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $payloadRaw = (string) ($_POST['payload'] ?? '');

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $decoded = json_decode($payloadRaw, true);
        if (!is_array($decoded)) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $fortalezas = $decoded['fortalezas'] ?? [];
        $debilidades = $decoded['debilidades'] ?? [];
        if (!is_array($fortalezas) || !is_array($debilidades)) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $max = 50;
        $items = [];
        $now = gmdate('Y-m-d H:i:s');

        $i = 0;
        foreach ($fortalezas as $txt) {
            $txt = trim((string) $txt);
            if ($txt === '') continue;
            $i++;
            if ($i > $max) break;
            $items[] = ['tipo' => 'FORTALEZA', 'posicion' => $i, 'descripcion' => $txt, 'updated_at' => $now];
        }

        $j = 0;
        foreach ($debilidades as $txt) {
            $txt = trim((string) $txt);
            if ($txt === '') continue;
            $j++;
            if ($j > $max) break;
            $items[] = ['tipo' => 'DEBILIDAD', 'posicion' => $j, 'descripcion' => $txt, 'updated_at' => $now];
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                echo json_encode(['ok' => false, 'error' => 'No tienes acceso a este proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $ok = Foda::replaceByProyectoFuente($supabase, $idProyecto, 'AUTODIAGNOSTICO_BCG', $items);
            if (!$ok) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el apartado.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            echo json_encode(['ok' => true, 'updated_at' => gmdate('c')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el apartado.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function saveFodaPerfilCompetitivo(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $payloadRaw = (string) ($_POST['payload'] ?? '');

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $decoded = json_decode($payloadRaw, true);
        if (!is_array($decoded)) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $oportunidades = $decoded['oportunidades'] ?? [];
        $amenazas = $decoded['amenazas'] ?? [];
        if (!is_array($oportunidades) || !is_array($amenazas)) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $max = 50;
        $items = [];
        $now = gmdate('Y-m-d H:i:s');

        $i = 0;
        foreach ($oportunidades as $txt) {
            $txt = trim((string) $txt);
            if ($txt === '') continue;
            $i++;
            if ($i > $max) break;
            $items[] = ['tipo' => 'OPORTUNIDAD', 'posicion' => $i, 'descripcion' => $txt, 'updated_at' => $now];
        }

        $j = 0;
        foreach ($amenazas as $txt) {
            $txt = trim((string) $txt);
            if ($txt === '') continue;
            $j++;
            if ($j > $max) break;
            $items[] = ['tipo' => 'AMENAZA', 'posicion' => $j, 'descripcion' => $txt, 'updated_at' => $now];
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                echo json_encode(['ok' => false, 'error' => 'No tienes acceso a este proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $ok = Foda::replaceByProyectoFuente($supabase, $idProyecto, 'PERFIL_COMPETITIVO', $items);
            if (!$ok) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el apartado.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            echo json_encode(['ok' => true, 'updated_at' => gmdate('c')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el apartado.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function saveFodaPest(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $payloadRaw = (string) ($_POST['payload'] ?? '');

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $decoded = json_decode($payloadRaw, true);
        if (!is_array($decoded)) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $oportunidades = $decoded['oportunidades'] ?? [];
        $amenazas = $decoded['amenazas'] ?? [];
        if (!is_array($oportunidades) || !is_array($amenazas)) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $max = 50;
        $items = [];
        $now = gmdate('Y-m-d H:i:s');

        $i = 0;
        foreach ($oportunidades as $txt) {
            $txt = trim((string) $txt);
            if ($txt === '') continue;
            $i++;
            if ($i > $max) break;
            $items[] = ['tipo' => 'OPORTUNIDAD', 'posicion' => $i, 'descripcion' => $txt, 'updated_at' => $now];
        }

        $j = 0;
        foreach ($amenazas as $txt) {
            $txt = trim((string) $txt);
            if ($txt === '') continue;
            $j++;
            if ($j > $max) break;
            $items[] = ['tipo' => 'AMENAZA', 'posicion' => $j, 'descripcion' => $txt, 'updated_at' => $now];
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                echo json_encode(['ok' => false, 'error' => 'No tienes acceso a este proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $ok = Foda::replaceByProyectoFuente($supabase, $idProyecto, 'PEST', $items);
            if (!$ok) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el apartado.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            echo json_encode(['ok' => true, 'updated_at' => gmdate('c')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el apartado.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function updateProjectName(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $nombre = trim((string) ($_POST['nombre'] ?? ''));

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
        if ($nombre === '') {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'El nombre no puede quedar vacío.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = Proyecto::findById($supabase, $idProyecto);
            if ($proyecto === null) {
                http_response_code(404);
                echo json_encode(['ok' => false, 'error' => 'Proyecto no encontrado.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
            $idPersona = (int) ($authUser['id_persona'] ?? 0);
            if (!$this->isCreadorProyecto($proyecto, $idPersona)) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'error' => 'Solo el creador puede editar el nombre.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            Proyecto::updateNombre($supabase, $idProyecto, $nombre);
            echo json_encode(['ok' => true, 'nombre' => $nombre], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        } catch (Throwable $e) {
            http_response_code(400);
            $msg = $this->friendlySupabaseError($e, 'No se pudo actualizar el nombre.');
            echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function inviteMiembro(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $email = trim((string) ($_POST['email'] ?? ''));

        $wantsJson = str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json') || (strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest');

        if ($idProyecto <= 0 || $email === '') {
            $this->debugInviteLog('invalid_input', ['id_proyecto' => $idProyecto, 'id_persona' => (int) $authUser['id_persona'], 'email' => $email]);
            if ($wantsJson) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'code' => 'INVALID_INPUT', 'error' => 'Datos inválidos.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
            Session::flash('error', 'Datos inválidos.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=overview&members=1');
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = Proyecto::findOwnedById($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                $this->debugInviteLog('not_creator', ['id_proyecto' => $idProyecto, 'id_persona' => (int) $authUser['id_persona'], 'email' => $email]);
                if ($wantsJson) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'code' => 'NOT_CREATOR', 'error' => 'Solo el creador puede invitar.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
                Session::flash('error', 'Solo el creador puede invitar miembros.');
                $this->redirect('/proyectos.php');
            }

            $persona = Persona::findByEmail($supabase, $email);
            if ($persona === null || (int) ($persona['id_persona'] ?? 0) <= 0) {
                $this->debugInviteLog('user_not_registered', ['id_proyecto' => $idProyecto, 'id_persona' => (int) $authUser['id_persona'], 'email' => $email]);
                if ($wantsJson) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'code' => 'USER_NOT_REGISTERED', 'error' => 'El usuario no está registrado.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
                Session::flash('error', 'USER_NOT_REGISTERED');
                $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=overview&members=1');
            }

            $idPersonaInvitada = (int) ($persona['id_persona'] ?? 0);
            $creadorId = (int) ($proyecto['creador_id'] ?? 0);
            if ($idPersonaInvitada === $creadorId) {
                $this->debugInviteLog('already_member_creator', ['id_proyecto' => $idProyecto, 'id_persona' => (int) $authUser['id_persona'], 'invited_id' => $idPersonaInvitada, 'email' => $email]);
                if ($wantsJson) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'code' => 'USER_ALREADY_MEMBER', 'error' => 'El usuario ya es miembro.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
                Session::flash('error', 'USER_ALREADY_MEMBER');
                $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=overview&members=1');
            }

            if (ProyectoMiembro::exists($supabase, $idProyecto, $idPersonaInvitada)) {
                $this->debugInviteLog('already_member', ['id_proyecto' => $idProyecto, 'id_persona' => (int) $authUser['id_persona'], 'invited_id' => $idPersonaInvitada, 'email' => $email]);
                if ($wantsJson) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'code' => 'USER_ALREADY_MEMBER', 'error' => 'El usuario ya es miembro.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
                Session::flash('error', 'USER_ALREADY_MEMBER');
                $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=overview&members=1');
            }

            $ok = ProyectoMiembro::createInvitado($supabase, $idProyecto, $idPersonaInvitada);
            if (!$ok) {
                $this->debugInviteLog('conflict', ['id_proyecto' => $idProyecto, 'id_persona' => (int) $authUser['id_persona'], 'invited_id' => $idPersonaInvitada, 'email' => $email]);
                if ($wantsJson) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'code' => 'USER_ALREADY_MEMBER', 'error' => 'El usuario ya es miembro.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
                Session::flash('error', 'USER_ALREADY_MEMBER');
                $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=overview&members=1');
            }

            if ($wantsJson) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            Session::flash('success', 'Invitación enviada.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=overview&members=1');
        } catch (Throwable $e) {
            $this->debugInviteLog('error', ['id_proyecto' => $idProyecto, 'id_persona' => (int) $authUser['id_persona'], 'email' => $email, 'error' => $e->getMessage()]);
            if ($wantsJson) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'code' => 'ERROR', 'error' => 'No se pudo invitar al usuario.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
            Session::flash('error', 'No se pudo invitar al usuario.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=overview&members=1');
        }
    }

    public function eliminarMiembro(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $idPersona = (int) ($_POST['id_persona'] ?? 0);

        $wantsJson = str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json') || (strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest');

        if ($idProyecto <= 0 || $idPersona <= 0) {
            if ($wantsJson) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'code' => 'INVALID_INPUT', 'error' => 'Datos inválidos.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
            Session::flash('error', 'Datos inválidos.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=overview&members=1');
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = Proyecto::findOwnedById($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                if ($wantsJson) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'code' => 'NOT_CREATOR', 'error' => 'Solo el creador puede eliminar.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
                Session::flash('error', 'Solo el creador puede eliminar miembros.');
                $this->redirect('/proyectos.php');
            }

            $creadorId = (int) ($proyecto['creador_id'] ?? 0);
            if ($idPersona === $creadorId) {
                if ($wantsJson) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'code' => 'CANNOT_REMOVE_CREATOR', 'error' => 'No se puede eliminar al creador.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
                Session::flash('error', 'No se puede eliminar al creador.');
                $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=overview&members=1');
            }

            if (!ProyectoMiembro::exists($supabase, $idProyecto, $idPersona)) {
                if ($wantsJson) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'code' => 'NOT_MEMBER', 'error' => 'El usuario no es miembro del proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
                Session::flash('error', 'El usuario no es miembro del proyecto.');
                $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=overview&members=1');
            }

            ProyectoMiembro::delete($supabase, $idProyecto, $idPersona);

            if ($wantsJson) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            Session::flash('success', 'Miembro eliminado.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=overview&members=1');
        } catch (Throwable $e) {
            if ($wantsJson) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'code' => 'ERROR', 'error' => 'No se pudo eliminar el miembro.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
            Session::flash('error', 'No se pudo eliminar el miembro.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=overview&members=1');
        }
    }

    public function saveCadenaValor(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $idPregunta = (int) ($_POST['id_pregunta'] ?? 0);
        $valor = (int) ($_POST['valor'] ?? -1);

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        if ($idPregunta <= 0 || $valor < 0 || $valor > 4) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                echo json_encode(['ok' => false, 'error' => 'No tienes acceso a este proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            CadenaValor::ensureSeeded($supabase);
            if (!CadenaValor::existsPregunta($supabase, $idPregunta)) {
                echo json_encode(['ok' => false, 'error' => 'Pregunta inválida.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $ok = CadenaValor::upsertRespuesta($supabase, $idProyecto, $idPregunta, $valor);
            if (!$ok) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar la respuesta.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $preguntas = CadenaValor::listPreguntas($supabase);
            $respuestas = CadenaValor::listRespuestasByProyecto($supabase, $idProyecto);
            $calc = CadenaValor::compute($preguntas, $respuestas);

            if ($calc['potential'] !== null) {
                CadenaValor::upsertResultado($supabase, $idProyecto, (int) $calc['sum'], (float) $calc['potential']);
            }

            echo json_encode(
                [
                    'ok' => true,
                    'calc' => $calc,
                    'updated_at' => gmdate('c'),
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
            exit;
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => $this->friendlySupabaseError($e, 'Error al guardar.')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function saveCadenaValorBatch(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $answersRaw = (string) ($_POST['answers'] ?? '');

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $decoded = json_decode($answersRaw, true);
        if (!is_array($decoded)) {
            echo json_encode(['ok' => false, 'error' => 'Respuestas inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $answers = [];
        foreach ($decoded as $qid => $value) {
            $qid = (int) $qid;
            $value = (int) $value;
            if ($qid <= 0 || $value < 0 || $value > 4) {
                echo json_encode(['ok' => false, 'error' => 'Respuestas inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
            $answers[$qid] = $value;
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                echo json_encode(['ok' => false, 'error' => 'No tienes acceso a este proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            CadenaValor::ensureSeeded($supabase);
            $preguntas = CadenaValor::listPreguntas($supabase);
            if (empty($preguntas)) {
                echo json_encode(['ok' => false, 'error' => 'No se pudieron cargar las preguntas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $ids = [];
            foreach ($preguntas as $p) {
                if (!is_array($p)) {
                    continue;
                }
                $id = (int) ($p['id_pregunta'] ?? 0);
                if ($id > 0) {
                    $ids[$id] = true;
                }
            }

            $count = count($ids);
            if ($count <= 0) {
                echo json_encode(['ok' => false, 'error' => 'No se pudieron cargar las preguntas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            if (count($answers) !== $count) {
                echo json_encode(['ok' => false, 'error' => 'Debes responder todas las preguntas antes de guardar.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $sum = 0;
            foreach ($ids as $qid => $_) {
                if (!array_key_exists($qid, $answers)) {
                    echo json_encode(['ok' => false, 'error' => 'Debes responder todas las preguntas antes de guardar.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
                $sum += (int) $answers[$qid];
            }

            $ok = CadenaValor::upsertRespuestasBatch($supabase, $idProyecto, $answers);
            if (!$ok) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar la evaluación.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $potential = 1 - ($sum / 100);
            CadenaValor::upsertResultado($supabase, $idProyecto, (int) $sum, (float) $potential);

            echo json_encode(
                [
                    'ok' => true,
                    'calc' => [
                        'sum' => (int) $sum,
                        'valid' => (int) $count,
                        'count' => (int) $count,
                        'missing' => 0,
                        'potential' => (float) $potential,
                    ],
                    'updated_at' => gmdate('c'),
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
            exit;
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => $this->friendlySupabaseError($e, 'Error al guardar la evaluación.')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function savePerfilCompetitivo(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $idFactor = (int) ($_POST['id_factor'] ?? 0);
        $valor = (int) ($_POST['valor'] ?? -1);

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        if ($idFactor <= 0 || $valor < 0 || $valor > 4) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                echo json_encode(['ok' => false, 'error' => 'No tienes acceso a este proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            PerfilCompetitivo::ensureSeeded($supabase);
            if (!PerfilCompetitivo::existsFactor($supabase, $idFactor)) {
                echo json_encode(['ok' => false, 'error' => 'Factor inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $ok = PerfilCompetitivo::upsertRespuesta($supabase, $idProyecto, $idFactor, $valor);
            if (!$ok) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar la respuesta.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $factores = PerfilCompetitivo::listFactores($supabase);
            $respuestas = PerfilCompetitivo::listRespuestasByProyecto($supabase, $idProyecto);
            $calc = PerfilCompetitivo::compute($factores, $respuestas);

            if (($calc['missing'] ?? 0) === 0 && isset($calc['conclusion_code'], $calc['conclusion_text'])) {
                PerfilCompetitivo::upsertResultado(
                    $supabase,
                    $idProyecto,
                    (int) ($calc['total'] ?? 0),
                    (int) $calc['conclusion_code'],
                    (string) $calc['conclusion_text']
                );
            }

            echo json_encode(
                [
                    'ok' => true,
                    'calc' => $calc,
                    'updated_at' => gmdate('c'),
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
            exit;
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'Error al guardar.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function savePerfilCompetitivoBatch(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $answersRaw = (string) ($_POST['answers'] ?? '');

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $decoded = json_decode($answersRaw, true);
        if (!is_array($decoded)) {
            echo json_encode(['ok' => false, 'error' => 'Respuestas inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $answers = [];
        foreach ($decoded as $fid => $value) {
            $fid = (int) $fid;
            $value = (int) $value;
            if ($fid <= 0 || $value < 0 || $value > 4) {
                echo json_encode(['ok' => false, 'error' => 'Respuestas inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
            $answers[$fid] = $value;
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                echo json_encode(['ok' => false, 'error' => 'No tienes acceso a este proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            PerfilCompetitivo::ensureSeeded($supabase);
            $factores = PerfilCompetitivo::listFactores($supabase);
            if (empty($factores)) {
                echo json_encode(['ok' => false, 'error' => 'No se pudieron cargar los factores.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $ids = [];
            foreach ($factores as $f) {
                if (!is_array($f)) {
                    continue;
                }
                $id = (int) ($f['id_factor'] ?? 0);
                if ($id > 0) {
                    $ids[$id] = true;
                }
            }

            $count = count($ids);
            if ($count <= 0) {
                echo json_encode(['ok' => false, 'error' => 'No se pudieron cargar los factores.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            if (count($answers) !== $count) {
                echo json_encode(['ok' => false, 'error' => 'Debes responder todas las filas antes de guardar.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $total = 0;
            foreach ($ids as $fid => $_) {
                if (!array_key_exists($fid, $answers)) {
                    echo json_encode(['ok' => false, 'error' => 'Debes responder todas las filas antes de guardar.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
                $total += (int) $answers[$fid];
            }

            $ok = PerfilCompetitivo::upsertRespuestasBatch($supabase, $idProyecto, $answers);
            if (!$ok) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar la evaluación.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $conclusion = PerfilCompetitivo::conclusionForTotal($total);
            PerfilCompetitivo::upsertResultado($supabase, $idProyecto, (int) $total, (int) $conclusion['code'], (string) $conclusion['text']);

            echo json_encode(
                [
                    'ok' => true,
                    'calc' => [
                        'total' => (int) $total,
                        'valid' => (int) $count,
                        'count' => (int) $count,
                        'missing' => 0,
                        'conclusion_code' => (int) $conclusion['code'],
                        'conclusion_text' => (string) $conclusion['text'],
                    ],
                    'updated_at' => gmdate('c'),
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
            exit;
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'Error al guardar la evaluación.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function savePerfilCompetitivoAutosaveBatch(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $answersRaw = (string) ($_POST['answers'] ?? '');

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $decoded = json_decode($answersRaw, true);
        if (!is_array($decoded) || empty($decoded)) {
            echo json_encode(['ok' => false, 'error' => 'Respuestas inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $answers = [];
        foreach ($decoded as $fid => $value) {
            $fid = (int) $fid;
            $value = (int) $value;
            if ($fid <= 0 || $value < 0 || $value > 4) {
                echo json_encode(['ok' => false, 'error' => 'Respuestas inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
            $answers[$fid] = $value;
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                echo json_encode(['ok' => false, 'error' => 'No tienes acceso a este proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            PerfilCompetitivo::ensureSeeded($supabase);
            $ok = PerfilCompetitivo::upsertRespuestasBatch($supabase, $idProyecto, $answers);
            if (!$ok) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar automáticamente.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $factores = PerfilCompetitivo::listFactores($supabase);
            $respuestas = PerfilCompetitivo::listRespuestasByProyecto($supabase, $idProyecto);
            $calc = PerfilCompetitivo::compute($factores, $respuestas);

            if (($calc['missing'] ?? 0) === 0 && isset($calc['conclusion_code'], $calc['conclusion_text'])) {
                PerfilCompetitivo::upsertResultado(
                    $supabase,
                    $idProyecto,
                    (int) ($calc['total'] ?? 0),
                    (int) $calc['conclusion_code'],
                    (string) $calc['conclusion_text']
                );
            }

            echo json_encode(
                [
                    'ok' => true,
                    'calc' => $calc,
                    'updated_at' => gmdate('c'),
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
            exit;
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => $this->friendlySupabaseError($e, 'Error al guardar automáticamente.')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function savePestAutosaveBatch(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $answersRaw = (string) ($_POST['answers'] ?? '');

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $decoded = json_decode($answersRaw, true);
        if (!is_array($decoded) || empty($decoded)) {
            echo json_encode(['ok' => false, 'error' => 'Respuestas inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $answers = [];
        foreach ($decoded as $qid => $value) {
            $qid = (int) $qid;
            $value = (int) $value;
            if ($qid <= 0 || $value < 0 || $value > 4) {
                echo json_encode(['ok' => false, 'error' => 'Respuestas inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
            $answers[$qid] = $value;
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                echo json_encode(['ok' => false, 'error' => 'No tienes acceso a este proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            Pest::ensureSeeded($supabase);
            $ok = Pest::upsertRespuestasBatch($supabase, $idProyecto, $answers);
            if (!$ok) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar automáticamente.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $preguntas = Pest::listPreguntas($supabase);
            $respuestas = Pest::listRespuestasByProyecto($supabase, $idProyecto);
            $calc = Pest::compute($preguntas, $respuestas);

            if (($calc['missing'] ?? 0) === 0) {
                $pct = is_array($calc['pct'] ?? null) ? (array) $calc['pct'] : [];
                Pest::upsertResultado($supabase, $idProyecto, $pct);
            }

            echo json_encode(
                [
                    'ok' => true,
                    'calc' => $calc,
                    'updated_at' => gmdate('c'),
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
            exit;
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => $this->friendlySupabaseError($e, 'Error al guardar automáticamente.')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function savePestBatch(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $answersRaw = (string) ($_POST['answers'] ?? '');

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $decoded = json_decode($answersRaw, true);
        if (!is_array($decoded)) {
            echo json_encode(['ok' => false, 'error' => 'Respuestas inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $answers = [];
        foreach ($decoded as $qid => $value) {
            $qid = (int) $qid;
            $value = (int) $value;
            if ($qid <= 0 || $value < 0 || $value > 4) {
                echo json_encode(['ok' => false, 'error' => 'Respuestas inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
            $answers[$qid] = $value;
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                echo json_encode(['ok' => false, 'error' => 'No tienes acceso a este proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            Pest::ensureSeeded($supabase);
            $preguntas = Pest::listPreguntas($supabase);
            if (empty($preguntas)) {
                echo json_encode(['ok' => false, 'error' => 'No se pudieron cargar las preguntas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $ids = [];
            foreach ($preguntas as $p) {
                if (!is_array($p)) {
                    continue;
                }
                $id = (int) ($p['id_pregunta'] ?? 0);
                if ($id > 0) {
                    $ids[$id] = true;
                }
            }

            $count = count($ids);
            if ($count <= 0) {
                echo json_encode(['ok' => false, 'error' => 'No se pudieron cargar las preguntas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            if (count($answers) !== $count) {
                echo json_encode(['ok' => false, 'error' => 'Debes responder todas las preguntas antes de guardar.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            foreach ($ids as $qid => $_) {
                if (!array_key_exists($qid, $answers)) {
                    echo json_encode(['ok' => false, 'error' => 'Debes responder todas las preguntas antes de guardar.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
            }

            $ok = Pest::upsertRespuestasBatch($supabase, $idProyecto, $answers);
            if (!$ok) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar la evaluación.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $respuestas = Pest::listRespuestasByProyecto($supabase, $idProyecto);
            $calc = Pest::compute($preguntas, $respuestas);
            $pct = is_array($calc['pct'] ?? null) ? (array) $calc['pct'] : [];
            Pest::upsertResultado($supabase, $idProyecto, $pct);

            echo json_encode(
                [
                    'ok' => true,
                    'calc' => $calc,
                    'updated_at' => gmdate('c'),
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
            exit;
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => $this->friendlySupabaseError($e, 'Error al guardar la evaluación.')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function saveFodaCruzadaAutosaveBatch(): void
    {
        $this->saveFodaCruzadaInternal(false);
    }

    public function saveFodaCruzadaBatch(): void
    {
        $this->saveFodaCruzadaInternal(true);
    }

    private function saveFodaCruzadaInternal(bool $requireComplete): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $answersRaw = (string) ($_POST['answers'] ?? '');

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $decoded = json_decode($answersRaw, true);
        if (!is_array($decoded)) {
            echo json_encode(['ok' => false, 'error' => 'Respuestas inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                echo json_encode(['ok' => false, 'error' => 'No tienes acceso a este proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $factorRows = FodaCruzada::listFactorRows($supabase, $idProyecto);
            $factorSet = FodaCruzada::buildFactorSet($factorRows);
            if (empty($factorSet['ready'])) {
                echo json_encode(
                    [
                        'ok' => false,
                        'error' => 'Debes registrar al menos una fortaleza, una debilidad, una oportunidad y una amenaza en los módulos previos antes de evaluar la matriz cruzada.',
                    ],
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                );
                exit;
            }

            $relations = is_array($factorSet['relations'] ?? null) ? (array) $factorSet['relations'] : [];
            $validCells = [];
            foreach (FodaCruzada::relationOrder() as $relation) {
                $meta = is_array($relations[$relation] ?? null) ? (array) $relations[$relation] : ['rows' => [], 'cols' => []];
                $rows = is_array($meta['rows'] ?? null) ? (array) $meta['rows'] : [];
                $cols = is_array($meta['cols'] ?? null) ? (array) $meta['cols'] : [];
                foreach ($rows as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $rowKey = trim((string) ($row['key'] ?? ''));
                    if ($rowKey === '') {
                        continue;
                    }
                    foreach ($cols as $col) {
                        if (!is_array($col)) {
                            continue;
                        }
                        $colKey = trim((string) ($col['key'] ?? ''));
                        if ($colKey === '') {
                            continue;
                        }
                        $composite = $relation . '|' . $rowKey . '|' . $colKey;
                        $validCells[$composite] = [
                            'relacion' => $relation,
                            'fila_key' => $rowKey,
                            'columna_key' => $colKey,
                        ];
                    }
                }
            }

            $answerMap = [];
            $persistRows = [];
            foreach ($decoded as $item) {
                if (!is_array($item)) {
                    echo json_encode(['ok' => false, 'error' => 'Respuestas inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
                $relation = strtoupper(trim((string) ($item['relation'] ?? '')));
                $rowKey = trim((string) ($item['row_key'] ?? ''));
                $colKey = trim((string) ($item['col_key'] ?? ''));
                $value = (int) ($item['value'] ?? -1);
                $composite = $relation . '|' . $rowKey . '|' . $colKey;

                if (!isset($validCells[$composite]) || $value < 0 || $value > 4) {
                    echo json_encode(['ok' => false, 'error' => 'Respuestas inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }
                if (isset($answerMap[$composite])) {
                    echo json_encode(['ok' => false, 'error' => 'No se permiten duplicados en la misma relación.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
                }

                $answerMap[$composite] = $value;
                $persistRows[] = [
                    'id_proyecto' => $idProyecto,
                    'relacion' => $relation,
                    'fila_key' => $rowKey,
                    'columna_key' => $colKey,
                    'valor' => $value,
                    'updated_at' => gmdate('Y-m-d H:i:s'),
                ];
            }

            if ($requireComplete && count($answerMap) !== count($validCells)) {
                echo json_encode(['ok' => false, 'error' => 'Debes valorar todas las relaciones de la matriz antes de guardar.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $ok = FodaCruzada::replaceEvaluaciones($supabase, $idProyecto, $persistRows);
            if (!$ok) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar la matriz cruzada.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $calc = FodaCruzada::compute($factorSet, $answerMap);
            if (!empty($calc['complete'])) {
                FodaCruzada::upsertResultado($supabase, $idProyecto, $calc);
            } else {
                FodaCruzada::deleteResultado($supabase, $idProyecto);
            }

            echo json_encode(
                [
                    'ok' => true,
                    'calc' => $calc,
                    'updated_at' => gmdate('c'),
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
            exit;
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => $this->friendlySupabaseError($e, 'Error al guardar la matriz cruzada.')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function saveCameAutosaveBatch(): void
    {
        $this->saveCameInternal(false);
    }

    public function saveCameBatch(): void
    {
        $this->saveCameInternal(true);
    }

    private function saveCameInternal(bool $isFinalSave): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $accionesRaw = (string) ($_POST['acciones'] ?? '');

        header('Content-Type: application/json; charset=utf-8');

        if ($idProyecto <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Proyecto inválido.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $decoded = json_decode($accionesRaw, true);
        if (!is_array($decoded)) {
            echo json_encode(['ok' => false, 'error' => 'Acciones inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $grouped = ['C' => [], 'A' => [], 'M' => [], 'E' => []];
        foreach ($decoded as $item) {
            if (!is_array($item)) {
                echo json_encode(['ok' => false, 'error' => 'Acciones inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
            $cat = strtoupper(trim((string) ($item['category'] ?? '')));
            $pos = (int) ($item['position'] ?? 0);
            $text = trim((string) ($item['text'] ?? ''));
            if (!in_array($cat, Came::categoryOrder(), true) || $pos < 1) {
                echo json_encode(['ok' => false, 'error' => 'Acciones inválidas.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
            $grouped[$cat][] = [
                'position' => $pos,
                'description' => $text,
            ];
        }

        $persist = [];
        $normalized = ['C' => [], 'A' => [], 'M' => [], 'E' => []];
        foreach (Came::categoryOrder() as $cat) {
            $rows = is_array($grouped[$cat] ?? null) ? (array) $grouped[$cat] : [];
            usort($rows, static fn (array $a, array $b): int => ((int) ($a['position'] ?? 0)) <=> ((int) ($b['position'] ?? 0)));
            $seq = 1;
            foreach ($rows as $row) {
                $text = trim((string) ($row['description'] ?? ''));
                if ($text === '') {
                    continue;
                }
                $normalized[$cat][] = [
                    'position' => $seq,
                    'description' => $text,
                ];
                $persist[] = [
                    'id_proyecto' => $idProyecto,
                    'categoria' => $cat,
                    'posicion' => $seq,
                    'descripcion' => $text,
                    'updated_at' => gmdate('Y-m-d H:i:s'),
                ];
                $seq++;
            }
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                echo json_encode(['ok' => false, 'error' => 'No tienes acceso a este proyecto.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $ok = Came::replaceAcciones($supabase, $idProyecto, $persist);
            if (!$ok) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar la matriz.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            $calc = Came::compute($normalized);
            Came::upsertResultado($supabase, $idProyecto, $calc);

            echo json_encode(
                [
                    'ok' => true,
                    'calc' => $calc,
                    'final' => $isFinalSave,
                    'updated_at' => gmdate('c'),
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
            exit;
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => $this->friendlySupabaseError($e, 'Error al guardar la matriz.')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    public function saveMision(): void
    {
        $this->saveSingleTextBlock('mision', 'mision', 'La misión es obligatoria.');
    }

    public function saveVision(): void
    {
        $this->saveSingleTextBlock('vision', 'vision', 'La visión es obligatoria.');
    }

    public function saveOverviewConclusion(): void
    {
        $this->saveSingleTextBlock('overview_conclusion', 'overview', 'La conclusión es obligatoria.');
    }

    public function addValor(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($idProyecto <= 0) {
            Session::flash('error', 'Proyecto inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 2) {
            Session::flash('error', 'El valor es obligatorio.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&edit=valores');
        }

        $supabase = new SupabaseClient();
        $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        Valor::create($supabase, $idProyecto, $descripcion);
        Session::flash('success', 'Valor agregado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=valores');
    }

    public function saveValores(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $valores = $_POST['valores'] ?? [];

        if ($idProyecto <= 0) {
            if ($this->wantsJson()) {
                $this->jsonError('Proyecto inválido.', 400);
            }
            Session::flash('error', 'Proyecto inválido.');
            $this->redirect('/proyectos.php');
        }

        if (!is_array($valores)) {
            if ($this->wantsJson()) {
                $this->jsonError('Valores inválidos.', 400);
            }
            Session::flash('error', 'Valores inválidos.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&edit=valores');
        }

        $clean = [];
        foreach ($valores as $v) {
            $v = trim((string) $v);
            if ($v === '') {
                continue;
            }
            if (mb_strlen($v, 'UTF-8') < 2) {
                if ($this->wantsJson()) {
                    $this->jsonError('Cada valor debe tener al menos 2 caracteres.', 400);
                }
                Session::flash('error', 'Cada valor debe tener al menos 2 caracteres.');
                $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&edit=valores');
            }
            $clean[] = $v;
        }

        try {
            $supabase = new SupabaseClient();
            $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
            if ($proyecto === null) {
                if ($this->wantsJson()) {
                    $this->jsonError('No tienes acceso a este proyecto.', 403);
                }
                Session::flash('error', 'No tienes acceso a este proyecto.');
                $this->redirect('/proyectos.php');
            }

            Valor::replaceAll($supabase, $idProyecto, $clean);
        } catch (Throwable $e) {
            $msg = $this->friendlySupabaseError($e, 'No se pudieron guardar los valores.');
            if ($this->wantsJson()) {
                $this->jsonError($msg, 400);
            }
            Session::flash('error', $msg);
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=valores&edit=valores');
        }

        if ($this->wantsJson()) {
            $this->jsonOk('Valores guardados correctamente.');
        }
        Session::flash('success', 'Valores guardados correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=valores');
    }

    public function updateValor(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $idValor = (int) ($_POST['id_valor'] ?? 0);
        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($idProyecto <= 0 || $idValor <= 0) {
            Session::flash('error', 'Valor inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 2) {
            Session::flash('error', 'El valor es obligatorio.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&edit=valores');
        }

        $supabase = new SupabaseClient();
        $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        $ok = Valor::update($supabase, $idValor, $idProyecto, $descripcion);
        if (!$ok) {
            Session::flash('error', 'No se pudo actualizar el valor.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=valores');
        }

        Session::flash('success', 'Valor actualizado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=valores');
    }

    public function saveObjetivosBatch(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $payloadRaw = (string) ($_POST['payload'] ?? '');

        if ($idProyecto <= 0) {
            if ($this->wantsJson()) {
                $this->jsonError('Proyecto inválido.', 400);
            }
            Session::flash('error', 'Proyecto inválido.');
            $this->redirect('/proyectos.php');
        }

        $decoded = json_decode($payloadRaw, true);
        if (!is_array($decoded)) {
            if ($this->wantsJson()) {
                $this->jsonError('Datos inválidos.', 400);
            }
            Session::flash('error', 'Datos inválidos.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        $estrategicos = $decoded['estrategicos'] ?? [];
        $especificos = $decoded['especificos'] ?? [];
        if (!is_array($estrategicos) || !is_array($especificos)) {
            if ($this->wantsJson()) {
                $this->jsonError('Datos inválidos.', 400);
            }
            Session::flash('error', 'Datos inválidos.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        if (count($estrategicos) > 50 || count($especificos) > 200) {
            if ($this->wantsJson()) {
                $this->jsonError('Demasiados elementos para guardar.', 400);
            }
            Session::flash('error', 'Demasiados elementos para guardar.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        $supabase = new SupabaseClient();
        $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este proyecto.', 403);
            }
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        $createdOE = 0;
        $createdOESP = 0;
        $tmpToObjEst = [];

        try {
            foreach ($estrategicos as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $tmpId = trim((string) ($row['tmp_id'] ?? ''));
                $desc = trim((string) ($row['descripcion'] ?? ''));
                if ($desc === '' || mb_strlen($desc, 'UTF-8') < 5) {
                    throw new InvalidArgumentException('Cada objetivo estratégico debe tener al menos 5 caracteres.');
                }

                $idObjEst = ObjetivoEstrategico::create($supabase, $idProyecto, $desc);
                if ($idObjEst <= 0) {
                    throw new RuntimeException('No se pudo crear un objetivo estratégico.');
                }
                $createdOE++;
                if ($tmpId !== '') {
                    $tmpToObjEst[$tmpId] = (int) $idObjEst;
                }

                $espList = $row['especificos'] ?? [];
                if (!is_array($espList)) {
                    $espList = [];
                }
                foreach ($espList as $esp) {
                    $esp = trim((string) $esp);
                    if ($esp === '') {
                        continue;
                    }
                    if (mb_strlen($esp, 'UTF-8') < 5) {
                        throw new InvalidArgumentException('Cada objetivo específico debe tener al menos 5 caracteres.');
                    }
                    ObjetivoEspecifico::create($supabase, (int) $idObjEst, $esp);
                    $createdOESP++;
                }
            }

            foreach ($especificos as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $oeToken = trim((string) ($row['oe'] ?? ''));
                $oeTmp = trim((string) ($row['oe_tmp'] ?? ''));
                $desc = trim((string) ($row['descripcion'] ?? ''));
                if ($desc === '' || mb_strlen($desc, 'UTF-8') < 5) {
                    throw new InvalidArgumentException('Cada objetivo específico debe tener al menos 5 caracteres.');
                }
                $idObjEst = 0;
                if ($oeTmp !== '') {
                    $idObjEst = (int) ($tmpToObjEst[$oeTmp] ?? 0);
                    if ($idObjEst <= 0) {
                        throw new InvalidArgumentException('Objetivo estratégico inválido.');
                    }
                } else {
                    if ($oeToken === '') {
                        throw new InvalidArgumentException('Objetivo estratégico inválido.');
                    }
                    $idObjEst = $this->objetivoEstrategicoIdFromToken($oeToken);
                    if ($idObjEst <= 0) {
                        throw new InvalidArgumentException('Objetivo estratégico inválido.');
                    }
                    if (!ObjetivoEstrategico::existsInProyecto($supabase, $idObjEst, $idProyecto)) {
                        throw new InvalidArgumentException('No tienes acceso a este objetivo estratégico.');
                    }
                }

                ObjetivoEspecifico::create($supabase, $idObjEst, $desc);
                $createdOESP++;
            }
        } catch (InvalidArgumentException $e) {
            if ($this->wantsJson()) {
                $this->jsonError($e->getMessage(), 400);
            }
            Session::flash('error', $e->getMessage());
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        } catch (Throwable $e) {
            if ($this->wantsJson()) {
                $this->jsonError('No se pudieron guardar los objetivos.', 400);
            }
            Session::flash('error', 'No se pudieron guardar los objetivos.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        if ($this->wantsJson()) {
            $this->jsonOk('Objetivos guardados correctamente.', [
                'created' => [
                    'estrategicos' => $createdOE,
                    'especificos' => $createdOESP,
                ],
            ]);
        }
        Session::flash('success', 'Objetivos guardados correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
    }

    public function createObjetivoEstrategico(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($idProyecto <= 0) {
            if ($this->wantsJson()) {
                $this->jsonError('Proyecto inválido.', 400);
            }
            Session::flash('error', 'Proyecto inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 5) {
            if ($this->wantsJson()) {
                $this->jsonError('La descripción del objetivo estratégico es obligatoria (mínimo 5 caracteres).', 400);
            }
            Session::flash('error', 'La descripción del objetivo estratégico es obligatoria (mínimo 5 caracteres).');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        $supabase = new SupabaseClient();
        $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este proyecto.', 403);
            }
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        ObjetivoEstrategico::create($supabase, $idProyecto, $descripcion);
        if ($this->wantsJson()) {
            $this->jsonOk('Objetivo estratégico registrado correctamente.');
        }
        Session::flash('success', 'Objetivo estratégico registrado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
    }

    public function updateObjetivoEstrategico(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $oeToken = trim((string) ($_POST['oe'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        $idObjetivoEst = $this->objetivoEstrategicoIdFromToken($oeToken);

        if ($idProyecto <= 0 || $idObjetivoEst <= 0) {
            if ($this->wantsJson()) {
                $this->jsonError('Objetivo inválido.', 400);
            }
            Session::flash('error', 'Objetivo inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 5) {
            if ($this->wantsJson()) {
                $this->jsonError('La descripción del objetivo estratégico es obligatoria (mínimo 5 caracteres).', 400);
            }
            Session::flash('error', 'La descripción del objetivo estratégico es obligatoria (mínimo 5 caracteres).');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos&oe_edit=' . urlencode($oeToken));
        }

        $supabase = new SupabaseClient();
        $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este proyecto.', 403);
            }
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        if (!ObjetivoEstrategico::existsInProyecto($supabase, $idObjetivoEst, $idProyecto)) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este objetivo.', 403);
            }
            Session::flash('error', 'No tienes acceso a este objetivo.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        try {
            $ok = ObjetivoEstrategico::update($supabase, $idObjetivoEst, $idProyecto, $descripcion);
        } catch (Throwable $e) {
            $ok = false;
        }
        if (!$ok) {
            if ($this->wantsJson()) {
                $this->jsonError('No se pudo actualizar el objetivo estratégico.', 400);
            }
            Session::flash('error', 'No se pudo actualizar el objetivo estratégico.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos&oe_edit=' . urlencode($oeToken));
        }

        if ($this->wantsJson()) {
            $this->jsonOk('Objetivo estratégico actualizado correctamente.');
        }
        Session::flash('success', 'Objetivo estratégico actualizado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
    }

    public function deleteObjetivoEstrategico(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $oeToken = trim((string) ($_POST['oe'] ?? ''));
        $idObjetivoEst = $this->objetivoEstrategicoIdFromToken($oeToken);

        if ($idProyecto <= 0 || $idObjetivoEst <= 0) {
            if ($this->wantsJson()) {
                $this->jsonError('Objetivo inválido.', 400);
            }
            Session::flash('error', 'Objetivo inválido.');
            $this->redirect('/proyectos.php');
        }

        $supabase = new SupabaseClient();
        $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este proyecto.', 403);
            }
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        if (!ObjetivoEstrategico::existsInProyecto($supabase, $idObjetivoEst, $idProyecto)) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este objetivo.', 403);
            }
            Session::flash('error', 'No tienes acceso a este objetivo.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        try {
            $ok = ObjetivoEstrategico::delete($supabase, $idObjetivoEst, $idProyecto);
        } catch (Throwable $e) {
            $ok = false;
        }
        if (!$ok) {
            if ($this->wantsJson()) {
                $this->jsonError('No se pudo eliminar el objetivo estratégico.', 400);
            }
            Session::flash('error', 'No se pudo eliminar el objetivo estratégico.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        if ($this->wantsJson()) {
            $this->jsonOk('Objetivo estratégico eliminado correctamente.');
        }
        Session::flash('success', 'Objetivo estratégico eliminado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
    }

    public function createObjetivoEspecifico(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $oeToken = trim((string) ($_POST['oe'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        $idObjetivoEst = $this->objetivoEstrategicoIdFromToken($oeToken);

        if ($idProyecto <= 0 || $idObjetivoEst <= 0) {
            if ($this->wantsJson()) {
                $this->jsonError('Objetivo inválido.', 400);
            }
            Session::flash('error', 'Objetivo inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 5) {
            if ($this->wantsJson()) {
                $this->jsonError('La descripción del objetivo específico es obligatoria (mínimo 5 caracteres).', 400);
            }
            Session::flash('error', 'La descripción del objetivo específico es obligatoria (mínimo 5 caracteres).');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        $supabase = new SupabaseClient();
        $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este proyecto.', 403);
            }
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        if (!ObjetivoEstrategico::existsInProyecto($supabase, $idObjetivoEst, $idProyecto)) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este objetivo.', 403);
            }
            Session::flash('error', 'No tienes acceso a este objetivo.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        ObjetivoEspecifico::create($supabase, $idObjetivoEst, $descripcion);
        if ($this->wantsJson()) {
            $this->jsonOk('Objetivo específico registrado correctamente.');
        }
        Session::flash('success', 'Objetivo específico registrado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
    }

    public function updateObjetivoEspecifico(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $oeToken = trim((string) ($_POST['oe'] ?? ''));
        $oespToken = trim((string) ($_POST['oesp'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        $idObjetivoEst = $this->objetivoEstrategicoIdFromToken($oeToken);
        $idObjetivoEsp = $this->objetivoEspecificoIdFromToken($oespToken);

        if ($idProyecto <= 0 || $idObjetivoEst <= 0 || $idObjetivoEsp <= 0) {
            if ($this->wantsJson()) {
                $this->jsonError('Objetivo inválido.', 400);
            }
            Session::flash('error', 'Objetivo inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '' || mb_strlen($descripcion, 'UTF-8') < 5) {
            if ($this->wantsJson()) {
                $this->jsonError('La descripción del objetivo específico es obligatoria (mínimo 5 caracteres).', 400);
            }
            Session::flash('error', 'La descripción del objetivo específico es obligatoria (mínimo 5 caracteres).');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos&oesp_edit=' . urlencode($oespToken));
        }

        $supabase = new SupabaseClient();
        $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este proyecto.', 403);
            }
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        if (!ObjetivoEstrategico::existsInProyecto($supabase, $idObjetivoEst, $idProyecto)) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este objetivo.', 403);
            }
            Session::flash('error', 'No tienes acceso a este objetivo.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        if (!ObjetivoEspecifico::existsInObjetivoEstrategico($supabase, $idObjetivoEsp, $idObjetivoEst)) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este objetivo específico.', 403);
            }
            Session::flash('error', 'No tienes acceso a este objetivo específico.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        try {
            $ok = ObjetivoEspecifico::update($supabase, $idObjetivoEsp, $idObjetivoEst, $descripcion);
        } catch (Throwable $e) {
            $ok = false;
        }
        if (!$ok) {
            if ($this->wantsJson()) {
                $this->jsonError('No se pudo actualizar el objetivo específico.', 400);
            }
            Session::flash('error', 'No se pudo actualizar el objetivo específico.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos&oesp_edit=' . urlencode($oespToken));
        }

        if ($this->wantsJson()) {
            $this->jsonOk('Objetivo específico actualizado correctamente.');
        }
        Session::flash('success', 'Objetivo específico actualizado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
    }

    public function deleteObjetivoEspecifico(): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $oeToken = trim((string) ($_POST['oe'] ?? ''));
        $oespToken = trim((string) ($_POST['oesp'] ?? ''));

        $idObjetivoEst = $this->objetivoEstrategicoIdFromToken($oeToken);
        $idObjetivoEsp = $this->objetivoEspecificoIdFromToken($oespToken);

        if ($idProyecto <= 0 || $idObjetivoEst <= 0 || $idObjetivoEsp <= 0) {
            if ($this->wantsJson()) {
                $this->jsonError('Objetivo inválido.', 400);
            }
            Session::flash('error', 'Objetivo inválido.');
            $this->redirect('/proyectos.php');
        }

        $supabase = new SupabaseClient();
        $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este proyecto.', 403);
            }
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        if (!ObjetivoEstrategico::existsInProyecto($supabase, $idObjetivoEst, $idProyecto)) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este objetivo.', 403);
            }
            Session::flash('error', 'No tienes acceso a este objetivo.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        if (!ObjetivoEspecifico::existsInObjetivoEstrategico($supabase, $idObjetivoEsp, $idObjetivoEst)) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este objetivo específico.', 403);
            }
            Session::flash('error', 'No tienes acceso a este objetivo específico.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        try {
            $ok = ObjetivoEspecifico::delete($supabase, $idObjetivoEsp, $idObjetivoEst);
        } catch (Throwable $e) {
            $ok = false;
        }
        if (!$ok) {
            if ($this->wantsJson()) {
                $this->jsonError('No se pudo eliminar el objetivo específico.', 400);
            }
            Session::flash('error', 'No se pudo eliminar el objetivo específico.');
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
        }

        if ($this->wantsJson()) {
            $this->jsonOk('Objetivo específico eliminado correctamente.');
        }
        Session::flash('success', 'Objetivo específico eliminado correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=objetivos');
    }

    private function saveSingleTextBlock(string $block, string $editQuery, string $emptyMessage): void
    {
        $authController = new AuthController();
        $authUser = $authController->requireAuth();

        $token = trim((string) ($_POST['t'] ?? ''));
        $idProyecto = $this->projectIdFromToken($token);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($idProyecto <= 0) {
            if ($this->wantsJson()) {
                $this->jsonError('Proyecto inválido.', 400);
            }
            Session::flash('error', 'Proyecto inválido.');
            $this->redirect('/proyectos.php');
        }

        if ($descripcion === '') {
            if ($this->wantsJson()) {
                $this->jsonError($emptyMessage, 400);
            }
            Session::flash('error', $emptyMessage);
            $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=' . urlencode($editQuery) . '&edit=' . $editQuery);
        }

        $supabase = new SupabaseClient();
        $proyecto = $this->findAccessibleProyecto($supabase, $idProyecto, (int) $authUser['id_persona']);
        if ($proyecto === null) {
            if ($this->wantsJson()) {
                $this->jsonError('No tienes acceso a este proyecto.', 403);
            }
            Session::flash('error', 'No tienes acceso a este proyecto.');
            $this->redirect('/proyectos.php');
        }

        if ($block === 'mision') {
            Mision::save($supabase, $idProyecto, $descripcion);
        } elseif ($block === 'vision') {
            Vision::save($supabase, $idProyecto, $descripcion);
        } elseif ($block === 'overview_conclusion') {
            PlanEstrategicoConclusion::save($supabase, $idProyecto, $descripcion);
        }

        if ($this->wantsJson()) {
            $this->jsonOk('Cambios guardados correctamente.');
        }
        Session::flash('success', 'Cambios guardados correctamente.');
        $this->redirect('/detalle-proyecto.php?t=' . urlencode($token) . '&section=' . urlencode($editQuery));
    }

    private function wantsJson(): bool
    {
        $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));
        if (str_contains($accept, 'application/json')) {
            return true;
        }
        $xhr = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));
        return $xhr === 'xmlhttprequest';
    }

    private function jsonOk(string $message, array $extra = []): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true, 'message' => $message] + $extra, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    private function jsonError(string $message, int $status): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    private function findAccessibleProyecto(SupabaseClient $supabase, int $idProyecto, int $idPersona): ?array
    {
        if ($idProyecto <= 0 || $idPersona <= 0) {
            return null;
        }

        $proyecto = Proyecto::findById($supabase, $idProyecto);
        if ($proyecto === null) {
            return null;
        }

        if ($this->isCreadorProyecto($proyecto, $idPersona)) {
            return $proyecto;
        }

        if (ProyectoMiembro::exists($supabase, $idProyecto, $idPersona)) {
            return $proyecto;
        }

        return null;
    }

    private function isCreadorProyecto(array $proyecto, int $idPersona): bool
    {
        return (int) ($proyecto['creador_id'] ?? 0) === (int) $idPersona;
    }

    private function redirect(string $path): void
    {
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '\\/');
        $location = $basePath === '' ? $path : ($basePath . $path);
        header('Location: ' . $location);
        exit;
    }

    private function debugInviteLog(string $event, array $context): void
    {
        if (!$this->isDebug()) {
            return;
        }
        $email = (string) ($context['email'] ?? '');
        if ($email !== '') {
            $context['email'] = $this->maskEmail($email);
        }
        $payload = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        error_log('[invite_member] ' . $event . ' ' . ($payload ?: ''));
    }

    private function maskEmail(string $email): string
    {
        $email = trim($email);
        $parts = explode('@', $email, 2);
        if (count($parts) !== 2) {
            return $email === '' ? '' : '***';
        }
        $local = $parts[0];
        $domain = $parts[1];
        $head = mb_substr($local, 0, 1, 'UTF-8');
        return $head . '***@' . $domain;
    }

    private function friendlySupabaseError(Throwable $e, string $prefix): string
    {
        $message = $e->getMessage();
        $lower = strtolower($message);

        $hint = ' Revisa SUPABASE_URL, SUPABASE_ANON_KEY y que existan las tablas (proyecto, mision, vision, valor) en Supabase.';

        if (str_contains($lower, 'permission denied') || str_contains($lower, 'row level security') || str_contains($lower, 'rls')) {
            $hint = ' Falta permiso (RLS). Usa SUPABASE_SERVICE_ROLE_KEY en el .env o configura policies.';
        } elseif (str_contains($lower, 'does not exist') && str_contains($lower, 'relation')) {
            $hint = ' No existen las tablas en la base (ejecuta base-datos.sql en el SQL Editor de Supabase).';
        }

        if ($this->isDebug()) {
            return $prefix . $hint . ' Detalle: ' . $message;
        }

        return $prefix . $hint;
    }

    private function isDebug(): bool
    {
        $value = getenv('APP_DEBUG');
        if ($value === false) {
            return false;
        }

        $value = strtolower(trim((string) $value));
        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }

    private function issueProjectToken(int $idProyecto): string
    {
        Session::start();
        $tokens = Session::get('project_tokens', []);
        if (!is_array($tokens)) {
            $tokens = [];
        }

        foreach ($tokens as $t => $id) {
            if ((int) $id === (int) $idProyecto && is_string($t) && $t !== '') {
                return $t;
            }
        }

        $token = bin2hex(random_bytes(16));
        $tokens[$token] = (int) $idProyecto;
        Session::set('project_tokens', $tokens);
        return $token;
    }

    private function projectIdFromToken(string $token): int
    {
        if ($token === '' || !preg_match('/^[a-f0-9]{32}$/', $token)) {
            return 0;
        }

        Session::start();
        $tokens = Session::get('project_tokens', []);
        if (!is_array($tokens)) {
            return 0;
        }

        $id = $tokens[$token] ?? 0;
        return (int) $id;
    }

    private function attachProjectTokens(array $proyectos): array
    {
        $out = [];
        foreach ($proyectos as $p) {
            if (!is_array($p)) {
                continue;
            }

            $id = (int) ($p['id_proyecto'] ?? 0);
            if ($id > 0) {
                $p['token'] = $this->issueProjectToken($id);
            }

            $out[] = $p;
        }

        return $out;
    }

    private function issueObjetivoEstrategicoToken(int $idObjetivoEst): string
    {
        Session::start();
        $tokens = Session::get('obj_est_tokens', []);
        if (!is_array($tokens)) {
            $tokens = [];
        }

        foreach ($tokens as $t => $id) {
            if ((int) $id === (int) $idObjetivoEst && is_string($t) && $t !== '') {
                return $t;
            }
        }

        $token = bin2hex(random_bytes(16));
        $tokens[$token] = (int) $idObjetivoEst;
        Session::set('obj_est_tokens', $tokens);
        return $token;
    }

    private function objetivoEstrategicoIdFromToken(string $token): int
    {
        if ($token === '' || !preg_match('/^[a-f0-9]{32}$/', $token)) {
            return 0;
        }

        Session::start();
        $tokens = Session::get('obj_est_tokens', []);
        if (!is_array($tokens)) {
            return 0;
        }

        return (int) ($tokens[$token] ?? 0);
    }

    private function issueObjetivoEspecificoToken(int $idObjetivoEsp): string
    {
        Session::start();
        $tokens = Session::get('obj_esp_tokens', []);
        if (!is_array($tokens)) {
            $tokens = [];
        }

        foreach ($tokens as $t => $id) {
            if ((int) $id === (int) $idObjetivoEsp && is_string($t) && $t !== '') {
                return $t;
            }
        }

        $token = bin2hex(random_bytes(16));
        $tokens[$token] = (int) $idObjetivoEsp;
        Session::set('obj_esp_tokens', $tokens);
        return $token;
    }

    private function objetivoEspecificoIdFromToken(string $token): int
    {
        if ($token === '' || !preg_match('/^[a-f0-9]{32}$/', $token)) {
            return 0;
        }

        Session::start();
        $tokens = Session::get('obj_esp_tokens', []);
        if (!is_array($tokens)) {
            return 0;
        }

        return (int) ($tokens[$token] ?? 0);
    }

    private function attachObjetivoEstrategicoTokens(array $objetivos): array
    {
        $out = [];
        foreach ($objetivos as $o) {
            if (!is_array($o)) {
                continue;
            }

            $id = (int) ($o['id_objetivo_est'] ?? 0);
            if ($id > 0) {
                $o['token'] = $this->issueObjetivoEstrategicoToken($id);
            }

            $out[] = $o;
        }

        return $out;
    }

    private function groupObjetivosEspecificosByEstrategicoWithTokens(array $especificos): array
    {
        $out = [];
        foreach ($especificos as $e) {
            if (!is_array($e)) {
                continue;
            }

            $idEsp = (int) ($e['id_objetivo_esp'] ?? 0);
            $idEst = (int) ($e['id_objetivo_est'] ?? 0);
            if ($idEsp > 0) {
                $e['token'] = $this->issueObjetivoEspecificoToken($idEsp);
            }

            if (!isset($out[$idEst])) {
                $out[$idEst] = [];
            }

            $out[$idEst][] = $e;
        }

        return $out;
    }

    private function attachEspecificosCountToObjetivosEstrategicos(array $estrategicos, array $especificosByEstrategico): array
    {
        $out = [];
        foreach ($estrategicos as $o) {
            if (!is_array($o)) {
                continue;
            }

            $id = (int) ($o['id_objetivo_est'] ?? 0);
            $o['especificos_count'] = isset($especificosByEstrategico[$id]) ? count($especificosByEstrategico[$id]) : 0;
            $out[] = $o;
        }

        return $out;
    }
}
