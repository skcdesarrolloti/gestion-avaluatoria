function field(container, name) {
    return container.querySelector(`[name$="[${name}]"]`);
}

function selectText(node, includeEmpty = false) {
    if (!(node instanceof HTMLSelectElement)) return '';
    const option = node.selectedOptions?.[0];
    if (!option) return '';
    if (!includeEmpty && option.value === '') return '';
    return option.textContent.trim();
}

function inputText(node) {
    return typeof node?.value === 'string' ? node.value.trim() : '';
}

function sentence(label, value) {
    return value ? `${label}: ${value}.` : '';
}

function stateWeight(text) {
    const match = text.match(/^(\d+(?:[.,]\d+)?)/);
    return match ? Number.parseFloat(match[1].replace(',', '.')) : 0;
}

function worstState(states) {
    return states.reduce((worst, state) => stateWeight(state) > stateWeight(worst) ? state : worst, '');
}

export function conservationSummaryText(panel) {
    const rows = [];
    const states = [];
    const groups = new Map();

    panel.querySelectorAll('[data-conservation-subcomponent]').forEach(component => {
        const applicability = selectText(field(component, 'applicability'), true) || 'Aplica';
        if (applicability.toLowerCase() === 'no aplica') return;
        const material = selectText(field(component, 'material'));
        const finding = selectText(field(component, 'finding'), true) || 'Sin hallazgo registrado';
        const functionality = selectText(field(component, 'functionality'));
        const intervention = selectText(field(component, 'intervention'));
        const adopted = selectText(field(component, 'state_adopted'));
        const notes = inputText(field(component, 'notes'));
        const evidence = inputText(field(component, 'evidence'));
        const hasData = [material, functionality, intervention, adopted, notes, evidence].some(Boolean)
            || applicability.toLowerCase() !== 'aplica';
        if (!hasData) return;

        const group = component.dataset.conservationGroup || 'Conservación';
        const label = component.dataset.conservationLabel || 'Elemento';
        const parts = [
            sentence('Aplicabilidad', applicability),
            sentence('Tipo/material', material),
            sentence('Hallazgo observable', finding),
            sentence('Funcionalidad', functionality),
            sentence('Intervención aparente', intervention),
            sentence('Estado adoptado', adopted),
            sentence('Observación técnica', notes),
            sentence('Evidencia', evidence),
        ].filter(Boolean).join(' ');
        rows.push(`${group}: ${label}. ${parts}`);
        if (adopted) states.push(adopted);
        groups.set(group, adopted || groups.get(group) || '');
    });

    if (!rows.length) return '';

    const adoptedGlobal = selectText(panel.querySelector('[name$="[conservation_summary][global_adopted]"]'));
    const justification = inputText(panel.querySelector('[name$="[conservation_summary][change_justification]"]'));
    const global = adoptedGlobal || worstState(states) || 'pendiente de adopción';
    const groupText = [...groups.entries()]
        .map(([group, state]) => `${group}: ${state || 'pendiente'}`)
        .join('; ');

    const lines = [
        `Texto automático de conservación: se diligenciaron ${rows.length} componente(s) con información verificable para el numeral 7.`,
        ...rows,
        `Resumen por grupo: ${groupText}.`,
        `Resultado global adoptado/propuesto: ${global}.`,
    ];
    if (justification) lines.push(`Justificación del analista: ${justification.replace(/[.]+$/, '')}.`);
    lines.push('Base técnica: Resolución IGAC 941 de 2026, IN-GCT-PC03-01 V2 e IN-GCT-PC01-06 V1. Este texto documenta condición observable y criterio del analista; no reemplaza diagnóstico especializado.');
    return lines.join('\n');
}

function setStatus(panel, text) {
    const status = panel.querySelector('[data-conservation-summary-status]');
    if (status) status.textContent = text;
}

function updatePanel(panel, force = false, dispatch = true) {
    const textarea = panel.querySelector('[data-conservation-approved]');
    if (!(textarea instanceof HTMLTextAreaElement)) return;
    const generated = conservationSummaryText(panel);
    if (!generated) return;
    const last = textarea.dataset.conservationLastGenerated || '';
    const mayUpdate = force || textarea.dataset.conservationAuto === '1' || textarea.value.trim() === '' || textarea.value.trim() === last.trim();
    if (!mayUpdate) {
        setStatus(panel, 'Hay una edición manual: se conserva. Pulsa regenerar si quieres reemplazarla.');
        return;
    }
    if (textarea.value !== generated) {
        textarea.value = generated;
        textarea.dataset.conservationAuto = '1';
        textarea.dataset.conservationLastGenerated = generated;
        setStatus(panel, 'Texto automático actualizado y pendiente de autoguardado.');
        if (dispatch) textarea.dispatchEvent(new Event('input', { bubbles: true }));
    } else {
        setStatus(panel, 'Texto automático listo y protegido por autoguardado.');
    }
}

function panelFor(target) {
    return target?.closest?.('[data-conservation-panel]') ?? null;
}

function refreshAll(root = document) {
    root.querySelectorAll?.('[data-conservation-panel]').forEach(panel => updatePanel(panel));
}

export function installConservationSummaryLive(root = document) {
    root.addEventListener('input', event => {
        if (event.target?.matches?.('[data-conservation-approved]')) {
            event.target.dataset.conservationAuto = event.isTrusted ? '0' : event.target.dataset.conservationAuto;
            return;
        }
        const panel = panelFor(event.target);
        if (panel) updatePanel(panel);
    });
    root.addEventListener('change', event => {
        const panel = panelFor(event.target);
        if (panel) updatePanel(panel);
    });
    root.addEventListener('click', event => {
        const button = event.target.closest?.('[data-conservation-regenerate]');
        if (!button) return;
        event.preventDefault();
        const panel = panelFor(button);
        if (panel) updatePanel(panel, true);
    });
    refreshAll(root);
    if (typeof MutationObserver !== 'undefined' && root.documentElement) {
        new MutationObserver(records => {
            records.forEach(record => record.addedNodes.forEach(node => {
                if (node instanceof Element) refreshAll(node);
            }));
        }).observe(root.documentElement, { childList: true, subtree: true });
    }
}
