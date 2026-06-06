<?php
  $panel = (string) ($panel ?? '');
  $allowed = ['overview', 'mision', 'vision', 'valores', 'objetivos', 'cadena', 'perfil_competitivo', 'pest', 'bgg'];
  if (!in_array($panel, $allowed, true)) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Panel inválido.';
    return;
  }
?>
<?php if ($panel === 'mision') : ?>
  <section id="panel-mision" class="project-panel bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center justify-between gap-3">
      <div>
        <h2 class="text-lg font-semibold">Misión</h2>
        <p class="mt-1 text-sm text-neutral-600">
          Define la razón de ser del proyecto.
        </p>
      </div>
      <a
        data-js-edit-mision="1"
        href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&section=mision&edit=mision"
        class="<?php echo ($edit ?? '') === 'mision' ? 'hidden' : 'inline-flex'; ?> items-center justify-center rounded-xl border border-neutral-200 bg-white p-2 text-brand-700 hover:bg-brand-50"
        aria-label="Editar misión"
        title="Editar"
      >
        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11 4h-4a2 2 0 00-2 2v4m14-4l-9 9-4 1 1-4 9-9 3 3z" />
        </svg>
      </a>
    </div>

    <div id="mision-editor" class="<?php echo ($edit ?? '') === 'mision' ? 'block' : 'hidden'; ?> mt-4 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <div class="text-sm font-semibold text-neutral-900">Editor de misión</div>
          <div class="mt-0.5 text-xs text-neutral-600">Actualiza el texto de la misión del proyecto.</div>
        </div>
      </div>

      <form class="mt-4 space-y-4" method="post" action="detalle-proyecto.php">
        <input type="hidden" name="action" value="save_mision" />
        <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />

        <textarea
          name="descripcion"
          rows="10"
          class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-4 text-sm leading-relaxed outline-none resize-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
          placeholder="Escribe la misión del proyecto..."
          required
        ><?php echo htmlspecialchars((string) ($misionTexto ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>

        <div class="flex justify-end gap-3">
          <a
            data-js-cancel-mision="1"
            href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&section=mision"
            class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100"
          >
            Cancelar
          </a>
          <button
            type="submit"
            class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700"
          >
            Guardar cambios
          </button>
        </div>
      </form>
    </div>

    <div id="mision-display" class="<?php echo ($edit ?? '') === 'mision' ? 'hidden' : 'block'; ?>">
      <?php if ($misionTexto === '') : ?>
        <p class="mt-4 text-sm text-neutral-600">Aún no se registró la misión. Presiona el lápiz para agregarla.</p>
      <?php else : ?>
        <div class="mt-4 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
          <div class="text-sm leading-relaxed text-neutral-800">
            <?php echo nl2br(htmlspecialchars($misionTexto, ENT_QUOTES, 'UTF-8')); ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>
<?php elseif ($panel === 'vision') : ?>
  <section id="panel-vision" class="project-panel bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center justify-between gap-3">
      <div>
        <h2 class="text-lg font-semibold">Visión</h2>
        <p class="mt-1 text-sm text-neutral-600">
          Define hacia dónde se dirige el proyecto.
        </p>
      </div>
      <a
        data-js-edit-vision="1"
        href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&section=vision&edit=vision"
        class="<?php echo ($edit ?? '') === 'vision' ? 'hidden' : 'inline-flex'; ?> items-center justify-center rounded-xl border border-neutral-200 bg-white p-2 text-brand-700 hover:bg-brand-50"
        aria-label="Editar visión"
        title="Editar"
      >
        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11 4h-4a2 2 0 00-2 2v4m14-4l-9 9-4 1 1-4 9-9 3 3z" />
        </svg>
      </a>
    </div>

    <div id="vision-editor" class="<?php echo ($edit ?? '') === 'vision' ? 'block' : 'hidden'; ?> mt-4 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <div class="text-sm font-semibold text-neutral-900">Editor de visión</div>
          <div class="mt-0.5 text-xs text-neutral-600">Actualiza el texto de la visión del proyecto.</div>
        </div>
      </div>

      <form class="mt-4 space-y-4" method="post" action="detalle-proyecto.php">
        <input type="hidden" name="action" value="save_vision" />
        <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />

        <textarea
          name="descripcion"
          rows="10"
          class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-4 text-sm leading-relaxed outline-none resize-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
          placeholder="Escribe la visión del proyecto..."
          required
        ><?php echo htmlspecialchars((string) ($visionTexto ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>

        <div class="flex justify-end gap-3">
          <a
            data-js-cancel-vision="1"
            href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&section=vision"
            class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100"
          >
            Cancelar
          </a>
          <button
            type="submit"
            class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700"
          >
            Guardar cambios
          </button>
        </div>
      </form>
    </div>

    <div id="vision-display" class="<?php echo ($edit ?? '') === 'vision' ? 'hidden' : 'block'; ?>">
      <?php if ($visionTexto === '') : ?>
        <p class="mt-4 text-sm text-neutral-600">Aún no se registró la visión. Presiona el lápiz para agregarla.</p>
      <?php else : ?>
        <div class="mt-4 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
          <div class="text-sm leading-relaxed text-neutral-800">
            <?php echo nl2br(htmlspecialchars($visionTexto, ENT_QUOTES, 'UTF-8')); ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>
<?php elseif ($panel === 'valores') : ?>
  <section id="panel-valores" class="project-panel bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center justify-between gap-3">
      <h2 class="text-lg font-semibold">Valores</h2>
      <a
        data-js-edit-valores="1"
        href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&edit=valores"
        class="<?php echo ($edit ?? '') === 'valores' ? 'hidden' : 'inline-flex'; ?> items-center justify-center rounded-xl border border-neutral-200 bg-white p-2 text-brand-700 hover:bg-brand-50"
        aria-label="Editar valores"
        title="Editar"
      >
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 4h-4a2 2 0 00-2 2v4m14-4l-9 9-4 1 1-4 9-9 3 3z" />
          </svg>
      </a>
    </div>

    <div id="valores-editor" class="<?php echo ($edit ?? '') === 'valores' ? 'block' : 'hidden'; ?> mt-4 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <div class="text-sm font-semibold text-neutral-900">Editor de valores</div>
            <div class="mt-0.5 text-xs text-neutral-600">Agrega o elimina valores y luego guarda.</div>
          </div>
        </div>

        <form id="valores-form" class="mt-4 space-y-4" method="post" action="detalle-proyecto.php">
          <input type="hidden" name="action" value="save_valores" />
          <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />

          <div class="flex flex-col gap-3 sm:flex-row">
            <input
              id="nuevo-valor"
              type="text"
              class="flex-1 rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
              placeholder="Escribe un valor (ej: Innovación)"
            />
            <button
              id="agregar-valor"
              type="button"
              class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700"
            >
              <span class="inline-flex h-5 w-5 items-center justify-center rounded-md bg-white/15 text-base leading-none">+</span>
              Agregar
            </button>
          </div>

          <div id="valores-lista" class="space-y-2">
            <?php foreach (($valores ?? []) as $valor) : ?>
              <div class="flex items-center gap-3 rounded-xl border border-neutral-200 bg-white px-4 py-3">
                <input type="hidden" name="valores[]" value="<?php echo htmlspecialchars((string) ($valor['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" />
                <div class="flex-1 text-sm text-neutral-800">
                  <?php echo htmlspecialchars((string) ($valor['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <button type="button" class="quitar-valor inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-red-700">
                  Eliminar
                </button>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="flex justify-end gap-3">
            <a
              data-js-cancel-valores="1"
              href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>"
              class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100"
            >
              Cancelar
            </a>
            <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
              Guardar cambios
            </button>
          </div>
        </form>
      </div>

    <div id="valores-display" class="<?php echo ($edit ?? '') === 'valores' ? 'hidden' : 'block'; ?>">
      <?php if (empty($valores)) : ?>
        <p class="mt-4 text-sm text-neutral-600">Aún no se registraron valores. Presiona el lápiz para agregarlos.</p>
      <?php else : ?>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
          <?php foreach ($valores as $valor) : ?>
            <div class="rounded-xl border border-neutral-200 bg-white px-4 py-3">
              <div class="flex items-start justify-between gap-3">
                <div class="text-sm text-neutral-800">
                  <?php echo htmlspecialchars((string) ($valor['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>
<?php elseif ($panel === 'objetivos') : ?>
  <section id="panel-objetivos" class="project-panel bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center justify-between gap-3">
      <h2 class="text-lg font-semibold">Objetivos</h2>
    </div>
    <?php if (($objetivosError ?? '') !== '') : ?>
      <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
        <?php echo htmlspecialchars((string) $objetivosError, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <div id="objetivos-batch-card" class="mt-6 rounded-2xl border border-neutral-200 bg-white p-5">
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <div class="text-sm font-semibold text-neutral-900">Agregar objetivos (en lote)</div>
          <div class="mt-0.5 text-xs text-neutral-600">Agrega varios y guarda una sola vez.</div>
        </div>
      </div>

      <form id="objetivos-batch-form" class="mt-4 space-y-4" method="post" action="detalle-proyecto.php">
        <input type="hidden" name="action" value="save_objetivos_batch" />
        <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
        <input type="hidden" name="payload" id="objetivos-batch-payload" value="" />

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
            <div class="text-sm font-semibold text-neutral-900">Nuevos objetivos estratégicos</div>
            <div class="mt-0.5 text-xs text-neutral-600">Crea 2, 3 o más antes de guardar.</div>

            <div class="mt-3 space-y-3">
              <textarea
                id="oe-batch-input"
                rows="3"
                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none resize-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                placeholder="Escribe un objetivo estratégico..."
              ></textarea>

              <div class="flex justify-end">
                <button
                  id="oe-batch-add"
                  type="button"
                  class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                >
                  + Añadir a lista
                </button>
              </div>
            </div>

            <div id="oe-batch-list" class="mt-4 space-y-3"></div>
          </div>

          <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
            <div class="text-sm font-semibold text-neutral-900">Nuevos objetivos específicos</div>
            <div class="mt-0.5 text-xs text-neutral-600">
              Agrega varios objetivos específicos para distintos objetivos estratégicos y guarda una sola vez.
            </div>

            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
              <div class="text-xs text-neutral-500">Cada fila puede incluir varios objetivos (uno por línea).</div>
              <button
                id="oesp-batch-add-row"
                type="button"
                class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-300 bg-white px-3 text-xs font-semibold text-neutral-700 hover:bg-neutral-100"
              >
                + Fila
              </button>
            </div>

            <div id="oesp-batch-rows" class="mt-3 space-y-3">
              <div class="rounded-xl border border-neutral-200 bg-white p-3" data-oesp-row>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-7">
                  <div class="sm:col-span-3">
                    <select
                      class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                      data-oesp-oe
                    >
                      <option value="">Selecciona un objetivo estratégico</option>
                      <?php foreach ($objetivosEstrategicos as $obj) : ?>
                        <?php $oeTokenOpt = (string) ($obj['token'] ?? ''); ?>
                        <?php if ($oeTokenOpt !== '') : ?>
                          <option value="<?php echo htmlspecialchars($oeTokenOpt, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars((string) ($obj['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                          </option>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="sm:col-span-3">
                    <textarea
                      rows="3"
                      class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none resize-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                      placeholder="Escribe objetivos específicos (uno por línea)..."
                      data-oesp-lines
                    ></textarea>
                  </div>
                  <div class="sm:col-span-1 flex sm:justify-end">
                    <button
                      type="button"
                      class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-red-600 px-3 text-sm font-semibold text-white hover:bg-red-700"
                      data-oesp-remove
                    >
                      Quitar
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap justify-end gap-3">
          <button
            id="objetivos-batch-clear"
            type="button"
            class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100"
          >
            Limpiar
          </button>
          <button
            type="submit"
            class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700"
          >
            Guardar todo
          </button>
        </div>
      </form>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2">
      <?php if (empty($objetivosEstrategicos)) : ?>
        <div class="rounded-2xl border border-neutral-200 bg-neutral-50 px-5 py-4 text-sm text-neutral-700 sm:col-span-2">
          Aún no hay objetivos estratégicos registrados.
        </div>
      <?php else : ?>
        <?php foreach ($objetivosEstrategicos as $obj) : ?>
          <?php
            $oeToken = (string) ($obj['token'] ?? '');
            $idObjEst = (int) ($obj['id_objetivo_est'] ?? 0);
            $especificosCount = (int) ($obj['especificos_count'] ?? 0);
            $especificos = $objetivosEspecificosByEstrategico[$idObjEst] ?? [];
          ?>
          <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="flex flex-wrap items-center gap-2">
                  <div class="text-sm font-semibold text-neutral-900">Objetivo estratégico</div>
                  <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-800">
                    <?php echo (int) $especificosCount; ?> específicos
                  </span>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <a
                  data-js-edit-oe="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>"
                  href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&section=objetivos&oe_edit=<?php echo urlencode($oeToken); ?>"
                  class="inline-flex items-center justify-center rounded-xl border border-neutral-200 bg-white p-2 text-brand-700 hover:bg-brand-50"
                  aria-label="Editar objetivo estratégico"
                  title="Editar"
                >
                  <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 4h-4a2 2 0 00-2 2v4m14-4l-9 9-4 1 1-4 9-9 3 3z" />
                  </svg>
                </a>
                <form method="post" action="detalle-proyecto.php" data-confirm="¿Eliminar este objetivo estratégico y todos sus objetivos específicos?">
                  <input type="hidden" name="action" value="delete_obj_est" />
                  <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
                  <input type="hidden" name="oe" value="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>" />
                  <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                    Eliminar
                  </button>
                </form>
              </div>
            </div>

            <div data-oe-card="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>">
              <div data-oe-view class="<?php echo (($oeEditToken ?? '') !== '' && hash_equals((string) ($oeEditToken ?? ''), $oeToken)) ? 'hidden' : 'block'; ?> mt-4 text-sm text-neutral-700 leading-relaxed">
                <?php echo nl2br(htmlspecialchars((string) ($obj['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8')); ?>
              </div>

              <div data-oe-form class="<?php echo (($oeEditToken ?? '') !== '' && hash_equals((string) ($oeEditToken ?? ''), $oeToken)) ? 'block' : 'hidden'; ?> mt-4">
                <form class="space-y-3" method="post" action="detalle-proyecto.php">
                  <input type="hidden" name="action" value="update_obj_est" />
                  <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
                  <input type="hidden" name="oe" value="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>" />
                  <textarea
                    name="descripcion"
                    rows="4"
                    class="w-full rounded-xl border border-neutral-300 px-4 py-3 text-sm outline-none resize-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                    required
                  ><?php echo htmlspecialchars((string) ($obj['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                  <div class="flex justify-end gap-3">
                    <a
                      data-js-cancel-oe="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>"
                      href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&section=objetivos"
                      class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100"
                    >
                      Cancelar
                    </a>
                    <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                      Guardar
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <div class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
              <div class="flex items-center justify-between gap-3">
                <div>
                  <div class="text-sm font-semibold text-neutral-900">Objetivos específicos</div>
                  <div class="mt-0.5 text-xs text-neutral-600">Cada objetivo específico pertenece a este objetivo estratégico.</div>
                </div>
              </div>

              <div class="mt-4 rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-neutral-700">
                Para agregar nuevos objetivos específicos en lote, usa el editor de arriba y luego presiona “Guardar todo”.
              </div>

              <?php if (empty($especificos)) : ?>
                <div class="mt-4 text-sm text-neutral-600">Aún no hay objetivos específicos registrados.</div>
              <?php else : ?>
                <div class="mt-4 space-y-2">
                  <?php foreach ($especificos as $esp) : ?>
                    <?php $oespToken = (string) ($esp['token'] ?? ''); ?>
                    <div class="rounded-xl border border-neutral-200 bg-white px-4 py-3">
                      <div data-oesp-row="<?php echo htmlspecialchars($oespToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <div data-oesp-view class="<?php echo (($oespEditToken ?? '') !== '' && hash_equals((string) ($oespEditToken ?? ''), $oespToken)) ? 'hidden' : 'flex'; ?> items-start justify-between gap-3">
                          <div class="text-sm text-neutral-800">
                            <?php echo htmlspecialchars((string) ($esp['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                          </div>
                          <div class="flex items-center gap-2">
                            <a
                              data-js-edit-oesp="<?php echo htmlspecialchars($oespToken, ENT_QUOTES, 'UTF-8'); ?>"
                              href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&section=objetivos&oesp_edit=<?php echo urlencode($oespToken); ?>"
                              class="inline-flex items-center justify-center rounded-xl border border-neutral-200 bg-white p-2 text-brand-700 hover:bg-brand-50"
                              aria-label="Editar objetivo específico"
                              title="Editar"
                            >
                              <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 4h-4a2 2 0 00-2 2v4m14-4l-9 9-4 1 1-4 9-9 3 3z" />
                              </svg>
                            </a>
                            <form method="post" action="detalle-proyecto.php" data-confirm="¿Eliminar este objetivo específico?">
                              <input type="hidden" name="action" value="delete_obj_esp" />
                              <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
                              <input type="hidden" name="oe" value="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>" />
                              <input type="hidden" name="oesp" value="<?php echo htmlspecialchars($oespToken, ENT_QUOTES, 'UTF-8'); ?>" />
                              <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                Eliminar
                              </button>
                            </form>
                          </div>
                        </div>

                        <div data-oesp-form class="<?php echo (($oespEditToken ?? '') !== '' && hash_equals((string) ($oespEditToken ?? ''), $oespToken)) ? 'block' : 'hidden'; ?>">
                          <form class="flex flex-col gap-3 sm:flex-row sm:items-center" method="post" action="detalle-proyecto.php">
                            <input type="hidden" name="action" value="update_obj_esp" />
                            <input type="hidden" name="t" value="<?php echo htmlspecialchars((string) $projectToken, ENT_QUOTES, 'UTF-8'); ?>" />
                            <input type="hidden" name="oe" value="<?php echo htmlspecialchars($oeToken, ENT_QUOTES, 'UTF-8'); ?>" />
                            <input type="hidden" name="oesp" value="<?php echo htmlspecialchars($oespToken, ENT_QUOTES, 'UTF-8'); ?>" />
                            <input
                              type="text"
                              name="descripcion"
                              value="<?php echo htmlspecialchars((string) ($esp['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                              class="flex-1 rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/15"
                              required
                            />
                            <div class="flex justify-end gap-2">
                              <a
                                data-js-cancel-oesp="<?php echo htmlspecialchars($oespToken, ENT_QUOTES, 'UTF-8'); ?>"
                                href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&section=objetivos"
                                class="rounded-xl border border-neutral-300 px-4 py-2 text-sm font-medium hover:bg-neutral-100"
                              >
                                Cancelar
                              </a>
                              <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                                Guardar
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
<?php elseif ($panel === 'cadena') : ?>
  <section id="panel-cadena" class="project-panel bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center justify-between gap-3">
      <h2 class="text-lg font-semibold">Cadena de valor</h2>
    </div>
    <div class="mt-4 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
      <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <div class="text-sm font-semibold text-neutral-900">Autodiagnóstico de la Cadena de Valor Interna</div>
          <div class="mt-0.5 text-xs text-neutral-600">Selecciona una valoración (0–4) por fila. El resultado se calcula automáticamente.</div>
        </div>
        <div class="text-xs text-neutral-500">Opciones: 0 · 1 · 2 · 3 · 4</div>
      </div>
    </div>

    <div class="mt-4 overflow-x-auto rounded-2xl border border-neutral-200 bg-white">
      <form id="cvi-form" class="min-w-[1060px]">
        <table class="w-full border-separate border-spacing-0 text-sm">
          <thead class="bg-neutral-100">
            <tr>
              <th colspan="2" class="border-b border-neutral-200 px-4 py-3 text-left font-semibold text-neutral-900">
                AUTODIAGNÓSTICO DE LA CADENA DE VALOR INTERNA
              </th>
              <th colspan="5" class="border-b border-l border-neutral-200 px-4 py-3 text-center font-semibold text-neutral-900">
                VALORACIÓN
              </th>
            </tr>
            <tr>
              <th class="w-14 border-b border-neutral-200 px-4 py-2 text-center text-xs font-semibold text-neutral-700">#</th>
              <th class="border-b border-l border-neutral-200 px-4 py-2 text-left text-xs font-semibold text-neutral-700">Pregunta</th>
              <th class="w-24 border-b border-l border-neutral-200 px-3 py-2 text-center text-xs font-semibold text-neutral-700">0</th>
              <th class="w-24 border-b border-l border-neutral-200 px-3 py-2 text-center text-xs font-semibold text-neutral-700">1</th>
              <th class="w-24 border-b border-l border-neutral-200 px-3 py-2 text-center text-xs font-semibold text-neutral-700">2</th>
              <th class="w-24 border-b border-l border-neutral-200 px-3 py-2 text-center text-xs font-semibold text-neutral-700">3</th>
              <th class="w-24 border-b border-l border-neutral-200 px-3 py-2 text-center text-xs font-semibold text-neutral-700">4</th>
            </tr>
          </thead>
          <tbody id="cvi-body" class="divide-y divide-neutral-200">
            <?php
              $fallback = [
                1 => 'La empresa tiene una política sistematizada de cero defectos en la producción de productos/servicios.',
                2 => 'La empresa emplea los medios productivos tecnológicamente más avanzados de su sector.',
                3 => 'La empresa dispone de un sistema de información y control de gestión eficiente y eficaz.',
                4 => 'Los medios técnicos y tecnológicos de la empresa están preparados para competir en un futuro a corto, medio y largo plazo.',
                5 => 'La empresa es un referente en su sector en I+D+i.',
                6 => 'La excelencia de los procedimientos de la empresa (ISO, etc.) son una principal fuente de ventaja competitiva.',
                7 => 'La empresa dispone de página web, y esta se emplea no sólo como escaparate virtual de productos/servicios, sino también para establecer relaciones con clientes y proveedores.',
                8 => 'Los productos/servicios que desarrolla nuestra empresa llevan incorporada una tecnología difícil de imitar.',
                9 => 'La empresa es referente en su sector en la optimización, en términos de coste, de su cadena de producción, siendo ésta una de sus principales ventajas competitivas.',
                10 => 'La informatización de la empresa es una fuente de ventaja competitiva clara respecto a sus competidores.',
                11 => 'Los canales de distribución de la empresa son una importante fuente de ventajas competitivas.',
                12 => 'Los productos/servicios de la empresa son altamente y diferencialmente valorados por el cliente respecto a nuestros competidores.',
                13 => 'La empresa dispone y ejecuta un sistemático plan de marketing y ventas.',
                14 => 'La empresa tiene optimizada su gestión financiera.',
                15 => 'La empresa busca continuamente mejorar la relación con sus clientes cortando los plazos de ejecución, personalizando la oferta o mejorando las condiciones de entrega, siempre partiendo de un plan previo.',
                16 => 'La empresa es referente en su sector en el lanzamiento de innovadores productos y servicios de éxito demostrado en el mercado.',
                17 => 'Los Recursos Humanos son especialmente responsables del éxito de la empresa, considerándolos incluso como el principal activo estratégico.',
                18 => 'Se tiene una plantilla altamente motivada, que conoce con claridad las metas, objetivos y estrategias de la organización.',
                19 => 'La empresa siempre trabaja conforme a una estrategia y objetivos claros.',
                20 => 'La gestión del circulante está optimizada.',
                21 => 'Se tiene definido claramente el posicionamiento estratégico de todos los productos de la empresa.',
                22 => 'Se dispone de una política de marca basada en la reputación que la empresa genera, en la gestión de relación con el cliente y en el posicionamiento estratégico previamente definido.',
                23 => 'La cartera de clientes de nuestra empresa está altamente fidelizada, ya que tenemos como principal propósito deleitarlos día a día.',
                24 => 'Nuestra política y equipo de ventas y marketing es una importante ventaja competitiva de nuestra empresa respecto al sector.',
                25 => 'El servicio al cliente que prestamos es una de nuestras principales ventajas competitivas respecto a nuestros competidores.',
              ];
              $cadenaPreguntas = is_array($cadenaPreguntas ?? null) ? $cadenaPreguntas : [];
              if (empty($cadenaPreguntas)) {
                foreach ($fallback as $n => $t) {
                  $cadenaPreguntas[] = ['id_pregunta' => (int) $n, 'numero' => (int) $n, 'texto' => (string) $t];
                }
              }
              $cadenaRespuestas = is_array($cadenaRespuestas ?? null) ? $cadenaRespuestas : [];
            ?>
            <?php foreach ($cadenaPreguntas as $q) : ?>
              <?php
                $qId = is_array($q) ? (int) ($q['id_pregunta'] ?? 0) : 0;
                $qNumber = is_array($q) ? (int) ($q['numero'] ?? 0) : 0;
                $qText = is_array($q) ? (string) ($q['texto'] ?? '') : '';
                $selected = array_key_exists($qId, $cadenaRespuestas) ? (int) $cadenaRespuestas[$qId] : null;
                if ($qId <= 0 || $qNumber <= 0 || $qText === '') {
                  continue;
                }
              ?>
              <tr class="cvi-row" data-cvi-row="<?php echo (int) $qId; ?>" data-cvi-number="<?php echo (int) $qNumber; ?>">
                <td class="border-b border-neutral-200 px-4 py-3 text-center text-xs font-semibold text-neutral-700">
                  <?php echo (int) $qNumber; ?>
                </td>
                <td class="border-b border-l border-neutral-200 px-4 py-3 text-sm text-neutral-800">
                  <div class="flex items-start justify-between gap-3">
                    <div class="leading-relaxed">
                      <?php echo htmlspecialchars($qText, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <span data-cvi-ref class="hidden rounded-lg bg-red-50 px-2 py-1 text-xs font-semibold text-red-700">#¡REF!</span>
                  </div>
                </td>
                <?php for ($v = 0; $v <= 4; $v++) : ?>
                  <td class="border-b border-l border-neutral-200 px-3 py-2 text-center">
                    <label class="cvi-cell flex h-12 w-full cursor-pointer items-center justify-center select-none">
                      <input
                        type="radio"
                        name="cvi_q<?php echo (int) $qId; ?>"
                        value="<?php echo (int) $v; ?>"
                        class="sr-only"
                        <?php echo ($selected !== null && (int) $selected === (int) $v) ? 'checked' : ''; ?>
                      />
                      <span class="cvi-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-neutral-300 bg-white px-3 text-sm font-semibold text-neutral-700 transition">
                        <?php echo (int) $v; ?>
                      </span>
                    </label>
                  </td>
                <?php endfor; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </form>
    </div>

    <?php
      $calcSum = (int) (($cadenaCalc ?? [])['sum'] ?? 0);
      $calcValid = (int) (($cadenaCalc ?? [])['valid'] ?? 0);
      $calcCount = (int) (($cadenaCalc ?? [])['count'] ?? 0);
      $calcPotential = ($cadenaCalc ?? [])['potential'] ?? null;
      $calcPotentialText = '—';
      $calcPotentialSub = '';
      if ($calcPotential !== null && is_numeric($calcPotential)) {
        $calcPotentialText = number_format((float) $calcPotential, 2, '.', '');
        $calcPotentialSub = ((string) round(((float) $calcPotential) * 100)) . '%';
      }
    ?>
    <div class="mt-4 rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <div class="text-sm font-semibold text-neutral-900">Resultado final</div>
          <div class="mt-0.5 text-xs text-neutral-500">Fórmula: 1 - (Σ respuestas / 100)</div>
        </div>
        <div class="flex items-center gap-2">
          <button
            id="cvi-save"
            type="button"
            class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25"
          >
            Guardar Evaluación
          </button>
        </div>
      </div>
      <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
          <div class="text-xs font-medium text-neutral-600">Suma</div>
          <div id="cvi-sum" class="mt-1 text-2xl font-semibold text-neutral-900"><?php echo (int) $calcSum; ?></div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
          <div class="text-xs font-medium text-neutral-600">Filas válidas</div>
          <div id="cvi-valid" class="mt-1 text-2xl font-semibold text-neutral-900"><?php echo (int) $calcValid; ?>/<?php echo (int) $calcCount; ?></div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
          <div class="text-xs font-medium text-neutral-600">Potencial de mejora</div>
          <div id="cvi-result" class="mt-1 text-2xl font-semibold text-brand-900"><?php echo htmlspecialchars($calcPotentialText, ENT_QUOTES, 'UTF-8'); ?></div>
          <div id="cvi-result-sub" class="mt-1 text-xs text-neutral-500"><?php echo htmlspecialchars($calcPotentialSub, ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
      </div>
      <div class="mt-4 text-xs text-neutral-600">
        POTENCIAL DE MEJORA DE LA CADENA DE VALOR INTERNA
      </div>
    </div>
    <div class="mt-4 rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <div class="text-sm font-semibold text-neutral-900">FODA</div>
          <div class="mt-0.5 text-xs text-neutral-500">Fortalezas y debilidades obtenidas desde Cadena de valor.</div>
        </div>
        <button
          id="foda-save"
          type="button"
          class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25"
        >
          Guardar FODA
        </button>
      </div>

      <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
          <div class="flex items-center justify-between gap-3">
            <div class="text-sm font-semibold text-neutral-900">Fortalezas</div>
            <button id="foda-add-fortaleza" type="button" class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
              Agregar
            </button>
          </div>
          <div class="mt-3 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                <tr>
                  <th scope="col" class="w-14 px-4 py-3 text-center">#</th>
                  <th scope="col" class="px-4 py-3">Descripción</th>
                  <th scope="col" class="w-24 px-4 py-3 text-right">Acción</th>
                </tr>
              </thead>
              <tbody id="foda-fortalezas-body" class="divide-y divide-neutral-200">
                <?php
                  $fortRows = array_values(array_filter(array_map('trim', array_map('strval', $fodaFortalezas ?? []))));
                  $fortTarget = max(1, count($fortRows));
                  for ($i = 0; $i < $fortTarget; $i++) :
                    $value = $fortRows[$i] ?? '';
                ?>
                  <tr data-foda-row="fortaleza">
                    <td class="px-4 py-3 text-center text-xs font-semibold text-neutral-600"><?php echo $i + 1; ?></td>
                    <td class="px-4 py-2">
                      <input type="text" value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" class="foda-input h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200" />
                    </td>
                    <td class="px-4 py-2 text-right">
                      <button type="button" class="foda-remove inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
                        Quitar
                      </button>
                    </td>
                  </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
          <div class="flex items-center justify-between gap-3">
            <div class="text-sm font-semibold text-neutral-900">Debilidades</div>
            <button id="foda-add-debilidad" type="button" class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
              Agregar
            </button>
          </div>
          <div class="mt-3 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                <tr>
                  <th scope="col" class="w-14 px-4 py-3 text-center">#</th>
                  <th scope="col" class="px-4 py-3">Descripción</th>
                  <th scope="col" class="w-24 px-4 py-3 text-right">Acción</th>
                </tr>
              </thead>
              <tbody id="foda-debilidades-body" class="divide-y divide-neutral-200">
                <?php
                  $debRows = array_values(array_filter(array_map('trim', array_map('strval', $fodaDebilidades ?? []))));
                  $debTarget = max(1, count($debRows));
                  for ($i = 0; $i < $debTarget; $i++) :
                    $value = $debRows[$i] ?? '';
                ?>
                  <tr data-foda-row="debilidad">
                    <td class="px-4 py-3 text-center text-xs font-semibold text-neutral-600"><?php echo $i + 1; ?></td>
                    <td class="px-4 py-2">
                      <input type="text" value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" class="foda-input h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200" />
                    </td>
                    <td class="px-4 py-2 text-right">
                      <button type="button" class="foda-remove inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
                        Quitar
                      </button>
                    </td>
                  </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div id="cvi-toast" class="pointer-events-none fixed bottom-6 right-6 z-50 hidden w-full max-w-sm">
      <div id="cvi-toast-card" class="pointer-events-auto rounded-2xl border border-neutral-200 bg-white p-4 shadow-lg">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div id="cvi-toast-title" class="text-sm font-semibold text-neutral-900"></div>
            <div id="cvi-toast-msg" class="mt-1 text-sm text-neutral-700"></div>
          </div>
          <button id="cvi-toast-close" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50">
            <span class="sr-only">Cerrar</span>
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </section>
<?php elseif ($panel === 'perfil_competitivo') : ?>
  <?php require dirname(__DIR__) . '/proyectos/panel-perfil-competitivo.php'; ?>
<?php elseif ($panel === 'pest') : ?>
  <?php require dirname(__DIR__) . '/proyectos/panel-pest.php'; ?>
<?php elseif ($panel === 'bgg') : ?>
  <section id="panel-bgg" class="project-panel bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
      <div>
        <h2 class="text-lg font-semibold">Autodiagnóstico BCG</h2>
        <p class="mt-1 text-sm text-neutral-600">Edita celdas y el sistema recalcula automáticamente.</p>
      </div>
      <div class="flex items-center gap-2">
        <button id="bcg-recalc-btn" type="button" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-800 hover:bg-neutral-50">
          Recalcular
        </button>
      </div>
    </div>

    <div id="bcg-loading" class="mt-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
      <div class="flex items-center gap-2 text-sm text-neutral-600">
        <svg class="h-4 w-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        <span>Cargando…</span>
      </div>
    </div>

    <div id="bcg-error" class="mt-5 hidden rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800"></div>

    <div id="bcg-app" class="mt-5 hidden">
      <div class="space-y-6">
        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
          <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm font-semibold text-neutral-900">PREVISIÓN DE VENTAS</div>
            <button id="bcg-products-save" type="button" class="inline-flex h-9 items-center justify-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-semibold text-white hover:bg-brand-700 disabled:opacity-50" disabled>
              <svg data-bcg-spinner class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Guardar cambios</span>
            </button>
          </div>
          <div class="mt-0.5 text-xs text-neutral-500">Edita ventas por producto. El sistema recalcula % sobre total.</div>

          <div class="mt-4 flex flex-col gap-3 sm:flex-row">
            <input id="bcg-new-product-name" type="text" class="h-10 flex-1 rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="Producto" />
            <input id="bcg-new-product-sales" type="number" min="0" step="0.01" class="h-10 w-full sm:w-56 rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="Ventas empresa" />
            <button id="bcg-add-product" type="button" class="h-10 rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-700">
              Agregar
            </button>
          </div>

          <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[1fr_360px]">
            <div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white">
              <table class="min-w-[860px] w-full text-left text-sm">
                <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                  <tr>
                    <th class="w-64 px-4 py-3">PRODUCTOS</th>
                    <th class="w-56 px-4 py-3">VENTAS</th>
                    <th class="w-44 px-4 py-3">% S/ TOTAL</th>
                    <th class="w-32 px-4 py-3 text-right">ACCIÓN</th>
                  </tr>
                </thead>
                <tbody id="bcg-products-body" class="divide-y divide-neutral-200"></tbody>
                <tfoot class="bg-neutral-50 text-xs font-semibold text-neutral-700">
                  <tr>
                    <td class="px-4 py-3">TOTAL</td>
                    <td id="bcg-total-ventas-inline" class="px-4 py-3">0</td>
                    <td class="px-4 py-3">100%</td>
                    <td class="px-4 py-3"></td>
                  </tr>
                </tfoot>
              </table>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-4">
              <div class="text-xs font-semibold text-neutral-600">RESULTADO GLOBAL</div>
              <div class="mt-3 grid grid-cols-1 gap-3">
                <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                  <div class="text-xs font-medium text-neutral-600">Total de ventas</div>
                  <div id="bcg-total-ventas" class="mt-1 text-2xl font-semibold text-neutral-900">0</div>
                </div>
                <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                  <div class="text-xs font-medium text-neutral-600">Último cálculo</div>
                  <div id="bcg-fecha-calculo" class="mt-1 text-sm font-semibold text-neutral-900">—</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
          <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm font-semibold text-neutral-900">TASAS DE CRECIMIENTO DEL MERCADO (TCM)</div>
            <button id="bcg-market-save-all" type="button" class="inline-flex h-9 items-center justify-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-semibold text-white hover:bg-brand-700 disabled:opacity-50" disabled>
              <svg data-bcg-spinner class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Guardar cambios</span>
            </button>
          </div>
          <div class="mt-0.5 text-xs text-neutral-500">Ingresa el año de inicio. El sistema genera el periodo automáticamente: 2017-2018, 2018-2019, ...</div>

          <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-4">
            <input id="bcg-market-year" type="number" min="1900" max="2100" class="h-10 rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="Año inicio (ej. 2017)" />
            <select id="bcg-market-product" class="h-10 rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200"></select>
            <input id="bcg-market-demand" type="number" min="0" step="0.01" class="h-10 rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200" placeholder="Tasa % (opcional)" />
            <button id="bcg-market-save" type="button" class="h-10 rounded-xl bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700">
              Agregar tasa
            </button>
          </div>

          <div class="mt-4 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
            <table class="min-w-[980px] w-full text-left text-sm">
              <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                <tr id="bcg-market-head-row">
                  <th class="w-24 px-4 py-3">PERIODOS</th>
                </tr>
              </thead>
              <tbody id="bcg-market-matrix-body" class="divide-y divide-neutral-200"></tbody>
            </table>
          </div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
          <div class="text-sm font-semibold text-neutral-900">RESULTADOS AUTOMÁTICOS</div>
          <div class="mt-4 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
            <table class="min-w-[980px] w-full text-left text-sm">
              <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                <tr id="bcg-summary-head-row">
                  <th class="w-28 px-4 py-3">INDICADOR</th>
                </tr>
              </thead>
              <tbody id="bcg-summary-body" class="divide-y divide-neutral-200"></tbody>
            </table>
          </div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
          <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm font-semibold text-neutral-900">EVOLUCIÓN DE LA DEMANDA GLOBAL SECTOR (POR PRODUCTO)</div>
            <button id="bcg-sector-save-all" type="button" class="inline-flex h-9 items-center justify-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-semibold text-white hover:bg-brand-700 disabled:opacity-50" disabled>
              <svg data-bcg-spinner class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Guardar cambios</span>
            </button>
          </div>
          <div class="mt-0.5 text-xs text-neutral-500">Los años se generan automáticamente desde las tasas registradas en TCM. Edita las celdas y guarda.</div>

          <div class="mt-4 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
            <table class="min-w-[980px] w-full text-left text-sm">
              <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                <tr id="bcg-sector-head-row">
                  <th class="w-24 px-4 py-3">AÑOS</th>
                </tr>
              </thead>
              <tbody id="bcg-sector-matrix-body" class="divide-y divide-neutral-200"></tbody>
            </table>
          </div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
          <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm font-semibold text-neutral-900">NIVELES DE VENTA DE LOS COMPETIDORES (POR PRODUCTO)</div>
            <button id="bcg-competitors-save-all" type="button" class="inline-flex h-9 items-center justify-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-semibold text-white hover:bg-brand-700 disabled:opacity-50" disabled>
              <svg data-bcg-spinner class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Guardar cambios</span>
            </button>
          </div>
          <div class="mt-0.5 text-xs text-neutral-500">PRM se calcula usando el mayor competidor.</div>

          <div id="bcg-competitors-grid" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"></div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
          <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <div class="text-sm font-semibold text-neutral-900">Matriz BCG</div>
              <div class="mt-1 text-xs text-neutral-500">X = PRM · Y = TCM (%) · Burbuja = % ventas.</div>
            </div>
          </div>
          <div class="mt-4 flex flex-col items-stretch gap-4 md:flex-row md:items-start md:gap-6">
            <div class="rounded-xl border border-neutral-200 bg-white p-3 md:flex-none" style="flex: 0 0 1040px; width: 1040px; max-width: 100%;">
              <canvas id="bcg-chart" height="500" style="display:block; width:100%;"></canvas>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-4 md:min-w-0 md:flex-1">
              <div class="text-xs font-semibold text-neutral-600">LEYENDA</div>
              <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-neutral-800">
                <div class="rounded-lg border border-neutral-200 bg-neutral-50 px-3 py-2">
                  <div class="font-semibold">Estrella</div>
                  <div class="mt-0.5 text-neutral-600">TCM &gt; 10% · PRM ≥ 1</div>
                </div>
                <div class="rounded-lg border border-neutral-200 bg-neutral-50 px-3 py-2">
                  <div class="font-semibold">Interrogante</div>
                  <div class="mt-0.5 text-neutral-600">TCM &gt; 10% · PRM &lt; 1</div>
                </div>
                <div class="rounded-lg border border-neutral-200 bg-neutral-50 px-3 py-2">
                  <div class="font-semibold">Vaca</div>
                  <div class="mt-0.5 text-neutral-600">TCM ≤ 10% · PRM ≥ 1</div>
                </div>
                <div class="rounded-lg border border-neutral-200 bg-neutral-50 px-3 py-2">
                  <div class="font-semibold">Perro</div>
                  <div class="mt-0.5 text-neutral-600">TCM ≤ 10% · PRM &lt; 1</div>
                </div>
              </div>

              <div class="mt-4">
                <div class="text-xs font-semibold text-neutral-600">PRODUCTOS</div>
                <div class="mt-2 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
                  <table class="min-w-full text-left text-xs">
                    <thead class="bg-neutral-50 text-[11px] font-semibold text-neutral-600">
                      <tr>
                        <th class="px-3 py-2">Producto</th>
                        <th class="px-3 py-2">Cuadrante</th>
                        <th class="px-3 py-2 text-right">PRM</th>
                        <th class="px-3 py-2 text-right">TCM</th>
                        <th class="px-3 py-2 text-right">% Ventas</th>
                      </tr>
                    </thead>
                    <tbody id="bcg-legend-products" class="divide-y divide-neutral-200"></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
          <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm font-semibold text-neutral-900">FODA (AUTODIAGNÓSTICO BCG)</div>
            <button id="bcg-foda-save" type="button" class="inline-flex h-9 items-center justify-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-semibold text-white hover:bg-brand-700 disabled:opacity-50">
              <svg data-bcg-spinner class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Guardar cambios</span>
            </button>
          </div>
          <div class="mt-0.5 text-xs text-neutral-500">Agrega y edita los ítems. Solo se guardan descripciones no vacías.</div>

          <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
            <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
              <div class="flex items-center justify-between gap-3">
                <div class="text-sm font-semibold text-neutral-900">Fortalezas</div>
                <button id="bcg-foda-add-fortaleza" type="button" class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
                  Agregar
                </button>
              </div>
              <div class="mt-3 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
                <table class="min-w-full text-left text-sm">
                  <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                    <tr>
                      <th scope="col" class="w-14 px-4 py-3 text-center">#</th>
                      <th scope="col" class="px-4 py-3">Descripción</th>
                      <th scope="col" class="w-24 px-4 py-3 text-right">Acción</th>
                    </tr>
                  </thead>
                  <tbody id="bcg-foda-fortalezas-body" class="divide-y divide-neutral-200">
                    <?php
                      $rows = array_values(array_filter(array_map('trim', array_map('strval', $bcgFortalezas ?? []))));
                      $target = max(1, count($rows));
                      for ($i = 0; $i < $target; $i++) :
                        $value = $rows[$i] ?? '';
                    ?>
                      <tr data-bcg-foda-row="FORTALEZA">
                        <td class="px-4 py-3 text-center text-xs font-semibold text-neutral-600"><?php echo $i + 1; ?></td>
                        <td class="px-4 py-2">
                          <input type="text" value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" class="bcg-foda-input h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200" />
                        </td>
                        <td class="px-4 py-2 text-right">
                          <button type="button" class="bcg-foda-remove inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">Quitar</button>
                        </td>
                      </tr>
                    <?php endfor; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
              <div class="flex items-center justify-between gap-3">
                <div class="text-sm font-semibold text-neutral-900">Debilidades</div>
                <button id="bcg-foda-add-debilidad" type="button" class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
                  Agregar
                </button>
              </div>
              <div class="mt-3 overflow-x-auto rounded-xl border border-neutral-200 bg-white">
                <table class="min-w-full text-left text-sm">
                  <thead class="bg-neutral-50 text-xs font-semibold text-neutral-600">
                    <tr>
                      <th scope="col" class="w-14 px-4 py-3 text-center">#</th>
                      <th scope="col" class="px-4 py-3">Descripción</th>
                      <th scope="col" class="w-24 px-4 py-3 text-right">Acción</th>
                    </tr>
                  </thead>
                  <tbody id="bcg-foda-debilidades-body" class="divide-y divide-neutral-200">
                    <?php
                      $rows = array_values(array_filter(array_map('trim', array_map('strval', $bcgDebilidades ?? []))));
                      $target = max(1, count($rows));
                      for ($i = 0; $i < $target; $i++) :
                        $value = $rows[$i] ?? '';
                    ?>
                      <tr data-bcg-foda-row="DEBILIDAD">
                        <td class="px-4 py-3 text-center text-xs font-semibold text-neutral-600"><?php echo $i + 1; ?></td>
                        <td class="px-4 py-2">
                          <input type="text" value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" class="bcg-foda-input h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200" />
                        </td>
                        <td class="px-4 py-2 text-right">
                          <button type="button" class="bcg-foda-remove inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">Quitar</button>
                        </td>
                      </tr>
                    <?php endfor; ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <div id="bcg-toast" class="pointer-events-none fixed bottom-6 right-6 z-50 hidden w-full max-w-sm">
      <div class="pointer-events-auto rounded-2xl border border-neutral-200 bg-white p-4 shadow-lg">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div id="bcg-toast-title" class="text-sm font-semibold text-neutral-900"></div>
            <div id="bcg-toast-msg" class="mt-1 text-sm text-neutral-700"></div>
          </div>
          <button id="bcg-toast-close" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50">
            <span class="sr-only">Cerrar</span>
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </section>
<?php elseif ($panel === 'overview') : ?>
  <section id="panel-overview" class="project-panel bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm">
    <div class="flex items-start justify-between gap-3">
      <div>
        <h2 class="text-lg font-semibold">Overview</h2>
        <p class="mt-1 text-sm text-neutral-600">Resumen general del proyecto.</p>
      </div>
      <div class="flex flex-wrap items-center justify-end gap-2">
        <a
          href="detalle-proyecto.php?t=<?php echo urlencode((string) $projectToken); ?>&export=overview_pdf"
          download
          class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-brand-700"
        >
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-3-3m3 3l3-3" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 17v3a1 1 0 001 1h14a1 1 0 001-1v-3" />
          </svg>
          Descargar PDF
        </a>
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
    </div>

    <div class="mt-5 space-y-4">
      <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
        <div class="text-sm font-semibold text-neutral-900">Misión</div>
        <?php $m = trim((string) ($misionTexto ?? '')); ?>
        <?php if ($m === '') : ?>
          <div class="mt-3 text-sm text-neutral-600">Aún no se registró la misión.</div>
        <?php else : ?>
          <div class="mt-3 text-sm text-neutral-700 leading-relaxed">
            <?php echo nl2br(htmlspecialchars($m, ENT_QUOTES, 'UTF-8')); ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
        <div class="text-sm font-semibold text-neutral-900">Visión</div>
        <?php $v = trim((string) ($visionTexto ?? '')); ?>
        <?php if ($v === '') : ?>
          <div class="mt-3 text-sm text-neutral-600">Aún no se registró la visión.</div>
        <?php else : ?>
          <div class="mt-3 text-sm text-neutral-700 leading-relaxed">
            <?php echo nl2br(htmlspecialchars($v, ENT_QUOTES, 'UTF-8')); ?>
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
        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
          <div class="text-sm text-neutral-600">Objetivos estratégicos y sus objetivos específicos.</div>
          <button
            type="button"
            data-open-panel="objetivos"
            class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50"
          >
            Gestionar
          </button>
        </div>

        <?php if (!empty($objetivosError)) : ?>
          <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <?php echo htmlspecialchars((string) $objetivosError, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php elseif (empty($objetivosEstrategicos)) : ?>
          <div class="mt-4 text-sm text-neutral-600">Aún no hay objetivos registrados.</div>
        <?php else : ?>
          <div class="mt-4 space-y-3">
            <?php foreach ($objetivosEstrategicos as $obj) : ?>
              <?php
                $idObjEst = (int) ($obj['id_objetivo_est'] ?? 0);
                $especificos = $objetivosEspecificosByEstrategico[$idObjEst] ?? [];
                $especificos = is_array($especificos) ? $especificos : [];
              ?>
              <div class="rounded-xl border border-neutral-200 bg-white p-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                  <div>
                    <div class="text-xs font-semibold text-neutral-600">Objetivo estratégico</div>
                    <div class="mt-2 text-sm text-neutral-800 leading-relaxed">
                      <?php echo nl2br(htmlspecialchars((string) ($obj['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8')); ?>
                    </div>
                  </div>
                  <div>
                    <div class="text-xs font-semibold text-neutral-600">Objetivos específicos</div>
                    <?php if (empty($especificos)) : ?>
                      <div class="mt-2 text-sm text-neutral-600">Sin objetivos específicos.</div>
                    <?php else : ?>
                      <ul class="mt-2 space-y-1 text-sm text-neutral-800">
                        <?php foreach ($especificos as $esp) : ?>
                          <li class="flex gap-2">
                            <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-neutral-400"></span>
                            <span class="leading-relaxed">
                              <?php echo htmlspecialchars((string) ($esp['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <?php
        $cadenaOverview = is_array($cadenaOverview ?? null) ? (array) $cadenaOverview : [];
        $bcgOverview = is_array($bcgOverview ?? null) ? (array) $bcgOverview : [];
        $fodaOverview = is_array($fodaOverview ?? null) ? (array) $fodaOverview : [];

        $cBadge = (string) ($cadenaOverview['badge_class'] ?? 'bg-neutral-100 text-neutral-700');
        $cStatus = (string) ($cadenaOverview['status_label'] ?? 'Sin evaluación');
        $cSub = (string) ($cadenaOverview['status_sub'] ?? '');
        $cSum = $cadenaOverview['sum'] ?? null;
        $cPotential = $cadenaOverview['potential'] ?? null;
        $cPotentialText = ($cPotential !== null && is_numeric($cPotential)) ? number_format((float) $cPotential, 2, '.', '') : '—';
        $cPotentialPct = ($cPotential !== null && is_numeric($cPotential)) ? ((string) round(((float) $cPotential) * 100) . '%') : '';

        $bBadge = (string) ($bcgOverview['badge_class'] ?? 'bg-neutral-100 text-neutral-700');
        $bStatus = (string) ($bcgOverview['status_label'] ?? 'Sin datos');
        $bSub = (string) ($bcgOverview['status_sub'] ?? '');
        $bTotal = (int) ($bcgOverview['total'] ?? 0);
        $bCounts = is_array($bcgOverview['counts'] ?? null) ? (array) $bcgOverview['counts'] : [];
        $bTop = is_array($bcgOverview['top'] ?? null) ? (array) $bcgOverview['top'] : [];

        $fCadena = is_array($fodaOverview['CADENA_VALOR_INTERNA'] ?? null) ? (array) $fodaOverview['CADENA_VALOR_INTERNA'] : [];
        $fBcg = is_array($fodaOverview['AUTODIAGNOSTICO_BCG'] ?? null) ? (array) $fodaOverview['AUTODIAGNOSTICO_BCG'] : [];
        $fCadenaLabel = (string) ($fCadena['label'] ?? 'Cadena de valor');
        $fBcgLabel = (string) ($fBcg['label'] ?? 'Matriz BCG');
        $fFortCadena = is_array($fCadena['FORTALEZA'] ?? null) ? (array) $fCadena['FORTALEZA'] : [];
        $fDebCadena = is_array($fCadena['DEBILIDAD'] ?? null) ? (array) $fCadena['DEBILIDAD'] : [];
        $fFortBcg = is_array($fBcg['FORTALEZA'] ?? null) ? (array) $fBcg['FORTALEZA'] : [];
        $fDebBcg = is_array($fBcg['DEBILIDAD'] ?? null) ? (array) $fBcg['DEBILIDAD'] : [];
      ?>

      <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div>
            <div class="text-sm font-semibold text-neutral-900">Análisis Estratégico</div>
            <div class="mt-1 text-sm text-neutral-600">Resumen ejecutivo de Cadena de valor y Matriz BCG.</div>
          </div>
          <div class="flex flex-wrap items-center gap-2">
            <button type="button" data-open-panel="cadena" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
              Ver Cadena
            </button>
            <button type="button" data-open-panel="bgg" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
              Ver BCG
            </button>
          </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div class="rounded-2xl border border-neutral-200 bg-white p-5">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="text-sm font-semibold text-neutral-900">Cadena de valor</div>
                <div class="mt-1 text-sm text-neutral-600">Potencial de mejora y estado general.</div>
              </div>
              <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?php echo htmlspecialchars($cBadge, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($cStatus, ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </div>
            <?php if ($cSub !== '') : ?>
              <div class="mt-2 text-xs text-neutral-600"><?php echo htmlspecialchars($cSub, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
              <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                <div class="text-xs font-medium text-neutral-600">Suma</div>
                <div class="mt-1 text-2xl font-semibold text-neutral-900"><?php echo ($cSum === null) ? '—' : (int) $cSum; ?></div>
              </div>
              <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                <div class="text-xs font-medium text-neutral-600">Potencial</div>
                <div class="mt-1 text-2xl font-semibold text-brand-900"><?php echo htmlspecialchars($cPotentialText, ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="mt-1 text-xs text-neutral-500"><?php echo htmlspecialchars($cPotentialPct, ENT_QUOTES, 'UTF-8'); ?></div>
              </div>
              <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                <div class="text-xs font-medium text-neutral-600">Interpretación</div>
                <div class="mt-1 text-sm font-semibold text-neutral-900"><?php echo htmlspecialchars($cStatus, ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="mt-1 text-xs text-neutral-500">Basado en 1 - (Σ/100).</div>
              </div>
            </div>
            <?php
              $cBar = ($cPotential !== null && is_numeric($cPotential)) ? max(0, min(100, (int) round(((float) $cPotential) * 100))) : 0;
            ?>
            <div class="mt-4">
              <div class="flex items-center justify-between text-xs text-neutral-600">
                <span>Potencial de mejora</span>
                <span><?php echo htmlspecialchars($cPotentialPct !== '' ? $cPotentialPct : '—', ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
              <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-neutral-200">
                <div class="h-2 rounded-full bg-brand-600" style="width: <?php echo (int) $cBar; ?>%"></div>
              </div>
            </div>
          </div>

          <div class="rounded-2xl border border-neutral-200 bg-white p-5">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="text-sm font-semibold text-neutral-900">Matriz BCG</div>
                <div class="mt-1 text-sm text-neutral-600">Clasificación y métricas por producto.</div>
              </div>
              <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?php echo htmlspecialchars($bBadge, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($bStatus, ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </div>
            <?php if ($bSub !== '') : ?>
              <div class="mt-2 text-xs text-neutral-600"><?php echo htmlspecialchars($bSub, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
              <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                <div class="text-xs font-medium text-neutral-600">Productos</div>
                <div class="mt-1 text-2xl font-semibold text-neutral-900"><?php echo (int) $bTotal; ?></div>
                <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-neutral-700">
                  <div class="rounded-lg border border-neutral-200 bg-white px-2 py-1">Estrella: <?php echo (int) ($bCounts['ESTRELLA'] ?? 0); ?></div>
                  <div class="rounded-lg border border-neutral-200 bg-white px-2 py-1">Vaca: <?php echo (int) ($bCounts['VACA'] ?? 0); ?></div>
                  <div class="rounded-lg border border-neutral-200 bg-white px-2 py-1">Interrogante: <?php echo (int) ($bCounts['INTERROGANTE'] ?? 0); ?></div>
                  <div class="rounded-lg border border-neutral-200 bg-white px-2 py-1">Perro: <?php echo (int) ($bCounts['PERRO'] ?? 0); ?></div>
                </div>
                <?php
                  $bStar = (int) ($bCounts['ESTRELLA'] ?? 0);
                  $bCow = (int) ($bCounts['VACA'] ?? 0);
                  $bQ = (int) ($bCounts['INTERROGANTE'] ?? 0);
                  $bDog = (int) ($bCounts['PERRO'] ?? 0);
                  $bDen = max(1, $bTotal);
                  $pStar = (int) round(($bStar / $bDen) * 100);
                  $pCow = (int) round(($bCow / $bDen) * 100);
                  $pQ = (int) round(($bQ / $bDen) * 100);
                  $pDog = max(0, 100 - ($pStar + $pCow + $pQ));
                ?>
                <div class="mt-3">
                  <div class="flex items-center justify-between text-xs text-neutral-600">
                    <span>Distribución</span>
                    <span><?php echo (int) $bTotal; ?> total</span>
                  </div>
                  <div class="mt-2 flex h-2 w-full overflow-hidden rounded-full bg-neutral-200">
                    <div class="h-2 bg-emerald-500" style="width: <?php echo (int) $pStar; ?>%"></div>
                    <div class="h-2 bg-amber-400" style="width: <?php echo (int) $pCow; ?>%"></div>
                    <div class="h-2 bg-sky-500" style="width: <?php echo (int) $pQ; ?>%"></div>
                    <div class="h-2 bg-red-500" style="width: <?php echo (int) $pDog; ?>%"></div>
                  </div>
                </div>
              </div>
              <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                <div class="text-xs font-medium text-neutral-600">Top productos</div>
                <?php if (empty($bTop)) : ?>
                  <div class="mt-2 text-sm text-neutral-600">Sin registros.</div>
                <?php else : ?>
                  <div class="mt-2 space-y-2">
                    <?php foreach ($bTop as $p) : ?>
                      <?php
                        $pn = trim((string) ($p['nombre'] ?? ''));
                        $pc = trim((string) ($p['clasificacion'] ?? ''));
                        $pp = isset($p['porcentaje_ventas']) && is_numeric($p['porcentaje_ventas']) ? round(((float) $p['porcentaje_ventas']) * 100, 1) . '%' : '—';
                      ?>
                      <div class="rounded-lg border border-neutral-200 bg-white px-3 py-2">
                        <div class="flex items-center justify-between gap-2">
                          <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-neutral-900"><?php echo htmlspecialchars($pn !== '' ? $pn : 'Producto', ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="mt-0.5 text-xs text-neutral-600"><?php echo htmlspecialchars($pc !== '' ? $pc : '—', ENT_QUOTES, 'UTF-8'); ?> · Ventas: <?php echo htmlspecialchars($pp, ENT_QUOTES, 'UTF-8'); ?></div>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div>
            <div class="text-sm font-semibold text-neutral-900">Análisis FODA</div>
            <div class="mt-1 text-sm text-neutral-600">Fortalezas y debilidades derivadas de Cadena de valor y BCG.</div>
          </div>
          <div class="flex flex-wrap items-center gap-2">
            <button type="button" data-open-panel="cadena" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
              Editar (Cadena)
            </button>
            <button type="button" data-open-panel="bgg" class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
              Editar (BCG)
            </button>
          </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div class="rounded-2xl border border-neutral-200 bg-white p-4">
            <div class="text-sm font-semibold text-neutral-900">Fortalezas</div>
            <div class="mt-2 space-y-3 text-sm text-neutral-800">
              <div>
                <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fCadenaLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php if (empty($fFortCadena)) : ?>
                  <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                <?php else : ?>
                  <ul class="mt-2 space-y-1">
                    <?php foreach ($fFortCadena as $txt) : ?>
                      <li class="flex gap-2">
                        <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></span>
                        <span class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></span>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
              <div>
                <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fBcgLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php if (empty($fFortBcg)) : ?>
                  <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                <?php else : ?>
                  <ul class="mt-2 space-y-1">
                    <?php foreach ($fFortBcg as $txt) : ?>
                      <li class="flex gap-2">
                        <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></span>
                        <span class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></span>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="rounded-2xl border border-neutral-200 bg-white p-4">
            <div class="text-sm font-semibold text-neutral-900">Debilidades</div>
            <div class="mt-2 space-y-3 text-sm text-neutral-800">
              <div>
                <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fCadenaLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php if (empty($fDebCadena)) : ?>
                  <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                <?php else : ?>
                  <ul class="mt-2 space-y-1">
                    <?php foreach ($fDebCadena as $txt) : ?>
                      <li class="flex gap-2">
                        <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-red-500"></span>
                        <span class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></span>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
              <div>
                <div class="text-xs font-semibold text-neutral-600"><?php echo htmlspecialchars($fBcgLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php if (empty($fDebBcg)) : ?>
                  <div class="mt-1 text-sm text-neutral-600">Sin registros.</div>
                <?php else : ?>
                  <ul class="mt-2 space-y-1">
                    <?php foreach ($fDebBcg as $txt) : ?>
                      <li class="flex gap-2">
                        <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-red-500"></span>
                        <span class="leading-relaxed"><?php echo htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8'); ?></span>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="rounded-2xl border border-neutral-200 bg-white p-4">
            <div class="text-sm font-semibold text-neutral-900">Oportunidades</div>
            <div class="mt-2 text-sm text-neutral-600">Aún no hay una fuente registrada para oportunidades en este módulo.</div>
          </div>
          <div class="rounded-2xl border border-neutral-200 bg-white p-4">
            <div class="text-sm font-semibold text-neutral-900">Amenazas</div>
            <div class="mt-2 text-sm text-neutral-600">Aún no hay una fuente registrada para amenazas en este módulo.</div>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>
