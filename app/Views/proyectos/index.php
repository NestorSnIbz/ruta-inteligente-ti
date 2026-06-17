<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Planes Estratégicos - Ruta Inteligente TI</title>
    <link href="dist/output.css" rel="stylesheet" />
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

    <div class="min-h-screen flex flex-col">

        <!-- HEADER -->
        <header class="ri-dashboard-header">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="ri-page-title text-xl font-semibold">Planes Estratégicos</h1>
                    <p class="ri-page-subtitle mt-1 text-sm">Consulta, busca y gestiona todos los planes estratégicos registrados.</p>
                </div>

                <a href="nuevo-proyecto.php"
                   class="ri-project-primary-btn inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold">
                    + Nuevo plan estratégico
                </a>
            </div>
        </header>

        <main class="flex-1 px-6 py-8">
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

            <!-- BUSCADOR Y FILTROS -->
            <?php
            $search = trim((string) ($search ?? ($_GET['q'] ?? '')));
            $sort = (string) ($sort ?? ($_GET['sort'] ?? 'recent'));
            ?>

            <form method="get" action="proyectos.php" class="mb-8 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
                <div class="w-full md:max-w-md">
                    <input
                        type="text"
                        name="q"
                        value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Buscar planes estratégicos..."
                        class="ri-project-search w-full rounded-2xl px-4 py-2.5 text-sm outline-none transition"
                    />
                </div>

                <div class="flex gap-2">
                    <select
                        name="sort"
                        class="ri-project-sort rounded-2xl px-4 py-2.5 text-sm outline-none transition"
                        onchange="this.form.submit()"
                    >
                        <option value="recent" <?php echo $sort === 'recent' ? 'selected' : ''; ?>>Más recientes</option>
                        <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Más antiguos</option>
                        <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Nombre A-Z</option>
                        <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Nombre Z-A</option>
                    </select>

                    <button
                        type="submit"
                        class="ri-project-primary-btn rounded-2xl px-4 py-2 text-sm font-semibold"
                    >
                        Buscar
                    </button>

                    <?php if ($search !== '' || $sort !== 'recent') : ?>
                        <a
                            href="proyectos.php"
                            class="ri-project-ghost-btn rounded-2xl px-4 py-2 text-sm font-semibold"
                        >
                            Limpiar
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- LISTA -->
            <div class="ri-project-list-card rounded-[24px]">

                <div class="divide-y divide-neutral-200">
                    <?php if (empty($proyectos)) : ?>
                        <div class="px-6 py-8 text-sm text-neutral-600">
                            Aún no tienes planes estratégicos registrados. Crea uno con “+ Nuevo plan estratégico”.
                        </div>
                    <?php else : ?>
                        <?php foreach ($proyectos as $proyecto) : ?>
                            <div
                                class="ri-project-list-row px-6 py-4 flex justify-between items-center"
                                data-project-row="1"
                                data-project-name="<?php echo htmlspecialchars((string) ($proyecto['nombre'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                data-project-id="<?php echo (int) ($proyecto['id_proyecto'] ?? 0); ?>"
                            >
                                <div>
                                    <p class="font-medium">
                                        <?php echo htmlspecialchars((string) ($proyecto['nombre'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <p class="text-xs text-neutral-500">Plan estratégico</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <a
                                        href="detalle-proyecto.php?t=<?php echo urlencode((string) ($proyecto['token'] ?? '')); ?>"
                                        class="ri-project-link text-sm font-medium hover:underline"
                                    >
                                        Ver detalle
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>

            <?php
              $page = max(1, (int) ($page ?? 1));
              $totalPages = max(1, (int) ($totalPages ?? 1));
              $totalProyectos = max(0, (int) ($totalProyectos ?? 0));
              $baseParams = $_GET ?? [];
              unset($baseParams['page']);
              $buildUrl = function (int $p) use ($baseParams): string {
                $params = $baseParams;
                $params['page'] = $p;
                $qs = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
                return 'proyectos.php' . ($qs ? ('?' . $qs) : '');
              };
              $start = max(1, $page - 2);
              $end = min($totalPages, $page + 2);
            ?>
            <?php if ($totalPages > 1) : ?>
              <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                <div class="text-sm text-neutral-600">
                  Página <?php echo (int) $page; ?> de <?php echo (int) $totalPages; ?> · <?php echo (int) $totalProyectos; ?> planes estratégicos
                </div>

                <nav class="ri-project-pagination inline-flex items-center gap-1" aria-label="Paginación">
                  <a
                    href="<?php echo htmlspecialchars($buildUrl(max(1, $page - 1)), ENT_QUOTES, 'UTF-8'); ?>"
                    class="<?php echo $page <= 1 ? 'pointer-events-none opacity-50' : ''; ?> ri-project-ghost-btn inline-flex h-10 items-center justify-center rounded-xl px-3 text-sm font-semibold"
                  >
                    Anterior
                  </a>

                  <?php for ($p = $start; $p <= $end; $p++) : ?>
                    <a
                      href="<?php echo htmlspecialchars($buildUrl($p), ENT_QUOTES, 'UTF-8'); ?>"
                      class="<?php echo $p === $page ? 'text-white' : 'ri-project-ghost-btn'; ?> inline-flex h-10 w-10 items-center justify-center rounded-xl border text-sm font-semibold"
                      aria-current="<?php echo $p === $page ? 'page' : 'false'; ?>"
                    >
                      <?php echo (int) $p; ?>
                    </a>
                  <?php endfor; ?>

                  <a
                    href="<?php echo htmlspecialchars($buildUrl(min($totalPages, $page + 1)), ENT_QUOTES, 'UTF-8'); ?>"
                    class="<?php echo $page >= $totalPages ? 'pointer-events-none opacity-50' : ''; ?> ri-project-ghost-btn inline-flex h-10 items-center justify-center rounded-xl px-3 text-sm font-semibold"
                  >
                    Siguiente
                  </a>
                </nav>
              </div>
            <?php endif; ?>

        </main>
    </div>
</div>

<script>
    document.querySelectorAll('a[href^="detalle-proyecto.php?t="]').forEach((a) => {
        a.addEventListener("click", () => {
            const row = a.closest("[data-project-row]");
            const id = row ? Number(row.getAttribute("data-project-id") || 0) : 0;
            const name = row ? (row.getAttribute("data-project-name") || "") : "";
            if (id && name && window.RISidebar && typeof window.RISidebar.pushRecentProject === "function") {
                window.RISidebar.pushRecentProject(id, name);
            }
        });
    });
</script>
</body>
</html>
