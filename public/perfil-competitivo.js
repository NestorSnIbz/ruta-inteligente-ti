(() => {
  const CONCLUSIONS = [
    "Entorno altamente hostil. La empresa enfrenta una fuerte presión competitiva y condiciones desfavorables en el sector.",
    "Entorno moderadamente hostil. Existen factores que limitan la competitividad y requieren estrategias de mejora.",
    "Entorno favorable. La empresa cuenta con condiciones competitivas aceptables para desarrollar sus actividades.",
    "Entorno muy favorable. La posición competitiva de la empresa es sólida y las condiciones del sector son altamente positivas.",
  ];

  function conclusionForTotal(total) {
    const t = Number(total || 0);
    if (t < 30) return { code: 1, text: CONCLUSIONS[0] };
    if (t >= 30 && t < 45) return { code: 2, text: CONCLUSIONS[1] };
    if (t >= 45 && t < 60) return { code: 3, text: CONCLUSIONS[2] };
    return { code: 4, text: CONCLUSIONS[3] };
  }

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
    btn.textContent = label || "Guardar Evaluación";
  }

  window.RI = window.RI || {};
  window.RI.initPerfilCompetitivoPanel = function initPerfilCompetitivoPanel() {
    const panel = document.getElementById("panel-perfil_competitivo");
    if (!panel || panel.dataset.riInit === "1") return;
    panel.dataset.riInit = "1";

    const form = panel.querySelector("#pc-form");
    const totalEl = panel.querySelector("#pc-total");
    const validEl = panel.querySelector("#pc-valid");
    const conclusionEl = panel.querySelector("#pc-conclusion");
    const saveBtn = panel.querySelector("#pc-save");

    const toast = panel.querySelector("#pc-toast");
    const toastCard = panel.querySelector("#pc-toast-card");
    const toastTitle = panel.querySelector("#pc-toast-title");
    const toastMsg = panel.querySelector("#pc-toast-msg");
    const toastClose = panel.querySelector("#pc-toast-close");

    const rows = Array.from(panel.querySelectorAll("[data-pc-row]"));
    const answers = {};
    let validationActive = false;
    let toastTimer = null;
    let savingBatch = false;
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

    if (toastClose) {
      toastClose.addEventListener("click", () => closeToast());
    }

    function updateRowStyles(row) {
      const cells = Array.from(row.querySelectorAll(".pc-cell"));
      for (const cell of cells) {
        const input = cell.querySelector("input[type='radio']");
        const label = cell.querySelector(".pc-cell-label");
        const checked = input && input.checked;
        cell.className = checked
          ? "pc-cell flex h-12 w-full cursor-pointer items-center justify-center select-none bg-brand-50"
          : "pc-cell flex h-12 w-full cursor-pointer items-center justify-center select-none hover:bg-neutral-50";
        if (label) {
          label.className = checked
            ? "pc-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-brand-600 bg-brand-600 px-3 text-sm font-semibold text-white shadow-sm transition"
            : "pc-cell-label inline-flex h-9 w-full max-w-[4.25rem] items-center justify-center rounded-xl border border-neutral-300 bg-white px-3 text-sm font-semibold text-neutral-700 transition hover:border-brand-300";
          label.textContent = checked ? "X" : "";
        }
      }
    }

    function applyCalc(calc) {
      const total = Number(calc && calc.total !== undefined ? calc.total : 0);
      const valid = Number(calc && calc.valid !== undefined ? calc.valid : 0);
      const count = Number(calc && calc.count !== undefined ? calc.count : rows.length);
      const missing = Number(calc && calc.missing !== undefined ? calc.missing : Math.max(0, count - valid));

      if (totalEl) totalEl.textContent = String(total);
      if (validEl) validEl.textContent = `${valid}/${count}`;

      if (missing > 0) {
        if (conclusionEl) {
          conclusionEl.textContent = validationActive ? "#¡REF!" : "—";
        }
        return;
      }

      const cText = calc && typeof calc.conclusion_text === "string" && calc.conclusion_text.trim() !== ""
        ? calc.conclusion_text
        : conclusionForTotal(total).text;
      if (conclusionEl) conclusionEl.textContent = String(cText);
    }

    function recalcFromDom() {
      let total = 0;
      let valid = 0;
      let hasInvalid = false;

      for (const row of rows) {
        const checked = row.querySelectorAll("input[type='radio']:checked");
        const ref = row.querySelector("[data-pc-ref]");
        updateRowStyles(row);

        if (checked.length === 1) {
          valid += 1;
          const v = Number(checked[0].value || 0);
          total += v;
          if (ref) ref.classList.add("hidden");
          row.className = "pc-row";
          answers[Number(row.dataset.pcRow || 0)] = v;
        } else {
          hasInvalid = true;
          if (validationActive && ref) ref.classList.remove("hidden");
          if (ref && !validationActive) ref.classList.add("hidden");
          row.className = validationActive ? "pc-row bg-red-50/40" : "pc-row";
          delete answers[Number(row.dataset.pcRow || 0)];
        }
      }

      const calc = {
        total,
        valid,
        count: rows.length,
        missing: hasInvalid ? Math.max(0, rows.length - valid) : 0,
        conclusion_text: hasInvalid ? null : conclusionForTotal(total).text,
        conclusion_code: hasInvalid ? null : conclusionForTotal(total).code,
      };
      applyCalc(calc);
      return calc;
    }

    async function postAction(action, payload) {
      const fd = new FormData();
      fd.set("action", String(action));
      fd.set("t", String(window.projectToken || ""));
      for (const [k, v] of Object.entries(payload || {})) {
        fd.set(k, v);
      }
      const res = await fetch("detalle-proyecto.php", { method: "POST", body: fd, headers: { Accept: "application/json" } });
      const json = await res.json().catch(() => null);
      return { ok: res.ok, json };
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
        const { ok, json } = await postAction("save_perfil_competitivo_autosave_batch", { answers: JSON.stringify(payload) });
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
        if (json.calc) applyCalc(json.calc);
      } finally {
        autosaveInflight = false;
        if (autosaveQueued) {
          autosaveQueued = false;
          flushAutosave();
        }
      }
    }

    function scheduleAutosave(idFactor, valor) {
      if (!window.projectToken) return;
      autosaveChanges[String(idFactor)] = Number(valor);
      if (autosaveTimer) clearTimeout(autosaveTimer);
      autosaveTimer = setTimeout(() => flushAutosave(), 500);
    }

    async function saveBatch() {
      if (savingBatch) return;
      savingBatch = true;
      setButtonLoading(saveBtn, true);
      try {
        validationActive = true;
        recalcFromDom();
        if (Object.keys(answers).length !== rows.length) {
          showToast("error", "Faltan respuestas", "Completa todas las filas antes de guardar.");
          return;
        }

        const { ok, json } = await postAction("save_perfil_competitivo_batch", { answers: JSON.stringify(answers) });
        if (!ok || !json || json.ok !== true) {
          showToast("error", "No se pudo guardar", String((json && json.error) || "Error al guardar la evaluación."));
          return;
        }

        if (json.calc) applyCalc(json.calc);
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
        const row = target.closest("[data-pc-row]");
        const idFactor = row ? Number(row.dataset.pcRow || 0) : 0;
        const valor = Number(target.value || 0);
        if (idFactor > 0 && valor >= 0 && valor <= 4) {
          scheduleAutosave(idFactor, valor);
        }
      });
    }

    if (saveBtn) {
      saveBtn.addEventListener("click", () => saveBatch());
    }

    recalcFromDom();
  };
})();
