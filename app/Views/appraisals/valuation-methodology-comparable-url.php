<section class="mt-5 rounded-xl border border-blue-200 bg-blue-50 p-4" data-listing-reader
    data-default-query="<?= e((string) ($baseQuery ?? '')) ?>"
    data-endpoint="<?= e(url('avaluos/' . $record['id'] . '/metodologia-valuatoria/comparables/leer-aviso')) ?>">
    <h4 class="font-semibold text-blue-950">Leer un aviso por enlace · FincaRaíz</h4>
    <p class="mt-2 text-sm text-blue-950">Abre un inmueble, copia su enlace y pégalo aquí. Pulsa «Leer aviso», revisa la vista previa y después incorpóralo. Para otros portales usa enlace y texto en la captura de abajo.</p>
    <label for="listing-reader-url" class="mt-3 block text-sm font-semibold">Enlace del inmueble (no de los resultados)</label>
    <input id="listing-reader-url" type="url" class="input mt-1 w-full bg-white" data-listing-url @input.stop @change.stop
        placeholder="https://www.fincaraiz.com.co/casa-en-venta-en-sector-ciudad/123456789" aria-describedby="listing-reader-status">
    <div class="mt-3 flex flex-wrap gap-3">
        <button type="button" class="btn-primary min-h-11" data-listing-read>Leer aviso</button>
        <button type="button" class="btn-secondary min-h-11" data-listing-add disabled>Incorporar como por verificar</button>
    </div>
    <p id="listing-reader-status" role="status" class="mt-3 text-sm leading-6" data-listing-message></p>
    <div class="text-anywhere mt-3 space-y-1 text-sm" data-listing-preview></div>
    <p class="mt-2 text-xs text-slate-600">La lectura prellena los datos publicados que reconoce. No confirma comparabilidad ni adjunta fotografías o PDF. Precio y clase de área deben revisarse.</p>
</section>
