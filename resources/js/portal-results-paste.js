import { fillRows, parseComparableText } from './comparable-bulk-import.js';
import { candidateMatches, candidateSuggestions, matrixRows } from './comparable-candidate-review.js';
import { previewFacts } from './comparable-preview-facts.js';
import { flushModuleForm } from './module-autosave.js';
import { completeListingDetails } from './comparable-detail-enrichment.js';
const normalize = value => String(value ?? '').toLowerCase().trim();

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
            if (panel.dataset.detailEndpoint) {
                this.busy = true;
                try {
                    const detail = await completeListingDetails(this.results, panel.dataset.detailEndpoint,
                        (number,total) => { this.message = `Completando ficha ${number} de ${total}. Aún no se ha incorporado el lote.`; });
                    this.message = `${this.results.length} avisos preparados: ${detail.completed} fichas leídas y ${detail.failed} pendientes. Revisa los cuadros y pulsa Agregar. Todavía no se han guardado en la matriz.`;
                } finally { this.busy = false; this.selected = []; this.refresh(); }
                return;
            }
            this.selected = []; this.refresh();
            this.message = `Pegado recibido: ${this.results.length} avisos preparados. Ahora pulsa «Agregar sugeridos sin coincidencias», debajo del cuadro. Todavía no se han agregado a la matriz.`;
        },
        refresh() {
            const matches = candidateMatches(this.results, matrixRows(form));
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
                const rows = this.results.filter(item => this.selected.includes(item.row.source_url)).map(item => item.row);
                const result = fillRows(form, rows, panel.dataset.query || '', undefined, { deferDuplicateReview: true });
                this.selected = []; this.refresh();
                const summary = `${result.count} avisos nuevos por revisar. ${result.enriched || 0} anuncios existentes complementados. ${result.duplicates} enlaces ya registrados sin duplicar. ${result.overflow} sin cargar.`;
                this.message = `${summary} Guardando en la base de datos…`;
                const saved = await flushModuleForm(form);
                this.message = `${summary} ${saved ? 'Guardado confirmado en la base de datos. Puedes continuar en Revisar por portal.' : 'Guardado pendiente. No cierres ni recargues: pulsa Guardar matriz para reintentar y consulta el aviso de guardado.'}`;
            } finally { this.busy = false; }
        },
    };
}
