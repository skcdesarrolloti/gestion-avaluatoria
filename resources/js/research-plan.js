const normalize = value => String(value ?? '').trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[_\s]+/g, ' ');
const unknown = value => ['', 'no verificado', 'por verificar', 'desconocido', 'no publicado', 'pendiente'].includes(normalize(value));
export function researchValue(raw, factor, key) {
    if (unknown(raw)) return null;
    const text = normalize(raw);
    if (factor.kind === 'numeric') {
        if (!/^\d+(?:[.,]\d+)?$/.test(text)) return null;
        const value = Number(text.replace(',', '.'));
        return ['area', 'built', 'land', 'height'].includes(key) && value <= 0 ? null : value;
    }
    if (factor.kind === 'binary') {
        if (['1', 'si', 'yes'].includes(text)) return 1;
        if (['0', 'no'].includes(text)) return 0;
        return null;
    }
    const categories = String(factor.categories ?? '').split('\n').map(normalize).filter(Boolean);
    return categories.includes(text) ? text : null;
}
export function factorEvidence(key, factor, groups) {
    const ready = [], values = [], portals = {};
    let missing = 0, conflicts = 0, formats = 0, context = 0;
    for (const group of groups) {
        const present = []; let revision = false;
        for (const ad of group.ads) {
            const stats = portals[ad.portal] ??= { ads: 0, present: 0, readable: 0 };
            stats.ads++;
            const raw = ad.values[key];
            if (!unknown(raw)) stats.present++;
            const value = researchValue(raw, factor, key);
            if (value !== null) { stats.readable++; present.push(value); }
            revision ||= ad.revision;
        }
        const distinct = [...new Set(present)];
        if (group.contextPending) { context++; continue; }
        if (distinct.length > 1 || revision) { conflicts++; continue; }
        if (!distinct.length) {
            if (group.ads.some(ad => !unknown(ad.values[key]))) formats++;
            else missing++;
            continue;
        }
        // An unrecognized value in another source still requires reconciliation.
        if (group.ads.some(ad => !unknown(ad.values[key]) && researchValue(ad.values[key], factor, key) === null)) { formats++; continue; }
        ready.push(group.id); values.push(distinct[0]);
    }
    return { ready, missing, conflicts, formats, context, variation: new Set(values).size, portals };
}
export function researchSummary(plan, evidence) {
    const selected = Object.entries(plan.factors).filter(([, f]) => ['model', 'investigate'].includes(f.decision));
    const stats = Object.fromEntries(Object.entries(plan.factors).map(([key, f]) => [key, factorEvidence(key, f, evidence.groups)]));
    let parameters = 0; const warnings = [];
    for (const [key, f] of selected) {
        const categories = [...new Set(String(f.categories ?? '').split('\n').map(normalize).filter(Boolean))];
        parameters += f.kind === 'categorical' ? Math.max(0, categories.length - 1) : 1;
        if (f.kind === 'categorical' && categories.length < 2) warnings.push(`${key}: define al menos dos categorías.`);
        if (!f.reason.trim() || !f.definition.trim()) warnings.push(`${key}: completa definición y justificación.`);
        if (researchValue(evidence.subjects[key], f, key) === null) warnings.push(`${key}: completa o concilia el dato del sujeto.`);
        if (stats[key].variation < 2) warnings.push(`${key}: no hay variación suficiente observada.`);
    }
    const areaKey = ['area','built','land'].find(key => plan.factors[key]);
    if (areaKey) stats[areaKey] = factorEvidence(areaKey,{kind:'numeric'},evidence.groups);
    const areaReady = areaKey ? stats[areaKey].ready.length : 0;
    const joint = selected.length ? evidence.groups.filter(group => (!areaKey || stats[areaKey].ready.includes(group.id)) && selected.every(([key]) => stats[key].ready.includes(group.id))).length : 0;
    return { stats, areaKey, areaReady, selected: selected.length, parameters, joint, target: parameters * plan.target_ratio, warnings };
}
export function comparisonState(key, factor, ads) {
    const known = ads.filter(ad => !unknown(ad.values[key]));
    if (!known.length) return 'missing';
    const parsed = known.map(ad => researchValue(ad.values[key], factor, key));
    if (new Set(parsed.filter(v => v !== null)).size > 1) return 'difference';
    if (known.some(ad => ad.revision) || parsed.some(v => v === null)) return 'review';
    if (known.length < ads.length) return 'incomplete';
    return known.length > 1 ? 'equal' : known.length ? 'single' : 'missing';
}
export function publishedAreaState(ads) {
    const state = comparisonState('area',{kind:'numeric'},ads.map(ad => ({values:{area:ad.publishedArea},revision:ad.revision})));
    if (state === 'difference' || state === 'missing') return state;
    const bases = ads.filter(ad => !unknown(ad.publishedArea)).map(ad => normalize(ad.areaBasis));
    return bases.some(unknown) || new Set(bases).size > 1 ? 'review' : state;
}
export function researchPlan(config) {
    return {
        plan: config.plan, evidence: config.evidence, catalog: config.catalog,
        comparisonId: config.evidence.groups[0]?.id || '',
        get comparisonGroup() { return this.evidence.groups.find(g => g.id === this.comparisonId); },
        get comparisonKeys() {
            const area = ['area','built','land'].find(key => this.plan.factors[key]);
            const selected = Object.keys(this.plan.factors).filter(k => ['model','investigate'].includes(this.plan.factors[k].decision));
            const keys = selected.length ? selected : ['bathrooms','bedrooms','parking','deposit','age','floor','elevator','view'];
            return [...new Set([area,...keys].filter(k => k && this.plan.factors[k]))];
        },
        get publishedAreaState() { return publishedAreaState(this.comparisonGroup?.ads || []); },
        get publishedAreaLabel() { return ({difference:'Diferencia',review:'Base / dato por revisar',incomplete:'Datos incompletos',equal:'Coinciden con misma base',single:'Una fuente',missing:'Sin dato'})[this.publishedAreaState]; },
        publishedAreaClass(ad) { return unknown(ad.publishedArea) ? 'bg-slate-50 text-slate-600' : this.publishedAreaState === 'equal' ? 'bg-emerald-50 text-emerald-900' : ['difference','review'].includes(this.publishedAreaState) ? 'bg-amber-100 text-amber-900' : 'bg-slate-50 text-slate-700'; },
        comparisonState(key) { return comparisonState(key,['area','built','land'].includes(key) ? {kind:'numeric'} : this.plan.factors[key],this.comparisonGroup?.ads || []); },
        comparisonLabel(key) { return ({difference:'Diferencia',review:'Revisar formato / relectura',incomplete:'Datos incompletos',equal:'Coinciden',single:'Una fuente',missing:'Sin dato'})[this.comparisonState(key)]; },
        comparisonClass(key, ad) {
            if (unknown(ad.values[key])) return 'bg-slate-50 text-slate-600';
            return this.comparisonState(key) === 'equal' ? 'bg-emerald-50 text-emerald-900' : ['difference','review'].includes(this.comparisonState(key)) ? 'bg-amber-100 text-amber-900' : 'bg-slate-50 text-slate-700';
        },
        comparisonValue(key,ad) { return unknown(ad.values[key]) ? 'No publicado' : ad.values[key]; },
        get summary() { return researchSummary(this.plan, this.evidence); },
        get payload() { return JSON.stringify(this.plan); },
        subjectLabel(key) { return this.evidence.subjects[key] || 'Pendiente en numeral 3'; },
        factorLabel(key) { return this.catalog[key]?.label || key; },
        warningLabel(text) { const index = text.indexOf(':'); return `${this.factorLabel(text.slice(0, index))}${text.slice(index)}`; },
    };
}
