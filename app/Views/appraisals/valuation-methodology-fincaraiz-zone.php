<?php
$zoneCity = mb_strtolower(trim((string) ($sourceSearch['city'] ?? $guide['source_search']['city'] ?? $record['municipio'] ?? '')));
$zoneSupported = ($record['tipo_inmueble'] ?? '') === 'oficina' && ($record['tipo_negocio'] ?? '') === 'venta'
    && in_array($zoneCity, ['cartagena', 'cartagena de indias'], true);
$zoneNeighborhood = (string) ($sourceSearch['neighborhood'] ?? $guide['source_search']['neighborhood'] ?? '');
?>
<?php if ($zoneSupported): ?>
<section x-data="fincaraizAreaSearch" data-neighborhood="<?= e($zoneNeighborhood) ?>"
    data-endpoint="<?= e(url('avaluos/' . $record['id'] . '/metodologia-valuatoria/comparables/buscar-zona')) ?>"
    @input.stop @change.stop :aria-busy="busy">
    <h4 class="font-semibold">FincaRaíz · Oficinas en venta · Cartagena</h4>
    <label for="fincaraiz-neighborhood" class="mt-3 block text-sm font-semibold">Barrio donde buscar</label>
    <input id="fincaraiz-neighborhood" class="input mt-1 w-full" placeholder="Ejemplo: Bocagrande" x-model="neighborhood" :disabled="busy" @input="clear()" @keydown.enter.prevent="search(1)" aria-describedby="fincaraiz-zone-help">
    <p id="fincaraiz-zone-help" class="mt-1 text-xs text-slate-600">Se toma del expediente; puedes cambiarlo solo para esta búsqueda. No modifica el inmueble avaluado.</p>
    <div class="mt-3 flex flex-wrap gap-2">
        <button type="button" class="btn-primary min-h-11" @click="search(1)" :disabled="busy">Buscar oficinas del barrio</button>
        <a class="btn-secondary min-h-11" :href="searchUrl" href="<?= e($source['url']) ?>" target="_blank" rel="noopener">Ver en FincaRaíz</a>
    </div>
    <p role="status" class="mt-3 text-sm" x-text="message"></p>
    <div x-show="results.length" x-cloak class="mt-3">
        <div class="flex flex-wrap items-center gap-3">
            <button type="button" class="btn-secondary min-h-11" @click="selected = results.map(item => item.row.source_url)">Marcar esta página</button>
            <button type="button" class="btn-secondary min-h-11" @click="selected = []" :disabled="!selected.length">Quitar selección</button>
            <button type="button" class="btn-primary min-h-11" @click="incorporate()" :disabled="!selected.length || busy">Agregar seleccionados (<span x-text="selected.length"></span>)</button>
        </div>
        <div class="mt-3 grid gap-2 sm:grid-cols-2">
            <template x-for="item in results" :key="item.row.source_url">
                <article class="rounded-lg border border-slate-200 p-3">
                    <label class="flex min-h-11 items-center gap-2 font-semibold"><input type="checkbox" :value="item.row.source_url" x-model="selected"><span x-text="item.title"></span></label>
                    <p class="text-sm"><span x-text="money(item.row.price_amount)"></span> · <span x-text="item.row.area_m2 || 'Área pendiente'"></span> m²</p>
                    <p class="text-sm text-slate-600" x-text="item.row.address_hint || 'Dirección pendiente'"></p>
                    <a class="inline-flex min-h-11 items-center text-sm font-semibold text-blue-800" :href="item.row.source_url" target="_blank" rel="noopener">Ver ficha y comprobar ubicación</a>
                </article>
            </template>
        </div>
        <div class="mt-3 flex flex-wrap items-center gap-3">
            <button type="button" class="btn-secondary min-h-11" @click="search(page - 1)" :disabled="busy || page <= 1 || selected.length > 0">Anterior</button>
            <span>Página <span x-text="page"></span></span>
            <button type="button" class="btn-secondary min-h-11" @click="search(page + 1)" :disabled="busy || !hasNext || selected.length > 0">Siguiente página</button>
        </div>
        <p class="mt-2 text-xs">Agrega lo seleccionado antes de cambiar de página. Los resúmenes pueden diferir de la ficha; quedan por verificar y no adjuntan fotos ni PDF.</p>
    </div>
</section>
<?php endif; ?>
<details class="mt-4" <?= !$zoneSupported ? 'open' : '' ?>>
    <summary class="min-h-11 cursor-pointer py-3 text-sm font-semibold">Capturar un enlace individual o pegar texto</summary>
    <?php if (!$zoneSupported): ?><a href="<?= e($source['url']) ?>" target="_blank" rel="noopener" class="btn-secondary min-h-11">Abrir búsqueda en FincaRaíz</a><?php endif; ?>
    <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-comparable-url.php'; ?>
    <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-source-paste.php'; ?>
</details>

