<?php
  $pestPreguntas = is_array($pestPreguntas ?? null) ? $pestPreguntas : [];
  $pestRespuestas = is_array($pestRespuestas ?? null) ? $pestRespuestas : [];
  $pestCalc = is_array($pestCalc ?? null) ? $pestCalc : [
    'valid' => 0,
    'count' => 0,
    'missing' => 0,
    'pct' => [],
    'conclusions' => [],
  ];

  $catLabels = [
    'SOCIALES' => 'Factores Sociales y Demográficos',
    'MEDIOAMBIENTALES' => 'Factores Medioambientales',
    'POLITICOS' => 'Factores Políticos',
    'ECONOMICOS' => 'Factores Económicos',
    'TECNOLOGICOS' => 'Factores Tecnológicos',
  ];

  $pct = is_array($pestCalc['pct'] ?? null) ? (array) $pestCalc['pct'] : [];
  $conclusions = is_array($pestCalc['conclusions'] ?? null) ? (array) $pestCalc['conclusions'] : [];
  $valid = (int) ($pestCalc['valid'] ?? 0);
  $count = (int) ($pestCalc['count'] ?? 0);
  $missing = (int) ($pestCalc['missing'] ?? 0);

  $pestOportunidades = array_values(array_filter(array_map('trim', array_map('strval', $pestOportunidades ?? []))));
  $pestAmenazas = array_values(array_filter(array_map('trim', array_map('strval', $pestAmenazas ?? []))));

  $barOrder = ['SOCIALES', 'MEDIOAMBIENTALES', 'POLITICOS', 'ECONOMICOS', 'TECNOLOGICOS'];
  $groups = [];
  foreach ($pestPreguntas as $q) {
    if (!is_array($q)) continue;
    $cat = strtoupper(trim((string) ($q['categoria'] ?? '')));
    if (!isset($groups[$cat])) $groups[$cat] = [];
    $groups[$cat][] = $q;
  }

  $calcConclusionText = function (string $cat) use ($conclusions) : array {
    $c = $conclusions[$cat] ?? null;
    if (!is_array($c)) {
      return ['positive' => null, 'text' => '—'];
    }
    $pos = isset($c['positive']) ? (bool) $c['positive'] : null;
    $txt = trim((string) ($c['text'] ?? ''));
    return ['positive' => $pos, 'text' => $txt !== '' ? $txt : '—'];
  };
?>

