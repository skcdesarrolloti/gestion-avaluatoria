<?php
$zoneCity = mb_strtolower(trim((string) ($sourceSearch['city'] ?? $guide['source_search']['city'] ?? $record['municipio'] ?? '')));
$zoneSupported = ($record['tipo_inmueble'] ?? '') === 'oficina' && ($record['tipo_negocio'] ?? '') === 'venta'
    && in_array($zoneCity, ['cartagena', 'cartagena de indias'], true);
$zoneNeighborhood = (string) ($sourceSearch['neighborhood'] ?? $guide['source_search']['neighborhood'] ?? '');
?>
<?php if ($zoneSupported): ?>
<section x-data="fincaraizAreaSearch" data-neighborhood-id="<?= e((string) ($subject['neighborhood_id'] ?? '')) ?>"
    data-neighborhoods="<?= e(json_encode($marketNeighborhoods ?? [], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>"
    data-endpoint="<?= e(url('avaluos/' . $record['id'] . '/metodologia-valuatoria/comparables/buscar-zona')) ?>"
    @input.stop @change.stop :aria-busy="busy">
    <h4 class="font-semibold">FincaRaíz · Datos del expediente</h4>
    <div class="mt-2 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
        <label class="text-sm">Tipo de inmueble<input class="input mt-1" value="<?= e((string) ($guide['type_label'] ?? '')) ?>" readonly placeholder="Completa el tipo en el expediente"></label>
        <label class="text-sm">Operación<input class="input mt-1" value="<?= e((string) ($guide['business_label'] ?? '')) ?>" readonly placeholder="Completa la operación en el expediente"></label>
        <label class="text-sm">Ciudad<input class="input mt-1" value="<?= e((string) ($sourceSearch['city'] ?? $guide['source_search']['city'] ?? $record['municipio'] ?? '')) ?>" readonly placeholder="Completa la ciudad en el expediente"></label>
        <label class="text-sm">Propiedad horizontal (PH)<input class="input mt-1" value="<?= e(['si' => 'Sí', 'no' => 'No', 'no_aplica' => 'No aplica'][$record['regimen_ph'] ?? ''] ?? 'Por definir en el expediente') ?>" readonly placeholder="Completa PH en el expediente" aria-describedby="fincaraiz-subject-ph-help"></label>
    </div>
    <p id="fincaraiz-subject-ph-help" class="mt-2 text-xs text-slate-600">PH se toma del expediente del inmueble avaluado. El régimen de cada comparable se verifica por separado.</p>
    <label for="fincaraiz-neighborhood" class="mt-3 block text-sm font-semibold">Barrio donde buscar</label>
    <input id="fincaraiz-neighborhood" type="search" autocomplete="off" class="input mt-1 w-full" placeholder="Escribe para buscar en el catálogo: Boca…" x-model="neighborhood" :disabled="busy" @input="editNeighborhood()" @keydown.enter.prevent="search(1)" aria-describedby="fincaraiz-zone-help">
    <p id="fincaraiz-zone-help" class="mt-1 text-xs text-slate-600">Barrio inicial del expediente. Para cambiar la zona de búsqueda, escribe y selecciona una sugerencia de la base de datos; no modifica el inmueble avaluado.</p>
    <div x-show="!neighborhoodId" class="mt-2 flex flex-wrap gap-2" aria-label="Sugerencias de barrios">
        <template x-for="item in suggestions" :key="item.id"><button type="button" class="btn-secondary min-h-11" @click="choose(item)" :disabled="busy" x-text="item.name"></button></template>
        <p x-show="!suggestions.length" class="text-sm">No hay coincidencias activas en el catálogo de esta ciudad. Revisa el barrio en los datos del expediente.</p>
    </div>
    <p x-show="neighborhoodId" class="mt-1 text-xs text-teal-800">Barrio seleccionado del catálogo.</p>
    <div class="mt-3 flex flex-wrap gap-2">
        <button type="button" class="btn-primary min-h-11" @click="search(1)" :disabled="busy || !neighborhoodId">Buscar oficinas del barrio</button>
        <a x-show="neighborhoodId" class="btn-secondary min-h-11" :href="searchUrl" target="_blank" rel="noopener">Ver en FincaRaíz</a>
    </div>
    <p role="status" class="mt-3 text-sm" x-text="message"></p>
    <div x-show="results.length" x-cloak class="mt-3">
        <p class="mb-2 text-xs">PH del inmueble avaluado: <strong><?= e(['si' => 'Sí', 'no' => 'No', 'no_aplica' => 'No aplica'][$record['regimen_ph'] ?? ''] ?? 'Por verificar') ?></strong>. Clasifica las muestras por separado.</p>
        <label class="label" for="capture-ph-filter">Filtrar resultados por propiedad horizontal</label>
        <select id="capture-ph-filter" class="input mb-3" x-model="phFilter" @change="selected = []"><option value="all">Todos, incluidos por verificar</option><option value="si">Sí, PH</option><option value="no">No PH</option><option value="por_verificar">Por verificar</option></select>
        <p class="mb-3 text-xs">El portal no confirma PH en estos resúmenes. Verifica y clasifica cada aviso; este filtro actúa sobre los resultados cargados, no sobre todo el portal.</p>
        <p x-show="!visibleResults.length" class="mb-3 text-sm">No hay avisos clasificados con este régimen en la página. Revisa «Por verificar» o muestra todos.</p>
        <div class="flex flex-wrap items-center gap-3">
            <button type="button" class="btn-secondary min-h-11" @click="selected = visibleResults.map(item => item.row.source_url)">Seleccionar todos</button>
            <button type="button" class="btn-secondary min-h-11" @click="selected = []" :disabled="!selected.length">Desmarcar todos</button>
            <button type="button" class="btn-primary min-h-11" @click="incorporate()" :disabled="!selected.length || busy">Agregar seleccionados (<span x-text="selected.length"></span>)</button>
        </div>
        <p class="mt-2 text-xs text-slate-600">Selecciona todos los avisos visibles de esta página y desmarca las casillas de los que no quieras agregar.</p>
        <div class="mt-3 grid gap-2 sm:grid-cols-2">
            <template x-for="item in visibleResults" :key="item.row.source_url">
                <article class="rounded-lg border border-slate-200 p-3">
                    <label class="flex min-h-11 items-center gap-2 font-semibold"><input type="checkbox" :value="item.row.source_url" x-model="selected"><span x-text="item.title"></span></label>
                    <label class="mt-2 block text-xs font-semibold">Propiedad horizontal de este aviso
                        <select class="input min-h-11" x-model="item.row.ph_regime" @change="selected = selected.filter(url => visibleResults.some(result => result.row.source_url === url))"><option value="por_verificar">Por verificar</option><option value="si">Sí, PH</option><option value="no">No PH</option></select>
                    </label>
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

