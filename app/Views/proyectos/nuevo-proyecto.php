<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Nuevo Plan Estratégico - Ruta Inteligente TI</title>
  <link href="/dist/output.css" rel="stylesheet" />
  <link href="/app-shell.css" rel="stylesheet" />
</head>

<body class="ri-page-shell min-h-screen text-neutral-900">
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
    <header class="ri-dashboard-header">
      <div class="px-6 py-4 flex flex-wrap items-start justify-between gap-4">
        <div>
          <h1 class="ri-page-title text-2xl font-semibold tracking-tight">
            Nuevo Plan Estratégico
          </h1>
          <p class="ri-page-subtitle mt-1 text-sm">
            Registra un nuevo plan estratégico.
          </p>
        </div>

        <a href="proyectos.php"
           class="ri-project-ghost-btn rounded-2xl px-4 py-2 text-sm font-medium">
          Volver
        </a>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 p-6">

      <div class="ri-project-create-card max-w-4xl mx-auto rounded-[24px] p-6">
        <?php if (!empty($error)) : ?>
          <div class="ri-app-alert-danger mb-6 rounded-[24px] px-6 py-4 text-sm">
            <?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($success)) : ?>
          <div class="ri-app-alert-success mb-6 rounded-[24px] px-6 py-4 text-sm">
            <?php echo htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>

        <form action="nuevo-proyecto.php" method="post">
          <div>
            <label for="nombre" class="ri-app-label block text-sm font-medium">
              Nombre del plan estratégico
            </label>

            <input
              id="nombre"
              name="nombre"
              type="text"
              required
              placeholder="Plan Estratégico 2026"
              class="ri-app-input mt-2 w-full rounded-2xl px-4 py-3 text-sm outline-none transition"
            />

            <p class="mt-2 text-xs text-neutral-500">
              Se guardará en la base de datos del plan estratégico.
            </p>
          </div>

          <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a
              href="proyectos.php"
              class="ri-project-ghost-btn rounded-2xl px-5 py-2.5 text-sm font-medium"
            >
              Cancelar
            </a>

            <button
              type="submit"
              class="ri-project-primary-btn rounded-2xl px-5 py-2.5 text-sm font-semibold"
            >
              Guardar plan estratégico
            </button>
          </div>
        </form>

      </div>

    </main>
  </div>
</div>
</body>
</html>
