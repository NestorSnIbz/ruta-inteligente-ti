<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Detalle Proyecto - Ruta Inteligente TI</title>
  <link href="/dist/output.css" rel="stylesheet" />
</head>

<body class="min-h-screen bg-neutral-50 text-neutral-900">
<?php
  $proyectoNombre = is_array($proyecto ?? null) ? (string) ($proyecto['nombre'] ?? '') : '';
  $idProyecto = is_array($proyecto ?? null) ? (int) ($proyecto['id_proyecto'] ?? 0) : 0;
  $misionTexto = is_array($mision ?? null) ? (string) ($mision['descripcion'] ?? '') : '';
  $visionTexto = is_array($vision ?? null) ? (string) ($vision['descripcion'] ?? '') : '';
  $valores = is_array($valores ?? null) ? $valores : [];
  $edit = (string) ($edit ?? '');
  $projectToken = (string) ($projectToken ?? '');
  $objetivosEstrategicos = is_array($objetivosEstrategicos ?? null) ? $objetivosEstrategicos : [];
  $objetivosEspecificosByEstrategico = is_array($objetivosEspecificosByEstrategico ?? null) ? $objetivosEspecificosByEstrategico : [];
  $objetivosError = (string) ($objetivosError ?? '');
  $oeEditToken = (string) ($oeEditToken ?? '');
  $oespEditToken = (string) ($oespEditToken ?? '');
  $cadenaPreguntas = is_array($cadenaPreguntas ?? null) ? $cadenaPreguntas : [];
  $cadenaRespuestas = is_array($cadenaRespuestas ?? null) ? $cadenaRespuestas : [];
  $cadenaCalc = is_array($cadenaCalc ?? null) ? $cadenaCalc : [
    'sum' => 0,
    'valid' => 0,
    'count' => 0,
    'missing' => 0,
    'potential' => null,
  ];
  $fodaFortalezas = is_array($fodaFortalezas ?? null) ? $fodaFortalezas : [];
  $fodaDebilidades = is_array($fodaDebilidades ?? null) ? $fodaDebilidades : [];
  $cadenaOverview = is_array($cadenaOverview ?? null) ? $cadenaOverview : [];
  $bcgOverview = is_array($bcgOverview ?? null) ? $bcgOverview : [];
  $perfilOverview = is_array($perfilOverview ?? null) ? $perfilOverview : [];
  $pestOverview = is_array($pestOverview ?? null) ? $pestOverview : [];
  $fodaOverview = is_array($fodaOverview ?? null) ? $fodaOverview : [];
?>

