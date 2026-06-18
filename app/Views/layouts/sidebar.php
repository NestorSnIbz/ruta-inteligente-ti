<?php
  $authUser = is_array($authUser ?? null) ? $authUser : [];
  $sidebarActive = (string) ($sidebarActive ?? '');
  $sidebarSeedProjects = is_array($sidebarSeedProjects ?? null) ? $sidebarSeedProjects : [];
  $sidebarCurrentProject = is_array($sidebarCurrentProject ?? null) ? $sidebarCurrentProject : null;

  $nombre = (string) ($authUser['nombre'] ?? '');
  $correo = (string) ($authUser['email'] ?? '');

  $initials = '';
  if ($nombre !== '') {
    $parts = preg_split('/\s+/', trim($nombre)) ?: [];
    $initials = strtoupper(mb_substr($parts[0] ?? '', 0, 1, 'UTF-8') . mb_substr($parts[1] ?? '', 0, 1, 'UTF-8'));
  }
  if ($initials === '' && $correo !== '') {
    $initials = strtoupper(mb_substr($correo, 0, 2, 'UTF-8'));
  }

  $seed = [];
  foreach ($sidebarSeedProjects as $p) {
    if (!is_array($p)) continue;
    $id = (int) ($p['id_proyecto'] ?? $p['id'] ?? 0);
    $n = (string) ($p['name'] ?? $p['nombre'] ?? '');
    if ($id > 0 && $n !== '') {
      $seed[] = ['id' => $id, 'name' => $n];
    }
    if (count($seed) >= 10) break;
  }

  $currentProject = null;
  if (is_array($sidebarCurrentProject)) {
    $id = (int) ($sidebarCurrentProject['id'] ?? 0);
    $n = (string) ($sidebarCurrentProject['name'] ?? $sidebarCurrentProject['nombre'] ?? '');
    if ($id > 0 && $n !== '') {
      $currentProject = ['id' => $id, 'name' => $n];
    }
  }
?>

<link href="/app-shell.css" rel="stylesheet" />

