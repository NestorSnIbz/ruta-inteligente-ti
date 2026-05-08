<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Proyectos - Ruta Inteligente TI</title>
    <link href="dist/output.css" rel="stylesheet" />
</head>

<body class="min-h-screen bg-neutral-50 text-neutral-900">
<?php
  $proyectos = is_array($proyectos ?? null) ? $proyectos : [];
?>
<div class="min-h-screen grid grid-cols-1 md:grid-cols-[16rem_1fr]">

    <aside class="bg-brand-900 text-white">
        <div class="px-6 py-6">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-white/10 grid place-items-center">
                    <span class="text-sm font-semibold">RI</span>
                </div>
                <div>
                    <div class="text-sm font-semibold">Ruta Inteligente TI</div>
                    <div class="text-xs text-white/70">Panel de control</div>
                </div>
            </div>
        </div>

        <nav class="px-3 pb-6">
            <a href="dashboard.php" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-white/80 hover:bg-white/10">
                <span class="h-2 w-2 bg-white/30 rounded-full"></span>
                Dashboard
            </a>

            <a href="proyectos.php" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm bg-white/10">
                <span class="h-2 w-2 bg-accent-500 rounded-full"></span>
                Proyectos
            </a>

            <a href="configuracion.php" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-white/80 hover:bg-white/10">
                <span class="h-2 w-2 bg-white/30 rounded-full"></span>
                Configuración
            </a>
        </nav>
    </aside>

    <div class="min-h-screen flex flex-col">

        <!-- HEADER -->
        <header class="bg-white border-b border-neutral-200">
            <div class="px-6 py-4 flex items-center justify-between">
                <h1 class="text-xl font-semibold">Proyectos</h1>

                <a href="nuevo-proyecto.php"
                   class="bg-brand-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-brand-700">
                    + Nuevo proyecto
                </a>
            </div>
        </header>

        <main class="flex-1 px-6 py-8">
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

            <!-- BUSCADOR Y FILTROS -->
            <div class="mb-8 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">

                <div class="w-full md:max-w-md">
                    <input
                        type="text"
                        placeholder="Buscar proyectos..."
                        class="w-full rounded-xl border border-neutral-300 px-3 py-2 text-sm focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15 outline-none"
                    />
                </div>

                <div class="flex gap-2">
                    <select class="rounded-xl border border-neutral-300 px-3 py-2 text-sm">
                        <option>Todos</option>
                        <option>Activo</option>
                        <option>Borrador</option>
                        <option>Compartido</option>
                    </select>

                    <select class="rounded-xl border border-neutral-300 px-3 py-2 text-sm">
                        <option>Ordenar por fecha</option>
                        <option>Ordenar por nombre</option>
                    </select>
                </div>
            </div>

            <!-- LISTA -->
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm">

                <div class="divide-y divide-neutral-200">
                    <?php if (empty($proyectos)) : ?>
                        <div class="px-6 py-8 text-sm text-neutral-600">
                            Aún no tienes proyectos registrados. Crea uno con “+ Nuevo proyecto”.
                        </div>
                    <?php else : ?>
                        <?php foreach ($proyectos as $proyecto) : ?>
                            <div class="px-6 py-4 flex justify-between items-center hover:bg-neutral-50 transition">
                                <div>
                                    <p class="font-medium">
                                        <?php echo htmlspecialchars((string) ($proyecto['nombre'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <p class="text-xs text-neutral-500">Proyecto estratégico</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <a
                                        href="detalle-proyecto.php?t=<?php echo urlencode((string) ($proyecto['token'] ?? '')); ?>"
                                        class="text-sm text-brand-700 font-medium hover:underline"
                                    >
                                        Ver detalle
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>

        </main>
    </div>
</div>
</body>
</html>
