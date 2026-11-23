import { photoUpload } from './photo-upload.js';
import { refreshCsrf, isLoginRedirect } from './session-refresh.js';
import { requestDeadline } from './request-deadline.js';

export function locationPhotos(saved = []) {
    const base = photoUpload();
    return {
        ...base, saved, name: '', status: '', uploadUrl: '',
        init() {
            // $el is the clicked button during save(); $root is the upload panel.
            const panel = this.$root;
            this.uploadUrl = panel.dataset.uploadUrl;
            this.guardLeave = event => {
                if (!panel.isConnected || (!this.hasFiles && !this.busy)) return;
                if (event.type === 'beforeunload') { event.preventDefault(); event.returnValue = ''; return; }
                const link = event.target.closest?.('a[href]');
                if (!link || link.target || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
                const next = new URL(link.href), current = new URL(window.location.href);
                if (next.pathname === current.pathname && next.search === current.search && next.hash) return;
                event.preventDefault(); event.stopImmediatePropagation();
                this.status = 'Imagen pendiente: pulsa Guardar foto y espera la confirmación antes de continuar.';
                panel.scrollIntoView({ block: 'center' });
            };
            document.addEventListener('click', this.guardLeave, true);
            window.addEventListener('beforeunload', this.guardLeave);
        },
        update(input) {
            base.update.call(this, input);
            if (this.hasFiles) this.status = 'Imagen pendiente de guardar. Pulsa Guardar foto.';
        },
        clearSelection() {
            if (this.busy) return;
            this.$refs.photos.value = '';
            this.update(this.$refs.photos);
            this.status = 'Selección retirada. Las imágenes guardadas se conservan.';
        },
        async save() {
            if (this.busy || !this.hasFiles) return;
            this.busy = true;
            this.status = 'Guardando datos del expediente...';
            try {
                if (!this.uploadUrl) throw new Error('No se encontró el destino de la imagen');
                if (window.gaFlushAutosaves && !await window.gaFlushAutosaves()) {
                    throw new Error('Primero resuelve el guardado pendiente del expediente y vuelve a intentar.');
                }
                const body = new FormData();
                body.set('_token', document.querySelector('meta[name="csrf-token"]')?.content ?? '');
                body.set('photo_caption', 'location:chapter-one');
                body.set('photo_name', this.name);
                Array.from(this.$refs.photos.files ?? []).forEach(file => body.append('photos[]', file));
                this.status = 'Subiendo imagen; espera la confirmación...';
                const controller = new AbortController();
                const { response, result } = await requestDeadline(async () => {
                    const send = () => fetch(this.uploadUrl, { method: 'POST', body, signal: controller.signal,
                        credentials: 'same-origin', headers: { Accept: 'application/json' } });
                    let response = await send();
                    if (response.status === 419 && await refreshCsrf(controller.signal)) {
                        body.set('_token', document.querySelector('meta[name="csrf-token"]').content);
                        response = await send();
                    }
                    if ([401, 419].includes(response.status) || (response.url && isLoginRedirect(response.url))) {
                        throw new Error('La sesión venció. Inicia sesión en otra pestaña y reintenta aquí sin recargar');
                    }
                    const result = await response.json().catch(() => ({ message: 'El servidor no confirmó la carga. Revisa la sesión y el tamaño del archivo.' }));
                    return { response, result };
                }, controller);
                if (!response.ok || !result.ok) throw new Error(result.message || 'No se pudo guardar la imagen.');
                if (!Array.isArray(result.photos) || !result.photos.some(photo => photo.id && photo.url && !this.saved.some(old => old.id === photo.id))) {
                    throw new Error('El servidor no devolvió la imagen guardada en este apartado');
                }
                this.saved = result.photos;
                this.status = result.message;
                this.$refs.photos.value = '';
                this.update(this.$refs.photos);
            } catch (error) {
                this.status = `No se confirmó el guardado: ${error.message}. Puedes reintentar.`;
            } finally { this.busy = false; }
        },
        destroy() {
            document.removeEventListener('click', this.guardLeave, true);
            window.removeEventListener('beforeunload', this.guardLeave);
            base.destroy.call(this);
        },
    };
}
