<script>
window.subjectMidasUpdater = window.subjectMidasUpdater || function(endpoint) {
    return {
        busy: false, step: '', message: '', error: '', unmapped: [],
        updated: {subject: {count: 0, fields: []}, urban: {count: 0, fields: []}},
        async actualizar(form) {
            if (!form || this.busy) return;
            this.busy = true; this.step = 'subject'; this.message = ''; this.error = ''; this.unmapped = [];
            this.updated = {subject: {count: 0, fields: []}, urban: {count: 0, fields: []}};
            try {
                await this.pause(300);
                this.step = 'urban';
                const response = await fetch(endpoint, {
                    method: 'POST',
                    body: new FormData(form),
                    credentials: 'same-origin',
                    headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok || !payload.ok) throw new Error(payload.message || 'No se pudo actualizar MIDAS.');
                this.step = 'done';
                this.message = payload.message || 'Actualización MIDAS finalizada.';
                this.updated = payload.updated || this.updated;
                this.unmapped = Array.isArray(payload.unmapped) ? payload.unmapped : [];
                this.syncUnmapped(form);
            } catch (error) {
                this.error = error.message || 'No se pudo actualizar MIDAS.';
            } finally {
                this.busy = false;
            }
        },
        pause(ms) { return new Promise(resolve => setTimeout(resolve, ms)); },
        syncUnmapped(form) {
            const target = form.querySelector('[name="midas_unmapped_notes"]');
            if (!target) return;
            target.value = this.unmapped.map(item =>
                `${item.section || 'Dato MIDAS'} - ${item.label || ''}:\n${item.value || ''}`.trim()
            ).join('\n\n');
        }
    };
};
</script>
