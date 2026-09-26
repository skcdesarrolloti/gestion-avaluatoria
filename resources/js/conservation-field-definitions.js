function field(container, name) {
    return container.querySelector(`[name$="[${name}]"]`);
}

function parsedDataset(panel, key, fallback) {
    try {
        const parsed = JSON.parse(panel?.dataset?.[key] || JSON.stringify(fallback));
        return parsed && typeof parsed === 'object' ? parsed : fallback;
    } catch {
        return fallback;
    }
}

function stateDefinitionText(states, value) {
    if (!value) return 'Selecciona un estado para ver el criterio técnico que sustentará la valoración.';
    const state = states.find(item => String(item.value || '') === String(value));
    if (!state) return 'No hay definición técnica cargada para este estado.';
    return `${state.value} - ${state.label}: ${state.criterion} ${state.intervention} ${state.use}`.trim();
}

function interventionDefinitionText(interventions, value) {
    const row = interventions.find(item => String(item.level ?? '') === String(value || ''));
    if (!row) return 'Selecciona intervención aparente para ver su alcance técnico.';
    return `${row.description} Ejemplos: ${row.examples} Relación orientativa: ${row.state_relation}`;
}

export function refreshConservationFieldDefinitions(panel) {
    const states = parsedDataset(panel, 'conservationStateDefinitions', []);
    const functionalities = parsedDataset(panel, 'conservationFunctionalityDefinitions', {});
    const interventions = parsedDataset(panel, 'conservationInterventionDefinitions', []);
    panel.querySelectorAll('[data-conservation-subcomponent]').forEach(component => {
        const stateTarget = component.querySelector('[data-conservation-state-definition]');
        const functionalityTarget = component.querySelector('[data-conservation-functionality-definition]');
        const interventionTarget = component.querySelector('[data-conservation-intervention-definition]');
        if (stateTarget) stateTarget.textContent = stateDefinitionText(states, field(component, 'state_adopted')?.value || '');
        if (functionalityTarget) {
            functionalityTarget.textContent = functionalities?.[field(component, 'functionality')?.value || '']?.scope
                || 'Selecciona funcionalidad para ver su alcance técnico.';
        }
        if (interventionTarget) {
            interventionTarget.textContent = interventionDefinitionText(interventions, field(component, 'intervention')?.value);
        }
    });
}
