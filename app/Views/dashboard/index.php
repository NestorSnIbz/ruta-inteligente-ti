<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ruta Inteligente TI - Dashboard</title>
    <link href="dist/output.css" rel="stylesheet" />
    <link href="/app-shell.css" rel="stylesheet" />
  </head>
  <body class="ri-dashboard-shell min-h-screen text-neutral-900">
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
      <?php
        $sidebarActive = 'dashboard';
        $sidebarSeedProjects = $initialProjects;
        include __DIR__ . '/../layouts/sidebar.php';
      ?>

      <div class="min-h-screen flex flex-col">
        <header class="ri-dashboard-header">
          <div class="px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex-1 max-w-xl">
              <label class="block">
                <span class="sr-only">Buscar</span>
                <div class="relative">
                  <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3m1.8-5.2a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </span>
                  <input
                    id="dashboard-search"
                    type="search"
                    placeholder="Buscar planes estratégicos…"
                    class="ri-dashboard-search w-full rounded-2xl py-2.5 pl-10 pr-3 text-sm outline-none transition"
                  />
                </div>
              </label>
            </div>

            <div class="flex items-center gap-3">
            </div>
          </div>
        </header>

        <main class="flex-1 px-6 py-8">
          <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
              <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
              <p class="mt-1 text-sm text-neutral-600">
                Bienvenido, <?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($correo, ENT_QUOTES, 'UTF-8'); ?>).
              </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
              <a
                href="nuevo-proyecto.php"
                class="ri-dashboard-primary-btn inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-semibold focus:outline-none"
              >
                Nuevo plan estratégico
              </a>
            </div>
          </div>

          <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-slate-500">
            <span class="ri-dashboard-chip inline-flex items-center gap-2 rounded-full px-3 py-1.5">
              <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
              <span id="dashboard-updated-label">Actualizado</span>
            </span>
            <?php if ($dashboardError !== '') : ?>
              <span class="ri-dashboard-chip-danger inline-flex items-center gap-2 rounded-full px-3 py-1.5">
                <?php echo htmlspecialchars($dashboardError, ENT_QUOTES, 'UTF-8'); ?>
              </span>
            <?php endif; ?>
          </div>

          <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="ri-dashboard-card rounded-[24px] p-5">
              <div class="ri-dashboard-card-title text-sm font-medium">Total planes estratégicos</div>
              <div id="metric-total-planes" class="ri-dashboard-card-value mt-2 text-3xl font-semibold"><?php echo (int) ($metrics['total_rutas'] ?? 0); ?></div>
              <div class="ri-dashboard-card-note mt-2 text-xs">Planes creados por tu cuenta.</div>
            </div>
            <div class="ri-dashboard-card rounded-[24px] p-5">
              <div class="ri-dashboard-card-title text-sm font-medium">Usuarios activos</div>
              <div id="metric-usuarios-activos" class="ri-dashboard-card-value mt-2 text-3xl font-semibold"><?php echo (int) ($metrics['usuarios_activos'] ?? 0); ?></div>
              <div class="ri-dashboard-card-note mt-2 text-xs">Usuarios con actividad reciente (según datos disponibles).</div>
            </div>
          </section>

          <section class="ri-dashboard-table mt-6 rounded-[24px]">
            <div class="ri-dashboard-table-head px-6 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-slate-900">Planes estratégicos</h2>
                <span id="dashboard-table-count" class="text-xs text-slate-500"></span>
              </div>
              <div class="text-xs text-slate-500">Listado general</div>
            </div>

            <div class="ri-dashboard-table-subhead px-6 py-4">
              <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:items-center">
                <div class="text-xs font-medium text-slate-600">Plan estratégico</div>
                <div class="hidden sm:block text-xs font-medium text-slate-600">Rol</div>
                <div class="hidden sm:block text-xs font-medium text-slate-600 text-right">Acciones</div>
              </div>
            </div>

            <div id="dashboard-table-empty" class="ri-dashboard-table-empty hidden px-6 py-10 text-center text-sm">
              No hay planes estratégicos para mostrar.
            </div>

            <div id="dashboard-table" class="divide-y divide-slate-200/80"></div>
          </section>
        </main>
      </div>
    </div>

    <script>
      const initialPayload = <?php echo json_encode($dashboardPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
      let currentPayload = initialPayload && typeof initialPayload === "object" ? initialPayload : { metrics: {}, projects: [] };

      const metricEls = {
        totalPlanes: document.getElementById("metric-total-planes"),
        usuariosActivos: document.getElementById("metric-usuarios-activos"),
      };

      const updatedLabel = document.getElementById("dashboard-updated-label");
      const tableEl = document.getElementById("dashboard-table");
      const tableEmptyEl = document.getElementById("dashboard-table-empty");
      const tableCountEl = document.getElementById("dashboard-table-count");
      const searchEl = document.getElementById("dashboard-search");
      function pushRecentProject(project) {
        const id = project && (project.id_proyecto !== undefined || project.id !== undefined) ? Number(project.id_proyecto ?? project.id) : 0;
        const name = project && project.nombre ? String(project.nombre) : "";
        if (!id || !name) return;
        if (window.RISidebar && typeof window.RISidebar.pushRecentProject === "function") {
          window.RISidebar.pushRecentProject(id, name);
        }
      }

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

      function renderMetrics(payload) {
        const m = payload && payload.metrics && typeof payload.metrics === "object" ? payload.metrics : {};
        setText(metricEls.totalPlanes, String(m.total_rutas ?? 0));
        setText(metricEls.usuariosActivos, String(m.usuarios_activos ?? 0));

        setText(updatedLabel, formatUpdatedAt(payload.updated_at || ""));
      }

      function buildRow(project) {
        const row = document.createElement("div");
        row.className = "ri-dashboard-row px-6 py-4";
        row.dataset.projectName = String(project.nombre ?? "").toLowerCase();
        row.dataset.projectRole = String(project.rol ?? "").toLowerCase();

        const grid = document.createElement("div");
        grid.className = "grid grid-cols-1 gap-3 sm:grid-cols-3 sm:items-center";

        const name = document.createElement("div");
        name.className = "text-sm font-medium text-slate-900";
        const link = document.createElement("a");
        link.className = "ri-dashboard-link transition-colors hover:underline";
        link.href = project && project.token ? `detalle-proyecto.php?t=${encodeURIComponent(String(project.token))}` : "proyectos.php";
        link.textContent = String(project.nombre ?? "—");
        link.addEventListener("click", () => pushRecentProject(project));
        name.appendChild(link);

        const role = document.createElement("div");
        role.className = "hidden sm:flex";
        const roleBadge = document.createElement("span");
        const roleLabel = String(project.rol ?? "Invitado");
        const isCreator = roleLabel.toLowerCase() === "creador";
        roleBadge.className = isCreator
          ? "inline-flex items-center rounded-xl bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700"
          : "inline-flex items-center rounded-xl bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700";
        roleBadge.textContent = roleLabel;
        role.appendChild(roleBadge);

        const actions = document.createElement("div");
        actions.className = "hidden sm:flex justify-end";
        const viewBtn = document.createElement("a");
        viewBtn.href = project && project.token ? `detalle-proyecto.php?t=${encodeURIComponent(String(project.token))}` : "proyectos.php";
        viewBtn.className = "ri-dashboard-outline-btn inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-semibold";
        viewBtn.textContent = "Ver detalle";
        viewBtn.addEventListener("click", () => pushRecentProject(project));
        actions.appendChild(viewBtn);

        grid.appendChild(name);
        grid.appendChild(role);
        grid.appendChild(actions);

        const mobileMeta = document.createElement("div");
        mobileMeta.className = "sm:hidden flex items-center justify-between gap-3";
        const mobileRole = document.createElement("span");
        const mobileRoleLabel = String(project.rol ?? "Invitado");
        const mobileIsCreator = mobileRoleLabel.toLowerCase() === "creador";
        mobileRole.className = mobileIsCreator
          ? "inline-flex items-center rounded-xl bg-brand-50 px-3 py-1 text-[11px] font-semibold text-brand-700"
          : "inline-flex items-center rounded-xl bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-700";
        mobileRole.textContent = mobileRoleLabel;
        const mobileLink = document.createElement("a");
        mobileLink.href = project && project.token ? `detalle-proyecto.php?t=${encodeURIComponent(String(project.token))}` : "proyectos.php";
        mobileLink.className = "ri-dashboard-mobile-link text-xs font-semibold hover:underline";
        mobileLink.textContent = "Ver";
        mobileLink.addEventListener("click", () => pushRecentProject(project));
        mobileMeta.appendChild(mobileRole);
        mobileMeta.appendChild(mobileLink);

        row.appendChild(grid);
        row.appendChild(mobileMeta);

        return row;
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
          const role = row.dataset.projectRole || "";
          const matchesText = q === "" || name.includes(q) || role.includes(q);

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
        } catch (e) {}
      }

      renderMetrics(currentPayload);
      renderTable(currentPayload);

      setInterval(() => {
        if (document.visibilityState === "visible") {
          refreshDashboard();
        }
      }, 30000);
    </script>
  </body>
</html>
