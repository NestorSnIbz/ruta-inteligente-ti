<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Configuración - Ruta Inteligente TI</title>
        <link href="dist/output.css" rel="stylesheet" />
        <link href="/app-shell.css" rel="stylesheet" />
    </head>
    <body class="ri-dashboard-shell min-h-screen text-neutral-900">
    <?php
        $nombre = is_array($authUser ?? null) ? (string) ($authUser['nombre'] ?? '') : '';
        $success = $success ?? null;
        $error = $error ?? null;
    ?>
    <div class="min-h-screen grid grid-cols-1 md:grid-cols-[16rem_1fr]">
    <?php
      $sidebarActive = 'configuracion';
      $sidebarSeedProjects = [];
      include __DIR__ . '/../layouts/sidebar.php';
    ?>
    <div class="min-h-screen flex flex-col">
        <header class="ri-dashboard-header">
        <div class="px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Configuración</h1>
                <p class="mt-1 text-sm text-slate-500">Administra tu perfil y la seguridad de tu cuenta.</p>
            </div>
        </div>
        </header>
        <main class="flex-1 px-6 py-8 space-y-6">
        <?php if (!empty($error)) : ?>
            <div class="ri-app-alert-danger rounded-[24px] px-6 py-4 text-sm">
                <?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)) : ?>
            <div class="ri-app-alert-success rounded-[24px] px-6 py-4 text-sm">
                <?php echo htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        <div class="ri-dashboard-card rounded-[24px] p-6">
            <h2 class="mb-2 text-lg font-semibold text-slate-900">Perfil</h2>
            <p class="mb-4 text-sm text-slate-500">Actualiza la información visible de tu cuenta.</p>
            <form class="space-y-4" action="configuracion.php" method="post">
                <input type="hidden" name="action" value="update_name" />
                <div>
                    <label for="nombre" class="ri-app-label text-sm">Nombre</label>
                    <input
                        id="nombre"
                        name="nombre"
                        type="text"
                        value="<?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?>"
                        required
                        class="ri-app-input mt-1 w-full rounded-2xl px-4 py-3 text-sm outline-none transition"
                    >
                </div>
                <button class="ri-dashboard-primary-btn mt-2 inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-medium">
                    Guardar cambios
                </button>
            </form>
        </div>
        <div class="ri-dashboard-card rounded-[24px] p-6">
            <h2 class="mb-2 text-lg font-semibold text-slate-900">Cambiar contraseña</h2>
            <p class="mb-4 text-sm text-slate-500">Define una contraseña nueva para reforzar la seguridad de tu cuenta.</p>
            <form class="space-y-4" action="configuracion.php" method="post">
                <input type="hidden" name="action" value="update_password" />
                <div>
                    <label for="password" class="ri-app-label text-sm">Nueva contraseña</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                        class="ri-app-input mt-1 w-full rounded-2xl px-4 py-3 text-sm outline-none transition"
                    >
                </div>
                <div>
                    <label for="password_confirmation" class="ri-app-label text-sm">Confirmar contraseña</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                        class="ri-app-input mt-1 w-full rounded-2xl px-4 py-3 text-sm outline-none transition"
                    >
                </div>
                <button class="ri-dashboard-primary-btn mt-2 inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-medium">
                    Actualizar contraseña
                </button>
            </form>
        </div>
        </main>
    </div>
    </div>
    </body>
</html>
