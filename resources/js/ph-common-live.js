export function phCommonLive({ statuses = {}, labels = {}, notes = {}, groups = {}, priorities = [], typologyLabel = '', summary = '' } = {}) {
    return {
        statuses, labels, notes, summary, groups, priorities, typologyLabel,
        openGroups: Object.fromEntries(Object.keys(groups).map(key => [key, true])),
        statusEffects: {
            '': 'Faltante: no alimenta el texto y queda pendiente en la matriz.',
            ok: 'Entra al resumen como bien común verificado por documento, sitio o criterio del analista.',
            warn: 'Entra como mención documental pendiente de confirmar en sitio.',
            risk: 'Entra como alerta por depurar antes del Entregable.',
            na: 'Se excluye del conteo y no se incorpora al Entregable.',
        },
        init() { if (this.isAutomaticSummary(this.summary)) this.summary = this.generatedSummary(); },
        label(key) { return (this.labels[key] || key.replaceAll('_', ' ')).toLocaleLowerCase('es-CO'); },
        limited(names) { return names.slice(0, 9).join(', '); },
        hasEvidence(key) { return this.statuses[key] && this.statuses[key] !== 'na' && !String(this.notes[key] || '').toLowerCase().startsWith('no identificado'); },
        source(key) {
            const text = String(this.notes[key] || '').toLowerCase();
            const site = /\bvisita\b|foto|fotograf|inspecci[oó]n|\bsitio\b|\bcampo\b/.test(text);
            const doc = /menci[oó]n documental|reglamento|escritura|pdf|p\.|p[aá]gina|cl[aá]usula/.test(text);
            return site && doc ? 'ambos' : (site ? 'sitio' : (doc ? 'documento' : 'analista'));
        },
        originLabel(key) { return ({ ambos: 'Reglamento y sitio', sitio: 'Sitio / fotografías', documento: 'Reglamento / documento', analista: 'Criterio del analista' })[this.source(key)]; },
        generatedSummary() {
            const parts = [];
            const priority = this.priorities.filter(key => this.hasEvidence(key)).map(key => this.label(key));
            if (priority.length) parts.push('Para la tipología ' + (this.typologyLabel || 'seleccionada') + ', los elementos prioritarios identificados son ' + this.limited(priority) + '.');
            Object.values(this.groups).forEach(group => {
                const bucket = { documento: [], sitio: [], analista: [], pendiente: [], riesgo: [] };
                (group.keys || []).forEach(key => {
                    if (!this.hasEvidence(key)) return;
                    const name = this.label(key), status = this.statuses[key], source = this.source(key);
                    if (status === 'risk') bucket.riesgo.push(name);
                    else if (status === 'ok' && (source === 'sitio' || source === 'ambos')) bucket.sitio.push(name);
                    else if (source === 'documento') bucket.documento.push(name);
                    else if (status === 'ok') bucket.analista.push(name);
                    else bucket.pendiente.push(name);
                });
                const lines = [];
                if (bucket.documento.length) lines.push('el reglamento o soporte documental menciona ' + this.limited(bucket.documento));
                if (bucket.sitio.length) lines.push('en sitio o fotografías se verifica ' + this.limited(bucket.sitio));
                if (bucket.analista.length) lines.push('el analista verifica ' + this.limited(bucket.analista));
                if (bucket.pendiente.length) lines.push('pendiente de verificar en sitio ' + this.limited(bucket.pendiente));
                if (bucket.riesgo.length) lines.push('con alerta por depurar ' + this.limited(bucket.riesgo));
                if (lines.length) parts.push(group.title + ': ' + lines.join('; ') + '.');
            });
            return parts.length ? parts.join(' ') : 'No se han identificado bienes comunes con soporte documental o verificación del analista para incorporar al Entregable.';
        },
        isAutomaticSummary(text) {
            const value = String(text || '').trim();
            return value === '' || ['Para la tipología ', 'Bienes comunes esenciales:', 'Bienes comunes no esenciales', 'Áreas comunes de uso exclusivo:', 'Soporte operativo y técnico común:', 'Se verifican ', 'Quedan por confirmar ', 'Se registran alertas o salvedades en ', 'Los bienes comunes específicos deben confirmarse', 'No se han marcado bienes comunes verificados', 'No se han identificado bienes comunes', 'Bienes comunes y soporte:'].some(prefix => value.startsWith(prefix));
        },
        refreshSummary() { this.summary = this.generatedSummary(); this.$nextTick(() => this.$root.querySelector('[data-common-summary]')?.dispatchEvent(new Event('input', { bubbles: true }))); },
        updateCommonStatus(key, value) { this.statuses[key] = value; this.refreshSummary(); },
        updateCommonNotes(key, value) { this.notes[key] = value; if (this.isAutomaticSummary(this.summary)) this.refreshSummary(); },
        toggleGroup(key) { this.openGroups[key] = !this.openGroups[key]; },
        isOpen(key) { return this.openGroups[key] !== false; },
        readyCount(keys) { return keys.filter(key => ['ok', 'warn', 'risk'].includes(this.statuses[key])).length; },
        applicableCount(keys) { return keys.filter(key => this.statuses[key] !== 'na').length; },
        effectFor(key) { return this.statusEffects[this.statuses[key] || ''] || this.statusEffects['']; },
    };
}
