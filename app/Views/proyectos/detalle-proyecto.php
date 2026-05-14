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
?>

<div class="min-h-screen grid grid-cols-1 md:grid-cols-[16rem_1fr]">

  <?php
    $sidebarActive = 'proyectos';
    $sidebarSeedProjects = [];
    $sidebarCurrentProject = ['t' => $projectToken, 'name' => $proyectoNombre];
    include __DIR__ . '/../layouts/sidebar.php';
  ?>

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
                        <th scope="col" class="w-[45%] px-4 py-3">Objetivo estratégico</th>
                        <th scope="col" class="px-4 py-3">Objetivos específicos</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                      <?php foreach ($objetivosEstrategicos as $obj) : ?>
                        <?php
                          $oeId = (int) ($obj['id_objetivo_est'] ?? 0);
                          $esps = ($oeId > 0 && isset($objetivosEspecificosByEstrategico[$oeId]) && is_array($objetivosEspecificosByEstrategico[$oeId]))
                            ? $objetivosEspecificosByEstrategico[$oeId]
                            : [];
                        ?>
                        <tr>
                          <td class="px-4 py-3 text-neutral-800">
                            <?php echo htmlspecialchars((string) ($obj['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                          </td>
                          <td class="px-4 py-3 text-neutral-800">
                            <?php if (empty($esps)) : ?>
                              <span class="text-sm text-neutral-500">Sin objetivos específicos</span>
                            <?php else : ?>
                              <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white">
                                <?php foreach ($esps as $esp) : ?>
                                  <div class="border-t border-neutral-200 first:border-t-0 px-3 py-2 text-sm text-neutral-800 leading-relaxed">
                                    <?php echo htmlspecialchars((string) ($esp['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                  </div>
                                <?php endforeach; ?>
                              </div>
                            <?php endif; ?>
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
          <div class="mt-4 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <div class="text-sm font-semibold text-neutral-900">Autodiagnóstico de la Cadena de Valor Interna</div>
                <div class="mt-0.5 text-xs text-neutral-600">Selecciona una valoración (0–4) por fila. El resultado se calcula automáticamente.</div>
              </div>
              <div class="text-xs text-neutral-500">Opciones: 0 · 1 · 2 · 3 · 4</div>
            </div>
          </div>

          <div class="mt-4 overflow-x-auto rounded-2xl border border-neutral-200 bg-white">
            <form id="cvi-form" class="min-w-[1060px]">
              <table class="w-full border-separate border-spacing-0 text-sm">
                <thead class="bg-neutral-100">
                  <tr>
                    <th colspan="2" class="border-b border-neutral-200 px-4 py-3 text-left font-semibold text-neutral-900">
                      AUTODIAGNÓSTICO DE LA CADENA DE VALOR INTERNA
                    </th>
                    <th colspan="5" class="border-b border-l border-neutral-200 px-4 py-3 text-center font-semibold text-neutral-900">
                      VALORACIÓN
                    </th>
                  </tr>
                  <tr>
                    <th class="w-14 border-b border-neutral-200 px-4 py-2 text-center text-xs font-semibold text-neutral-700">#</th>
                    <th class="border-b border-l border-neutral-200 px-4 py-2 text-left text-xs font-semibold text-neutral-700">Pregunta</th>
                    <th class="w-24 border-b border-l border-neutral-200 px-3 py-2 text-center text-xs font-semibold text-neutral-700">0</th>
                    <th class="w-24 border-b border-l border-neutral-200 px-3 py-2 text-center text-xs font-semibold text-neutral-700">1</th>
                    <th class="w-24 border-b border-l border-neutral-200 px-3 py-2 text-center text-xs font-semibold text-neutral-700">2</th>
                    <th class="w-24 border-b border-l border-neutral-200 px-3 py-2 text-center text-xs font-semibold text-neutral-700">3</th>
                    <th class="w-24 border-b border-l border-neutral-200 px-3 py-2 text-center text-xs font-semibold text-neutral-700">4</th>
                  </tr>
                </thead>
                <tbody id="cvi-body" class="divide-y divide-neutral-200">
                  <?php
                    $fallback = [
                      1 => 'La empresa tiene una política sistematizada de cero defectos en la producción de productos/servicios.',
                      2 => 'La empresa emplea los medios productivos tecnológicamente más avanzados de su sector.',
                      3 => 'La empresa dispone de un sistema de información y control de gestión eficiente y eficaz.',
                      4 => 'Los medios técnicos y tecnológicos de la empresa están preparados para competir en un futuro a corto, medio y largo plazo.',
                      5 => 'La empresa es un referente en su sector en I+D+i.',
                      6 => 'La excelencia de los procedimientos de la empresa (ISO, etc.) son una principal fuente de ventaja competitiva.',
                      7 => 'La empresa dispone de página web, y esta se emplea no sólo como escaparate virtual de productos/servicios, sino también para establecer relaciones con clientes y proveedores.',
                      8 => 'Los productos/servicios que desarrolla nuestra empresa llevan incorporada una tecnología difícil de imitar.',
                      9 => 'La empresa es referente en su sector en la optimización, en términos de coste, de su cadena de producción, siendo ésta una de sus principales ventajas competitivas.',
                      10 => 'La informatización de la empresa es una fuente de ventaja competitiva clara respecto a sus competidores.',
                      11 => 'Los canales de distribución de la empresa son una importante fuente de ventajas competitivas.',
                      12 => 'Los productos/servicios de la empresa son altamente y diferencialmente valorados por el cliente respecto a nuestros competidores.',
                      13 => 'La empresa dispone y ejecuta un sistemático plan de marketing y ventas.',
                      14 => 'La empresa tiene optimizada su gestión financiera.',
                      15 => 'La empresa busca continuamente mejorar la relación con sus clientes cortando los plazos de ejecución, personalizando la oferta o mejorando las condiciones de entrega, siempre partiendo de un plan previo.',
                      16 => 'La empresa es referente en su sector en el lanzamiento de innovadores productos y servicios de éxito demostrado en el mercado.',
                      17 => 'Los Recursos Humanos son especialmente responsables del éxito de la empresa, considerándolos incluso como el principal activo estratégico.',
                      18 => 'Se tiene una plantilla altamente motivada, que conoce con claridad las metas, objetivos y estrategias de la organización.',
                      19 => 'La empresa siempre trabaja conforme a una estrategia y objetivos claros.',
                      20 => 'La gestión del circulante está optimizada.',
                      21 => 'Se tiene definido claramente el posicionamiento estratégico de todos los productos de la empresa.',
                      22 => 'Se dispone de una política de marca basada en la reputación que la empresa genera, en la gestión de relación con el cliente y en el posicionamiento estratégico previamente definido.',
                      23 => 'La cartera de clientes de nuestra empresa está altamente fidelizada, ya que tenemos como principal propósito deleitarlos día a día.',
                      24 => 'Nuestra política y equipo de ventas y marketing es una importante ventaja competitiva de nuestra empresa respecto al sector.',
                      25 => 'El servicio al cliente que prestamos es una de nuestras principales ventajas competitivas respecto a nuestros competidores.',
                    ];
                    if (empty($cadenaPreguntas)) {
                      $cadenaPreguntas = [];
                      foreach ($fallback as $n => $t) {
                        $cadenaPreguntas[] = ['id_pregunta' => (int) $n, 'numero' => (int) $n, 'texto' => (string) $t];
                      }
                    }
                  ?>
                  <?php foreach ($cadenaPreguntas as $q) : ?>
                    <?php
                      $qId = is_array($q) ? (int) ($q['id_pregunta'] ?? 0) : 0;
                      $qNumber = is_array($q) ? (int) ($q['numero'] ?? 0) : 0;
                      $qText = is_array($q) ? (string) ($q['texto'] ?? '') : '';
                      $selected = array_key_exists($qId, $cadenaRespuestas) ? (int) $cadenaRespuestas[$qId] : null;
                      if ($qId <= 0 || $qNumber <= 0 || $qText === '') {
                        continue;
                      }
                    ?>
                    <tr class="cvi-row" data-cvi-row="<?php echo (int) $qId; ?>" data-cvi-number="<?php echo (int) $qNumber; ?>">
                      <td class="border-b border-neutral-200 px-4 py-3 text-center text-xs font-semibold text-neutral-700">
                        <?php echo (int) $qNumber; ?>
                      </td>
                      <td class="border-b border-l border-neutral-200 px-4 py-3 text-sm text-neutral-800">
                        <div class="flex items-start justify-between gap-3">
                          <div class="leading-relaxed">
                            <?php echo htmlspecialchars($qText, ENT_QUOTES, 'UTF-8'); ?>
                          </div>
                          <span data-cvi-ref class="hidden rounded-lg bg-red-50 px-2 py-1 text-xs font-semibold text-red-700">#¡REF!</span>
                        </div>
                      </td>
                      <?php for ($v = 0; $v <= 4; $v++) : ?>
                        <td class="border-b border-l border-neutral-200 px-3 py-2 text-center">
                          <label class="cvi-cell flex h-12 w-full cursor-pointer items-center justify-center select-none">
                            <input
                              type="radio"
                              name="cvi_q<?php echo (int) $qId; ?>"
                              value="<?php echo (int) $v; ?>"
                              class="sr-only"
                              <?php echo ($selected !== null && (int) $selected === (int) $v) ? 'checked' : ''; ?>
                            />
                            <span class="cvi-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-neutral-300 bg-white px-3 text-sm font-semibold text-neutral-700 transition">
                              <?php echo (int) $v; ?>
                            </span>
                          </label>
                        </td>
                      <?php endfor; ?>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </form>
          </div>

          <?php
            $calcSum = (int) ($cadenaCalc['sum'] ?? 0);
            $calcValid = (int) ($cadenaCalc['valid'] ?? 0);
            $calcCount = (int) ($cadenaCalc['count'] ?? 0);
            $calcPotential = $cadenaCalc['potential'] ?? null;
            $calcPotentialText = '—';
            $calcPotentialSub = '';
            if ($calcPotential !== null && is_numeric($calcPotential)) {
              $calcPotentialText = number_format((float) $calcPotential, 2, '.', '');
              $calcPotentialSub = ((string) round(((float) $calcPotential) * 100)) . '%';
            }
          ?>
          <div class="mt-4 rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <div class="text-sm font-semibold text-neutral-900">Resultado final</div>
                <div class="mt-0.5 text-xs text-neutral-500">Fórmula: 1 - (Σ respuestas / 100)</div>
              </div>
              <div class="flex items-center gap-2">
                <button
                  id="cvi-save"
                  type="button"
                  class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25"
                >
                  Guardar Evaluación
                </button>
              </div>
            </div>
            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
              <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                <div class="text-xs font-medium text-neutral-600">Suma</div>
                <div id="cvi-sum" class="mt-1 text-2xl font-semibold text-neutral-900"><?php echo (int) $calcSum; ?></div>
              </div>
              <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                <div class="text-xs font-medium text-neutral-600">Filas válidas</div>
                <div id="cvi-valid" class="mt-1 text-2xl font-semibold text-neutral-900"><?php echo (int) $calcValid; ?>/<?php echo (int) $calcCount; ?></div>
              </div>
              <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                <div class="text-xs font-medium text-neutral-600">Potencial de mejora</div>
                <div id="cvi-result" class="mt-1 text-2xl font-semibold text-brand-900"><?php echo htmlspecialchars($calcPotentialText, ENT_QUOTES, 'UTF-8'); ?></div>
                <div id="cvi-result-sub" class="mt-1 text-xs text-neutral-500"><?php echo htmlspecialchars($calcPotentialSub, ENT_QUOTES, 'UTF-8'); ?></div>
              </div>
            </div>
            <div class="mt-4 text-xs text-neutral-600">
              POTENCIAL DE MEJORA DE LA CADENA DE VALOR INTERNA
            </div>
          </div>

          <div class="mt-4 rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
              <div>
                <div class="text-sm font-semibold text-neutral-900">FODA</div>
                <div class="mt-0.5 text-xs text-neutral-500">Fortalezas y debilidades obtenidas desde Cadena de valor.</div>
              </div>
              <button
                id="foda-save"
                type="button"
                class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25"
              >
                Guardar FODA
              </button>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
              <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
                <div class="flex items-center justify-between gap-3">
                  <div class="text-sm font-semibold text-neutral-900">Fortalezas</div>
                  <button id="foda-add-fortaleza" type="button" class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
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
                    <tbody id="foda-fortalezas-body" class="divide-y divide-neutral-200">
                      <?php
                        $fortRows = array_values(array_filter(array_map('trim', array_map('strval', $fodaFortalezas))));
                        $fortTarget = max(3, count($fortRows));
                        for ($i = 0; $i < $fortTarget; $i++) :
                          $value = $fortRows[$i] ?? '';
                      ?>
                        <tr data-foda-row="fortaleza">
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
                  <div class="text-sm font-semibold text-neutral-900">Debilidades</div>
                  <button id="foda-add-debilidad" type="button" class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
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
                    <tbody id="foda-debilidades-body" class="divide-y divide-neutral-200">
                      <?php
                        $debRows = array_values(array_filter(array_map('trim', array_map('strval', $fodaDebilidades))));
                        $debTarget = max(3, count($debRows));
                        for ($i = 0; $i < $debTarget; $i++) :
                          $value = $debRows[$i] ?? '';
                      ?>
                        <tr data-foda-row="debilidad">
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

          <div id="cvi-toast" class="pointer-events-none fixed bottom-6 right-6 z-50 hidden w-full max-w-sm">
            <div id="cvi-toast-card" class="pointer-events-auto rounded-2xl border border-neutral-200 bg-white p-4 shadow-lg">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div id="cvi-toast-title" class="text-sm font-semibold text-neutral-900"></div>
                  <div id="cvi-toast-msg" class="mt-1 text-sm text-neutral-700"></div>
                </div>
                <button id="cvi-toast-close" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50">
                  <span class="sr-only">Cerrar</span>
                  <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
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
  const projectName = <?php echo json_encode((string) $proyectoNombre, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
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

  const cviForm = document.getElementById("cvi-form");
  const cviSumEl = document.getElementById("cvi-sum");
  const cviValidEl = document.getElementById("cvi-valid");
  const cviResultEl = document.getElementById("cvi-result");
  const cviResultSubEl = document.getElementById("cvi-result-sub");
  const cviSaveButton = document.getElementById("cvi-save");
  const cviRows = Array.from(document.querySelectorAll("[data-cvi-row]"));
  const fodaSaveButton = document.getElementById("foda-save");
  const fodaFortBody = document.getElementById("foda-fortalezas-body");
  const fodaDebBody = document.getElementById("foda-debilidades-body");
  const fodaAddFort = document.getElementById("foda-add-fortaleza");
  const fodaAddDeb = document.getElementById("foda-add-debilidad");
  const cviToast = document.getElementById("cvi-toast");
  const cviToastCard = document.getElementById("cvi-toast-card");
  const cviToastTitle = document.getElementById("cvi-toast-title");
  const cviToastMsg = document.getElementById("cvi-toast-msg");
  const cviToastClose = document.getElementById("cvi-toast-close");
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

  function renumberFodaRows(tbody) {
    const rows = Array.from(tbody.querySelectorAll("tr"));
    rows.forEach((row, idx) => {
      const n = row.querySelector("td");
      if (n) n.textContent = String(idx + 1);
    });
  }

  function attachFodaRemoveHandlers(tbody) {
    tbody.querySelectorAll(".foda-remove").forEach((btn) => {
      btn.addEventListener("click", () => {
        const row = btn.closest("tr");
        if (row) row.remove();
        renumberFodaRows(tbody);
      });
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

  if (fodaFortBody) attachFodaRemoveHandlers(fodaFortBody);
  if (fodaDebBody) attachFodaRemoveHandlers(fodaDebBody);
  if (fodaFortBody) renumberFodaRows(fodaFortBody);
  if (fodaDebBody) renumberFodaRows(fodaDebBody);

  if (fodaAddFort && fodaFortBody) {
    fodaAddFort.addEventListener("click", () => {
      const tr = createFodaRow("fortaleza");
      fodaFortBody.appendChild(tr);
      attachFodaRemoveHandlers(fodaFortBody);
      renumberFodaRows(fodaFortBody);
      tr.querySelector("input")?.focus();
    });
  }

  if (fodaAddDeb && fodaDebBody) {
    fodaAddDeb.addEventListener("click", () => {
      const tr = createFodaRow("debilidad");
      fodaDebBody.appendChild(tr);
      attachFodaRemoveHandlers(fodaDebBody);
      renumberFodaRows(fodaDebBody);
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
      fodaSaveButton.disabled = true;
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
        fodaSaving = false;
      }
    });
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
      if (!cviValidationActive) {
        if (cviResultEl) cviResultEl.textContent = "—";
        if (cviResultSubEl) cviResultSubEl.textContent = "";
        return;
      }
      if (cviResultEl) cviResultEl.textContent = "#¡REF!";
      if (cviResultSubEl) cviResultSubEl.textContent = "";
      return;
    }

    const p = Number(potential);
    if (Number.isNaN(p)) {
      if (cviResultEl) cviResultEl.textContent = "#¡REF!";
      if (cviResultSubEl) cviResultSubEl.textContent = "";
      return;
    }

    if (cviResultEl) cviResultEl.textContent = p.toFixed(2);
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
