<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ruta Inteligente TI - Dashboard</title>
    <link href="dist/output.css" rel="stylesheet" />
  </head>
  <body class="min-h-screen bg-neutral-50 text-neutral-900">
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
          <a
            href="dashboard.php"
            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium bg-white/10"
          >
            <span class="h-2 w-2 rounded-full bg-accent-500"></span>
            Dashboard
          </a>
          <a href="#" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
            <span class="h-2 w-2 rounded-full bg-white/30"></span>
            Proyectos
          </a>
          <a href="#" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
            <span class="h-2 w-2 rounded-full bg-white/30"></span>
            Configuración
          </a>
        </nav>
      </aside>

      <div class="min-h-screen flex flex-col">
        <header class="bg-white border-b border-neutral-200">
          <div class="px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex-1 max-w-xl">
              <label class="block">
                <span class="sr-only">Buscar</span>
                <div class="relative">
                  <span class="absolute inset-y-0 left-3 flex items-center text-neutral-500">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3m1.8-5.2a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </span>
                  <input
                    type="search"
                    placeholder="Buscar proyectos, reportes, módulos…"
                    class="w-full rounded-xl border border-neutral-300 bg-white py-2 pl-10 pr-3 text-sm outline-none transition focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                  />
                </div>
              </label>
            </div>

            <div class="flex items-center gap-3">
              <button type="button" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 hover:bg-brand-50">
                <span class="sr-only">Notificaciones</span>
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0h6z" />
                </svg>
                <span class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-accent-500 text-[10px] font-semibold text-white grid place-items-center">3</span>
              </button>

              <div class="relative">
                <button
                  id="user-menu-button"
                  type="button"
                  class="inline-flex items-center gap-3 rounded-xl border border-neutral-200 bg-white px-3 py-2 text-sm font-medium text-neutral-800 hover:bg-brand-50"
                  aria-expanded="false"
                  aria-controls="user-menu"
                >
                  <span class="h-8 w-8 rounded-full bg-brand-600 text-white grid place-items-center text-xs font-semibold">JM</span>
                  <span class="hidden sm:block">Junior Mamani</span>
                  <svg viewBox="0 0 24 24" class="h-4 w-4 text-neutral-500" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <div
                  id="user-menu"
                  class="hidden absolute right-0 mt-2 w-52 overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm"
                  role="menu"
                  aria-labelledby="user-menu-button"
                >
                  <a href="#" class="block px-4 py-2.5 text-sm text-neutral-700 hover:bg-brand-50" role="menuitem">Mi perfil</a>
                  <a href="#" class="block px-4 py-2.5 text-sm text-neutral-700 hover:bg-brand-50" role="menuitem">Configuración</a>
                  <div class="h-px bg-neutral-200"></div>
                  <a href="login.php" class="block px-4 py-2.5 text-sm font-medium text-brand-700 hover:bg-brand-50" role="menuitem">
                    Cerrar sesión
                  </a>
                </div>
              </div>
            </div>
          </div>
        </header>

        <main class="flex-1 px-6 py-8">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
              <p class="mt-1 text-sm text-neutral-600">Resumen general de tus proyectos.</p>
            </div>
            <a
              href="#"
              class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25"
            >
              Nuevo proyecto
            </a>
          </div>

          <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
              <div class="text-sm font-medium text-neutral-600">Total proyectos</div>
              <div class="mt-2 text-3xl font-semibold text-brand-900">12</div>
              <div class="mt-2 text-xs text-neutral-500">Incluye proyectos activos y archivados.</div>
            </div>
            <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
              <div class="text-sm font-medium text-neutral-600">Proyectos activos</div>
              <div class="mt-2 text-3xl font-semibold text-brand-900">5</div>
              <div class="mt-2 text-xs text-neutral-500">En ejecución durante este periodo.</div>
            </div>
            <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
              <div class="text-sm font-medium text-neutral-600">Proyectos compartidos</div>
              <div class="mt-2 text-3xl font-semibold text-brand-900">3</div>
              <div class="mt-2 text-xs text-neutral-500">Colaboración con tu equipo.</div>
            </div>
          </section>

          <section class="mt-6 rounded-2xl border border-neutral-200 bg-white shadow-sm">
            <div class="px-6 py-4 border-b border-neutral-200 flex items-center justify-between gap-3">
              <h2 class="text-sm font-semibold text-neutral-900">Proyectos recientes</h2>
              <a href="#" class="text-sm font-medium text-brand-700 hover:underline">Ver todos</a>
            </div>

            <div class="divide-y divide-neutral-200">
              <div class="px-6 py-4 flex items-center justify-between gap-4">
                <div>
                  <div class="text-sm font-medium text-neutral-900">Plan Estratégico 2026</div>
                  <div class="mt-1 text-xs text-neutral-500">Actualizado hoy</div>
                </div>
                <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-700">Activo</span>
              </div>
              <div class="px-6 py-4 flex items-center justify-between gap-4">
                <div>
                  <div class="text-sm font-medium text-neutral-900">Análisis FODA - Q2</div>
                  <div class="mt-1 text-xs text-neutral-500">Actualizado hace 2 días</div>
                </div>
                <span class="inline-flex items-center rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-700">Borrador</span>
              </div>
              <div class="px-6 py-4 flex items-center justify-between gap-4">
                <div>
                  <div class="text-sm font-medium text-neutral-900">Objetivos Estratégicos</div>
                  <div class="mt-1 text-xs text-neutral-500">Actualizado hace 1 semana</div>
                </div>
                <span class="inline-flex items-center rounded-full bg-accent-500/10 px-3 py-1 text-xs font-medium text-accent-600">Compartido</span>
              </div>
            </div>
          </section>
        </main>
      </div>
    </div>

    <script>
      const userMenuButton = document.getElementById("user-menu-button");
      const userMenu = document.getElementById("user-menu");

      function closeUserMenu() {
        userMenu.classList.add("hidden");
        userMenuButton.setAttribute("aria-expanded", "false");
      }

      function toggleUserMenu() {
        const isHidden = userMenu.classList.contains("hidden");
        if (isHidden) {
          userMenu.classList.remove("hidden");
          userMenuButton.setAttribute("aria-expanded", "true");
        } else {
          closeUserMenu();
        }
      }

      userMenuButton.addEventListener("click", (event) => {
        event.stopPropagation();
        toggleUserMenu();
      });

      document.addEventListener("click", () => {
        closeUserMenu();
      });
    </script>
  </body>
</html>
