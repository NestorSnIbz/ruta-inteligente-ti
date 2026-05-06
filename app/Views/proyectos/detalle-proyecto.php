<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Detalle Proyecto - Ruta Inteligente TI</title>
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
          <div class="text-sm font-semibold">
            Ruta Inteligente TI
          </div>
          <div class="text-xs text-white/70">
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
            Plan Estratégico 2026
          </h1>

          <p class="text-sm text-neutral-600 mt-1">
            Información general del proyecto estratégico.
          </p>
        </div>

        <span class="inline-flex items-center rounded-full bg-brand-50 px-4 py-1.5 text-sm font-medium text-brand-700">
          Activo
        </span>

      </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 p-6">

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- MISION -->
        <div class="bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Misión</h2>

            <button class="text-sm text-brand-700 hover:underline">
              Editar
            </button>
          </div>

          <p class="mt-4 text-sm text-neutral-600 leading-relaxed">
            Brindar soluciones tecnológicas innovadoras que optimicen los procesos empresariales.
          </p>
        </div>

        <!-- VISION -->
        <div class="bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Visión</h2>

            <button class="text-sm text-brand-700 hover:underline">
              Editar
            </button>
          </div>

          <p class="mt-4 text-sm text-neutral-600 leading-relaxed">
            Ser una empresa líder en transformación digital a nivel nacional.
          </p>
        </div>

        <!-- VALORES -->
        <div class="bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Valores</h2>

            <button class="text-sm text-brand-700 hover:underline">
              Editar
            </button>
          </div>

          <ul class="mt-4 space-y-2 text-sm text-neutral-600">
            <li>• Innovación</li>
            <li>• Transparencia</li>
            <li>• Compromiso</li>
            <li>• Calidad</li>
          </ul>
        </div>

        <!-- OBJETIVOS -->
        <div class="bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Objetivos Estratégicos</h2>

            <button class="text-sm text-brand-700 hover:underline">
              Editar
            </button>
          </div>

          <ul class="mt-4 space-y-2 text-sm text-neutral-600">
            <li>• Incrementar productividad empresarial.</li>
            <li>• Expandir presencia digital.</li>
            <li>• Optimizar recursos tecnológicos.</li>
          </ul>
        </div>

        <!-- ANALISIS -->
        <div class="lg:col-span-2 bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">
              Análisis Interno
            </h2>

            <button class="text-sm text-brand-700 hover:underline">
              Editar
            </button>
          </div>

          <p class="mt-4 text-sm text-neutral-600 leading-relaxed">
            La empresa cuenta con fortalezas tecnológicas y capacidad de innovación,
            aunque presenta limitaciones en automatización de procesos internos.
          </p>
        </div>

      </div>

    </main>
  </div>
</div>

</body>
</html>
