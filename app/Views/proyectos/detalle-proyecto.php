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
?>

<div class="min-h-screen grid grid-cols-1 md:grid-cols-[16rem_1fr]">

  <!-- SIDEBAR -->
  <aside class="bg-brand-900 text-white">
    <div class="px-6 py-6">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-white/10 grid place-items-center">
          <span class="text-sm font-semibold">RI</span>
        </div>

        <div>
          <div class="text-sm font-semibold">
            Ruta Inteligente TI
          </div>
          <div class="text-xs text-white/70">
            Panel de control
          </div>
        </div>
      </div>
    </div>

    <nav class="px-3 pb-6">
      <a href="dashboard.php"
         class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
        Dashboard
      </a>

      <a href="proyectos.php"
         class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium bg-white/10">
        Proyectos
      </a>

      <a href="configuracion.php"
         class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
        Configuración
      </a>
    </nav>
  </aside>

  <!-- MAIN -->
  <div class="flex flex-col">

    <!-- HEADER -->
    <header class="bg-white border-b border-neutral-200">
      <div class="px-6 py-4 flex items-center justify-between">

        <div>
          <h1 class="text-2xl font-semibold tracking-tight">
            <?php echo htmlspecialchars($proyectoNombre, ENT_QUOTES, 'UTF-8'); ?>
          </h1>

          <p class="text-sm text-neutral-600 mt-1">
            Panel estratégico: Misión, Visión y Valores.
          </p>
        </div>
        <a href="proyectos.php" class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100">
          Volver
        </a>

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
          <div id="flash-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

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
            BGG
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
              <?php if (empty($objetivosEstrategicos)) : ?>
                <div class="mt-3 text-sm text-neutral-600">Aún no se registraron objetivos estratégicos.</div>
              <?php else : ?>
                <div class="mt-4 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
                  <table class="min-w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                      <tr>
                        <th scope="col" class="px-4 py-3">Objetivo estratégico</th>
                        <th scope="col" class="w-40 px-4 py-3 text-right">Específicos</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                      <?php foreach ($objetivosEstrategicos as $obj) : ?>
                        <tr>
                          <td class="px-4 py-3 text-neutral-800">
                            <?php echo htmlspecialchars((string) ($obj['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                          </td>
                          <td class="px-4 py-3 text-right text-neutral-800">
                            <?php echo (int) ($obj['especificos_count'] ?? 0); ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </section>

        <section id="panel-mision" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold">Misión</h2>
              <p class="mt-1 text-sm text-neutral-600">
                Define la razón de ser del proyecto.
              </p>
            </div>
          </div>

          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <form class="space-y-4" method="post" action="detalle-proyecto.php">
              <input type="hidden" name="action" value="save_mision" />
              <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />

              <textarea
                name="descripcion"
                rows="10"
                class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-4 text-sm leading-relaxed outline-none resize-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                placeholder="Escribe la misión del proyecto..."
                required
              ><?php echo htmlspecialchars($misionTexto, ENT_QUOTES, 'UTF-8'); ?></textarea>

              <div class="flex justify-end gap-3">
                <button
                  type="reset"
                  class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100"
                >
                  Limpiar
                </button>

                <button
                  type="submit"
                  class="rounded-xl bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700"
                >
                  Guardar cambios
                </button>
              </div>
            </form>
          </div>
        </section>

        <section id="panel-vision" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold">Visión</h2>
              <p class="mt-1 text-sm text-neutral-600">
                Define hacia dónde se dirige el proyecto.
              </p>
            </div>
          </div>

          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <form class="space-y-4" method="post" action="detalle-proyecto.php">
              <input type="hidden" name="action" value="save_vision" />
              <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />

              <textarea
                name="descripcion"
                rows="10"
                class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-4 text-sm leading-relaxed outline-none resize-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                placeholder="Escribe la visión del proyecto..."
                required
              ><?php echo htmlspecialchars($visionTexto, ENT_QUOTES, 'UTF-8'); ?></textarea>

              <div class="flex justify-end gap-3">
                <button
                  type="reset"
                  class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100"
                >
                  Limpiar
                </button>

                <button
                  type="submit"
                  class="rounded-xl bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700"
                >
                  Guardar cambios
                </button>
              </div>
            </form>
          </div>
        </section>

        <section id="panel-valores" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Valores</h2>
            <a
              data-js-edit-valores="1"
              href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&edit=valores"
              class="<?php echo $edit === 'valores' ? 'hidden' : 'inline-flex'; ?> items-center justify-center rounded-xl border border-neutral-200 bg-white p-2 text-brand-700 hover:bg-brand-50"
              aria-label="Editar valores"
              title="Editar"
            >
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11 4h-4a2 2 0 00-2 2v4m14-4l-9 9-4 1 1-4 9-9 3 3z" />
                </svg>
            </a>
          </div>

          <div id="valores-editor" class="<?php echo $edit === 'valores' ? 'block' : 'hidden'; ?> mt-4 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
              <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                  <div class="text-sm font-semibold text-neutral-900">Editor de valores</div>
                  <div class="mt-0.5 text-xs text-neutral-600">Agrega o elimina valores y luego guarda.</div>
                </div>
              </div>

              <form id="valores-form" class="mt-4 space-y-4" method="post" action="detalle-proyecto.php">
                <input type="hidden" name="action" value="save_valores" />
                <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />

                <div class="flex flex-col gap-3 sm:flex-row">
                  <input
                    id="nuevo-valor"
                    type="text"
                    class="flex-1 rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                    placeholder="Escribe un valor (ej: Innovación)"
                  />
                  <button
                    id="agregar-valor"
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700"
                  >
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md bg-white/15 text-base leading-none">+</span>
                    Agregar
                  </button>
                </div>

                <div id="valores-lista" class="space-y-2">
                  <?php foreach ($valores as $valor) : ?>
                    <div class="flex items-center gap-3 rounded-xl border border-neutral-200 bg-white px-4 py-3">
                      <input type="hidden" name="valores[]" value="<?php echo htmlspecialchars((string) ($valor['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" />
                      <div class="flex-1 text-sm text-neutral-800">
                        <?php echo htmlspecialchars((string) ($valor['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                      <button type="button" class="quitar-valor inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-red-700">
                        Eliminar
                      </button>
                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="flex justify-end gap-3">
                  <a
                    data-js-cancel-valores="1"
                    href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>"
                    class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100"
                  >
                    Cancelar
                  </a>
                  <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                    Guardar cambios
                  </button>
                </div>
              </form>
            </div>

          <div id="valores-display" class="<?php echo $edit === 'valores' ? 'hidden' : 'block'; ?>">
            <?php if (empty($valores)) : ?>
              <p class="mt-4 text-sm text-neutral-600">Aún no se registraron valores. Presiona el lápiz para agregarlos.</p>
            <?php else : ?>
              <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                <?php foreach ($valores as $valor) : ?>
                  <div class="rounded-xl border border-neutral-200 bg-white px-4 py-3">
                    <div class="flex items-start justify-between gap-3">
                      <div class="text-sm text-neutral-800">
                        <?php echo htmlspecialchars((string) ($valor['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </section>

        <section id="panel-objetivos" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Objetivos</h2>
          </div>
          <?php if ($objetivosError !== '') : ?>
            <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
              <?php echo htmlspecialchars($objetivosError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
          <?php endif; ?>

          <form class="mt-6 rounded-2xl border border-neutral-200 bg-white p-5" method="post" action="detalle-proyecto.php">
            <input type="hidden" name="action" value="create_obj_est" />
            <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />

            <div class="flex items-center justify-between gap-3">
              <div>
                <div class="text-sm font-semibold text-neutral-900">Nuevo objetivo estratégico</div>
                <div class="mt-0.5 text-xs text-neutral-600">Define el objetivo estratégico del proyecto.</div>
              </div>
            </div>

            <textarea
              name="descripcion"
              rows="4"
              class="mt-4 w-full rounded-xl border border-neutral-300 px-4 py-3 text-sm outline-none resize-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
              placeholder="Escribe el objetivo estratégico..."
              required
            ></textarea>

            <div class="mt-4 flex justify-end">
              <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                + Agregar
              </button>
            </div>
          </form>

          <div class="mt-6 space-y-4">
            <?php if (empty($objetivosEstrategicos)) : ?>
              <div class="rounded-2xl border border-neutral-200 bg-neutral-50 px-5 py-4 text-sm text-neutral-700">
                Aún no hay objetivos estratégicos registrados.
              </div>
            <?php else : ?>
              <?php foreach ($objetivosEstrategicos as $obj) : ?>
                <?php
                  $oeToken = (string) ($obj['token'] ?? '');
                  $idObjEst = (int) ($obj['id_objetivo_est'] ?? 0);
                  $especificosCount = (int) ($obj['especificos_count'] ?? 0);
                  $especificos = $objetivosEspecificosByEstrategico[$idObjEst] ?? [];
                ?>
                <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <div class="flex flex-wrap items-center gap-2">
                        <div class="text-sm font-semibold text-neutral-900">Objetivo estratégico</div>
                        <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-800">
                          <?php echo (int) $especificosCount; ?> específicos
                        </span>
                      </div>
                    </div>

                    <div class="flex items-center gap-2">
                      <a
                        data-js-edit-oe="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>"
                        href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&section=objetivos&oe_edit=<?php echo urlencode($oeToken); ?>"
                        class="inline-flex items-center justify-center rounded-xl border border-neutral-200 bg-white p-2 text-brand-700 hover:bg-brand-50"
                        aria-label="Editar objetivo estratégico"
                        title="Editar"
                      >
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M11 4h-4a2 2 0 00-2 2v4m14-4l-9 9-4 1 1-4 9-9 3 3z" />
                        </svg>
                      </a>
                      <form method="post" action="detalle-proyecto.php" onsubmit="return confirm('¿Eliminar este objetivo estratégico y todos sus objetivos específicos?');">
                        <input type="hidden" name="action" value="delete_obj_est" />
                        <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
                        <input type="hidden" name="oe" value="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>" />
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                          Eliminar
                        </button>
                      </form>
                    </div>
                  </div>

                  <div data-oe-card="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <div data-oe-view class="<?php echo ($oeEditToken !== '' && hash_equals($oeEditToken, $oeToken)) ? 'hidden' : 'block'; ?> mt-4 text-sm text-neutral-700 leading-relaxed">
                      <?php echo nl2br(htmlspecialchars((string) ($obj['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8')); ?>
                    </div>

                    <div data-oe-form class="<?php echo ($oeEditToken !== '' && hash_equals($oeEditToken, $oeToken)) ? 'block' : 'hidden'; ?> mt-4">
                      <form class="space-y-3" method="post" action="detalle-proyecto.php">
                        <input type="hidden" name="action" value="update_obj_est" />
                        <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
                        <input type="hidden" name="oe" value="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>" />
                        <textarea
                          name="descripcion"
                          rows="4"
                          class="w-full rounded-xl border border-neutral-300 px-4 py-3 text-sm outline-none resize-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                          required
                        ><?php echo htmlspecialchars((string) ($obj['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <div class="flex justify-end gap-3">
                          <a
                            data-js-cancel-oe="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>"
                            href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&section=objetivos"
                            class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100"
                          >
                            Cancelar
                          </a>
                          <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                            Guardar
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>

                  <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                      <div>
                        <div class="text-sm font-semibold text-neutral-900">Objetivos específicos</div>
                        <div class="mt-0.5 text-xs text-neutral-600">Cada objetivo específico pertenece a este objetivo estratégico.</div>
                      </div>
                    </div>

                    <form class="mt-4 flex flex-col gap-3 sm:flex-row" method="post" action="detalle-proyecto.php">
                      <input type="hidden" name="action" value="create_obj_esp" />
                      <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
                      <input type="hidden" name="oe" value="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>" />
                      <input
                        type="text"
                        name="descripcion"
                        class="flex-1 rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                        placeholder="Escribe un objetivo específico..."
                        required
                      />
                      <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                        + Agregar
                      </button>
                    </form>

                    <?php if (empty($especificos)) : ?>
                      <div class="mt-4 text-sm text-neutral-600">Aún no hay objetivos específicos registrados.</div>
                    <?php else : ?>
                      <div class="mt-4 space-y-2">
                        <?php foreach ($especificos as $esp) : ?>
                          <?php $oespToken = (string) ($esp['token'] ?? ''); ?>
                          <div class="rounded-xl border border-neutral-200 bg-white px-4 py-3">
                            <div data-oesp-row="<?php echo htmlspecialchars($oespToken, ENT_QUOTES, 'UTF-8'); ?>">
                              <div data-oesp-view class="<?php echo ($oespEditToken !== '' && hash_equals($oespEditToken, $oespToken)) ? 'hidden' : 'flex'; ?> items-start justify-between gap-3">
                                <div class="text-sm text-neutral-800">
                                  <?php echo htmlspecialchars((string) ($esp['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                                <div class="flex items-center gap-2">
                                  <a
                                    data-js-edit-oesp="<?php echo htmlspecialchars($oespToken, ENT_QUOTES, 'UTF-8'); ?>"
                                    href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&section=objetivos&oesp_edit=<?php echo urlencode($oespToken); ?>"
                                    class="inline-flex items-center justify-center rounded-xl border border-neutral-200 bg-white p-2 text-brand-700 hover:bg-brand-50"
                                    aria-label="Editar objetivo específico"
                                    title="Editar"
                                  >
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M11 4h-4a2 2 0 00-2 2v4m14-4l-9 9-4 1 1-4 9-9 3 3z" />
                                    </svg>
                                  </a>
                                  <form method="post" action="detalle-proyecto.php" onsubmit="return confirm('¿Eliminar este objetivo específico?');">
                                    <input type="hidden" name="action" value="delete_obj_esp" />
                                    <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
                                    <input type="hidden" name="oe" value="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>" />
                                    <input type="hidden" name="oesp" value="<?php echo htmlspecialchars($oespToken, ENT_QUOTES, 'UTF-8'); ?>" />
                                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                      Eliminar
                                    </button>
                                  </form>
                                </div>
                              </div>

                              <div data-oesp-form class="<?php echo ($oespEditToken !== '' && hash_equals($oespEditToken, $oespToken)) ? 'block' : 'hidden'; ?>">
                                <form class="flex flex-col gap-3 sm:flex-row sm:items-center" method="post" action="detalle-proyecto.php">
                                  <input type="hidden" name="action" value="update_obj_esp" />
                                  <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
                                  <input type="hidden" name="oe" value="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>" />
                                  <input type="hidden" name="oesp" value="<?php echo htmlspecialchars($oespToken, ENT_QUOTES, 'UTF-8'); ?>" />
                                  <input
                                    type="text"
                                    name="descripcion"
                                    value="<?php echo htmlspecialchars((string) ($esp['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                    class="flex-1 rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                                    required
                                  />
                                  <div class="flex justify-end gap-2">
                                    <a
                                      data-js-cancel-oesp="<?php echo htmlspecialchars($oespToken, ENT_QUOTES, 'UTF-8'); ?>"
                                      href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&section=objetivos"
                                      class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100"
                                    >
                                      Cancelar
                                    </a>
                                    <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                                      Guardar
                                    </button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </section>

        <section id="panel-cadena" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Cadena de valor</h2>
          </div>
          <p class="mt-4 text-sm text-neutral-600">Sección lista para incorporar la cadena de valor del proyecto.</p>
        </section>

        <section id="panel-bgg" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">BGG</h2>
          </div>
          <p class="mt-4 text-sm text-neutral-600">Sección lista para incorporar BGG con el espacio ampliado.</p>
        </section>
      </div>

    </main>
  </div>
</div>

<script>
  const projectToken = <?php echo json_encode((string) $projectToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  const flashModal = document.getElementById("flash-modal");
  const flashBackdrop = document.getElementById("flash-backdrop");
  const flashClose = document.getElementById("flash-close");

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

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeFlashModal();
    }
  });

  const allowedPanels = new Set(["overview", "mision", "vision", "valores", "objetivos", "cadena", "bgg"]);
  const panelStorageKey = projectToken ? `ri:detalle-proyecto:section:${projectToken}` : "ri:detalle-proyecto:section";

  const projectTabs = Array.from(document.querySelectorAll(".project-tab"));
  const projectPanels = Array.from(document.querySelectorAll(".project-panel"));

  function setActiveProjectPanel(panelId, options = {}) {
    const { updateUrl = true } = options;

    projectPanels.forEach((panel) => panel.classList.add("hidden"));
    if (panelId) {
      const activePanel = document.getElementById(`panel-${panelId}`);
      if (activePanel) {
        activePanel.classList.remove("hidden");
      }
    }

    projectTabs.forEach((tab) => {
      const isActive = panelId && tab.getAttribute("data-panel") === panelId;
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
    if (panelId) {
      url.searchParams.set("section", panelId);
    } else {
      url.searchParams.delete("section");
    }
    const edit = url.searchParams.get("edit");
    if (edit && edit !== panelId) {
      url.searchParams.delete("edit");
    }
    window.history.replaceState({}, "", url.toString());
  }

  projectTabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      const panelId = tab.getAttribute("data-panel");
      if (!panelId) return;
      setActiveProjectPanel(panelId);
    });
  });

  const url = new URL(window.location.href);
  const editParam = url.searchParams.get("edit");
  const sectionParam = url.searchParams.get("section");
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
    (normalizedSectionParam || normalizedStoredPanel || "overview");

  setActiveProjectPanel(initialPanel, { updateUrl: false });

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

  const valoresEditButton = document.querySelector("[data-js-edit-valores]");
  if (valoresEditButton) {
    valoresEditButton.addEventListener("click", (e) => {
      e.preventDefault();
      setActiveProjectPanel("valores");
      openValoresEdit();
    });
  }

  const valoresCancelLink = document.querySelector("[data-js-cancel-valores]");
  if (valoresCancelLink) {
    valoresCancelLink.addEventListener("click", (e) => {
      e.preventDefault();
      closeValoresEdit();
    });
  }

  document.querySelectorAll("[data-js-edit-oe]").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      const token = el.getAttribute("data-js-edit-oe");
      if (!token) return;
      setActiveProjectPanel("objetivos");
      openObjetivoEstrategicoEdit(token);
    });
  });

  document.querySelectorAll("[data-js-cancel-oe]").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      const token = el.getAttribute("data-js-cancel-oe");
      if (!token) return;
      closeObjetivoEstrategicoEdit(token);
    });
  });

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

  document.querySelectorAll("[data-js-edit-oesp]").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      const token = el.getAttribute("data-js-edit-oesp");
      if (!token) return;
      setActiveProjectPanel("objetivos");
      openObjetivoEspecificoEdit(token);
    });
  });

  document.querySelectorAll("[data-js-cancel-oesp]").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      const token = el.getAttribute("data-js-cancel-oesp");
      if (!token) return;
      closeObjetivoEspecificoEdit(token);
    });
  });

  /*if (editParam === "mision" || editParam === "vision") {
    openBlockEdit(editParam);
  }*/
  if (editParam === "valores") {
    openValoresEdit();
  }
  if (oeEditParam) {
    setActiveProjectPanel("objetivos");
    openObjetivoEstrategicoEdit(oeEditParam);
  }
  if (oespEditParam) {
    setActiveProjectPanel("objetivos");
    openObjetivoEspecificoEdit(oespEditParam);
  }

  const addButton = document.getElementById("agregar-valor");
  const input = document.getElementById("nuevo-valor");
  const list = document.getElementById("valores-lista");

  function createItem(text) {
    const row = document.createElement("div");
    row.className = "flex items-center gap-3 rounded-xl border border-neutral-200 bg-white px-4 py-3";

    const hidden = document.createElement("input");
    hidden.type = "hidden";
    hidden.name = "valores[]";
    hidden.value = text;

    const label = document.createElement("div");
    label.className = "flex-1 text-sm text-neutral-800";
    label.textContent = text;

    const remove = document.createElement("button");
    remove.type = "button";
    remove.className = "quitar-valor inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-red-700";
    remove.textContent = "Eliminar";
    remove.addEventListener("click", () => row.remove());

    row.appendChild(hidden);
    row.appendChild(label);
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

  if (addButton) {
    addButton.addEventListener("click", addValue);
  }

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
</script>

</body>
</html>
