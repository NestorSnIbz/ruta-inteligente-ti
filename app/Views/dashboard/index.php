<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ruta Inteligente TI - Dashboard</title>
    <link href="dist/output.css" rel="stylesheet" />
  </head>
  <body class="min-h-screen bg-neutral-50 text-neutral-900">
    <?php
      $nombre = is_array($authUser ?? null) ? (string) ($authUser['nombre'] ?? '') : '';
      $correo = is_array($authUser ?? null) ? (string) ($authUser['email'] ?? '') : '';
      $dashboardPayload = is_array($dashboardPayload ?? null) ? $dashboardPayload : [];
      $dashboardError = (string) ($dashboardError ?? '');
      $metrics = is_array($dashboardPayload['metrics'] ?? null) ? $dashboardPayload['metrics'] : [];
      $initialProjects = is_array($dashboardPayload['projects'] ?? null) ? $dashboardPayload['projects'] : [];
      $initialUpdatedAt = (string) ($dashboardPayload['updated_at'] ?? '');
      $initials = '';
      if ($nombre !== '') {
        $parts = preg_split('/\s+/', trim($nombre)) ?: [];
        $initials = strtoupper(mb_substr($parts[0] ?? '', 0, 1, 'UTF-8') . mb_substr($parts[1] ?? '', 0, 1, 'UTF-8'));
      }
      if ($initials === '' && $correo !== '') {
        $initials = strtoupper(mb_substr($correo, 0, 2, 'UTF-8'));
      }
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
            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium bg-white/10"
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
            <div
              id="sidebar-projects-panel"
              class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-in-out"
            >
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
          <a href="configuracion.php" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
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
                    id="dashboard-search"
                    type="search"
                    placeholder="Buscar planes estratégicos…"
                    class="w-full rounded-xl border border-neutral-300 bg-white py-2 pl-10 pr-3 text-sm outline-none transition focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                  />
                </div>
              </label>
            </div>

            <div class="flex items-center gap-3">
              <button type="button" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 hover:bg-brand-50">
                <span class="sr-only">Pendientes</span>
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0h6z" />
                </svg>
                <span id="dashboard-notifications" class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-accent-500 text-[10px] font-semibold text-white grid place-items-center">0</span>
              </button>

              <div class="relative">
                <button
                  id="user-menu-button"
                  type="button"
                  class="inline-flex items-center gap-3 rounded-xl border border-neutral-200 bg-white px-3 py-2 text-sm font-medium text-neutral-800 hover:bg-brand-50"
                  aria-expanded="false"
                  aria-controls="user-menu"
                >
                  <span class="h-8 w-8 rounded-full bg-brand-600 text-white grid place-items-center text-xs font-semibold"><?php echo htmlspecialchars($initials, ENT_QUOTES, 'UTF-8'); ?></span>
                  <span class="hidden sm:block"><?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?></span>
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
                  <div class="px-4 py-3 text-sm">
                    <div class="font-medium text-neutral-900"><?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="mt-0.5 text-xs text-neutral-500"><?php echo htmlspecialchars($correo, ENT_QUOTES, 'UTF-8'); ?></div>
                  </div>
                  <div class="h-px bg-neutral-200"></div>
                  <a href="configuracion.php" class="block px-4 py-2.5 text-sm text-neutral-700 hover:bg-brand-50" role="menuitem">Mi perfil</a>
                  <a href="configuracion.php" class="block px-4 py-2.5 text-sm text-neutral-700 hover:bg-brand-50" role="menuitem">Configuración</a>
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
              <p class="mt-1 text-sm text-neutral-600">
                Bienvenido, <?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($correo, ENT_QUOTES, 'UTF-8'); ?>).
              </p>
            </div>
            <div class="flex items-center gap-3">
              <a
                href="nuevo-proyecto.php"
                class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25"
              >
                Nuevo plan estratégico
              </a>
            </div>
          </div>

          <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-neutral-500">
            <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 border border-neutral-200">
              <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
              <span id="dashboard-updated-label">Actualizado</span>
            </span>
            <?php if ($dashboardError !== '') : ?>
              <span class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1.5 border border-red-100 text-red-700">
                <?php echo htmlspecialchars($dashboardError, ENT_QUOTES, 'UTF-8'); ?>
              </span>
            <?php endif; ?>
          </div>

          <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
              <div class="text-sm font-medium text-neutral-600">Total planes estratégicos</div>
              <div id="metric-total-planes" class="mt-2 text-3xl font-semibold text-brand-900"><?php echo (int) ($metrics['total_rutas'] ?? 0); ?></div>
              <div class="mt-2 text-xs text-neutral-500">Planes creados por tu cuenta.</div>
            </div>
            <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
              <div class="text-sm font-medium text-neutral-600">Usuarios activos</div>
              <div id="metric-usuarios-activos" class="mt-2 text-3xl font-semibold text-brand-900"><?php echo (int) ($metrics['usuarios_activos'] ?? 0); ?></div>
              <div class="mt-2 text-xs text-neutral-500">Usuarios con actividad reciente (según datos disponibles).</div>
            </div>
          </section>

          <section class="mt-6 rounded-2xl border border-neutral-200 bg-white shadow-sm">
            <div class="px-6 py-4 border-b border-neutral-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-neutral-900">Planes estratégicos</h2>
                <span id="dashboard-table-count" class="text-xs text-neutral-500"></span>
              </div>
              <div class="text-xs text-neutral-500">Listado general</div>
            </div>

            <div class="px-6 py-4 border-b border-neutral-200 bg-neutral-50">
              <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="text-xs font-medium text-neutral-600">Plan estratégico</div>
                <div class="hidden sm:block text-xs font-medium text-neutral-600">Estado</div>
                <div class="hidden sm:block text-xs font-medium text-neutral-600 text-right">Acciones</div>
              </div>
            </div>

            <div id="dashboard-table-empty" class="hidden px-6 py-10 text-center text-sm text-neutral-600">
              No hay planes estratégicos para mostrar.
            </div>

            <div id="dashboard-table" class="divide-y divide-neutral-200"></div>
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

      const initialPayload = <?php echo json_encode($dashboardPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
      let currentPayload = initialPayload && typeof initialPayload === "object" ? initialPayload : { metrics: {}, projects: [] };

      const metricEls = {
        totalPlanes: document.getElementById("metric-total-planes"),
        usuariosActivos: document.getElementById("metric-usuarios-activos"),
      };

      const updatedLabel = document.getElementById("dashboard-updated-label");
      const notificationsEl = document.getElementById("dashboard-notifications");
      const tableEl = document.getElementById("dashboard-table");
      const tableEmptyEl = document.getElementById("dashboard-table-empty");
      const tableCountEl = document.getElementById("dashboard-table-count");
      const searchEl = document.getElementById("dashboard-search");
      const sidebarProjectsToggle = document.getElementById("sidebar-projects-toggle");
      const sidebarProjectsChevron = document.getElementById("sidebar-projects-chevron");
      const sidebarProjectsPanel = document.getElementById("sidebar-projects-panel");
      const sidebarRecentProjects = document.getElementById("sidebar-recent-projects");
      const recentProjectsKey = "ri:recent-projects";

      function setText(el, value) {
        if (!el) return;
        el.textContent = value;
      }

      function formatUpdatedAt(iso) {
        if (!iso) return "Actualizado";
        const d = new Date(iso);
        if (Number.isNaN(d.getTime())) return "Actualizado";
        return `Actualizado ${d.toLocaleString()}`;
      }

      function statusPill(status) {
        if (status === "Activo") {
          return { label: "Activo", cls: "bg-emerald-600/10 text-emerald-700" };
        }
        if (status === "En progreso") {
          return { label: "En progreso", cls: "bg-brand-50 text-brand-700" };
        }
        return { label: "Borrador", cls: "bg-neutral-100 text-neutral-700" };
      }

      function renderMetrics(payload) {
        const m = payload && payload.metrics && typeof payload.metrics === "object" ? payload.metrics : {};
        setText(metricEls.totalPlanes, String(m.total_rutas ?? 0));
        setText(metricEls.usuariosActivos, String(m.usuarios_activos ?? 0));

        const inProgress = Number(m.rutas_inactivas ?? 0);
        setText(notificationsEl, String(inProgress));

        setText(updatedLabel, formatUpdatedAt(payload.updated_at || ""));
      }

      function buildRow(project) {
        const status = String(project.status ?? "Borrador");
        const pill = statusPill(status);

        const row = document.createElement("div");
        row.className = "px-6 py-4";
        row.dataset.projectName = String(project.nombre ?? "").toLowerCase();

        const grid = document.createElement("div");
        grid.className = "grid grid-cols-1 gap-3 sm:grid-cols-3 sm:items-center";

        const name = document.createElement("div");
        name.className = "text-sm font-medium text-neutral-900";
        const link = document.createElement("a");
        link.className = "hover:underline";
        link.href = project && project.token ? `detalle-proyecto.php?t=${encodeURIComponent(String(project.token))}` : "proyectos.php";
        link.textContent = String(project.nombre ?? "—");
        link.addEventListener("click", () => pushRecentProject(project));
        name.appendChild(link);

        const statusEl = document.createElement("div");
        statusEl.className = "hidden sm:block";
        const pillEl = document.createElement("span");
        pillEl.className = `inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ${pill.cls}`;
        pillEl.textContent = pill.label;
        statusEl.appendChild(pillEl);

        const actions = document.createElement("div");
        actions.className = "hidden sm:flex justify-end";
        const viewBtn = document.createElement("a");
        viewBtn.href = project && project.token ? `detalle-proyecto.php?t=${encodeURIComponent(String(project.token))}` : "proyectos.php";
        viewBtn.className = "inline-flex items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 py-2 text-xs font-semibold text-neutral-800 hover:bg-brand-50";
        viewBtn.textContent = "Ver detalle";
        viewBtn.addEventListener("click", () => pushRecentProject(project));
        actions.appendChild(viewBtn);

        grid.appendChild(name);
        grid.appendChild(statusEl);
        grid.appendChild(actions);

        const mobileMeta = document.createElement("div");
        mobileMeta.className = "sm:hidden flex items-center justify-between gap-3";

        const mobileLeft = document.createElement("div");
        mobileLeft.className = "flex items-center gap-2";
        const mobilePill = document.createElement("span");
        mobilePill.className = `inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ${pill.cls}`;
        mobilePill.textContent = pill.label;
        mobileLeft.appendChild(mobilePill);

        const mobileRight = document.createElement("div");
        mobileRight.className = "text-xs";
        const mobileLink = document.createElement("a");
        mobileLink.href = project && project.token ? `detalle-proyecto.php?t=${encodeURIComponent(String(project.token))}` : "proyectos.php";
        mobileLink.className = "font-semibold text-brand-700 hover:underline";
        mobileLink.textContent = "Ver";
        mobileLink.addEventListener("click", () => pushRecentProject(project));
        mobileRight.appendChild(mobileLink);

        mobileMeta.appendChild(mobileLeft);
        mobileMeta.appendChild(mobileRight);

        row.appendChild(grid);
        row.appendChild(mobileMeta);

        return row;
      }

      function readRecentProjects() {
        try {
          const raw = window.localStorage.getItem(recentProjectsKey);
          const parsed = JSON.parse(raw || "[]");
          if (!Array.isArray(parsed)) return [];
          return parsed
            .filter((x) => x && typeof x === "object" && typeof x.t === "string" && x.t.length > 0 && typeof x.name === "string")
            .slice(0, 10);
        } catch (e) {
          return [];
        }
      }

      function writeRecentProjects(list) {
        try {
          window.localStorage.setItem(recentProjectsKey, JSON.stringify(list));
        } catch (e) {}
      }

      function pushRecentProject(project) {
        const t = project && project.token ? String(project.token) : "";
        const name = project && project.nombre ? String(project.nombre) : "";
        if (!t || !name) return;

        const now = Date.now();
        const current = readRecentProjects();
        const filtered = current.filter((x) => x.t !== t);
        filtered.unshift({ t, name, ts: now });
        writeRecentProjects(filtered.slice(0, 10));
      }

      function renderSidebarRecentProjects(payload) {
        if (!sidebarRecentProjects) return;
        const projects = Array.isArray(payload.projects) ? payload.projects : [];
        const byToken = new Map();
        for (const p of projects) {
          if (!p || typeof p !== "object") continue;
          const t = p.token ? String(p.token) : "";
          if (!t) continue;
          byToken.set(t, p);
        }

        const stored = readRecentProjects();
        const resolved = [];
        for (const item of stored) {
          const p = byToken.get(item.t);
          if (p) resolved.push(p);
          if (resolved.length >= 3) break;
        }

        if (resolved.length < 3) {
          for (const p of projects) {
            if (!p || typeof p !== "object") continue;
            if (!p.token) continue;
            if (resolved.some((x) => String(x.token) === String(p.token))) continue;
            resolved.push(p);
            if (resolved.length >= 3) break;
          }
        }

        sidebarRecentProjects.innerHTML = "";
        if (resolved.length === 0) {
          const empty = document.createElement("div");
          empty.className = "px-3 py-2 text-sm text-white/60";
          empty.textContent = "Sin proyectos recientes.";
          sidebarRecentProjects.appendChild(empty);
          return;
        }

        for (const p of resolved) {
          const a = document.createElement("a");
          a.className = "flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-white/75 hover:bg-white/10 hover:text-white";
          a.href = `detalle-proyecto.php?t=${encodeURIComponent(String(p.token))}`;
          a.addEventListener("click", () => pushRecentProject(p));

          const dot = document.createElement("span");
          dot.className = "h-1.5 w-1.5 rounded-full bg-white/40";

          const label = document.createElement("span");
          label.className = "truncate";
          label.textContent = String(p.nombre ?? "—");

          a.appendChild(dot);
          a.appendChild(label);
          sidebarRecentProjects.appendChild(a);
        }

        if (sidebarProjectsToggle && sidebarProjectsPanel && sidebarProjectsToggle.getAttribute("aria-expanded") === "true") {
          sidebarProjectsPanel.style.maxHeight = `${sidebarProjectsPanel.scrollHeight}px`;
        }
      }

      function setProjectsPanelOpen(open) {
        if (!sidebarProjectsPanel || !sidebarProjectsToggle) return;
        const shouldOpen = !!open;
        sidebarProjectsToggle.setAttribute("aria-expanded", shouldOpen ? "true" : "false");
        if (sidebarProjectsChevron) {
          sidebarProjectsChevron.style.transform = shouldOpen ? "rotate(180deg)" : "rotate(0deg)";
        }
        if (shouldOpen) {
          sidebarProjectsPanel.style.maxHeight = `${sidebarProjectsPanel.scrollHeight}px`;
        } else {
          sidebarProjectsPanel.style.maxHeight = "0px";
        }
      }

      if (sidebarProjectsToggle) {
        sidebarProjectsToggle.addEventListener("click", () => {
          const expanded = sidebarProjectsToggle.getAttribute("aria-expanded") === "true";
          setProjectsPanelOpen(!expanded);
        });
      }

      function renderTable(payload) {
        const projects = Array.isArray(payload.projects) ? payload.projects : [];
        tableEl.innerHTML = "";
        for (const p of projects) {
          if (!p || typeof p !== "object") continue;
          tableEl.appendChild(buildRow(p));
        }
        applyFilters();
      }

      function applyFilters() {
        const q = (searchEl && searchEl.value ? searchEl.value : "").trim().toLowerCase();
        const rows = Array.from(tableEl.querySelectorAll("[data-project-name]"));
        let visibleCount = 0;

        for (const row of rows) {
          const name = row.dataset.projectName || "";
          const matchesText = q === "" || name.includes(q);

          if (matchesText) {
            row.classList.remove("hidden");
            visibleCount++;
          } else {
            row.classList.add("hidden");
          }
        }

        if (tableEmptyEl) {
          if (rows.length === 0 || visibleCount === 0) {
            tableEmptyEl.classList.remove("hidden");
          } else {
            tableEmptyEl.classList.add("hidden");
          }
        }

        if (tableCountEl) {
          tableCountEl.textContent = `${visibleCount} / ${rows.length}`;
        }
      }

      if (searchEl) {
        searchEl.addEventListener("input", () => {
          applyFilters();
        });
      }

      async function refreshDashboard() {
        try {
          const res = await fetch("dashboard.php?format=json", { headers: { Accept: "application/json" } });
          const json = await res.json();
          if (!json || typeof json !== "object" || !json.payload) return;
          currentPayload = json.payload;
          renderMetrics(currentPayload);
          renderTable(currentPayload);
          renderSidebarRecentProjects(currentPayload);
        } catch (e) {}
      }

      renderMetrics(currentPayload);
      renderTable(currentPayload);
      renderSidebarRecentProjects(currentPayload);
      setProjectsPanelOpen(false);

      setInterval(() => {
        if (document.visibilityState === "visible") {
          refreshDashboard();
        }
      }, 30000);
    </script>
  </body>
</html>
