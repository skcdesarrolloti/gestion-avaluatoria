export function appraisalForm() {
    return {
        fields: {}, version: 1, endpoint: '', errors: {}, savedAt: '',
        dirty: false, saving: false, blocked: false, message: 'Lista para editar',
        timer: null, acknowledged: '', beforeLeave: null, online: null,

        init() {
            const initial = JSON.parse(this.$el.dataset.initial);
            this.fields = initial.fields;
            this.version = initial.version;
            this.endpoint = initial.endpoint;
            this.acknowledged = JSON.stringify(this.fields);
            this.beforeLeave = (event) => {
                if (this.dirty || this.saving) {
                    event.preventDefault();
                    event.returnValue = '';
                }
            };
            this.online = () => { if (this.dirty && !this.blocked) this.save(); };
            window.addEventListener('beforeunload', this.beforeLeave);
            window.addEventListener('online', this.online);
        },

        changed() {
            // Alpine applies x-model before nextTick, including input/change ordering.
            this.$nextTick(() => {
                this.dirty = JSON.stringify(this.fields) !== this.acknowledged;
                clearTimeout(this.timer);
                if (this.blocked) return;
                this.message = this.dirty ? 'Cambios pendientes' : 'Sin cambios pendientes';
                if (this.dirty) this.timer = setTimeout(() => this.save(), 800);
            });
        },

        async save() {
            clearTimeout(this.timer);
            if (this.saving || this.blocked || !this.dirty) return;
            if (!navigator.onLine) {
                this.message = 'Sin conexión. Conserva esta pestaña abierta; reintentaremos al volver.';
                return;
            }
            const snapshot = JSON.stringify(this.fields);
            this.saving = true;
            this.message = 'Guardando…';
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 15000);
            let success = false;
            try {
                const response = await fetch(this.endpoint, {
                    method: 'POST', credentials: 'same-origin', signal: controller.signal,
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ ...JSON.parse(snapshot), version: this.version }),
                });
                const result = await response.json();
                if (!response.ok) {
                    this.errors = result.errors || {};
                    this.blocked = [401, 403, 404, 409, 419].includes(response.status);
                    throw new Error(result.message || 'No se pudo guardar. Intenta nuevamente.');
                }
                if (result.ok !== true || !Number.isInteger(result.version) || result.version !== this.version + 1) {
                    throw new Error('No recibimos una confirmación válida. Conserva tus cambios e intenta nuevamente.');
                }
                this.version = result.version;
                this.acknowledged = snapshot;
                this.dirty = JSON.stringify(this.fields) !== snapshot;
                this.errors = {};
                this.savedAt = 'Último guardado: ' + new Date(result.saved_at).toLocaleTimeString('es-CO');
                this.message = this.dirty ? 'Cambios pendientes' : 'Todos los cambios guardados';
                success = true;
            } catch (error) {
                this.dirty = true;
                this.message = error instanceof TypeError || error.name === 'AbortError'
                    ? 'No pudimos confirmar el guardado. Conserva esta pestaña y pulsa Guardar ahora para reintentar.'
                    : error.message;
            } finally {
                clearTimeout(timeout);
                this.saving = false;
                // Only serialize a following save after confirmed success; never overwrite conflicts.
                if (success && this.dirty) this.timer = setTimeout(() => this.save(), 800);
            }
        },

        destroy() {
            clearTimeout(this.timer);
            window.removeEventListener('beforeunload', this.beforeLeave);
            window.removeEventListener('online', this.online);
        },
    };
}
