<?php
  $perfilFactores = is_array($perfilFactores ?? null) ? $perfilFactores : [];
  $perfilRespuestas = is_array($perfilRespuestas ?? null) ? $perfilRespuestas : [];
  $perfilCalc = is_array($perfilCalc ?? null) ? $perfilCalc : [
    'total' => 0,
    'valid' => 0,
    'count' => 0,
    'missing' => 0,
    'conclusion_text' => null,
    'conclusion_code' => null,
  ];

  $labels = [
    'RIVALIDAD_EMPRESAS_DEL_SECTOR' => 'Rivalidad empresas del sector',
    'BARRERAS_DE_ENTRADA' => 'Barreras de entrada',
    'PODER_DE_LOS_CLIENTES' => 'Poder de los clientes',
    'PRODUCTOS_SUSTITUTIVOS' => 'Productos sustitutivos',
  ];

  $groups = [];
  foreach ($perfilFactores as $f) {
    if (!is_array($f)) continue;
    $cat = (string) ($f['categoria'] ?? '');
    if ($cat === '') continue;
    if (!isset($groups[$cat])) $groups[$cat] = [];
    $groups[$cat][] = $f;
  }

  $calcTotal = (int) ($perfilCalc['total'] ?? 0);
  $calcValid = (int) ($perfilCalc['valid'] ?? 0);
  $calcCount = (int) ($perfilCalc['count'] ?? 0);
  $calcMissing = (int) ($perfilCalc['missing'] ?? 0);
  $calcConclusion = $perfilCalc['conclusion_text'] ?? null;
  $calcConclusionText = ($calcMissing > 0 || $calcConclusion === null) ? '—' : (string) $calcConclusion;

  $pcOportunidades = array_values(array_filter(array_map('trim', array_map('strval', $pcOportunidades ?? []))));
  $pcAmenazas = array_values(array_filter(array_map('trim', array_map('strval', $pcAmenazas ?? []))));
?>

