<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Nuevo Proyecto - Ruta Inteligente TI</title>
  <link href="/dist/output.css" rel="stylesheet" />
</head>

<body class="min-h-screen bg-neutral-50 text-neutral-900">
<?php
  $proyectos = is_array($proyectos ?? null) ? $proyectos : [];
?>
<div class="min-h-screen grid grid-cols-1 md:grid-cols-[16rem_1fr]">
  <?php
    $sidebarActive = 'proyectos';
    $sidebarSeedProjects = $proyectos;
    include __DIR__ . '/../layouts/sidebar.php';
  ?>

  <!-- MAIN -->
  <div class="flex flex-col">

    <!-- HEADER -->
    <header class="bg-white border-b border-neutral-200">
      <div class="px-6 py-4 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">
            Nuevo Proyecto
          </h1>
          <p class="text-sm text-neutral-600 mt-1">
            Registra un nuevo proyecto estratégico.
          </p>
        </div>

        <a href="proyectos.php"
           class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100">
          Volver
        </a>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 p-6">

      <div class="max-w-4xl mx-auto bg-white border border-neutral-200 rounded-2xl shadow-sm p-6">
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

        <form action="nuevo-proyecto.php" method="post">
          <div>
            <label for="nombre" class="block text-sm font-medium text-neutral-700">
              Nombre del proyecto
            </label>

            <input
              id="nombre"
              name="nombre"
              type="text"
              required
              placeholder="Plan Estratégico 2026"
              class="mt-2 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
            />

            <p class="mt-2 text-xs text-neutral-500">
              Se guardará según la estructura de la tabla proyecto (id_proyecto, nombre, creador_id).
            </p>
          </div>

          <div class="mt-8 flex justify-end gap-3">
            <a
              href="proyectos.php"
              class="rounded-xl border border-neutral-300 px-5 py-2.5 text-sm font-medium hover:bg-neutral-100"
            >
              Cancelar
            </a>

            <button
              type="submit"
              class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700"
            >
              Guardar proyecto
            </button>
          </div>
        </form>

      </div>

    </main>
  </div>
</div>
</body>
</html>
