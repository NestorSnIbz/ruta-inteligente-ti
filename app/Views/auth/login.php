<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ruta Inteligente TI - Acceso</title>
    <link href="dist/output.css" rel="stylesheet" />
  </head>
  <body class="min-h-screen bg-brand-900 text-neutral-900">
    <main class="min-h-screen grid grid-cols-1 md:grid-cols-2">
      <section class="relative bg-black h-44 md:h-auto overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-brand-600/35 via-black to-black"></div>
        <div class="absolute inset-0 bg-[linear-gradient(135deg,_rgba(6,182,212,0.18),_rgba(37,99,235,0.08),_rgba(0,0,0,0))]"></div>
        <div class="relative h-full w-full p-8 text-white hidden md:flex items-end">
          <div>
            <p class="text-sm text-white/70">Ruta Inteligente TI</p>
            <h2 class="mt-2 text-3xl font-semibold tracking-tight">Planifica. Ejecuta. Mide.</h2>
            <p class="mt-2 max-w-sm text-sm text-white/70">
              Accede al panel para gestionar proyectos y ver un resumen del avance.
            </p>
          </div>
        </div>
      </section>

      <section class="bg-white flex items-center justify-center px-6 py-10">
        <div class="w-full max-w-md">
          <div class="mb-8">
            <h1 class="text-2xl font-semibold tracking-tight text-brand-900">Ruta Inteligente TI</h1>
            <p class="mt-1 text-sm text-neutral-600">Accede con tu cuenta o crea una nueva.</p>
          </div>

          <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm">
            <div class="grid grid-cols-2 rounded-xl bg-brand-50 p-1 text-sm font-medium">
              <button
                id="tab-login"
                type="button"
                class="rounded-lg px-3 py-2 text-brand-900 bg-white shadow-sm"
                data-tab="login"
                aria-controls="panel-login"
                aria-selected="true"
              >
                Iniciar sesión
              </button>
              <button
                id="tab-register"
                type="button"
                class="rounded-lg px-3 py-2 text-brand-900/70"
                data-tab="register"
                aria-controls="panel-register"
                aria-selected="false"
              >
                Registrarse
              </button>
            </div>

            <div class="mt-6">
              <div id="panel-login" role="tabpanel" aria-labelledby="tab-login">
                <form class="space-y-4" action="dashboard.php" method="post">
                  <div>
                    <label for="login-email" class="block text-sm font-medium text-neutral-800">Correo</label>
                    <input
                      id="login-email"
                      name="email"
                      type="email"
                      autocomplete="email"
                      required
                      class="mt-1 block w-full rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm outline-none ring-0 transition focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                      placeholder="tu@correo.com"
                    />
                  </div>

                  <div>
                    <label for="login-password" class="block text-sm font-medium text-neutral-800">Contraseña</label>
                    <input
                      id="login-password"
                      name="password"
                      type="password"
                      autocomplete="current-password"
                      required
                      class="mt-1 block w-full rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                      placeholder="••••••••"
                    />
                  </div>

                  <div class="flex items-center justify-between gap-3">
                    <label class="flex items-center gap-2 text-sm text-neutral-700">
                      <input
                        type="checkbox"
                        name="remember"
                        class="h-4 w-4 rounded border-neutral-300 text-brand-700 focus:ring-brand-600/20"
                      />
                      Recordarme
                    </label>
                    <a href="#" class="text-sm font-medium text-brand-700 hover:underline">¿Olvidaste tu contraseña?</a>
                  </div>

                  <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25"
                  >
                    Entrar
                  </button>
                </form>

                <div class="my-6 flex items-center gap-3">
                  <div class="h-px flex-1 bg-neutral-200"></div>
                  <span class="text-xs font-medium text-neutral-500">o continúa con</span>
                  <div class="h-px flex-1 bg-neutral-200"></div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm font-medium text-neutral-800 transition hover:bg-brand-50"
                  >
                    Google
                  </button>
                  <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm font-medium text-neutral-800 transition hover:bg-brand-50"
                  >
                    GitHub
                  </button>
                </div>
              </div>

              <div id="panel-register" class="hidden" role="tabpanel" aria-labelledby="tab-register">
                <form class="space-y-4" action="dashboard.php" method="post">
                  <div>
                    <label for="register-name" class="block text-sm font-medium text-neutral-800">Nombre</label>
                    <input
                      id="register-name"
                      name="name"
                      type="text"
                      autocomplete="name"
                      required
                      class="mt-1 block w-full rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                      placeholder="Tu nombre"
                    />
                  </div>

                  <div>
                    <label for="register-email" class="block text-sm font-medium text-neutral-800">Correo</label>
                    <input
                      id="register-email"
                      name="email"
                      type="email"
                      autocomplete="email"
                      required
                      class="mt-1 block w-full rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                      placeholder="tu@correo.com"
                    />
                  </div>

                  <div>
                    <label for="register-password" class="block text-sm font-medium text-neutral-800">Contraseña</label>
                    <input
                      id="register-password"
                      name="password"
                      type="password"
                      autocomplete="new-password"
                      required
                      class="mt-1 block w-full rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                      placeholder="••••••••"
                    />
                  </div>

                  <div>
                    <label for="register-password-confirm" class="block text-sm font-medium text-neutral-800">Confirmar contraseña</label>
                    <input
                      id="register-password-confirm"
                      name="password_confirmation"
                      type="password"
                      autocomplete="new-password"
                      required
                      class="mt-1 block w-full rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                      placeholder="••••••••"
                    />
                  </div>

                  <label class="flex items-start gap-2 text-sm text-neutral-700">
                    <input
                      type="checkbox"
                      name="terms"
                      class="mt-1 h-4 w-4 rounded border-neutral-300 text-brand-700 focus:ring-brand-600/20"
                      required
                    />
                    <span>Acepto los términos y condiciones.</span>
                  </label>

                  <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25"
                  >
                    Crear cuenta
                  </button>
                </form>
              </div>
            </div>
          </div>

          <p class="mt-6 text-xs text-neutral-500">
            Al continuar, aceptas nuestras políticas de privacidad y condiciones de uso.
          </p>
        </div>
      </section>
    </main>

    <script>
      const tabLogin = document.getElementById("tab-login");
      const tabRegister = document.getElementById("tab-register");
      const panelLogin = document.getElementById("panel-login");
      const panelRegister = document.getElementById("panel-register");

      function setActiveTab(tab) {
        const isLogin = tab === "login";

        tabLogin.className = isLogin
          ? "rounded-lg px-3 py-2 text-brand-900 bg-white shadow-sm"
          : "rounded-lg px-3 py-2 text-brand-900/70";
        tabRegister.className = !isLogin
          ? "rounded-lg px-3 py-2 text-brand-900 bg-white shadow-sm"
          : "rounded-lg px-3 py-2 text-brand-900/70";

        tabLogin.setAttribute("aria-selected", isLogin ? "true" : "false");
        tabRegister.setAttribute("aria-selected", !isLogin ? "true" : "false");

        if (isLogin) {
          panelLogin.classList.remove("hidden");
          panelRegister.classList.add("hidden");
        } else {
          panelRegister.classList.remove("hidden");
          panelLogin.classList.add("hidden");
        }
      }

      tabLogin.addEventListener("click", () => setActiveTab("login"));
      tabRegister.addEventListener("click", () => setActiveTab("register"));
    </script>
  </body>
</html>
