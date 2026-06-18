<?php
$logoPath = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo.png';
$logoDataUri = null;

if (is_file($logoPath)) {
    $logoContents = @file_get_contents($logoPath);
    if ($logoContents !== false) {
        $logoDataUri = 'data:image/png;base64,' . base64_encode($logoContents);
    }
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ruta Inteligente TI - Acceso</title>
    <link href="/dist/output.css" rel="stylesheet" />
    <link href="/app-shell.css" rel="stylesheet" />
  </head>
  <body class="ri-login-shell text-neutral-900">
    <main class="h-screen px-0 py-0">
      <div class="mx-auto grid h-full w-full max-w-none ri-login-frame md:grid-cols-2">
        <section class="ri-login-hero relative overflow-hidden px-6 py-10 sm:px-10 lg:px-16 lg:py-16">
          <div class="ri-login-hero-glow"></div>
          <?php if ($logoDataUri !== null) : ?>
            <div class="ri-login-corner-logo absolute right-6 top-6 z-10 flex items-center justify-center rounded-2xl px-3 py-2 sm:right-8 sm:top-8">
              <img src="<?php echo htmlspecialchars($logoDataUri, ENT_QUOTES, 'UTF-8'); ?>" alt="Ruta Inteligente TI" class="ri-login-corner-logo-image" />
            </div>
          <?php endif; ?>
          <div class="ri-login-copy relative">
            <div class="ri-login-copy-intro pr-24 sm:pr-28">
              <p class="text-base font-semibold uppercase tracking-[0.3em] text-white">PLATAFORMA DE GESTION</p>
              <h1 class="mt-5 text-5xl font-semibold uppercase leading-none tracking-[0.06em] text-white sm:text-6xl lg:text-7xl">RUTA INTELIGENTE TI</h1>
              <p class="mt-6 text-xl font-semibold uppercase tracking-[0.2em] text-white sm:text-2xl">ACCESO A LA PLATAFORMA</p>
            </div>

            <div class="ri-login-copy-body">
              <p class="ri-login-chip inline-flex items-center rounded-full px-4 py-1.5 text-sm font-semibold uppercase tracking-[0.22em]">
                PLANEA, EJECUTA Y MIDE
              </p>
              <h2 class="mt-7 text-4xl font-semibold uppercase leading-tight tracking-[0.04em] text-white sm:text-5xl lg:text-6xl">
                GESTIONA TUS PLANES ESTRATEGICOS CON LA MISMA INTERFAZ VISUAL DEL SISTEMA.
              </h2>
              <p class="mt-6 max-w-3xl text-lg font-medium uppercase leading-8 tracking-[0.08em] text-white sm:text-xl">
                INGRESA PARA REVISAR OBJETIVOS, MATRICES Y HERRAMIENTAS CLAVE DESDE UN ENTORNO UNIFICADO, CLARO Y RESPONSIVO.
              </p>
            </div>
          </div>
        </section>

        <section class="ri-login-panel flex items-center justify-center px-4 py-8 sm:px-8 lg:px-12">
          <div class="w-full max-w-md">
            <div class="ri-login-card rounded-3xl p-6 shadow-sm sm:p-8">
              <div class="mb-8">
                <p class="text-sm font-medium uppercase tracking-[0.2em] ri-page-subtitle">Bienvenido</p>
                <h3 class="mt-3 text-3xl font-semibold tracking-tight ri-page-title">Inicia sesion</h3>
                <p class="mt-2 text-sm ri-page-subtitle">Accede con tu correo y contrasena para continuar.</p>
              </div>

              <?php if (!empty($error)) : ?>
                <div class="ri-app-alert-danger mb-5 rounded-2xl px-4 py-3 text-sm">
                  <?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
              <?php endif; ?>

              <form class="space-y-5" action="login.php" method="post">
                <div>
                  <label for="login-email" class="ri-app-label block text-sm font-medium">Correo</label>
                  <input
                    id="login-email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    class="ri-app-input mt-2 block w-full rounded-2xl px-4 py-3 text-sm outline-none transition"
                    placeholder="tu@correo.com"
                  />
                </div>

                <div>
                  <label for="login-password" class="ri-app-label block text-sm font-medium">Contrasena</label>
                  <input
                    id="login-password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    class="ri-app-input mt-2 block w-full rounded-2xl px-4 py-3 text-sm outline-none transition"
                    placeholder="••••••••"
                  />
                </div>

                <button
                  type="submit"
                  class="ri-login-submit inline-flex w-full items-center justify-center rounded-2xl px-4 py-3 text-sm font-semibold text-white"
                >
                  Entrar
                </button>
              </form>

              <p class="mt-6 text-center text-xs ri-page-subtitle">
                Al continuar, aceptas nuestras politicas de privacidad y condiciones de uso.
              </p>
            </div>
          </div>
        </section>
      </div>
    </main>
  </body>
</html>
