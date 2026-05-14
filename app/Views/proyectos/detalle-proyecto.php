<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Detalle Proyecto - Ruta Inteligente TI</title>
  <link href="/dist/output.css" rel="stylesheet" />
</head>

<body class="min-h-screen bg-neutral-50 text-neutral-900">
<?php
  $proyectoNombre = is_array($proyecto ?? null) ? (string) ($proyecto['nombre'] ?? '') : '';
  $idProyecto = is_array($proyecto ?? null) ? (int) ($proyecto['id_proyecto'] ?? 0) : 0;
  $misionTexto = is_array($mision ?? null) ? (string) ($mision['descripcion'] ?? '') : '';
  $visionTexto = is_array($vision ?? null) ? (string) ($vision['descripcion'] ?? '') : '';
  $valores = is_array($valores ?? null) ? $valores : [];
  $edit = (string) ($edit ?? '');
  $projectToken = (string) ($projectToken ?? '');
  $objetivosEstrategicos = is_array($objetivosEstrategicos ?? null) ? $objetivosEstrategicos : [];
  $objetivosEspecificosByEstrategico = is_array($objetivosEspecificosByEstrategico ?? null) ? $objetivosEspecificosByEstrategico : [];
  $objetivosError = (string) ($objetivosError ?? '');
  $oeEditToken = (string) ($oeEditToken ?? '');
  $oespEditToken = (string) ($oespEditToken ?? '');
  $cadenaPreguntas = is_array($cadenaPreguntas ?? null) ? $cadenaPreguntas : [];
  $cadenaRespuestas = is_array($cadenaRespuestas ?? null) ? $cadenaRespuestas : [];
  $cadenaCalc = is_array($cadenaCalc ?? null) ? $cadenaCalc : [
    'sum' => 0,
    'valid' => 0,
    'count' => 0,
    'missing' => 0,
    'potential' => null,
  ];
  $fodaFortalezas = is_array($fodaFortalezas ?? null) ? $fodaFortalezas : [];
  $fodaDebilidades = is_array($fodaDebilidades ?? null) ? $fodaDebilidades : [];
?>

