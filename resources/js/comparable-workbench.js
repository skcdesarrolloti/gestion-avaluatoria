import { rowFactory } from './comparable-row-growth.js';
import { mapFields, compositionVisible, locationPoints } from './comparable-location.js';
import { comparablePhotos } from './comparable-photos.js';
import { comparableMapNavigation } from './comparable-map-navigation.js';
import { comparableRemoval } from './comparable-removal.js';
import { hasComparableData, missingComparableFields, comparableUrlKey } from './comparable-review.js';

const groups = {
    capture: ['source_type', 'source_name', 'source_url', 'operation', 'property_type', 'neighborhood',
        'project_name', 'ph_regime', 'price_amount', 'price_unit', 'area_m2', 'consulted_at', 'contact_name', 'contact_phone'],
    location: ['source_name', 'source_url', 'consulted_at', 'neighborhood', 'address_hint', 'project_name', ...mapFields],
    composition: ['ph_regime', 'ph_special', 'area_m2', 'area_basis', 'private_built_m2', 'private_free_m2', 'ph_units_detail', 'land_m2', 'built_m2', 'annexes_detail', 'areas_source'],
    ph: ['source_name', 'source_url', 'consulted_at', 'price_amount', 'price_unit', 'ph_regime', 'ph_special',
        'area_m2', 'area_basis', 'private_built_m2', 'private_free_m2', 'areas_source', 'parking_spaces',
        'ph_parking_presence', 'ph_parking_in_price', 'ph_parking_nature', 'ph_deposit_presence', 'ph_deposit_count',
        'ph_deposit_in_price', 'ph_deposit_nature', 'ph_other_components', 'ph_units_detail', 'ph_components_source'],
    attributes: ['ph_regime', 'admin_fee', 'vat_applies', 'bedrooms', 'bathrooms', 'parking_spaces', 'floor_level', 'stratum',
        'age_years', 'building_condition', 'conservation_state', 'view_quality', 'finish_quality', 'elevator',
        'amenities', 'security_features', 'power_plant', 'parking_relation', 'balcony_terrace', 'noise_humidity_sun'],
    review: ['active', 'status', 'analysis_factor', 'query_used', 'listing_code', 'listing_date',
        'legal_relation_notes', 'comparability_notes', 'rejection_reason'],
};

