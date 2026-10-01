<section x-show="photoOpen" x-cloak x-ref="photoPanel" @input.stop @change.stop
    data-base="<?= e(url('avaluos/' . $record['id'] . '/comparables')) ?>"
    class="mt-4 scroll-mt-6 rounded-xl border border-teal-200 bg-teal-50 p-4" aria-label="Fotos del comparable">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h4 class="font-semibold" x-text="photoTitle"></h4>
        <button type="button" class="btn-secondary min-h-11" @click="photoOpen = false" :disabled="photoBusy || mapBusy || photoRetry">Cerrar fotos</button>
    </div>
    <a x-show="photoSourceUrl" :href="photoSourceUrl || '#'" target="_blank" rel="noopener" class="btn-secondary mt-3 min-h-11">1. Abrir aviso de esta muestra</a>
    <p x-show="!photoSourceUrl" class="mt-2 text-sm">Completa el enlace del inmueble en la matriz para abrir su aviso desde aquí.</p>
    <p class="mt-2 text-sm">En el aviso: clic derecho sobre la foto → «Copiar imagen». Regresa, haz clic en el recuadro y pulsa Ctrl+V. La foto se sube directamente a esta muestra y aparece abajo al confirmar el guardado.</p>
    <p class="mt-2 text-sm">Soporte de la investigación: agrega también una captura del aviso donde se vean URL, precio, áreas y ubicación; registra la fecha de consulta en la matriz. Una foto sola no reemplaza esa evidencia (arts. 14 y 17, anexo 2.1).</p>
    <div tabindex="0" role="region" aria-label="Pegar foto de esta muestra" @paste.prevent.stop="pastePhoto($event)"
        :aria-disabled="photoBusy || !photoReady" :aria-busy="photoBusy"
        class="mt-3 cursor-text rounded-lg border-2 border-dashed border-teal-600 bg-white p-4 focus:outline-2 focus:outline-teal-800">
        <strong>2. Haz clic aquí y pega la foto con Ctrl+V</strong>
        <p class="mt-1 text-sm">También admite una captura copiada. Una imagen JPG, PNG o WEBP por vez, máximo 5 MB. Pegar la dirección de la imagen no sube la foto.</p>
    </div>
    <button type="button" x-show="photoRetry" class="btn-primary mt-3 min-h-11" @click="retryPhoto()" :disabled="photoBusy">Reintentar foto pendiente</button>
    <label for="comparable-photo-caption" class="label mt-3">Descripción opcional · escríbela antes de pegar</label>
    <input id="comparable-photo-caption" class="input" x-model="photoCaption" maxlength="300" :disabled="photoBusy" placeholder="Ej.: recepción, foto del aviso consultado hoy">
    <details class="mt-3">
    <summary class="min-h-11 cursor-pointer py-3 font-semibold">Alternativa: elegir una foto guardada en el equipo</summary>
    <label for="comparable-photo-file" class="label mt-3">Foto del inmueble · JPG, PNG o WEBP, máximo 5 MB</label>
    <input id="comparable-photo-file" type="file" accept="image/jpeg,image/png,image/webp" x-ref="photoFile" :disabled="photoBusy" class="input min-h-11" aria-describedby="comparable-photo-message">
    <button type="button" class="btn-primary mt-3 min-h-11" @click="uploadPhoto()" :disabled="photoBusy || !photoReady">Subir archivo elegido</button>
    </details>
    <p id="comparable-photo-message" role="status" class="mt-2 text-sm" x-text="photoMessage"></p>
    <div class="mt-3 flex flex-wrap items-center gap-3" x-show="searchTab === 'mapa'">
        <button type="button" class="btn-secondary" @click="moveMap(-1, true)" :disabled="page <= 1 || photoBusy || photoRetry || mapBusy">Muestra anterior</button>
        <button type="button" class="btn-primary" @click="moveMap(1, true)" :disabled="page >= pages || photoBusy || photoRetry || mapBusy">Continuar con la siguiente muestra</button>
        <span x-show="photoReady" class="text-sm" x-text="photos.length + ' fotos guardadas en esta muestra'"></span>
        <p class="w-full text-sm">Puedes pegar varias fotos antes de continuar. La siguiente muestra se abre al confirmar el guardado; si una foto falla, reinténtala antes de avanzar.</p>
    </div>
    <div class="mt-3 grid max-h-80 gap-3 overflow-y-auto sm:grid-cols-3">
        <template x-for="photo in photos" :key="photo.id">
            <figure class="rounded-lg border border-slate-200 bg-white p-2">
                <a :href="photoEndpoint + '/' + photo.id" target="_blank" rel="noopener" data-no-fetch>
                    <img :src="photoEndpoint + '/' + photo.id" :alt="photo.caption || photo.source_filename" class="h-40 w-full object-contain" loading="lazy">
                </a>
                <figcaption class="mt-2 break-words text-xs" x-text="photo.caption || photo.source_filename"></figcaption>
            </figure>
        </template>
    </div>
</section>
