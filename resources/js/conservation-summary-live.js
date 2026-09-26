import { refreshConservationFieldDefinitions } from './conservation-field-definitions.js';

function field(container, name) { return container.querySelector(`[name$="[${name}]"]`); }

function selectText(node, includeEmpty = false) {
    if (!(node instanceof HTMLSelectElement)) return '';
    const option = node.selectedOptions?.[0];
    if (!option) return '';
    if (!includeEmpty && option.value === '') return '';
    return option.textContent.trim();
}

function inputText(node) { return typeof node?.value === 'string' ? node.value.trim() : ''; }

function stateWeight(text) {
    const match = text.match(/^(\d+(?:[.,]\d+)?)/);
    return match ? Number.parseFloat(match[1].replace(',', '.')) : 0;
}

function factorWeight(criticality) { return { Crítica: 1.5, Alta: 1.25, Media: 1, Baja: 0.75 }[criticality] ?? 1; }

function groupWeight(id) {
    const weights = { estructura: 2, instalaciones: 1.5, envolvente: 1.25, acabados: 1, espacios_funcionales: 1, condiciones_ambientales: 0.75 };
    return weights[id] ?? 1;
}

function stateFromScore(score) {
    if (!score) return '';
    const rounded = Math.max(1, Math.min(5, Math.round(score * 2) / 2));
    return Number.isInteger(rounded) ? String(rounded) : rounded.toFixed(1);
}

function weightedScore(items) {
    const total = items.reduce((acc, item) => {
        const state = stateWeight(item.state);
        if (!state) return acc;
        acc.sum += state * item.weight;
        acc.weight += item.weight;
        return acc;
    }, { sum: 0, weight: 0 });
    return total.weight ? total.sum / total.weight : 0;
}

function findingFor(item) { return item.finding || item.notes || 'sin hallazgo específico registrado'; }

function interpretationFor(item) {
    const parts = [];
    if (item.finding) parts.push('el hallazgo seleccionado sustenta la calificación adoptada');
    if (item.functionality) parts.push(`funcionalidad ${item.functionality}`);
    if (item.intervention) parts.push(`intervención aparente ${item.intervention}`);
    if (!parts.length) return 'sin interpretación automática adicional por ausencia de hallazgo, funcionalidad o intervención seleccionada';
    return parts.join('; ');
}

function groupConclusion(group) {
    const score = group.score ? group.score.toFixed(2).replace('.', ',') : 'pendiente';
    const parts = [`se asigna ${group.state || 'estado pendiente'} al grupo a partir de ${group.items.length} factor(es), con índice técnico ponderado ${score}.`];
    group.items.forEach(item => {
        let line = `${item.label}: Hallazgo observado: ${findingFor(item)}. `;
        line += `Interpretación técnica: ${interpretationFor(item)}. `;
        line += `Estado asignado: ${item.state || 'pendiente'}.`;
        if (item.notes) line += ` Observación del analista: ${item.notes.replace(/[.]+$/, '')}.`;
        if (item.evidence) line += ` Evidencia: ${item.evidence.replace(/[.]+$/, '')}.`;
        parts.push(line);
    });
    return parts.join(' ');
}

function shortConclusion(group) {
    const worst = group.items.reduce((selected, item) => stateWeight(item.state) > stateWeight(selected?.state || '') ? item : selected, null);
    if (!worst) return 'Sin conclusión automática.';
    const finding = findingFor(worst);
    return finding !== 'sin hallazgo específico registrado'
        ? `${worst.label}: ${finding}`
        : 'Componentes diligenciados sin hallazgo negativo específico.';
}

