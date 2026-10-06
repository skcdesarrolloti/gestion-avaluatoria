import { parseComparableText } from './comparable-bulk-import.js';
import { candidateMatches, candidateSuggestions, matrixRows } from './comparable-candidate-review.js';
import { previewFacts } from './comparable-preview-facts.js';
import { captureSelectedDetails } from './capture-selected-details.js';
const normalize = value => String(value ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim();

export function portalResultsPaste({ label, readRows, validUrl, allowTsv = false, emptyMessage = '', readText = false }) {
    let panel, form;
    return {
        previewFacts, results: [], selected: [], message: '', pastedText: '', busy: false,
        get suggestedCount() { return this.results.filter(item => item.suggested).length; },
        get registeredCount() { return this.results.filter(item => item.tone === 'registered').length; },
        get reviewCount() { return this.results.filter(item => item.tone === 'review').length; },
        init() { panel = this.$el; form = document.getElementById('tabla-madre-83'); },
        async paste(event) {
            event.preventDefault();
            if (this.busy) return;
            this.results = []; this.selected = [];
            const html = event.clipboardData?.getData('text/html') || '';
            const text = event.clipboardData?.getData('text/plain') || '';
            this.pastedText = text.slice(0, 4000) + (text.length > 4000 ? '\n… Vista abreviada del texto recibido.' : '');
            if (!text && !html) { this.message = 'No llegó texto de la página. Si copiaste una foto o captura, vuelve al listado de inmuebles y copia la página con Ctrl+A y Ctrl+C.'; return; }
            if (html.length + text.length > 2000000) { this.message = 'Copia solo una página de resultados (máximo 2 MB).'; return; }
            let rows = html || readText ? readRows(html, panel.dataset.city, text) : [];
            if (allowTsv && !rows.length && text.includes('\t')) rows = parseComparableText(text).filter(row =>
                validUrl(row.source_url) && row.price_amount && row.area_m2 && normalize(row.operation) === 'venta');
            if (!rows.length) {
                this.message = emptyMessage || `No se reconocieron tarjetas. Sí llegó contenido, pero no se pudieron leer sus avisos. Copia directamente la página de resultados de ${label} con Ctrl+A y Ctrl+C y pega con Ctrl+V (sin Mayús). No copies una ficha individual ni pases el texto por otra aplicación.`;
                return;
            }
            this.results = rows.map((row, i) => ({ row, number: i + 1, matches: [] }));
            this.selected = []; this.refresh();
            this.message = `Pegado recibido: ${this.results.length} avisos preparados. Pulsa «Subir sin repetidos de este portal», arriba o al final de los avisos. Todavía no se han agregado a la matriz.`;
        },
        refresh() {
            const matches = candidateMatches(this.results, matrixRows(form).filter(row => normalize(row.source_name)===normalize(label)));
            this.results.forEach((item, i) => { item.matches = matches[i]; });
            const suggestions = candidateSuggestions(this.results);
            this.results.forEach((item, i) => Object.assign(item, suggestions[i]));
            this.selected = this.selected.filter(url => this.results.some(item => item.row.source_url === url && item.tone !== 'registered'));
        },
        selectAll() { this.refresh(); this.selected = this.results.filter(item => item.tone !== 'registered').map(item => item.row.source_url); },
        captureAll() { this.selected = this.results.map(item => item.row.source_url); this.add(true); },
        addSuggested() {
            if (this.busy) return;
            this.refresh();
            this.selected = this.results.filter(item => item.suggested).map(item => item.row.source_url);
            if (!this.selected.length) { this.message = 'No hay sugeridos nuevos: los avisos ya están registrados o tienen coincidencias pendientes de revisión.'; return; }
            this.add();
        },
        async add(includeRegistered = false) {
            if (this.busy || !this.selected.length) return;
            this.busy = true;
            try {
                const requested = [...this.selected];
                this.refresh();
                if (includeRegistered) this.selected = requested;
                const picked = this.results.filter(item => this.selected.includes(item.row.source_url));
                const pending = captureSelectedDetails(form,picked,panel.dataset.query || '', '',
                    message=>{this.message=message;},includeRegistered);
                this.selected = []; this.refresh();
                this.message = await pending;
                this.refresh();
            } finally { this.busy = false; }
        },
    };
}
