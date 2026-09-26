import test from 'node:test';
import assert from 'node:assert/strict';
import { conservationSummaryText } from '../resources/js/conservation-summary-live.js';

class FakeSelect {
    constructor(value, text) {
        this.selectedOptions = [{ value, textContent: text }];
    }
}

class FakeInput {
    constructor(value) {
        this.value = value;
    }
}

globalThis.HTMLSelectElement = FakeSelect;

function component(dataset, fields) {
    return {
        dataset,
        querySelector(selector) {
            const match = selector.match(/\[name\$="\[(.+?)\]"\]/);
            return match ? fields[match[1]] ?? null : null;
        },
    };
}

test('conservation summary is generated from filled component data', () => {
    const panel = {
        querySelectorAll(selector) {
            if (selector !== '[data-conservation-subcomponent]') return [];
            return [component({
                conservationGroup: 'Estructura',
                conservationLabel: 'Sistema portante',
            }, {
                applicability: new FakeSelect('aplica', 'Aplica'),
                material: new FakeSelect('concreto', 'Concreto reforzado'),
                finding: new FakeSelect('', 'Sin hallazgo registrado'),
                functionality: new FakeSelect('normal', 'Normal'),
                intervention: new FakeSelect('0', '0 - Ninguna'),
                state_adopted: new FakeSelect('1', '1 - Optimo'),
                notes: new FakeInput('Sin lesiones visibles en inspeccion.'),
                evidence: new FakeInput('Foto 3.3-7'),
            })];
        },
        querySelector(selector) {
            if (selector.includes('[global_adopted]')) return new FakeSelect('', 'Usar propuesta del sistema');
            if (selector.includes('[change_justification]')) return new FakeInput('');
            return null;
        },
    };

    const text = conservationSummaryText(panel);
    assert.match(text, /Sistema portante/);
    assert.match(text, /Concreto reforzado/);
    assert.match(text, /Estado adoptado: 1 - Optimo/);
    assert.match(text, /Resultado global adoptado\/propuesto: 1 - Optimo/);
});