<section id="panel-pest" class="project-panel bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
  <div class="flex items-center justify-between gap-3">
    <h2 class="text-lg font-semibold">AUTODIAGNÓSTICO ENTORNO GLOBAL P.E.S.T.</h2>
  </div>

  <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
    <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
      <div class="text-sm font-semibold text-neutral-900">Económicos</div>
      <div class="mt-2 text-sm text-neutral-700 leading-relaxed">
        Los factores políticos implican efectos económicos. El comportamiento, la confianza del comprador y su nivel adquisitivo están relacionados con el auge, estancamiento, recesión y recuperación de la economía.
      </div>
      <div class="mt-3 text-sm font-semibold text-neutral-900">Ejemplos:</div>
      <ul class="mt-2 list-disc pl-5 text-sm text-neutral-700 space-y-1">
        <li>Tasas impositivas</li>
        <li>Tasas de interés</li>
        <li>Niveles de deuda</li>
        <li>Niveles de ahorro</li>
        <li>Tasa de empleo</li>
        <li>Índices de precio</li>
      </ul>
    </div>

    <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
      <div class="text-sm font-semibold text-neutral-900">Sociales</div>
      <div class="mt-2 text-sm text-neutral-700 leading-relaxed">
        Se enfoca a las fuerzas que actúan dentro de la sociedad y afectan las actitudes, opiniones e intereses de las personas.
      </div>
      <div class="mt-3 text-sm font-semibold text-neutral-900">Ejemplos:</div>
      <ul class="mt-2 list-disc pl-5 text-sm text-neutral-700 space-y-1">
        <li>Estratos demográficos</li>
        <li>Estilos de vida</li>
        <li>Distribución del ingreso</li>
        <li>Ocio</li>
        <li>Factores étnicos</li>
        <li>Factores religiosos</li>
      </ul>
    </div>

    <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
      <div class="text-sm font-semibold text-neutral-900">Tecnológicos</div>
      <div class="mt-2 text-sm text-neutral-700 leading-relaxed">
        La tecnología es una fuerza impulsora de negocios.
      </div>
      <div class="mt-3 text-sm text-neutral-700 leading-relaxed">
        Permite:
      </div>
      <ul class="mt-2 list-disc pl-5 text-sm text-neutral-700 space-y-1">
        <li>Mejorar la calidad</li>
        <li>Reducir tiempos de comercialización</li>
        <li>Modernizar procesos</li>
        <li>Automatizar operaciones</li>
      </ul>
      <div class="mt-3 text-sm font-semibold text-neutral-900">Ejemplos:</div>
      <ul class="mt-2 list-disc pl-5 text-sm text-neutral-700 space-y-1">
        <li>Obsolescencia tecnológica</li>
        <li>Automatización</li>
        <li>Tecnologías de información</li>
        <li>Incentivos tecnológicos</li>
      </ul>
    </div>
  </div>

  <div class="mt-4 rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
    <div class="flex items-start justify-between gap-3">
      <div>
        <div class="text-sm font-semibold text-neutral-900">Gráfico resumen</div>
        <div class="mt-0.5 text-xs text-neutral-500">Porcentaje = (Puntaje Obtenido / Puntaje Máximo) × 100</div>
      </div>
      <div class="text-xs text-neutral-500">Filas válidas: <span id="pest-valid"><?php echo (int) $valid; ?>/<?php echo (int) $count; ?></span></div>
    </div>

    <div class="mt-4 overflow-x-auto">
      <div class="min-w-[980px]">
        <div class="flex items-stretch gap-4">
          <div class="flex items-center">
            <div class="whitespace-nowrap text-xs font-semibold text-neutral-600 -rotate-90">
              Nivel de impacto de factores generales externos
            </div>
          </div>

          <div class="flex-1">
            <div class="rounded-2xl border border-neutral-200 bg-white p-4">
              <div class="flex items-end justify-between gap-8">
                <?php foreach ($barOrder as $cat) : ?>
                  <?php
                    $p = (int) ($pct[$cat] ?? 0);
                    $p = max(0, min(100, $p));
                    $label = $catLabels[$cat] ?? $cat;
                  ?>
                  <div class="flex w-full flex-col items-center justify-end">
                    <div data-pest-bar-label="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>" class="mb-2 rounded-lg bg-neutral-50 px-2 py-1 text-xs font-semibold text-neutral-800">
                      <?php echo (int) $p; ?>%
                    </div>
                    <div class="h-44 w-10 flex items-end">
                      <div data-pest-bar="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-lg bg-brand-600" style="height: <?php echo (int) $p; ?>%"></div>
                    </div>
                    <div class="mt-3 text-[11px] font-semibold text-neutral-700 text-center leading-tight">
                      <?php echo htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="mt-3 text-center text-xs font-semibold text-neutral-600">
              Tipología de factores generales externos
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-4 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
    <div class="text-sm text-neutral-700 leading-relaxed">
      A continuación marque con una X para valorar su empresa en función de cada una de las afirmaciones, de tal forma que:
      <br />
      0 = En total desacuerdo
      <br />
      1 = No está de acuerdo
      <br />
      2 = Está de acuerdo
      <br />
      3 = Está bastante de acuerdo
      <br />
      4 = En total acuerdo
      <br />
      <br />
      En caso de no cumplimentar una casilla o duplicar su respuesta aparecerá un mensaje de validación.
    </div>
  </div>

  <div class="mt-4 overflow-x-auto rounded-2xl border border-neutral-200 bg-white">
    <form id="pest-form" class="min-w-[1180px]">
      <table class="w-full border-separate border-spacing-0 text-sm">
        <thead class="bg-neutral-100">
          <tr>
            <th class="w-14 border-b border-neutral-200 px-4 py-3 text-center text-xs font-semibold text-neutral-700">#</th>
            <th class="border-b border-l border-neutral-200 px-4 py-3 text-left text-xs font-semibold text-neutral-700">Afirmación</th>
            <th class="w-32 border-b border-l border-neutral-200 px-3 py-3 text-center text-xs font-semibold text-neutral-700">En total desacuerdo (0)</th>
            <th class="w-32 border-b border-l border-neutral-200 px-3 py-3 text-center text-xs font-semibold text-neutral-700">No está de acuerdo (1)</th>
            <th class="w-32 border-b border-l border-neutral-200 px-3 py-3 text-center text-xs font-semibold text-neutral-700">Está de acuerdo (2)</th>
            <th class="w-32 border-b border-l border-neutral-200 px-3 py-3 text-center text-xs font-semibold text-neutral-700">Está bastante de acuerdo (3)</th>
            <th class="w-32 border-b border-l border-neutral-200 px-3 py-3 text-center text-xs font-semibold text-neutral-700">En total acuerdo (4)</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-neutral-200">
          <?php foreach ($barOrder as $cat) : ?>
            <?php
              $label = $catLabels[$cat] ?? $cat;
              $items = $groups[$cat] ?? [];
              if (!is_array($items)) $items = [];
              if (empty($items)) continue;
            ?>
            <tr class="bg-neutral-50">
              <td colspan="7" class="border-b border-neutral-200 px-4 py-2 text-xs font-semibold text-neutral-700">
                <?php echo htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8'); ?>
              </td>
            </tr>
            <?php foreach ($items as $q) : ?>
              <?php
                $qid = (int) ($q['id_pregunta'] ?? 0);
                $n = (int) ($q['numero'] ?? 0);
                $txt = trim((string) ($q['texto'] ?? ''));
                $selected = array_key_exists($qid, $pestRespuestas) ? (int) $pestRespuestas[$qid] : null;
                if ($qid <= 0 || $n <= 0 || $txt === '') continue;
              ?>
              <tr class="pest-row" data-pest-row="<?php echo (int) $qid; ?>" data-pest-cat="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>">
                <td class="border-b border-neutral-200 px-4 py-3 text-center text-xs font-semibold text-neutral-700"><?php echo (int) $n; ?></td>
                <td class="border-b border-l border-neutral-200 px-4 py-3 text-sm text-neutral-800">
                  <div class="flex items-start justify-between gap-3">
                    <div class="leading-relaxed"><?php echo htmlspecialchars($txt, ENT_QUOTES, 'UTF-8'); ?></div>
                    <span data-pest-ref class="hidden rounded-lg bg-red-50 px-2 py-1 text-xs font-semibold text-red-700">#¡REF!</span>
                  </div>
                </td>
                <?php for ($v = 0; $v <= 4; $v++) : ?>
                  <td class="border-b border-l border-neutral-200 px-3 py-2 text-center">
                    <label class="pest-cell flex h-12 w-full cursor-pointer items-center justify-center select-none">
                      <input
                        type="radio"
                        name="pest_q<?php echo (int) $qid; ?>"
                        value="<?php echo (int) $v; ?>"
                        class="sr-only"
                        <?php echo ($selected !== null && (int) $selected === (int) $v) ? 'checked' : ''; ?>
                      />
                      <span class="pest-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-neutral-300 bg-white px-3 text-sm font-semibold text-neutral-700 transition">
                        <?php echo ($selected !== null && (int) $selected === (int) $v) ? 'X' : ''; ?>
                      </span>
                    </label>
                  </td>
                <?php endfor; ?>
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
        <div class="text-sm font-semibold text-neutral-900">Resultados y conclusiones</div>
        <div class="mt-0.5 text-xs text-neutral-500">Cada resultado se calcula sobre 20 puntos (5 preguntas × 4).</div>
      </div>
      <div class="flex items-center gap-2">
        <button
          id="pest-save"
          type="button"
          class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25"
        >
          Guardar Evaluación
        </button>
      </div>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-3 lg:grid-cols-5">
      <?php foreach ($barOrder as $cat) : ?>
        <?php
          $label = $catLabels[$cat] ?? $cat;
          $p = (int) ($pct[$cat] ?? 0);
          $p = max(0, min(100, $p));
          $c = $calcConclusionText($cat);
          $badge = 'bg-neutral-100 text-neutral-700';
          if ($missing === 0 && $c['positive'] === true) $badge = 'bg-emerald-50 text-emerald-800 border border-emerald-200';
          if ($missing === 0 && $c['positive'] === false) $badge = 'bg-amber-50 text-amber-800 border border-amber-200';
        ?>
        <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
          <div class="flex items-start justify-between gap-2">
            <div class="text-xs font-semibold text-neutral-700"><?php echo htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8'); ?></div>
            <span data-pest-badge="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>" class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold <?php echo htmlspecialchars($badge, ENT_QUOTES, 'UTF-8'); ?>">
              <span data-pest-pct="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>"><?php echo (int) $p; ?>%</span>
            </span>
          </div>
          <div data-pest-conclusion="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>" class="mt-3 text-xs text-neutral-700 leading-relaxed"><?php echo htmlspecialchars((string) $c['text'], ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="mt-4 rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <div class="text-sm font-semibold text-neutral-900">FODA</div>
        <div class="mt-0.5 text-xs text-neutral-500">Oportunidades y amenazas obtenidas desde P.E.S.T.</div>
      </div>
      <button
        id="pest-foda-save"
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
          <button id="pest-foda-add-oportunidad" type="button" class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
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
            <tbody id="pest-foda-oportunidades-body" class="divide-y divide-neutral-200">
              <?php
                $opTarget = max(1, count($pestOportunidades));
                for ($i = 0; $i < $opTarget; $i++) :
                  $value = $pestOportunidades[$i] ?? '';
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
          <button id="pest-foda-add-amenaza" type="button" class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
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
            <tbody id="pest-foda-amenazas-body" class="divide-y divide-neutral-200">
              <?php
                $amTarget = max(1, count($pestAmenazas));
                for ($i = 0; $i < $amTarget; $i++) :
                  $value = $pestAmenazas[$i] ?? '';
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

  <div id="pest-toast" class="pointer-events-none fixed bottom-6 right-6 z-50 hidden w-full max-w-sm">
    <div id="pest-toast-card" class="pointer-events-auto rounded-2xl border border-neutral-200 bg-white p-4 shadow-lg">
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
          <div id="pest-toast-title" class="text-sm font-semibold text-neutral-900"></div>
          <div id="pest-toast-msg" class="mt-1 text-sm text-neutral-700"></div>
        </div>
        <button id="pest-toast-close" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50">
          <span class="sr-only">Cerrar</span>
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</section>