export function comparableWorkbench() {
    let entries = [], resize, form, createRow, prepare, grow;
    return {
        ...comparablePhotos(), ...comparableMapNavigation(), ...comparableRemoval(), phFilter: 'all', mode: 'table', group: 'capture', filter: 'all', search: '', page: 1, pages: 1, total: 0,
        pending: 0, duplicates: 0, shown: 0, usedIndexes: [], mapPoints: [],
        get groupHelp() {
            return {
                capture: 'Fuente, enlace, precio, área y contacto del aviso.',
                composition: 'Áreas originales y sus soportes según PH/no PH. La desagregación de valores y los cálculos corresponden a 8.4.',
                ph: 'PH: área privada, parqueaderos y depósitos, inclusión en el precio y naturaleza jurídica. Vacío significa por verificar; no equivale a No. Para condominio usa Áreas y componentes.',
                location: 'Sector, dirección y coordenadas; indica si la ubicación es aproximada.',
                attributes: 'Alcobas, baños, parqueaderos, edad, estado y dotaciones publicadas.',
                review: 'Estado, variable de análisis y razones para incluir o descartar. No aplica factores de ajuste.',
                all: 'Muestra todos los campos de la misma ficha. Cambiar de grupo conserva lo diligenciado.',
            }[this.group];
        },
        init() {
            form = this.$el;
            if (form.dataset.phSubject === 'si') this.group = 'ph';
            this.initPhotos(form);
            const headers = [...form.querySelectorAll('thead th')].map(th => th.childNodes[0].textContent.trim());
            createRow = rowFactory(form);
            prepare = (tr, index) => {
                const controls = [...tr.querySelectorAll('[name]')];
                for (const [column, cell] of [...tr.cells].entries()) {
                    const input = cell.querySelector('input:not([type=hidden]),select,textarea');
                    if (!input) continue;
                    input.id = `comparable-${index}-${column}`;
                    const label = document.createElement('label');
                    label.htmlFor = input.id;
                    label.className = 'comparable-field-label';
                    label.textContent = headers[column];
                    cell.prepend(label);
                    if (input.tagName !== 'SELECT' && input.type !== 'date' && !input.placeholder) {
                        input.placeholder = `Indica ${headers[column].toLowerCase()}`;
                    }
                }
                const summary = document.createElement('span');
                summary.className = 'comparable-row-summary';
                tr.cells[0].append(summary);
                return { tr, controls, summary, index, opened: false, used: false, missing: [], data: {} };
            };
            entries = [...form.querySelectorAll('tbody tr')].map(prepare);
            grow = event => { for (let i = 0; i < event.detail; i++) this.appendRow(); };
            form.addEventListener('comparable-grow', grow);
            this.initRemoval(form, entries);
            this.refresh();
            this.$watch('searchTab', () => { this.page = 1; this.render(); });
            resize = new ResizeObserver(() => this.syncWidth());
            resize.observe(this.$refs.grid);
            resize.observe(this.$refs.grid.querySelector('table'));
        },
        destroy() { resize?.disconnect(); form.removeEventListener('comparable-grow', grow); },
        appendRow() {
            const tr = createRow(entries.length);
            const entry = prepare(tr, entries.length);
            entries.push(entry);
            form.querySelector('tbody').append(tr);
            return entry;
        },
        refresh() {
            const counts = new Map();
            for (const entry of entries) {
                entry.data = Object.fromEntries(entry.controls.map(input => [input.name.match(/\[([^\]]+)\]$/)[1], input.value]));
                entry.used = hasComparableData(entry.data);
                entry.missing = missingComparableFields(entry.data);
                entry.key = comparableUrlKey(entry.data.source_url);
                if (entry.used && entry.key) counts.set(entry.key, (counts.get(entry.key) ?? 0) + 1);
            }
            for (const entry of entries) {
                entry.duplicate = !!entry.key && counts.get(entry.key) > 1;
                entry.summary.textContent = !entry.used ? 'Nueva muestra' :
                    `${entry.data.project_name || entry.data.source_name || 'Muestra'} · ${entry.missing.length ?
                        'Falta: ' + entry.missing.join(', ') : 'Datos básicos diligenciados; verificar soporte'}${entry.duplicate ? ' · Enlace repetido' : ''}`;
            }
            this.total = entries.filter(e => e.used).length;
            this.usedIndexes = entries.filter(e => e.used).map(e => String(e.index));
            this.removalSelection = this.removalSelection.filter(index => this.usedIndexes.includes(index));
            this.pending = entries.filter(e => e.used && e.missing.length).length;
            this.duplicates = entries.filter(e => e.used && e.duplicate).length;
            this.mapPoints = locationPoints(entries.map(e => e.data), {latitude:form.dataset.subjectLatitude, longitude:form.dataset.subjectLongitude});
            this.render();
        },
        render() {
            const query = this.search.toLocaleLowerCase('es').trim();
            const eligible = entries.filter(e => (this.phFilter === 'all' || (e.data.ph_regime || 'por_verificar') === this.phFilter) && (e.used || e.opened) &&
                (this.filter !== 'pending' || e.missing.length) && (this.filter !== 'duplicates' || e.duplicate) &&
                (!query || Object.values(e.data).join(' ').toLocaleLowerCase('es').includes(query)));
            const mapMode = this.searchTab === 'mapa', pageSize = mapMode ? 1 : 10;
            this.pages = Math.max(1, Math.ceil(eligible.length / pageSize));
            this.page = Math.min(this.page, this.pages);
            const visible = eligible.slice((this.page - 1) * pageSize, this.page * pageSize);
            this.mapIndex = mapMode ? visible[0]?.index ?? null : null;
            if (mapMode && this.photoOpen && this.photoIndex !== this.mapIndex && !this.photoBusy) this.photoOpen = false;
            this.shown = eligible.length;
            for (const entry of entries) entry.tr.hidden = !visible.includes(entry);
            const fields = groups[mapMode ? 'location' : this.group];
            const first = entries[0];
            if (!first) return;
            [...first.tr.cells].forEach((cell, index) => {
                const key = cell.querySelector('[name]')?.name.match(/\[([^\]]+)\]$/)?.[1];
                const hidden = index > 0 && (mapMode ? !fields.includes(key) : mapFields.includes(key) || (this.group !== 'all' && !fields?.includes(key)));
                form.querySelector(`thead th:nth-child(${index + 1})`).hidden = hidden;
                for (const entry of entries) entry.tr.cells[index].hidden = hidden || (!mapMode && this.mode === 'cards' && !compositionVisible(key, entry.data));
            });
            this.$nextTick(() => this.syncWidth());
        },
        add() {
            const entry = entries.find(e => !e.used && !e.opened) || this.appendRow();
            entry.opened = true;
            this.phFilter = 'all'; this.filter = 'all'; this.search = ''; this.group = 'capture';
            this.page = Math.ceil(entries.filter(e => e.used || e.opened).indexOf(entry) / 10 + 0.1);
            this.render();
            this.$nextTick(() => entry.tr.querySelector('select')?.focus());
        },
        showImported(index) {
            this.phFilter = 'all'; this.filter = 'all'; this.search = ''; this.group = 'capture';
            const entry = entries[index];
            this.page = Math.floor(entries.filter(e => e.used || e.opened).indexOf(entry) / 10) + 1;
            this.render();
        },
        syncWidth() { this.$refs.track.style.width = `${this.$refs.grid.scrollWidth}px`; },
    };
}