<div class="min-h-screen grid grid-cols-1 md:grid-cols-[16rem_1fr]">

  <?php
    $sidebarActive = 'proyectos';
    $sidebarSeedProjects = [];
    $sidebarCurrentProject = ['id' => $idProyecto, 'name' => $proyectoNombre];
    include __DIR__ . '/../layouts/sidebar.php';
  ?>

  <!-- MAIN -->
  <div class="flex flex-col">

    <!-- HEADER -->
    <header class="bg-white border-b border-neutral-200">
      <div class="px-6 py-4 flex items-center justify-between">

        <div>
          <h1 class="text-2xl font-semibold tracking-tight">
            <span id="project-name-label"><?php echo htmlspecialchars($proyectoNombre, ENT_QUOTES, 'UTF-8'); ?></span>
          </h1>

          <div id="project-name-edit" class="mt-2 hidden flex flex-wrap items-center gap-2">
            <input
              id="project-name-input"
              type="text"
              value="<?php echo htmlspecialchars($proyectoNombre, ENT_QUOTES, 'UTF-8'); ?>"
              class="h-10 w-full max-w-lg rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200"
            />
            <button id="project-name-save" type="button" class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-700">
              Guardar
            </button>
            <button id="project-name-cancel" type="button" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-800 hover:bg-neutral-50">
              Cancelar
            </button>
          </div>

          <p class="text-sm text-neutral-600 mt-1">
            Panel estratégico: Misión, Visión y Valores.
          </p>
        </div>
        <div class="flex items-center gap-2">
          <?php if (!empty($isCreador)) : ?>
            <button id="project-name-edit-btn" type="button" class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100">
              Editar nombre
            </button>
          <?php endif; ?>
          <a href="proyectos.php" class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100">
            Volver
          </a>
        </div>

      </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 p-6">
      <?php if (!empty($error) || !empty($success)) : ?>
        <div
          id="flash-modal"
          class="fixed inset-0 z-50 flex items-center justify-center px-4"
          role="dialog"
          aria-modal="true"
          aria-labelledby="flash-modal-title"
        >
          <div id="flash-backdrop" class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm"></div>

          <div class="relative w-full max-w-md rounded-3xl border border-neutral-200 bg-white p-6 text-center shadow-xl">
            <?php if (!empty($success)) : ?>
              <div class="mx-auto mb-4 inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-white">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5" />
                </svg>
              </div>
              <div id="flash-modal-title" class="text-base font-semibold text-neutral-900">Listo</div>
              <div class="mt-2 text-sm text-neutral-700">
                <?php echo htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8'); ?>
              </div>
            <?php else : ?>
              <div class="mx-auto mb-4 inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-red-600 text-white">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86l-8.2 14.2A2 2 0 003.82 21h16.36a2 2 0 001.73-2.94l-8.2-14.2a2 2 0 00-3.42 0z" />
                </svg>
              </div>
              <div id="flash-modal-title" class="text-base font-semibold text-neutral-900">Ocurrió un error</div>
              <div class="mt-2 text-sm text-neutral-700">
                <?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?>
              </div>
            <?php endif; ?>

            <div class="mt-6 flex justify-center">
              <button
                id="flash-close"
                type="button"
                class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700"
              >
                Cerrar
              </button>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <div class="mb-6 rounded-2xl border border-neutral-200 bg-white p-2 shadow-sm">
        <div class="flex flex-wrap gap-2">
          <button type="button" data-panel="overview" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Overview
          </button>
          <button type="button" data-panel="mision" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Misión
          </button>
          <button type="button" data-panel="vision" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Visión
          </button>
          <button type="button" data-panel="valores" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Valores
          </button>
          <button type="button" data-panel="objetivos" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Objetivos
          </button>
          <button type="button" data-panel="cadena" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Cadena de valor
          </button>
          <button type="button" data-panel="bgg" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            BCG
          </button>
          <button type="button" data-panel="perfil_competitivo" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Perfil competitivo
          </button>
          <button type="button" data-panel="pest" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            P.E.S.T.
          </button>
          <button type="button" data-panel="estrategias" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Estrategias
          </button>
          <button type="button" data-panel="came" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            CAME
          </button>
          
        </div>
      </div>

      <div class="space-y-6">
        <section id="panel-overview" class="project-panel bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-start justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold">Overview</h2>
              <p class="mt-1 text-sm text-neutral-600">Resumen general del proyecto.</p>
            </div>
            <div class="flex flex-wrap items-center justify-end gap-2">
              <a
                href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&export=overview_pdf"
                download
                class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-brand-700"
              >
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-3-3m3 3l3-3" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 17v3a1 1 0 001 1h14a1 1 0 001-1v-3" />
                </svg>
                Descargar PDF
              </a>
              <?php if (!empty($isCreador)) : ?>
              <button
                id="members-manage-open"
                type="button"
                class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50"
              >
                <svg viewBox="0 0 24 24" class="h-5 w-5 text-neutral-700" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 11a4 4 0 100-8 4 4 0 000 8z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 00-3-3.87" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 010 7.75" />
                </svg>
                Gestionar Miembros
              </button>
              <?php endif; ?>
            </div>
          </div>

          <div class="mt-5 space-y-4">
            <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
              <div class="text-sm font-semibold text-neutral-900">Misión</div>
              <?php if ($misionTexto === '') : ?>
                <div class="mt-3 text-sm text-neutral-600">Aún no se registró la misión.</div>
              <?php else : ?>
                <div class="mt-3 text-sm text-neutral-700 leading-relaxed">
                  <?php echo nl2br(htmlspecialchars($misionTexto, ENT_QUOTES, 'UTF-8')); ?>
                </div>
              <?php endif; ?>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
              <div class="text-sm font-semibold text-neutral-900">Visión</div>
              <?php if ($visionTexto === '') : ?>
                <div class="mt-3 text-sm text-neutral-600">Aún no se registró la visión.</div>
              <?php else : ?>
                <div class="mt-3 text-sm text-neutral-700 leading-relaxed">
                  <?php echo nl2br(htmlspecialchars($visionTexto, ENT_QUOTES, 'UTF-8')); ?>
                </div>
              <?php endif; ?>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
              <div class="text-sm font-semibold text-neutral-900">Valores</div>
              <?php if (empty($valores)) : ?>
                <div class="mt-3 text-sm text-neutral-600">Aún no se registraron valores.</div>
              <?php else : ?>
                <div class="mt-4 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
                  <table class="min-w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                      <tr>
                        <th scope="col" class="w-14 px-4 py-3">#</th>
                        <th scope="col" class="px-4 py-3">Valor</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                      <?php foreach ($valores as $i => $valor) : ?>
                        <tr>
                          <td class="px-4 py-3 text-neutral-500"><?php echo (int) $i + 1; ?></td>
                          <td class="px-4 py-3 text-neutral-800">
                            <?php echo htmlspecialchars((string) ($valor['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
              <div class="text-sm font-semibold text-neutral-900">Objetivos</div>
              <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                <div class="text-sm text-neutral-600">
                  Objetivos estratégicos y sus objetivos específicos.
                </div>
                <button
                  type="button"
                  data-open-panel="objetivos"
                  class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50"
                >
                  Gestionar
                </button>
              </div>

              <?php if (!empty($objetivosError)) : ?>
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                  <?php echo htmlspecialchars((string) $objetivosError, ENT_QUOTES, 'UTF-8'); ?>
                </div>
              <?php elseif (empty($objetivosEstrategicos)) : ?>
                <div class="mt-4 text-sm text-neutral-600">
                  Aún no hay objetivos registrados.
                </div>
              <?php else : ?>
                <div class="mt-4 space-y-3">
                  <?php foreach ($objetivosEstrategicos as $idx => $obj) : ?>
                    <?php
                      $idObjEst = (int) ($obj['id_objetivo_est'] ?? 0);
                      $especificos = $objetivosEspecificosByEstrategico[$idObjEst] ?? [];
                      $especificos = is_array($especificos) ? $especificos : [];
                    ?>
                    <div class="rounded-xl border border-neutral-200 bg-white p-4">
                      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                        <div>
                          <div class="text-xs font-semibold text-neutral-600">Objetivo estratégico</div>
                          <div class="mt-2 text-sm text-neutral-800 leading-relaxed">
                            <?php echo nl2br(htmlspecialchars((string) ($obj['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8')); ?>
                          </div>
                        </div>
                        <div>
                          <div class="text-xs font-semibold text-neutral-600">Objetivos específicos</div>
                          <?php if (empty($especificos)) : ?>
                            <div class="mt-2 text-sm text-neutral-600">Sin objetivos específicos.</div>
                          <?php else : ?>
                            <ul class="mt-2 space-y-1 text-sm text-neutral-800">
                              <?php foreach ($especificos as $esp) : ?>
                                <li class="flex gap-2">
                                  <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-neutral-400"></span>
                                  <span class="leading-relaxed">
                                    <?php echo htmlspecialchars((string) ($esp['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                  </span>
                                </li>
                              <?php endforeach; ?>
                            </ul>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>

            <?php
              $cBadge = (string) ($cadenaOverview['badge_class'] ?? 'bg-neutral-100 text-neutral-700');
              $cStatus = (string) ($cadenaOverview['status_label'] ?? 'Sin evaluación');
              $cSub = (string) ($cadenaOverview['status_sub'] ?? '');
              $cSum = $cadenaOverview['sum'] ?? null;
              $cPotential = $cadenaOverview['potential'] ?? null;
              $cPotentialText = ($cPotential !== null && is_numeric($cPotential)) ? number_format((float) $cPotential, 2, '.', '') : '—';
              $cPotentialPct = ($cPotential !== null && is_numeric($cPotential)) ? ((string) round(((float) $cPotential) * 100) . '%') : '';

              $bBadge = (string) ($bcgOverview['badge_class'] ?? 'bg-neutral-100 text-neutral-700');
              $bStatus = (string) ($bcgOverview['status_label'] ?? 'Sin datos');
              $bSub = (string) ($bcgOverview['status_sub'] ?? '');
              $bTotal = (int) ($bcgOverview['total'] ?? 0);
              $bCounts = is_array($bcgOverview['counts'] ?? null) ? (array) $bcgOverview['counts'] : [];
              $bTop = is_array($bcgOverview['top'] ?? null) ? (array) $bcgOverview['top'] : [];

              $pcBadge = (string) ($perfilOverview['badge_class'] ?? 'bg-neutral-100 text-neutral-700');
              $pcStatus = (string) ($perfilOverview['status_label'] ?? 'Sin evaluación');
              $pcSub = (string) ($perfilOverview['status_sub'] ?? '');
              $pcTotal = $perfilOverview['total'] ?? null;
              $pcConclusion = trim((string) ($perfilOverview['conclusion_text'] ?? ''));

              $pBadge = (string) ($pestOverview['badge_class'] ?? 'bg-neutral-100 text-neutral-700');
              $pStatus = (string) ($pestOverview['status_label'] ?? 'Sin evaluación');
              $pSub = (string) ($pestOverview['status_sub'] ?? '');
              $pPct = is_array($pestOverview['pct'] ?? null) ? (array) $pestOverview['pct'] : null;
              $pAvg = is_array($pPct) ? (int) round((((int) ($pPct['SOCIALES'] ?? 0)) + ((int) ($pPct['MEDIOAMBIENTALES'] ?? 0)) + ((int) ($pPct['POLITICOS'] ?? 0)) + ((int) ($pPct['ECONOMICOS'] ?? 0)) + ((int) ($pPct['TECNOLOGICOS'] ?? 0))) / 5) : null;

              $fCadena = is_array($fodaOverview['CADENA_VALOR_INTERNA'] ?? null) ? (array) $fodaOverview['CADENA_VALOR_INTERNA'] : [];
              $fBcg = is_array($fodaOverview['AUTODIAGNOSTICO_BCG'] ?? null) ? (array) $fodaOverview['AUTODIAGNOSTICO_BCG'] : [];
              $fPerfil = is_array($fodaOverview['PERFIL_COMPETITIVO'] ?? null) ? (array) $fodaOverview['PERFIL_COMPETITIVO'] : [];
              $fPest = is_array($fodaOverview['PEST'] ?? null) ? (array) $fodaOverview['PEST'] : [];
              $fCadenaLabel = (string) ($fCadena['label'] ?? 'Cadena de valor');
              $fBcgLabel = (string) ($fBcg['label'] ?? 'Matriz BCG');
              $fPerfilLabel = (string) ($fPerfil['label'] ?? 'Perfil competitivo');
              $fPestLabel = (string) ($fPest['label'] ?? 'P.E.S.T.');
              $fFortCadena = is_array($fCadena['FORTALEZA'] ?? null) ? (array) $fCadena['FORTALEZA'] : [];
              $fDebCadena = is_array($fCadena['DEBILIDAD'] ?? null) ? (array) $fCadena['DEBILIDAD'] : [];
              $fFortBcg = is_array($fBcg['FORTALEZA'] ?? null) ? (array) $fBcg['FORTALEZA'] : [];
              $fDebBcg = is_array($fBcg['DEBILIDAD'] ?? null) ? (array) $fBcg['DEBILIDAD'] : [];
              $fOppPerfil = is_array($fPerfil['OPORTUNIDAD'] ?? null) ? (array) $fPerfil['OPORTUNIDAD'] : [];
              $fAmePerfil = is_array($fPerfil['AMENAZA'] ?? null) ? (array) $fPerfil['AMENAZA'] : [];
              $fOppPest = is_array($fPest['OPORTUNIDAD'] ?? null) ? (array) $fPest['OPORTUNIDAD'] : [];
              $fAmePest = is_array($fPest['AMENAZA'] ?? null) ? (array) $fPest['AMENAZA'] : [];
            ?>

            <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
              <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                  <div class="text-sm font-semibold text-neutral-900">Análisis Estratégico</div>
                  <div class="mt-1 text-sm text-neutral-600">Resumen ejecutivo de Cadena de valor, Matriz BCG, Perfil competitivo y P.E.S.T.</div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                  <button type="button" data-open-panel="cadena" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
                    Ver Cadena
                  </button>
                  <button type="button" data-open-panel="bgg" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
                    Ver BCG
                  </button>
                  <button type="button" data-open-panel="perfil_competitivo" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
                    Ver Perfil
                  </button>
                  <button type="button" data-open-panel="pest" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
                    Ver P.E.S.T.
                  </button>
                </div>
              </div>

              <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-neutral-200 bg-white p-5">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <div class="text-sm font-semibold text-neutral-900">Cadena de valor</div>
                      <div class="mt-1 text-sm text-neutral-600">Potencial de mejora y estado general.</div>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?php echo htmlspecialchars($cBadge, ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo htmlspecialchars($cStatus, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </div>
                  <?php if ($cSub !== '') : ?>
                    <div class="mt-2 text-xs text-neutral-600"><?php echo htmlspecialchars($cSub, ENT_QUOTES, 'UTF-8'); ?></div>
                  <?php endif; ?>
                  <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                      <div class="text-xs font-medium text-neutral-600">Suma</div>
                      <div class="mt-1 text-2xl font-semibold text-neutral-900"><?php echo ($cSum === null) ? '—' : (int) $cSum; ?></div>
                    </div>
                    <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                      <div class="text-xs font-medium text-neutral-600">Potencial</div>
                      <div class="mt-1 text-2xl font-semibold text-brand-900"><?php echo htmlspecialchars($cPotentialText, ENT_QUOTES, 'UTF-8'); ?></div>
                      <div class="mt-1 text-xs text-neutral-500"><?php echo htmlspecialchars($cPotentialPct, ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                    <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                      <div class="text-xs font-medium text-neutral-600">Interpretación</div>
                      <div class="mt-1 text-sm font-semibold text-neutral-900"><?php echo htmlspecialchars($cStatus, ENT_QUOTES, 'UTF-8'); ?></div>
                      <div class="mt-1 text-xs text-neutral-500">Basado en 1 - (Σ/100).</div>
                    </div>
                  </div>
                  <?php
                    $cBar = ($cPotential !== null && is_numeric($cPotential)) ? max(0, min(100, (int) round(((float) $cPotential) * 100))) : 0;
                  ?>
                  <div class="mt-4">
                    <div class="flex items-center justify-between text-xs text-neutral-600">
                      <span>Potencial de mejora</span>
                      <span><?php echo htmlspecialchars($cPotentialPct !== '' ? $cPotentialPct : '—', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-neutral-200">
                      <div class="h-2 rounded-full bg-brand-600" style="width: <?php echo (int) $cBar; ?>%"></div>
                    </div>
                  </div>
                </div>

                <div class="rounded-2xl border border-neutral-200 bg-white p-5">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <div class="text-sm font-semibold text-neutral-900">Matriz BCG</div>
                      <div class="mt-1 text-sm text-neutral-600">Clasificación y métricas por producto.</div>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?php echo htmlspecialchars($bBadge, ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo htmlspecialchars($bStatus, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </div>
                  <?php if ($bSub !== '') : ?>
                    <div class="mt-2 text-xs text-neutral-600"><?php echo htmlspecialchars($bSub, ENT_QUOTES, 'UTF-8'); ?></div>
                  <?php endif; ?>
                  <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                      <div class="text-xs font-medium text-neutral-600">Productos</div>
                      <div class="mt-1 text-2xl font-semibold text-neutral-900"><?php echo (int) $bTotal; ?></div>
                      <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-neutral-700">
                        <div class="rounded-lg border border-neutral-200 bg-white px-2 py-1">Estrella: <?php echo (int) ($bCounts['ESTRELLA'] ?? 0); ?></div>
                        <div class="rounded-lg border border-neutral-200 bg-white px-2 py-1">Vaca: <?php echo (int) ($bCounts['VACA'] ?? 0); ?></div>
                        <div class="rounded-lg border border-neutral-200 bg-white px-2 py-1">Interrogante: <?php echo (int) ($bCounts['INTERROGANTE'] ?? 0); ?></div>
                        <div class="rounded-lg border border-neutral-200 bg-white px-2 py-1">Perro: <?php echo (int) ($bCounts['PERRO'] ?? 0); ?></div>
                      </div>
                      <?php
                        $bStar = (int) ($bCounts['ESTRELLA'] ?? 0);
                        $bCow = (int) ($bCounts['VACA'] ?? 0);
                        $bQ = (int) ($bCounts['INTERROGANTE'] ?? 0);
                        $bDog = (int) ($bCounts['PERRO'] ?? 0);
                        $bDen = max(1, $bTotal);
                        $pStar = (int) round(($bStar / $bDen) * 100);
                        $pCow = (int) round(($bCow / $bDen) * 100);
                        $pQ = (int) round(($bQ / $bDen) * 100);
                        $pDog = max(0, 100 - ($pStar + $pCow + $pQ));
                      ?>
                      <div class="mt-3">
                        <div class="flex items-center justify-between text-xs text-neutral-600">
                          <span>Distribución</span>
                          <span><?php echo (int) $bTotal; ?> total</span>
                        </div>
                        <div class="mt-2 flex h-2 w-full overflow-hidden rounded-full bg-neutral-200">
                          <div class="h-2 bg-emerald-500" style="width: <?php echo (int) $pStar; ?>%"></div>
                          <div class="h-2 bg-amber-400" style="width: <?php echo (int) $pCow; ?>%"></div>
                          <div class="h-2 bg-sky-500" style="width: <?php echo (int) $pQ; ?>%"></div>
                          <div class="h-2 bg-red-500" style="width: <?php echo (int) $pDog; ?>%"></div>
                        </div>
                      </div>
                    </div>
                    <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                      <div class="text-xs font-medium text-neutral-600">Top productos</div>
                      <?php if (empty($bTop)) : ?>
                        <div class="mt-2 text-sm text-neutral-600">Sin registros.</div>
                      <?php else : ?>
                        <div class="mt-2 space-y-2">
                          <?php foreach ($bTop as $p) : ?>
                            <?php
                              $pn = trim((string) ($p['nombre'] ?? ''));
                              $pc = trim((string) ($p['clasificacion'] ?? ''));
                              $pp = isset($p['porcentaje_ventas']) && is_numeric($p['porcentaje_ventas']) ? round(((float) $p['porcentaje_ventas']) * 100, 1) . '%' : '—';
                            ?>
                            <div class="rounded-lg border border-neutral-200 bg-white px-3 py-2">
                              <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                  <div class="truncate text-sm font-semibold text-neutral-900"><?php echo htmlspecialchars($pn !== '' ? $pn : 'Producto', ENT_QUOTES, 'UTF-8'); ?></div>
                                  <div class="mt-0.5 text-xs text-neutral-600"><?php echo htmlspecialchars($pc !== '' ? $pc : '—', ENT_QUOTES, 'UTF-8'); ?> · Ventas: <?php echo htmlspecialchars($pp, ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <div class="rounded-2xl border border-neutral-200 bg-white p-5">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <div class="text-sm font-semibold text-neutral-900">Perfil competitivo</div>
                      <div class="mt-1 text-sm text-neutral-600">Total y conclusión del entorno próximo.</div>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?php echo htmlspecialchars($pcBadge, ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo htmlspecialchars($pcStatus, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </div>
                  <?php if ($pcSub !== '') : ?>
                    <div class="mt-2 text-xs text-neutral-600"><?php echo htmlspecialchars($pcSub, ENT_QUOTES, 'UTF-8'); ?></div>
                  <?php endif; ?>
                  <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                      <div class="text-xs font-medium text-neutral-600">Total</div>
                      <div class="mt-1 text-2xl font-semibold text-neutral-900"><?php echo ($pcTotal === null) ? '—' : (int) $pcTotal; ?></div>
                      <div class="mt-1 text-xs text-neutral-500">Suma de puntajes (0–4 por fila).</div>
                    </div>
                    <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                      <div class="text-xs font-medium text-neutral-600">Conclusión</div>
                      <div class="mt-1 text-sm font-semibold text-neutral-900"><?php echo htmlspecialchars($pcConclusion !== '' ? $pcConclusion : '—', ENT_QUOTES, 'UTF-8'); ?></div>
                      <div class="mt-1 text-xs text-neutral-500">Basado en rangos Excel.</div>
                    </div>
                  </div>
                </div>

                <div class="rounded-2xl border border-neutral-200 bg-white p-5">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <div class="text-sm font-semibold text-neutral-900">P.E.S.T.</div>
                      <div class="mt-1 text-sm text-neutral-600">Impacto de factores generales externos.</div>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?php echo htmlspecialchars($pBadge, ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo htmlspecialchars($pStatus, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </div>
                  <?php if ($pSub !== '') : ?>
                    <div class="mt-2 text-xs text-neutral-600"><?php echo htmlspecialchars($pSub, ENT_QUOTES, 'UTF-8'); ?></div>
                  <?php endif; ?>
                  <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                      <div class="text-xs font-medium text-neutral-600">Promedio</div>
                      <div class="mt-1 text-2xl font-semibold text-brand-900"><?php echo ($pAvg === null) ? '—' : (int) $pAvg; ?><?php echo ($pAvg === null) ? '' : '%'; ?></div>
                      <div class="mt-1 text-xs text-neutral-500">Promedio simple de 5 categorías.</div>
                    </div>
                    <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                      <div class="text-xs font-medium text-neutral-600">Categorías</div>
                      <?php if (!is_array($pPct)) : ?>
                        <div class="mt-2 text-sm text-neutral-600">Sin evaluación.</div>
                      <?php else : ?>
                        <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-neutral-700">
                          <div class="rounded-lg border border-neutral-200 bg-white px-2 py-1">Sociales: <?php echo (int) ($pPct['SOCIALES'] ?? 0); ?>%</div>
                          <div class="rounded-lg border border-neutral-200 bg-white px-2 py-1">Medioamb.: <?php echo (int) ($pPct['MEDIOAMBIENTALES'] ?? 0); ?>%</div>
                          <div class="rounded-lg border border-neutral-200 bg-white px-2 py-1">Políticos: <?php echo (int) ($pPct['POLITICOS'] ?? 0); ?>%</div>
                          <div class="rounded-lg border border-neutral-200 bg-white px-2 py-1">Económicos: <?php echo (int) ($pPct['ECONOMICOS'] ?? 0); ?>%</div>
                          <div class="rounded-lg border border-neutral-200 bg-white px-2 py-1">Tecnol.: <?php echo (int) ($pPct['TECNOLOGICOS'] ?? 0); ?>%</div>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
              <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                  <div class="text-sm font-semibold text-neutral-900">Análisis FODA</div>
                  <div class="mt-1 text-sm text-neutral-600">Fortalezas y debilidades (Cadena/BCG) y oportunidades/amenazas (Perfil/P.E.S.T.).</div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                  <button type="button" data-open-panel="cadena" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
                    Editar (Cadena)
                  </button>
                  <button type="button" data-open-panel="bgg" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
                    Editar (BCG)
                  </button>
                  <button type="button" data-open-panel="perfil_competitivo" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
                    Editar (Perfil)
                  </button>
                  <button type="button" data-open-panel="pest" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
                    Editar (P.E.S.T.)
                  </button>
                </div>
              </div>

              <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-neutral-200 bg-white p-4">
                  <div class="text-sm font-semibold text-neutral-900">Fortalezas</div>
                  <div class="mt-2 space-y-3 text-sm text-neutral-800">
                    <div>
                      <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fCadenaLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                      <?php if (empty($fFortCadena)) : ?>
                        <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                      <?php else : ?>
                        <ul class="mt-2 space-y-1">
                          <?php foreach ($fFortCadena as $txt) : ?>
                            <li class="flex gap-2">
                              <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></span>
                              <span class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></span>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                    <div>
                      <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fBcgLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                      <?php if (empty($fFortBcg)) : ?>
                        <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                      <?php else : ?>
                        <ul class="mt-2 space-y-1">
                          <?php foreach ($fFortBcg as $txt) : ?>
                            <li class="flex gap-2">
                              <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></span>
                              <span class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></span>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <div class="rounded-2xl border border-neutral-200 bg-white p-4">
                  <div class="text-sm font-semibold text-neutral-900">Debilidades</div>
                  <div class="mt-2 space-y-3 text-sm text-neutral-800">
                    <div>
                      <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fCadenaLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                      <?php if (empty($fDebCadena)) : ?>
                        <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                      <?php else : ?>
                        <ul class="mt-2 space-y-1">
                          <?php foreach ($fDebCadena as $txt) : ?>
                            <li class="flex gap-2">
                              <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-red-500"></span>
                              <span class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></span>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                    <div>
                      <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fBcgLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                      <?php if (empty($fDebBcg)) : ?>
                        <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                      <?php else : ?>
                        <ul class="mt-2 space-y-1">
                          <?php foreach ($fDebBcg as $txt) : ?>
                            <li class="flex gap-2">
                              <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-red-500"></span>
                              <span class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></span>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <div class="rounded-2xl border border-neutral-200 bg-white p-4">
                  <div class="text-sm font-semibold text-neutral-900">Oportunidades</div>
                  <div class="mt-2 space-y-3 text-sm text-neutral-800">
                    <div>
                      <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fPerfilLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                      <?php if (empty($fOppPerfil)) : ?>
                        <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                      <?php else : ?>
                        <ul class="mt-2 space-y-1">
                          <?php foreach ($fOppPerfil as $txt) : ?>
                            <li class="flex gap-2">
                              <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-sky-500"></span>
                              <span class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></span>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                    <div>
                      <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fPestLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                      <?php if (empty($fOppPest)) : ?>
                        <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                      <?php else : ?>
                        <ul class="mt-2 space-y-1">
                          <?php foreach ($fOppPest as $txt) : ?>
                            <li class="flex gap-2">
                              <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-sky-500"></span>
                              <span class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></span>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <div class="rounded-2xl border border-neutral-200 bg-white p-4">
                  <div class="text-sm font-semibold text-neutral-900">Amenazas</div>
                  <div class="mt-2 space-y-3 text-sm text-neutral-800">
                    <div>
                      <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fPerfilLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                      <?php if (empty($fAmePerfil)) : ?>
                        <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                      <?php else : ?>
                        <ul class="mt-2 space-y-1">
                          <?php foreach ($fAmePerfil as $txt) : ?>
                            <li class="flex gap-2">
                              <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-orange-500"></span>
                              <span class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></span>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                    <div>
                      <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fPestLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                      <?php if (empty($fAmePest)) : ?>
                        <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                      <?php else : ?>
                        <ul class="mt-2 space-y-1">
                          <?php foreach ($fAmePest as $txt) : ?>
                            <li class="flex gap-2">
                              <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-orange-500"></span>
                              <span class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></span>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <?php if (!empty($isCreador)) : ?>
        <section id="panel-miembros" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold">Gestionar miembros</h2>
              <p class="mt-1 text-sm text-neutral-600">Invita por correo y administra accesos del proyecto.</p>
            </div>
            <button id="members-back" type="button" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
              Volver
            </button>
          </div>

          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
              <div class="text-sm font-semibold text-neutral-900">Invitar</div>
              <form id="invite-member-form" class="flex w-full max-w-xl items-center gap-2 sm:w-auto" method="post" action="detalle-proyecto.php">
                <input type="hidden" name="action" value="invite_member" />
                <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
                <input
                  type="email"
                  name="email"
                  placeholder="Invitar por email"
                  class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200"
                  required
                />
                <button type="submit" class="h-10 shrink-0 rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                  Invitar
                </button>
              </form>
            </div>

            <div class="mt-4 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
              <table class="min-w-full text-left text-sm">
                <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                  <tr>
                    <th scope="col" class="px-4 py-3">Nombre</th>
                    <th scope="col" class="px-4 py-3">Email</th>
                    <th scope="col" class="w-32 px-4 py-3">Rol</th>
                    <th scope="col" class="w-32 px-4 py-3 text-right">Acción</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                  <?php if (empty($miembros)) : ?>
                    <tr>
                      <td colspan="4" class="px-4 py-4 text-sm text-neutral-600">Aún no hay miembros.</td>
                    </tr>
                  <?php else : ?>
                    <?php foreach ($miembros as $m) : ?>
                      <tr data-member-row="<?php echo (int) ($m['id_persona'] ?? 0); ?>">
                        <td class="px-4 py-3 text-neutral-800">
                          <?php echo htmlspecialchars((string) ($m['nombre'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td class="px-4 py-3 text-neutral-700">
                          <?php echo htmlspecialchars((string) ($m['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td class="px-4 py-3">
                          <?php $rol = (string) ($m['rol'] ?? ''); ?>
                          <span class="<?php echo $rol === 'CREADOR' ? 'inline-flex items-center rounded-xl bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700' : 'inline-flex items-center rounded-xl bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-700'; ?>">
                            <?php echo htmlspecialchars($rol, ENT_QUOTES, 'UTF-8'); ?>
                          </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                          <?php if ($rol === 'CREADOR') : ?>
                            <span class="text-xs text-neutral-500">—</span>
                          <?php else : ?>
                            <form class="remove-member-form inline-flex" method="post" action="detalle-proyecto.php">
                              <input type="hidden" name="action" value="remove_member" />
                              <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
                              <input type="hidden" name="id_persona" value="<?php echo (int) ($m['id_persona'] ?? 0); ?>" />
                              <button type="submit" class="inline-flex h-9 items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 text-xs font-semibold text-red-700 hover:bg-red-100">
                                Eliminar
                              </button>
                            </form>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </section>
        <?php endif; ?>

        <section id="panel-mision" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="mision">
          <div class="flex items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold">Misión</h2>
              <p class="mt-1 text-sm text-neutral-600">Define la razón de ser del proyecto.</p>
            </div>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-vision" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="vision">
          <div class="flex items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold">Visión</h2>
              <p class="mt-1 text-sm text-neutral-600">Define hacia dónde se dirige el proyecto.</p>
            </div>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-valores" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="valores">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Valores</h2>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-objetivos" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="objetivos">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Objetivos</h2>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-cadena" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="cadena">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Cadena de valor</h2>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-perfil_competitivo" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="perfil_competitivo">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Perfil competitivo</h2>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-pest" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="pest">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">P.E.S.T.</h2>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-estrategias" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="estrategias">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Identificación de estrategias</h2>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-came" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="came">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Matriz CAME</h2>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-bgg" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="bgg">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Autodiagnóstico BCG</h2>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>
      </div>

    </main>
  </div>
</div>

<script src="foda-oa.js"></script>
<script src="perfil-competitivo.js"></script>
<script src="pest.js"></script>
<script src="foda-cruzada.js"></script>
<script src="came.js"></script>
<script>
  const projectToken = <?php echo json_encode((string) $projectToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  const projectId = <?php echo (int) $idProyecto; ?>;
  const projectName = <?php echo json_encode((string) $proyectoNombre, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  window.projectToken = projectToken;
  const flashModal = document.getElementById("flash-modal");
  const flashBackdrop = document.getElementById("flash-backdrop");
  const flashClose = document.getElementById("flash-close");


  const projectNameLabel = document.getElementById("project-name-label");
  const projectNameEditWrap = document.getElementById("project-name-edit");
  const projectNameInput = document.getElementById("project-name-input");
  const projectNameEditBtn = document.getElementById("project-name-edit-btn");
  const projectNameSave = document.getElementById("project-name-save");
  const projectNameCancel = document.getElementById("project-name-cancel");

  function closeFlashModal() {
    if (!flashModal) return;
    flashModal.remove();
  }

  if (flashBackdrop) {
    flashBackdrop.addEventListener("click", closeFlashModal);
  }

  if (flashClose) {
    flashClose.addEventListener("click", closeFlashModal);
  }

  function setProjectNameEditing(on) {
    if (!projectNameEditWrap) return;
    projectNameEditWrap.classList.toggle("hidden", !on);
    if (projectNameEditBtn) projectNameEditBtn.classList.toggle("hidden", on);
    if (on) {
      if (projectNameInput) {
        projectNameInput.value = String(projectNameLabel ? projectNameLabel.textContent || "" : projectName || "");
        projectNameInput.focus();
        projectNameInput.select();
      }
    }
  }

  async function saveProjectName() {
    const name = String(projectNameInput ? projectNameInput.value : "").trim();
    if (!name) {
      if (typeof showInlineToast === "function") showInlineToast("Error", "El nombre no puede quedar vacío.");
      return;
    }
    if (projectNameSave) {
      projectNameSave.disabled = true;
      projectNameSave.textContent = "Guardando…";
    }
    try {
      const fd = new FormData();
      fd.set("action", "update_project_name");
      fd.set("t", String(projectToken || ""));
      fd.set("nombre", name);

      const res = await fetch("detalle-proyecto.php", {
        method: "POST",
        headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
        body: fd,
      });
      const json = await res.json().catch(() => null);
      if (!res.ok || !json || json.ok !== true) {
        const msg = (json && json.error) ? String(json.error) : "No se pudo actualizar el nombre.";
        if (typeof showInlineToast === "function") showInlineToast("Error", msg);
        return;
      }
      if (projectNameLabel) projectNameLabel.textContent = name;
      document.title = `${name} - Ruta Inteligente TI`;
      try {
        if (window.RISidebar && typeof window.RISidebar.pushRecentProject === "function") {
          window.RISidebar.pushRecentProject(Number(projectId || 0), name);
        }
      } catch {}
      setProjectNameEditing(false);
      if (typeof showInlineToast === "function") showInlineToast("Guardado", "Nombre actualizado correctamente.");
    } catch (e) {
      if (typeof showInlineToast === "function") showInlineToast("Error", "No se pudo actualizar el nombre.");
    } finally {
      if (projectNameSave) {
        projectNameSave.disabled = false;
        projectNameSave.textContent = "Guardar";
      }
    }
  }

  if (projectNameEditBtn) {
    projectNameEditBtn.addEventListener("click", () => setProjectNameEditing(true));
  }
  if (projectNameCancel) {
    projectNameCancel.addEventListener("click", () => setProjectNameEditing(false));
  }
  if (projectNameSave) {
    projectNameSave.addEventListener("click", () => saveProjectName());
  }
  if (projectNameInput) {
    projectNameInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        saveProjectName();
      }
      if (e.key === "Escape") {
        e.preventDefault();
        setProjectNameEditing(false);
      }
    });
  }

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeFlashModal();
    }
  });

  const allowedPanels = new Set(["overview", "mision", "vision", "valores", "objetivos", "cadena", "perfil_competitivo", "pest", "estrategias", "came", "bgg"]);
  const alwaysRefreshPanels = new Set(["overview", "estrategias", "came"]);
  const panelStorageKey = projectToken ? `ri:detalle-proyecto:section:${projectToken}` : "ri:detalle-proyecto:section";

  const projectTabs = Array.from(document.querySelectorAll(".project-tab"));
  const loadedPanels = new Set(["overview"]);
  if (document.getElementById("panel-miembros")) loadedPanels.add("miembros");
  const inflight = new Map();
  let activePanelId = "overview";

  function setActiveProjectPanel(panelId, options = {}) {
    const { updateUrl = true } = options;
    activePanelId = panelId || "";

    document.querySelectorAll(".project-panel").forEach((panel) => panel.classList.add("hidden"));
    if (panelId) {
      const activePanel = document.getElementById(`panel-${panelId}`);
      if (activePanel) {
        activePanel.classList.remove("hidden");
      }
    }

    projectTabs.forEach((tab) => {
      const highlightPanel = panelId === "miembros" ? "overview" : panelId;
      const isActive = highlightPanel && tab.getAttribute("data-panel") === highlightPanel;
      tab.className = isActive
        ? "project-tab rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white"
        : "project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50";
    });

    if (panelId && allowedPanels.has(panelId)) {
      try {
        window.localStorage.setItem(panelStorageKey, panelId);
      } catch (e) {}
    }

    if (!updateUrl) return;

    const url = new URL(window.location.href);
    if (panelId === "miembros") {
      url.searchParams.set("members", "1");
      url.searchParams.set("section", "overview");
    } else {
      url.searchParams.delete("members");
      if (panelId) {
        url.searchParams.set("section", panelId);
      } else {
        url.searchParams.delete("section");
      }
    }
    const edit = url.searchParams.get("edit");
    if (edit && edit !== panelId) {
      url.searchParams.delete("edit");
    }
    if (panelId !== "objetivos") {
      url.searchParams.delete("oe_edit");
      url.searchParams.delete("oesp_edit");
    }
    window.history.replaceState({}, "", url.toString());
  }

  async function ensurePanelLoaded(panelId) {
    if (!panelId) return;
    if (panelId === "miembros") return;
    if (!allowedPanels.has(panelId)) return;
    if (alwaysRefreshPanels.has(panelId)) return reloadPanel(panelId);
    if (loadedPanels.has(panelId)) return;
    if (!projectToken) return;

    if (inflight.has(panelId)) return inflight.get(panelId);

    const task = (async () => {
      const current = document.getElementById(`panel-${panelId}`);
      if (!current) return;
      try {
        const u = new URL("detalle-proyecto.php", window.location.href);
        u.searchParams.set("t", String(projectToken));
        u.searchParams.set("partial", String(panelId));
        ["edit", "oe_edit", "oesp_edit", "members"].forEach((k) => {
          const v = new URL(window.location.href).searchParams.get(k);
          if (v) u.searchParams.set(k, v);
        });

        const res = await fetch(u.toString(), {
          headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "text/html" },
        });
        const html = await res.text();
        if (!res.ok) return;
        const shouldBeHidden = activePanelId !== panelId;
        current.outerHTML = html;
        const updated = document.getElementById(`panel-${panelId}`);
        if (updated) {
          if (shouldBeHidden) updated.classList.add("hidden");
          else updated.classList.remove("hidden");
        }
        loadedPanels.add(panelId);
        initLazyPanel(panelId);
      } catch (e) {
      } finally {
        inflight.delete(panelId);
      }
    })();

    inflight.set(panelId, task);
    return task;
  }

  async function reloadPanel(panelId) {
    if (!panelId) return;
    if (panelId === "miembros") return;
    if (!allowedPanels.has(panelId)) return;
    if (!projectToken) return;
    if (inflight.has(panelId)) return inflight.get(panelId);

    const task = (async () => {
      const current = document.getElementById(`panel-${panelId}`);
      if (!current) return;
      try {
        const u = new URL("detalle-proyecto.php", window.location.href);
        u.searchParams.set("t", String(projectToken));
        u.searchParams.set("partial", String(panelId));
        u.searchParams.set("_ts", String(Date.now()));
        ["edit", "oe_edit", "oesp_edit", "members"].forEach((k) => {
          const v = new URL(window.location.href).searchParams.get(k);
          if (v) u.searchParams.set(k, v);
        });

        const res = await fetch(u.toString(), {
          headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "text/html", "Cache-Control": "no-cache" },
          cache: "no-store",
        });
        const html = await res.text();
        if (!res.ok) return;
        const shouldBeHidden = activePanelId !== panelId;
        current.outerHTML = html;
        const updated = document.getElementById(`panel-${panelId}`);
        if (updated) {
          if (shouldBeHidden) updated.classList.add("hidden");
          else updated.classList.remove("hidden");
        }
        loadedPanels.add(panelId);
        initLazyPanel(panelId);
      } catch (e) {
      } finally {
        inflight.delete(panelId);
      }
    })();

    inflight.set(panelId, task);
    return task;
  }

  async function openPanel(panelId) {
    if (!panelId) return;
    setActiveProjectPanel(panelId);
    if (alwaysRefreshPanels.has(panelId)) {
      await reloadPanel(panelId);
      return;
    }
    await ensurePanelLoaded(panelId);
  }

  function initMisionPanel() {
    const panel = document.getElementById("panel-mision");
    if (!panel || panel.dataset.riInit === "1") return;
    panel.dataset.riInit = "1";

    function openMisionEdit() {
      const editor = panel.querySelector("#mision-editor");
      const display = panel.querySelector("#mision-display");
      if (display) display.classList.add("hidden");
      if (editor) editor.classList.remove("hidden");
      const editButton = panel.querySelector("[data-js-edit-mision]");
      if (editButton) editButton.classList.add("hidden");
      const textarea = panel.querySelector("#mision-editor textarea");
      if (textarea) textarea.focus();
    }

    function closeMisionEdit() {
      const editor = panel.querySelector("#mision-editor");
      const display = panel.querySelector("#mision-display");
      if (editor) editor.classList.add("hidden");
      if (display) display.classList.remove("hidden");
      const editButton = panel.querySelector("[data-js-edit-mision]");
      if (editButton) editButton.classList.remove("hidden");
    }

    const editButton = panel.querySelector("[data-js-edit-mision]");
    if (editButton) {
      editButton.addEventListener("click", (e) => {
        e.preventDefault();
        setActiveProjectPanel("mision");
        openMisionEdit();
        const u = new URL(window.location.href);
        u.searchParams.set("edit", "mision");
        u.searchParams.set("section", "mision");
        window.history.replaceState({}, "", u.toString());
      });
    }

    const cancelLink = panel.querySelector("[data-js-cancel-mision]");
    if (cancelLink) {
      cancelLink.addEventListener("click", (e) => {
        e.preventDefault();
        closeMisionEdit();
        const u = new URL(window.location.href);
        u.searchParams.delete("edit");
        u.searchParams.set("section", "mision");
        window.history.replaceState({}, "", u.toString());
      });
    }

    const url = new URL(window.location.href);
    if (url.searchParams.get("edit") === "mision") {
      openMisionEdit();
    }

    const editorForm = panel.querySelector("#mision-editor form");
    if (editorForm && editorForm.dataset.riAjax !== "1") {
      editorForm.dataset.riAjax = "1";
      editorForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        e.stopPropagation();
        const submitter = (e.submitter instanceof HTMLButtonElement) ? e.submitter : editorForm.querySelector('button[type="submit"]');
        setButtonLoading(submitter, true, "Guardando…");
        try {
          const fd = new FormData(editorForm);
          const res = await fetch("detalle-proyecto.php", {
            method: "POST",
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
            body: fd,
          });
          const json = await res.json().catch(() => null);
          if (!res.ok || !json || json.ok !== true) {
            const msg = (json && json.error) ? json.error : "No se pudo guardar.";
            showInlineToast("Error", msg);
            return;
          }
          const u = new URL(window.location.href);
          u.searchParams.delete("edit");
          u.searchParams.set("section", "mision");
          window.history.replaceState({}, "", u.toString());
          showInlineToast("Guardado", (json && json.message) ? json.message : "Guardado correctamente.");
          await reloadPanel("mision");
          setActiveProjectPanel("mision", { updateUrl: false });
        } catch (err) {
          showInlineToast("Error", "No se pudo guardar.");
        } finally {
          setButtonLoading(submitter, false);
        }
      });
    }
  }

  function initVisionPanel() {
    const panel = document.getElementById("panel-vision");
    if (!panel || panel.dataset.riInit === "1") return;
    panel.dataset.riInit = "1";

    function openVisionEdit() {
      const editor = panel.querySelector("#vision-editor");
      const display = panel.querySelector("#vision-display");
      if (display) display.classList.add("hidden");
      if (editor) editor.classList.remove("hidden");
      const editButton = panel.querySelector("[data-js-edit-vision]");
      if (editButton) editButton.classList.add("hidden");
      const textarea = panel.querySelector("#vision-editor textarea");
      if (textarea) textarea.focus();
    }

    function closeVisionEdit() {
      const editor = panel.querySelector("#vision-editor");
      const display = panel.querySelector("#vision-display");
      if (editor) editor.classList.add("hidden");
      if (display) display.classList.remove("hidden");
      const editButton = panel.querySelector("[data-js-edit-vision]");
      if (editButton) editButton.classList.remove("hidden");
    }

    const editButton = panel.querySelector("[data-js-edit-vision]");
    if (editButton) {
      editButton.addEventListener("click", (e) => {
        e.preventDefault();
        setActiveProjectPanel("vision");
        openVisionEdit();
        const u = new URL(window.location.href);
        u.searchParams.set("edit", "vision");
        u.searchParams.set("section", "vision");
        window.history.replaceState({}, "", u.toString());
      });
    }

    const cancelLink = panel.querySelector("[data-js-cancel-vision]");
    if (cancelLink) {
      cancelLink.addEventListener("click", (e) => {
        e.preventDefault();
        closeVisionEdit();
        const u = new URL(window.location.href);
        u.searchParams.delete("edit");
        u.searchParams.set("section", "vision");
        window.history.replaceState({}, "", u.toString());
      });
    }

    const url = new URL(window.location.href);
    if (url.searchParams.get("edit") === "vision") {
      openVisionEdit();
    }

    const editorForm = panel.querySelector("#vision-editor form");
    if (editorForm && editorForm.dataset.riAjax !== "1") {
      editorForm.dataset.riAjax = "1";
      editorForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        e.stopPropagation();
        const submitter = (e.submitter instanceof HTMLButtonElement) ? e.submitter : editorForm.querySelector('button[type="submit"]');
        setButtonLoading(submitter, true, "Guardando…");
        try {
          const fd = new FormData(editorForm);
          const res = await fetch("detalle-proyecto.php", {
            method: "POST",
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
            body: fd,
          });
          const json = await res.json().catch(() => null);
          if (!res.ok || !json || json.ok !== true) {
            const msg = (json && json.error) ? json.error : "No se pudo guardar.";
            showInlineToast("Error", msg);
            return;
          }
          const u = new URL(window.location.href);
          u.searchParams.delete("edit");
          u.searchParams.set("section", "vision");
          window.history.replaceState({}, "", u.toString());
          showInlineToast("Guardado", (json && json.message) ? json.message : "Guardado correctamente.");
          await reloadPanel("vision");
          setActiveProjectPanel("vision", { updateUrl: false });
        } catch (err) {
          showInlineToast("Error", "No se pudo guardar.");
        } finally {
          setButtonLoading(submitter, false);
        }
      });
    }
  }

  function initValoresPanel() {
    const panel = document.getElementById("panel-valores");
    if (!panel || panel.dataset.riInit === "1") return;
    panel.dataset.riInit = "1";

    const editButton = panel.querySelector("[data-js-edit-valores]");
    if (editButton) {
      editButton.addEventListener("click", (e) => {
        e.preventDefault();
        setActiveProjectPanel("valores");
        openValoresEdit();
        const u = new URL(window.location.href);
        u.searchParams.set("edit", "valores");
        u.searchParams.set("section", "valores");
        window.history.replaceState({}, "", u.toString());
      });
    }

    const cancelLink = panel.querySelector("[data-js-cancel-valores]");
    if (cancelLink) {
      cancelLink.addEventListener("click", (e) => {
        e.preventDefault();
        closeValoresEdit();
        const u = new URL(window.location.href);
        u.searchParams.delete("edit");
        window.history.replaceState({}, "", u.toString());
      });
    }

    const addButton = panel.querySelector("#agregar-valor");
    const input = panel.querySelector("#nuevo-valor");
    const list = panel.querySelector("#valores-lista");

    function createItem(text) {
      const row = document.createElement("div");
      row.className = "flex items-center gap-3 rounded-xl border border-neutral-200 bg-white px-4 py-3";

      const inputValue = document.createElement("input");
      inputValue.type = "text";
      inputValue.name = "valores[]";
      inputValue.value = text;
      inputValue.placeholder = "Escribe un valor";
      inputValue.className = "flex-1 rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-800 outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15";

      const remove = document.createElement("button");
      remove.type = "button";
      remove.className = "quitar-valor inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-red-700";
      remove.textContent = "Eliminar";
      remove.addEventListener("click", () => row.remove());

      row.appendChild(inputValue);
      row.appendChild(remove);
      return row;
    }

    function addValue() {
      if (!input || !list) return;
      const text = (input.value || "").trim();
      if (text.length < 2) return;
      list.appendChild(createItem(text));
      input.value = "";
      input.focus();
    }

    if (addButton) addButton.addEventListener("click", addValue);
    if (input) {
      input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
          e.preventDefault();
          addValue();
        }
      });
    }
    if (list) {
      list.querySelectorAll(".quitar-valor").forEach((btn) => {
        btn.addEventListener("click", () => btn.closest("div")?.remove());
      });
    }

    const url = new URL(window.location.href);
    if (url.searchParams.get("edit") === "valores") {
      openValoresEdit();
    }

    const form = panel.querySelector("#valores-form");
    if (form && form.dataset.riAjax !== "1") {
      form.dataset.riAjax = "1";
      form.addEventListener("submit", async (e) => {
        e.preventDefault();
        e.stopPropagation();
        const submitter = (e.submitter instanceof HTMLButtonElement) ? e.submitter : form.querySelector('button[type="submit"]');
        setButtonLoading(submitter, true, "Guardando…");
        try {
          const fd = new FormData(form);
          const res = await fetch("detalle-proyecto.php", {
            method: "POST",
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
            body: fd,
          });
          const json = await res.json().catch(() => null);
          if (!res.ok || !json || json.ok !== true) {
            const msg = (json && json.error) ? json.error : "No se pudo guardar.";
            showInlineToast("Error", msg);
            return;
          }

          const u = new URL(window.location.href);
          u.searchParams.delete("edit");
          u.searchParams.set("section", "valores");
          window.history.replaceState({}, "", u.toString());

          showInlineToast("Guardado", (json && json.message) ? json.message : "Guardado correctamente.");
          await reloadPanel("valores");
          setActiveProjectPanel("valores", { updateUrl: false });
        } catch (err) {
          showInlineToast("Error", "No se pudo guardar.");
        } finally {
          setButtonLoading(submitter, false);
        }
      });
    }
  }

  function initObjetivosPanel() {
    const panel = document.getElementById("panel-objetivos");
    if (!panel || panel.dataset.riInit === "1") return;
    panel.dataset.riInit = "1";

    panel.querySelectorAll("[data-js-edit-oe]").forEach((el) => {
      el.addEventListener("click", (e) => {
        e.preventDefault();
        const token = el.getAttribute("data-js-edit-oe");
        if (!token) return;
        setActiveProjectPanel("objetivos");
        openObjetivoEstrategicoEdit(token);
        const u = new URL(window.location.href);
        u.searchParams.set("section", "objetivos");
        u.searchParams.set("oe_edit", token);
        u.searchParams.delete("oesp_edit");
        window.history.replaceState({}, "", u.toString());
      });
    });
    panel.querySelectorAll("[data-js-cancel-oe]").forEach((el) => {
      el.addEventListener("click", (e) => {
        e.preventDefault();
        const token = el.getAttribute("data-js-cancel-oe");
        if (!token) return;
        closeObjetivoEstrategicoEdit(token);
        const u = new URL(window.location.href);
        u.searchParams.delete("oe_edit");
        window.history.replaceState({}, "", u.toString());
      });
    });
    panel.querySelectorAll("[data-js-edit-oesp]").forEach((el) => {
      el.addEventListener("click", (e) => {
        e.preventDefault();
        const token = el.getAttribute("data-js-edit-oesp");
        if (!token) return;
        setActiveProjectPanel("objetivos");
        openObjetivoEspecificoEdit(token);
        const u = new URL(window.location.href);
        u.searchParams.set("section", "objetivos");
        u.searchParams.set("oesp_edit", token);
        u.searchParams.delete("oe_edit");
        window.history.replaceState({}, "", u.toString());
      });
    });
    panel.querySelectorAll("[data-js-cancel-oesp]").forEach((el) => {
      el.addEventListener("click", (e) => {
        e.preventDefault();
        const token = el.getAttribute("data-js-cancel-oesp");
        if (!token) return;
        closeObjetivoEspecificoEdit(token);
        const u = new URL(window.location.href);
        u.searchParams.delete("oesp_edit");
        window.history.replaceState({}, "", u.toString());
      });
    });

    const url = new URL(window.location.href);
    const oeEditParam = url.searchParams.get("oe_edit");
    const oespEditParam = url.searchParams.get("oesp_edit");
    if (oeEditParam) {
      openObjetivoEstrategicoEdit(oeEditParam);
    }
    if (oespEditParam) {
      openObjetivoEspecificoEdit(oespEditParam);
    }

    const batchForm = panel.querySelector("#objetivos-batch-form");
    const batchPayload = panel.querySelector("#objetivos-batch-payload");
    const oeInput = panel.querySelector("#oe-batch-input");
    const oeAdd = panel.querySelector("#oe-batch-add");
    const oeList = panel.querySelector("#oe-batch-list");
    const oespRows = panel.querySelector("#oesp-batch-rows");
    const oespAddRow = panel.querySelector("#oesp-batch-add-row");
    const batchClear = panel.querySelector("#objetivos-batch-clear");

    if (batchForm && batchPayload && oeList && oespRows) {
      const newStrategic = [];

      function makeId() {
        return (Math.random().toString(16).slice(2) + Date.now().toString(16)).slice(0, 24);
      }

      function truncateText(value, max) {
        const s = String(value || "").trim();
        if (s.length <= max) return s;
        return s.slice(0, max - 1) + "…";
      }

      function getOespRowEls() {
        return Array.from(oespRows.querySelectorAll("[data-oesp-row]"));
      }

      function bindOespRow(row) {
        const remove = row.querySelector("[data-oesp-remove]");
        if (remove && remove.dataset.riBound !== "1") {
          remove.dataset.riBound = "1";
          remove.addEventListener("click", () => {
            const rows = getOespRowEls();
            if (rows.length <= 1) {
              const sel = row.querySelector("[data-oesp-oe]");
              const ta = row.querySelector("[data-oesp-lines]");
              if (sel) sel.value = "";
              if (ta) ta.value = "";
              return;
            }
            row.remove();
          });
        }
      }

      function addOespRow() {
        const rows = getOespRowEls();
        const base = rows[0];
        if (!base) return;
        const clone = base.cloneNode(true);
        const sel = clone.querySelector("[data-oesp-oe]");
        const ta = clone.querySelector("[data-oesp-lines]");
        const remove = clone.querySelector("[data-oesp-remove]");
        if (sel) sel.value = "";
        if (ta) ta.value = "";
        if (remove) remove.dataset.riBound = "0";
        bindOespRow(clone);
        oespRows.appendChild(clone);
        syncTmpOptions();
        if (sel) sel.focus();
      }

      function syncTmpOptions() {
        const rows = getOespRowEls();
        for (const row of rows) {
          const sel = row.querySelector("[data-oesp-oe]");
          if (!(sel instanceof HTMLSelectElement)) continue;
          const selected = String(sel.value || "");
          const existing = sel.querySelector('optgroup[data-tmp="1"]');
          if (existing) existing.remove();
          if (newStrategic.length === 0) continue;

          const og = document.createElement("optgroup");
          og.label = "Nuevos (pendientes)";
          og.setAttribute("data-tmp", "1");
          for (const it of newStrategic) {
            const opt = document.createElement("option");
            opt.value = `tmp:${it.id}`;
            opt.textContent = `⏳ ${truncateText(it.descripcion, 60) || "Objetivo estratégico"}`;
            og.appendChild(opt);
          }
          sel.appendChild(og);

          if (selected.startsWith("tmp:")) {
            const found = newStrategic.some((x) => `tmp:${x.id}` === selected);
            if (!found) sel.value = "";
          }
        }
      }

      function renderNewStrategic() {
        oeList.innerHTML = "";
        newStrategic.forEach((item) => {
          const card = document.createElement("div");
          card.className = "rounded-xl border border-neutral-200 bg-white p-4";

          const header = document.createElement("div");
          header.className = "flex items-start justify-between gap-3";

          const title = document.createElement("div");
          title.className = "text-xs font-semibold text-neutral-600";
          title.textContent = "Objetivo estratégico (pendiente)";

          const remove = document.createElement("button");
          remove.type = "button";
          remove.className = "inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700";
          remove.textContent = "Eliminar";
          remove.addEventListener("click", () => {
            const idx = newStrategic.findIndex((x) => x.id === item.id);
            if (idx >= 0) newStrategic.splice(idx, 1);
            renderAll();
          });

          header.appendChild(title);
          header.appendChild(remove);

          const textarea = document.createElement("textarea");
          textarea.rows = 3;
          textarea.className = "mt-3 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none resize-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15";
          textarea.value = String(item.descripcion || "");
          textarea.addEventListener("input", () => {
            item.descripcion = String(textarea.value || "");
          });

          const specWrap = document.createElement("div");
          specWrap.className = "mt-4 rounded-xl border border-neutral-200 bg-neutral-50 p-3";

          const specTitle = document.createElement("div");
          specTitle.className = "text-xs font-semibold text-neutral-700";
          specTitle.textContent = "Objetivos específicos (pendientes)";

          const specForm = document.createElement("div");
          specForm.className = "mt-2 flex flex-col gap-2 sm:flex-row";

          const specInput = document.createElement("input");
          specInput.type = "text";
          specInput.className = "h-10 flex-1 rounded-xl border border-neutral-300 bg-white px-4 text-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15";
          specInput.placeholder = "Escribe un objetivo específico...";

          const specAdd = document.createElement("button");
          specAdd.type = "button";
          specAdd.className = "h-10 rounded-xl bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700";
          specAdd.textContent = "+ Añadir";
          specAdd.addEventListener("click", () => {
            const txt = String(specInput.value || "").trim();
            if (txt.length < 5) {
              showInlineToast("Error", "El objetivo específico debe tener al menos 5 caracteres.");
              return;
            }
            item.especificos.push(txt);
            specInput.value = "";
            specInput.focus();
            renderAll();
          });

          specInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
              e.preventDefault();
              specAdd.click();
            }
          });

          specForm.appendChild(specInput);
          specForm.appendChild(specAdd);

          const specList = document.createElement("div");
          specList.className = "mt-3 space-y-2";
          if (item.especificos.length === 0) {
            const empty = document.createElement("div");
            empty.className = "text-sm text-neutral-600";
            empty.textContent = "Aún no hay objetivos específicos en la lista.";
            specList.appendChild(empty);
          } else {
            item.especificos.forEach((sp, i) => {
              const row = document.createElement("div");
              row.className = "flex items-center gap-3 rounded-xl border border-neutral-200 bg-white px-3 py-2";

              const label = document.createElement("div");
              label.className = "flex-1 text-sm text-neutral-800";
              label.textContent = String(sp || "");

              const del = document.createElement("button");
              del.type = "button";
              del.className = "inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700";
              del.textContent = "Eliminar";
              del.addEventListener("click", () => {
                item.especificos.splice(i, 1);
                renderAll();
              });

              row.appendChild(label);
              row.appendChild(del);
              specList.appendChild(row);
            });
          }

          specWrap.appendChild(specTitle);
          specWrap.appendChild(specForm);
          specWrap.appendChild(specList);

          card.appendChild(header);
          card.appendChild(textarea);
          card.appendChild(specWrap);
          oeList.appendChild(card);
        });
        syncTmpOptions();
      }

      function renderAll() {
        renderNewStrategic();
      }

      function clearAll() {
        newStrategic.splice(0, newStrategic.length);
        if (oeInput) oeInput.value = "";
        batchPayload.value = "";
        const rows = getOespRowEls();
        rows.slice(1).forEach((r) => r.remove());
        const first = rows[0];
        if (first) {
          const sel = first.querySelector("[data-oesp-oe]");
          const ta = first.querySelector("[data-oesp-lines]");
          if (sel) sel.value = "";
          if (ta) ta.value = "";
          bindOespRow(first);
        }
        syncTmpOptions();
        renderAll();
      }

      if (oeAdd && oeInput) {
        oeAdd.addEventListener("click", () => {
          const txt = String(oeInput.value || "").trim();
          if (txt.length < 5) {
            showInlineToast("Error", "El objetivo estratégico debe tener al menos 5 caracteres.");
            return;
          }
          newStrategic.push({ id: makeId(), descripcion: txt, especificos: [] });
          oeInput.value = "";
          oeInput.focus();
          renderAll();
        });
      }

      if (oeInput) {
        oeInput.addEventListener("keydown", (e) => {
          if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            if (oeAdd) oeAdd.click();
          }
        });
      }

      getOespRowEls().forEach((r) => bindOespRow(r));
      syncTmpOptions();
      if (oespAddRow) {
        oespAddRow.addEventListener("click", addOespRow);
      }

      if (batchClear) {
        batchClear.addEventListener("click", clearAll);
      }

      batchForm.addEventListener("submit", (e) => {
        const estrategicos = newStrategic
          .map((it) => ({
            tmp_id: String(it.id || ""),
            descripcion: String(it.descripcion || "").trim(),
            especificos: Array.isArray(it.especificos) ? it.especificos.map((s) => String(s || "").trim()).filter((s) => s) : [],
          }))
          .filter((it) => it.descripcion);

        const especificos = [];
        const rows = getOespRowEls();
        for (const row of rows) {
          const sel = row.querySelector("[data-oesp-oe]");
          const ta = row.querySelector("[data-oesp-lines]");
          const oe = String(sel instanceof HTMLSelectElement ? (sel.value || "") : "").trim();
          const raw = String(ta instanceof HTMLTextAreaElement ? (ta.value || "") : "");
          const lines = raw.split(/\r?\n/).map((v) => String(v || "").trim()).filter((v) => v !== "");
          if (oe === "" && lines.length === 0) {
            continue;
          }
          if (oe === "") {
            e.preventDefault();
            showInlineToast("Error", "Selecciona un objetivo estratégico en todas las filas con texto.");
            return;
          }
          for (const line of lines) {
            if (line.length < 5) {
              e.preventDefault();
              showInlineToast("Error", "Cada objetivo específico debe tener al menos 5 caracteres.");
              return;
            }
            if (oe.startsWith("tmp:")) {
              especificos.push({ oe_tmp: oe.slice(4), descripcion: line });
            } else {
              especificos.push({ oe, descripcion: line });
            }
          }
        }

        if (estrategicos.length === 0 && especificos.length === 0) {
          e.preventDefault();
          showInlineToast("Error", "Agrega al menos un objetivo antes de guardar.");
          return;
        }

        const badOE = estrategicos.find((x) => x.descripcion.length < 5);
        if (badOE) {
          e.preventDefault();
          showInlineToast("Error", "Cada objetivo estratégico debe tener al menos 5 caracteres.");
          return;
        }

        const badOESP =
          estrategicos.some((x) => x.especificos.some((s) => String(s || "").trim().length < 5)) ||
          especificos.some((x) => String(x.descripcion || "").trim().length < 5);
        if (badOESP) {
          e.preventDefault();
          showInlineToast("Error", "Cada objetivo específico debe tener al menos 5 caracteres.");
          return;
        }

        batchPayload.value = JSON.stringify({ estrategicos, especificos });
      });

      renderAll();
    }

    panel.querySelectorAll("form").forEach((form) => {
      if (!(form instanceof HTMLFormElement)) return;
      if (form.dataset.riAjax === "1") return;
      const actionEl = form.querySelector('input[name="action"]');
      const action = actionEl ? String(actionEl.value || "") : "";
      if (!["update_obj_est", "delete_obj_est", "update_obj_esp", "delete_obj_esp", "save_objetivos_batch"].includes(action)) {
        return;
      }
      form.dataset.riAjax = "1";
      form.addEventListener("submit", async (e) => {
        e.preventDefault();
        e.stopPropagation();

        const confirmMsg = String(form.getAttribute("data-confirm") || "").trim();
        if (confirmMsg) {
          const ok = await openConfirmModal({
            title: "Confirmar eliminación",
            message: confirmMsg,
            confirmText: "Eliminar",
            cancelText: "Cancelar",
          });
          if (!ok) {
            return;
          }
        }

        const submitter = (e.submitter instanceof HTMLButtonElement) ? e.submitter : form.querySelector('button[type="submit"]');
        const loadingText = action.startsWith("delete_") ? "Eliminando…" : "Guardando…";
        setButtonLoading(submitter, true, loadingText);
        try {
          const fd = new FormData(form);
          const res = await fetch("detalle-proyecto.php", {
            method: "POST",
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
            body: fd,
          });
          const json = await res.json().catch(() => null);
          if (!res.ok || !json || json.ok !== true) {
            const msg = (json && json.error) ? json.error : "No se pudo guardar.";
            showInlineToast("Error", msg);
            return;
          }

          const u = new URL(window.location.href);
          u.searchParams.delete("oe_edit");
          u.searchParams.delete("oesp_edit");
          u.searchParams.set("section", "objetivos");
          window.history.replaceState({}, "", u.toString());

          showInlineToast("Guardado", (json && json.message) ? json.message : "Guardado correctamente.");
          await reloadPanel("objetivos");
          setActiveProjectPanel("objetivos", { updateUrl: false });
        } catch (err) {
          showInlineToast("Error", "No se pudo guardar.");
        } finally {
          setButtonLoading(submitter, false);
        }
      });
    });
  }

  function initCadenaPanel() {
    const panel = document.getElementById("panel-cadena");
    if (!panel || panel.dataset.riInit === "1") return;
    panel.dataset.riInit = "1";

    const cviForm = panel.querySelector("#cvi-form");
    const cviSumEl = panel.querySelector("#cvi-sum");
    const cviValidEl = panel.querySelector("#cvi-valid");
    const cviResultEl = panel.querySelector("#cvi-result");
    const cviResultSubEl = panel.querySelector("#cvi-result-sub");
    const cviSaveButton = panel.querySelector("#cvi-save");
    const fodaSaveButton = panel.querySelector("#foda-save");
    const fodaFortBody = panel.querySelector("#foda-fortalezas-body");
    const fodaDebBody = panel.querySelector("#foda-debilidades-body");
    const fodaAddFort = panel.querySelector("#foda-add-fortaleza");
    const fodaAddDeb = panel.querySelector("#foda-add-debilidad");
    const cviToast = panel.querySelector("#cvi-toast");
    const cviToastCard = panel.querySelector("#cvi-toast-card");
    const cviToastTitle = panel.querySelector("#cvi-toast-title");
    const cviToastMsg = panel.querySelector("#cvi-toast-msg");
    const cviToastClose = panel.querySelector("#cvi-toast-close");
    const cviRows = Array.from(panel.querySelectorAll("[data-cvi-row]"));

    let cviToastTimer = null;
    let cviValidationActive = false;
    let cviDirty = false;
    let cviSaving = false;
    const cviAnswers = {};

    function cviCloseToast() {
      if (!cviToast) return;
      cviToast.classList.add("hidden");
      if (cviToastTimer) {
        clearTimeout(cviToastTimer);
        cviToastTimer = null;
      }
    }

    function cviShowToast(type, title, message) {
      if (!cviToast || !cviToastCard || !cviToastTitle || !cviToastMsg) return;
      cviToastTitle.textContent = title || "";
      cviToastMsg.textContent = message || "";
      cviToastCard.className =
        type === "success"
          ? "pointer-events-auto rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-lg"
          : "pointer-events-auto rounded-2xl border border-red-200 bg-red-50 p-4 shadow-lg";
      cviToast.classList.remove("hidden");
      if (cviToastTimer) clearTimeout(cviToastTimer);
      cviToastTimer = setTimeout(() => cviCloseToast(), 3500);
    }

    if (cviToastClose) {
      cviToastClose.addEventListener("click", () => cviCloseToast());
    }

    function cviUpdateRowStyles(row) {
      const cells = Array.from(row.querySelectorAll(".cvi-cell"));
      for (const cell of cells) {
        const input = cell.querySelector("input[type='radio']");
        const label = cell.querySelector(".cvi-cell-label");
        const checked = input && input.checked;
        cell.className = checked
          ? "cvi-cell flex h-12 w-full cursor-pointer items-center justify-center select-none bg-brand-50"
          : "cvi-cell flex h-12 w-full cursor-pointer items-center justify-center select-none hover:bg-neutral-50";
        if (label) {
          label.className = checked
            ? "cvi-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-brand-600 bg-brand-600 px-3 text-sm font-semibold text-white shadow-sm transition"
            : "cvi-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-neutral-300 bg-white px-3 text-sm font-semibold text-neutral-700 transition hover:border-brand-300";
        }
      }
    }

    function cviApplyCalc(calc) {
      const sum = Number(calc && calc.sum !== undefined ? calc.sum : 0);
      const valid = Number(calc && calc.valid !== undefined ? calc.valid : 0);
      const count = Number(calc && calc.count !== undefined ? calc.count : cviRows.length);
      const missing = Number(calc && calc.missing !== undefined ? calc.missing : Math.max(0, count - valid));
      const potential = calc ? calc.potential : null;

      if (cviSumEl) cviSumEl.textContent = String(sum);
      if (cviValidEl) cviValidEl.textContent = `${valid}/${count}`;

      if (missing > 0 || potential === null || potential === undefined) {
        if (cviResultEl) {
          cviResultEl.textContent = "Incompleto";
          cviResultEl.className = "mt-1 text-2xl font-semibold text-amber-700";
        }
        if (cviResultSubEl) {
          cviResultSubEl.textContent = cviValidationActive
            ? `Faltan ${missing} respuesta${missing === 1 ? "" : "s"} para guardar la evaluación.`
            : "Completa todas las preguntas para calcular el potencial de mejora.";
        }
        return;
      }

      const p = Number(potential);
      if (Number.isNaN(p)) {
        if (cviResultEl) {
          cviResultEl.textContent = "Incompleto";
          cviResultEl.className = "mt-1 text-2xl font-semibold text-amber-700";
        }
        if (cviResultSubEl) cviResultSubEl.textContent = "No se pudo calcular el potencial de mejora porque la evaluación está incompleta.";
        return;
      }

      if (cviResultEl) {
        cviResultEl.textContent = p.toFixed(2);
        cviResultEl.className = "mt-1 text-2xl font-semibold text-brand-900";
      }
      if (cviResultSubEl) cviResultSubEl.textContent = `${Math.round(p * 100)}%`;
    }

    function cviRecalculateFromDom() {
      let sum = 0;
      let valid = 0;
      let hasInvalid = false;

      for (const row of cviRows) {
        const checked = row.querySelectorAll("input[type='radio']:checked");
        const ref = row.querySelector("[data-cvi-ref]");
        cviUpdateRowStyles(row);

        if (checked.length === 1) {
          valid += 1;
          sum += Number(checked[0].value || 0);
          if (ref) ref.classList.add("hidden");
          row.className = "cvi-row";
          cviAnswers[Number(row.dataset.cviRow || 0)] = Number(checked[0].value || 0);
        } else {
          hasInvalid = true;
          if (cviValidationActive && ref) ref.classList.remove("hidden");
          if (ref && !cviValidationActive) ref.classList.add("hidden");
          row.className = cviValidationActive ? "cvi-row bg-red-50/40" : "cvi-row";
          delete cviAnswers[Number(row.dataset.cviRow || 0)];
        }
      }

      cviApplyCalc({
        sum,
        valid,
        count: cviRows.length,
        missing: hasInvalid ? Math.max(0, cviRows.length - valid) : 0,
        potential: hasInvalid ? null : (1 - (sum / 100)),
      });
    }

    async function cviSaveAll() {
      if (cviSaving) return;
      cviValidationActive = true;
      cviRecalculateFromDom();

      if (Object.keys(cviAnswers).length !== cviRows.length) {
        cviShowToast("error", "Faltan respuestas", "Completa todas las preguntas antes de guardar.");
        return;
      }

      cviSaving = true;
      if (cviSaveButton) {
        cviSaveButton.disabled = true;
        cviSaveButton.className = "inline-flex items-center justify-center rounded-xl bg-brand-600/60 px-4 py-2 text-sm font-semibold text-white shadow-sm";
        cviSaveButton.textContent = "Guardando…";
      }

      try {
        const formData = new FormData();
        formData.set("action", "save_cadena_valor_batch");
        formData.set("t", projectToken || "");
        formData.set("answers", JSON.stringify(cviAnswers));

        const res = await fetch("detalle-proyecto.php", {
          method: "POST",
          body: formData,
          headers: { Accept: "application/json" },
        });

        const json = await res.json();
        if (!json || typeof json !== "object" || !json.ok) {
          cviShowToast("error", "No se pudo guardar", String((json && json.error) || "Error al guardar la evaluación."));
          return;
        }

        if (json.calc) {
          cviApplyCalc(json.calc);
        }
        cviDirty = false;
        cviShowToast("success", "Guardado", "Evaluación guardada correctamente.");
      } catch (e) {
        cviShowToast("error", "No se pudo guardar", "Error al guardar la evaluación.");
      } finally {
        cviSaving = false;
        if (cviSaveButton) {
          cviSaveButton.disabled = false;
          cviSaveButton.className = "inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25";
          cviSaveButton.textContent = "Guardar Evaluación";
        }
      }
    }

    function renumberFodaRows(tbody) {
      const rows = Array.from(tbody.querySelectorAll("tr"));
      rows.forEach((row, idx) => {
        const n = row.querySelector("td");
        if (n) n.textContent = String(idx + 1);
      });
    }

    function ensureAtLeastOneFodaRow(tbody, kind) {
      const rows = Array.from(tbody.querySelectorAll("tr"));
      if (rows.length > 0) {
        renumberFodaRows(tbody);
        return;
      }
      tbody.appendChild(createFodaRow(kind));
      renumberFodaRows(tbody);
    }

    function attachFodaRemoveHandlers(tbody, kind) {
      if (!tbody || tbody.dataset.fodaBound === "1") return;
      tbody.dataset.fodaBound = "1";
      tbody.addEventListener("click", (e) => {
        const btn = e.target && e.target.closest ? e.target.closest(".foda-remove") : null;
        if (!btn) return;
        const row = btn.closest("tr");
        const rows = Array.from(tbody.querySelectorAll("tr"));
        if (rows.length <= 1) {
          const input = row ? row.querySelector("input") : null;
          if (input) input.value = "";
          ensureAtLeastOneFodaRow(tbody, kind);
          return;
        }
        if (row) row.remove();
        ensureAtLeastOneFodaRow(tbody, kind);
      });
    }

    function createFodaRow(kind) {
      const tr = document.createElement("tr");
      tr.setAttribute("data-foda-row", kind);

      const tdN = document.createElement("td");
      tdN.className = "px-4 py-3 text-center text-xs font-semibold text-neutral-600";
      tdN.textContent = "—";

      const tdInput = document.createElement("td");
      tdInput.className = "px-4 py-2";
      const input = document.createElement("input");
      input.type = "text";
      input.className = "foda-input h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200";
      tdInput.appendChild(input);

      const tdAction = document.createElement("td");
      tdAction.className = "px-4 py-2 text-right";
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "foda-remove inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50";
      btn.textContent = "Quitar";
      tdAction.appendChild(btn);

      tr.appendChild(tdN);
      tr.appendChild(tdInput);
      tr.appendChild(tdAction);
      return tr;
    }

    if (fodaFortBody) attachFodaRemoveHandlers(fodaFortBody, "fortaleza");
    if (fodaDebBody) attachFodaRemoveHandlers(fodaDebBody, "debilidad");
    if (fodaFortBody) ensureAtLeastOneFodaRow(fodaFortBody, "fortaleza");
    if (fodaDebBody) ensureAtLeastOneFodaRow(fodaDebBody, "debilidad");

    if (fodaAddFort && fodaFortBody) {
      fodaAddFort.addEventListener("click", () => {
        const tr = createFodaRow("fortaleza");
        fodaFortBody.appendChild(tr);
        ensureAtLeastOneFodaRow(fodaFortBody, "fortaleza");
        tr.querySelector("input")?.focus();
      });
    }

    if (fodaAddDeb && fodaDebBody) {
      fodaAddDeb.addEventListener("click", () => {
        const tr = createFodaRow("debilidad");
        fodaDebBody.appendChild(tr);
        ensureAtLeastOneFodaRow(fodaDebBody, "debilidad");
        tr.querySelector("input")?.focus();
      });
    }

    function collectFodaValues(tbody) {
      const rows = Array.from(tbody.querySelectorAll("tr"));
      const out = [];
      for (const row of rows) {
        const value = (row.querySelector("input")?.value || "").trim();
        if (value) out.push(value);
      }
      return out;
    }

    let fodaSaving = false;
    if (fodaSaveButton) {
      fodaSaveButton.addEventListener("click", async () => {
        if (fodaSaving) return;
        fodaSaving = true;
        const prevLabel = fodaSaveButton.textContent;
        fodaSaveButton.disabled = true;
        fodaSaveButton.textContent = "Guardando…";
        try {
          const fortalezas = fodaFortBody ? collectFodaValues(fodaFortBody) : [];
          const debilidades = fodaDebBody ? collectFodaValues(fodaDebBody) : [];
          const payload = JSON.stringify({ fortalezas, debilidades });

          const fd = new FormData();
          fd.set("action", "save_foda_cadena");
          fd.set("t", projectToken || "");
          fd.set("payload", payload);

          const res = await fetch("detalle-proyecto.php", {
            method: "POST",
            headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
            body: fd,
          });
          const data = await res.json().catch(() => null);
          if (!data || data.ok !== true) {
            cviShowToast("error", "Error", (data && data.error) ? String(data.error) : "No se pudo guardar el FODA.");
            return;
          }
          cviShowToast("success", "Guardado", "FODA guardado correctamente.");
        } catch (e) {
          cviShowToast("error", "Error", "No se pudo guardar el FODA.");
        } finally {
          fodaSaveButton.disabled = false;
          fodaSaveButton.textContent = prevLabel || "Guardar FODA";
          fodaSaving = false;
        }
      });
    }

    if (cviForm) {
      cviForm.addEventListener("change", (e) => {
        const target = e.target;
        if (!(target instanceof HTMLInputElement)) return;
        if (target.type !== "radio") return;

        const row = target.closest("[data-cvi-row]");
        if (!row) return;

        cviDirty = true;
        cviRecalculateFromDom();
        if (!cviValidationActive) {
          const ref = row.querySelector("[data-cvi-ref]");
          if (ref) ref.classList.add("hidden");
          row.className = "cvi-row";
        }
      });

      cviRecalculateFromDom();
    }

    if (cviSaveButton) {
      cviSaveButton.addEventListener("click", () => {
        cviSaveAll();
      });
    }
  }

  async function loadChartJs() {
    if (window.Chart) return true;
    if (window.__riChartJsPromise) return window.__riChartJsPromise;
    window.__riChartJsPromise = new Promise((resolve) => {
      const s = document.createElement("script");
      s.src = "https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js";
      s.async = true;
      s.onload = () => resolve(true);
      s.onerror = () => resolve(false);
      document.head.appendChild(s);
    });
    return window.__riChartJsPromise;
  }

  function initBcgPanel() {
    const panel = document.getElementById("panel-bgg");
    if (!panel || panel.dataset.riInit === "1") return;
    panel.dataset.riInit = "1";

    const loading = panel.querySelector("#bcg-loading");
    const errorEl = panel.querySelector("#bcg-error");
    const app = panel.querySelector("#bcg-app");
    const recalcBtn = panel.querySelector("#bcg-recalc-btn");
    const toastEl = panel.querySelector("#bcg-toast");
    const toastTitle = panel.querySelector("#bcg-toast-title");
    const toastMsg = panel.querySelector("#bcg-toast-msg");
    const toastClose = panel.querySelector("#bcg-toast-close");

    const newProductName = panel.querySelector("#bcg-new-product-name");
    const newProductSales = panel.querySelector("#bcg-new-product-sales");
    const addProductBtn = panel.querySelector("#bcg-add-product");
    const productsBody = panel.querySelector("#bcg-products-body");
    const productsSaveAll = panel.querySelector("#bcg-products-save");

    const marketProductSel = panel.querySelector("#bcg-market-product");
    const marketYear = panel.querySelector("#bcg-market-year");
    const marketDemand = panel.querySelector("#bcg-market-demand");
    const marketSave = panel.querySelector("#bcg-market-save");
    const marketSaveAll = panel.querySelector("#bcg-market-save-all");
    const marketHeadRow = panel.querySelector("#bcg-market-head-row");
    const marketMatrixBody = panel.querySelector("#bcg-market-matrix-body");

    const sectorSaveAll = panel.querySelector("#bcg-sector-save-all");
    const sectorHeadRow = panel.querySelector("#bcg-sector-head-row");
    const sectorMatrixBody = panel.querySelector("#bcg-sector-matrix-body");

    const competitorsGrid = panel.querySelector("#bcg-competitors-grid");
    const competitorsSaveAll = panel.querySelector("#bcg-competitors-save-all");
    const completenessStatusEl = panel.querySelector("#bcg-completeness-status");
    const completenessMsgEl = panel.querySelector("#bcg-completeness-msg");

    const bcgFodaSave = panel.querySelector("#bcg-foda-save");
    const bcgFodaAddFort = panel.querySelector("#bcg-foda-add-fortaleza");
    const bcgFodaAddDeb = panel.querySelector("#bcg-foda-add-debilidad");
    const bcgFodaFortBody = panel.querySelector("#bcg-foda-fortalezas-body");
    const bcgFodaDebBody = panel.querySelector("#bcg-foda-debilidades-body");

    const totalVentasEl = panel.querySelector("#bcg-total-ventas");
    const fechaCalculoEl = panel.querySelector("#bcg-fecha-calculo");
    const totalVentasInlineEl = panel.querySelector("#bcg-total-ventas-inline");
    const summaryHeadRow = panel.querySelector("#bcg-summary-head-row");
    const summaryBody = panel.querySelector("#bcg-summary-body");

    const chartCanvas = panel.querySelector("#bcg-chart");
    let chart = null;

    const legendProductsBody = panel.querySelector("#bcg-legend-products");

    function quadrantFrom(prm, tcm) {
      const prmN = Number(prm);
      const tcmN = Number(tcm);
      const prmHigh = Number.isFinite(prmN) && prmN >= 1;
      const tcmHigh = Number.isFinite(tcmN) && tcmN > 0.1;
      if (prmHigh && tcmHigh) return "Estrella";
      if (!prmHigh && tcmHigh) return "Interrogante";
      if (prmHigh && !tcmHigh) return "Vaca";
      return "Perro";
    }

    function renderLegendProducts(payload) {
      if (!legendProductsBody) return;
      legendProductsBody.innerHTML = "";
      const products = payload && Array.isArray(payload.products) ? payload.products : [];
      if (!products.length) return;

      const sorted = [...products].sort((a, b) => Number(b.porcentaje_ventas ?? 0) - Number(a.porcentaje_ventas ?? 0));
      for (const p of sorted) {
        const prm = Number(p.prm ?? 0);
        const tcm = Number(p.tcm ?? 0);
        const salesPct = Number(p.porcentaje_ventas_pct ?? (Number(p.porcentaje_ventas ?? 0) * 100));
        const name = String(p.nombre || "—");
        const q = quadrantFrom(prm, tcm);

        const tr = document.createElement("tr");
        const tdName = document.createElement("td");
        tdName.className = "px-3 py-2 font-semibold text-neutral-900";
        tdName.textContent = name;

        const tdQ = document.createElement("td");
        tdQ.className = "px-3 py-2 text-neutral-800";
        tdQ.textContent = q;

        const tdPrm = document.createElement("td");
        tdPrm.className = "px-3 py-2 text-right text-neutral-700";
        tdPrm.textContent = Number.isFinite(prm) ? prm.toFixed(2) : "—";

        const tdTcm = document.createElement("td");
        tdTcm.className = "px-3 py-2 text-right text-neutral-700";
        tdTcm.textContent = Number.isFinite(tcm) ? `${(tcm * 100).toFixed(2)}%` : "—";

        const tdSales = document.createElement("td");
        tdSales.className = "px-3 py-2 text-right text-neutral-700";
        tdSales.textContent = Number.isFinite(salesPct) ? `${salesPct.toFixed(2)}%` : "—";

        tr.appendChild(tdName);
        tr.appendChild(tdQ);
        tr.appendChild(tdPrm);
        tr.appendChild(tdTcm);
        tr.appendChild(tdSales);
        legendProductsBody.appendChild(tr);
      }
    }

    let toastTimer = null;
    function showToast(title, msg) {
      if (!toastEl || !toastTitle || !toastMsg) return;
      if (toastTimer) clearTimeout(toastTimer);
      toastTitle.textContent = String(title || "");
      toastMsg.textContent = String(msg || "");
      toastEl.classList.remove("hidden");
      toastTimer = setTimeout(() => toastEl.classList.add("hidden"), 2200);
    }
    if (toastClose) toastClose.addEventListener("click", () => toastEl && toastEl.classList.add("hidden"));

    function setSaving(btn, saving) {
      if (!btn) return;
      const spinner = btn.querySelector("[data-bcg-spinner]");
      if (spinner) spinner.classList.toggle("hidden", !saving);
      if (saving) {
        btn.dataset.prevDisabled = btn.disabled ? "1" : "0";
        btn.disabled = true;
        return;
      }
      const prev = btn.dataset.prevDisabled;
      delete btn.dataset.prevDisabled;
      btn.disabled = prev === "1";
    }

    function showError(msg) {
      if (errorEl) {
        errorEl.textContent = String(msg || "Error");
        errorEl.classList.remove("hidden");
      }
    }

    function clearError() {
      if (errorEl) errorEl.classList.add("hidden");
    }

    function setLoading(on) {
      if (loading) loading.classList.toggle("hidden", !on);
      if (app) app.classList.toggle("hidden", on);
    }

    const debounceTimers = new Map();
    function debounce(key, fn, waitMs = 450) {
      if (debounceTimers.has(key)) {
        clearTimeout(debounceTimers.get(key));
      }
      const t = setTimeout(() => {
        debounceTimers.delete(key);
        fn();
      }, waitMs);
      debounceTimers.set(key, t);
    }

    function formatMoney(v) {
      const n = Number(v);
      if (!Number.isFinite(n)) return "0";
      return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatPct(v) {
      const n = Number(v);
      if (!Number.isFinite(n)) return "0%";
      return `${n.toFixed(2)}%`;
    }

    function formatPlainNumber(v, decimals = 2) {
      const n = Number(v);
      if (!Number.isFinite(n)) return "";
      const fixed = n.toFixed(decimals);
      return fixed.replace(/\.0+$/, "").replace(/(\.\d*?)0+$/, "$1").replace(/\.$/, "");
    }

    function buildBadge(classification) {
      const c = String(classification || "");
      const map = {
        ESTRELLA: "bg-emerald-50 text-emerald-800 border-emerald-200",
        VACA: "bg-blue-50 text-blue-800 border-blue-200",
        INTERROGANTE: "bg-amber-50 text-amber-800 border-amber-200",
        PERRO: "bg-red-50 text-red-800 border-red-200",
      };
      const cls = map[c] || map.PERRO;
      const span = document.createElement("span");
      span.className = `inline-flex items-center rounded-xl border px-3 py-1 text-xs font-semibold ${cls}`;
      span.textContent = c || "—";
      return span;
    }

    const productHeaderClasses = [
      "bg-sky-100 text-sky-900 border-sky-200",
      "bg-amber-100 text-amber-900 border-amber-200",
      "bg-red-100 text-red-900 border-red-200",
      "bg-emerald-100 text-emerald-900 border-emerald-200",
      "bg-lime-100 text-lime-900 border-lime-200",
      "bg-indigo-100 text-indigo-900 border-indigo-200",
    ];

    function productStyle(index) {
      return productHeaderClasses[index % productHeaderClasses.length];
    }

    const dirtyProducts = new Map();
    const dirtyMarket = new Map();
    const dirtySector = new Map();
    const dirtyCompetitors = new Map();
    const newCompetitorsByProduct = new Map();

    function setButtonEnabled(btn, enabled) {
      if (!btn) return;
      btn.disabled = !enabled;
    }

    function toNumberOrZero(value) {
      const n = Number(value);
      return Number.isFinite(n) ? n : 0;
    }

    function rateDisplayValue(v) {
      const n = Number(v);
      if (!Number.isFinite(n)) return "";
      return formatPlainNumber(n * 100, 2);
    }

    function rateToDecimal(v) {
      const n = Number(v);
      if (!Number.isFinite(n)) return 0;
      return n / 100;
    }

    function marketYearsKey() {
      return `ri:bcg-market-years:${String(projectId || "")}`;
    }

    function loadMarketYearsFromStorage() {
      try {
        const raw = localStorage.getItem(marketYearsKey());
        if (!raw) return [];
        const parsed = JSON.parse(raw);
        if (!Array.isArray(parsed)) return [];
        const out = [];
        for (const v of parsed) {
          const y = Number(v);
          if (!Number.isFinite(y)) continue;
          if (y < 1900 || y > 2100) continue;
          out.push(y);
        }
        return Array.from(new Set(out)).sort((a, b) => a - b);
      } catch {
        return [];
      }
    }

    function saveMarketYearsToStorage(years) {
      try {
        const clean = Array.isArray(years) ? years : [];
        localStorage.setItem(marketYearsKey(), JSON.stringify(clean));
      } catch {}
    }

    function addMarketYearToStorage(year) {
      const y = Number(year);
      if (!Number.isFinite(y) || y < 1900 || y > 2100) return;
      const current = loadMarketYearsFromStorage();
      if (!current.includes(y)) current.push(y);
      current.sort((a, b) => a - b);
      saveMarketYearsToStorage(current);
    }

    function removeMarketYearFromStorage(year) {
      const y = Number(year);
      if (!Number.isFinite(y)) return;
      const current = loadMarketYearsFromStorage().filter((v) => v !== y);
      saveMarketYearsToStorage(current);
    }

    function getMarketYears(products) {
      const yearsSet = new Set(loadMarketYearsFromStorage());
      for (const p of products) {
        const periods = Array.isArray(p.market_periods) ? p.market_periods : [];
        for (const mp of periods) {
          const y = Number(mp.anio);
          if (!Number.isFinite(y)) continue;
          yearsSet.add(y);
        }
      }
      return Array.from(yearsSet).sort((a, b) => a - b);
    }

    function renumberBcgFodaRows(tbody) {
      const rows = Array.from(tbody.querySelectorAll("tr"));
      rows.forEach((row, idx) => {
        const n = row.querySelector("td");
        if (n) n.textContent = String(idx + 1);
      });
    }

    function createBcgFodaRow(tipo) {
      const tr = document.createElement("tr");
      tr.setAttribute("data-bcg-foda-row", String(tipo || ""));

      const tdN = document.createElement("td");
      tdN.className = "px-4 py-3 text-center text-xs font-semibold text-neutral-600";
      tdN.textContent = "—";

      const tdInput = document.createElement("td");
      tdInput.className = "px-4 py-2";
      const input = document.createElement("input");
      input.type = "text";
      input.className = "bcg-foda-input h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200";
      tdInput.appendChild(input);

      const tdAction = document.createElement("td");
      tdAction.className = "px-4 py-2 text-right";
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "bcg-foda-remove inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50";
      btn.textContent = "Quitar";
      tdAction.appendChild(btn);

      tr.appendChild(tdN);
      tr.appendChild(tdInput);
      tr.appendChild(tdAction);
      return tr;
    }

    function ensureAtLeastOneBcgFodaRow(tbody, tipo) {
      const rows = Array.from(tbody.querySelectorAll("tr"));
      if (rows.length > 0) {
        renumberBcgFodaRows(tbody);
        return;
      }
      tbody.appendChild(createBcgFodaRow(tipo));
      renumberBcgFodaRows(tbody);
    }

    function bindBcgFodaRemove(tbody, tipo) {
      if (!tbody || tbody.dataset.bcgFodaBound === "1") return;
      tbody.dataset.bcgFodaBound = "1";
      tbody.addEventListener("click", (e) => {
        const btn = e.target && e.target.closest ? e.target.closest(".bcg-foda-remove") : null;
        if (!btn) return;
        const row = btn.closest("tr");
        const rows = Array.from(tbody.querySelectorAll("tr"));
        if (rows.length <= 1) {
          const input = row ? row.querySelector("input") : null;
          if (input) input.value = "";
          ensureAtLeastOneBcgFodaRow(tbody, tipo);
          return;
        }
        if (row) row.remove();
        ensureAtLeastOneBcgFodaRow(tbody, tipo);
      });
    }

    function collectBcgFodaValues(tbody) {
      const rows = Array.from(tbody.querySelectorAll("tr"));
      const out = [];
      for (const row of rows) {
        const value = (row.querySelector("input")?.value || "").trim();
        if (value) out.push(value);
      }
      return out;
    }

    async function postAction(action, fields) {
      const fd = new FormData();
      fd.set("action", action);
      fd.set("t", projectToken || "");
      fd.set("id_proyecto", String(projectId || ""));
      Object.entries(fields || {}).forEach(([k, v]) => fd.set(k, String(v ?? "")));
      const res = await fetch("detalle-proyecto.php", {
        method: "POST",
        headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
        body: fd,
      });
      const text = await res.text().catch(() => "");
      const json = (() => {
        try {
          return text ? JSON.parse(text) : null;
        } catch {
          return null;
        }
      })();
      return { ok: res.ok, json, status: res.status, text };
    }

    function fillProductSelect(selectEl, products) {
      if (!selectEl) return;
      const current = selectEl.value;
      selectEl.innerHTML = "";
      const opt0 = document.createElement("option");
      opt0.value = "";
      opt0.textContent = "Producto (opcional)";
      selectEl.appendChild(opt0);
      for (const p of products) {
        const opt = document.createElement("option");
        opt.value = String(p.id_producto_bcg);
        opt.textContent = String(p.nombre || "—");
        selectEl.appendChild(opt);
      }
      if (current) selectEl.value = current;
    }

    function renderProducts(products) {
      if (!productsBody) return;
      productsBody.innerHTML = "";
      for (let idx = 0; idx < products.length; idx++) {
        const p = products[idx];
        const tr = document.createElement("tr");
        tr.setAttribute("data-bcg-product-id", String(p.id_producto_bcg));

        const tdName = document.createElement("td");
        tdName.className = "px-4 py-3";
        const nameWrap = document.createElement("div");
        nameWrap.className = `inline-flex h-10 w-full items-center gap-2 rounded-xl border px-3 ${productStyle(idx)}`;
        const nameInput = document.createElement("input");
        nameInput.type = "text";
        nameInput.value = String(p.nombre || "");
        nameInput.setAttribute("data-bcg-name", "1");
        nameInput.className = "h-8 w-full bg-transparent text-sm font-semibold text-neutral-900 outline-none placeholder:text-neutral-500";
        nameWrap.appendChild(nameInput);
        tdName.appendChild(nameWrap);

        const tdSales = document.createElement("td");
        tdSales.className = "px-4 py-3";
        const salesInput = document.createElement("input");
        salesInput.type = "number";
        salesInput.min = "0";
        salesInput.step = "0.01";
        const dirty = dirtyProducts.get(p.id_producto_bcg) || {};
        salesInput.value = String(dirty.ventas_empresa ?? formatPlainNumber(p.ventas_empresa ?? 0, 2));
        salesInput.setAttribute("data-bcg-sales", "1");
        salesInput.className = "h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200";
        tdSales.appendChild(salesInput);

        const tdPct = document.createElement("td");
        tdPct.className = "px-4 py-3 text-sm text-neutral-700";
        tdPct.textContent = formatPct(p.porcentaje_ventas_pct ?? 0);

        const tdActions = document.createElement("td");
        tdActions.className = "px-4 py-3 text-right";
        const delBtn = document.createElement("button");
        delBtn.type = "button";
        delBtn.className = "inline-flex h-9 items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 text-xs font-semibold text-red-700 hover:bg-red-100";
        delBtn.textContent = "Eliminar";
        delBtn.addEventListener("click", async () => {
          if (!confirm("¿Eliminar este producto BCG?")) return;
          clearError();
          const { ok, json } = await postAction("bcg_delete_product", { id_producto_bcg: p.id_producto_bcg });
          if (!ok || !json || json.ok !== true) {
            showError((json && json.error) ? json.error : "No se pudo eliminar.");
            return;
          }
          applyState(json.payload);
        });

        tdActions.appendChild(delBtn);

        nameInput.value = String(dirty.nombre ?? (p.nombre || ""));
        nameInput.addEventListener("input", () => {
          const current = dirtyProducts.get(p.id_producto_bcg) || {};
          current.nombre = nameInput.value;
          dirtyProducts.set(p.id_producto_bcg, current);
          nameWrap.classList.add("ring-2", "ring-brand-200");
          setButtonEnabled(productsSaveAll, dirtyProducts.size > 0);
        });
        salesInput.addEventListener("input", () => {
          const current = dirtyProducts.get(p.id_producto_bcg) || {};
          current.ventas_empresa = salesInput.value;
          dirtyProducts.set(p.id_producto_bcg, current);
          salesInput.classList.add("ring-2", "ring-brand-200");
          setButtonEnabled(productsSaveAll, dirtyProducts.size > 0);
        });

        tr.appendChild(tdName);
        tr.appendChild(tdSales);
        tr.appendChild(tdPct);
        tr.appendChild(tdActions);

        productsBody.appendChild(tr);
      }
    }

    function renderMarketMatrix(products) {
      if (!marketMatrixBody || !marketHeadRow) return;

      marketHeadRow.innerHTML = `<th class="w-24 px-4 py-3">PERIODOS</th>`;
      for (let idx = 0; idx < products.length; idx++) {
        const p = products[idx];
        const th = document.createElement("th");
        th.className = `min-w-[160px] px-4 py-3 text-center text-xs font-semibold ${productStyle(idx)}`;
        th.textContent = String(p.nombre || "—");
        marketHeadRow.appendChild(th);
      }

      const yearsSet = new Set();
      const demandByProductYear = new Map();
      for (const p of products) {
        const periods = Array.isArray(p.market_periods) ? p.market_periods : [];
        for (const mp of periods) {
          const y = Number(mp.anio);
          if (!Number.isFinite(y)) continue;
          yearsSet.add(y);
          demandByProductYear.set(`${p.id_producto_bcg}:${y}`, mp);
        }
      }

      let years = getMarketYears(products);
      marketMatrixBody.innerHTML = "";

      if (years.length === 0) {
        const tr = document.createElement("tr");
        const td = document.createElement("td");
        td.colSpan = 1 + Math.max(1, products.length);
        td.className = "px-4 py-4 text-sm text-neutral-600";
        td.textContent = "Ingresa el año de inicio y presiona “Agregar tasa” para generar el periodo. Luego edita las celdas y guarda.";
        tr.appendChild(td);
        marketMatrixBody.appendChild(tr);
        return;
      }

      for (const y of years) {
        const tr = document.createElement("tr");
        const tdYear = document.createElement("td");
        tdYear.className = "px-4 py-3 text-sm font-semibold text-neutral-800";
        const yearWrap = document.createElement("div");
        yearWrap.className = "flex items-center justify-between gap-2";
        const yearLabel = document.createElement("span");
        yearLabel.textContent = `${y}-${y + 1}`;
        const yearDel = document.createElement("button");
        yearDel.type = "button";
        yearDel.className = "inline-flex h-8 items-center justify-center rounded-lg border border-red-200 bg-red-50 px-2 text-xs font-semibold text-red-700 hover:bg-red-100";
        yearDel.textContent = "Eliminar";
        yearDel.addEventListener("click", async () => {
          if (!confirm(`¿Eliminar el periodo ${y}-${y + 1}?`)) return;
          clearError();
          const prev = loadMarketYearsFromStorage();
          removeMarketYearFromStorage(y);
          const hadAny = products.some((p) => Array.isArray(p.market_periods) && p.market_periods.some((mp) => Number(mp.anio) === y));
          if (!hadAny) {
            renderMarketMatrix(products);
            renderSectorDemandMatrix(products);
            showToast("Eliminado", "Periodo eliminado correctamente.");
            return;
          }
          const prevLabel = yearDel.textContent;
          yearDel.textContent = "Eliminando…";
          yearDel.disabled = true;
          const { ok, json, status, text } = await postAction("bcg_delete_market_year_batch", { anio: y });
          yearDel.textContent = prevLabel || "Eliminar";
          yearDel.disabled = false;
          if (!ok || !json || json.ok !== true) {
            saveMarketYearsToStorage(prev);
            const body = String(text || "");
            const looksHtml = body.includes("<html") || body.includes("<!DOCTYPE");
            const msg =
              (json && json.error) ? json.error :
              (looksHtml ? "No se pudo eliminar: la sesión pudo haber expirado. Vuelve a ingresar e inténtalo." :
              (status ? `No se pudo eliminar el periodo (HTTP ${status}).` : "No se pudo eliminar el periodo."));
            showError(msg);
            return;
          }
          applyState(json.payload);
          showToast("Eliminado", "Periodo eliminado correctamente.");
        });
        yearWrap.appendChild(yearLabel);
        yearWrap.appendChild(yearDel);
        tdYear.appendChild(yearWrap);
        tr.appendChild(tdYear);

        for (let idx = 0; idx < products.length; idx++) {
          const p = products[idx];
          const td = document.createElement("td");
          td.className = "px-3 py-2";
          const cell = document.createElement("input");
          cell.type = "number";
          cell.min = "0";
          cell.step = "0.01";
          const mp = demandByProductYear.get(`${p.id_producto_bcg}:${y}`);
          const dirtyKey = `${p.id_producto_bcg}:${y}`;
          cell.value = dirtyMarket.has(dirtyKey)
            ? String(dirtyMarket.get(dirtyKey))
            : (mp ? rateDisplayValue(mp.demanda_mercado ?? 0) : "");
          cell.placeholder = "0";
          cell.className = "h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200";
          cell.addEventListener("input", () => {
            dirtyMarket.set(dirtyKey, cell.value === "" ? "0" : cell.value);
            cell.classList.add("ring-2", "ring-brand-200");
            setButtonEnabled(marketSaveAll, dirtyMarket.size > 0);
          });
          td.appendChild(cell);
          tr.appendChild(td);
        }

        marketMatrixBody.appendChild(tr);
      }
    }

    function renderSectorDemandMatrix(products) {
      if (!sectorMatrixBody || !sectorHeadRow) return;

      sectorHeadRow.innerHTML = `<th class="w-24 px-4 py-3">AÑOS</th>`;
      for (let idx = 0; idx < products.length; idx++) {
        const p = products[idx];
        const th = document.createElement("th");
        th.className = `min-w-[160px] px-4 py-3 text-center text-xs font-semibold ${productStyle(idx)}`;
        th.textContent = String(p.nombre || "—");
        sectorHeadRow.appendChild(th);
      }

      const yearsSet = new Set();
      const demandByProductYear = new Map();
      for (const p of products) {
        const periods = Array.isArray(p.sector_demand_periods) ? p.sector_demand_periods : [];
        for (const sp of periods) {
          const y = Number(sp.anio);
          if (!Number.isFinite(y)) continue;
          yearsSet.add(y);
          demandByProductYear.set(`${p.id_producto_bcg}:${y}`, sp);
        }
      }

      let years = getMarketYears(products);
      if (years.length === 0) {
        years = Array.from(yearsSet).sort((a, b) => a - b);
      }
      sectorMatrixBody.innerHTML = "";
      if (years.length === 0) return;

      for (const y of years) {
        const tr = document.createElement("tr");
        const tdYear = document.createElement("td");
        tdYear.className = "px-4 py-3 text-sm font-semibold text-neutral-800";
        tdYear.textContent = String(y);
        tr.appendChild(tdYear);

        for (let idx = 0; idx < products.length; idx++) {
          const p = products[idx];
          const td = document.createElement("td");
          td.className = "px-3 py-2";
          const cell = document.createElement("input");
          cell.type = "number";
          cell.min = "0";
          cell.step = "0.01";
          const sp = demandByProductYear.get(`${p.id_producto_bcg}:${y}`);
          const dirtyKey = `${p.id_producto_bcg}:${y}`;
          cell.value = dirtySector.has(dirtyKey) ? String(dirtySector.get(dirtyKey)) : (sp ? formatPlainNumber(sp.demanda_sector ?? 0, 2) : "");
          cell.placeholder = "0";
          cell.className = "h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200";
          cell.addEventListener("input", () => {
            dirtySector.set(dirtyKey, cell.value === "" ? "0" : cell.value);
            cell.classList.add("ring-2", "ring-brand-200");
            setButtonEnabled(sectorSaveAll, dirtySector.size > 0);
          });
          td.appendChild(cell);
          tr.appendChild(td);
        }

        sectorMatrixBody.appendChild(tr);
      }
    }

    function renderCompetitorsGrid(products) {
      if (!competitorsGrid) return;
      competitorsGrid.innerHTML = "";

      for (let idx = 0; idx < products.length; idx++) {
        const p = products[idx];
        const comps = Array.isArray(p.competitors) ? p.competitors : [];
        const queued = newCompetitorsByProduct.get(p.id_producto_bcg) || [];

        const card = document.createElement("div");
        card.className = "rounded-2xl border border-neutral-200 bg-white shadow-sm overflow-hidden";

        const header = document.createElement("div");
        header.className = `px-4 py-3 border-b ${productStyle(idx)}`;
        header.innerHTML = `<div class="text-sm font-semibold">${String(p.nombre || "—")}</div>`;
        card.appendChild(header);

        const body = document.createElement("div");
        body.className = "p-4";

        const form = document.createElement("div");
        form.className = "grid grid-cols-1 gap-3 md:grid-cols-[1fr_220px_auto]";
        form.innerHTML = `
          <input type="text" data-bcg-comp-name class="h-10 rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="Competidor" />
          <input type="number" min="0" step="0.01" data-bcg-comp-sales class="h-10 rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="Ventas" />
          <button type="button" data-bcg-comp-add class="h-10 rounded-xl bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700">Agregar</button>
        `;
        body.appendChild(form);

        const tableWrap = document.createElement("div");
        tableWrap.className = "mt-4 overflow-x-auto rounded-xl border border-neutral-200 bg-white";
        const table = document.createElement("table");
        table.className = "min-w-[620px] w-full text-left text-sm";
        table.innerHTML = `
          <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
            <tr>
              <th class="px-4 py-3">Competidor</th>
              <th class="w-56 px-4 py-3">Ventas</th>
              <th class="w-32 px-4 py-3 text-right">Acción</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-neutral-200"></tbody>
          <tfoot class="bg-neutral-50 text-xs font-semibold text-neutral-700">
            <tr>
              <td class="px-4 py-3">Mayor</td>
              <td class="px-4 py-3">${formatMoney(p.mayor_competidor ?? 0)}</td>
              <td class="px-4 py-3"></td>
            </tr>
          </tfoot>
        `;
        tableWrap.appendChild(table);
        body.appendChild(tableWrap);

        const tbody = table.querySelector("tbody");
        if (tbody) {
          for (const c of comps) {
            const tr = document.createElement("tr");
            const tdN = document.createElement("td");
            tdN.className = "px-4 py-3";
            const nInput = document.createElement("input");
            nInput.type = "text";
            nInput.value = String(c.nombre || "");
            nInput.className = "h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200";
            tdN.appendChild(nInput);

            const tdV = document.createElement("td");
            tdV.className = "px-4 py-3";
            const vInput = document.createElement("input");
            vInput.type = "number";
            vInput.min = "0";
            vInput.step = "0.01";
            vInput.value = formatPlainNumber(c.ventas ?? 0, 2);
            vInput.className = "h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200";
            tdV.appendChild(vInput);

            const tdA = document.createElement("td");
            tdA.className = "px-4 py-3 text-right";
            const del = document.createElement("button");
            del.type = "button";
            del.className = "inline-flex h-9 items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 text-xs font-semibold text-red-700 hover:bg-red-100";
            del.textContent = "Eliminar";
            del.addEventListener("click", async () => {
              clearError();
              const { ok, json } = await postAction("bcg_delete_competitor", { id_competidor: c.id_competidor });
              if (!ok || !json || json.ok !== true) {
                showError((json && json.error) ? json.error : "No se pudo eliminar.");
                return;
              }
              applyState(json.payload);
            });
            tdA.appendChild(del);

            nInput.addEventListener("input", () => {
              const cur = dirtyCompetitors.get(c.id_competidor) || {};
              cur.nombre = nInput.value;
              dirtyCompetitors.set(c.id_competidor, cur);
              nInput.classList.add("ring-2", "ring-brand-200");
              setButtonEnabled(competitorsSaveAll, dirtyCompetitors.size > 0 || Array.from(newCompetitorsByProduct.values()).some((a) => a.length > 0));
            });
            vInput.addEventListener("input", () => {
              const cur = dirtyCompetitors.get(c.id_competidor) || {};
              cur.ventas = vInput.value;
              dirtyCompetitors.set(c.id_competidor, cur);
              vInput.classList.add("ring-2", "ring-brand-200");
              setButtonEnabled(competitorsSaveAll, dirtyCompetitors.size > 0 || Array.from(newCompetitorsByProduct.values()).some((a) => a.length > 0));
            });

            tr.appendChild(tdN);
            tr.appendChild(tdV);
            tr.appendChild(tdA);
            tbody.appendChild(tr);
          }

          for (let i = 0; i < queued.length; i++) {
            const q = queued[i];
            const tr = document.createElement("tr");
            const tdN = document.createElement("td");
            tdN.className = "px-4 py-3";
            const nInput = document.createElement("input");
            nInput.type = "text";
            nInput.value = String(q.nombre || "");
            nInput.className = "h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200";
            tdN.appendChild(nInput);

            const tdV = document.createElement("td");
            tdV.className = "px-4 py-3";
            const vInput = document.createElement("input");
            vInput.type = "number";
            vInput.min = "0";
            vInput.step = "0.01";
            vInput.value = formatPlainNumber(q.ventas ?? 0, 2);
            vInput.className = "h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200";
            tdV.appendChild(vInput);

            const tdA = document.createElement("td");
            tdA.className = "px-4 py-3 text-right";
            const rm = document.createElement("button");
            rm.type = "button";
            rm.className = "inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50";
            rm.textContent = "Quitar";
            rm.addEventListener("click", () => {
              const list = newCompetitorsByProduct.get(p.id_producto_bcg) || [];
              list.splice(i, 1);
              newCompetitorsByProduct.set(p.id_producto_bcg, list);
              setButtonEnabled(competitorsSaveAll, dirtyCompetitors.size > 0 || Array.from(newCompetitorsByProduct.values()).some((a) => a.length > 0));
              renderCompetitorsGrid(lastPayload && Array.isArray(lastPayload.products) ? lastPayload.products : []);
            });
            tdA.appendChild(rm);

            nInput.addEventListener("input", () => {
              q.nombre = nInput.value;
              setButtonEnabled(competitorsSaveAll, true);
            });
            vInput.addEventListener("input", () => {
              q.ventas = vInput.value;
              setButtonEnabled(competitorsSaveAll, true);
            });

            tr.appendChild(tdN);
            tr.appendChild(tdV);
            tr.appendChild(tdA);
            tbody.appendChild(tr);
          }
        }

        const addBtn = form.querySelector("[data-bcg-comp-add]");
        const nameEl = form.querySelector("[data-bcg-comp-name]");
        const salesEl = form.querySelector("[data-bcg-comp-sales]");
        if (addBtn) {
          addBtn.addEventListener("click", async () => {
            clearError();
            const nombre = nameEl ? nameEl.value : "";
            const ventas = salesEl ? salesEl.value : "";
            if (!String(nombre).trim()) {
              showError("El nombre del competidor es obligatorio.");
              return;
            }
            const list = newCompetitorsByProduct.get(p.id_producto_bcg) || [];
            list.push({ nombre: String(nombre), ventas: String(ventas || "0") });
            newCompetitorsByProduct.set(p.id_producto_bcg, list);
            if (nameEl) nameEl.value = "";
            if (salesEl) salesEl.value = "";
            setButtonEnabled(competitorsSaveAll, true);
            renderCompetitorsGrid(lastPayload && Array.isArray(lastPayload.products) ? lastPayload.products : []);
          });
        }

        card.appendChild(body);
        competitorsGrid.appendChild(card);
      }
    }

    function renderSummary(products) {
      if (!summaryHeadRow || !summaryBody) return;

      summaryHeadRow.innerHTML = `<th class="w-28 px-4 py-3">INDICADOR</th>`;
      for (let idx = 0; idx < products.length; idx++) {
        const p = products[idx];
        const th = document.createElement("th");
        th.className = `min-w-[160px] px-4 py-3 text-center text-xs font-semibold ${productStyle(idx)}`;
        th.textContent = String(p.nombre || "—");
        summaryHeadRow.appendChild(th);
      }

      const rows = [
        { key: "tcm", label: "TCM" },
        { key: "prm", label: "PRM" },
        { key: "pct", label: "% VTAS" },
      ];

      summaryBody.innerHTML = "";
      for (const r of rows) {
        const tr = document.createElement("tr");
        const tdLabel = document.createElement("td");
        tdLabel.className = "px-4 py-3 text-sm font-semibold text-neutral-800";
        tdLabel.textContent = r.label;
        tr.appendChild(tdLabel);

        for (const p of products) {
          const td = document.createElement("td");
          td.className = "px-4 py-3 text-center text-sm text-neutral-700";
          if (r.key === "tcm") {
            td.textContent = `${(Number(p.tcm ?? 0) * 100).toFixed(2)}%`;
          } else if (r.key === "prm") {
            td.textContent = String(Number(p.prm ?? 0).toFixed(2));
          } else {
            td.textContent = formatPct(p.porcentaje_ventas_pct ?? 0);
          }
          tr.appendChild(td);
        }
        summaryBody.appendChild(tr);
      }
    }

    async function ensureChart() {
      if (!chartCanvas) return null;
      const ok = await loadChartJs();
      if (!ok || !window.Chart) return null;
      if (chart) return chart;
      const quadrantPlugin = {
        id: "bcgQuadrants",
        afterDraw: (c) => {
          const { ctx, chartArea, scales } = c;
          if (!chartArea) return;
          const x = scales.x;
          const y = scales.y;
          if (!x || !y) return;
          const xPx = x.getPixelForValue(1);
          const yPx = y.getPixelForValue(0.1);

          const left = chartArea.left;
          const right = chartArea.right;
          const top = chartArea.top;
          const bottom = chartArea.bottom;
          ctx.save();
          ctx.strokeStyle = "rgba(120, 120, 120, 0.35)";
          ctx.fillStyle = "rgba(34, 197, 94, 0.08)";
          ctx.fillRect(xPx, top, right - xPx, yPx - top);
          ctx.fillStyle = "rgba(245, 158, 11, 0.10)";
          ctx.fillRect(left, top, xPx - left, yPx - top);
          ctx.fillStyle = "rgba(59, 130, 246, 0.08)";
          ctx.fillRect(xPx, yPx, right - xPx, bottom - yPx);
          ctx.fillStyle = "rgba(239, 68, 68, 0.08)";
          ctx.fillRect(left, yPx, xPx - left, bottom - yPx);

          ctx.lineWidth = 1;
          ctx.setLineDash([6, 6]);
          ctx.beginPath();
          ctx.beginPath();
          ctx.moveTo(xPx, chartArea.top);
          ctx.lineTo(xPx, chartArea.bottom);
          ctx.stroke();
          ctx.beginPath();
          ctx.moveTo(chartArea.left, yPx);
          ctx.lineTo(chartArea.right, yPx);
          ctx.stroke();
          ctx.restore();
        },
      };
      chart = new window.Chart(chartCanvas.getContext("2d"), {
        type: "bubble",
        data: { datasets: [] },
        plugins: [quadrantPlugin],
        options: {
          responsive: true,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: (ctx) => {
                  const raw = ctx.raw || {};
                  const label = raw.label ? `${raw.label}: ` : "";
                  const prm = Number(raw.x ?? 0);
                  const tcm = Number(raw.y ?? 0);
                  const salesPct = Number(raw.salesPct ?? 0);
                  const tcmPct = Number.isFinite(tcm) ? `${(tcm * 100).toFixed(2)}%` : "—";
                  const prmTxt = Number.isFinite(prm) ? prm.toFixed(2) : "—";
                  const bubble = Number.isFinite(salesPct) ? `${salesPct.toFixed(2)}%` : "—";
                  return `${label}PRM ${prmTxt}, TCM ${tcmPct}, % ventas ${bubble}`;
                },
              },
            },
          },
          onHover: () => {},
          onClick: () => {},
          scales: {
            x: { title: { display: true, text: "PRM" }, min: 0, max: 2 },
            y: {
              title: { display: true, text: "TCM (%)" },
              min: 0,
              max: 0.2,
              ticks: {
                callback: (v) => {
                  const n = Number(v);
                  if (!Number.isFinite(n)) return "";
                  return `${Math.round(n * 100)}%`;
                },
              },
            },
          },
        },
      });
      return chart;
    }

    async function renderChartFromState(payload) {
      if (!payload || !Array.isArray(payload.matrix)) return;
      const c = await ensureChart();
      if (!c) return;
      c.data.datasets = [
        {
          data: payload.matrix.map((p) => ({
            x: Number(p.prm ?? 0),
            y: Number(p.tcm ?? 0),
            r: Math.max(6, Number(p.bubbleSize ?? 0) * 0.25),
            label: String(p.productName || ""),
            salesPct: Number(p.bubbleSize ?? 0),
          })),
          backgroundColor: payload.matrix.map((p) => String(p.color || "#999999")),
          borderColor: payload.matrix.map((p) => String(p.color || "#999999")),
          borderWidth: 1,
        },
      ];
      c.update();

      renderLegendProducts(payload);
    }

    let lastPayload = null;

    function computeBcgCompleteness(payload) {
      const products = payload && Array.isArray(payload.products) ? payload.products : [];
      if (products.length === 0) {
        return {
          complete: false,
          title: "Incompleto",
          message: "Agrega al menos un producto para construir la evaluación BCG.",
        };
      }

      const issues = [];
      const globalYears = new Set();
      for (const p of products) {
        const periods = Array.isArray(p.market_periods) ? p.market_periods : [];
        for (const row of periods) {
          const year = Number(row && row.anio);
          if (Number.isFinite(year) && year > 0) globalYears.add(year);
        }
      }
      const years = Array.from(globalYears).sort((a, b) => a - b);

      for (const p of products) {
        const name = String(p && p.nombre ? p.nombre : "Producto").trim() || "Producto";
        const periods = Array.isArray(p && p.market_periods) ? p.market_periods : [];
        const sectorPeriods = Array.isArray(p && p.sector_demand_periods) ? p.sector_demand_periods : [];
        const competitors = Array.isArray(p && p.competitors) ? p.competitors : [];
        const periodYears = new Set();
        const sectorYears = new Set();

        for (const row of periods) {
          const year = Number(row && row.anio);
          if (Number.isFinite(year) && year > 0) periodYears.add(year);
        }
        for (const row of sectorPeriods) {
          const year = Number(row && row.anio);
          if (Number.isFinite(year) && year > 0) sectorYears.add(year);
        }

        if (periods.length < 2) {
          issues.push(`${name}: registra al menos 2 periodos de mercado.`);
        }
        if (competitors.length < 1) {
          issues.push(`${name}: registra al menos 1 competidor.`);
        }
        if (years.length === 0) {
          issues.push(`${name}: registra periodos de mercado para calcular la TCM.`);
        } else {
          for (const year of years) {
            if (!periodYears.has(year)) {
              issues.push(`${name}: falta la tasa del periodo ${year}-${year + 1}.`);
              break;
            }
          }
          for (const year of years) {
            if (!sectorYears.has(year)) {
              issues.push(`${name}: falta la demanda del sector para ${year}.`);
              break;
            }
          }
        }
      }

      if (issues.length === 0) {
        return {
          complete: true,
          title: "Completo",
          message: "La información mínima del análisis BCG está completa y lista para evaluarse.",
        };
      }

      const visible = issues.slice(0, 2).join(" ");
      const extra = issues.length > 2 ? ` Hay ${issues.length - 2} aspecto(s) adicional(es) pendiente(s).` : "";
      return {
        complete: false,
        title: "Incompleto",
        message: `${visible}${extra}`,
      };
    }

    function renderBcgCompleteness(payload) {
      const info = computeBcgCompleteness(payload);
      if (completenessStatusEl) {
        completenessStatusEl.textContent = info.title;
        completenessStatusEl.className = info.complete
          ? "mt-1 text-sm font-semibold text-emerald-700"
          : "mt-1 text-sm font-semibold text-amber-700";
      }
      if (completenessMsgEl) {
        completenessMsgEl.textContent = info.message;
      }
      return info;
    }

    function applyState(payload) {
      lastPayload = payload;
      const products = payload && Array.isArray(payload.products) ? payload.products : [];
      renderProducts(products);
      renderMarketMatrix(products);
      renderSectorDemandMatrix(products);
      renderCompetitorsGrid(products);
      renderSummary(products);
      fillProductSelect(marketProductSel, products);

      if (totalVentasEl) totalVentasEl.textContent = formatMoney(payload.totalVentas ?? 0);
      if (totalVentasInlineEl) totalVentasInlineEl.textContent = formatMoney(payload.totalVentas ?? 0);
      if (fechaCalculoEl) fechaCalculoEl.textContent = String(payload.fechaCalculo || "—");
      renderBcgCompleteness(payload);
      renderChartFromState(payload);
    }

    async function refresh() {
      clearError();
      setLoading(true);
      try {
        const u = new URL("detalle-proyecto.php", window.location.href);
        u.searchParams.set("format", "json");
        u.searchParams.set("bcg", "1");
        u.searchParams.set("t", String(projectToken || ""));
        u.searchParams.set("id_proyecto", String(projectId || ""));
        const res = await fetch(u.toString(), { headers: { Accept: "application/json" } });
        const json = await res.json().catch(() => null);
        if (!res.ok || !json || json.ok !== true || !json.payload) {
          showError((json && json.error) ? json.error : "No se pudo cargar BCG.");
          return;
        }
        applyState(json.payload);
      } catch (e) {
        showError("No se pudo cargar BCG.");
      } finally {
        setLoading(false);
      }
    }

    if (recalcBtn) {
      recalcBtn.addEventListener("click", async () => {
        clearError();
        if (lastPayload) {
          const info = renderBcgCompleteness(lastPayload);
          if (!info.complete) {
            showError(info.message);
            showToast("BCG incompleto", "Completa la información pendiente antes de recalcular la evaluación.");
            return;
          }
        }
        const { ok, json } = await postAction("bcg_recalculate", {});
        if (!ok || !json || json.ok !== true) {
          showError((json && json.error) ? json.error : "No se pudo recalcular.");
          return;
        }
        applyState(json.payload);
      });
    }

    if (addProductBtn) {
      addProductBtn.addEventListener("click", async () => {
        clearError();
        const nombre = newProductName ? newProductName.value : "";
        const ventas = newProductSales ? newProductSales.value : "0";
        const { ok, json } = await postAction("bcg_create_product", { nombre, ventas_empresa: ventas });
        if (!ok || !json || json.ok !== true) {
          showError((json && json.error) ? json.error : "No se pudo crear el producto.");
          return;
        }
        if (newProductName) newProductName.value = "";
        if (newProductSales) newProductSales.value = "";
        applyState(json.payload);
      });
    }

    if (marketSave) {
      marketSave.addEventListener("click", async () => {
        clearError();
        const idProducto = marketProductSel ? marketProductSel.value : "";
        const anio = marketYear ? marketYear.value : "";
        const demanda = marketDemand ? marketDemand.value : "";

        const yearNum = Number(anio);
        if (!Number.isFinite(yearNum) || yearNum < 1900 || yearNum > 2100) {
          showError("Año inválido.");
          return;
        }

        addMarketYearToStorage(yearNum);
        if (lastPayload && Array.isArray(lastPayload.products)) {
          renderMarketMatrix(lastPayload.products);
          renderSectorDemandMatrix(lastPayload.products);
        }

        const productNum = Number(idProducto);
        const hasProduct = Number.isFinite(productNum) && productNum > 0;
        const hasRate = String(demanda).trim() !== "";

        if (!hasProduct || !hasRate) {
          if (marketYear) marketYear.value = String((Number(anio) || 0) + 1 || "");
          showToast("Periodo agregado", `Se generó el periodo ${yearNum}-${yearNum + 1}.`);
          return;
        }

        const { ok, json } = await postAction("bcg_upsert_market_period", { id_producto_bcg: idProducto, anio, demanda_mercado: rateToDecimal(demanda) });
        if (!ok || !json || json.ok !== true) {
          showError((json && json.error) ? json.error : "No se pudo guardar el periodo.");
          return;
        }
        if (marketYear) marketYear.value = String((Number(anio) || 0) + 1 || "");
        if (marketDemand) marketDemand.value = "";
        applyState(json.payload);
        showToast("Guardado", "Tasa registrada correctamente.");
      });
    }

    if (productsSaveAll) {
      productsSaveAll.addEventListener("click", async () => {
        clearError();
        document.querySelectorAll("#bcg-products-body tr").forEach((tr) => {
          const id = Number(tr.getAttribute("data-bcg-product-id") || 0);
          if (!id) return;
          const name = tr.querySelector("[data-bcg-name]");
          const sales = tr.querySelector("[data-bcg-sales]");
          const cur = dirtyProducts.get(id) || {};
          if (name) cur.nombre = name.value;
          if (sales) cur.ventas_empresa = sales.value;
          dirtyProducts.set(id, cur);
        });
        const payload = [];
        for (const [id, fields] of dirtyProducts.entries()) {
          payload.push({
            id_producto_bcg: id,
            nombre: fields.nombre,
            ventas_empresa: toNumberOrZero(fields.ventas_empresa),
          });
        }
        setSaving(productsSaveAll, true);
        const { ok, json } = await postAction("bcg_save_products_batch", { payload: JSON.stringify(payload) });
        setSaving(productsSaveAll, false);
        if (!ok || !json || json.ok !== true) {
          showError((json && json.error) ? json.error : "No se pudo guardar.");
          return;
        }
        dirtyProducts.clear();
        setButtonEnabled(productsSaveAll, false);
        applyState(json.payload);
        showToast("Guardado", "Productos guardados correctamente.");
      });
    }

    if (marketSaveAll) {
      marketSaveAll.addEventListener("click", async () => {
        clearError();
        const payload = [];
        for (const [key, value] of dirtyMarket.entries()) {
          const [pid, y] = String(key).split(":");
          payload.push({
            id_producto_bcg: Number(pid),
            anio: Number(y),
            demanda_mercado: rateToDecimal(value),
          });
        }
        setSaving(marketSaveAll, true);
        const { ok, json } = await postAction("bcg_save_market_rates_batch", { payload: JSON.stringify(payload) });
        setSaving(marketSaveAll, false);
        if (!ok || !json || json.ok !== true) {
          showError((json && json.error) ? json.error : "No se pudo guardar.");
          return;
        }
        dirtyMarket.clear();
        setButtonEnabled(marketSaveAll, false);
        applyState(json.payload);
        showToast("Guardado", "Tasas guardadas correctamente.");
      });
    }

    if (sectorSaveAll) {
      sectorSaveAll.addEventListener("click", async () => {
        clearError();
        const payload = [];
        for (const [key, value] of dirtySector.entries()) {
          const [pid, y] = String(key).split(":");
          payload.push({
            id_producto_bcg: Number(pid),
            anio: Number(y),
            demanda_sector: toNumberOrZero(value),
          });
        }
        setSaving(sectorSaveAll, true);
        const { ok, json } = await postAction("bcg_save_sector_demand_batch", { payload: JSON.stringify(payload) });
        setSaving(sectorSaveAll, false);
        if (!ok || !json || json.ok !== true) {
          showError((json && json.error) ? json.error : "No se pudo guardar.");
          return;
        }
        dirtySector.clear();
        setButtonEnabled(sectorSaveAll, false);
        applyState(json.payload);
        showToast("Guardado", "Demanda sector guardada correctamente.");
      });
    }

    if (competitorsSaveAll) {
      competitorsSaveAll.addEventListener("click", async () => {
        clearError();
        const updates = [];
        for (const [id, fields] of dirtyCompetitors.entries()) {
          updates.push({
            id_competidor: id,
            nombre: fields.nombre,
            ventas: toNumberOrZero(fields.ventas),
          });
        }
        const creates = [];
        for (const [pid, list] of newCompetitorsByProduct.entries()) {
          for (const row of list) {
            creates.push({
              id_producto_bcg: Number(pid),
              nombre: String(row.nombre || ""),
              ventas: toNumberOrZero(row.ventas),
            });
          }
        }
        setSaving(competitorsSaveAll, true);
        const { ok, json } = await postAction("bcg_save_competitors_batch", { payload: JSON.stringify({ updates, creates }) });
        setSaving(competitorsSaveAll, false);
        if (!ok || !json || json.ok !== true) {
          showError((json && json.error) ? json.error : "No se pudo guardar.");
          return;
        }
        dirtyCompetitors.clear();
        newCompetitorsByProduct.clear();
        setButtonEnabled(competitorsSaveAll, false);
        applyState(json.payload);
        showToast("Guardado", "Competidores guardados correctamente.");
      });
    }

    if (bcgFodaFortBody) bindBcgFodaRemove(bcgFodaFortBody, "FORTALEZA");
    if (bcgFodaDebBody) bindBcgFodaRemove(bcgFodaDebBody, "DEBILIDAD");

    if (bcgFodaFortBody) ensureAtLeastOneBcgFodaRow(bcgFodaFortBody, "FORTALEZA");
    if (bcgFodaDebBody) ensureAtLeastOneBcgFodaRow(bcgFodaDebBody, "DEBILIDAD");

    if (bcgFodaAddFort && bcgFodaFortBody) {
      bcgFodaAddFort.addEventListener("click", () => {
        const tr = createBcgFodaRow("FORTALEZA");
        bcgFodaFortBody.appendChild(tr);
        ensureAtLeastOneBcgFodaRow(bcgFodaFortBody, "FORTALEZA");
        tr.querySelector("input")?.focus();
      });
    }
    if (bcgFodaAddDeb && bcgFodaDebBody) {
      bcgFodaAddDeb.addEventListener("click", () => {
        const tr = createBcgFodaRow("DEBILIDAD");
        bcgFodaDebBody.appendChild(tr);
        ensureAtLeastOneBcgFodaRow(bcgFodaDebBody, "DEBILIDAD");
        tr.querySelector("input")?.focus();
      });
    }

    if (bcgFodaSave) {
      bcgFodaSave.addEventListener("click", async () => {
        clearError();
        const fortalezas = bcgFodaFortBody ? collectBcgFodaValues(bcgFodaFortBody) : [];
        const debilidades = bcgFodaDebBody ? collectBcgFodaValues(bcgFodaDebBody) : [];
        setSaving(bcgFodaSave, true);
        const { ok, json, status, text } = await postAction("save_foda_bcg", { payload: JSON.stringify({ fortalezas, debilidades }) });
        setSaving(bcgFodaSave, false);
        if (!ok || !json || json.ok !== true) {
          const body = String(text || "");
          const looksHtml = body.includes("<html") || body.includes("<!DOCTYPE");
          const msg =
            (json && json.error) ? json.error :
            (looksHtml ? "No se pudo guardar: la sesión pudo haber expirado. Vuelve a ingresar e inténtalo." :
            (status ? `No se pudo guardar (HTTP ${status}).` : "No se pudo guardar."));
          showError(msg);
          return;
        }
        showToast("Guardado", "FODA (BCG) guardado correctamente.");
      });
    }

    refresh();
  }

  function initLazyPanel(panelId) {
    if (panelId === "mision") initMisionPanel();
    if (panelId === "vision") initVisionPanel();
    if (panelId === "valores") initValoresPanel();
    if (panelId === "objetivos") initObjetivosPanel();
    if (panelId === "cadena") initCadenaPanel();
    if (panelId === "perfil_competitivo") window.RI && typeof window.RI.initPerfilCompetitivoPanel === "function" && window.RI.initPerfilCompetitivoPanel();
    if (panelId === "pest") window.RI && typeof window.RI.initPestPanel === "function" && window.RI.initPestPanel();
    if (panelId === "estrategias") window.RI && typeof window.RI.initFodaCruzadaPanel === "function" && window.RI.initFodaCruzadaPanel();
    if (panelId === "came") window.RI && typeof window.RI.initCamePanel === "function" && window.RI.initCamePanel();
    if (panelId === "bgg") initBcgPanel();
  }

  projectTabs.forEach((tab) => {
    tab.addEventListener("click", async () => {
      const panelId = tab.getAttribute("data-panel");
      if (!panelId) return;
      await openPanel(panelId);
    });
  });

  const url = new URL(window.location.href);
  const editParam = url.searchParams.get("edit");
  const sectionParam = url.searchParams.get("section");
  const membersParam = url.searchParams.get("members");
  const oeEditParam = url.searchParams.get("oe_edit");
  const oespEditParam = url.searchParams.get("oesp_edit");
  let storedPanel = "";
  try {
    storedPanel = window.localStorage.getItem(panelStorageKey) || "";
  } catch (e) {}
  const normalizedSectionParam = sectionParam && allowedPanels.has(sectionParam) ? sectionParam : "";
  const normalizedStoredPanel = storedPanel && allowedPanels.has(storedPanel) ? storedPanel : "";
  const initialPanel =
    (editParam === "mision" || editParam === "vision" || editParam === "valores") ? editParam :
    ((oeEditParam || oespEditParam) ? "objetivos" : null) ||
    ((membersParam === "1" && document.getElementById("panel-miembros")) ? "miembros" : null) ||
    (normalizedSectionParam || normalizedStoredPanel || "overview");

  setActiveProjectPanel(initialPanel, { updateUrl: false });
  if (alwaysRefreshPanels.has(initialPanel)) {
    reloadPanel(initialPanel);
  } else {
    ensurePanelLoaded(initialPanel);
  }

  let riToastTimer = null;
  function showInlineToast(title, message) {
    if (riToastTimer) clearTimeout(riToastTimer);
    let el = document.getElementById("ri-inline-toast");
    if (!el) {
      el = document.createElement("div");
      el.id = "ri-inline-toast";
      el.className = "fixed bottom-6 right-6 z-50 hidden w-full max-w-sm";
      el.innerHTML = `
        <div class="rounded-2xl border border-neutral-200 bg-white p-4 shadow-lg">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div data-title class="text-sm font-semibold text-neutral-900"></div>
              <div data-msg class="mt-1 text-sm text-neutral-700"></div>
            </div>
            <button type="button" data-close class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50">
              <span class="sr-only">Cerrar</span>
              <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      `;
      document.body.appendChild(el);
      const close = el.querySelector("[data-close]");
      if (close) close.addEventListener("click", () => el.classList.add("hidden"));
    }
    const t = el.querySelector("[data-title]");
    const m = el.querySelector("[data-msg]");
    if (t) t.textContent = String(title || "");
    if (m) m.textContent = String(message || "");
    el.classList.remove("hidden");
    riToastTimer = setTimeout(() => el.classList.add("hidden"), 2200);
  }

  function openConfirmModal(options = {}) {
    const title = String(options.title || "Confirmar acción");
    const message = String(options.message || "¿Confirmas esta acción?");
    const confirmText = String(options.confirmText || "Eliminar");
    const cancelText = String(options.cancelText || "Cancelar");

    return new Promise((resolve) => {
      let modal = document.getElementById("ri-confirm-modal");
      if (!modal) {
        modal = document.createElement("div");
        modal.id = "ri-confirm-modal";
        modal.className = "fixed inset-0 z-50 hidden";
        modal.innerHTML = `
          <div data-backdrop class="absolute inset-0 bg-neutral-900/40 backdrop-blur-[1px]"></div>
          <div class="relative flex min-h-full items-end justify-center p-4 sm:items-center">
            <div role="dialog" aria-modal="true" class="w-full max-w-md rounded-2xl border border-neutral-200 bg-white shadow-xl">
              <div class="flex items-start gap-4 p-5">
                <div class="mt-0.5 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-700">
                  <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86l-8.23 14.27A2 2 0 003.79 21h16.42a2 2 0 001.73-2.87L13.71 3.86a2 2 0 00-3.42 0z" />
                  </svg>
                </div>
                <div class="min-w-0 flex-1">
                  <div data-title class="text-base font-semibold text-neutral-900"></div>
                  <div data-message class="mt-1 text-sm text-neutral-700"></div>
                </div>
              </div>
              <div class="flex flex-col-reverse gap-2 p-5 pt-0 sm:flex-row sm:justify-end">
                <button type="button" data-cancel class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-300 bg-white px-4 text-sm font-semibold text-neutral-800 hover:bg-neutral-50">
                  Cancelar
                </button>
                <button type="button" data-confirm class="inline-flex h-10 items-center justify-center rounded-xl bg-red-600 px-4 text-sm font-semibold text-white hover:bg-red-700">
                  Eliminar
                </button>
              </div>
            </div>
          </div>
        `;
        document.body.appendChild(modal);
      }

      const titleEl = modal.querySelector("[data-title]");
      const msgEl = modal.querySelector("[data-message]");
      const cancelBtn = modal.querySelector("[data-cancel]");
      const confirmBtn = modal.querySelector("[data-confirm]");
      const backdrop = modal.querySelector("[data-backdrop]");

      if (titleEl) titleEl.textContent = title;
      if (msgEl) msgEl.textContent = message;
      if (cancelBtn) cancelBtn.textContent = cancelText;
      if (confirmBtn) confirmBtn.textContent = confirmText;

      function cleanup(result) {
        modal.classList.add("hidden");
        document.removeEventListener("keydown", onKeyDown, true);
        if (cancelBtn) cancelBtn.removeEventListener("click", onCancel);
        if (confirmBtn) confirmBtn.removeEventListener("click", onConfirm);
        if (backdrop) backdrop.removeEventListener("click", onCancel);
        resolve(result);
      }

      function onCancel() {
        cleanup(false);
      }

      function onConfirm() {
        cleanup(true);
      }

      function onKeyDown(e) {
        if (e.key === "Escape") {
          e.preventDefault();
          cleanup(false);
        }
      }

      if (cancelBtn) cancelBtn.addEventListener("click", onCancel);
      if (confirmBtn) confirmBtn.addEventListener("click", onConfirm);
      if (backdrop) backdrop.addEventListener("click", onCancel);
      document.addEventListener("keydown", onKeyDown, true);

      modal.classList.remove("hidden");
      if (confirmBtn instanceof HTMLButtonElement) confirmBtn.focus();
    });
  }

  function setButtonLoading(btn, on, loadingText) {
    if (!(btn instanceof HTMLButtonElement)) return;
    if (on) {
      if (btn.dataset.riOrigHtml === undefined) btn.dataset.riOrigHtml = btn.innerHTML;
      btn.disabled = true;
      btn.setAttribute("aria-busy", "true");
      const text = String(loadingText || "Guardando…");
      btn.innerHTML = `
        <span class="inline-flex items-center gap-2">
          <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
          </svg>
          <span>${text}</span>
        </span>
      `;
      return;
    }

    if (btn.dataset.riOrigHtml !== undefined) {
      btn.innerHTML = btn.dataset.riOrigHtml;
    }
    btn.disabled = false;
    btn.removeAttribute("aria-busy");
  }

  function isAjaxSaveAction(action) {
    return [
      "save_mision",
      "save_vision",
      "save_valores",
      "save_objetivos_batch",
      "create_obj_est",
      "update_obj_est",
      "delete_obj_est",
      "create_obj_esp",
      "update_obj_esp",
      "delete_obj_esp",
    ].includes(action);
  }

  function panelForAction(action) {
    if (action === "save_mision") return "mision";
    if (action === "save_vision") return "vision";
    if (action === "save_valores") return "valores";
    return "objetivos";
  }

  document.addEventListener("submit", async (e) => {
    if (e.defaultPrevented) return;
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (!form.action || !form.action.includes("detalle-proyecto.php")) return;
    const actionEl = form.querySelector('input[name="action"]');
    const action = actionEl ? String(actionEl.value || "") : "";
    if (!isAjaxSaveAction(action)) return;
    e.preventDefault();

    const panelId = panelForAction(action);
    const submitter = (e.submitter instanceof HTMLButtonElement) ? e.submitter : form.querySelector('button[type="submit"]');
    const loadingText = action.startsWith("delete_") ? "Eliminando…" : "Guardando…";
    setButtonLoading(submitter, true, loadingText);

    try {
      const fd = new FormData(form);
      const res = await fetch("detalle-proyecto.php", {
        method: "POST",
        headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
        body: fd,
      });
      const json = await res.json().catch(() => null);
      if (!res.ok || !json || json.ok !== true) {
        const msg = (json && json.error) ? json.error : "No se pudo guardar.";
        showInlineToast("Error", msg);
        return;
      }

      const u = new URL(window.location.href);
      if (panelId === "mision" || panelId === "vision") {
        u.searchParams.delete("edit");
        u.searchParams.set("section", panelId);
        window.history.replaceState({}, "", u.toString());
      }
      if (panelId === "valores") {
        u.searchParams.delete("edit");
        u.searchParams.set("section", "valores");
        window.history.replaceState({}, "", u.toString());
      }
      if (panelId === "objetivos") {
        u.searchParams.delete("oe_edit");
        u.searchParams.delete("oesp_edit");
        u.searchParams.set("section", "objetivos");
        window.history.replaceState({}, "", u.toString());
      }

      showInlineToast("Guardado", (json && json.message) ? json.message : "Guardado correctamente.");
      await reloadPanel(panelId);
      setActiveProjectPanel(panelId, { updateUrl: false });
    } catch (err) {
      showInlineToast("Error", "No se pudo guardar.");
    } finally {
      setButtonLoading(submitter, false);
    }
  });

  document.querySelectorAll("[data-open-panel]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const panelId = btn.getAttribute("data-open-panel");
      if (!panelId) return;
      await openPanel(panelId);
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  });

  /*function openBlockEdit(block) {
    const container = document.querySelector(`[data-block="${block}"]`);
    if (!container) return;
    const view = container.querySelector("[data-block-view]");
    const form = container.querySelector("[data-block-form]");
    if (view) view.classList.add("hidden");
    if (form) {
      form.classList.remove("hidden");
      const textarea = form.querySelector("textarea");
      if (textarea) textarea.focus();
    }

    const editButton = document.querySelector(`[data-js-edit-block="${block}"]`);
    if (editButton) editButton.classList.add("hidden");
  }

  function closeBlockEdit(block) {
    const container = document.querySelector(`[data-block="${block}"]`);
    if (!container) return;
    const view = container.querySelector("[data-block-view]");
    const form = container.querySelector("[data-block-form]");
    if (form) form.classList.add("hidden");
    if (view) view.classList.remove("hidden");

    const editButton = document.querySelector(`[data-js-edit-block="${block}"]`);
    if (editButton) editButton.classList.remove("hidden");
  }*/ 

  function openValoresEdit() {
    const editor = document.getElementById("valores-editor");
    const display = document.getElementById("valores-display");
    if (display) display.classList.add("hidden");
    if (editor) editor.classList.remove("hidden");
    const editButton = document.querySelector("[data-js-edit-valores]");
    if (editButton) editButton.classList.add("hidden");
    const input = document.getElementById("nuevo-valor");
    if (input) input.focus();
  }

  function closeValoresEdit() {
    const editor = document.getElementById("valores-editor");
    const display = document.getElementById("valores-display");
    if (editor) editor.classList.add("hidden");
    if (display) display.classList.remove("hidden");
    const editButton = document.querySelector("[data-js-edit-valores]");
    if (editButton) editButton.classList.remove("hidden");
  }

  function openObjetivoEstrategicoEdit(token) {
    const card = document.querySelector(`[data-oe-card="${token}"]`);
    if (!card) return;
    const view = card.querySelector("[data-oe-view]");
    const form = card.querySelector("[data-oe-form]");
    if (view) view.classList.add("hidden");
    if (form) {
      form.classList.remove("hidden");
      const textarea = form.querySelector("textarea");
      if (textarea) textarea.focus();
    }
  }

  function closeObjetivoEstrategicoEdit(token) {
    const card = document.querySelector(`[data-oe-card="${token}"]`);
    if (!card) return;
    const view = card.querySelector("[data-oe-view]");
    const form = card.querySelector("[data-oe-form]");
    if (form) form.classList.add("hidden");
    if (view) view.classList.remove("hidden");
  }

  /*document.querySelectorAll("[data-js-edit-block]").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      const block = el.getAttribute("data-js-edit-block");
      if (!block) return;
      setActiveProjectPanel(block);
      openBlockEdit(block);
    });
  });

  document.querySelectorAll("[data-js-cancel-block]").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      const block = el.getAttribute("data-js-cancel-block");
      if (!block) return;
      closeBlockEdit(block);
    });
  });*/

  function openObjetivoEspecificoEdit(token) {
    const row = document.querySelector(`[data-oesp-row="${token}"]`);
    if (!row) return;
    const view = row.querySelector("[data-oesp-view]");
    const form = row.querySelector("[data-oesp-form]");
    if (view) view.classList.add("hidden");
    if (form) {
      form.classList.remove("hidden");
      const input = form.querySelector('input[name="descripcion"]');
      if (input) input.focus();
    }
  }

  function closeObjetivoEspecificoEdit(token) {
    const row = document.querySelector(`[data-oesp-row="${token}"]`);
    if (!row) return;
    const view = row.querySelector("[data-oesp-view]");
    const form = row.querySelector("[data-oesp-form]");
    if (form) form.classList.add("hidden");
    if (view) view.classList.remove("hidden");
  }
  const membersManageOpen = document.getElementById("members-manage-open");
  const membersBack = document.getElementById("members-back");

  if (membersManageOpen) {
    membersManageOpen.addEventListener("click", () => {
      setActiveProjectPanel("miembros", { updateUrl: false });
      const u = new URL(window.location.href);
      u.searchParams.set("members", "1");
      window.history.replaceState({}, "", u.toString());
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  if (membersBack) {
    membersBack.addEventListener("click", () => {
      setActiveProjectPanel("overview");
      const u = new URL(window.location.href);
      u.searchParams.delete("members");
      window.history.replaceState({}, "", u.toString());
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  function cviUpdateRowStyles(row) {
    const cells = Array.from(row.querySelectorAll(".cvi-cell"));
    for (const cell of cells) {
      const input = cell.querySelector("input[type='radio']");
      const label = cell.querySelector(".cvi-cell-label");
      const checked = input && input.checked;
      cell.className = checked
        ? "cvi-cell flex h-12 w-full cursor-pointer items-center justify-center select-none bg-brand-50"
        : "cvi-cell flex h-12 w-full cursor-pointer items-center justify-center select-none hover:bg-neutral-50";
      if (label) {
        label.className = checked
          ? "cvi-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-brand-600 bg-brand-600 px-3 text-sm font-semibold text-white shadow-sm transition"
          : "cvi-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-neutral-300 bg-white px-3 text-sm font-semibold text-neutral-700 transition hover:border-brand-300";
      }
    }
  }

  function cviApplyCalc(calc) {
    const sum = Number(calc && calc.sum !== undefined ? calc.sum : 0);
    const valid = Number(calc && calc.valid !== undefined ? calc.valid : 0);
    const count = Number(calc && calc.count !== undefined ? calc.count : cviRows.length);
    const missing = Number(calc && calc.missing !== undefined ? calc.missing : Math.max(0, count - valid));
    const potential = calc ? calc.potential : null;

    if (cviSumEl) cviSumEl.textContent = String(sum);
    if (cviValidEl) cviValidEl.textContent = `${valid}/${count}`;

    if (missing > 0 || potential === null || potential === undefined) {
      if (cviResultEl) {
        cviResultEl.textContent = "Incompleto";
        cviResultEl.className = "mt-1 text-2xl font-semibold text-amber-700";
      }
      if (cviResultSubEl) {
        cviResultSubEl.textContent = cviValidationActive
          ? `Faltan ${missing} respuesta${missing === 1 ? "" : "s"} para guardar la evaluación.`
          : "Completa todas las preguntas para calcular el potencial de mejora.";
      }
      return;
    }

    const p = Number(potential);
    if (Number.isNaN(p)) {
      if (cviResultEl) {
        cviResultEl.textContent = "Incompleto";
        cviResultEl.className = "mt-1 text-2xl font-semibold text-amber-700";
      }
      if (cviResultSubEl) cviResultSubEl.textContent = "No se pudo calcular el potencial de mejora porque la evaluación está incompleta.";
      return;
    }

    if (cviResultEl) {
      cviResultEl.textContent = p.toFixed(2);
      cviResultEl.className = "mt-1 text-2xl font-semibold text-brand-900";
    }
    if (cviResultSubEl) cviResultSubEl.textContent = `${Math.round(p * 100)}%`;
  }

  function cviRecalculateFromDom() {
    let sum = 0;
    let valid = 0;
    let hasInvalid = false;

    for (const row of cviRows) {
      const checked = row.querySelectorAll("input[type='radio']:checked");
      const ref = row.querySelector("[data-cvi-ref]");
      cviUpdateRowStyles(row);

      if (checked.length === 1) {
        valid += 1;
        sum += Number(checked[0].value || 0);
        if (ref) ref.classList.add("hidden");
        row.className = "cvi-row";
        cviAnswers[Number(row.dataset.cviRow || 0)] = Number(checked[0].value || 0);
      } else {
        hasInvalid = true;
        if (cviValidationActive && ref) ref.classList.remove("hidden");
        if (ref && !cviValidationActive) ref.classList.add("hidden");
        row.className = cviValidationActive ? "cvi-row bg-red-50/40" : "cvi-row";
        delete cviAnswers[Number(row.dataset.cviRow || 0)];
      }
    }

    cviApplyCalc({
      sum,
      valid,
      count: cviRows.length,
      missing: hasInvalid ? Math.max(0, cviRows.length - valid) : 0,
      potential: hasInvalid ? null : (1 - (sum / 100)),
    });
  }

  async function cviSaveAll() {
    if (cviSaving) return;
    cviValidationActive = true;
    cviRecalculateFromDom();

    if (Object.keys(cviAnswers).length !== cviRows.length) {
      cviShowToast("error", "Faltan respuestas", "Completa todas las preguntas antes de guardar.");
      return;
    }

    cviSaving = true;
    if (cviSaveButton) {
      cviSaveButton.disabled = true;
      cviSaveButton.className = "inline-flex items-center justify-center rounded-xl bg-brand-600/60 px-4 py-2 text-sm font-semibold text-white shadow-sm";
      cviSaveButton.textContent = "Guardando…";
    }

    try {
      const formData = new FormData();
      formData.set("action", "save_cadena_valor_batch");
      formData.set("t", projectToken || "");
      formData.set("answers", JSON.stringify(cviAnswers));

      const res = await fetch("detalle-proyecto.php", {
        method: "POST",
        body: formData,
        headers: { Accept: "application/json" },
      });

      const json = await res.json();
      if (!json || typeof json !== "object" || !json.ok) {
        cviShowToast("error", "No se pudo guardar", String((json && json.error) || "Error al guardar la evaluación."));
        return;
      }

      if (json.calc) {
        cviApplyCalc(json.calc);
      }
      cviDirty = false;
      cviShowToast("success", "Guardado", "Evaluación guardada correctamente.");
    } catch (e) {
      cviShowToast("error", "No se pudo guardar", "Error al guardar la evaluación.");
    } finally {
      cviSaving = false;
      if (cviSaveButton) {
        cviSaveButton.disabled = false;
        cviSaveButton.className = "inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25";
        cviSaveButton.textContent = "Guardar Evaluación";
      }
    }
  }

  if (cviForm) {
    cviForm.addEventListener("change", (e) => {
      const target = e.target;
      if (!(target instanceof HTMLInputElement)) return;
      if (target.type !== "radio") return;

      const row = target.closest("[data-cvi-row]");
      if (!row) return;

      cviDirty = true;
      cviRecalculateFromDom();
      if (!cviValidationActive) {
        const ref = row.querySelector("[data-cvi-ref]");
        if (ref) ref.classList.add("hidden");
        row.className = "cvi-row";
      }
    });

    cviRecalculateFromDom();
  }

  if (cviSaveButton) {
    cviSaveButton.addEventListener("click", () => {
      cviSaveAll();
    });
  }
</script>

</body>
</html>
