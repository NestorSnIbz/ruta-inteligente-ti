<?php
  $cameAcciones = is_array($cameAcciones ?? null) ? (array) $cameAcciones : ['C' => [], 'A' => [], 'M' => [], 'E' => []];
  $cameCalc = is_array($cameCalc ?? null) ? (array) $cameCalc : [];
  $cameFactors = is_array($cameFactors ?? null) ? (array) $cameFactors : ['groups' => []];

  $totalActions = (int) ($cameCalc['total_actions'] ?? 0);
  $categoriesUsed = (int) ($cameCalc['categories_used'] ?? 0);
  $factorGroups = is_array($cameFactors['groups'] ?? null) ? (array) $cameFactors['groups'] : [];

  $cats = [
    'C' => ['title' => 'Corregir las debilidades', 'factor_type' => 'DEBILIDAD', 'factor_title' => 'Debilidades'],
    'A' => ['title' => 'Afrontar las amenazas', 'factor_type' => 'AMENAZA', 'factor_title' => 'Amenazas'],
    'M' => ['title' => 'Mantener las fortalezas', 'factor_type' => 'FORTALEZA', 'factor_title' => 'Fortalezas'],
    'E' => ['title' => 'Explotar las oportunidades', 'factor_type' => 'OPORTUNIDAD', 'factor_title' => 'Oportunidades'],
  ];
?>

<section
  id="panel-came"
  class="project-panel bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm"
  data-came-panel="1"
>
  <div class="flex flex-wrap items-center justify-between gap-3">
    <h2 class="text-lg font-semibold">Matriz CAME</h2>
    <div class="inline-flex items-center rounded-full border border-neutral-200 bg-neutral-50 px-3 py-1 text-xs font-semibold text-neutral-700">
      Acciones registradas: <span id="came-count" class="ml-1"><?php echo (int) $totalActions; ?></span>
      <span class="mx-2 text-neutral-300">|</span>
      Categorías utilizadas: <span id="came-categories-used" class="ml-1"><?php echo (int) $categoriesUsed; ?>/4</span>
    </div>
  </div>

  <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
    <div class="text-sm text-neutral-700 leading-relaxed">
      Define acciones concretas para corregir debilidades, afrontar amenazas, mantener fortalezas y explotar oportunidades. No es obligatorio completar todos los apartados: puedes guardar la matriz con solo las acciones que consideres necesarias.
    </div>
  </div>

  <div class="mt-5 space-y-5">
    <?php foreach ($cats as $cat => $meta) : ?>
      <?php
        $title = (string) ($meta['title'] ?? '');
        $factorType = (string) ($meta['factor_type'] ?? '');
        $factorTitle = (string) ($meta['factor_title'] ?? '');
        $rows = is_array($cameAcciones[$cat] ?? null) ? (array) $cameAcciones[$cat] : [];
        $factorRows = is_array($factorGroups[$factorType] ?? null) ? (array) $factorGroups[$factorType] : [];
      ?>
      <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm" data-came-category-block="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600 text-sm font-bold text-white"><?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?></span>
            <div>
              <div class="text-sm font-semibold text-neutral-900"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></div>
              <div class="mt-0.5 text-xs text-neutral-500">Registra solo las acciones que realmente planeas ejecutar en este bloque.</div>
            </div>
          </div>
          <button
            type="button"
            class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50"
            data-came-add="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>"
          >
            Agregar acción
          </button>
        </div>
        <div class="mt-4 overflow-x-auto">
          <div class="flex w-full min-w-[980px] items-start gap-4">
          <div class="min-w-0 flex-1 rounded-2xl border border-neutral-200 bg-white">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-neutral-100">
                <tr>
                  <th class="w-20 px-4 py-3 text-center text-xs font-semibold text-neutral-700">#</th>
                  <th class="px-4 py-3 text-xs font-semibold text-neutral-700">Acción</th>
                  <th class="w-24 px-4 py-3 text-right text-xs font-semibold text-neutral-700">Acción</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-neutral-200" data-came-category="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (empty($rows)) : ?>
                  <tr data-came-empty-row="1">
                    <td colspan="3" class="px-4 py-4 text-sm text-neutral-500">No hay acciones registradas en este apartado.</td>
                  </tr>
                <?php else : ?>
                  <?php foreach ($rows as $index => $row) : ?>
                    <?php
                      $pos = (int) ($row['position'] ?? ($index + 1));
                      $value = (string) ($row['description'] ?? '');
                    ?>
                    <tr data-came-row="1">
                      <td class="px-4 py-3 text-center text-xs font-semibold text-neutral-700" data-came-index><?php echo (int) $pos; ?></td>
                      <td class="px-4 py-2">
                        <input
                          type="text"
                          value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"
                          placeholder="Escribe una acción…"
                          class="came-input h-10 w-full rounded-xl border border-neutral-200 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-neutral-300 focus:ring-2 focus:ring-brand-100"
                          data-came-input="1"
                          data-came-category-input="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>"
                        />
                      </td>
                      <td class="px-4 py-2 text-right">
                        <button type="button" class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50" data-came-remove="1">
                          Quitar
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <aside class="flex-none rounded-2xl border border-neutral-200 bg-neutral-50 p-4" style="width: 700px; min-width: 700px; max-width: 700px; flex-basis: 700px;">
            <div class="text-sm font-semibold text-neutral-900"><?php echo htmlspecialchars($factorTitle, ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="mt-0.5 text-xs text-neutral-500">Factores registrados para este bloque.</div>
            <?php if (empty($factorRows)) : ?>
              <div class="mt-3 text-sm text-neutral-500">Sin elementos registrados.</div>
            <?php else : ?>
              <div class="mt-3 space-y-3">
                <?php foreach ($factorRows as $row) : ?>
                  <?php
                    $code = (string) ($row['code'] ?? '');
                    $desc = (string) ($row['description'] ?? '');
                    $source = (string) ($row['source_label'] ?? '');
                  ?>
                  <div class="text-sm text-neutral-700 leading-relaxed">
                    <span class="font-semibold text-brand-800"><?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>.</span>
                    <?php echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'); ?>
                    <div class="mt-1 text-[11px] text-neutral-500">Proviene de: <?php echo htmlspecialchars($source, ENT_QUOTES, 'UTF-8'); ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </aside>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="mt-5 flex flex-wrap items-center justify-end gap-2">
    <button
      id="came-save"
      type="button"
      class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25"
    >
      Guardar Matriz
    </button>
  </div>

  <div id="came-toast" class="pointer-events-none fixed bottom-6 right-6 z-50 hidden w-full max-w-sm">
    <div id="came-toast-card" class="pointer-events-auto rounded-2xl border border-neutral-200 bg-white p-4 shadow-lg">
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
          <div id="came-toast-title" class="text-sm font-semibold text-neutral-900"></div>
          <div id="came-toast-msg" class="mt-1 text-sm text-neutral-700"></div>
        </div>
        <button id="came-toast-close" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50">
          <span class="sr-only">Cerrar</span>
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</section>
