<section class="mt-5 rounded-xl border border-blue-200 bg-blue-50 p-4" data-listing-reader
    data-default-query="<?= e((string) ($baseQuery ?? '')) ?>"
    data-endpoint="<?= e(url('avaluos/' . $record['id'] . '/metodologia-valuatoria/comparables/leer-aviso')) ?>">
    <h4 class="font-semibold text-blue-950">2. Pega aquí el enlace del inmueble · FincaRaíz</h4>
    <p class="mt-2 text-sm text-blue-950">Lee el aviso, revisa los datos y pulsa «Agregar a la tabla». Después pega el siguiente enlace: cada aviso agrega otra muestra, sin reemplazar las anteriores.</p>
    <label for="listing-reader-url" class="mt-3 block text-sm font-semibold">Enlace del inmueble (no de los resultados)</label>
    <input id="listing-reader-url" type="url" class="input mt-1 w-full bg-white" data-listing-url @input.stop @change.stop
        placeholder="https://www.fincaraiz.com.co/casa-en-venta-en-sector-ciudad/123456789" aria-describedby="listing-reader-status">
    <div class="mt-3 flex flex-wrap gap-3">
        <button type="button" class="btn-primary min-h-11" data-listing-read>Leer aviso</button>
        <button type="button" class="btn-secondary min-h-11" data-listing-add disabled>Agregar a la tabla como por verificar</button>
        <a class="btn-secondary min-h-11" data-listing-next href="<?= e($source['url'] ?? 'https://www.fincaraiz.com.co/') ?>" target="_blank" rel="noopener">Buscar otro inmueble</a>
    </div>
    <p id="listing-reader-status" role="status" class="mt-3 text-sm leading-6" data-listing-message></p>
    <p class="mt-2 text-xs">«Buscar otro inmueble» limpia esta captura y abre la búsqueda. No borra las filas de la tabla. Las coincidencias entre fuentes requieren revisión; pueden existir duplicados que los datos publicados no permitan detectar.</p>
    <div class="text-anywhere mt-3 space-y-1 text-sm" data-listing-preview></div>
    <p class="mt-2 text-xs text-slate-600">La lectura prellena los datos publicados que reconoce. No confirma comparabilidad ni adjunta fotografías o PDF. Precio y clase de área deben revisarse.</p>
</section>
