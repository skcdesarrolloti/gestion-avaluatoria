<div class="md:col-span-2 rounded-xl border border-teal-200 bg-teal-50 p-4"
    x-data="locationPhotos(<?= e(json_encode($locationPhotos ?? [], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>)"
    data-upload-url="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto/fotos')) ?>">
    <h4 class="font-semibold">Fotos y mapas de localización · 1.4</h4>
    <noscript><a class="btn-secondary" href="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto#fotos')) ?>">Abrir carga de fotos del expediente</a></noscript>
    <p class="my-2 text-sm">Elige una imagen o pega una captura y pulsa «Guardar foto». Se conserva en este expediente para el analista y el titular.</p>
    <label class="label">Nombre de la imagen
        <input class="input" x-model="name" maxlength="190" placeholder="Ej. Mapa de localización o fachada principal">
    </label>
    <label class="label mt-3">Archivo de imagen
        <input class="input" type="file" accept="image/jpeg,image/png,image/webp" x-ref="photos" :disabled="busy" @change="update($event.target)">
        <span class="text-xs">JPG, PNG o WebP; máximo 12 MB. Una imagen por carga.</span>
    </label>
    <div class="my-3 min-h-11 rounded-lg border bg-white p-3 text-sm" tabindex="0" role="group" aria-label="Pegar imagen de localización" @paste="if (!busy) paste($event)">Pega aquí una imagen con Ctrl+V.</div>
    <p class="text-sm" x-text="fileNames"></p>
    <div class="flex flex-wrap gap-2"><template x-for="preview in previews" :key="preview.url"><img class="size-24 object-contain" :src="preview.url" :alt="preview.name"></template></div>
    <button type="button" class="btn-primary my-3" :disabled="busy || !hasFiles" @click="save()" x-text="busy ? 'Guardando foto...' : 'Guardar foto'">Guardar foto</button>
    <p role="status" aria-live="polite" class="text-sm font-semibold" x-text="status"></p>
    <div class="mt-3 grid gap-3 sm:grid-cols-2">
        <template x-for="photo in saved" :key="photo.id">
            <a class="rounded-lg border bg-white p-3" :href="photo.url" target="_blank" rel="noopener">
                <img class="h-40 w-full object-contain" :src="photo.url" :alt="photo.name">
                <span class="block text-sm" x-text="photo.name"></span><span class="text-xs text-teal-800">Guardada · abrir imagen</span>
            </a>
        </template>
    </div>
</div>