<div class="min-h-screen grid grid-cols-1 md:grid-cols-[16rem_1fr]">

  <?php
    $sidebarActive = 'proyectos';
    $sidebarSeedProjects = [];
    $sidebarCurrentProject = ['id' => $idProyecto, 'name' => $proyectoNombre];
    include __DIR__ . '/../layouts/sidebar.php';
  ?>

  <!-- MAIN -->
  <div class="flex flex-col">

    <!-- HEADER -->
    <header class="bg-white border-b border-neutral-200">
      <div class="px-6 py-4 flex items-center justify-between">

        <div>
          <h1 class="text-2xl font-semibold tracking-tight">
            <?php echo htmlspecialchars($proyectoNombre, ENT_QUOTES, 'UTF-8'); ?>
          </h1>

          <p class="text-sm text-neutral-600 mt-1">
            Panel estratégico: Misión, Visión y Valores.
          </p>
        </div>
        <a href="proyectos.php" class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100">
          Volver
        </a>

      </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 p-6">
      <?php if (!empty($error) || !empty($success)) : ?>
        <div
          id="flash-modal"
          class="fixed inset-0 z-50 flex items-center justify-center px-4"
          role="dialog"
          aria-modal="true"
          aria-labelledby="flash-modal-title"
        >
          <div id="flash-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

          <div class="relative w-full max-w-md rounded-3xl border border-neutral-200 bg-white p-6 text-center shadow-xl">
            <?php if (!empty($success)) : ?>
              <div class="mx-auto mb-4 inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-white">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5" />
                </svg>
              </div>
              <div id="flash-modal-title" class="text-base font-semibold text-neutral-900">Listo</div>
              <div class="mt-2 text-sm text-neutral-700">
                <?php echo htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8'); ?>
              </div>
            <?php else : ?>
              <div class="mx-auto mb-4 inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-red-600 text-white">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86l-8.2 14.2A2 2 0 003.82 21h16.36a2 2 0 001.73-2.94l-8.2-14.2a2 2 0 00-3.42 0z" />
                </svg>
              </div>
              <div id="flash-modal-title" class="text-base font-semibold text-neutral-900">Ocurrió un error</div>
              <div class="mt-2 text-sm text-neutral-700">
                <?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?>
              </div>
            <?php endif; ?>

            <div class="mt-6 flex justify-center">
              <button
                id="flash-close"
                type="button"
                class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700"
              >
                Cerrar
              </button>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <div class="mb-6 rounded-2xl border border-neutral-200 bg-white p-2 shadow-sm">
        <div class="flex flex-wrap gap-2">
          <button type="button" data-panel="overview" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Overview
          </button>
          <button type="button" data-panel="mision" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Misión
          </button>
          <button type="button" data-panel="vision" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Visión
          </button>
          <button type="button" data-panel="valores" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Valores
          </button>
          <button type="button" data-panel="objetivos" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Objetivos
          </button>
          <button type="button" data-panel="cadena" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            Cadena de valor
          </button>
          <button type="button" data-panel="bgg" class="project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50">
            BGG
          </button>
        </div>
      </div>

      <div class="space-y-6">
        <section id="panel-overview" class="project-panel bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-start justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold">Overview</h2>
              <p class="mt-1 text-sm text-neutral-600">Resumen general del proyecto.</p>
            </div>
            <?php if (!empty($isCreador)) : ?>
            <button
              id="members-manage-open"
              type="button"
              class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50"
            >
              <svg viewBox="0 0 24 24" class="h-5 w-5 text-neutral-700" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 11a4 4 0 100-8 4 4 0 000 8z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 00-3-3.87" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 010 7.75" />
              </svg>
              Gestionar Miembros
            </button>
            <?php endif; ?>
          </div>

          <div class="mt-5 space-y-4">
            <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
              <div class="text-sm font-semibold text-neutral-900">Misión</div>
              <?php if ($misionTexto === '') : ?>
                <div class="mt-3 text-sm text-neutral-600">Aún no se registró la misión.</div>
              <?php else : ?>
                <div class="mt-3 text-sm text-neutral-700 leading-relaxed">
                  <?php echo nl2br(htmlspecialchars($misionTexto, ENT_QUOTES, 'UTF-8')); ?>
                </div>
              <?php endif; ?>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
              <div class="text-sm font-semibold text-neutral-900">Visión</div>
              <?php if ($visionTexto === '') : ?>
                <div class="mt-3 text-sm text-neutral-600">Aún no se registró la visión.</div>
              <?php else : ?>
                <div class="mt-3 text-sm text-neutral-700 leading-relaxed">
                  <?php echo nl2br(htmlspecialchars($visionTexto, ENT_QUOTES, 'UTF-8')); ?>
                </div>
              <?php endif; ?>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
              <div class="text-sm font-semibold text-neutral-900">Valores</div>
              <?php if (empty($valores)) : ?>
                <div class="mt-3 text-sm text-neutral-600">Aún no se registraron valores.</div>
              <?php else : ?>
                <div class="mt-4 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
                  <table class="min-w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                      <tr>
                        <th scope="col" class="w-14 px-4 py-3">#</th>
                        <th scope="col" class="px-4 py-3">Valor</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                      <?php foreach ($valores as $i => $valor) : ?>
                        <tr>
                          <td class="px-4 py-3 text-neutral-500"><?php echo (int) $i + 1; ?></td>
                          <td class="px-4 py-3 text-neutral-800">
                            <?php echo htmlspecialchars((string) ($valor['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
              <div class="text-sm font-semibold text-neutral-900">Objetivos</div>
              <div class="mt-3 text-sm text-neutral-600">
                Abre la pestaña “Objetivos” para cargar y gestionar los objetivos del proyecto.
              </div>
              <button
                type="button"
                data-open-panel="objetivos"
                class="mt-4 inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50"
              >
                Abrir Objetivos
              </button>
            </div>
          </div>
        </section>

        <?php if (!empty($isCreador)) : ?>
        <section id="panel-miembros" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold">Gestionar miembros</h2>
              <p class="mt-1 text-sm text-neutral-600">Invita por correo y administra accesos del proyecto.</p>
            </div>
            <button id="members-back" type="button" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
              Volver
            </button>
          </div>

          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
              <div class="text-sm font-semibold text-neutral-900">Invitar</div>
              <form id="invite-member-form" class="flex w-full max-w-xl items-center gap-2 sm:w-auto" method="post" action="detalle-proyecto.php">
                <input type="hidden" name="action" value="invite_member" />
                <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
                <input
                  type="email"
                  name="email"
                  placeholder="Invitar por email"
                  class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200"
                  required
                />
                <button type="submit" class="h-10 shrink-0 rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                  Invitar
                </button>
              </form>
            </div>

            <div class="mt-4 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
              <table class="min-w-full text-left text-sm">
                <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                  <tr>
                    <th scope="col" class="px-4 py-3">Nombre</th>
                    <th scope="col" class="px-4 py-3">Email</th>
                    <th scope="col" class="w-32 px-4 py-3">Rol</th>
                    <th scope="col" class="w-32 px-4 py-3 text-right">Acción</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                  <?php if (empty($miembros)) : ?>
                    <tr>
                      <td colspan="4" class="px-4 py-4 text-sm text-neutral-600">Aún no hay miembros.</td>
                    </tr>
                  <?php else : ?>
                    <?php foreach ($miembros as $m) : ?>
                      <tr data-member-row="<?php echo (int) ($m['id_persona'] ?? 0); ?>">
                        <td class="px-4 py-3 text-neutral-800">
                          <?php echo htmlspecialchars((string) ($m['nombre'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td class="px-4 py-3 text-neutral-700">
                          <?php echo htmlspecialchars((string) ($m['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td class="px-4 py-3">
                          <?php $rol = (string) ($m['rol'] ?? ''); ?>
                          <span class="<?php echo $rol === 'CREADOR' ? 'inline-flex items-center rounded-xl bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700' : 'inline-flex items-center rounded-xl bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-700'; ?>">
                            <?php echo htmlspecialchars($rol, ENT_QUOTES, 'UTF-8'); ?>
                          </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                          <?php if ($rol === 'CREADOR') : ?>
                            <span class="text-xs text-neutral-500">—</span>
                          <?php else : ?>
                            <form class="remove-member-form inline-flex" method="post" action="detalle-proyecto.php">
                              <input type="hidden" name="action" value="remove_member" />
                              <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
                              <input type="hidden" name="id_persona" value="<?php echo (int) ($m['id_persona'] ?? 0); ?>" />
                              <button type="submit" class="inline-flex h-9 items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 text-xs font-semibold text-red-700 hover:bg-red-100">
                                Eliminar
                              </button>
                            </form>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </section>
        <?php endif; ?>

        <section id="panel-mision" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="mision">
          <div class="flex items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold">Misión</h2>
              <p class="mt-1 text-sm text-neutral-600">Define la razón de ser del proyecto.</p>
            </div>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-vision" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="vision">
          <div class="flex items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold">Visión</h2>
              <p class="mt-1 text-sm text-neutral-600">Define hacia dónde se dirige el proyecto.</p>
            </div>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-valores" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="valores">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Valores</h2>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-objetivos" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="objetivos">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Objetivos</h2>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-cadena" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="cadena">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Cadena de valor</h2>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>

        <section id="panel-bgg" class="project-panel hidden bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm" data-lazy-panel="bgg">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">BGG</h2>
          </div>
          <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <div class="flex items-center gap-2 text-sm text-neutral-600">
              <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Cargando…</span>
            </div>
          </div>
        </section>
      </div>

    </main>
  </div>
</div>

<script>
  const projectToken = <?php echo json_encode((string) $projectToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  const projectName = <?php echo json_encode((string) $proyectoNombre, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  const flashModal = document.getElementById("flash-modal");
  const flashBackdrop = document.getElementById("flash-backdrop");
  const flashClose = document.getElementById("flash-close");

  function closeFlashModal() {
    if (!flashModal) return;
    flashModal.remove();
  }

  if (flashBackdrop) {
    flashBackdrop.addEventListener("click", closeFlashModal);
  }

  if (flashClose) {
    flashClose.addEventListener("click", closeFlashModal);
  }

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeFlashModal();
    }
  });

  const allowedPanels = new Set(["overview", "mision", "vision", "valores", "objetivos", "cadena", "bgg"]);
  const panelStorageKey = projectToken ? `ri:detalle-proyecto:section:${projectToken}` : "ri:detalle-proyecto:section";

  const projectTabs = Array.from(document.querySelectorAll(".project-tab"));
  const loadedPanels = new Set(["overview"]);
  if (document.getElementById("panel-miembros")) loadedPanels.add("miembros");
  const inflight = new Map();

  function setActiveProjectPanel(panelId, options = {}) {
    const { updateUrl = true } = options;

    document.querySelectorAll(".project-panel").forEach((panel) => panel.classList.add("hidden"));
    if (panelId) {
      const activePanel = document.getElementById(`panel-${panelId}`);
      if (activePanel) {
        activePanel.classList.remove("hidden");
      }
    }

    projectTabs.forEach((tab) => {
      const highlightPanel = panelId === "miembros" ? "overview" : panelId;
      const isActive = highlightPanel && tab.getAttribute("data-panel") === highlightPanel;
      tab.className = isActive
        ? "project-tab rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white"
        : "project-tab rounded-xl px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-brand-50";
    });

    if (panelId && allowedPanels.has(panelId)) {
      try {
        window.localStorage.setItem(panelStorageKey, panelId);
      } catch (e) {}
    }

    if (!updateUrl) return;

    const url = new URL(window.location.href);
    if (panelId === "miembros") {
      url.searchParams.set("members", "1");
      url.searchParams.set("section", "overview");
    } else {
      url.searchParams.delete("members");
      if (panelId) {
        url.searchParams.set("section", panelId);
      } else {
        url.searchParams.delete("section");
      }
    }
    const edit = url.searchParams.get("edit");
    if (edit && edit !== panelId) {
      url.searchParams.delete("edit");
    }
    if (panelId !== "objetivos") {
      url.searchParams.delete("oe_edit");
      url.searchParams.delete("oesp_edit");
    }
    window.history.replaceState({}, "", url.toString());
  }

  async function ensurePanelLoaded(panelId) {
    if (!panelId) return;
    if (panelId === "overview" || panelId === "miembros") return;
    if (!allowedPanels.has(panelId)) return;
    if (loadedPanels.has(panelId)) return;
    if (!projectToken) return;

    if (inflight.has(panelId)) return inflight.get(panelId);

    const task = (async () => {
      const current = document.getElementById(`panel-${panelId}`);
      if (!current) return;
      const wasHidden = current.classList.contains("hidden");
      try {
        const u = new URL("detalle-proyecto.php", window.location.href);
        u.searchParams.set("t", String(projectToken));
        u.searchParams.set("partial", String(panelId));
        ["edit", "oe_edit", "oesp_edit", "members"].forEach((k) => {
          const v = new URL(window.location.href).searchParams.get(k);
          if (v) u.searchParams.set(k, v);
        });

        const res = await fetch(u.toString(), {
          headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "text/html" },
        });
        const html = await res.text();
        if (!res.ok) return;
        current.outerHTML = html;
        const updated = document.getElementById(`panel-${panelId}`);
        if (updated) {
          if (wasHidden) updated.classList.add("hidden");
          else updated.classList.remove("hidden");
        }
        loadedPanels.add(panelId);
        initLazyPanel(panelId);
      } catch (e) {
      } finally {
        inflight.delete(panelId);
      }
    })();

    inflight.set(panelId, task);
    return task;
  }

  function initValoresPanel() {
    const panel = document.getElementById("panel-valores");
    if (!panel || panel.dataset.riInit === "1") return;
    panel.dataset.riInit = "1";

    const editButton = panel.querySelector("[data-js-edit-valores]");
    if (editButton) {
      editButton.addEventListener("click", (e) => {
        e.preventDefault();
        setActiveProjectPanel("valores");
        openValoresEdit();
        const u = new URL(window.location.href);
        u.searchParams.set("edit", "valores");
        u.searchParams.set("section", "valores");
        window.history.replaceState({}, "", u.toString());
      });
    }

    const cancelLink = panel.querySelector("[data-js-cancel-valores]");
    if (cancelLink) {
      cancelLink.addEventListener("click", (e) => {
        e.preventDefault();
        closeValoresEdit();
        const u = new URL(window.location.href);
        u.searchParams.delete("edit");
        window.history.replaceState({}, "", u.toString());
      });
    }

    const addButton = panel.querySelector("#agregar-valor");
    const input = panel.querySelector("#nuevo-valor");
    const list = panel.querySelector("#valores-lista");

    function createItem(text) {
      const row = document.createElement("div");
      row.className = "flex items-center gap-3 rounded-xl border border-neutral-200 bg-white px-4 py-3";

      const hidden = document.createElement("input");
      hidden.type = "hidden";
      hidden.name = "valores[]";
      hidden.value = text;

      const label = document.createElement("div");
      label.className = "flex-1 text-sm text-neutral-800";
      label.textContent = text;

      const remove = document.createElement("button");
      remove.type = "button";
      remove.className = "quitar-valor inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-red-700";
      remove.textContent = "Eliminar";
      remove.addEventListener("click", () => row.remove());

      row.appendChild(hidden);
      row.appendChild(label);
      row.appendChild(remove);
      return row;
    }

    function addValue() {
      if (!input || !list) return;
      const text = (input.value || "").trim();
      if (text.length < 2) return;
      list.appendChild(createItem(text));
      input.value = "";
      input.focus();
    }

    if (addButton) addButton.addEventListener("click", addValue);
    if (input) {
      input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
          e.preventDefault();
          addValue();
        }
      });
    }
    if (list) {
      list.querySelectorAll(".quitar-valor").forEach((btn) => {
        btn.addEventListener("click", () => btn.closest("div")?.remove());
      });
    }

    const url = new URL(window.location.href);
    if (url.searchParams.get("edit") === "valores") {
      openValoresEdit();
    }
  }

  function initObjetivosPanel() {
    const panel = document.getElementById("panel-objetivos");
    if (!panel || panel.dataset.riInit === "1") return;
    panel.dataset.riInit = "1";

    panel.querySelectorAll("[data-js-edit-oe]").forEach((el) => {
      el.addEventListener("click", (e) => {
        e.preventDefault();
        const token = el.getAttribute("data-js-edit-oe");
        if (!token) return;
        setActiveProjectPanel("objetivos");
        openObjetivoEstrategicoEdit(token);
        const u = new URL(window.location.href);
        u.searchParams.set("section", "objetivos");
        u.searchParams.set("oe_edit", token);
        u.searchParams.delete("oesp_edit");
        window.history.replaceState({}, "", u.toString());
      });
    });
    panel.querySelectorAll("[data-js-cancel-oe]").forEach((el) => {
      el.addEventListener("click", (e) => {
        e.preventDefault();
        const token = el.getAttribute("data-js-cancel-oe");
        if (!token) return;
        closeObjetivoEstrategicoEdit(token);
        const u = new URL(window.location.href);
        u.searchParams.delete("oe_edit");
        window.history.replaceState({}, "", u.toString());
      });
    });
    panel.querySelectorAll("[data-js-edit-oesp]").forEach((el) => {
      el.addEventListener("click", (e) => {
        e.preventDefault();
        const token = el.getAttribute("data-js-edit-oesp");
        if (!token) return;
        setActiveProjectPanel("objetivos");
        openObjetivoEspecificoEdit(token);
        const u = new URL(window.location.href);
        u.searchParams.set("section", "objetivos");
        u.searchParams.set("oesp_edit", token);
        u.searchParams.delete("oe_edit");
        window.history.replaceState({}, "", u.toString());
      });
    });
    panel.querySelectorAll("[data-js-cancel-oesp]").forEach((el) => {
      el.addEventListener("click", (e) => {
        e.preventDefault();
        const token = el.getAttribute("data-js-cancel-oesp");
        if (!token) return;
        closeObjetivoEspecificoEdit(token);
        const u = new URL(window.location.href);
        u.searchParams.delete("oesp_edit");
        window.history.replaceState({}, "", u.toString());
      });
    });

    const url = new URL(window.location.href);
    const oeEditParam = url.searchParams.get("oe_edit");
    const oespEditParam = url.searchParams.get("oesp_edit");
    if (oeEditParam) {
      openObjetivoEstrategicoEdit(oeEditParam);
    }
    if (oespEditParam) {
      openObjetivoEspecificoEdit(oespEditParam);
    }
  }

  function initCadenaPanel() {
    const panel = document.getElementById("panel-cadena");
    if (!panel || panel.dataset.riInit === "1") return;
    panel.dataset.riInit = "1";

    const cviForm = panel.querySelector("#cvi-form");
    const cviSumEl = panel.querySelector("#cvi-sum");
    const cviValidEl = panel.querySelector("#cvi-valid");
    const cviResultEl = panel.querySelector("#cvi-result");
    const cviResultSubEl = panel.querySelector("#cvi-result-sub");
    const cviSaveButton = panel.querySelector("#cvi-save");
    const fodaSaveButton = panel.querySelector("#foda-save");
    const fodaFortBody = panel.querySelector("#foda-fortalezas-body");
    const fodaDebBody = panel.querySelector("#foda-debilidades-body");
    const fodaAddFort = panel.querySelector("#foda-add-fortaleza");
    const fodaAddDeb = panel.querySelector("#foda-add-debilidad");
    const cviToast = panel.querySelector("#cvi-toast");
    const cviToastCard = panel.querySelector("#cvi-toast-card");
    const cviToastTitle = panel.querySelector("#cvi-toast-title");
    const cviToastMsg = panel.querySelector("#cvi-toast-msg");
    const cviToastClose = panel.querySelector("#cvi-toast-close");
    const cviRows = Array.from(panel.querySelectorAll("[data-cvi-row]"));

    let cviToastTimer = null;
    let cviValidationActive = false;
    let cviDirty = false;
    let cviSaving = false;
    const cviAnswers = {};

    function cviCloseToast() {
      if (!cviToast) return;
      cviToast.classList.add("hidden");
      if (cviToastTimer) {
        clearTimeout(cviToastTimer);
        cviToastTimer = null;
      }
    }

    function cviShowToast(type, title, message) {
      if (!cviToast || !cviToastCard || !cviToastTitle || !cviToastMsg) return;
      cviToastTitle.textContent = title || "";
      cviToastMsg.textContent = message || "";
      cviToastCard.className =
        type === "success"
          ? "pointer-events-auto rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-lg"
          : "pointer-events-auto rounded-2xl border border-red-200 bg-red-50 p-4 shadow-lg";
      cviToast.classList.remove("hidden");
      if (cviToastTimer) clearTimeout(cviToastTimer);
      cviToastTimer = setTimeout(() => cviCloseToast(), 3500);
    }

    if (cviToastClose) {
      cviToastClose.addEventListener("click", () => cviCloseToast());
    }

    function cviUpdateRowStyles(row) {
      const cells = Array.from(row.querySelectorAll(".cvi-cell"));
      for (const cell of cells) {
        const input = cell.querySelector("input[type='radio']");
        const label = cell.querySelector(".cvi-cell-label");
        const checked = input && input.checked;
        cell.className = checked
          ? "cvi-cell flex h-12 w-full cursor-pointer items-center justify-center select-none bg-brand-50"
          : "cvi-cell flex h-12 w-full cursor-pointer items-center justify-center select-none hover:bg-neutral-50";
        if (label) {
          label.className = checked
            ? "cvi-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-brand-600 bg-brand-600 px-3 text-sm font-semibold text-white shadow-sm transition"
            : "cvi-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-neutral-300 bg-white px-3 text-sm font-semibold text-neutral-700 transition hover:border-brand-300";
        }
      }
    }

    function cviApplyCalc(calc) {
      const sum = Number(calc && calc.sum !== undefined ? calc.sum : 0);
      const valid = Number(calc && calc.valid !== undefined ? calc.valid : 0);
      const count = Number(calc && calc.count !== undefined ? calc.count : cviRows.length);
      const missing = Number(calc && calc.missing !== undefined ? calc.missing : Math.max(0, count - valid));
      const potential = calc ? calc.potential : null;

      if (cviSumEl) cviSumEl.textContent = String(sum);
      if (cviValidEl) cviValidEl.textContent = `${valid}/${count}`;

      if (missing > 0 || potential === null || potential === undefined) {
        if (!cviValidationActive) {
          if (cviResultEl) cviResultEl.textContent = "—";
          if (cviResultSubEl) cviResultSubEl.textContent = "";
          return;
        }
        if (cviResultEl) cviResultEl.textContent = "#¡REF!";
        if (cviResultSubEl) cviResultSubEl.textContent = "";
        return;
      }

      const p = Number(potential);
      if (Number.isNaN(p)) {
        if (cviResultEl) cviResultEl.textContent = "#¡REF!";
        if (cviResultSubEl) cviResultSubEl.textContent = "";
        return;
      }

      if (cviResultEl) cviResultEl.textContent = p.toFixed(2);
      if (cviResultSubEl) cviResultSubEl.textContent = `${Math.round(p * 100)}%`;
    }

    function cviRecalculateFromDom() {
      let sum = 0;
      let valid = 0;
      let hasInvalid = false;

      for (const row of cviRows) {
        const checked = row.querySelectorAll("input[type='radio']:checked");
        const ref = row.querySelector("[data-cvi-ref]");
        cviUpdateRowStyles(row);

        if (checked.length === 1) {
          valid += 1;
          sum += Number(checked[0].value || 0);
          if (ref) ref.classList.add("hidden");
          row.className = "cvi-row";
          cviAnswers[Number(row.dataset.cviRow || 0)] = Number(checked[0].value || 0);
        } else {
          hasInvalid = true;
          if (cviValidationActive && ref) ref.classList.remove("hidden");
          if (ref && !cviValidationActive) ref.classList.add("hidden");
          row.className = cviValidationActive ? "cvi-row bg-red-50/40" : "cvi-row";
          delete cviAnswers[Number(row.dataset.cviRow || 0)];
        }
      }

      cviApplyCalc({
        sum,
        valid,
        count: cviRows.length,
        missing: hasInvalid ? Math.max(0, cviRows.length - valid) : 0,
        potential: hasInvalid ? null : (1 - (sum / 100)),
      });
    }

    async function cviSaveAll() {
      if (cviSaving) return;
      cviValidationActive = true;
      cviRecalculateFromDom();

      if (Object.keys(cviAnswers).length !== cviRows.length) {
        cviShowToast("error", "Faltan respuestas", "Completa todas las preguntas antes de guardar.");
        return;
      }

      cviSaving = true;
      if (cviSaveButton) {
        cviSaveButton.disabled = true;
        cviSaveButton.className = "inline-flex items-center justify-center rounded-xl bg-brand-600/60 px-4 py-2 text-sm font-semibold text-white shadow-sm";
        cviSaveButton.textContent = "Guardando…";
      }

      try {
        const formData = new FormData();
        formData.set("action", "save_cadena_valor_batch");
        formData.set("t", projectToken || "");
        formData.set("answers", JSON.stringify(cviAnswers));

        const res = await fetch("detalle-proyecto.php", {
          method: "POST",
          body: formData,
          headers: { Accept: "application/json" },
        });

        const json = await res.json();
        if (!json || typeof json !== "object" || !json.ok) {
          cviShowToast("error", "No se pudo guardar", String((json && json.error) || "Error al guardar la evaluación."));
          return;
        }

        if (json.calc) {
          cviApplyCalc(json.calc);
        }
        cviDirty = false;
        cviShowToast("success", "Guardado", "Evaluación guardada correctamente.");
      } catch (e) {
        cviShowToast("error", "No se pudo guardar", "Error al guardar la evaluación.");
      } finally {
        cviSaving = false;
        if (cviSaveButton) {
          cviSaveButton.disabled = false;
          cviSaveButton.className = "inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25";
          cviSaveButton.textContent = "Guardar Evaluación";
        }
      }
    }

    function renumberFodaRows(tbody) {
      const rows = Array.from(tbody.querySelectorAll("tr"));
      rows.forEach((row, idx) => {
        const n = row.querySelector("td");
        if (n) n.textContent = String(idx + 1);
      });
    }

    function attachFodaRemoveHandlers(tbody) {
      tbody.querySelectorAll(".foda-remove").forEach((btn) => {
        btn.addEventListener("click", () => {
          const row = btn.closest("tr");
          if (row) row.remove();
          renumberFodaRows(tbody);
        });
      });
    }

    function createFodaRow(kind) {
      const tr = document.createElement("tr");
      tr.setAttribute("data-foda-row", kind);

      const tdN = document.createElement("td");
      tdN.className = "px-4 py-3 text-center text-xs font-semibold text-neutral-600";
      tdN.textContent = "—";

      const tdInput = document.createElement("td");
      tdInput.className = "px-4 py-2";
      const input = document.createElement("input");
      input.type = "text";
      input.className = "foda-input h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200";
      tdInput.appendChild(input);

      const tdAction = document.createElement("td");
      tdAction.className = "px-4 py-2 text-right";
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "foda-remove inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50";
      btn.textContent = "Quitar";
      tdAction.appendChild(btn);

      tr.appendChild(tdN);
      tr.appendChild(tdInput);
      tr.appendChild(tdAction);
      return tr;
    }

    if (fodaFortBody) attachFodaRemoveHandlers(fodaFortBody);
    if (fodaDebBody) attachFodaRemoveHandlers(fodaDebBody);
    if (fodaFortBody) renumberFodaRows(fodaFortBody);
    if (fodaDebBody) renumberFodaRows(fodaDebBody);

    if (fodaAddFort && fodaFortBody) {
      fodaAddFort.addEventListener("click", () => {
        const tr = createFodaRow("fortaleza");
        fodaFortBody.appendChild(tr);
        attachFodaRemoveHandlers(fodaFortBody);
        renumberFodaRows(fodaFortBody);
        tr.querySelector("input")?.focus();
      });
    }

    if (fodaAddDeb && fodaDebBody) {
      fodaAddDeb.addEventListener("click", () => {
        const tr = createFodaRow("debilidad");
        fodaDebBody.appendChild(tr);
        attachFodaRemoveHandlers(fodaDebBody);
        renumberFodaRows(fodaDebBody);
        tr.querySelector("input")?.focus();
      });
    }

    function collectFodaValues(tbody) {
      const rows = Array.from(tbody.querySelectorAll("tr"));
      const out = [];
      for (const row of rows) {
        const value = (row.querySelector("input")?.value || "").trim();
        if (value) out.push(value);
      }
      return out;
    }

    let fodaSaving = false;
    if (fodaSaveButton) {
      fodaSaveButton.addEventListener("click", async () => {
        if (fodaSaving) return;
        fodaSaving = true;
        fodaSaveButton.disabled = true;
        try {
          const fortalezas = fodaFortBody ? collectFodaValues(fodaFortBody) : [];
          const debilidades = fodaDebBody ? collectFodaValues(fodaDebBody) : [];
          const payload = JSON.stringify({ fortalezas, debilidades });

          const fd = new FormData();
          fd.set("action", "save_foda_cadena");
          fd.set("t", projectToken || "");
          fd.set("payload", payload);

          const res = await fetch("detalle-proyecto.php", {
            method: "POST",
            headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
            body: fd,
          });
          const data = await res.json().catch(() => null);
          if (!data || data.ok !== true) {
            cviShowToast("error", "Error", (data && data.error) ? String(data.error) : "No se pudo guardar el FODA.");
            return;
          }
          cviShowToast("success", "Guardado", "FODA guardado correctamente.");
        } catch (e) {
          cviShowToast("error", "Error", "No se pudo guardar el FODA.");
        } finally {
          fodaSaveButton.disabled = false;
          fodaSaving = false;
        }
      });
    }

    if (cviForm) {
      cviForm.addEventListener("change", (e) => {
        const target = e.target;
        if (!(target instanceof HTMLInputElement)) return;
        if (target.type !== "radio") return;

        const row = target.closest("[data-cvi-row]");
        if (!row) return;

        cviDirty = true;
        cviRecalculateFromDom();
        if (!cviValidationActive) {
          const ref = row.querySelector("[data-cvi-ref]");
          if (ref) ref.classList.add("hidden");
          row.className = "cvi-row";
        }
      });

      cviRecalculateFromDom();
    }

    if (cviSaveButton) {
      cviSaveButton.addEventListener("click", () => {
        cviSaveAll();
      });
    }
  }

  function initLazyPanel(panelId) {
    if (panelId === "valores") initValoresPanel();
    if (panelId === "objetivos") initObjetivosPanel();
    if (panelId === "cadena") initCadenaPanel();
  }

  projectTabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      const panelId = tab.getAttribute("data-panel");
      if (!panelId) return;
      setActiveProjectPanel(panelId);
      ensurePanelLoaded(panelId);
    });
  });

  const url = new URL(window.location.href);
  const editParam = url.searchParams.get("edit");
  const sectionParam = url.searchParams.get("section");
  const membersParam = url.searchParams.get("members");
  const oeEditParam = url.searchParams.get("oe_edit");
  const oespEditParam = url.searchParams.get("oesp_edit");
  let storedPanel = "";
  try {
    storedPanel = window.localStorage.getItem(panelStorageKey) || "";
  } catch (e) {}
  const normalizedSectionParam = sectionParam && allowedPanels.has(sectionParam) ? sectionParam : "";
  const normalizedStoredPanel = storedPanel && allowedPanels.has(storedPanel) ? storedPanel : "";
  const initialPanel =
    (editParam === "mision" || editParam === "vision" || editParam === "valores") ? editParam :
    ((oeEditParam || oespEditParam) ? "objetivos" : null) ||
    ((membersParam === "1" && document.getElementById("panel-miembros")) ? "miembros" : null) ||
    (normalizedSectionParam || normalizedStoredPanel || "overview");

  setActiveProjectPanel(initialPanel, { updateUrl: false });
  ensurePanelLoaded(initialPanel);

  document.querySelectorAll("[data-open-panel]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const panelId = btn.getAttribute("data-open-panel");
      if (!panelId) return;
      setActiveProjectPanel(panelId);
      ensurePanelLoaded(panelId);
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  });

  /*function openBlockEdit(block) {
    const container = document.querySelector(`[data-block="${block}"]`);
    if (!container) return;
    const view = container.querySelector("[data-block-view]");
    const form = container.querySelector("[data-block-form]");
    if (view) view.classList.add("hidden");
    if (form) {
      form.classList.remove("hidden");
      const textarea = form.querySelector("textarea");
      if (textarea) textarea.focus();
    }

    const editButton = document.querySelector(`[data-js-edit-block="${block}"]`);
    if (editButton) editButton.classList.add("hidden");
  }

  function closeBlockEdit(block) {
    const container = document.querySelector(`[data-block="${block}"]`);
    if (!container) return;
    const view = container.querySelector("[data-block-view]");
    const form = container.querySelector("[data-block-form]");
    if (form) form.classList.add("hidden");
    if (view) view.classList.remove("hidden");

    const editButton = document.querySelector(`[data-js-edit-block="${block}"]`);
    if (editButton) editButton.classList.remove("hidden");
  }*/ 

  function openValoresEdit() {
    const editor = document.getElementById("valores-editor");
    const display = document.getElementById("valores-display");
    if (display) display.classList.add("hidden");
    if (editor) editor.classList.remove("hidden");
    const editButton = document.querySelector("[data-js-edit-valores]");
    if (editButton) editButton.classList.add("hidden");
    const input = document.getElementById("nuevo-valor");
    if (input) input.focus();
  }

  function closeValoresEdit() {
    const editor = document.getElementById("valores-editor");
    const display = document.getElementById("valores-display");
    if (editor) editor.classList.add("hidden");
    if (display) display.classList.remove("hidden");
    const editButton = document.querySelector("[data-js-edit-valores]");
    if (editButton) editButton.classList.remove("hidden");
  }

  function openObjetivoEstrategicoEdit(token) {
    const card = document.querySelector(`[data-oe-card="${token}"]`);
    if (!card) return;
    const view = card.querySelector("[data-oe-view]");
    const form = card.querySelector("[data-oe-form]");
    if (view) view.classList.add("hidden");
    if (form) {
      form.classList.remove("hidden");
      const textarea = form.querySelector("textarea");
      if (textarea) textarea.focus();
    }
  }

  function closeObjetivoEstrategicoEdit(token) {
    const card = document.querySelector(`[data-oe-card="${token}"]`);
    if (!card) return;
    const view = card.querySelector("[data-oe-view]");
    const form = card.querySelector("[data-oe-form]");
    if (form) form.classList.add("hidden");
    if (view) view.classList.remove("hidden");
  }

  /*document.querySelectorAll("[data-js-edit-block]").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      const block = el.getAttribute("data-js-edit-block");
      if (!block) return;
      setActiveProjectPanel(block);
      openBlockEdit(block);
    });
  });

  document.querySelectorAll("[data-js-cancel-block]").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      const block = el.getAttribute("data-js-cancel-block");
      if (!block) return;
      closeBlockEdit(block);
    });
  });*/

  function openObjetivoEspecificoEdit(token) {
    const row = document.querySelector(`[data-oesp-row="${token}"]`);
    if (!row) return;
    const view = row.querySelector("[data-oesp-view]");
    const form = row.querySelector("[data-oesp-form]");
    if (view) view.classList.add("hidden");
    if (form) {
      form.classList.remove("hidden");
      const input = form.querySelector('input[name="descripcion"]');
      if (input) input.focus();
    }
  }

  function closeObjetivoEspecificoEdit(token) {
    const row = document.querySelector(`[data-oesp-row="${token}"]`);
    if (!row) return;
    const view = row.querySelector("[data-oesp-view]");
    const form = row.querySelector("[data-oesp-form]");
    if (form) form.classList.add("hidden");
    if (view) view.classList.remove("hidden");
  }
  const membersManageOpen = document.getElementById("members-manage-open");
  const membersBack = document.getElementById("members-back");

  if (membersManageOpen) {
    membersManageOpen.addEventListener("click", () => {
      setActiveProjectPanel("miembros", { updateUrl: false });
      const u = new URL(window.location.href);
      u.searchParams.set("members", "1");
      window.history.replaceState({}, "", u.toString());
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  if (membersBack) {
    membersBack.addEventListener("click", () => {
      setActiveProjectPanel("overview");
      const u = new URL(window.location.href);
      u.searchParams.delete("members");
      window.history.replaceState({}, "", u.toString());
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  function cviUpdateRowStyles(row) {
    const cells = Array.from(row.querySelectorAll(".cvi-cell"));
    for (const cell of cells) {
      const input = cell.querySelector("input[type='radio']");
      const label = cell.querySelector(".cvi-cell-label");
      const checked = input && input.checked;
      cell.className = checked
        ? "cvi-cell flex h-12 w-full cursor-pointer items-center justify-center select-none bg-brand-50"
        : "cvi-cell flex h-12 w-full cursor-pointer items-center justify-center select-none hover:bg-neutral-50";
      if (label) {
        label.className = checked
          ? "cvi-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-brand-600 bg-brand-600 px-3 text-sm font-semibold text-white shadow-sm transition"
          : "cvi-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-neutral-300 bg-white px-3 text-sm font-semibold text-neutral-700 transition hover:border-brand-300";
      }
    }
  }

  function cviApplyCalc(calc) {
    const sum = Number(calc && calc.sum !== undefined ? calc.sum : 0);
    const valid = Number(calc && calc.valid !== undefined ? calc.valid : 0);
    const count = Number(calc && calc.count !== undefined ? calc.count : cviRows.length);
    const missing = Number(calc && calc.missing !== undefined ? calc.missing : Math.max(0, count - valid));
    const potential = calc ? calc.potential : null;

    if (cviSumEl) cviSumEl.textContent = String(sum);
    if (cviValidEl) cviValidEl.textContent = `${valid}/${count}`;

    if (missing > 0 || potential === null || potential === undefined) {
      if (!cviValidationActive) {
        if (cviResultEl) cviResultEl.textContent = "—";
        if (cviResultSubEl) cviResultSubEl.textContent = "";
        return;
      }
      if (cviResultEl) cviResultEl.textContent = "#¡REF!";
      if (cviResultSubEl) cviResultSubEl.textContent = "";
      return;
    }

    const p = Number(potential);
    if (Number.isNaN(p)) {
      if (cviResultEl) cviResultEl.textContent = "#¡REF!";
      if (cviResultSubEl) cviResultSubEl.textContent = "";
      return;
    }

    if (cviResultEl) cviResultEl.textContent = p.toFixed(2);
    if (cviResultSubEl) cviResultSubEl.textContent = `${Math.round(p * 100)}%`;
  }

  function cviRecalculateFromDom() {
    let sum = 0;
    let valid = 0;
    let hasInvalid = false;

    for (const row of cviRows) {
      const checked = row.querySelectorAll("input[type='radio']:checked");
      const ref = row.querySelector("[data-cvi-ref]");
      cviUpdateRowStyles(row);

      if (checked.length === 1) {
        valid += 1;
        sum += Number(checked[0].value || 0);
        if (ref) ref.classList.add("hidden");
        row.className = "cvi-row";
        cviAnswers[Number(row.dataset.cviRow || 0)] = Number(checked[0].value || 0);
      } else {
        hasInvalid = true;
        if (cviValidationActive && ref) ref.classList.remove("hidden");
        if (ref && !cviValidationActive) ref.classList.add("hidden");
        row.className = cviValidationActive ? "cvi-row bg-red-50/40" : "cvi-row";
        delete cviAnswers[Number(row.dataset.cviRow || 0)];
      }
    }

    cviApplyCalc({
      sum,
      valid,
      count: cviRows.length,
      missing: hasInvalid ? Math.max(0, cviRows.length - valid) : 0,
      potential: hasInvalid ? null : (1 - (sum / 100)),
    });
  }

  async function cviSaveAll() {
    if (cviSaving) return;
    cviValidationActive = true;
    cviRecalculateFromDom();

    if (Object.keys(cviAnswers).length !== cviRows.length) {
      cviShowToast("error", "Faltan respuestas", "Completa todas las preguntas antes de guardar.");
      return;
    }

    cviSaving = true;
    if (cviSaveButton) {
      cviSaveButton.disabled = true;
      cviSaveButton.className = "inline-flex items-center justify-center rounded-xl bg-brand-600/60 px-4 py-2 text-sm font-semibold text-white shadow-sm";
      cviSaveButton.textContent = "Guardando…";
    }

    try {
      const formData = new FormData();
      formData.set("action", "save_cadena_valor_batch");
      formData.set("t", projectToken || "");
      formData.set("answers", JSON.stringify(cviAnswers));

      const res = await fetch("detalle-proyecto.php", {
        method: "POST",
        body: formData,
        headers: { Accept: "application/json" },
      });

      const json = await res.json();
      if (!json || typeof json !== "object" || !json.ok) {
        cviShowToast("error", "No se pudo guardar", String((json && json.error) || "Error al guardar la evaluación."));
        return;
      }

      if (json.calc) {
        cviApplyCalc(json.calc);
      }
      cviDirty = false;
      cviShowToast("success", "Guardado", "Evaluación guardada correctamente.");
    } catch (e) {
      cviShowToast("error", "No se pudo guardar", "Error al guardar la evaluación.");
    } finally {
      cviSaving = false;
      if (cviSaveButton) {
        cviSaveButton.disabled = false;
        cviSaveButton.className = "inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25";
        cviSaveButton.textContent = "Guardar Evaluación";
      }
    }
  }

  if (cviForm) {
    cviForm.addEventListener("change", (e) => {
      const target = e.target;
      if (!(target instanceof HTMLInputElement)) return;
      if (target.type !== "radio") return;

      const row = target.closest("[data-cvi-row]");
      if (!row) return;

      cviDirty = true;
      cviRecalculateFromDom();
      if (!cviValidationActive) {
        const ref = row.querySelector("[data-cvi-ref]");
        if (ref) ref.classList.add("hidden");
        row.className = "cvi-row";
      }
    });

    cviRecalculateFromDom();
  }

  if (cviSaveButton) {
    cviSaveButton.addEventListener("click", () => {
      cviSaveAll();
    });
  }
</script>

</body>
</html>
