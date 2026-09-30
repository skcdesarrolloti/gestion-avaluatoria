<section x-show="photoOpen" x-cloak x-ref="photoPanel" @input.stop @change.stop
    data-base="<?= e(url('avaluos/' . $record['id'] . '/comparables')) ?>"
    class="mt-4 scroll-mt-6 rounded-xl border border-teal-200 bg-teal-50 p-4" aria-label="Fotos del comparable">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h4 class="font-semibold" x-text="photoTitle"></h4>
        <button type="button" class="btn-secondary min-h-11" @click="photoOpen = false" :disabled="photoBusy">Cerrar fotos</button>
    </div>
    <p class="mt-2 text-sm">Abre el aviso, guarda la foto o una captura en tu equipo y cárgala aquí. Se vincula únicamente a esta muestra; el lector del portal no descarga fotos automáticamente.</p>
    <label for="comparable-photo-file" class="label mt-3">Foto del inmueble · JPG, PNG o WEBP, máximo 5 MB</label>
    <input id="comparable-photo-file" type="file" accept="image/jpeg,image/png,image/webp" x-ref="photoFile" :disabled="photoBusy" class="input min-h-11" aria-describedby="comparable-photo-message">
    <label for="comparable-photo-caption" class="label mt-3">Descripción y procedencia de la foto</label>
    <input id="comparable-photo-caption" class="input" x-model="photoCaption" maxlength="300" :disabled="photoBusy" placeholder="Ej.: recepción, captura del aviso consultado el 30/09/2026">
    <button type="button" class="btn-primary mt-3 min-h-11" @click="uploadPhoto()" :disabled="photoBusy">Subir foto</button>
    <p id="comparable-photo-message" role="status" class="mt-2 text-sm" x-text="photoMessage"></p>
    <div class="mt-3 grid gap-3 sm:grid-cols-3">
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
