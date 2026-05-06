<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Nuevo Proyecto - Ruta Inteligente TI</title>
  <link href="/dist/output.css" rel="stylesheet" />
</head>

<body class="min-h-screen bg-neutral-50 text-neutral-900">
<div class="min-h-screen grid grid-cols-1 md:grid-cols-[16rem_1fr]">

  <!-- SIDEBAR -->
  <aside class="bg-brand-900 text-white">
    <div class="px-6 py-6">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-white/10 grid place-items-center">
          <span class="text-sm font-semibold">RI</span>
        </div>

        <div>
          <div class="text-sm font-semibold leading-tight">
            Ruta Inteligente TI
          </div>
          <div class="text-xs text-white/70 leading-tight">
            Panel de control
          </div>
        </div>
      </div>
    </div>

    <nav class="px-3 pb-6">
      <a href="dashboard.php"
         class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
        Dashboard
      </a>

      <a href="proyectos.php"
         class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium bg-white/10">
        Proyectos
      </a>

      <a href="configuracion.php"
         class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
        Configuración
      </a>
    </nav>
  </aside>

  <!-- MAIN -->
  <div class="flex flex-col">

    <!-- HEADER -->
    <header class="bg-white border-b border-neutral-200">
      <div class="px-6 py-4 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">
            Nuevo Proyecto
          </h1>
          <p class="text-sm text-neutral-600 mt-1">
            Registra un nuevo proyecto estratégico.
          </p>
        </div>

        <a href="proyectos.php"
           class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100">
          Volver
        </a>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 p-6">

      <div class="max-w-4xl mx-auto bg-white border border-neutral-200 rounded-2xl shadow-sm p-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          <div>
            <label class="block text-sm font-medium text-neutral-700">
              Nombre del proyecto
            </label>

            <input
              type="text"
              placeholder="Plan Estratégico 2026"
              class="mt-2 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-neutral-700">
              Empresa
            </label>

            <input
              type="text"
              placeholder="Nombre de la empresa"
              class="mt-2 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-neutral-700">
              Fecha de inicio
            </label>

            <input
              type="date"
              class="mt-2 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm outline-none"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-neutral-700">
              Estado
            </label>

            <select
              class="mt-2 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm outline-none">
              <option>Activo</option>
              <option>Borrador</option>
              <option>Finalizado</option>
            </select>
          </div>

        </div>

        <div class="mt-6">
          <label class="block text-sm font-medium text-neutral-700">
            Descripción
          </label>

          <textarea
            rows="5"
            placeholder="Describe el objetivo general del proyecto..."
            class="mt-2 w-full rounded-xl border border-neutral-300 px-4 py-3 text-sm outline-none resize-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"></textarea>
        </div>

        <div class="mt-8 flex justify-end gap-3">
          <button
            class="rounded-xl border border-neutral-300 px-5 py-2.5 text-sm font-medium hover:bg-neutral-100">
            Cancelar
          </button>

          <button
            class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
            Guardar proyecto
          </button>
        </div>

      </div>

    </main>
  </div>
</div>
</body>
</html>
