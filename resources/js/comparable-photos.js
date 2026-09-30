import { flushModuleAutosaves } from './module-autosave.js';

export function comparablePhotos() {
    let form;
    return {
        initPhotos(element) { form = element; },
        photoOpen: false, photoBusy: false, photoMessage: '', photoTitle: '', photoEndpoint: '', photos: [], photoCaption: '',
        async openPhotos(index) {
            if (this.photoBusy) return;
            const row = form.querySelectorAll('.comparable-grid tbody tr')[index];
            const value = key => row.querySelector(`[name$="[${key}]"]`)?.value || '';
            this.photoOpen = true; this.photos = []; this.photoCaption = '';
            this.$refs.photoFile.value = '';
            this.photoTitle = `Muestra ${index + 1}: ${value('project_name') || value('source_name') || 'sin datos'}`;
            this.photoEndpoint = `${this.$refs.photoPanel.dataset.base}/${value('id')}/fotos`;
            this.photoBusy = true; this.photoMessage = 'Confirmando guardado y cargando fotos…';
            this.$nextTick(() => this.$refs.photoPanel.scrollIntoView({ block: 'start' }));
            try {
                if (!await flushModuleAutosaves()) throw new Error('La matriz tiene cambios sin guardar. Reintenta Guardar ahora antes de cargar fotos.');
                const data = await this.photoRequest();
                this.photos = data.photos; this.photoMessage = this.photos.length ? 'Fotos guardadas de este inmueble.' : 'Todavía no hay fotos para este inmueble.';
            } catch (error) { this.photoMessage = error.message; }
            finally { this.photoBusy = false; }
        },
        async photoRequest(options = {}) {
            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(), 30000);
            try {
                const response = await fetch(this.photoEndpoint, { ...options, signal: controller.signal, headers: { Accept: 'application/json' } });
                if (!response.headers.get('content-type')?.includes('application/json')) throw new Error('No se pudo confirmar la operación. Revisa la sesión y vuelve a intentar.');
                const data = await response.json();
                if (!response.ok || !data.ok) throw new Error(data.message || 'No se pudo acceder a las fotos.');
                return data;
            } catch (error) {
                if (error.name === 'AbortError') throw new Error('La carga tardó demasiado. Reintenta: una foto idéntica no se duplica.');
                throw error;
            } finally { clearTimeout(timer); }
        },
        async uploadPhoto() {
            if (this.photoBusy) return;
            const file = this.$refs.photoFile.files[0];
            if (!file || file.size > 5 * 1024 * 1024) { this.photoMessage = 'Selecciona una foto de hasta 5 MB.'; return; }
            this.photoBusy = true; this.photoMessage = 'Subiendo foto…';
            try {
                const body = new FormData();
                body.set('photo', file); body.set('caption', this.photoCaption);
                body.set('_token', form.querySelector('[name="_token"]')?.value || '');
                const data = await this.photoRequest({ method: 'POST', body });
                this.photos = data.photos; this.photoMessage = 'Foto guardada y vinculada a este inmueble.';
                this.$refs.photoFile.value = ''; this.photoCaption = '';
            } catch (error) { this.photoMessage = error.message; }
            finally { this.photoBusy = false; }
        },
    };
}
