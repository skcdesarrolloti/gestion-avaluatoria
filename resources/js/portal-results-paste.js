import { fillRows, parseComparableText } from './comparable-bulk-import.js';
import { candidateMatches, candidateSuggestions, matrixRows } from './comparable-candidate-review.js';
const normalize = value => String(value ?? '').toLowerCase().trim();

export function portalResultsPaste({ label, readRows, validUrl, allowTsv = false }) {
    let panel, form;
    return {
        results: [], selected: [], message: '', busy: false,
        get suggestedCount() { return this.results.filter(item => item.suggested).length; },
        get registeredCount() { return this.results.filter(item => item.tone === 'registered').length; },
        get reviewCount() { return this.results.filter(item => item.tone === 'review').length; },
        init() { panel = this.$el; form = document.getElementById('tabla-madre-83'); },
        paste(event) {
            event.preventDefault();
            this.results = []; this.selected = [];
            const html = event.clipboardData?.getData('text/html') || '';
            const text = event.clipboardData?.getData('text/plain') || '';
            if (html.length + text.length > 2000000) { this.message = 'Copia solo una página de resultados (máximo 2 MB).'; return; }
            let rows = html ? readRows(html, panel.dataset.city) : [];
            if (allowTsv && !rows.length && text.includes('\t')) rows = parseComparableText(text).filter(row =>
                validUrl(row.source_url) && row.price_amount && row.area_m2 && normalize(row.operation) === 'venta');
            if (!rows.length) {
                this.message = `No se reconocieron tarjetas. Abre los resultados de ${label}, espera que carguen y usa Ctrl+A y Ctrl+C; vuelve aquí y pega con Ctrl+V. Pegar solo la dirección no trae los inmuebles.`;
                return;
            }
            this.results = rows.slice(0, 60).map((row, i) => ({ row, number: i + 1, matches: [] }));
            this.selected = []; this.refresh();
            this.message = `${this.results.length} avisos preparados; todavía no se han agregado. Comprueba barrio, precio y área. Solo se lee la página copiada.`;
        },
        refresh() {
            const matches = candidateMatches(this.results, matrixRows(form));
            this.results.forEach((item, i) => { item.matches = matches[i]; });
            const suggestions = candidateSuggestions(this.results);
            this.results.forEach((item, i) => Object.assign(item, suggestions[i]));
            this.selected = this.selected.filter(url => this.results.some(item => item.row.source_url === url && item.tone !== 'registered'));
        },
        selectAll() { this.refresh(); this.selected = this.results.filter(item => item.tone !== 'registered').map(item => item.row.source_url); },
        addSuggested() {
            if (this.busy) return;
            this.refresh();
            this.selected = this.results.filter(item => item.suggested).map(item => item.row.source_url);
            if (!this.selected.length) { this.message = 'No hay sugeridos nuevos: los avisos ya están registrados o tienen coincidencias pendientes de revisión.'; return; }
            this.add();
        },
        add() {
            if (this.busy || !this.selected.length) return;
            this.busy = true;
            try {
                this.refresh();
                const rows = this.results.filter(item => this.selected.includes(item.row.source_url)).map(item => item.row);
                const result = fillRows(form, rows, panel.dataset.query || '', undefined, { deferDuplicateReview: true });
                this.selected = []; this.refresh();
                this.message = `${result.count} avisos incorporados como por verificar. ${result.duplicates} enlaces ya registrados omitidos. ${result.overflow} sin cargar por límite de 60. Revisa el estado de guardado en la matriz.`;
            } finally { this.busy = false; }
        },
    };
}