<section id="panel-perfil_competitivo" class="project-panel bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
  <div class="flex items-center justify-between gap-3">
    <h2 class="text-lg font-semibold">Perfil Competitivo (Análisis del Entorno Próximo)</h2>
  </div>

  <div class="mt-4 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
    <div class="text-sm text-neutral-700 leading-relaxed">
      A continuación marque con una X en las casillas que estime conveniente según el estado actual de su empresa. Valore su perfil competitivo en la escala Hostil-Favorable. Al finalizar lea la conclusión, para su caso particular, relativa al análisis del entorno próximo.
    </div>
    <div class="mt-2 text-xs text-neutral-500">Opciones: Nada (0) · Poco (1) · Medio (2) · Alto (3) · Muy Alto (4)</div>
  </div>

  <div class="mt-4 overflow-x-auto rounded-2xl border border-neutral-200 bg-white">
    <form id="pc-form" class="min-w-[1180px]">
      <table class="w-full border-separate border-spacing-0 text-sm">
        <thead class="bg-neutral-100">
          <tr>
            <th class="border-b border-neutral-200 px-4 py-3 text-left font-semibold text-neutral-900">Perfil Competitivo</th>
            <th class="w-40 border-b border-l border-neutral-200 px-4 py-3 text-center font-semibold text-red-700">Hostil</th>
            <th class="w-24 border-b border-l border-neutral-200 px-3 py-3 text-center font-semibold text-neutral-900">Nada</th>
            <th class="w-24 border-b border-l border-neutral-200 px-3 py-3 text-center font-semibold text-neutral-900">Poco</th>
            <th class="w-24 border-b border-l border-neutral-200 px-3 py-3 text-center font-semibold text-neutral-900">Medio</th>
            <th class="w-24 border-b border-l border-neutral-200 px-3 py-3 text-center font-semibold text-neutral-900">Alto</th>
            <th class="w-24 border-b border-l border-neutral-200 px-3 py-3 text-center font-semibold text-neutral-900">Muy Alto</th>
            <th class="w-40 border-b border-l border-neutral-200 px-4 py-3 text-center font-semibold text-emerald-700">Favorable</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-neutral-200">
          <?php foreach ($groups as $cat => $items) : ?>
            <?php
              $catLabel = $labels[$cat] ?? $cat;
            ?>
            <tr class="bg-neutral-50">
              <td colspan="8" class="border-b border-neutral-200 px-4 py-2 text-xs font-semibold text-neutral-700">
                <?php echo htmlspecialchars((string) $catLabel, ENT_QUOTES, 'UTF-8'); ?>
              </td>
            </tr>

            <?php foreach ($items as $f) : ?>
              <?php
                $id = (int) ($f['id_factor'] ?? 0);
                $nombre = trim((string) ($f['nombre_factor'] ?? ''));
                $hostil = trim((string) ($f['hostil_label'] ?? ''));
                $fav = trim((string) ($f['favorable_label'] ?? ''));
                $selected = array_key_exists($id, $perfilRespuestas) ? (int) $perfilRespuestas[$id] : null;
                if ($id <= 0 || $nombre === '') continue;
              ?>
              <tr class="pc-row" data-pc-row="<?php echo (int) $id; ?>">
                <td class="border-b border-neutral-200 px-4 py-3 text-sm text-neutral-800">
                  <div class="flex items-start justify-between gap-3">
                    <div class="leading-relaxed"><?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?></div>
                    <span data-pc-ref class="hidden rounded-lg bg-red-50 px-2 py-1 text-xs font-semibold text-red-700">#¡REF!</span>
                  </div>
                </td>
                <td class="border-b border-l border-neutral-200 px-4 py-3 text-center text-sm font-semibold text-red-700">
                  <?php echo htmlspecialchars($hostil, ENT_QUOTES, 'UTF-8'); ?>
                </td>
                <?php for ($v = 0; $v <= 4; $v++) : ?>
                  <td class="border-b border-l border-neutral-200 px-3 py-2 text-center">
                    <label class="pc-cell flex h-12 w-full cursor-pointer items-center justify-center select-none">
                      <input
                        type="radio"
                        name="pc_f<?php echo (int) $id; ?>"
                        value="<?php echo (int) $v; ?>"
                        class="sr-only"
                        <?php echo ($selected !== null && (int) $selected === (int) $v) ? 'checked' : ''; ?>
                      />
                      <span class="pc-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-neutral-300 bg-white px-3 text-sm font-semibold text-neutral-700 transition">
                        <?php echo ($selected !== null && (int) $selected === (int) $v) ? 'X' : ''; ?>
                      </span>
                    </label>
                  </td>
                <?php endfor; ?>
                <td class="border-b border-l border-neutral-200 px-4 py-3 text-center text-sm font-semibold text-emerald-700">
                  <?php echo htmlspecialchars($fav, ENT_QUOTES, 'UTF-8'); ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </form>
  </div>

  <div class="mt-4 rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="text-sm font-semibold text-neutral-900">Resultado</div>
        <div class="mt-0.5 text-xs text-neutral-500">El total y la conclusión se recalculan automáticamente.</div>
      </div>
      <div class="flex items-center gap-2">
        <button
          id="pc-save"
          type="button"
          class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25"
        >
          Guardar Evaluación
        </button>
      </div>
    </div>

    <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
      <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
        <div class="text-xs font-medium text-neutral-600">Total</div>
        <div id="pc-total" class="mt-1 text-2xl font-semibold text-neutral-900"><?php echo (int) $calcTotal; ?></div>
      </div>
      <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
        <div class="text-xs font-medium text-neutral-600">Filas válidas</div>
        <div id="pc-valid" class="mt-1 text-2xl font-semibold text-neutral-900"><?php echo (int) $calcValid; ?>/<?php echo (int) $calcCount; ?></div>
      </div>
      <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
        <div class="text-xs font-medium text-neutral-600">Conclusión</div>
        <div id="pc-conclusion" class="mt-1 text-sm font-semibold text-neutral-900 leading-relaxed"><?php echo htmlspecialchars($calcConclusionText, ENT_QUOTES, 'UTF-8'); ?></div>
      </div>
    </div>
  </div>

  <div class="mt-4 rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <div class="text-sm font-semibold text-neutral-900">FODA</div>
        <div class="mt-0.5 text-xs text-neutral-500">Oportunidades y amenazas obtenidas desde Perfil competitivo.</div>
      </div>
      <button
        id="pc-foda-save"
        type="button"
        class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25"
      >
        Guardar FODA
      </button>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
      <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
        <div class="flex items-center justify-between gap-3">
          <div class="text-sm font-semibold text-neutral-900">Oportunidades</div>
          <button id="pc-foda-add-oportunidad" type="button" class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
            Agregar
          </button>
        </div>
        <div class="mt-3 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
          <table class="min-w-full text-left text-sm">
            <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
              <tr>
                <th scope="col" class="w-14 px-4 py-3 text-center">#</th>
                <th scope="col" class="px-4 py-3">Descripción</th>
                <th scope="col" class="w-24 px-4 py-3 text-right">Acción</th>
              </tr>
            </thead>
            <tbody id="pc-foda-oportunidades-body" class="divide-y divide-neutral-200">
              <?php
                $opTarget = max(1, count($pcOportunidades));
                for ($i = 0; $i < $opTarget; $i++) :
                  $value = $pcOportunidades[$i] ?? '';
              ?>
                <tr data-foda-row="OPORTUNIDAD">
                  <td class="px-4 py-3 text-center text-xs font-semibold text-neutral-600"><?php echo $i + 1; ?></td>
                  <td class="px-4 py-2">
                    <input type="text" value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" class="foda-input h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200" />
                  </td>
                  <td class="px-4 py-2 text-right">
                    <button type="button" class="foda-remove inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
                      Quitar
                    </button>
                  </td>
                </tr>
              <?php endfor; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
        <div class="flex items-center justify-between gap-3">
          <div class="text-sm font-semibold text-neutral-900">Amenazas</div>
          <button id="pc-foda-add-amenaza" type="button" class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
            Agregar
          </button>
        </div>
        <div class="mt-3 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
          <table class="min-w-full text-left text-sm">
            <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
              <tr>
                <th scope="col" class="w-14 px-4 py-3 text-center">#</th>
                <th scope="col" class="px-4 py-3">Descripción</th>
                <th scope="col" class="w-24 px-4 py-3 text-right">Acción</th>
              </tr>
            </thead>
            <tbody id="pc-foda-amenazas-body" class="divide-y divide-neutral-200">
              <?php
                $amTarget = max(1, count($pcAmenazas));
                for ($i = 0; $i < $amTarget; $i++) :
                  $value = $pcAmenazas[$i] ?? '';
              ?>
                <tr data-foda-row="AMENAZA">
                  <td class="px-4 py-3 text-center text-xs font-semibold text-neutral-600"><?php echo $i + 1; ?></td>
                  <td class="px-4 py-2">
                    <input type="text" value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" class="foda-input h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200" />
                  </td>
                  <td class="px-4 py-2 text-right">
                    <button type="button" class="foda-remove inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
                      Quitar
                    </button>
                  </td>
                </tr>
              <?php endfor; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div id="pc-toast" class="pointer-events-none fixed bottom-6 right-6 z-50 hidden w-full max-w-sm">
    <div id="pc-toast-card" class="pointer-events-auto rounded-2xl border border-neutral-200 bg-white p-4 shadow-lg">
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
          <div id="pc-toast-title" class="text-sm font-semibold text-neutral-900"></div>
          <div id="pc-toast-msg" class="mt-1 text-sm text-neutral-700"></div>
        </div>
        <button id="pc-toast-close" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50">
          <span class="sr-only">Cerrar</span>
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</section>
