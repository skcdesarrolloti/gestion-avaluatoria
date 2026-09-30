import { fillRows } from './comparable-bulk-import.js';

export function fincaraizAreaSearch() {
    let panel, form;
    return {
        neighborhood: '', results: [], selected: [], busy: false, page: 1, hasNext: false, message: '', resultUrl: '',
        init() { panel = this.$el; form = panel.closest('form'); this.neighborhood = panel.dataset.neighborhood || ''; },
        get searchUrl() {
            let slug = this.neighborhood.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            if (slug === 'castillo-grande') slug = 'castillogrande';
            return `https://www.fincaraiz.com.co/venta/oficinas/${slug}/cartagena`;
        },
        clear() { this.results = []; this.selected = []; this.message = ''; this.hasNext = false; this.page = 1; this.resultUrl = ''; },
        money(value) { return value ? new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(Number(String(value).replace(',', '.'))) : 'Precio pendiente'; },
        async search(page = 1) {
            if (this.busy) return;
            if (!this.neighborhood.trim()) { this.message = 'Escribe el barrio.'; return; }
            this.busy = true; this.results = []; this.selected = []; this.hasNext = false;
            this.message = 'Buscando oficinas en el barrio…';
            const abort = new AbortController();
            const timer = setTimeout(() => abort.abort(), 25000);
            try {
                const body = new FormData();
                body.set('neighborhood', this.neighborhood); body.set('page', String(page));
                const token = form.querySelector('[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.content || '';
                body.set('_token', token);
                const response = await fetch(panel.dataset.endpoint, { method: 'POST', body, signal: abort.signal, headers: { Accept: 'application/json', 'X-CSRF-Token': token } });
                if (!response.headers.get('content-type')?.includes('application/json')) throw new Error('Comprueba la sesión e intenta de nuevo.');
                const data = await response.json();
                if (!response.ok || !data.ok) throw new Error(data.message || 'No se pudo consultar el portal.');
                this.results = data.results; this.page = data.page; this.hasNext = data.has_next; this.resultUrl = data.url;
                this.message = this.results.length ? `${this.results.length} avisos en la página ${this.page}. Marca los que quieras incorporar; aún no están en la tabla.` : 'No se encontraron avisos legibles. Comprueba el barrio en el portal.';
            } catch (error) { this.message = error.name === 'AbortError' ? 'El portal tardó demasiado. Reintenta o abre la búsqueda.' : error.message; }
            finally { clearTimeout(timer); this.busy = false; }
        },
        incorporate() {
            const rows = this.results.filter(item => this.selected.includes(item.row.source_url)).map(item => ({ ...item.row }));
            const counts = fillRows(form, rows, this.resultUrl);
            this.message = `${counts.count} agregados por verificar; ${counts.duplicates} enlaces repetidos omitidos; ${counts.suspected} posibles duplicados sin agregar; ${counts.overflow} sin espacio. Comprueba el estado de guardado.`;
            this.selected = [];
        },
    };
}