<aside class="ri-sidebar-host flex flex-col md:sticky md:top-0 md:h-screen md:max-h-screen md:overflow-y-auto">
  <div class="ri-sidebar-wrap flex flex-col" data-mobile-open="0">
  <div class="ri-sidebar-brand-row px-6 py-6">
    <div class="ri-sidebar-brand-copy flex items-center gap-3">
      <div class="ri-sidebar-logo-badge grid h-10 w-10 place-items-center rounded-xl">
        <span class="text-sm font-semibold">RI</span>
      </div>
      <div>
        <div class="text-sm font-semibold leading-tight text-white">Ruta Inteligente TI</div>
        <div class="ri-sidebar-subtitle text-xs leading-tight">Panel de control</div>
      </div>
    </div>
    <button
      id="sidebar-mobile-toggle"
      type="button"
      class="ri-sidebar-mobile-toggle h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-white"
      aria-expanded="false"
      aria-controls="ri-sidebar-content"
      aria-label="Abrir menu lateral"
    >
      <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
      </svg>
    </button>
  </div>

  <div id="ri-sidebar-content" class="ri-sidebar-content">
  <div class="ri-sidebar-mobile-bar px-4 pt-1 pb-3">
    <div class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200">
      <span>Menu</span>
      <button
        id="sidebar-mobile-close"
        type="button"
        class="ri-sidebar-mobile-close inline-flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-white"
        aria-label="Cerrar menu lateral"
      >
        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
        </svg>
      </button>
    </div>
  </div>
  <nav class="ri-sidebar-nav px-3 pb-6 flex-1">
    <div class="mb-3 px-3">
      <div class="ri-sidebar-section">Menu Principal</div>
    </div>
    <a
      href="dashboard.php"
      class="ri-sidebar-link flex items-center gap-3 px-3 py-3 text-sm font-medium"
      data-sidebar-item="dashboard"
    >
      <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9v9a2 2 0 01-2 2h-4v-6H9v6H5a2 2 0 01-2-2v-9z" />
      </svg>
      Dashboard
    </a>

    <div class="mb-3 mt-6 px-3">
      <div class="ri-sidebar-section">General</div>
    </div>
    <div class="mt-1">
      <button
        id="sidebar-projects-toggle"
        type="button"
        class="ri-sidebar-link w-full flex items-center justify-between gap-3 px-3 py-3 text-sm font-medium"
        aria-expanded="false"
        aria-controls="sidebar-projects-panel"
        data-sidebar-item="proyectos"
      >
        <span class="flex items-center gap-3">
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
          </svg>
          Planes Estratégicos
        </span>
        <svg id="sidebar-projects-chevron" viewBox="0 0 24 24" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <div
        id="sidebar-projects-panel"
        class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-in-out"
      >
        <div class="mt-1 space-y-1 pl-2">
          <a href="proyectos.php" class="ri-sidebar-link flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium">
            <span class="h-1.5 w-1.5 rounded-full" style="background:#a5b4fc;"></span>
            Ver todos
          </a>
          <div class="ri-sidebar-divider mx-3 h-px"></div>
          <div id="sidebar-recent-projects" class="space-y-1"></div>
        </div>
      </div>
    </div>

    <div class="mb-3 mt-6 px-3">
      <div class="ri-sidebar-section">Cuenta</div>
    </div>
    <a
      href="configuracion.php"
      class="ri-sidebar-link mt-1 flex items-center gap-3 px-3 py-3 text-sm font-medium"
      data-sidebar-item="configuracion"
    >
      <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 110-7 3.5 3.5 0 010 7z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a7.96 7.96 0 00.1-1 7.96 7.96 0 00-.1-1l2-1.6-2-3.4-2.4 1a8.3 8.3 0 00-1.7-1l-.4-2.6H9.1L8.7 7a8.3 8.3 0 00-1.7 1l-2.4-1-2 3.4L4.6 13a7.96 7.96 0 00-.1 1 7.96 7.96 0 00.1 1l-2 1.6 2 3.4 2.4-1a8.3 8.3 0 001.7 1l.4 2.6h5.8l.4-2.6a8.3 8.3 0 001.7-1l2.4 1 2-3.4-2-1.6z" />
      </svg>
      Configuración
    </a>
  </nav>

  <div class="ri-sidebar-user-slot px-3 pb-6">
    <div class="relative">
      <button
        id="user-menu-button"
        type="button"
        class="ri-sidebar-user-card w-full inline-flex items-center gap-3 px-3 py-2.5 text-sm font-semibold"
        aria-expanded="false"
        aria-controls="user-menu"
      >
        <span class="ri-sidebar-user-avatar grid h-9 w-9 place-items-center rounded-full text-xs font-semibold">
          <?php echo htmlspecialchars($initials, ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <span class="min-w-0 flex-1 text-left">
          <div class="truncate text-slate-900"><?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?></div>
          <div class="ri-sidebar-user-role truncate text-xs font-medium"><?php echo htmlspecialchars($correo, ENT_QUOTES, 'UTF-8'); ?></div>
        </span>
        <svg id="user-menu-chevron" viewBox="0 0 24 24" class="h-4 w-4 shrink-0 rotate-180 text-slate-500 transition-transform" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <div
        id="user-menu"
        class="ri-sidebar-user-menu hidden absolute bottom-full left-0 right-0 mb-2 overflow-hidden text-slate-900"
        role="menu"
        aria-labelledby="user-menu-button"
      >
        <div class="px-4 py-3 text-sm">
          <div class="font-medium text-slate-900"><?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?></div>
          <div class="mt-0.5 text-xs text-slate-500"><?php echo htmlspecialchars($correo, ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
        <div class="h-px bg-slate-200"></div>
        <a href="configuracion.php" class="block px-4 py-2.5 text-sm text-slate-700 transition-colors hover:bg-slate-50" role="menuitem">Mi perfil</a>
        <a href="configuracion.php" class="block px-4 py-2.5 text-sm text-slate-700 transition-colors hover:bg-slate-50" role="menuitem">Configuración</a>
        <div class="h-px bg-slate-200"></div>
        <a href="login.php" class="ri-sidebar-logout block px-4 py-2.5 text-sm font-medium transition-colors hover:bg-indigo-50" role="menuitem">Cerrar sesión</a>
      </div>
    </div>
  </div>
</div>
</div>
</aside>

<script>
  (function () {
    const sidebarActiveServer = <?php echo json_encode($sidebarActive, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    const currentUserId = <?php echo (int) ($authUser['id_persona'] ?? 0); ?>;
    const seedProjects = <?php echo json_encode($seed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    const currentProject = <?php echo json_encode($currentProject, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    const recentProjectsKey = currentUserId ? `ri:recent-project-ids:${currentUserId}` : "ri:recent-project-ids";
    const sidebarActiveKey = "ri:sidebar:active";
    const sidebarProjectsOpenKey = "ri:sidebar:projects_open";

    const sidebarProjectsToggle = document.getElementById("sidebar-projects-toggle");
    const sidebarProjectsChevron = document.getElementById("sidebar-projects-chevron");
    const sidebarProjectsPanel = document.getElementById("sidebar-projects-panel");
    const sidebarRecentProjects = document.getElementById("sidebar-recent-projects");
    const sidebarWrap = document.querySelector(".ri-sidebar-wrap");
    const sidebarMobileToggle = document.getElementById("sidebar-mobile-toggle");
    const sidebarMobileClose = document.getElementById("sidebar-mobile-close");

    const userMenuButton = document.getElementById("user-menu-button");
    const userMenu = document.getElementById("user-menu");
    const userMenuChevron = document.getElementById("user-menu-chevron");

    function setMobileSidebarOpen(open) {
      if (!sidebarWrap) return;
      const shouldOpen = !!open;
      sidebarWrap.setAttribute("data-mobile-open", shouldOpen ? "1" : "0");
      if (sidebarMobileToggle) {
        sidebarMobileToggle.setAttribute("aria-expanded", shouldOpen ? "true" : "false");
      }
    }

    if (sidebarMobileToggle) {
      sidebarMobileToggle.addEventListener("click", (event) => {
        event.stopPropagation();
        setMobileSidebarOpen(true);
      });
    }

    if (sidebarMobileClose) {
      sidebarMobileClose.addEventListener("click", (event) => {
        event.stopPropagation();
        setMobileSidebarOpen(false);
      });
    }

    function closeUserMenu() {
      if (!userMenu || !userMenuButton) return;
      userMenu.classList.add("hidden");
      userMenuButton.setAttribute("aria-expanded", "false");
      if (userMenuChevron) userMenuChevron.classList.add("rotate-180");
    }

    function toggleUserMenu() {
      if (!userMenu || !userMenuButton) return;
      const isHidden = userMenu.classList.contains("hidden");
      if (isHidden) {
        userMenu.classList.remove("hidden");
        userMenuButton.setAttribute("aria-expanded", "true");
        if (userMenuChevron) userMenuChevron.classList.remove("rotate-180");
      } else {
        closeUserMenu();
      }
    }

    if (userMenuButton) {
      userMenuButton.addEventListener("click", (event) => {
        event.stopPropagation();
        toggleUserMenu();
      });
    }

    document.addEventListener("click", () => {
      closeUserMenu();
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        setMobileSidebarOpen(false);
      }
    });

    window.addEventListener("resize", () => {
      if (window.innerWidth >= 768) {
        setMobileSidebarOpen(false);
      }
    });

    function getActiveFromPath() {
      const path = (window.location.pathname || "").toLowerCase();
      if (path.endsWith("/dashboard.php")) return "dashboard";
      if (path.endsWith("/proyectos.php")) return "proyectos";
      if (path.endsWith("/configuracion.php")) return "configuracion";
      if (path.endsWith("/detalle-proyecto.php")) return "proyectos";
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
            ? "ri-sidebar-link ri-sidebar-link-active w-full flex items-center justify-between gap-3 px-3 py-3 text-sm font-medium"
            : "ri-sidebar-link w-full flex items-center justify-between gap-3 px-3 py-3 text-sm font-medium";
          continue;
        }
        if (el.tagName.toLowerCase() === "a") {
          el.className = isActive
            ? "ri-sidebar-link ri-sidebar-link-active flex items-center gap-3 px-3 py-3 text-sm font-medium"
            : "ri-sidebar-link flex items-center gap-3 px-3 py-3 text-sm font-medium";
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
        return parsed
          .filter((x) => x && typeof x === "object" && Number.isFinite(Number(x.id)) && Number(x.id) > 0 && typeof x.name === "string")
          .slice(0, 10);
      } catch (e) { return []; }
    }

    function writeRecentProjects(list) {
      try { window.localStorage.setItem(recentProjectsKey, JSON.stringify(list)); } catch (e) {}
    }

    function pushRecentProject(id, name) {
      const pid = Number(id);
      const n = String(name || "");
      if (!Number.isFinite(pid) || pid <= 0 || !n) return;
      const now = Date.now();
      const current = readRecentProjects();
      const filtered = current.filter((x) => Number(x.id) !== pid);
      filtered.unshift({ id: pid, name: n, ts: now });
      writeRecentProjects(filtered.slice(0, 10));
      renderSidebarRecentProjects();
    }

    let dbLatestProjects = null;

    async function fetchDbLatestProjects() {
      try {
        const res = await fetch("proyectos.php?format=json&recent=1", { headers: { Accept: "application/json" } });
        const json = await res.json();
        if (!json || json.ok !== true || !Array.isArray(json.projects)) return null;
        const parsed = json.projects
          .map((p) => ({
            id: Number(p.id_proyecto),
            name: String(p.nombre || ""),
          }))
          .filter((p) => Number.isFinite(p.id) && p.id > 0 && p.name);
        return parsed.slice(0, 3);
      } catch (e) {
        return null;
      }
    }

    function renderSidebarRecentProjects() {
      if (!sidebarRecentProjects) return;
      let list = Array.isArray(dbLatestProjects) ? dbLatestProjects.slice(0, 3) : [];
      if (list.length === 0) {
        const stored = readRecentProjects();
        for (const item of stored) {
          list.push(item);
          if (list.length >= 3) break;
        }
      }
      if (list.length < 3 && Array.isArray(seedProjects)) {
        for (const p of seedProjects) {
          if (!p || typeof p !== "object") continue;
          if (list.some((x) => Number(x.id) === Number(p.id))) continue;
          list.push({ id: Number(p.id), name: String(p.name || ""), ts: 0 });
          if (list.length >= 3) break;
        }
      }

      sidebarRecentProjects.innerHTML = "";
      if (list.length === 0) {
        const empty = document.createElement("div");
        empty.className = "px-3 py-2 text-sm";
        empty.style.color = "#64748b";
        empty.textContent = "Sin proyectos recientes.";
        sidebarRecentProjects.appendChild(empty);
        return;
      }

      for (const p of list) {
        const a = document.createElement("a");
        a.className = "ri-sidebar-link flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium";
        a.href = `detalle-proyecto.php?id=${encodeURIComponent(String(p.id))}`;
        a.addEventListener("click", () => pushRecentProject(Number(p.id), String(p.name)));

        const dot = document.createElement("span");
        dot.className = "h-1.5 w-1.5 rounded-full";
        dot.style.background = "#a5b4fc";

        const label = document.createElement("span");
        label.className = "truncate";
        label.textContent = String(p.name || "—");

        a.appendChild(dot);
        a.appendChild(label);
        sidebarRecentProjects.appendChild(a);
      }

      if (sidebarProjectsToggle && sidebarProjectsPanel && sidebarProjectsToggle.getAttribute("aria-expanded") === "true") {
        sidebarProjectsPanel.style.maxHeight = `${sidebarProjectsPanel.scrollHeight}px`;
      }
    }

    if (sidebarProjectsToggle) {
      sidebarProjectsToggle.addEventListener("click", async () => {
        const expanded = sidebarProjectsToggle.getAttribute("aria-expanded") === "true";
        const willOpen = !expanded;
        setProjectsPanelOpen(willOpen);
        if (willOpen) {
          const latest = await fetchDbLatestProjects();
          if (latest && latest.length > 0) {
            dbLatestProjects = latest;
            renderSidebarRecentProjects();
          }
        }
      });
    }

    const active = getActiveFromPath() || sidebarActiveServer || readActive() || "dashboard";
    writeActive(active);
    setSidebarActive(active);
    renderSidebarRecentProjects();
    setProjectsPanelOpen(readProjectsOpen() || active === "proyectos");
    setMobileSidebarOpen(false);

    if (currentProject && typeof currentProject === "object") {
      pushRecentProject(Number(currentProject.id || 0), String(currentProject.name || ""));
    }

    const api = {
      pushRecentProject,
      renderSidebarRecentProjects,
      closeUserMenu,
      setProjectsPanelOpen,
    };

    try {
      window.RISidebar = window.RISidebar ? Object.assign(window.RISidebar, api) : api;
    } catch (e) {}
  })();
</script>
