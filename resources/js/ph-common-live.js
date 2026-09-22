export function phCommonLive({ statuses = {}, labels = {}, groups = {}, summary = '' } = {}) {
    return {
        statuses,
        labels,
        summary,
        groups,
        openGroups: Object.fromEntries(Object.keys(groups).map(key => [key, true])),
        statusEffects: {
            '': 'Faltante: no alimenta el texto y queda pendiente en la matriz.',
            ok: 'Entra al resumen del Entregable como bien común verificado.',
            warn: 'No entra al resumen; queda pendiente para revisión del analista.',
            risk: 'No entra al resumen; queda como alerta para depurar o llevar a salvedad.',
            na: 'Se excluye del conteo y no se incorpora al Entregable.',
        },
        init() {
            if (this.isAutomaticSummary(this.summary)) this.summary = this.generatedSummary();
        },
        label(key) { return (this.labels[key] || key.replaceAll('_', ' ')).toLocaleLowerCase('es-CO'); },
        limited(names) { return names.slice(0, 9).join(', '); },
        namesFor(status) { return Object.entries(this.statuses).filter(([, value]) => value === status).map(([key]) => this.label(key)); },
        generatedSummary() {
            const parts = [];
            Object.values(this.groups).forEach(group => {
                const names = (group.keys || []).filter(key => this.statuses[key] === 'ok').map(key => this.label(key));
                if (names.length) parts.push(group.title + ': se verifican ' + this.limited(names) + '.');
            });
            return parts.length ? parts.join(' ') : 'No se han marcado bienes comunes verificados para incorporar al Entregable.';
        },
        isAutomaticSummary(text) {
            const value = String(text || '').trim();
            return value === '' || ['Bienes comunes esenciales:', 'Bienes comunes no esenciales', 'Áreas comunes de uso exclusivo:', 'Soporte operativo y técnico común:', 'Se verifican ', 'Quedan por confirmar ', 'Se registran alertas o salvedades en ', 'Los bienes comunes específicos deben confirmarse', 'No se han marcado bienes comunes verificados', 'Bienes comunes y soporte:'].some(prefix => value.startsWith(prefix));
        },
        updateCommonStatus(key, value) {
            this.statuses[key] = value;
            this.summary = this.generatedSummary();
            this.$nextTick(() => this.$root.querySelector('[data-common-summary]')?.dispatchEvent(new Event('input', { bubbles: true })));
        },
        toggleGroup(key) { this.openGroups[key] = !this.openGroups[key]; },
        isOpen(key) { return this.openGroups[key] !== false; },
        readyCount(keys) { return keys.filter(key => ['ok', 'warn', 'risk'].includes(this.statuses[key])).length; },
        applicableCount(keys) { return keys.filter(key => this.statuses[key] !== 'na').length; },
        effectFor(key) { return this.statusEffects[this.statuses[key] || ''] || this.statusEffects['']; },
    };
}
