import { flushModuleAutosaves } from './module-autosave.js';

export function comparableMapNavigation(save = flushModuleAutosaves) {
    return {
        mapIndex: null, mapBusy: false, mapMessage: '',
        async moveMap(step, withPhotos = false) {
            if (this.mapBusy || this.photoBusy || this.photoRetry || this.page + step < 1 || this.page + step > this.pages) return;
            this.mapBusy = true; this.mapMessage = 'Confirmando guardado antes de cambiar de muestra…';
            try {
                if (!await save()) throw new Error('No se pudo confirmar el guardado. Conserva esta muestra y pulsa Guardar ahora para reintentar.');
                this.photoOpen = false;
                this.page += step; this.render();
                this.mapMessage = 'Guardado confirmado. Puedes continuar con esta muestra.';
                if (withPhotos && this.mapIndex !== null) await this.openPhotos(this.mapIndex);
            } catch (error) { this.mapMessage = error.message; }
            finally { this.mapBusy = false; }
        },
    };
}
