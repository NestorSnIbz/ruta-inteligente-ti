(() => {
  function setButtonLoading(btn, loading, label) {
    if (!btn) return;
    if (loading) {
      btn.disabled = true;
      btn.className =
        "inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm opacity-90";
      btn.textContent = label || "Guardando…";
      return;
    }
    btn.disabled = false;
    btn.className =
      "inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25";
    btn.textContent = label || "Guardar FODA";
  }

  function renumberRows(tbody) {
    const rows = Array.from(tbody.querySelectorAll("tr"));
    rows.forEach((row, idx) => {
      const n = row.querySelector("td");
      if (n) n.textContent = String(idx + 1);
    });
  }

  function ensureAtLeastOneRow(tbody, kind) {
    const rows = Array.from(tbody.querySelectorAll("tr"));
    if (rows.length > 0) {
      renumberRows(tbody);
      return;
    }
    tbody.appendChild(createRow(kind, ""));
    renumberRows(tbody);
  }

  function createRow(kind, value) {
    const tr = document.createElement("tr");
    tr.setAttribute("data-foda-row", kind);
    tr.innerHTML = `
      <td class="px-4 py-3 text-center text-xs font-semibold text-neutral-600">1</td>
      <td class="px-4 py-2">
        <input type="text" value="${String(value || "").replace(/"/g, "&quot;")}" class="foda-input h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-800 shadow-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200" />
      </td>
      <td class="px-4 py-2 text-right">
        <button type="button" class="foda-remove inline-flex h-9 items-center justify-center rounded-xl border border-neutral-200 bg-white px-3 text-xs font-semibold text-neutral-800 hover:bg-neutral-50">
          Quitar
        </button>
      </td>
    `;
    return tr;
  }

  function collectValues(tbody) {
    const out = [];
    const rows = Array.from(tbody.querySelectorAll("tr"));
    for (const row of rows) {
      const input = row.querySelector("input");
      const v = input ? String(input.value || "").trim() : "";
      if (v !== "") out.push(v);
    }
    return out;
  }

  async function postAction(action, payload) {
    const fd = new FormData();
    fd.set("action", String(action));
    fd.set("t", String(window.projectToken || ""));
    for (const [k, v] of Object.entries(payload || {})) fd.set(k, v);
    const res = await fetch("detalle-proyecto.php", { method: "POST", body: fd, headers: { Accept: "application/json" } });
    const json = await res.json().catch(() => null);
    return { ok: res.ok, json };
  }

  window.RI = window.RI || {};
  window.RI.initFodaOa = function initFodaOa(options) {
    const panel = options && options.panel ? options.panel : null;
    if (!panel) return;
    const action = String(options.action || "");
    if (!action) return;

    const saveBtn = panel.querySelector(`#${options.saveButtonId || ""}`);
    const addOp = panel.querySelector(`#${options.addOportunidadId || ""}`);
    const addAm = panel.querySelector(`#${options.addAmenazaId || ""}`);
    const opBody = panel.querySelector(`#${options.oportunidadesBodyId || ""}`);
    const amBody = panel.querySelector(`#${options.amenazasBodyId || ""}`);
    const showToast = typeof options.showToast === "function" ? options.showToast : null;

    if (!opBody || !amBody) return;
    ensureAtLeastOneRow(opBody, "OPORTUNIDAD");
    ensureAtLeastOneRow(amBody, "AMENAZA");

    function toast(type, title, msg) {
      if (showToast) {
        showToast(type, title, msg);
      }
    }

    function bindRemove(tbody, kind) {
      if (tbody.dataset.fodaBound === "1") return;
      tbody.dataset.fodaBound = "1";
      tbody.addEventListener("click", (e) => {
        const btn = e.target && e.target.closest ? e.target.closest(".foda-remove") : null;
        if (!btn) return;
        const row = btn.closest("tr");
        const rows = Array.from(tbody.querySelectorAll("tr"));
        if (rows.length <= 1) {
          const input = row ? row.querySelector("input") : null;
          if (input) input.value = "";
          ensureAtLeastOneRow(tbody, kind);
          return;
        }
        if (row) row.remove();
        ensureAtLeastOneRow(tbody, kind);
      });
    }

    bindRemove(opBody, "OPORTUNIDAD");
    bindRemove(amBody, "AMENAZA");

    if (addOp) {
      addOp.addEventListener("click", () => {
        const tr = createRow("OPORTUNIDAD", "");
        opBody.appendChild(tr);
        ensureAtLeastOneRow(opBody, "OPORTUNIDAD");
        tr.querySelector("input")?.focus();
      });
    }

    if (addAm) {
      addAm.addEventListener("click", () => {
        const tr = createRow("AMENAZA", "");
        amBody.appendChild(tr);
        ensureAtLeastOneRow(amBody, "AMENAZA");
        tr.querySelector("input")?.focus();
      });
    }

    if (saveBtn && saveBtn.dataset.riBound !== "1") {
      saveBtn.dataset.riBound = "1";
      saveBtn.addEventListener("click", async () => {
        if (!window.projectToken) {
          toast("error", "No se pudo guardar", "Proyecto inválido.");
          return;
        }
        setButtonLoading(saveBtn, true);
        try {
          const oportunidades = collectValues(opBody);
          const amenazas = collectValues(amBody);
          const payload = JSON.stringify({ oportunidades, amenazas });
          const { ok, json } = await postAction(action, { payload });
          if (!ok || !json || json.ok !== true) {
            toast("error", "No se pudo guardar", String((json && json.error) || "No se pudo guardar el apartado."));
            return;
          }
          toast("success", "Guardado", "FODA guardado correctamente.");
        } catch (e) {
          toast("error", "No se pudo guardar", "No se pudo guardar el apartado.");
        } finally {
          setButtonLoading(saveBtn, false);
        }
      });
    }
  };
})();

