export function normalizeIgac(text) {
    return String(text || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, ' ').trim();
}

export function filterIgacOptions(options, { type = '', query = '', selected = '', showAll = false, rules = {} } = {}) {
    const search = normalizeIgac(query);
    const family = Object.values(rules).find(rule => rule.aliases.some(alias => normalizeIgac(alias) === search));
    const typeRule = showAll ? null : rules[type];
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
        get allIgacOptions() {
            const categories = this.constructionType === 'oficina'
                ? [...new Set(['COMERCIALES', 'EDIFICIOS', this.igacCategory])] : [this.igacCategory];
            return categories.flatMap(category => (this.typologies[category] || []).map(item => ({ ...item, category,
                label: item.label + (this.constructionType === 'oficina' ? ' · ' + category : '') })));
        },
        get igacOptions() {
            return filterIgacOptions(this.allIgacOptions, { type: this.constructionType, query: this.igacQuery,
                selected: this.igacHint, showAll: this.igacShowAll, rules: this.igacFilterRules });
        },
        selectIgacCategory() {
            const selected = this.allIgacOptions.find(item => item.value === this.igacHint);
            if (selected) this.igacCategory = selected.category;
        },
        syncIgacFromConstruction() {
            this.igacQuery = '';
            this.igacShowAll = false;
            if (this.constructionType === 'oficina' && this.igacHint) { this.selectIgacCategory(); return; }
            const next = this.constructionIgacCategories[this.constructionType] || (this.unitKind === 'annex' ? 'ANEXOS' : '');
            if (next && next !== this.igacCategory) this.igacCategory = next;
            if (!this.allIgacOptions.some(item => item.value === this.igacHint)) this.igacHint = '';
        },
    };
}
