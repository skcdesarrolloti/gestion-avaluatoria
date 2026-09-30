import { flushModuleAutosaves } from './module-autosave.js';
import { hasComparableData } from './comparable-review.js';

const keyOf = input => input.name.match(/\[([^\]]+)\]$/)[1];
export const emptyComparable = row => ({ id: crypto.randomUUID().replaceAll('-', ''),
    active: 'si', status: 'por_verificar', ph_regime: 'por_verificar',
    operation: row.operation || '', property_type: row.property_type || '', consulted_at: new Date().toISOString().slice(0, 10) });

export function comparableRemoval(flush = flushModuleAutosaves) {
    let form, entries, removed = [];
    const values = entry => Object.fromEntries(entry.controls.map(input => [keyOf(input), input.value]));
    const write = (entry, row) => entry.controls.forEach(input => { input.value = row[keyOf(input)] ?? ''; });
    return {
        removalSelection: [], removalPending: [], removalBusy: false, removalMessage: '', undoCount: 0,
        initRemoval(element, rows) { form = element; entries = rows; },
        selectRemovalPage() { this.removalSelection = entries.filter(e => e.used && !e.tr.hidden).map(e => String(e.index)); },
        requestRemoval(all = false) {
            if (this.photoBusy || this.removalBusy) return;
            this.removalPending = entries.filter(e => hasComparableData(values(e)) && (all || this.removalSelection.includes(String(e.index)))).map(e => e.index);
        },
        async confirmRemoval() {
            if (this.removalBusy || this.photoBusy || !this.removalPending.length) return;
            this.removalBusy = true;
            this.removalMessage = 'Confirmando los cambios pendientes…';
            try {
                if (!await flush()) throw new Error('No se pudo guardar la matriz. No se retiraron muestras. Revisa el estado de guardado.');
                const targets = this.removalPending.map(i => entries[i]);
                removed.push(...targets.map(values));
                targets.forEach(entry => { write(entry, emptyComparable(values(entry))); entry.opened = false; });
                this.undoCount = removed.length; this.removalSelection = []; this.removalPending = [];
                this.photoOpen = false; this.photos = []; this.page = 1;
                this.phFilter = 'all'; this.filter = 'all'; this.search = '';
                await this.saveRemoval(`${targets.length} muestras retiradas`);
            } catch (error) { this.removalMessage = error.message; }
            finally { this.removalBusy = false; }
        },
        async undoRemoval() {
            if (this.removalBusy || this.photoBusy || !removed.length) return;
            const blanks = entries.filter(entry => !hasComparableData(values(entry)));
            if (blanks.length < removed.length) { this.removalMessage = 'No hay espacio para restaurar todas las muestras retiradas. No se cambió la matriz.'; return; }
            this.removalBusy = true;
            try {
                removed.forEach((row, i) => write(blanks[i], row));
                removed = []; this.undoCount = 0;
                await this.saveRemoval('Muestras restauradas');
            } catch (error) { this.removalMessage = error.message; }
            finally { this.removalBusy = false; }
        },
        async saveRemoval(message) {
            form.dispatchEvent(new Event('input', { bubbles: true }));
            form.dispatchEvent(new CustomEvent('comparable-matrix-changed', { bubbles: true }));
            this.removalMessage = `${message}. Guardando…`;
            this.removalMessage = await flush() ? `${message}. Guardado confirmado.` : `${message} en pantalla, pero SIN confirmar guardado. Usa Guardar ahora; no cierres esta página.`;
        },
    };
}
