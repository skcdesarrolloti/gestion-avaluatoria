const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

export function legalAutosave() {
    return {
        endpoint: '', dirty: false, saving: false, message: 'Autoguardado activo', savedAt: '', timer: null, controller: null,
        init() {
            this.endpoint = this.$el.dataset.autosaveEndpoint || '';
            this.beforeLeave = event => {
                if (!this.dirty && !this.saving) return;
                event.preventDefault();
                event.returnValue = '';
            };
            window.addEventListener('beforeunload', this.beforeLeave);
        },
        changed() {
            if (!this.endpoint) return;
            this.dirty = true;
            this.message = 'Cambios pendientes';
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.save(), 800);
        },
        async save() {
            if (!this.endpoint || this.saving || !this.dirty) return;
            const body = new FormData(this.$el);
            this.saving = true;
            this.message = 'Guardando...';
            this.controller = new AbortController();
            try {
                const response = await fetch(this.endpoint, {
                    method: 'POST',
                    body,
                    headers: { Accept: 'application/json', 'X-CSRF-Token': csrfToken() },
                    credentials: 'same-origin',
                    signal: this.controller.signal,
                });
                const result = await response.json();
                if (!response.ok || result.ok !== true) throw new Error(result.message || 'No se pudo guardar.');
                this.dirty = false;
                this.message = 'Todos los cambios guardados';
                this.savedAt = 'Último guardado: ' + new Date(result.saved_at).toLocaleTimeString('es-CO');
            } catch (error) {
                if (error.name !== 'AbortError') {
                    this.dirty = true;
                    this.message = 'Pendiente de guardar';
                }
            } finally {
                this.saving = false;
                this.controller = null;
            }
        },
        cancel() {
            clearTimeout(this.timer);
            this.controller?.abort();
            window.removeEventListener('beforeunload', this.beforeLeave);
        },
    };
}
