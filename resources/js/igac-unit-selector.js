export function normalizeIgac(text) {
    return String(text || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, ' ').trim();
}

export function filterIgacOptions(options, { type = '', query = '', selected = '', showAll = false, rules = {} } = {}) {
    const search = normalizeIgac(query);
    const family = Object.values(rules).find(rule => rule.aliases.some(alias => normalizeIgac(alias) === search));
    const typeRule = showAll ? null : rules[type === 'parqueadero' ? 'parqueo' : type];
    const matchesRule = (item, rule) => {
        const text = normalizeIgac(rule.fields.map(field => item[field] || '').join(' '));
        return rule.terms.some(term => text.includes(normalizeIgac(term)));
    };
    return options.filter(item => {
        if (selected && item.value === selected) return true;
        const text = normalizeIgac([item.label, item.description, item.specifications].join(' '));
        if (typeRule && !matchesRule(item, typeRule)) return false;
        return !search || (family ? matchesRule(item, family)
            : search.split(' ').every(term => text.includes(term)));
    });
}

export function igacUnitSelector(initial) {
    return {
        ...initial, igacQuery: '', igacShowAll: false,
        get igacType() { return this.constructionType || this.defaultConstructionType || ''; },
        get igacCatalog() {
            return Object.entries(this.typologies).flatMap(([category,options])=>options.map(item=>({...item,category,label:item.label+' · '+category})));
        },
        get allIgacOptions() {
            const categories=this.igacFamilies?.[this.igacType] ??
                (!this.igacType && this.igacCategory?[this.igacCategory]:Object.keys(this.typologies));
            return filterIgacOptions(this.igacCatalog.filter(item=>categories.includes(item.category)),{type:this.igacType,rules:this.igacFilterRules});
        },
        get igacOutsideType() { return Boolean(this.igacHint) && !this.allIgacOptions.some(item=>item.value===this.igacHint); },
        get igacMatches() {
            return filterIgacOptions(this.igacShowAll?this.igacCatalog:this.allIgacOptions,{query:this.igacQuery,rules:this.igacFilterRules});
        },
        get igacOptions() {
            const options=this.igacMatches, previous=this.igacCatalog.find(item=>item.value===this.igacHint);
            return previous && !options.some(item=>item.value===this.igacHint)?[...options,{...previous,
                label:(this.igacOutsideType?'Anterior · revisar tipo: ':'Selección fuera de búsqueda: ')+previous.label}]:options;
        },
        selectIgacCategory() {
            const selected = this.igacCatalog.find(item => item.value === this.igacHint);
            if (selected) this.igacCategory = selected.category;
        },
        syncIgacFromConstruction() {
            this.igacQuery = '';
            this.igacShowAll = false;
            if (this.igacHint) { this.selectIgacCategory(); return; }
            this.igacCategory = this.constructionIgacCategories?.[this.igacType] || '';
        },
    };
}
