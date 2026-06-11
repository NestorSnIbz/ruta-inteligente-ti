<?php
  $fodaCruzadaFactors = is_array($fodaCruzadaFactors ?? null) ? $fodaCruzadaFactors : [];
  $fodaCruzadaAnswers = is_array($fodaCruzadaAnswers ?? null) ? $fodaCruzadaAnswers : [];
  $fodaCruzadaCalc = is_array($fodaCruzadaCalc ?? null) ? $fodaCruzadaCalc : [];

  $groups = is_array($fodaCruzadaFactors['groups'] ?? null) ? (array) $fodaCruzadaFactors['groups'] : [];
  $matrices = is_array($fodaCruzadaCalc['matrices'] ?? null) ? (array) $fodaCruzadaCalc['matrices'] : [];
  $summary = is_array($fodaCruzadaCalc['summary'] ?? null) ? (array) $fodaCruzadaCalc['summary'] : [];
  $counts = is_array($fodaCruzadaCalc['counts'] ?? null) ? (array) $fodaCruzadaCalc['counts'] : [
    'fortalezas' => 0,
    'debilidades' => 0,
    'oportunidades' => 0,
    'amenazas' => 0,
  ];
  $predominant = is_array($fodaCruzadaCalc['predominant'] ?? null) ? (array) $fodaCruzadaCalc['predominant'] : null;
  $executiveConclusion = trim((string) ($fodaCruzadaCalc['executive_conclusion'] ?? ''));
  $ready = !empty($fodaCruzadaCalc['ready']);
  $complete = !empty($fodaCruzadaCalc['complete']);
  $answered = (int) ($fodaCruzadaCalc['answered'] ?? 0);
  $totalCells = (int) ($fodaCruzadaCalc['total_cells'] ?? 0);
  $missing = (int) ($fodaCruzadaCalc['missing'] ?? max(0, $totalCells - $answered));

  $groupLabels = [
    'FORTALEZA' => 'Fortalezas',
    'DEBILIDAD' => 'Debilidades',
    'OPORTUNIDAD' => 'Oportunidades',
    'AMENAZA' => 'Amenazas',
  ];

  $groupStyles = [
    'FORTALEZA' => 'border-brand-200 bg-brand-50',
    'DEBILIDAD' => 'border-neutral-300 bg-neutral-100',
    'OPORTUNIDAD' => 'border-accent-200 bg-accent-50',
    'AMENAZA' => 'border-neutral-400 bg-white',
  ];

  $matrixHeaders = [
    'FO' => ['title' => 'Estrategias Ofensivas', 'columns' => 'Oportunidades', 'rows' => 'Fortalezas'],
    'FA' => ['title' => 'Estrategias Defensivas', 'columns' => 'Amenazas', 'rows' => 'Fortalezas'],
    'DO' => ['title' => 'Estrategias de Reorientación', 'columns' => 'Oportunidades', 'rows' => 'Debilidades'],
    'DA' => ['title' => 'Estrategias de Supervivencia', 'columns' => 'Amenazas', 'rows' => 'Debilidades'],
  ];

  $summaryDescriptions = [
    'FO' => 'Deberá adoptar estrategias de crecimiento.',
    'FA' => 'La empresa está preparada para enfrentarse a las amenazas.',
    'DA' => 'Se enfrenta a amenazas externas sin las fortalezas necesarias para luchar con la competencia.',
    'DO' => 'La empresa no puede aprovechar las oportunidades porque carece de preparación adecuada.',
  ];

  $scoreLegend = '0 = En total desacuerdo, 1 = No está de acuerdo, 2 = Está de acuerdo, 3 = Bastante de acuerdo, 4 = Totalmente de acuerdo';
?>

<section
  id="panel-estrategias"
  class="project-panel bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm"
  data-swot-ready="<?php echo $ready ? '1' : '0'; ?>"
  data-swot-complete="<?php echo $complete ? '1' : '0'; ?>"
