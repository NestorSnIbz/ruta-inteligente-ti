(() => {
  const RELATION_ORDER = ["FO", "FA", "DO", "DA"];
  const RELATION_LABELS = {
    FO: "Estrategia Ofensiva",
    FA: "Estrategia Defensiva",
    DO: "Estrategia de Reorientación",
    DA: "Estrategia de Supervivencia",
  };
  const RELATION_DESCRIPTIONS = {
    FO: "Deberá adoptar estrategias de crecimiento.",
    FA: "La empresa está preparada para enfrentarse a las amenazas.",
    DO: "La empresa no puede aprovechar las oportunidades porque carece de preparación adecuada.",
    DA: "Se enfrenta a amenazas externas sin las fortalezas necesarias para luchar con la competencia.",
  };
  const SCORE_LABELS = {
    0: "En total desacuerdo",
    1: "No está de acuerdo",
    2: "Está de acuerdo",
    3: "Bastante de acuerdo",
    4: "Totalmente de acuerdo",
  };

  function clampInt(value, min, max) {
    const n = Number(value);
    if (Number.isNaN(n)) return min;
    return Math.max(min, Math.min(max, Math.trunc(n)));
  }

  function setButtonLoading(btn, loading, label) {
    if (!btn) return;
    if (loading) {
      btn.disabled = true;
      btn.className =
        "inline-flex items-center justify-center rounded-xl bg-brand-600/60 px-4 py-2 text-sm font-semibold text-white shadow-sm";
      btn.textContent = label || "Guardando…";
      return;
    }
    btn.disabled = false;
    btn.className =
      "inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/25";
    btn.textContent = label || "Guardar Matriz";
  }

  function shorten(text, maxLen) {
    const clean = String(text || "").replace(/\s+/g, " ").trim();
    if (clean.length <= maxLen) return clean;
    return `${clean.slice(0, Math.max(0, maxLen - 3))}...`;
  }

  function relationReasoning(relation, rowCode, rowDesc, colCode, colDesc, score) {
    const label = SCORE_LABELS[score] || String(score);
    const row = shorten(rowDesc, 95);
    const col = shorten(colDesc, 95);
    if (relation === "FO") {
      if (score === 4) return `${rowCode}-${colCode}: existe una alineación estratégica muy alta; ${row} permite capitalizar de forma directa ${col}, por lo que la empresa debería priorizar una acción ofensiva sobre esta relación.`;
      if (score === 3) return `${rowCode}-${colCode}: la fortaleza ${row} favorece claramente el aprovechamiento de ${col}; la relación es sólida y merece seguimiento prioritario.`;
      if (score === 2) return `${rowCode}-${colCode}: hay una relación aprovechable entre ${row} y ${col}, aunque todavía requiere decisiones adicionales para transformarse en una ventaja ofensiva consistente.`;
      if (score === 1) return `${rowCode}-${colCode}: la fortaleza ${row} apenas contribuye a capturar ${col}; la conexión estratégica es débil y necesita refuerzo antes de ser priorizada.`;
      return `${rowCode}-${colCode}: no se observa una conexión estratégica clara entre ${row} y ${col}; esta fortaleza no ofrece evidencia suficiente para aprovechar esa oportunidad.`;
    }
    if (relation === "FA") {
      if (score === 4) return `${rowCode}-${colCode}: ${row} protege de manera muy efectiva frente a ${col}; la empresa dispone de una defensa robusta para neutralizar esta amenaza.`;
      if (score === 3) return `${rowCode}-${colCode}: la fortaleza ${row} ayuda de forma importante a contener ${col}; conviene reforzar esta capacidad como mecanismo defensivo.`;
      if (score === 2) return `${rowCode}-${colCode}: ${row} aporta una defensa parcial ante ${col}; la empresa cuenta con base de respuesta, pero necesita complementarla.`;
      if (score === 1) return `${rowCode}-${colCode}: la fortaleza ${row} apenas reduce el impacto de ${col}; la protección actual es limitada y podría ser insuficiente.`;
      return `${rowCode}-${colCode}: ${row} no contribuye de forma apreciable a neutralizar ${col}; la amenaza permanece prácticamente expuesta.`;
    }
    if (relation === "DO") {
      if (score === 4) return `${rowCode}-${colCode}: ${col} ofrece una palanca muy fuerte para superar ${row}; existe una oportunidad clara de reorientación estratégica.`;
      if (score === 3) return `${rowCode}-${colCode}: la oportunidad ${col} puede ayudar significativamente a corregir ${row}; vale la pena convertirla en una iniciativa prioritaria.`;
      if (score === 2) return `${rowCode}-${colCode}: ${col} podría apoyar parcialmente la superación de ${row}, aunque la empresa necesitará capacidades adicionales para capturar ese efecto.`;
      if (score === 1) return `${rowCode}-${colCode}: la oportunidad ${col} tiene poca capacidad para compensar ${row}; la mejora potencial es baja.`;
      return `${rowCode}-${colCode}: no se aprecia que ${col} ayude a resolver ${row}; la debilidad sigue sin una vía externa clara de corrección.`;
    }
    if (score === 4) return `${rowCode}-${colCode}: ${row} intensifica de forma crítica el impacto de ${col}; esta combinación exige medidas inmediatas de supervivencia y control de riesgo.`;
    if (score === 3) return `${rowCode}-${colCode}: la debilidad ${row} aumenta claramente la gravedad de ${col}; la empresa debería intervenir pronto para reducir vulnerabilidad.`;
    if (score === 2) return `${rowCode}-${colCode}: existe una exposición moderada; ${row} puede agravar ${col}, pero aún hay margen para contener el riesgo con acciones oportunas.`;
    if (score === 1) return `${rowCode}-${colCode}: ${row} apenas incrementa el efecto de ${col}; el riesgo existe, aunque todavía es relativamente acotado.`;
    return `${rowCode}-${colCode}: no se identifican evidencias suficientes de que ${row} agrave ${col}; el impacto cruzado es mínimo o no relevante.`;
  }

  function buildExecutiveConclusion(predominant, summary, topPairs) {
    if (!predominant) {
      return "Completa todas las relaciones de la matriz para generar automáticamente la conclusión ejecutiva.";
    }

    const relation = String(predominant.relation || "");
    const label = RELATION_LABELS[relation] || relation;
    const total = clampInt(predominant.total || 0, 0, 99999);

    const others = RELATION_ORDER
      .filter((rel) => rel !== relation)
      .map((rel) => `${rel} (${clampInt(summary[rel]?.total || 0, 0, 99999)})`);

    const pairText =
      topPairs.length > 0
        ? topPairs
            .map(
              (pair) =>
                `${pair.rowCode}-${pair.colCode} (${shorten(pair.rowDesc, 58)} / ${shorten(pair.colDesc, 58)})`
            )
            .join("; ")
        : "no se registran combinaciones destacadas con ventaja individual clara";

    const actions = {
      FO: "priorizar iniciativas de crecimiento, acelerar el desarrollo comercial, escalar capacidades diferenciales y convertir las oportunidades mejor valoradas en proyectos concretos con responsables, plazos y presupuesto",
      FA: "blindar procesos críticos, reforzar la propuesta de valor, preparar respuestas ante cambios externos y utilizar las fortalezas actuales como mecanismo activo de defensa competitiva",
      DO: "cerrar brechas internas mediante inversión, capacitación, alianzas o rediseño de procesos para transformar las oportunidades externas en mejoras efectivas y sostenibles",
      DA: "contener riesgos, corregir vulnerabilidades prioritarias, racionalizar recursos y establecer planes de respuesta que disminuyan la exposición frente a amenazas relevantes",
    };

    const risks = {
      FO: "sobreestimar la capacidad de ejecución, crecer sin estructura suficiente o dispersar recursos en demasiadas iniciativas al mismo tiempo",
      FA: "reaccionar tarde a cambios externos, enfocarse solo en defensa y relegar opciones de expansión estratégicamente viables",
      DO: "subestimar el tiempo necesario para cerrar debilidades o perder oportunidades por lentitud de adaptación interna",
      DA: "caer en una gestión excesivamente reactiva, sostener vulnerabilidades críticas durante demasiado tiempo o debilitar el posicionamiento por falta de foco",
    };

    return [
      `La matriz FODA cruzada muestra que la estrategia predominante recomendada para la empresa es la ${label}, porque la relación ${relation} obtuvo la mayor puntuación acumulada con ${total} puntos. Frente a las demás alternativas ${others.join(", ")}, este resultado indica que la prioridad estratégica debe concentrarse en el patrón de interacción que hoy ofrece mayor coherencia entre los factores internos y externos registrados en el diagnóstico.`,
      `Los factores que más influyeron en el resultado fueron las combinaciones con mejor valoración dentro de la relación ${relation}: ${pairText}. Estas asociaciones reflejan dónde existe mayor capacidad de respuesta estratégica y cuáles son los vínculos con más peso para orientar decisiones, inversiones y secuencia de ejecución dentro del plan empresarial.`,
      `En términos de acción, la empresa debería ${actions[relation] || "traducir el diagnóstico en una hoja de ruta priorizada"}. La recomendación es convertir estas relaciones mejor puntuadas en iniciativas operativas con seguimiento periódico, métricas de avance y responsables claros, para asegurar que la estrategia predominante no quede solo como un resultado analítico sino como una línea efectiva de implementación.`,
      `Como riesgos potenciales, conviene considerar ${risks[relation] || "la falta de alineación entre diagnóstico y ejecución"}. Por eso, además de ejecutar la estrategia principal, resulta importante monitorear las demás relaciones de la matriz, ya que cambios en el entorno o en la capacidad interna podrían alterar la conveniencia de la estrategia actualmente recomendada.`,
    ].join("\n\n");
  }

  async function postAction(action, payload) {
    const fd = new FormData();
    fd.set("action", String(action));
    fd.set("t", String(window.projectToken || ""));
    for (const [key, value] of Object.entries(payload || {})) {
      fd.set(key, value);
    }
    const res = await fetch("detalle-proyecto.php", {
      method: "POST",
      body: fd,
      headers: { Accept: "application/json" },
    });
    const json = await res.json().catch(() => null);
    return { ok: res.ok, json };
  }

  window.RI = window.RI || {};
  window.RI.initFodaCruzadaPanel = function initFodaCruzadaPanel() {
    const panel = document.getElementById("panel-estrategias");
    if (!panel || panel.dataset.riInit === "1") return;
    panel.dataset.riInit = "1";

    const ready = panel.dataset.swotReady === "1";
    const selects = Array.from(panel.querySelectorAll("[data-swot-select='1']"));
    const saveBtn = panel.querySelector("#swot-save");
    const answeredEl = panel.querySelector("#swot-answered");

    const toast = panel.querySelector("#swot-toast");
    const toastCard = panel.querySelector("#swot-toast-card");
    const toastTitle = panel.querySelector("#swot-toast-title");
    const toastMsg = panel.querySelector("#swot-toast-msg");
    const toastClose = panel.querySelector("#swot-toast-close");
    const factorHoverButtons = Array.from(panel.querySelectorAll("[data-swot-factor-hover='1']"));
    const factorTooltip = document.getElementById("swot-factor-tooltip");
    const factorTooltipCode = document.getElementById("swot-factor-tooltip-code");
    const factorTooltipDesc = document.getElementById("swot-factor-tooltip-desc");
    const factorTooltipSource = document.getElementById("swot-factor-tooltip-source");

    let validationActive = false;
    let autosaveTimer = null;
    let autosaveInflight = false;
    let autosaveQueued = false;
    let savingBatch = false;
    let lastAutosaveErrorAt = 0;
    let toastTimer = null;

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
      toastTimer = setTimeout(() => closeToast(), 3600);
    }

    if (toastClose) toastClose.addEventListener("click", () => closeToast());

    let tooltipHideTimer = null;
    let tooltipAnchor = null;

    function hideFactorTooltip() {
      if (tooltipHideTimer) {
        clearTimeout(tooltipHideTimer);
        tooltipHideTimer = null;
      }
      tooltipAnchor = null;
      if (factorTooltip) factorTooltip.classList.add("hidden");
    }

    function positionFactorTooltip(anchorEl) {
      if (!factorTooltip || !anchorEl) return;
      const rect = anchorEl.getBoundingClientRect();
      const tooltipRect = factorTooltip.getBoundingClientRect();
      const gap = 10;
      const vw = window.innerWidth || document.documentElement.clientWidth || 0;
      const vh = window.innerHeight || document.documentElement.clientHeight || 0;
      const minPad = 8;

      let left = rect.left + rect.width / 2 - tooltipRect.width / 2;
      left = Math.max(minPad, Math.min(left, Math.max(minPad, vw - tooltipRect.width - minPad)));

      const fitsAbove = rect.top >= tooltipRect.height + gap + minPad;
      let top = fitsAbove ? (rect.top - tooltipRect.height - gap) : (rect.bottom + gap);
      if (top + tooltipRect.height + minPad > vh) {
        top = Math.max(minPad, vh - tooltipRect.height - minPad);
      }

      factorTooltip.style.left = `${Math.round(left)}px`;
      factorTooltip.style.top = `${Math.round(top)}px`;
    }

    function showFactorTooltip(anchorEl) {
      if (!factorTooltip || !anchorEl) return;
      if (tooltipHideTimer) {
        clearTimeout(tooltipHideTimer);
        tooltipHideTimer = null;
      }

      tooltipAnchor = anchorEl;
      const code = String(anchorEl.dataset.swotFactorCode || "").trim();
      const desc = String(anchorEl.dataset.swotFactorDesc || "").trim();
      const source = String(anchorEl.dataset.swotFactorSource || "").trim();

      if (factorTooltipCode) factorTooltipCode.textContent = code ? code : "";
      if (factorTooltipDesc) factorTooltipDesc.textContent = desc ? desc : "Sin detalle.";
      if (factorTooltipSource) factorTooltipSource.textContent = source ? source : "";

      factorTooltip.classList.remove("hidden");
      requestAnimationFrame(() => positionFactorTooltip(anchorEl));
    }

    for (const btn of factorHoverButtons) {
      btn.addEventListener("mouseenter", () => showFactorTooltip(btn));
      btn.addEventListener("mouseleave", () => {
        tooltipHideTimer = setTimeout(() => hideFactorTooltip(), 120);
      });
      btn.addEventListener("focus", () => showFactorTooltip(btn));
      btn.addEventListener("blur", () => hideFactorTooltip());
    }

    if (factorTooltip) {
      factorTooltip.addEventListener("mouseenter", () => {
        if (tooltipHideTimer) {
          clearTimeout(tooltipHideTimer);
          tooltipHideTimer = null;
        }
      });
      factorTooltip.addEventListener("mouseleave", () => {
        tooltipHideTimer = setTimeout(() => hideFactorTooltip(), 120);
      });
    }

    window.addEventListener("scroll", () => hideFactorTooltip(), true);
    window.addEventListener("resize", () => {
      if (tooltipAnchor) requestAnimationFrame(() => positionFactorTooltip(tooltipAnchor));
    });

    function buildState() {
      const state = {
        totalCells: selects.length,
        answered: 0,
        missing: 0,
        payload: [],
        matrices: {},
        summary: {},
        predominant: null,
      };

      for (const relation of RELATION_ORDER) {
        state.matrices[relation] = {
          relation,
          total: 0,
          answered: 0,
          cellCount: 0,
          rowTotals: {},
          colTotals: {},
          topPairs: [],
          justifications: [],
        };
      }

      for (const select of selects) {
        const relation = String(select.dataset.relation || "");
        if (!state.matrices[relation]) continue;
        const rowKey = String(select.dataset.rowKey || "");
        const colKey = String(select.dataset.colKey || "");
        const rowCode = String(select.dataset.rowCode || "");
        const colCode = String(select.dataset.colCode || "");
        const rowDesc = String(select.dataset.rowDesc || "");
        const colDesc = String(select.dataset.colDesc || "");
        const valueRaw = String(select.value || "").trim();
        const ref = select.parentElement ? select.parentElement.querySelector("[data-swot-ref]") : null;

        state.matrices[relation].cellCount += 1;
        if (!Object.prototype.hasOwnProperty.call(state.matrices[relation].rowTotals, rowKey)) {
          state.matrices[relation].rowTotals[rowKey] = 0;
        }
        if (!Object.prototype.hasOwnProperty.call(state.matrices[relation].colTotals, colKey)) {
          state.matrices[relation].colTotals[colKey] = 0;
        }

        if (valueRaw === "") {
          select.className =
            validationActive
              ? "swot-select h-10 w-full rounded-lg border border-amber-300 bg-amber-50 px-2 text-center text-sm font-semibold text-amber-800 outline-none focus:border-amber-300 focus:ring-2 focus:ring-amber-100"
              : "swot-select h-10 w-full rounded-lg border border-neutral-200 bg-white px-2 text-center text-sm font-semibold text-neutral-800 outline-none focus:border-neutral-300 focus:ring-2 focus:ring-brand-100";
          if (ref) {
            if (validationActive) ref.classList.remove("hidden");
            else ref.classList.add("hidden");
          }
          continue;
        }

        const value = clampInt(valueRaw, 0, 4);
        state.answered += 1;
        state.matrices[relation].answered += 1;
        state.matrices[relation].total += value;
        state.matrices[relation].rowTotals[rowKey] += value;
        state.matrices[relation].colTotals[colKey] += value;
        state.payload.push({
          relation,
          row_key: rowKey,
          col_key: colKey,
          value,
        });
        state.matrices[relation].justifications.push({
          pair: `${rowCode}-${colCode}`,
          score: value,
          text: relationReasoning(relation, rowCode, rowDesc, colCode, colDesc, value),
          rowCode,
          colCode,
          rowDesc,
          colDesc,
        });
        state.matrices[relation].topPairs.push({
          value,
          rowCode,
          colCode,
          rowDesc,
          colDesc,
        });

        select.className =
          "swot-select h-10 w-full rounded-lg border border-neutral-200 bg-white px-2 text-center text-sm font-semibold text-brand-800 outline-none focus:border-neutral-300 focus:ring-2 focus:ring-brand-100";
        if (ref) ref.classList.add("hidden");
      }

      state.missing = Math.max(0, state.totalCells - state.answered);

      for (const relation of RELATION_ORDER) {
        const matrix = state.matrices[relation];
        matrix.topPairs.sort((a, b) => {
          if (a.value !== b.value) return b.value - a.value;
          return `${a.rowCode}${a.colCode}`.localeCompare(`${b.rowCode}${b.colCode}`);
        });
        matrix.topPairs = matrix.topPairs.slice(0, 3);
        state.summary[relation] = {
          relation,
          label: RELATION_LABELS[relation],
          total: matrix.total,
          description: RELATION_DESCRIPTIONS[relation],
        };
      }

      const complete = ready && state.totalCells > 0 && state.missing === 0;
      if (complete) {
        let best = null;
        for (const relation of RELATION_ORDER) {
          const item = state.summary[relation];
          if (!best || item.total > best.total) {
            best = item;
          }
        }
        state.predominant = best;
      }

      return state;
    }

    function renderState(state) {
      if (answeredEl) answeredEl.textContent = `${state.answered}/${state.totalCells}`;

      for (const relation of RELATION_ORDER) {
        const matrix = state.matrices[relation];
        const answeredNode = panel.querySelector(`[data-matrix-answered='${relation}']`);
        const totalNode = panel.querySelector(`[data-matrix-total='${relation}']`);
        if (answeredNode) answeredNode.textContent = `${matrix.answered}/${matrix.cellCount}`;
        if (totalNode) totalNode.textContent = String(matrix.total);

        for (const [rowKey, total] of Object.entries(matrix.rowTotals)) {
          const node = panel.querySelector(`[data-matrix-row-total='${relation}|${CSS.escape(rowKey)}']`);
          if (node) node.textContent = String(total);
        }
        for (const [colKey, total] of Object.entries(matrix.colTotals)) {
          const node = panel.querySelector(`[data-matrix-col-total='${relation}|${CSS.escape(colKey)}']`);
          if (node) node.textContent = String(total);
        }

        const body = panel.querySelector(`[data-justification-body='${relation}']`);
        if (body) {
          if (matrix.justifications.length === 0) {
            body.innerHTML = `<tr><td colspan="3" class="px-4 py-4 text-sm text-neutral-500">Aún no hay relaciones valoradas.</td></tr>`;
          } else {
            body.innerHTML = matrix.justifications
              .map(
                (item) => `
                  <tr>
                    <td class="px-4 py-3 font-semibold text-neutral-900">${item.pair}</td>
                    <td class="px-4 py-3"><span class="inline-flex min-w-[2.5rem] items-center justify-center rounded-lg border border-brand-200 bg-brand-50 px-2 py-1 font-semibold text-brand-800">${item.score}</span></td>
                    <td class="px-4 py-3 text-neutral-800 leading-relaxed">${item.text}</td>
                  </tr>
                `
              )
              .join("");
          }
        }

        const summaryRow = panel.querySelector(`[data-summary-row='${relation}']`);
        const summaryTotal = panel.querySelector(`[data-summary-total='${relation}']`);
        if (summaryTotal) summaryTotal.textContent = String(state.summary[relation].total);
        if (summaryRow) {
          const active = state.predominant && state.predominant.relation === relation;
          summaryRow.className = active ? "bg-brand-50/70" : "";
        }
      }

      const labelEl = panel.querySelector("#swot-predominant-label");
      const relationEl = panel.querySelector("#swot-predominant-relation");
      const totalEl = panel.querySelector("#swot-predominant-total");
      const conclusionEl = panel.querySelector("#swot-conclusion");

      if (state.predominant) {
        const relation = String(state.predominant.relation || "");
        const conclusion = buildExecutiveConclusion(
          state.predominant,
          state.summary,
          state.matrices[relation] ? state.matrices[relation].topPairs : []
        );
        if (labelEl) labelEl.textContent = String(state.predominant.label || "—");
        if (relationEl) relationEl.textContent = relation;
        if (totalEl) totalEl.textContent = String(state.predominant.total || 0);
        if (conclusionEl) conclusionEl.textContent = conclusion;
      } else {
        if (labelEl) labelEl.textContent = "—";
        if (relationEl) relationEl.textContent = "";
        if (totalEl) totalEl.textContent = "0";
        if (conclusionEl) {
          conclusionEl.textContent = ready
            ? "Completa todas las relaciones de la matriz para generar automáticamente la conclusión ejecutiva."
            : "Registra al menos una fortaleza, una debilidad, una oportunidad y una amenaza en los módulos previos para habilitar la matriz cruzada.";
        }
      }
    }

    function scheduleAutosave() {
      if (!ready || !window.projectToken) return;
      if (autosaveTimer) clearTimeout(autosaveTimer);
      autosaveTimer = setTimeout(() => flushAutosave(), 650);
    }

    async function flushAutosave() {
      if (!ready || !window.projectToken || savingBatch) return;
      if (autosaveInflight) {
        autosaveQueued = true;
        return;
      }

      const state = buildState();
      autosaveInflight = true;
      try {
        const { ok, json } = await postAction("save_foda_cruzada_autosave_batch", {
          answers: JSON.stringify(state.payload),
        });
        if (!ok || !json || json.ok !== true) {
          const now = Date.now();
          if (now - lastAutosaveErrorAt > 1200) {
            lastAutosaveErrorAt = now;
            showToast("error", "No se pudo guardar", String((json && json.error) || "Error al guardar automáticamente."));
          }
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
      validationActive = true;
      const state = buildState();
      renderState(state);

      if (!ready) {
        showToast(
          "error",
          "No se puede guardar",
          "Debes registrar al menos una fortaleza, una debilidad, una oportunidad y una amenaza en los módulos previos."
        );
        return;
      }
      if (state.totalCells <= 0) {
        showToast("error", "No se puede guardar", "No hay relaciones disponibles para evaluar en la matriz.");
        return;
      }
      if (state.missing > 0) {
        showToast("error", "Faltan respuestas", "Debes valorar todas las relaciones de la matriz antes de guardar.");
        return;
      }

      savingBatch = true;
      setButtonLoading(saveBtn, true);
      try {
        const { ok, json } = await postAction("save_foda_cruzada_batch", {
          answers: JSON.stringify(state.payload),
        });
        if (!ok || !json || json.ok !== true) {
          showToast("error", "No se pudo guardar", String((json && json.error) || "No se pudo guardar la matriz cruzada."));
          return;
        }
        showToast("success", "Guardado", "La matriz FODA cruzada se guardó correctamente.");
      } catch (e) {
        showToast("error", "No se pudo guardar", "No se pudo guardar la matriz cruzada.");
      } finally {
        savingBatch = false;
        setButtonLoading(saveBtn, false);
      }
    }

    if (saveBtn) saveBtn.addEventListener("click", () => saveBatch());

    for (const select of selects) {
      select.addEventListener("change", () => {
        const state = buildState();
        renderState(state);
        scheduleAutosave();
      });
    }

    renderState(buildState());
  };
})();
