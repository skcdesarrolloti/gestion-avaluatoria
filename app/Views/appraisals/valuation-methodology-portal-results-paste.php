<section class="mt-4 rounded-lg border border-teal-100 bg-teal-50 p-3" x-data="<?= e($pasteComponent) ?>"
    data-detail-endpoint="<?= !empty($record['id']) ? e(url('avaluos/' . $record['id'] . '/metodologia-valuatoria/comparables/leer-aviso')) : '' ?>"
    data-city="<?= e($sourceSearch['city'] ?? $guide['source_search']['city'] ?? $record['municipio'] ?? '') ?>" data-query="<?= e($baseQuery) ?>" @input.stop @change.stop @comparable-matrix-changed.window="refresh()">
    <h4 class="font-semibold">Copiar y pegar desde <?= e($pasteLabel) ?></h4>
    <p class="mt-2 text-sm">En la fuente: <strong>Ctrl+A → Ctrl+C</strong>. Vuelve a esta ventana y pega con <strong>Ctrl+V</strong>. Revisa los avisos antes de incorporarlos.</p>
    <p class="mt-2 text-sm"><strong>1.</strong> Pega y revisa los avisos. <strong>2.</strong> Pulsa «Subir sin repetidos de este portal». Después consolida las muestras; todavía no se abren sus fichas.</p>
    <p role="status" x-ref="pasteFeedback" x-show="message" x-cloak class="mt-3 rounded-lg border bg-white p-3 text-sm font-semibold" x-text="message"></p>
    <label for="source-results-<?= (int) $sourceIndex ?>" class="mt-3 block text-sm font-semibold">Pega la página de resultados</label>
    <textarea id="source-results-<?= (int) $sourceIndex ?>" x-ref="pasteInput" class="input mt-1 min-h-24 w-full bg-white" :value="pastedText"
        :disabled="busy" @paste="paste($event); $nextTick(() => $refs.pasteFeedback.scrollIntoView({ block: 'center', behavior: 'smooth' }))"
        placeholder="Haz clic aquí y pulsa Ctrl+V para preparar todos los avisos copiados." aria-describedby="source-help-<?= (int) $sourceIndex ?>"></textarea>
    <p id="source-help-<?= (int) $sourceIndex ?>" class="mt-2 text-xs"><?= !empty($pasteGeneral) ? 'Si el listado no se reconoce, pega el enlace y texto de una ficha; separa las fichas con una línea vacía.' : 'Este lector reconoce oficinas en venta de la ciudad del expediente.' ?> Solo se conserva lo publicado. Los datos faltantes quedan pendientes.</p>
    <p class="mt-2 text-sm">Cada página se agrega a las muestras anteriores al pulsar «Subir sin repetidos». Pegar solo prepara el lote; no lo guarda.</p>
    <button type="button" class="btn-secondary mt-2 min-h-11" :disabled="busy" @click="nextPage()">Pegar otra página</button>
    <div x-show="results.length" x-cloak class="mt-3">
        <p class="mb-3 text-sm font-semibold" x-text="results.length + ' avisos leídos en esta página · ' + suggestedCount + ' sugeridos para agregar · ' + registeredCount + ' ya registrados · ' + reviewCount + ' posibles coincidencias omitidas del lote sugerido'"></p>
        <div class="flex flex-wrap gap-2">

            <button type="button" class="btn-primary min-h-11" :disabled="busy || !suggestedCount" @click="addSuggested()" x-text="'Subir sin repetidos de este portal (' + suggestedCount + ')'">Subir sin repetidos de este portal</button>
            <button type="button" class="btn-secondary min-h-11" @click="intakeNavigate('review')">Revisar incorporados</button>
        </div>
        <p class="mt-2 text-xs">Verde: nuevo sin coincidencias detectadas. Gris: ya registrado. Amarillo: revisar posible repetido. El botón sube los verdes y guarda lo recogido de este portal.</p>
        <details class="mt-3"><summary class="min-h-11 cursor-pointer py-3 text-sm font-semibold">Selección manual y complementar avisos existentes</summary>
        <button type="button" class="btn-secondary min-h-11" @click="captureAll()">Recoger nuevos y complementar existentes</button>
        <div class="flex flex-wrap gap-2">
            <button type="button" class="btn-secondary min-h-11" @click="selectAll()">Seleccionar todos los disponibles</button>
            <button type="button" class="btn-secondary min-h-11" @click="selected = []">Desmarcar todos</button>
            <button type="button" class="btn-primary min-h-11" :disabled="busy || !selected.length" @click="add()" x-text="'Agregar seleccionados (' + selected.length + ')'">Agregar seleccionados</button>
        </div>
        <p class="mt-2 text-xs">Gris: ya registrado. Amarillo: posible coincidencia; puedes incorporarlo como por verificar y revisarlo después en la matriz. No se declara que sea otro inmueble.</p>
        </details>
        <div class="mt-3 grid gap-3 md:grid-cols-2">
            <template x-for="item in results" :key="item.row.source_url">
                <article class="rounded-lg border p-3" :class="item.tone === 'registered' ? 'bg-slate-100' : (item.tone === 'review' ? 'bg-amber-50' : 'bg-emerald-50')">
                    <label class="flex min-h-11 items-center gap-2 font-semibold"><input type="checkbox" x-model="selected" :value="item.row.source_url" :disabled="item.tone === 'registered' || busy"><span x-text="'Aviso ' + item.number + ' · ' + (item.row.listing_title || item.row.neighborhood || item.row.project_name || item.row.property_type || 'Ubicación por verificar')"></span></label>
                    <?php require __DIR__ . '/methodology-preview-facts.php'; ?>
                    <p class="mt-2 text-sm font-semibold" x-show="item.detailState" x-text="item.detailState"></p>
                    <p class="mt-1 text-xs" x-text="item.label"></p>
                    <a class="mt-1 inline-flex min-h-11 items-center text-sm text-blue-700 underline" :href="item.row.source_url" target="_blank" rel="noopener">Ver inmueble</a>
                </article>
            </template>
        </div>
        <div class="sticky bottom-0 mt-3 flex flex-wrap items-center gap-3 rounded-lg border bg-white p-3 shadow-sm">
            <button type="button" class="btn-primary min-h-11" :disabled="busy || !suggestedCount" @click="addSuggested()" x-text="busy ? 'Guardando…' : 'Subir sin repetidos de este portal (' + suggestedCount + ')'">Subir sin repetidos de este portal</button>
            <span class="text-sm" x-text="registeredCount + ' ya registrados · ' + reviewCount + ' por revisar'"></span>
        </div>
    </div>
</section>
