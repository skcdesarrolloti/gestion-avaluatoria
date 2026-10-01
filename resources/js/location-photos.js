import { photoUpload } from './photo-upload.js';

export function locationPhotos(saved = []) {
    return {
        ...photoUpload(), saved, name: '', status: '',
        async save() {
            if (this.busy || !this.hasFiles) return;
            this.busy = true;
            this.status = 'Guardando datos del expediente...';
            try {
                if (window.gaFlushAutosaves && !await window.gaFlushAutosaves()) {
                    throw new Error('Primero resuelve el guardado pendiente del expediente y vuelve a intentar.');
                }
                const body = new FormData();
                body.set('_token', document.querySelector('meta[name="csrf-token"]')?.content ?? '');
                body.set('photo_caption', 'location:chapter-one');
                body.set('photo_name', this.name);
                Array.from(this.$refs.photos.files ?? []).forEach(file => body.append('photos[]', file));
                this.status = 'Subiendo imagen; espera la confirmación...';
                const response = await fetch(this.$el.dataset.uploadUrl, { method: 'POST', body, headers: { Accept: 'application/json' } });
                const result = await response.json().catch(() => ({ message: 'El servidor no confirmó la carga. Revisa la sesión y el tamaño del archivo.' }));
                if (!response.ok || !result.ok) throw new Error(result.message || 'No se pudo guardar la imagen.');
                this.saved = result.photos;
                this.status = result.message;
                this.$refs.photos.value = '';
                this.update(this.$refs.photos);
            } catch (error) {
                this.status = `No se confirmó el guardado: ${error.message}. Puedes reintentar.`;
            } finally { this.busy = false; }
        },
    };
}
