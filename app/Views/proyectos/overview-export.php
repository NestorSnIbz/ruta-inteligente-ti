<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Overview - Reporte</title>
  <link href="/dist/output.css" rel="stylesheet" />
  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; }
    }
    @page { margin: 14mm; }
  </style>
</head>
<body class="bg-white text-neutral-900">
<?php
  $proyectoNombre = is_array($proyecto ?? null) ? (string) ($proyecto['nombre'] ?? '') : '';
  $misionTexto = is_array($mision ?? null) ? (string) ($mision['descripcion'] ?? '') : (string) ($misionTexto ?? '');
  $visionTexto = is_array($vision ?? null) ? (string) ($vision['descripcion'] ?? '') : (string) ($visionTexto ?? '');
  $valores = is_array($valores ?? null) ? $valores : [];
  $objetivosEstrategicos = is_array($objetivosEstrategicos ?? null) ? $objetivosEstrategicos : [];
  $objetivosEspecificosByEstrategico = is_array($objetivosEspecificosByEstrategico ?? null) ? $objetivosEspecificosByEstrategico : [];

  $cadenaOverview = is_array($cadenaOverview ?? null) ? (array) $cadenaOverview : [];
  $bcgOverview = is_array($bcgOverview ?? null) ? (array) $bcgOverview : [];
  $fodaOverview = is_array($fodaOverview ?? null) ? (array) $fodaOverview : [];

  $cStatus = (string) ($cadenaOverview['status_label'] ?? 'Sin evaluación');
  $cSum = $cadenaOverview['sum'] ?? null;
  $cPotential = $cadenaOverview['potential'] ?? null;
  $cPotentialText = ($cPotential !== null && is_numeric($cPotential)) ? number_format((float) $cPotential, 2, '.', '') : '—';
  $cPotentialPct = ($cPotential !== null && is_numeric($cPotential)) ? ((string) round(((float) $cPotential) * 100) . '%') : '';

  $bTotal = (int) ($bcgOverview['total'] ?? 0);
  $bCounts = is_array($bcgOverview['counts'] ?? null) ? (array) $bcgOverview['counts'] : [];
  $bTop = is_array($bcgOverview['top'] ?? null) ? (array) $bcgOverview['top'] : [];

  $fCadena = is_array($fodaOverview['CADENA_VALOR_INTERNA'] ?? null) ? (array) $fodaOverview['CADENA_VALOR_INTERNA'] : [];
  $fBcg = is_array($fodaOverview['AUTODIAGNOSTICO_BCG'] ?? null) ? (array) $fodaOverview['AUTODIAGNOSTICO_BCG'] : [];
  $fCadenaLabel = (string) ($fCadena['label'] ?? 'Cadena de valor');
  $fBcgLabel = (string) ($fBcg['label'] ?? 'Matriz BCG');
  $fFortCadena = is_array($fCadena['FORTALEZA'] ?? null) ? (array) $fCadena['FORTALEZA'] : [];
  $fDebCadena = is_array($fCadena['DEBILIDAD'] ?? null) ? (array) $fCadena['DEBILIDAD'] : [];
  $fFortBcg = is_array($fBcg['FORTALEZA'] ?? null) ? (array) $fBcg['FORTALEZA'] : [];
  $fDebBcg = is_array($fBcg['DEBILIDAD'] ?? null) ? (array) $fBcg['DEBILIDAD'] : [];
?>

