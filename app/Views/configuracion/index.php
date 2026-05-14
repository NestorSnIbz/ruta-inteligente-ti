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
        <a
            href="dashboard.php"
            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white"
            data-sidebar-item="dashboard"
        >
            <svg viewBox="0 0 24 24" class="h-5 w-5 text-white/85" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9v9a2 2 0 01-2 2h-4v-6H9v6H5a2 2 0 01-2-2v-9z" />
            </svg>
            Dashboard
        </a>
        <div class="mt-1">
            <button
              id="sidebar-projects-toggle"
              type="button"
              class="w-full flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white"
              aria-expanded="false"
              aria-controls="sidebar-projects-panel"
              data-sidebar-item="proyectos"
            >
              <span class="flex items-center gap-3">
                <svg viewBox="0 0 24 24" class="h-5 w-5 text-white/85" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                </svg>
                Proyectos
              </span>
              <svg id="sidebar-projects-chevron" viewBox="0 0 24 24" class="h-4 w-4 text-white/70 transition-transform" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div id="sidebar-projects-panel" class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-in-out">
              <div class="mt-1 space-y-1 pl-2">
                <a href="proyectos.php" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-white/75 hover:bg-white/10 hover:text-white">
                  <span class="h-1.5 w-1.5 rounded-full bg-white/40"></span>
                  Ver todos
                </a>
                <div class="h-px bg-white/10 mx-3"></div>
                <div id="sidebar-recent-projects" class="space-y-1"></div>
              </div>
            </div>
        </div>
        <a
            href="configuracion.php"
            class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white"
            data-sidebar-item="configuracion"
        >
            <svg viewBox="0 0 24 24" class="h-5 w-5 text-white/85" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 110-7 3.5 3.5 0 010 7z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a7.96 7.96 0 00.1-1 7.96 7.96 0 00-.1-1l2-1.6-2-3.4-2.4 1a8.3 8.3 0 00-1.7-1l-.4-2.6H9.1L8.7 7a8.3 8.3 0 00-1.7 1l-2.4-1-2 3.4L4.6 13a7.96 7.96 0 00-.1 1 7.96 7.96 0 00.1 1l-2 1.6 2 3.4 2.4-1a8.3 8.3 0 001.7 1l.4 2.6h5.8l.4-2.6a8.3 8.3 0 001.7-1l2.4 1 2-3.4-2-1.6z" />
            </svg>
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
    <script>
      const recentProjectsKey = "ri:recent-projects";
      const sidebarActiveKey = "ri:sidebar:active";
      const sidebarProjectsOpenKey = "ri:sidebar:projects_open";
      const sidebarProjectsToggle = document.getElementById("sidebar-projects-toggle");
      const sidebarProjectsChevron = document.getElementById("sidebar-projects-chevron");
      const sidebarProjectsPanel = document.getElementById("sidebar-projects-panel");
      const sidebarRecentProjects = document.getElementById("sidebar-recent-projects");

      function getActiveFromPath() {
        const path = (window.location.pathname || "").toLowerCase();
        if (path.endsWith("/dashboard.php")) return "dashboard";
        if (path.endsWith("/proyectos.php")) return "proyectos";
        if (path.endsWith("/configuracion.php")) return "configuracion";
        return "";
      }

      function readActive() {
        try { return window.sessionStorage.getItem(sidebarActiveKey) || ""; } catch (e) { return ""; }
      }

      function writeActive(value) {
        try { window.sessionStorage.setItem(sidebarActiveKey, value); } catch (e) {}
      }

      function setSidebarActive(value) {
        const items = Array.from(document.querySelectorAll("[data-sidebar-item]"));
        for (const el of items) {
          const key = el.getAttribute("data-sidebar-item");
          if (!key) continue;
          const isActive = key === value;
          if (key === "proyectos" && el.tagName.toLowerCase() === "button") {
            el.className = isActive
              ? "w-full flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium bg-white/10 text-white"
              : "w-full flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white";
            continue;
          }
          if (el.tagName.toLowerCase() === "a") {
            el.className = isActive
              ? "flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium bg-white/10 text-white"
              : "flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white";
          }
        }
      }

      function readProjectsOpen() {
        try { return window.localStorage.getItem(sidebarProjectsOpenKey) === "1"; } catch (e) { return false; }
      }

      function writeProjectsOpen(open) {
        try { window.localStorage.setItem(sidebarProjectsOpenKey, open ? "1" : "0"); } catch (e) {}
      }

      function setProjectsPanelOpen(open) {
        if (!sidebarProjectsPanel || !sidebarProjectsToggle) return;
        const shouldOpen = !!open;
        sidebarProjectsToggle.setAttribute("aria-expanded", shouldOpen ? "true" : "false");
        if (sidebarProjectsChevron) {
          sidebarProjectsChevron.style.transform = shouldOpen ? "rotate(180deg)" : "rotate(0deg)";
        }
        if (shouldOpen) sidebarProjectsPanel.style.maxHeight = `${sidebarProjectsPanel.scrollHeight}px`;
        else sidebarProjectsPanel.style.maxHeight = "0px";
        writeProjectsOpen(shouldOpen);
      }

      function readRecentProjects() {
        try {
          const raw = window.localStorage.getItem(recentProjectsKey);
          const parsed = JSON.parse(raw || "[]");
          if (!Array.isArray(parsed)) return [];
          return parsed.filter((x) => x && typeof x === "object" && typeof x.t === "string" && x.t.length > 0 && typeof x.name === "string").slice(0, 10);
        } catch (e) { return []; }
      }

      function renderSidebarRecentProjects() {
        if (!sidebarRecentProjects) return;
        const stored = readRecentProjects().slice(0, 3);
        sidebarRecentProjects.innerHTML = "";
        if (stored.length === 0) {
          const empty = document.createElement("div");
          empty.className = "px-3 py-2 text-sm text-white/60";
          empty.textContent = "Sin proyectos recientes.";
          sidebarRecentProjects.appendChild(empty);
          return;
        }
        for (const p of stored) {
          const a = document.createElement("a");
          a.className = "flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-white/75 hover:bg-white/10 hover:text-white";
          a.href = `detalle-proyecto.php?t=${encodeURIComponent(String(p.t))}`;
          const dot = document.createElement("span");
          dot.className = "h-1.5 w-1.5 rounded-full bg-white/40";
          const label = document.createElement("span");
          label.className = "truncate";
          label.textContent = String(p.name || "—");
          a.appendChild(dot);
          a.appendChild(label);
          sidebarRecentProjects.appendChild(a);
        }
      }

      if (sidebarProjectsToggle) {
        sidebarProjectsToggle.addEventListener("click", () => {
          const expanded = sidebarProjectsToggle.getAttribute("aria-expanded") === "true";
          setProjectsPanelOpen(!expanded);
        });
      }

      const active = getActiveFromPath() || readActive() || "configuracion";
      writeActive(active);
      setSidebarActive(active);
      renderSidebarRecentProjects();
      setProjectsPanelOpen(readProjectsOpen() || active === "proyectos");
    </script>
    </body>
</html>