>
  <div class="flex items-center justify-between gap-3">
    <h2 class="text-lg font-semibold">Identificación de estrategias</h2>
  </div>

  <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
    <div class="text-sm text-neutral-700 leading-relaxed">
      Evalúa cada relación de la matriz FODA cruzada con una escala de 0 a 4 para identificar la estrategia predominante recomendada para la empresa.
    </div>
    <div class="mt-2 text-xs text-neutral-500">Opciones: 0 = En total desacuerdo · 1 = No está de acuerdo · 2 = Está de acuerdo · 3 = Bastante de acuerdo · 4 = Totalmente de acuerdo</div>
  </div>

  <div class="mt-5 rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <div class="text-sm font-semibold text-neutral-900">Factores internos y externos detectados</div>
        <div class="mt-0.5 text-xs text-neutral-500">Las oportunidades y amenazas conservan la procedencia de Perfil competitivo o P.E.S.T.; las fortalezas y debilidades conservan la de Cadena de valor o BCG.</div>
      </div>
      <div class="inline-flex items-center rounded-full border border-neutral-200 bg-neutral-50 px-3 py-1 text-xs font-semibold text-neutral-700">
        Relaciones respondidas: <span id="swot-answered" class="ml-1"><?php echo (int) $answered; ?>/<?php echo (int) $totalCells; ?></span>
      </div>
    </div>

    <?php if (!$ready) : ?>
      <div class="mt-4 rounded-2xl border border-accent-200 bg-accent-50 px-4 py-3 text-sm leading-relaxed text-neutral-800">
        Debes registrar al menos una fortaleza, una debilidad, una oportunidad y una amenaza en los módulos previos antes de evaluar la matriz cruzada.
      </div>
    <?php endif; ?>

    <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
      <?php foreach (['DEBILIDAD', 'AMENAZA', 'FORTALEZA', 'OPORTUNIDAD'] as $groupKey) : ?>
        <?php
          $items = is_array($groups[$groupKey] ?? null) ? (array) $groups[$groupKey] : [];
          $label = $groupLabels[$groupKey] ?? $groupKey;
          $style = $groupStyles[$groupKey] ?? 'border-neutral-200 bg-neutral-50';
        ?>
        <div class="rounded-2xl border <?php echo htmlspecialchars($style, ENT_QUOTES, 'UTF-8'); ?> p-4">
          <div class="flex items-center justify-between gap-3">
            <div class="text-sm font-semibold text-neutral-900"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="rounded-full border border-neutral-200 bg-white px-3 py-1 text-xs font-semibold text-neutral-700">
              <?php echo count($items); ?>
            </div>
          </div>
          <div class="mt-3 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                <tr>
                  <th class="w-16 px-4 py-3">Código</th>
                  <th class="px-4 py-3">Factor</th>
                  <th class="w-40 px-4 py-3">Procedencia</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-neutral-200">
                <?php if (empty($items)) : ?>
                  <tr>
                    <td colspan="3" class="px-4 py-4 text-sm text-neutral-500">Sin registros.</td>
                  </tr>
                <?php else : ?>
                  <?php foreach ($items as $item) : ?>
                    <tr>
                      <td class="px-4 py-3 font-semibold text-neutral-800"><?php echo htmlspecialchars((string) ($item['code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                      <td class="px-4 py-3 text-neutral-800 leading-relaxed"><?php echo htmlspecialchars((string) ($item['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                      <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full border border-neutral-200 bg-neutral-50 px-3 py-1 text-xs font-semibold text-neutral-700">
                          <?php echo htmlspecialchars((string) ($item['source_label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="mt-5 space-y-6">
    <?php foreach (['FO', 'FA', 'DO', 'DA'] as $relation) : ?>
      <?php
        $matrix = is_array($matrices[$relation] ?? null) ? (array) $matrices[$relation] : [];
        $rows = is_array($matrix['rows'] ?? null) ? (array) $matrix['rows'] : [];
        $cols = is_array($matrix['cols'] ?? null) ? (array) $matrix['cols'] : [];
        $cells = is_array($matrix['cells'] ?? null) ? (array) $matrix['cells'] : [];
        $rowTotals = is_array($matrix['row_totals'] ?? null) ? (array) $matrix['row_totals'] : [];
        $colTotals = is_array($matrix['col_totals'] ?? null) ? (array) $matrix['col_totals'] : [];
        $meta = $matrixHeaders[$relation] ?? ['title' => $relation, 'columns' => '', 'rows' => ''];
        $matrixTotal = (int) ($matrix['total'] ?? 0);
        $matrixAnswered = (int) ($matrix['answered'] ?? 0);
        $matrixCellCount = (int) ($matrix['cell_count'] ?? 0);
      ?>
      <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
          <div>
            <div class="text-lg font-semibold text-neutral-900"><?php echo htmlspecialchars((string) $meta['title'], ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="mt-1 text-sm text-neutral-700 leading-relaxed"><?php echo htmlspecialchars((string) $meta['rows'], ENT_QUOTES, 'UTF-8'); ?> vs <?php echo htmlspecialchars((string) $meta['columns'], ENT_QUOTES, 'UTF-8'); ?>. <?php echo htmlspecialchars((string) ($matrix['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="mt-2 text-sm font-medium italic text-neutral-800"><?php echo htmlspecialchars($scoreLegend, ENT_QUOTES, 'UTF-8'); ?></div>
          </div>
          <div class="inline-flex items-center rounded-full border border-neutral-200 bg-neutral-50 px-3 py-1 text-xs font-semibold text-neutral-700">
            Evaluadas: <span data-matrix-answered="<?php echo htmlspecialchars($relation, ENT_QUOTES, 'UTF-8'); ?>" class="ml-1"><?php echo (int) $matrixAnswered; ?>/<?php echo (int) $matrixCellCount; ?></span>
          </div>
        </div>

        <?php if (empty($rows) || empty($cols)) : ?>
          <div class="mt-4 rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-600">
            No hay factores suficientes para construir esta matriz todavía.
          </div>
        <?php else : ?>
          <div class="mt-4 overflow-x-auto overflow-y-visible rounded-2xl border border-neutral-200 bg-white">
            <table class="min-w-[920px] w-full border-separate border-spacing-0 text-sm">
              <thead>
                <tr class="bg-accent-50">
                  <th class="border-b border-neutral-200 px-4 py-3 text-left text-sm font-semibold text-neutral-900"></th>
                  <th colspan="<?php echo count($cols); ?>" class="border-b border-l border-neutral-200 px-4 py-3 text-center text-base font-semibold text-neutral-900">
                    <?php echo htmlspecialchars((string) strtoupper($meta['columns']), ENT_QUOTES, 'UTF-8'); ?>
                  </th>
                  <th class="border-b border-l border-neutral-200 px-4 py-3 text-center text-sm font-semibold text-neutral-900">Total</th>
                </tr>
                <tr class="bg-neutral-50">
                  <th class="border-b border-neutral-200 px-4 py-3 text-left text-sm font-semibold text-neutral-900">Código</th>
                  <?php foreach ($cols as $col) : ?>
                    <?php
                      $colKey = (string) ($col['key'] ?? '');
                      $colCode = (string) ($col['code'] ?? '');
                      $colDesc = (string) ($col['description'] ?? '');
                      $colSource = (string) ($col['source_label'] ?? '');
                    ?>
                    <th
                      class="border-b border-l border-neutral-200 px-3 py-3 text-center text-sm font-semibold text-neutral-900"
                      data-swot-col-header="<?php echo htmlspecialchars($colKey, ENT_QUOTES, 'UTF-8'); ?>"
                      data-swot-col-code="<?php echo htmlspecialchars($colCode, ENT_QUOTES, 'UTF-8'); ?>"
                      data-swot-col-desc="<?php echo htmlspecialchars($colDesc, ENT_QUOTES, 'UTF-8'); ?>"
                    >
                      <div class="relative inline-flex">
                        <button
                          type="button"
                          class="mx-auto flex min-h-[2.5rem] min-w-[4.25rem] items-center justify-center rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm font-semibold text-brand-800 transition hover:border-neutral-300 hover:bg-neutral-50"
                          data-swot-factor-toggle="1"
                          aria-expanded="false"
                        >
                          <?php echo htmlspecialchars($colCode, ENT_QUOTES, 'UTF-8'); ?>
                        </button>
                        <div data-swot-factor-detail class="absolute bottom-full left-1/2 z-30 mb-2 hidden w-64 -translate-x-1/2 rounded-xl border border-neutral-200 bg-white px-3 py-3 text-left text-xs font-medium leading-relaxed text-neutral-700 shadow-lg">
                          <div class="absolute left-1/2 top-full h-3 w-3 -translate-x-1/2 -translate-y-1/2 rotate-45 border-b border-r border-neutral-200 bg-white"></div>
                          <div class="relative"><?php echo htmlspecialchars($colDesc, ENT_QUOTES, 'UTF-8'); ?></div>
                          <div class="relative mt-1 text-[11px] text-neutral-500"><?php echo htmlspecialchars($colSource, ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                      </div>
                    </th>
                  <?php endforeach; ?>
                  <th class="border-b border-l border-neutral-200 px-3 py-3 text-center text-sm font-semibold text-neutral-900">Fila</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-neutral-200">
                <?php foreach ($rows as $row) : ?>
                  <?php
                    $rowKey = (string) ($row['key'] ?? '');
                    $rowCode = (string) ($row['code'] ?? '');
                    $rowDesc = (string) ($row['description'] ?? '');
                    $rowSource = (string) ($row['source_label'] ?? '');
                  ?>
                  <tr data-swot-row="<?php echo htmlspecialchars($rowKey, ENT_QUOTES, 'UTF-8'); ?>">
                    <td class="border-b border-neutral-200 px-4 py-3 text-sm font-semibold text-neutral-900">
                      <div class="relative inline-flex">
                        <button
                          type="button"
                          class="flex min-h-[2.5rem] min-w-[4.25rem] items-center justify-center rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm font-semibold text-brand-800 transition hover:border-neutral-300 hover:bg-neutral-50"
                          data-swot-factor-toggle="1"
                          aria-expanded="false"
                        >
                          <?php echo htmlspecialchars($rowCode, ENT_QUOTES, 'UTF-8'); ?>
                        </button>
                        <div data-swot-factor-detail class="absolute bottom-full left-0 z-30 mb-2 hidden w-64 rounded-xl border border-neutral-200 bg-white px-3 py-3 text-left text-xs font-medium leading-relaxed text-neutral-700 shadow-lg">
                          <div class="absolute left-6 top-full h-3 w-3 -translate-y-1/2 rotate-45 border-b border-r border-neutral-200 bg-white"></div>
                          <div class="relative"><?php echo htmlspecialchars($rowDesc, ENT_QUOTES, 'UTF-8'); ?></div>
                          <div class="relative mt-1 text-[11px] text-neutral-500"><?php echo htmlspecialchars($rowSource, ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                      </div>
                    </td>
                    <?php foreach ($cols as $col) : ?>
                      <?php
                        $colKey = (string) ($col['key'] ?? '');
                        $value = $cells[$rowKey][$colKey] ?? null;
                      ?>
                      <td class="border-b border-l border-neutral-200 px-2 py-2 text-center">
                        <div class="space-y-1">
                          <select
                            class="swot-select h-10 w-full rounded-lg border border-neutral-200 bg-white px-2 text-center text-sm font-semibold text-neutral-800 outline-none focus:border-neutral-300 focus:ring-2 focus:ring-brand-100"
                            data-swot-select="1"
                            data-relation="<?php echo htmlspecialchars($relation, ENT_QUOTES, 'UTF-8'); ?>"
                            data-row-key="<?php echo htmlspecialchars($rowKey, ENT_QUOTES, 'UTF-8'); ?>"
                            data-col-key="<?php echo htmlspecialchars($colKey, ENT_QUOTES, 'UTF-8'); ?>"
                            data-row-code="<?php echo htmlspecialchars($rowCode, ENT_QUOTES, 'UTF-8'); ?>"
                            data-col-code="<?php echo htmlspecialchars((string) ($col['code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                            data-row-desc="<?php echo htmlspecialchars($rowDesc, ENT_QUOTES, 'UTF-8'); ?>"
                            data-col-desc="<?php echo htmlspecialchars((string) ($col['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                          >
                            <option value="" <?php echo $value === null ? 'selected' : ''; ?>>—</option>
                            <?php for ($v = 0; $v <= 4; $v++) : ?>
                              <option value="<?php echo $v; ?>" <?php echo ($value !== null && (int) $value === $v) ? 'selected' : ''; ?>><?php echo $v; ?></option>
                            <?php endfor; ?>
                          </select>
                          <div data-swot-ref class="hidden rounded-lg bg-amber-50 px-2 py-1 text-[11px] font-semibold text-amber-700">Incompleto</div>
                        </div>
                      </td>
                    <?php endforeach; ?>
                    <td class="border-b border-l border-neutral-200 px-4 py-3 text-center text-sm font-semibold text-neutral-900" data-matrix-row-total="<?php echo htmlspecialchars($relation . '|' . $rowKey, ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo (int) ($rowTotals[$rowKey] ?? 0); ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot class="bg-accent-50">
                <tr>
                  <th class="border-l border-neutral-200 px-4 py-3 text-left text-sm font-semibold text-neutral-900">Total</th>
                  <?php foreach ($cols as $col) : ?>
                    <?php $colKey = (string) ($col['key'] ?? ''); ?>
                    <th class="border-l border-neutral-200 px-3 py-3 text-center text-sm font-semibold text-neutral-900" data-matrix-col-total="<?php echo htmlspecialchars($relation . '|' . $colKey, ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo (int) ($colTotals[$colKey] ?? 0); ?>
                    </th>
                  <?php endforeach; ?>
                  <th class="border-l border-neutral-200 px-4 py-3 text-center text-sm font-semibold text-neutral-900" data-matrix-total="<?php echo htmlspecialchars($relation, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo (int) $matrixTotal; ?>
                  </th>
                </tr>
              </tfoot>
            </table>
          </div>

        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="mt-5 rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg font-semibold text-neutral-900">SÍNTESIS DE RESULTADOS</h3>
        <p class="mt-1 text-sm text-neutral-600">La puntuación mayor le indica la estrategia que deberá llevar a cabo.</p>
      </div>
      <button
        id="swot-save"
        type="button"
        class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25"
      >
        Guardar Matriz
      </button>
    </div>

    <div class="mt-4 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
      <table class="min-w-full text-left text-sm">
        <thead class="bg-brand-50 text-xs font-semibold uppercase tracking-wide text-neutral-700">
          <tr>
            <th class="w-24 px-4 py-3">Relaciones</th>
            <th class="w-56 px-4 py-3">Tipología de estrategia</th>
            <th class="w-32 px-4 py-3">Puntuación</th>
            <th class="px-4 py-3">Descripción</th>
          </tr>
        </thead>
        <tbody id="swot-summary-body" class="divide-y divide-neutral-200">
          <?php foreach (['FO', 'FA', 'DA', 'DO'] as $relation) : ?>
            <?php
              $row = null;
              foreach ($summary as $item) {
                if (is_array($item) && (string) ($item['relation'] ?? '') === $relation) {
                  $row = $item;
                  break;
                }
              }
              $isBest = is_array($predominant) && (string) ($predominant['relation'] ?? '') === $relation;
            ?>
            <tr data-summary-row="<?php echo htmlspecialchars($relation, ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $isBest ? 'bg-brand-50/70' : ''; ?>">
              <td class="px-4 py-3 font-semibold text-neutral-900"><?php echo htmlspecialchars($relation, ENT_QUOTES, 'UTF-8'); ?></td>
              <td class="px-4 py-3 text-neutral-800"><?php echo htmlspecialchars((string) ($row['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
              <td class="px-4 py-3">
                <span class="inline-flex min-w-[3rem] items-center justify-center rounded-lg border border-accent-200 bg-accent-50 px-3 py-1 text-sm font-semibold text-neutral-900" data-summary-total="<?php echo htmlspecialchars($relation, ENT_QUOTES, 'UTF-8'); ?>">
                  <?php echo (int) ($row['total'] ?? 0); ?>
                </span>
              </td>
              <td class="px-4 py-3 text-neutral-800" data-summary-description="<?php echo htmlspecialchars($relation, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($summaryDescriptions[$relation] ?? '', ENT_QUOTES, 'UTF-8'); ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-4 hidden grid-cols-1 gap-4 xl:grid-cols-[320px_1fr]">
      <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
        <div class="text-sm font-semibold text-neutral-900">Estrategia predominante</div>
        <div id="swot-predominant-label" class="mt-2 text-lg font-semibold text-brand-700">
          <?php echo $complete && is_array($predominant) ? htmlspecialchars((string) ($predominant['label'] ?? '—'), ENT_QUOTES, 'UTF-8') : '—'; ?>
        </div>
        <div id="swot-predominant-relation" class="mt-1 text-sm font-medium text-neutral-700">
          <?php echo $complete && is_array($predominant) ? htmlspecialchars((string) ($predominant['relation'] ?? ''), ENT_QUOTES, 'UTF-8') : ''; ?>
        </div>
        <div class="mt-3 text-xs font-semibold uppercase tracking-wide text-neutral-500">Puntuación</div>
        <div id="swot-predominant-total" class="mt-1 text-3xl font-semibold text-neutral-900">
          <?php echo $complete && is_array($predominant) ? (int) ($predominant['total'] ?? 0) : 0; ?>
        </div>
      </div>

      <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
        <div class="text-sm font-semibold text-neutral-900">Conclusión ejecutiva</div>
        <div id="swot-conclusion" class="mt-3 whitespace-pre-line text-sm leading-relaxed text-neutral-800">
          <?php echo $complete && $executiveConclusion !== '' ? htmlspecialchars($executiveConclusion, ENT_QUOTES, 'UTF-8') : 'Completa todas las relaciones de la matriz para generar automáticamente la conclusión ejecutiva.'; ?>
        </div>
      </div>
    </div>
  </div>

  <div id="swot-toast" class="pointer-events-none fixed bottom-6 right-6 z-50 hidden w-full max-w-sm">
    <div id="swot-toast-card" class="pointer-events-auto rounded-2xl border border-neutral-200 bg-white p-4 shadow-lg">
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
          <div id="swot-toast-title" class="text-sm font-semibold text-neutral-900"></div>
          <div id="swot-toast-msg" class="mt-1 text-sm text-neutral-700"></div>
        </div>
        <button id="swot-toast-close" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50">
          <span class="sr-only">Cerrar</span>
          <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"></path>
          </svg>
        </button>
      </div>
    </div>
  </div>
</section>
