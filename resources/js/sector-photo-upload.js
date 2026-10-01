import { photoUpload } from './photo-upload.js';
import { submitUpload } from './upload-progress.js';

export function sectorPhotoUpload() {
    const base = photoUpload();
    return {
        ...base,
        status: '',
        init() {
            const form = this.$root;
            this.guardNavigation = event => {
                if ((!this.busy && !this.hasFiles) || form.contains(event.target)) return;
                if (event.type === 'click' && !event.target.closest?.('a, button, input[type="file"]')) return;
                event.preventDefault(); event.stopImmediatePropagation();
                this.status = this.busy ? 'Espera: la imagen todavía se está guardando.' : 'La imagen sigue pendiente. Reintenta la carga antes de continuar.';
            };
            document.addEventListener('click', this.guardNavigation, true);
            document.addEventListener('submit', this.guardNavigation, true);
            this.beforeLeave = event => {
                if (!this.busy && !this.hasFiles) return;
                event.preventDefault(); event.returnValue = '';
            };
            window.addEventListener('beforeunload', this.beforeLeave);
        },
        update(input) {
            base.update.call(this, input);
            if (this.hasFiles) this.$nextTick(() => this.save());
        },
        async save() {
            if (this.busy || (!this.hasFiles && !this.url.trim())) return;
            this.busy = true;
            this.status = 'Guardando datos antes de subir la imagen...';
            try {
                if (window.gaFlushAutosaves && !await window.gaFlushAutosaves()) {
                    throw new Error('Resuelve el guardado pendiente antes de subir la imagen.');
                }
                this.status = 'Subiendo imagen. Espera la confirmación del servidor.';
                submitUpload(this.$root);
            } catch (error) {
                this.busy = false; this.status = error.message;
            }
        },
        failed() { this.busy = false; this.status = 'La imagen sigue pendiente. Revisa el error y pulsa Reintentar carga.'; },
        destroy() {
            document.removeEventListener('click', this.guardNavigation, true);
            document.removeEventListener('submit', this.guardNavigation, true);
            window.removeEventListener('beforeunload', this.beforeLeave);
            base.destroy.call(this);
        },
    };
}
