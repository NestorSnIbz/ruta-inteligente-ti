<?php

final class FodaCruzada
{
    public static function listFactorRows(SupabaseClient $supabase, int $idProyecto): array
    {
        if ($idProyecto <= 0) {
            return [];
        }

        $response = $supabase->request(
            'GET',
            '/rest/v1/foda_item',
            [
                'select' => 'fuente,tipo,posicion,descripcion',
                'id_proyecto' => 'eq.' . $idProyecto,
                'fuente' => 'in.(CADENA_VALOR_INTERNA,AUTODIAGNOSTICO_BCG,PERFIL_COMPETITIVO,PEST)',
                'order' => 'fuente.asc,tipo.asc,posicion.asc,id_item.asc',
                'limit' => 500,
            ],
            self::restHeaders($supabase)
        );

        if (($response['status'] ?? 500) >= 400 || !is_array($response['data'] ?? null)) {
            return [];
        }

        return $response['data'];
    }

    public static function buildFactorSet(array $rows): array
    {
        $groups = [
            'FORTALEZA' => [],
            'DEBILIDAD' => [],
            'OPORTUNIDAD' => [],
            'AMENAZA' => [],
        ];

        $typePrefixes = [
            'FORTALEZA' => 'F',
            'DEBILIDAD' => 'D',
            'OPORTUNIDAD' => 'O',
            'AMENAZA' => 'A',
        ];

        $allowed = [
            'CADENA_VALOR_INTERNA' => ['FORTALEZA', 'DEBILIDAD'],
            'AUTODIAGNOSTICO_BCG' => ['FORTALEZA', 'DEBILIDAD'],
            'PERFIL_COMPETITIVO' => ['OPORTUNIDAD', 'AMENAZA'],
            'PEST' => ['OPORTUNIDAD', 'AMENAZA'],
        ];

        $sourceOrder = array_keys($allowed);

        usort($rows, function ($a, $b) use ($sourceOrder): int {
            $a = is_array($a) ? $a : [];
            $b = is_array($b) ? $b : [];
            $af = array_search((string) ($a['fuente'] ?? ''), $sourceOrder, true);
            $bf = array_search((string) ($b['fuente'] ?? ''), $sourceOrder, true);
            $af = $af === false ? 999 : $af;
            $bf = $bf === false ? 999 : $bf;
            if ($af !== $bf) {
                return $af <=> $bf;
            }
            $ap = (int) ($a['posicion'] ?? 0);
            $bp = (int) ($b['posicion'] ?? 0);
            if ($ap !== $bp) {
                return $ap <=> $bp;
            }
            return strcmp((string) ($a['descripcion'] ?? ''), (string) ($b['descripcion'] ?? ''));
        });

        $counters = [
            'FORTALEZA' => 0,
            'DEBILIDAD' => 0,
            'OPORTUNIDAD' => 0,
            'AMENAZA' => 0,
        ];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $fuente = trim((string) ($row['fuente'] ?? ''));
            $tipo = trim((string) ($row['tipo'] ?? ''));
            $posicion = (int) ($row['posicion'] ?? 0);
            $descripcion = trim((string) ($row['descripcion'] ?? ''));
            if ($fuente === '' || $tipo === '' || $posicion <= 0 || $descripcion === '') {
                continue;
            }
            if (!isset($allowed[$fuente]) || !in_array($tipo, $allowed[$fuente], true)) {
                continue;
            }

            $counters[$tipo] += 1;
            $index = $counters[$tipo];
            $groups[$tipo][] = [
                'key' => self::factorKey($fuente, $tipo, $posicion),
                'code' => $typePrefixes[$tipo] . $index,
                'index' => $index,
                'source' => $fuente,
                'source_label' => self::sourceLabel($fuente),
                'type' => $tipo,
                'description' => $descripcion,
                'position' => $posicion,
            ];
        }

        $relations = [
            'FO' => ['rows' => $groups['FORTALEZA'], 'cols' => $groups['OPORTUNIDAD']],
            'FA' => ['rows' => $groups['FORTALEZA'], 'cols' => $groups['AMENAZA']],
            'DO' => ['rows' => $groups['DEBILIDAD'], 'cols' => $groups['OPORTUNIDAD']],
            'DA' => ['rows' => $groups['DEBILIDAD'], 'cols' => $groups['AMENAZA']],
        ];

        $totalCells = 0;
        foreach ($relations as $relation => $meta) {
            $totalCells += count($meta['rows']) * count($meta['cols']);
        }

