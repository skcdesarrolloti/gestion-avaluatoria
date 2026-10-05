import { intakeTable } from './comparable-intake-table.js';
import { comparisonRows } from './comparable-source-facts.js';
import { publishedDetails } from './comparable-published-details.js';
import { sourceUpdate } from './comparable-source-update.js';
import { duplicateEvidence } from './comparable-duplicates.js';

const normalize = value => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/\s+/g, ' ').trim();
const portalKey = value => normalize(value).replace(/ inmuebles$/, '');
export const intakeStates = {review:'Por revisar', selected:'Seleccionado para análisis', selected_pending:'Seleccionado con pendientes', not_selected:'No seleccionado'};
export function intakeGroups(rows) {
    const groups = new Map();
    rows.forEach(row => {
        const key = row.property_group || row.id;
        if (!key) return;
        if (!groups.has(key)) groups.set(key, {key, rows:[], title:row.project_name || row.property_type || 'Inmueble por identificar'});
        groups.get(key).rows.push(row);
    });
    return [...groups.values()].map(group => {
        const states = new Set(group.rows.map(row => row.intake_state || (row.status === 'usada' ? 'selected_pending' : 'review')));
        group.state = states.size === 1 ? [...states][0] : 'review';
        group.conflicts = ['price_amount','area_m2','parking_spaces','ph_deposit_count','bathrooms','bedrooms','view_quality','elevator'].filter(key =>
            new Set(group.rows.map(row => normalize(row[key])).filter(Boolean)).size > 1);
        group.pending = group.rows.some(row => !row.price_amount || !row.area_m2 || !row.contact_phone);
        group.candidates = [];
        return group;
    }).map((group, _, all) => {
        group.candidates = all.filter(other => other.key !== group.key && group.rows.some(a => other.rows.some(b =>
            normalize(a.operation) === normalize(b.operation) && normalize(a.property_type) === normalize(b.property_type) &&
            (duplicateEvidence(a,b) || (normalize(a.project_name) && normalize(a.project_name) === normalize(b.project_name) && normalize(a.neighborhood) === normalize(b.neighborhood)) ||
            (normalize(a.contact_phone) && normalize(a.contact_phone) === normalize(b.contact_phone) && a.area_m2 && a.area_m2 === b.area_m2)))))
            .map(other => ({key:other.key,title:other.title}));
        return group;
    });
}
export function comparableIntake(getEntries, getForm) {
    return {
        intakeCards:[], intakeTargets:[], intakeFilter:'all', intakePage:1, intakePages:1, intakeCount:0, intakeSearch:'',
        intakePortal:'', intakePortals:[], intakeView:'review', intakeConfirmedCount:0,
        intakeStates, intakeTableData:{columns:[],rows:[]},
        intakeNavigate(view) {
            this.searchTab='matriz'; this.intakeStep=view; this.intakeView=view;
            this.mode='intake'; this.intakeFilter='all'; this.intakeSearch=''; this.intakePage=1; this.rebuildIntake();
        },
        intakePortalCount(portal) { return getEntries().filter(e => e.used && portalKey(e.data.source_name || 'Fuente pendiente')===portalKey(portal)).length; },
        intakeComparison(card) {
            let config={};
            try { config=JSON.parse(getForm().dataset.intakeEvidence || '{}'); } catch { /* A pending reference stays empty. */ }
            return comparisonRows(card,config);
        },
        intakeComplement(row, text) {
            if (!String(text || '').trim()) return;
            const changes=sourceUpdate(row,publishedDetails(text));
            Object.entries(changes).forEach(([key,value]) => this.intakeWrite(row.index,key,value));
            this.intakeChanged();
        },
        rebuildIntake() {
            const entries = getEntries();
            const groups = intakeGroups(entries.filter(e => e.used).map(e => ({...e.data,index:e.index})));
            let config={};
            try { config=JSON.parse(getForm()?.dataset?.intakeEvidence || '{}'); } catch {}
            this.intakeTableData=intakeTable(groups,config);
            this.intakeCount = groups.length;
            this.intakeTargets = groups.map(g => ({key:g.key,title:g.title}));
            let configured=[];
            try { configured=JSON.parse(getForm()?.dataset?.intakeSources || '[]'); } catch { /* Stored source labels remain available. */ }
            this.intakePortals=[...new Map([...configured,...groups.flatMap(g => g.rows.map(r => r.source_name || 'Fuente pendiente'))].map(p => [portalKey(p),p])).values()];
            const preferred=this.intakePortals.find(p => portalKey(p)===portalKey(this.sourcePortal));
            if (preferred) this.intakePortal=preferred;
            else if (!this.intakePortals.includes(this.intakePortal)) this.intakePortal=this.intakePortals[0] || '';
            this.intakeConfirmedCount=groups.filter(g => g.rows.some(r => r.capture_confirmation==='confirmed')).length;
            const query = normalize(this.intakeSearch);
            const visible=this.intakeView==='confirmed' ? groups.map(g => ({...g,rows:g.rows.filter(r => r.capture_confirmation==='confirmed')})).filter(g => g.rows.length)
                : groups.flatMap(g => g.rows.filter(r => portalKey(r.source_name || 'Fuente pendiente')===portalKey(this.intakePortal)).map(r => ({...g,key:r.id,rows:[r]})));
            const filtered = visible.filter(g => (this.intakeFilter === 'all' || (g.rows.every(r => r.capture_confirmation==='confirmed') ? 'confirmed' : g.rows.every(r => r.capture_confirmation==='excluded') ? 'excluded' : 'pending')===this.intakeFilter) &&
                (!query || normalize(g.rows.map(r => Object.values(r).join(' ')).join(' ')).includes(query)));
            this.intakePages = Math.max(1, filtered.length);
            this.intakePage = Math.min(this.intakePage, this.intakePages);
            this.intakeCards = filtered.slice(this.intakePage - 1, this.intakePage);
        },
        intakeConfirm(card, value) {
            card.rows.forEach(row => this.intakeWrite(row.index,'capture_confirmation',value));
            this.intakeChanged();
        },
        intakeWrite(index, key, value) {
            const control = getEntries()[index]?.controls.find(input => input.name.endsWith(`[${key}]`));
            if (control) control.value = value;
        },
        intakeChanged() { getForm().dispatchEvent(new Event('input',{bubbles:true})); },
        intakeDecision(card, value) {
            card.rows.forEach(row => this.intakeWrite(row.index,'intake_state',value));
            this.intakeChanged();
        },
        intakeLink(card, target) {
            if (!target || target === card.key) return;
            const entries = getEntries();
            const targets = entries.filter(e => e.used && (e.data.property_group || e.data.id) === target);
            if (!targets.length) return;
            const identity = targets[0].data.property_group || targets[0].data.id;
            [...targets.map(e => ({index:e.index})),...card.rows].forEach(row => {
                this.intakeWrite(row.index,'property_group',identity);
                this.intakeWrite(row.index,'intake_state','review');
            });
            this.intakeChanged();
        },
        intakeUnlink(row) {
            const group = getEntries()[row.index]?.data.property_group;
            getEntries().filter(e => group && e.data.property_group === group).forEach(e => this.intakeWrite(e.index,'intake_state','review'));
            this.intakeWrite(row.index,'property_group','');
            this.intakeWrite(row.index,'intake_state','review'); this.intakeChanged();
        },
        intakeEdit(row) { this.mode='cards'; this.group='all'; this.showImported(row.index); this.group='all'; this.render(); },
    };
}
