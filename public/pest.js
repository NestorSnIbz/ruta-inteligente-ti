(() => {
  const CATEGORY_ORDER = ["SOCIALES", "MEDIOAMBIENTALES", "POLITICOS", "ECONOMICOS", "TECNOLOGICOS"];
  const CATEGORY_COLORS = {
    SOCIALES: "#A7D88D",
    MEDIOAMBIENTALES: "#5E9F59",
    POLITICOS: "#B98A5B",
    ECONOMICOS: "#D6680B",
    TECNOLOGICOS: "#7EC9F5",
  };

  const POSITIVE_TEXT =
    "La influencia de este factor en el entorno de la empresa es alta y debe considerarse una variable estratégica para la toma de decisiones.";
  const NEGATIVE_TEXT =
    "La influencia actual de este factor es moderada o reducida respecto al resto de variables del entorno.";

  function clampInt(n, min, max) {
    const x = Number(n);
    if (Number.isNaN(x)) return min;
    return Math.max(min, Math.min(max, Math.trunc(x)));
  }

  function conclusionForPct(pct) {
    const p = clampInt(pct, 0, 100);
    if (p >= 70) return { positive: true, text: POSITIVE_TEXT };
    return { positive: false, text: NEGATIVE_TEXT };
  }

  function badgeClass(positive) {
    if (positive === true) return "bg-emerald-50 text-emerald-800 border border-emerald-200";
    if (positive === false) return "bg-amber-50 text-amber-800 border border-amber-200";
    return "bg-neutral-100 text-neutral-700";
  }

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
    btn.textContent = label || "Guardar Evaluación";
  }

  window.RI = window.RI || {};
  window.RI.initPestPanel = function initPestPanel() {
    const panel = document.getElementById("panel-pest");
    if (!panel || panel.dataset.riInit === "1") return;
    panel.dataset.riInit = "1";

    const form = panel.querySelector("#pest-form");
    const saveBtn = panel.querySelector("#pest-save");
    const validEl = panel.querySelector("#pest-valid");

    const toast = panel.querySelector("#pest-toast");
    const toastCard = panel.querySelector("#pest-toast-card");
    const toastTitle = panel.querySelector("#pest-toast-title");
    const toastMsg = panel.querySelector("#pest-toast-msg");
    const toastClose = panel.querySelector("#pest-toast-close");

    const rows = Array.from(panel.querySelectorAll("[data-pest-row]"));
    const answers = {};
    let validationActive = false;
    let savingBatch = false;
    let toastTimer = null;

    let autosaveTimer = null;
    let autosaveInflight = false;
    let autosaveQueued = false;
    const autosaveChanges = {};
    let lastAutosaveErrorAt = 0;

    function closeToast() {
      if (!toast) return;
      toast.classList.add("hidden");
      if (toastTimer) {
        clearTimeout(toastTimer);
        toastTimer = null;
      }
    }

    function showToast(type, title, message) {
      if (!toast || !toastCard || !toastTitle || !toastMsg) return;
      toastTitle.textContent = String(title || "");
      toastMsg.textContent = String(message || "");
      toastCard.className =
        type === "success"
          ? "pointer-events-auto rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-lg"
          : "pointer-events-auto rounded-2xl border border-red-200 bg-red-50 p-4 shadow-lg";
      toast.classList.remove("hidden");
      if (toastTimer) clearTimeout(toastTimer);
      toastTimer = setTimeout(() => closeToast(), 3200);
    }

    if (toastClose) toastClose.addEventListener("click", () => closeToast());

    function categoryColor(cat) {
      return CATEGORY_COLORS[String(cat || "").toUpperCase()] || "#0054DC";
    }

    function updateRowStyles(row) {
      const cat = String(row.dataset.pestCat || "");
      const color = categoryColor(cat);
      const cells = Array.from(row.querySelectorAll(".pest-cell"));
      for (const cell of cells) {
        const input = cell.querySelector("input[type='radio']");
        const label = cell.querySelector(".pest-cell-label");
        const checked = input && input.checked;
        cell.className = checked
          ? "pest-cell flex h-12 w-full cursor-pointer items-center justify-center select-none"
          : "pest-cell flex h-12 w-full cursor-pointer items-center justify-center select-none hover:bg-neutral-50";
        if (label) {
          label.className = checked
            ? "pest-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border px-3 text-sm font-semibold text-white shadow-sm transition"
            : "pest-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-neutral-300 bg-white px-3 text-sm font-semibold text-neutral-700 transition hover:border-brand-300";
          if (checked) {
            label.style.backgroundColor = color;
            label.style.borderColor = color;
            label.style.color = "#FFFFFF";
            cell.style.backgroundColor = "transparent";
          } else {
            label.style.backgroundColor = "";
            label.style.borderColor = "";
            label.style.color = "";
            cell.style.backgroundColor = "";
          }
          label.textContent = checked ? "X" : "";
        }
      }
    }

    function applyPct(cat, pct, missing) {
      const p = clampInt(pct, 0, 100);
      const bar = panel.querySelector(`[data-pest-bar='${cat}']`);
      const barLabel = panel.querySelector(`[data-pest-bar-label='${cat}']`);
      const pctEl = panel.querySelector(`[data-pest-pct='${cat}']`);
      const badgeEl = panel.querySelector(`[data-pest-badge='${cat}']`);
      const conclusionEl = panel.querySelector(`[data-pest-conclusion='${cat}']`);

      if (bar) bar.style.height = `${p}%`;
      if (barLabel) barLabel.textContent = `${p}%`;
      if (pctEl) pctEl.textContent = `${p}%`;

      if (missing > 0) {
        if (badgeEl) badgeEl.className = `inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold ${badgeClass(null)}`;
        if (conclusionEl) {
          conclusionEl.textContent = validationActive
            ? `Incompleto. Faltan ${missing} fila${missing === 1 ? "" : "s"} por responder para guardar la evaluación.`
            : "Incompleto. Completa todas las filas para generar la conclusión.";
          conclusionEl.className = "mt-3 text-xs font-semibold text-amber-700 leading-relaxed";
        }
        return;
      }

      const c = conclusionForPct(p);
      if (badgeEl) badgeEl.className = `inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold ${badgeClass(c.positive)}`;
      if (conclusionEl) {
        conclusionEl.textContent = c.text;
        conclusionEl.className = "mt-3 text-xs text-neutral-700 leading-relaxed";
      }
    }

    function recalcFromDom() {
      const score = { SOCIALES: 0, MEDIOAMBIENTALES: 0, POLITICOS: 0, ECONOMICOS: 0, TECNOLOGICOS: 0 };
      const max = { SOCIALES: 0, MEDIOAMBIENTALES: 0, POLITICOS: 0, ECONOMICOS: 0, TECNOLOGICOS: 0 };
      let valid = 0;

      for (const row of rows) {
        const checked = row.querySelectorAll("input[type='radio']:checked");
        const ref = row.querySelector("[data-pest-ref]");
        const cat = String(row.dataset.pestCat || "");
        updateRowStyles(row);
        if (cat && Object.prototype.hasOwnProperty.call(max, cat)) {
          max[cat] += 4;
        }

        if (checked.length === 1) {
          valid += 1;
          const v = clampInt(checked[0].value, 0, 4);
          const qid = Number(row.dataset.pestRow || 0);
          answers[qid] = v;
          if (cat && Object.prototype.hasOwnProperty.call(score, cat)) {
            score[cat] += v;
          }
          if (ref) ref.classList.add("hidden");
          row.className = "pest-row";
        } else {
          const qid = Number(row.dataset.pestRow || 0);
          delete answers[qid];
          if (validationActive && ref) ref.classList.remove("hidden");
          if (ref && !validationActive) ref.classList.add("hidden");
          row.className = validationActive ? "pest-row bg-red-50/40" : "pest-row";
        }
      }

      const count = rows.length;
      const missing = Math.max(0, count - valid);
      if (validEl) validEl.textContent = `${valid}/${count}`;

      for (const cat of CATEGORY_ORDER) {
        const m = Number(max[cat] || 0);
        const s = Number(score[cat] || 0);
        const pct = m > 0 ? Math.floor((s / m) * 100) : 0;
        applyPct(cat, pct, missing);
      }

      return { valid, count, missing };
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

    function scheduleAutosave(qid, value) {
      if (!window.projectToken) return;
      autosaveChanges[String(qid)] = Number(value);
      if (autosaveTimer) clearTimeout(autosaveTimer);
      autosaveTimer = setTimeout(() => flushAutosave(), 500);
    }

    async function flushAutosave() {
      if (!window.projectToken) return;
      if (savingBatch) return;
      if (autosaveInflight) {
        autosaveQueued = true;
        return;
      }
      const keys = Object.keys(autosaveChanges);
      if (keys.length === 0) return;

      const payload = {};
      for (const k of keys) {
        payload[k] = autosaveChanges[k];
        delete autosaveChanges[k];
      }

      autosaveInflight = true;
      try {
        const { ok, json } = await postAction("save_pest_autosave_batch", { answers: JSON.stringify(payload) });
        if (!ok || !json || json.ok !== true) {
          const now = Date.now();
          if (now - lastAutosaveErrorAt > 900) {
            lastAutosaveErrorAt = now;
            showToast("error", "No se pudo guardar", String((json && json.error) || "Error al guardar automáticamente."));
          }
          for (const [k, v] of Object.entries(payload)) {
            autosaveChanges[k] = v;
          }
          return;
        }
      } finally {
        autosaveInflight = false;
        if (autosaveQueued) {
          autosaveQueued = false;
          flushAutosave();
        }
      }
    }

    async function saveBatch() {
      if (savingBatch) return;
      savingBatch = true;
      setButtonLoading(saveBtn, true);
      try {
        validationActive = true;
        recalcFromDom();

        if (Object.keys(answers).length !== rows.length) {
          showToast("error", "Faltan respuestas", "Debes responder todas las preguntas antes de guardar.");
          return;
        }

        const { ok, json } = await postAction("save_pest_batch", { answers: JSON.stringify(answers) });
        if (!ok || !json || json.ok !== true) {
          showToast("error", "No se pudo guardar", String((json && json.error) || "Error al guardar la evaluación."));
          return;
        }

        showToast("success", "Guardado", "Evaluación guardada correctamente.");
      } catch (e) {
        showToast("error", "No se pudo guardar", "Error al guardar la evaluación.");
      } finally {
        savingBatch = false;
        setButtonLoading(saveBtn, false);
      }
    }

    if (form) {
      form.addEventListener("change", (e) => {
        const target = e && e.target ? e.target : null;
        if (!target || target.tagName !== "INPUT" || target.type !== "radio") return;
        recalcFromDom();
        const row = target.closest("[data-pest-row]");
        const qid = row ? Number(row.dataset.pestRow || 0) : 0;
        const v = clampInt(target.value, 0, 4);
        if (qid > 0) scheduleAutosave(qid, v);
      });
    }

    if (saveBtn) saveBtn.addEventListener("click", () => saveBatch());

    if (window.RI && typeof window.RI.initFodaOa === "function") {
      window.RI.initFodaOa({
        panel,
        action: "save_foda_pest",
        saveButtonId: "pest-foda-save",
        addOportunidadId: "pest-foda-add-oportunidad",
        addAmenazaId: "pest-foda-add-amenaza",
        oportunidadesBodyId: "pest-foda-oportunidades-body",
        amenazasBodyId: "pest-foda-amenazas-body",
        showToast: (type, title, message) => showToast(type, title, message),
      });
    }

    recalcFromDom();
  };
})();
