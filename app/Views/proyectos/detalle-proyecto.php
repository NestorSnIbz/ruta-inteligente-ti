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
  $valorToEdit = is_array($valorToEdit ?? null) ? $valorToEdit : null;
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
      <?php if (!empty($error)) : ?>
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-800">
          <?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($success)) : ?>
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-900">
          <?php echo htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <div class="space-y-6">
        <section class="bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Misión</h2>
            <?php if ($edit !== 'mision') : ?>
              <a href="detalle-proyecto.php?id=<?php echo urlencode((string) $idProyecto); ?>&edit=mision" class="text-sm text-brand-700 font-medium hover:underline">
                Editar
              </a>
            <?php endif; ?>
          </div>

          <?php if ($edit === 'mision') : ?>
            <form class="mt-4 space-y-3" method="post" action="detalle-proyecto.php">
              <input type="hidden" name="action" value="save_mision" />
              <input type="hidden" name="id_proyecto" value="<?php echo htmlspecialchars((string) $idProyecto, ENT_QUOTES, 'UTF-8'); ?>" />
              <textarea
                name="descripcion"
                rows="5"
                class="w-full rounded-xl border border-neutral-300 px-4 py-3 text-sm outline-none resize-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                placeholder="Escribe la misión del proyecto..."
                required
              ><?php echo htmlspecialchars($misionTexto, ENT_QUOTES, 'UTF-8'); ?></textarea>
              <div class="flex justify-end gap-3">
                <a href="detalle-proyecto.php?id=<?php echo urlencode((string) $idProyecto); ?>" class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100">
                  Cancelar
                </a>
                <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                  Guardar
                </button>
              </div>
            </form>
          <?php else : ?>
            <?php if ($misionTexto === '') : ?>
              <p class="mt-4 text-sm text-neutral-600">Aún no se registró la misión. Presiona “Editar” para agregarla.</p>
            <?php else : ?>
              <p class="mt-4 text-sm text-neutral-600 leading-relaxed">
                <?php echo nl2br(htmlspecialchars($misionTexto, ENT_QUOTES, 'UTF-8')); ?>
              </p>
            <?php endif; ?>
          <?php endif; ?>
        </section>

        <section class="bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Visión</h2>
            <?php if ($edit !== 'vision') : ?>
              <a href="detalle-proyecto.php?id=<?php echo urlencode((string) $idProyecto); ?>&edit=vision" class="text-sm text-brand-700 font-medium hover:underline">
                Editar
              </a>
            <?php endif; ?>
          </div>

          <?php if ($edit === 'vision') : ?>
            <form class="mt-4 space-y-3" method="post" action="detalle-proyecto.php">
              <input type="hidden" name="action" value="save_vision" />
              <input type="hidden" name="id_proyecto" value="<?php echo htmlspecialchars((string) $idProyecto, ENT_QUOTES, 'UTF-8'); ?>" />
              <textarea
                name="descripcion"
                rows="5"
                class="w-full rounded-xl border border-neutral-300 px-4 py-3 text-sm outline-none resize-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                placeholder="Escribe la visión del proyecto..."
                required
              ><?php echo htmlspecialchars($visionTexto, ENT_QUOTES, 'UTF-8'); ?></textarea>
              <div class="flex justify-end gap-3">
                <a href="detalle-proyecto.php?id=<?php echo urlencode((string) $idProyecto); ?>" class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100">
                  Cancelar
                </a>
                <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                  Guardar
                </button>
              </div>
            </form>
          <?php else : ?>
            <?php if ($visionTexto === '') : ?>
              <p class="mt-4 text-sm text-neutral-600">Aún no se registró la visión. Presiona “Editar” para agregarla.</p>
            <?php else : ?>
              <p class="mt-4 text-sm text-neutral-600 leading-relaxed">
                <?php echo nl2br(htmlspecialchars($visionTexto, ENT_QUOTES, 'UTF-8')); ?>
              </p>
            <?php endif; ?>
          <?php endif; ?>
        </section>

        <section class="bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Valores</h2>
            <?php if ($edit !== 'valores') : ?>
              <a href="detalle-proyecto.php?id=<?php echo urlencode((string) $idProyecto); ?>&edit=valores" class="text-sm text-brand-700 font-medium hover:underline">
                Editar
              </a>
            <?php endif; ?>
          </div>

          <?php if ($edit === 'valores') : ?>
            <form class="mt-4 space-y-3" method="post" action="detalle-proyecto.php">
              <input type="hidden" name="action" value="add_valor" />
              <input type="hidden" name="id_proyecto" value="<?php echo htmlspecialchars((string) $idProyecto, ENT_QUOTES, 'UTF-8'); ?>" />
              <input
                name="descripcion"
                type="text"
                class="w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                placeholder="Agrega un valor (ej: Innovación)"
                required
              />
              <div class="flex justify-end gap-3">
                <a href="detalle-proyecto.php?id=<?php echo urlencode((string) $idProyecto); ?>" class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100">
                  Cancelar
                </a>
                <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                  Agregar
                </button>
              </div>
            </form>
          <?php endif; ?>

          <?php if ($edit === 'valor' && $valorToEdit) : ?>
            <form class="mt-4 space-y-3" method="post" action="detalle-proyecto.php">
              <input type="hidden" name="action" value="update_valor" />
              <input type="hidden" name="id_proyecto" value="<?php echo htmlspecialchars((string) $idProyecto, ENT_QUOTES, 'UTF-8'); ?>" />
              <input type="hidden" name="id_valor" value="<?php echo htmlspecialchars((string) ($valorToEdit['id_valor'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" />
              <input
                name="descripcion"
                type="text"
                value="<?php echo htmlspecialchars((string) ($valorToEdit['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                class="w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                required
              />
              <div class="flex justify-end gap-3">
                <a href="detalle-proyecto.php?id=<?php echo urlencode((string) $idProyecto); ?>" class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100">
                  Cancelar
                </a>
                <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                  Guardar
                </button>
              </div>
            </form>
          <?php endif; ?>

          <?php if (empty($valores)) : ?>
            <p class="mt-4 text-sm text-neutral-600">Aún no se registraron valores. Usa “Editar” para agregarlos.</p>
          <?php else : ?>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
              <?php foreach ($valores as $valor) : ?>
                <div class="rounded-xl border border-neutral-200 bg-white px-4 py-3">
                  <div class="flex items-start justify-between gap-3">
                    <div class="text-sm text-neutral-800">
                      <?php echo htmlspecialchars((string) ($valor['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <a
                      href="detalle-proyecto.php?id=<?php echo urlencode((string) $idProyecto); ?>&edit=valor&valor=<?php echo urlencode((string) ($valor['id_valor'] ?? '')); ?>"
                      class="text-sm text-brand-700 font-medium hover:underline"
                    >
                      Editar
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>
      </div>

    </main>
  </div>
</div>

</body>
</html>