<div class="mx-auto max-w-5xl px-6 py-8">
  <div class="no-print flex flex-wrap items-center justify-between gap-3">
    <div class="text-sm text-neutral-600">Vista de impresión. Usa “Guardar como PDF”.</div>
    <div class="flex items-center gap-2">
      <a href="detalle-proyecto.php?t=<?php echo urlencode((string) ($projectToken ?? '')); ?>&section=overview" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">
        Volver
      </a>
      <button type="button" onclick="window.print()" class="inline-flex h-10 items-center justify-center rounded-xl bg-neutral-900 px-4 text-sm font-semibold text-white hover:bg-neutral-800">
        Imprimir / PDF
      </button>
    </div>
  </div>

  <div class="mt-6 rounded-2xl border border-neutral-200 bg-white p-6">
    <div class="flex items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold">Reporte - Overview</h1>
        <div class="mt-1 text-sm text-neutral-600"><?php echo htmlspecialchars($proyectoNombre, ENT_QUOTES, 'UTF-8'); ?></div>
      </div>
      <div class="text-right text-xs text-neutral-500"><?php echo htmlspecialchars(date('Y-m-d H:i'), ENT_QUOTES, 'UTF-8'); ?></div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4">
      <div class="rounded-2xl border border-neutral-200 bg-white p-5">
        <div class="text-sm font-semibold text-neutral-900">Misión</div>
        <div class="mt-2 text-sm text-neutral-700 leading-relaxed">
          <?php echo $misionTexto !== '' ? nl2br(htmlspecialchars($misionTexto, ENT_QUOTES, 'UTF-8')) : '<span class="text-neutral-600">Sin registros.</span>'; ?>
        </div>
      </div>

      <div class="rounded-2xl border border-neutral-200 bg-white p-5">
        <div class="text-sm font-semibold text-neutral-900">Visión</div>
        <div class="mt-2 text-sm text-neutral-700 leading-relaxed">
          <?php echo $visionTexto !== '' ? nl2br(htmlspecialchars($visionTexto, ENT_QUOTES, 'UTF-8')) : '<span class="text-neutral-600">Sin registros.</span>'; ?>
        </div>
      </div>

      <div class="rounded-2xl border border-neutral-200 bg-white p-5">
        <div class="text-sm font-semibold text-neutral-900">Valores</div>
        <?php if (empty($valores)) : ?>
          <div class="mt-2 text-sm text-neutral-600">Sin registros.</div>
        <?php else : ?>
          <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-neutral-800">
            <?php foreach ($valores as $v) : ?>
              <li class="leading-relaxed"><?php echo htmlspecialchars((string) ($v['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="rounded-2xl border border-neutral-200 bg-white p-5">
        <div class="text-sm font-semibold text-neutral-900">Objetivos</div>
        <?php if (empty($objetivosEstrategicos)) : ?>
          <div class="mt-2 text-sm text-neutral-600">Sin registros.</div>
        <?php else : ?>
          <div class="mt-3 space-y-3">
            <?php foreach ($objetivosEstrategicos as $obj) : ?>
              <?php
                $idObjEst = (int) ($obj['id_objetivo_est'] ?? 0);
                $especificos = $objetivosEspecificosByEstrategico[$idObjEst] ?? [];
                $especificos = is_array($especificos) ? $especificos : [];
              ?>
              <div class="rounded-xl border border-neutral-200 bg-white p-4">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
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
                      <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-neutral-800">
                        <?php foreach ($especificos as $esp) : ?>
                          <li class="leading-relaxed"><?php echo htmlspecialchars((string) ($esp['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></li>
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

      <div class="rounded-2xl border border-neutral-200 bg-white p-5">
        <div class="text-sm font-semibold text-neutral-900">Cadena de Valor (resumen)</div>
        <div class="mt-3 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
          <table class="min-w-full text-left text-sm">
            <tbody class="divide-y divide-neutral-200">
              <tr>
                <td class="px-4 py-3 text-neutral-600">Suma</td>
                <td class="px-4 py-3 text-right font-semibold text-neutral-900"><?php echo ($cSum === null) ? '—' : (int) $cSum; ?></td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-neutral-600">Potencial de mejora</td>
                <td class="px-4 py-3 text-right font-semibold text-neutral-900"><?php echo htmlspecialchars($cPotentialText, ENT_QUOTES, 'UTF-8'); ?><?php echo $cPotentialPct !== '' ? (' (' . htmlspecialchars($cPotentialPct, ENT_QUOTES, 'UTF-8') . ')') : ''; ?></td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-neutral-600">Estado</td>
                <td class="px-4 py-3 text-right font-semibold text-neutral-900"><?php echo htmlspecialchars($cStatus, ENT_QUOTES, 'UTF-8'); ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="rounded-2xl border border-neutral-200 bg-white p-5">
        <div class="text-sm font-semibold text-neutral-900">Matriz BCG (resumen)</div>
        <div class="mt-3 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
          <table class="min-w-full text-left text-sm">
            <thead class="bg-white text-xs font-semibold text-neutral-700">
              <tr class="border-b border-neutral-200">
                <th class="px-4 py-3">Métrica</th>
                <th class="px-4 py-3 text-right">Valor</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200">
              <tr>
                <td class="px-4 py-3 text-neutral-600">Productos</td>
                <td class="px-4 py-3 text-right font-semibold text-neutral-900"><?php echo (int) $bTotal; ?></td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-neutral-600">Estrella</td>
                <td class="px-4 py-3 text-right font-semibold text-neutral-900"><?php echo (int) ($bCounts['ESTRELLA'] ?? 0); ?></td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-neutral-600">Vaca</td>
                <td class="px-4 py-3 text-right font-semibold text-neutral-900"><?php echo (int) ($bCounts['VACA'] ?? 0); ?></td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-neutral-600">Interrogante</td>
                <td class="px-4 py-3 text-right font-semibold text-neutral-900"><?php echo (int) ($bCounts['INTERROGANTE'] ?? 0); ?></td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-neutral-600">Perro</td>
                <td class="px-4 py-3 text-right font-semibold text-neutral-900"><?php echo (int) ($bCounts['PERRO'] ?? 0); ?></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-4">
          <div class="text-sm font-semibold text-neutral-900">Top productos</div>
          <?php if (empty($bTop)) : ?>
            <div class="mt-2 text-sm text-neutral-600">Sin registros.</div>
          <?php else : ?>
            <div class="mt-2 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
              <table class="min-w-full text-left text-sm">
                <thead class="bg-white text-xs font-semibold text-neutral-700">
                  <tr class="border-b border-neutral-200">
                    <th class="px-4 py-3">Producto</th>
                    <th class="px-4 py-3">Clasificación</th>
                    <th class="px-4 py-3 text-right">Ventas</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                  <?php foreach ($bTop as $p) : ?>
                    <?php
                      $pn = trim((string) ($p['nombre'] ?? ''));
                      $pc = trim((string) ($p['clasificacion'] ?? ''));
                      $pp = isset($p['porcentaje_ventas']) && is_numeric($p['porcentaje_ventas']) ? round(((float) $p['porcentaje_ventas']) * 100, 1) . '%' : '—';
                    ?>
                    <tr>
                      <td class="px-4 py-3 font-semibold text-neutral-900"><?php echo htmlspecialchars($pn !== '' ? $pn : 'Producto', ENT_QUOTES, 'UTF-8'); ?></td>
                      <td class="px-4 py-3 text-neutral-700"><?php echo htmlspecialchars($pc !== '' ? $pc : '—', ENT_QUOTES, 'UTF-8'); ?></td>
                      <td class="px-4 py-3 text-right text-neutral-700"><?php echo htmlspecialchars($pp, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="rounded-2xl border border-neutral-200 bg-white p-5">
        <div class="text-sm font-semibold text-neutral-900">FODA (resumen)</div>
        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div class="rounded-2xl border border-neutral-200 bg-white p-4">
            <div class="text-sm font-semibold text-neutral-900">Fortalezas</div>
            <div class="mt-2 space-y-3 text-sm text-neutral-800">
              <div>
                <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fCadenaLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php if (empty($fFortCadena)) : ?>
                  <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                <?php else : ?>
                  <ul class="mt-2 list-disc space-y-1 pl-5">
                    <?php foreach ($fFortCadena as $txt) : ?>
                      <li class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
              <div>
                <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fBcgLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php if (empty($fFortBcg)) : ?>
                  <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                <?php else : ?>
                  <ul class="mt-2 list-disc space-y-1 pl-5">
                    <?php foreach ($fFortBcg as $txt) : ?>
                      <li class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></li>
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
                  <ul class="mt-2 list-disc space-y-1 pl-5">
                    <?php foreach ($fDebCadena as $txt) : ?>
                      <li class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
              <div>
                <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fBcgLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php if (empty($fDebBcg)) : ?>
                  <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                <?php else : ?>
                  <ul class="mt-2 list-disc space-y-1 pl-5">
                    <?php foreach ($fDebBcg as $txt) : ?>
                      <li class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="rounded-2xl border border-neutral-200 bg-white p-4">
            <div class="text-sm font-semibold text-neutral-900">Oportunidades</div>
            <div class="mt-2 text-sm text-neutral-600">Sin registros.</div>
          </div>
          <div class="rounded-2xl border border-neutral-200 bg-white p-4">
            <div class="text-sm font-semibold text-neutral-900">Amenazas</div>
            <div class="mt-2 text-sm text-neutral-600">Sin registros.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
