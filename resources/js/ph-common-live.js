export function phCommonLive({ statuses = {}, labels = {}, summary = '', typology = '' } = {}) {
    const focus = {
        residencial: 'seguridad, habitabilidad, amenidades y sostenimiento común',
        oficinas: 'representatividad corporativa, acceso de usuarios, parqueo, seguridad y continuidad operativa',
        comercio: 'flujo de público, visibilidad operativa, parqueo, seguridad y soporte para atención a usuarios',
        bodegas: 'movilidad logística, control de acceso, maniobra, seguridad industrial y continuidad operativa',
        mixto: 'funcionalidad, seguridad, soporte común y compatibilidad entre usos',
    };
    return {
        statuses,
        labels,
        summary,
        typology,
        statusEffects: {
            '': 'Faltante: no alimenta el texto y queda pendiente en la matriz.',
            ok: 'Entra al Entregable como soporte o beneficio verificado.',
            warn: 'Queda en el Entregable como aspecto por confirmar.',
            risk: 'Entra como alerta o salvedad para revisión del analista.',
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
            const ok = this.namesFor('ok'), warn = this.namesFor('warn'), risk = this.namesFor('risk');
            if (ok.length) parts.push('Se verifican ' + this.limited(ok) + '.');
            if (warn.length) parts.push('Quedan por confirmar ' + this.limited(warn) + '.');
            if (risk.length) parts.push('Se registran alertas o salvedades en ' + this.limited(risk) + '.');
            const support = parts.length ? parts.join(' ') : 'Los bienes comunes específicos deben confirmarse con visita y soportes actuales.';
            const valueFocus = focus[this.typology] || focus.mixto;
            return support + ' Estos elementos deben valorarse frente a copropiedades de la misma tipología, porque aportan ' + valueFocus + ' y pueden incidir en funcionalidad, deseabilidad, comercialización y comparación del inmueble.';
        },
        isAutomaticSummary(text) {
            const value = String(text || '').trim();
            return value === '' || ['Se verifican ', 'Quedan por confirmar ', 'Se registran alertas o salvedades en ', 'Los bienes comunes específicos deben confirmarse', 'Bienes comunes y soporte:'].some(prefix => value.startsWith(prefix));
        },
        updateCommonStatus(key, value) {
            this.statuses[key] = value;
            this.summary = this.generatedSummary();
            this.$nextTick(() => this.$root.querySelector('[data-common-summary]')?.dispatchEvent(new Event('input', { bubbles: true })));
        },
        readyCount(keys) { return keys.filter(key => ['ok', 'warn', 'risk'].includes(this.statuses[key])).length; },
        applicableCount(keys) { return keys.filter(key => this.statuses[key] !== 'na').length; },
        effectFor(key) { return this.statusEffects[this.statuses[key] || ''] || this.statusEffects['']; },
    };
}
