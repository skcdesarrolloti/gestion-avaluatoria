import { emptyComparable } from './comparable-removal.js';

// Clone a pristine server-rendered row, never the user's current values/listeners.
export function rowFactory(form) {
    const template = form.querySelector('tbody tr').cloneNode(true);
    template.querySelectorAll('.comparable-field-label,.comparable-row-summary').forEach(node => node.remove());
    const defaults = Object.fromEntries([...template.querySelectorAll('[name]')].map(input =>
        [input.name.match(/\[([^\]]+)\]$/)[1], input.value]));
    return index => {
        const tr = template.cloneNode(true);
        const values = emptyComparable(defaults);
        for (const input of tr.querySelectorAll('[name]')) {
            const key = input.name.match(/\[([^\]]+)\]$/)[1];
            input.name = `comparables[${index}][${key}]`;
            input.value = values[key] ?? '';
        }
        const checkbox = tr.querySelector('input[type=checkbox]');
        checkbox.value = String(index);
        checkbox.checked = false;
        checkbox.setAttribute(':disabled', `removalBusy || photoBusy || !usedIndexes.includes('${index}')`);
        checkbox.setAttribute('aria-label', `Seleccionar muestra ${index + 1} para eliminar`);
        for (const node of checkbox.parentElement.childNodes) if (node.nodeType === 3) node.textContent = String(index + 1);
        tr.querySelector('button').setAttribute('@click', `openPhotos(${index})`);
        return tr;
    };
}
