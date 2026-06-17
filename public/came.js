(() => {
  function setButtonLoading(btn, loading, label) {
    if (!btn) return;
    if (loading) {
      btn.disabled = true;
      btn.className = "inline-flex items-center justify-center rounded-xl bg-brand-600/60 px-4 py-2 text-sm font-semibold text-white shadow-sm";
      btn.textContent = label || "Guardando…";
      return;
    }
    btn.disabled = false;
    btn.className = "inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25";
    btn.textContent = label || "Guardar Matriz";
  }

  window.RI = window.RI || {};
  window.RI.initCamePanel = function initCamePanel() {
    const panel = document.getElementById("panel-came");
    if (!panel || panel.dataset.riInit === "1") return;
    panel.dataset.riInit = "1";

    const countEl = panel.querySelector("#came-count");
    const categoriesUsedEl = panel.querySelector("#came-categories-used");
    const saveBtn = panel.querySelector("#came-save");

    const toast = panel.querySelector("#came-toast");
    const toastTitle = panel.querySelector("#came-toast-title");
    const toastMsg = panel.querySelector("#came-toast-msg");
    const toastClose = panel.querySelector("#came-toast-close");

    let toastTimer = null;
    let autosaveTimer = null;
    let autosaveInflight = false;
    let autosaveQueued = false;
    let saving = false;
    const CATEGORY_ORDER = ["C", "A", "M", "E"];

    function closeToast() {
      if (!toast) return;
      toast.classList.add("hidden");
      if (toastTimer) clearTimeout(toastTimer);
      toastTimer = null;
    }

    function showToast(title, msg) {
      if (!toast) return;
      if (toastTitle) toastTitle.textContent = String(title || "");
      if (toastMsg) toastMsg.textContent = String(msg || "");
      toast.classList.remove("hidden");
      if (toastTimer) clearTimeout(toastTimer);
      toastTimer = setTimeout(() => closeToast(), 3500);
    }

    if (toastClose) toastClose.addEventListener("click", () => closeToast());

    function createActionRow(category, value = "") {
      const tr = document.createElement("tr");
      tr.setAttribute("data-came-row", "1");
      tr.innerHTML = `
        <td class="px-4 py-3 text-center text-xs font-semibold text-neutral-700" data-came-index>1</td>
        <td class="px-4 py-2">
          <input
            type="text"
            value="${String(value || "").replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/</g, "&lt;").replace(/>/g, "&gt;")}"
            placeholder="Escribe una acción…"
            class="came-input h-10 w-full rounded-xl border border-neutral-200 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-neutral-300 focus:ring-2 focus:ring-brand-100"
            data-came-input="1"
            data-came-category-input="${category}"
          />
        </td>
        <td class="px-4 py-2 text-right">
          <button type="button" class="inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50" data-came-remove="1">
            Quitar
          </button>
        </td>
      `;
      return tr;
    }

    function ensureEmptyRow(tbody) {
      if (!tbody) return;
      const actionRows = Array.from(tbody.querySelectorAll("[data-came-row='1']"));
      const emptyRow = tbody.querySelector("[data-came-empty-row='1']");
      if (actionRows.length === 0) {
        if (!emptyRow) {
          const tr = document.createElement("tr");
          tr.setAttribute("data-came-empty-row", "1");
          tr.innerHTML = `<td colspan="3" class="px-4 py-4 text-sm text-neutral-500">No hay acciones registradas en este apartado.</td>`;
          tbody.appendChild(tr);
        }
        return;
      }
      if (emptyRow) emptyRow.remove();
    }

    function renumberCategory(tbody) {
      if (!tbody) return;
      const actionRows = Array.from(tbody.querySelectorAll("[data-came-row='1']"));
      actionRows.forEach((row, idx) => {
        const indexCell = row.querySelector("[data-came-index]");
        if (indexCell) indexCell.textContent = String(idx + 1);
      });
      ensureEmptyRow(tbody);
    }

    function allBodies() {
      return CATEGORY_ORDER
        .map((cat) => panel.querySelector(`[data-came-category="${cat}"]`))
        .filter(Boolean);
    }

    function buildState() {
      const payload = [];
      let totalActions = 0;
      let categoriesUsed = 0;

      for (const cat of CATEGORY_ORDER) {
        const tbody = panel.querySelector(`[data-came-category="${cat}"]`);
        if (!tbody) continue;
        const rows = Array.from(tbody.querySelectorAll("[data-came-row='1']"));
        let seq = 1;
        let catCount = 0;
        for (const row of rows) {
          const input = row.querySelector("[data-came-input='1']");
          const text = String(input ? input.value : "").trim();
          if (!text) continue;
          payload.push({ category: cat, position: seq, text });
          seq += 1;
          catCount += 1;
        }
        if (catCount > 0) categoriesUsed += 1;
        totalActions += catCount;
      }

      return { payload, totalActions, categoriesUsed };
    }

    function renderState(state) {
      if (countEl) countEl.textContent = String(state.totalActions);
      if (categoriesUsedEl) categoriesUsedEl.textContent = `${state.categoriesUsed}/4`;
    }

    async function postAction(action, payload) {
      const fd = new FormData();
      fd.set("action", String(action));
      fd.set("t", String(window.projectToken || ""));
      for (const [k, v] of Object.entries(payload || {})) {
        fd.set(k, String(v));
      }
      const res = await fetch("detalle-proyecto.php", {
        method: "POST",
        headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
        body: fd,
      });
      const json = await res.json().catch(() => null);
      return { ok: res.ok, json, status: res.status };
    }

    async function autosave() {
      if (autosaveInflight) {
        autosaveQueued = true;
        return;
      }
      autosaveInflight = true;
      autosaveQueued = false;

      try {
        const state = buildState();
        renderState(state);
        await postAction("save_came_autosave_batch", { acciones: JSON.stringify(state.payload) });
      } catch (e) {
      } finally {
        autosaveInflight = false;
        if (autosaveQueued) autosave();
      }
    }

    function scheduleAutosave() {
      if (autosaveTimer) clearTimeout(autosaveTimer);
      autosaveTimer = setTimeout(() => autosave(), 450);
    }

    panel.addEventListener("click", (event) => {
      const target = event.target;
      if (!(target instanceof Element)) return;

      const addBtn = target.closest("[data-came-add]");
      if (addBtn) {
        const category = String(addBtn.getAttribute("data-came-add") || "");
        const tbody = panel.querySelector(`[data-came-category="${category}"]`);
        if (!tbody) return;
        const emptyRow = tbody.querySelector("[data-came-empty-row='1']");
        if (emptyRow) emptyRow.remove();
        const tr = createActionRow(category, "");
        tbody.appendChild(tr);
        renumberCategory(tbody);
        const input = tr.querySelector("[data-came-input='1']");
        if (input instanceof HTMLInputElement) input.focus();
        scheduleAutosave();
        return;
      }

      const removeBtn = target.closest("[data-came-remove='1']");
      if (removeBtn) {
        const row = removeBtn.closest("[data-came-row='1']");
        const tbody = removeBtn.closest("tbody");
        if (row && tbody) {
          row.remove();
          renumberCategory(tbody);
          renderState(buildState());
          scheduleAutosave();
        }
      }
    });

    panel.addEventListener("input", (event) => {
      const target = event.target;
      if (!(target instanceof HTMLInputElement)) return;
      if (target.matches("[data-came-input='1']")) {
        renderState(buildState());
        scheduleAutosave();
      }
    });

    panel.addEventListener("change", (event) => {
      const target = event.target;
      if (!(target instanceof HTMLInputElement)) return;
      if (target.matches("[data-came-input='1']")) {
        renderState(buildState());
        scheduleAutosave();
      }
    });

    if (saveBtn) {
      saveBtn.addEventListener("click", async () => {
        if (saving) return;
        saving = true;
        setButtonLoading(saveBtn, true, "Guardando…");
        try {
          const state = buildState();
          renderState(state);
          const { ok, json } = await postAction("save_came_batch", { acciones: JSON.stringify(state.payload) });
          if (!ok || !json || json.ok !== true) {
            showToast("No se pudo guardar", String((json && json.error) || "No se pudo guardar la matriz."));
            return;
          }
          if (state.totalActions > 0) {
            showToast("Guardado", "La Matriz CAME se guardó correctamente.");
            return;
          }
          showToast("Guardado", "La Matriz CAME se guardó sin acciones registradas.");
        } catch (e) {
          showToast("No se pudo guardar", "No se pudo guardar la matriz.");
        } finally {
          saving = false;
          setButtonLoading(saveBtn, false);
        }
      });
    }

    allBodies().forEach((tbody) => renumberCategory(tbody));
    renderState(buildState());
  };
})();