export function conservationSummaryText(panel) {
    const groups = new Map();
    const allGroups = new Map();

    panel.querySelectorAll('[data-conservation-subcomponent]').forEach(component => {
        const groupKey = component.dataset.conservationGroup || 'Conservación';
        const groupId = component.dataset.conservationGroupId || groupKey;
        const groupNumber = component.dataset.conservationGroupNumber || '';
        allGroups.set(groupKey, { id: groupId, number: groupNumber, label: groupKey });
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

        const label = component.dataset.conservationLabel || 'Elemento';
        if (!groups.has(groupKey)) groups.set(groupKey, { id: groupId, number: groupNumber, label: groupKey, state: '', score: 0, items: [] });
        const item = {
            label, material, finding, functionality, intervention, state: adopted, notes, evidence,
            weight: factorWeight(component.dataset.conservationCriticality || ''),
        };
        groups.get(groupKey).items.push(item);
    });

    if (![...groups.values()].some(group => group.items.length)) return '';
    groups.forEach(group => {
        group.score = weightedScore(group.items);
        group.state = stateFromScore(group.score);
    });

    const adoptedGlobal = selectText(panel.querySelector('[name$="[conservation_summary][global_adopted]"]'));
    const justification = inputText(panel.querySelector('[name$="[conservation_summary][change_justification]"]'));
    const globalRaw = weightedScore([...groups.values()].map(group => ({ state: String(group.score || ''), weight: groupWeight(group.id) })));
    const global = adoptedGlobal || stateFromScore(globalRaw) || 'pendiente de adopción';
    const globalScore = globalRaw ? globalRaw.toFixed(2).replace('.', ',') : 'pendiente';

    const lines = ['Cuadro resumen del estado de conservación', 'Grupo | Estado | Principal conclusión'];
    allGroups.forEach((info, key) => {
        const group = groups.get(key);
        lines.push(group
            ? `${info.label} (${group.items.length} factor(es)) | ${group.state || 'pendiente'} | ${shortConclusion(group)}`
            : `${info.label} | No diligenciado | Sin conclusión automática por falta de selección.`);
    });
    lines.push(`Estado global | ${global} | Conclusión derivada de los grupos diligenciados.`);
    lines.push('');
    lines.push('Detalle de calificación por factor');
    groups.forEach(group => {
        group.items.forEach(item => {
            lines.push(`${group.label} - ${item.label} | ${item.state || 'pendiente'} | ${findingFor(item)} | peso ${String(item.weight).replace('.', ',')}; aporta estado x peso al índice del grupo.`);
        });
    });
    lines.push('');
    lines.push(`Método de cálculo: el factor tiene un peso interno para calcular su grupo. El global usa un segundo peso por grupo, dando mayor peso a estructura e instalaciones por su incidencia en vida útil, seguridad y reparabilidad. Índice global: ${globalScore}. Es una regla interna de apoyo basada en la escala IGAC; el analista puede adoptar otro estado si lo justifica.`);
    lines.push('');
    lines.push('Lectura técnica por grupo');
    allGroups.forEach((info, key) => {
        const group = groups.get(key);
        const heading = `${info.number} ${info.label}`.trim();
        lines.push(`${heading}: ${group ? groupConclusion(group) : 'no se registraron selecciones para este grupo; por tanto, no se emite calificación automática.'}`);
    });
    lines.push('');
    let conclusion = `Conclusión global: del análisis integral de los componentes constructivos diligenciados se concluye que la unidad presenta un Estado de Conservación ${global}.`;
    conclusion += ' La decisión se fundamenta en los hallazgos observados, la interpretación técnica registrada para cada grupo y la escala de estados de conservación utilizada por el IGAC.';
    conclusion += ' Las condiciones críticas solo se afirman cuando el analista las haya seleccionado o descrito expresamente en observaciones o evidencia.';
    if (justification) conclusion += ` Justificación del analista: ${justification.replace(/[.]+$/, '')}.`;
    lines.push(conclusion);
    lines.push('Base técnica: Resolución IGAC 941 de 2026, IN-GCT-PC03-01 V2 e IN-GCT-PC01-06 V1. La calificación documenta condición observable y criterio valuatorio; no sustituye diagnóstico especializado.');
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

function panelFor(target) { return target?.closest?.('[data-conservation-panel]') ?? null; }

function refreshAll(root = document) {
    root.querySelectorAll?.('[data-conservation-panel]').forEach(panel => {
        refreshConservationFieldDefinitions(panel); updatePanel(panel);
    });
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
        if (panel) { refreshConservationFieldDefinitions(panel); updatePanel(panel); }
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
