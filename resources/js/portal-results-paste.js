import { parseComparableText } from './comparable-bulk-import.js';
import { candidateMatches, candidateSuggestions, matrixRows } from './comparable-candidate-review.js';
import { previewFacts } from './comparable-preview-facts.js';
import { captureSelectedDetails } from './capture-selected-details.js';
import { comparableUrlKey } from './comparable-review.js';
import { sourceUpdate } from './comparable-source-update.js';
import { flushModuleForm } from './module-autosave.js';
const normalize = value => String(value ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim().replace(/ inmuebles$/,'');

export function portalResultsPaste({ label, readRows, validUrl, allowTsv = false, emptyMessage = '', readText = false }) {
    let panel, form, leaveWarning;
    return {
        previewFacts, sourceRevision: 0, sourceRestartPending: false, results: [], pages: [], portalTotal: 0, selected: [], message: '', pastedText: '', busy: false,
        get sourceSavedCount() { void this.sourceRevision; return form ? matrixRows(form).filter(row=>normalize(row.source_name)===normalize(label)).length : 0; },
        restartPreview() {
            this.results=[]; this.pages=[]; this.portalTotal=0; this.selected=[]; this.pastedText='';
            this.sourceRestartPending=false;
            this.message='Nueva captura de esta fuente. Copia la primera página y después añade las siguientes.';
        },
        sourceRestarted(event) { if (!event.detail.source || normalize(event.detail.source)===normalize(label)) this.restartPreview(); },
        async restartEmptySource() {
            if (this.busy) return;
            this.busy=true;
            try { if (await flushModuleForm(form)) this.restartPreview();
                else this.message='Guardado pendiente. No se inició otra captura; pulsa Guardar matriz y reintenta.';
            } finally { this.busy=false; }
        },
        get receivedCount() { return this.pages.reduce((sum,page)=>sum+page.count,0); },
        get suggestedCount() { return this.results.filter(item => item.suggested).length; },
        get registeredCount() { return this.results.filter(item => item.tone === 'registered').length; },
        get reviewCount() { return this.results.filter(item => item.tone === 'review').length; },
        init() {
            panel = this.$el; form = document.getElementById('tabla-madre-83');
            leaveWarning=event=>{
                if (this.results.some(item=>item.tone!=='registered')) { event.preventDefault(); event.returnValue=''; }
            };
            globalThis.window?.addEventListener?.('beforeunload',leaveWarning);
        },
        destroy() { globalThis.window?.removeEventListener?.('beforeunload',leaveWarning); },
        async paste(event) {
            event.preventDefault();
            if (this.busy) return;
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
            this.collectPage(rows,text);
            this.selected = []; this.refresh();
            this.message = `${this.pages.length} páginas recogidas: ${this.receivedCount} avisos recibidos, ${this.results.length} enlaces distintos. ${this.portalTotal > this.receivedCount ? 'El portal anuncia ' + this.portalTotal + '; faltan páginas por copiar. Pulsa «Pegar otra página».' : 'Revisa los colores y pulsa «Subir sin repetidos de este portal».'} Pegar no guarda las muestras.`;
        },
        collectPage(rows,text='') {
            const total=String(text).match(/mostrando\s*[\d.,]+\s*[-–]\s*[\d.,]+\s*de\s*([\d.,]+)\s*resultados/i)?.[1];
            if (total) this.portalTotal=Number(total.replace(/[.,]/g,''));
            const signature=rows.map(row=>comparableUrlKey(row.source_url)).sort().join('\n');
            if (!this.pages.some(page=>page.signature===signature)) this.pages.push({signature,count:rows.length});
            const known=new Map(this.results.map(item=>[comparableUrlKey(item.row.source_url),item]));
            for (const row of rows) {
                const key=comparableUrlKey(row.source_url), prior=known.get(key);
                if (prior) prior.row={...prior.row,...sourceUpdate(prior.row,row)};
                else {
                    const item={row,number:this.results.length+1,matches:[]};
                    this.results.push(item); known.set(key,item);
                }
            }
        },
        refresh() {
            this.sourceRevision++;
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
        nextPage() {
            if (this.busy) return;
            this.pastedText = '';
            this.message = `Campo listo para otra página. Se conservan los ${this.results.length} avisos recogidos aquí. En el portal abre la página siguiente, copia con Ctrl+A → Ctrl+C y pega aquí con Ctrl+V. Al terminar todas las páginas, sube los no repetidos y espera Guardado confirmado.`;
            this.$refs?.pasteInput?.focus();
        },
    };
}
