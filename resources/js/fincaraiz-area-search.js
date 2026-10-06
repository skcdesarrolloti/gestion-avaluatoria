import { previewFacts } from './comparable-preview-facts.js';
import { captureSelectedDetails } from './capture-selected-details.js';
import { candidateMatches, matrixRows, candidateSuggestions } from './comparable-candidate-review.js';

export function fincaraizAreaSearch() {
    let panel, form;
    return { previewFacts,
        phFilter: 'all',
        refreshDuplicates() {
            const matches = candidateMatches(this.results, matrixRows(form).filter(row=>String(row.source_name || '').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase()==='fincaraiz'));
            this.results.forEach((item, index) => {
                const signature = JSON.stringify(matches[index]);
                if (signature !== item.matchSignature) item.distinct = false;
                item.matchSignature = signature;
                item.matches = matches[index];
                item.number = index + 1;
            });
            candidateSuggestions(this.results).forEach((suggestion, i) => Object.assign(this.results[i], suggestion));
            this.selected = this.selected.filter(url => this.results.some(item => item.row.source_url === url && item.tone !== 'registered'));
        },
        selectSuggested() {
            this.refreshDuplicates();
            this.selected = this.visibleResults.filter(item => item.suggested).map(item => item.row.source_url);
            this.message = this.selected.length ? `${this.selected.length} sugeridos seleccionados. Puedes cambiar las casillas antes de agregar.` : 'No hay sugeridos nuevos en esta página: los avisos ya están en la matriz o requieren revisar coincidencias con ella.';
        },
        get visibleResults() { return this.results.filter(item => this.phFilter === 'all' || (item.row.ph_regime || 'por_verificar') === this.phFilter); },
        neighborhood: '', neighborhoodId: '', neighborhoods: [], results: [], selected: [], busy: false, page: 1, hasNext: false, message: '', resultUrl: '', notice: '',
        init() {
            panel = this.$el; form = panel.closest('form');
            this.neighborhoods = JSON.parse(panel.dataset.neighborhoods || '[]');
            const initial = this.neighborhoods.find(item => item.id === panel.dataset.neighborhoodId);
            if (initial) this.choose(initial);
        },
        get suggestions() {
            const normalize = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
            return this.neighborhoods.filter(item => normalize(item.name).includes(normalize(this.neighborhood))).slice(0, 12);
        },
        choose(item) { this.clear(); this.neighborhood = item.name; this.neighborhoodId = item.id; },
        editNeighborhood() { this.neighborhoodId = ''; this.clear(); },
        get searchUrl() {
            return this.neighborhoods.find(item => item.id === this.neighborhoodId)?.search_url || '';
        },
        clear() { this.results = []; this.selected = []; this.message = ''; this.hasNext = false; this.page = 1; this.resultUrl = ''; this.notice = ''; },
        money(value) {
            let raw = String(value ?? '').replace(/[^\d.,]/g, '');
            if (raw.includes(',')) raw = raw.replaceAll('.', '').replace(',', '.');
            else if (/^\d{1,3}(\.\d{3})+$/.test(raw)) raw = raw.replaceAll('.', '');
            const amount = Number(raw);
            return raw && Number.isFinite(amount) ? new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(amount) : 'Precio pendiente';
        },
        async search(page = 1) {
            if (this.busy) return;
            if (!this.neighborhoodId) { this.message = 'Selecciona un barrio de las sugerencias del catálogo.'; return; }
            this.busy = true; this.results = []; this.selected = []; this.hasNext = false; this.notice = '';
            this.message = 'Buscando oficinas en el barrio…';
            const abort = new AbortController();
            const timer = setTimeout(() => abort.abort(), 25000);
            try {
                const body = new FormData();
                body.set('neighborhood_id', this.neighborhoodId); body.set('page', String(page));
                body.set('portal', panel.dataset.portal || 'fincaraiz');
                body.set('component', panel.dataset.component || '');
                const token = form.querySelector('[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.content || '';
                body.set('_token', token);
                const response = await fetch(panel.dataset.endpoint, { method: 'POST', body, signal: abort.signal, headers: { Accept: 'application/json', 'X-CSRF-Token': token } });
                if (!response.headers.get('content-type')?.includes('application/json')) throw new Error('Comprueba la sesión e intenta de nuevo.');
                const data = await response.json();
                if (!response.ok || !data.ok) throw new Error(data.message || 'No se pudo consultar el portal.');
                this.results = data.results.map(item => ({ ...item, row: { ...item.row, ph_regime: item.row.ph_regime || 'por_verificar' } })); this.page = data.page; this.hasNext = data.has_next; this.resultUrl = data.url;
                this.refreshDuplicates();
                this.notice += data.notice || '';
                this.message = this.results.length ? `${this.results.length} avisos en la página ${this.page}: ${this.results.filter(item => item.suggested).length} sugeridos nuevos; ${this.results.filter(item => item.tone === 'registered').length} ya incorporados.` : 'No se encontraron avisos legibles. Comprueba el barrio en el portal.';
            } catch (error) { this.message = error.name === 'AbortError' ? 'El portal tardó demasiado. Reintenta o abre la búsqueda.' : error.message; }
            finally { clearTimeout(timer); this.busy = false; }
        },
        async incorporate(includeRegistered = false) {
            if (this.busy || !this.selected.length) return;
            this.busy=true;
            try {
            const requested = [...this.selected]; this.refreshDuplicates();
            if (includeRegistered) this.selected = requested;
            const picked = this.results.filter(item => this.selected.includes(item.row.source_url));
            this.message = await captureSelectedDetails(form,picked,this.resultUrl,'',
                message=>{this.message=message;},includeRegistered);
            this.selected = [];
            this.refreshDuplicates();
            } finally { this.busy=false; }
        },
        captureAll() {
            this.refreshDuplicates();
            this.selected = this.visibleResults.map(item => item.row.source_url);
            this.incorporate(true);
        },
    };
}
