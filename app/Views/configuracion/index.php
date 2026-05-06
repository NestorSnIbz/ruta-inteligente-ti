<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Configuración - Ruta Inteligente TI</title>
        <link href="dist/output.css" rel="stylesheet" />
    </head>
    <body class="min-h-screen bg-neutral-50 text-neutral-900">
    <?php
        $nombre = is_array($authUser ?? null) ? (string) ($authUser['nombre'] ?? '') : '';
        $success = $success ?? null;
        $error = $error ?? null;
    ?>
    <div class="min-h-screen grid grid-cols-1 md:grid-cols-[16rem_1fr]">
    <aside class="bg-brand-900 text-white">
        <div class="px-6 py-6">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-white/10 grid place-items-center">
            <span class="text-sm font-semibold">RI</span>
            </div>
            <div>
            <div class="text-sm font-semibold leading-tight">Ruta Inteligente TI</div>
            <div class="text-xs text-white/70 leading-tight">Panel de control</div>
            </div>
        </div>
        </div>
        <nav class="px-3 pb-6">
        <a href="dashboard.php" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
            <span class="h-2 w-2 rounded-full bg-white/30"></span>
            Dashboard
        </a>
        <a href="proyectos.php" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
            <span class="h-2 w-2 rounded-full bg-white/30"></span>
            Proyectos
        </a>
        <a href="configuracion.php" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium bg-white/10">
            <span class="h-2 w-2 rounded-full bg-accent-500"></span>
            Configuración
        </a>
        </nav>
    </aside>
    <div class="min-h-screen flex flex-col">
        <header class="bg-white border-b border-neutral-200">
        <div class="px-6 py-4 flex items-center justify-between">
            <h1 class="text-xl font-semibold">Configuración</h1>
        </div>
        </header>
        <main class="flex-1 px-6 py-8 space-y-6">
        <?php if (!empty($error)) : ?>
            <div class="rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-800">
                <?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)) : ?>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-900">
                <?php echo htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Perfil</h2>
            <form class="space-y-4" action="configuracion.php" method="post">
                <input type="hidden" name="action" value="update_name" />
                <div>
                    <label for="nombre" class="text-sm text-neutral-600">Nombre</label>
                    <input
                        id="nombre"
                        name="nombre"
                        type="text"
                        value="<?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?>"
                        required
                        class="mt-1 w-full border border-neutral-300 rounded-xl px-3 py-2 text-sm focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15 outline-none"
                    >
                </div>
                <button class="mt-2 bg-brand-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-brand-700">
                    Guardar cambios
                </button>
            </form>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Cambiar contraseña</h2>
            <form class="space-y-4" action="configuracion.php" method="post">
                <input type="hidden" name="action" value="update_password" />
                <div>
                    <label for="password" class="text-sm text-neutral-600">Nueva contraseña</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                        class="mt-1 w-full border border-neutral-300 rounded-xl px-3 py-2 text-sm focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15 outline-none"
                    >
                </div>
                <div>
                    <label for="password_confirmation" class="text-sm text-neutral-600">Confirmar contraseña</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                        class="mt-1 w-full border border-neutral-300 rounded-xl px-3 py-2 text-sm focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15 outline-none"
                    >
                </div>
                <button class="mt-2 bg-brand-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-brand-700">
                    Actualizar contraseña
                </button>
            </form>
        </div>
        </main>
    </div>
    </div>
    </body>
</html>