        $ready = !empty($groups['FORTALEZA']) && !empty($groups['DEBILIDAD']) && !empty($groups['OPORTUNIDAD']) && !empty($groups['AMENAZA']);

        return [
            'groups' => $groups,
            'relations' => $relations,
            'counts' => [
                'fortalezas' => count($groups['FORTALEZA']),
                'debilidades' => count($groups['DEBILIDAD']),
                'oportunidades' => count($groups['OPORTUNIDAD']),
                'amenazas' => count($groups['AMENAZA']),
            ],
            'ready' => $ready,
            'total_cells' => $totalCells,
        ];
    }

    public static function listEvaluacionesByProyecto(SupabaseClient $supabase, int $idProyecto): array
    {
        if ($idProyecto <= 0) {
            return [];
        }

        $response = $supabase->request(
            'GET',
            '/rest/v1/foda_cruzada_evaluacion',
            [
                'select' => 'relacion,fila_key,columna_key,valor',
                'id_proyecto' => 'eq.' . $idProyecto,
                'limit' => 5000,
            ],
            self::restHeaders($supabase)
        );

        if (($response['status'] ?? 500) >= 400 || !is_array($response['data'] ?? null)) {
            return [];
        }

        $out = [];
        foreach ($response['data'] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $rel = strtoupper(trim((string) ($row['relacion'] ?? '')));
            $filaKey = trim((string) ($row['fila_key'] ?? ''));
            $colKey = trim((string) ($row['columna_key'] ?? ''));
            $value = (int) ($row['valor'] ?? -1);
            if (!in_array($rel, self::relationOrder(), true) || $filaKey === '' || $colKey === '' || $value < 0 || $value > 4) {
                continue;
            }
            $out[$rel . '|' . $filaKey . '|' . $colKey] = $value;
        }

        return $out;
    }

    public static function replaceEvaluaciones(SupabaseClient $supabase, int $idProyecto, array $rows): bool
    {
        if ($idProyecto <= 0) {
            return false;
        }

        $delete = $supabase->request(
            'DELETE',
            '/rest/v1/foda_cruzada_evaluacion',
            [
                'id_proyecto' => 'eq.' . $idProyecto,
            ],
            self::restHeaders($supabase)
        );

        if (($delete['status'] ?? 500) >= 400) {
            return false;
        }

        if (empty($rows)) {
            return true;
        }

        $headers = self::restHeaders($supabase);
        $headers['Prefer'] = 'return=minimal';

        $insert = $supabase->request(
            'POST',
            '/rest/v1/foda_cruzada_evaluacion',
            [],
            $headers,
            $rows
        );

        return ($insert['status'] ?? 500) < 400;
    }

    public static function deleteResultado(SupabaseClient $supabase, int $idProyecto): bool
    {
        if ($idProyecto <= 0) {
            return false;
        }

        $response = $supabase->request(
            'DELETE',
            '/rest/v1/foda_cruzada_resultado',
            [
                'id_proyecto' => 'eq.' . $idProyecto,
            ],
            self::restHeaders($supabase)
        );

        return ($response['status'] ?? 500) < 400;
    }

    public static function upsertResultado(SupabaseClient $supabase, int $idProyecto, array $calc): bool
    {
        if ($idProyecto <= 0) {
            return false;
        }

        $summary = is_array($calc['summary'] ?? null) ? (array) $calc['summary'] : [];
        $totals = [];
        foreach (self::relationOrder() as $relation) {
            $totals[$relation] = 0;
        }
        foreach ($summary as $row) {
            if (!is_array($row)) {
                continue;
            }
            $rel = strtoupper(trim((string) ($row['relation'] ?? '')));
            if (!array_key_exists($rel, $totals)) {
                continue;
            }
            $totals[$rel] = (int) ($row['total'] ?? 0);
        }

        $predominant = is_array($calc['predominant'] ?? null) ? (array) $calc['predominant'] : [];
        $headers = self::restHeaders($supabase);
        $headers['Prefer'] = 'resolution=merge-duplicates,return=representation';

        $response = $supabase->request(
            'POST',
            '/rest/v1/foda_cruzada_resultado',
            [],
            $headers,
            [
                'id_proyecto' => $idProyecto,
                'fo_total' => (int) ($totals['FO'] ?? 0),
                'fa_total' => (int) ($totals['FA'] ?? 0),
                'do_total' => (int) ($totals['DO'] ?? 0),
                'da_total' => (int) ($totals['DA'] ?? 0),
                'estrategia_predominante' => (string) ($predominant['relation'] ?? ''),
                'estrategia_label' => (string) ($predominant['label'] ?? ''),
                'conclusion_text' => (string) ($calc['executive_conclusion'] ?? ''),
                'updated_at' => gmdate('Y-m-d H:i:s'),
            ]
        );

        return ($response['status'] ?? 500) < 400;
    }

    public static function compute(array $factorSet, array $answers): array
    {
        $relations = is_array($factorSet['relations'] ?? null) ? (array) $factorSet['relations'] : [];
        $counts = is_array($factorSet['counts'] ?? null) ? (array) $factorSet['counts'] : [];
        $totalCells = (int) ($factorSet['total_cells'] ?? 0);
        $ready = !empty($factorSet['ready']);

        $matrices = [];
        $summary = [];
        $answeredAll = 0;

        foreach (self::relationOrder() as $relation) {
            $meta = is_array($relations[$relation] ?? null) ? (array) $relations[$relation] : ['rows' => [], 'cols' => []];
            $rows = is_array($meta['rows'] ?? null) ? array_values($meta['rows']) : [];
            $cols = is_array($meta['cols'] ?? null) ? array_values($meta['cols']) : [];

            $rowTotals = [];
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $rowTotals[(string) ($row['key'] ?? '')] = 0;
            }
            $colTotals = [];
            foreach ($cols as $col) {
                if (!is_array($col)) {
                    continue;
                }
                $colTotals[(string) ($col['key'] ?? '')] = 0;
            }

            $cells = [];
            $total = 0;
            $answered = 0;
            $topPairs = [];

            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $rowKey = (string) ($row['key'] ?? '');
                $cells[$rowKey] = [];
                foreach ($cols as $col) {
                    if (!is_array($col)) {
                        continue;
                    }
                    $colKey = (string) ($col['key'] ?? '');
                    $composite = $relation . '|' . $rowKey . '|' . $colKey;
                    $value = array_key_exists($composite, $answers) ? (int) $answers[$composite] : null;
                    if ($value !== null && ($value < 0 || $value > 4)) {
                        $value = null;
                    }
                    if ($value !== null) {
                        $answered += 1;
                        $total += $value;
                        $rowTotals[$rowKey] = (int) ($rowTotals[$rowKey] ?? 0) + $value;
                        $colTotals[$colKey] = (int) ($colTotals[$colKey] ?? 0) + $value;
                        $topPairs[] = [
                            'relation' => $relation,
                            'value' => $value,
                            'row' => $row,
                            'col' => $col,
                        ];
                    }
                    $cells[$rowKey][$colKey] = $value;
                }
            }

            usort($topPairs, function ($a, $b): int {
                $av = (int) ($a['value'] ?? 0);
                $bv = (int) ($b['value'] ?? 0);
                if ($av !== $bv) {
                    return $bv <=> $av;
                }
                $ar = trim((string) (($a['row']['code'] ?? '') . ($a['col']['code'] ?? '')));
                $br = trim((string) (($b['row']['code'] ?? '') . ($b['col']['code'] ?? '')));
                return strcmp($ar, $br);
            });
            $topPairs = array_slice($topPairs, 0, 3);

            $cellCount = count($rows) * count($cols);
            $missing = max(0, $cellCount - $answered);
            $answeredAll += $answered;

            $matrix = [
                'relation' => $relation,
                'title' => self::relationLabel($relation),
                'description' => self::relationDescription($relation),
                'rows' => $rows,
                'cols' => $cols,
                'cells' => $cells,
                'row_totals' => $rowTotals,
                'col_totals' => $colTotals,
                'total' => $total,
                'cell_count' => $cellCount,
                'answered' => $answered,
                'missing' => $missing,
                'top_pairs' => $topPairs,
            ];
            $matrices[$relation] = $matrix;

            $summary[] = [
                'relation' => $relation,
                'label' => self::relationLabel($relation),
                'total' => $total,
                'description' => self::relationSummaryDescription($relation),
            ];
        }

        $complete = $ready && $totalCells > 0 && $answeredAll === $totalCells;
        $predominant = $complete ? self::predominantFromSummary($summary) : null;
        $executiveConclusion = ($complete && is_array($predominant)) ? self::buildExecutiveConclusion($predominant, $summary, $matrices) : null;

        return [
            'ready' => $ready,
            'counts' => $counts,
            'total_cells' => $totalCells,
            'answered' => $answeredAll,
            'missing' => max(0, $totalCells - $answeredAll),
            'complete' => $complete,
            'matrices' => $matrices,
            'summary' => $summary,
            'predominant' => $predominant,
            'executive_conclusion' => $executiveConclusion,
        ];
    }

    public static function relationOrder(): array
    {
        return ['FO', 'FA', 'DO', 'DA'];
    }

    public static function relationLabel(string $relation): string
    {
        $map = [
            'FO' => 'Estrategia Ofensiva',
            'FA' => 'Estrategia Defensiva',
            'DO' => 'Estrategia de Reorientación',
            'DA' => 'Estrategia de Supervivencia',
        ];
        $relation = strtoupper(trim($relation));
        return $map[$relation] ?? $relation;
    }

    public static function relationDescription(string $relation): string
    {
        $map = [
            'FO' => 'Analiza cómo cada fortaleza permite aprovechar cada oportunidad.',
            'FA' => 'Analiza cómo cada fortaleza ayuda a reducir o neutralizar cada amenaza.',
            'DO' => 'Analiza cómo las oportunidades podrían ayudar a superar cada debilidad.',
            'DA' => 'Analiza cómo las debilidades aumentan el impacto negativo de las amenazas.',
        ];
        $relation = strtoupper(trim($relation));
        return $map[$relation] ?? '';
    }

    public static function relationSummaryDescription(string $relation): string
    {
        $map = [
            'FO' => 'Deberá adoptar estrategias de crecimiento.',
            'FA' => 'La empresa está preparada para enfrentarse a las amenazas.',
            'DA' => 'Se enfrenta a amenazas externas sin las fortalezas necesarias para luchar con la competencia.',
            'DO' => 'La empresa no puede aprovechar las oportunidades porque carece de preparación adecuada.',
        ];
        $relation = strtoupper(trim($relation));
        return $map[$relation] ?? '';
    }

    public static function sourceLabel(string $source): string
    {
        $map = [
            'CADENA_VALOR_INTERNA' => 'Cadena de valor',
            'AUTODIAGNOSTICO_BCG' => 'Matriz BCG',
            'PERFIL_COMPETITIVO' => 'Perfil competitivo',
            'PEST' => 'P.E.S.T.',
        ];
        $source = trim($source);
        return $map[$source] ?? $source;
    }

    public static function factorKey(string $source, string $type, int $position): string
    {
        return trim($source) . ':' . trim($type) . ':' . max(0, $position);
    }

    private static function predominantFromSummary(array $summary): ?array
    {
        $best = null;
        foreach (self::relationOrder() as $relation) {
            foreach ($summary as $row) {
                if (!is_array($row) || strtoupper(trim((string) ($row['relation'] ?? ''))) !== $relation) {
                    continue;
                }
                if ($best === null || (int) ($row['total'] ?? 0) > (int) ($best['total'] ?? 0)) {
                    $best = $row;
                }
            }
        }
        return is_array($best) ? $best : null;
    }

    private static function buildExecutiveConclusion(array $predominant, array $summary, array $matrices): string
    {
        $relation = strtoupper(trim((string) ($predominant['relation'] ?? '')));
        $label = self::relationLabel($relation);
        $total = (int) ($predominant['total'] ?? 0);

        $others = [];
        foreach ($summary as $row) {
            if (!is_array($row)) {
                continue;
            }
            $rel = strtoupper(trim((string) ($row['relation'] ?? '')));
            if ($rel === $relation) {
                continue;
            }
            $others[] = $rel . ' (' . (int) ($row['total'] ?? 0) . ')';
        }

        $matrix = is_array($matrices[$relation] ?? null) ? (array) $matrices[$relation] : [];
        $topPairs = is_array($matrix['top_pairs'] ?? null) ? (array) $matrix['top_pairs'] : [];
        $topPairTexts = [];
        foreach ($topPairs as $pair) {
            if (!is_array($pair)) {
                continue;
            }
            $row = is_array($pair['row'] ?? null) ? (array) $pair['row'] : [];
            $col = is_array($pair['col'] ?? null) ? (array) $pair['col'] : [];
            $topPairTexts[] = trim((string) ($row['code'] ?? '')) . '-' . trim((string) ($col['code'] ?? '')) .
                ' (' . self::shortText((string) ($row['description'] ?? '')) . ' / ' . self::shortText((string) ($col['description'] ?? '')) . ')';
        }

        $actions = [
            'FO' => 'priorizar iniciativas de crecimiento, acelerar el desarrollo comercial, escalar las capacidades que ya generan diferenciación y convertir las oportunidades externas en proyectos concretos con responsables, plazos y presupuesto',
            'FA' => 'reforzar la propuesta de valor, blindar procesos críticos, anticipar contingencias regulatorias o competitivas y utilizar las fortalezas existentes para reducir la exposición de la empresa a amenazas relevantes',
            'DO' => 'cerrar brechas internas mediante capacitación, inversión, alianzas o rediseño de procesos para que las oportunidades del entorno se conviertan en palancas reales de mejora y no en ventajas desaprovechadas',
            'DA' => 'contener riesgos, corregir vulnerabilidades internas prioritarias, racionalizar recursos, elevar controles de gestión y preparar planes de respuesta que disminuyan la vulnerabilidad frente a amenazas externas',
        ];

        $risks = [
            'FO' => 'sobreestimar la capacidad de ejecución, crecer sin estructura suficiente o dispersar recursos en demasiadas iniciativas simultáneas',
            'FA' => 'concentrarse solo en la defensa, reaccionar tarde ante cambios externos o descuidar oportunidades de crecimiento por exceso de cautela',
            'DO' => 'subestimar el tiempo y la inversión necesarios para cerrar debilidades, perder oportunidades por lentitud de adaptación o ejecutar mejoras internas sin foco estratégico',
            'DA' => 'caer en una gestión excesivamente reactiva, deteriorar el posicionamiento por falta de inversión selectiva o mantener vulnerabilidades críticas durante demasiado tiempo',
        ];

        $paragraphs = [];
        $paragraphs[] = 'La matriz FODA cruzada muestra que la estrategia predominante recomendada para la empresa es la ' . $label . ', ya que la relación ' . $relation . ' obtuvo la mayor puntuación acumulada con ' . $total . ' puntos. En comparación con las demás alternativas ' . implode(', ', $others) . ', este resultado indica que la prioridad estratégica debe concentrarse en el patrón de interacción que hoy aporta mayor coherencia entre los factores internos y externos registrados por la empresa.';

        if (!empty($topPairTexts)) {
            $paragraphs[] = 'Los factores que más influyeron en el resultado fueron las combinaciones con mejor valoración dentro de la relación ' . $relation . ': ' . implode('; ', $topPairTexts) . '. Estas asociaciones revelan dónde existe mayor capacidad de respuesta estratégica y cuáles son los vínculos más sólidos entre capacidades internas, limitaciones y condiciones del entorno. En términos prácticos, son las interacciones que deberían orientar primero la toma de decisiones, la priorización de inversiones y la secuencia de ejecución.';
        } else {
            $paragraphs[] = 'Aunque no se identificaron combinaciones destacadas con suficiente peso individual para detallarlas, el total acumulado de la matriz seleccionada sigue marcando la dirección estratégica dominante. Esto sugiere que la empresa debe consolidar el enfoque recomendado y revisar periódicamente las valoraciones para detectar relaciones emergentes con mayor impacto.';
        }

        $paragraphs[] = 'En consecuencia, la empresa debería ' . ($actions[$relation] ?? 'definir un plan de acción concreto alineado con la estrategia resultante') . '. La recomendación es traducir esta lectura en una hoja de ruta operativa con iniciativas priorizadas, métricas de seguimiento y responsables definidos, de modo que cada combinación mejor valorada se convierta en una decisión accionable dentro del plan estratégico.';

        $paragraphs[] = 'Como riesgos potenciales, conviene considerar ' . ($risks[$relation] ?? 'la falta de alineación entre diagnóstico y ejecución') . '. Por ello, además de ejecutar la estrategia predominante, es necesario mantener seguimiento sobre las demás relaciones de la matriz, ya que cambios en el entorno o en la estructura interna podrían modificar la conveniencia de la estrategia actualmente recomendada.';

        return implode("\n\n", $paragraphs);
    }

    private static function shortText(string $text): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        if ($text === '') {
            return 'sin detalle';
        }
        if (function_exists('mb_strlen') && mb_strlen($text, 'UTF-8') > 70) {
            return mb_substr($text, 0, 67, 'UTF-8') . '...';
        }
        if (strlen($text) > 70) {
            return substr($text, 0, 67) . '...';
        }
        return $text;
    }

    private static function restHeaders(SupabaseClient $supabase): array
    {
        $serverKey = $supabase->getServiceRoleKey();
        $apiKey = $serverKey ?: $supabase->getAnonKey();
        $authBearer = $serverKey ?: $supabase->getAnonKey();

        return [
            'apikey' => $apiKey,
            'Authorization' => 'Bearer ' . $authBearer,
        ];
    }
}
