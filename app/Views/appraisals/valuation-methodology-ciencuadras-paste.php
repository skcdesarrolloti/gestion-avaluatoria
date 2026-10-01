<section class="mt-4 rounded-lg border border-teal-100 bg-teal-50 p-3" x-data="ciencuadrasPaste"
    data-city="<?= e($sourceSearch['city'] ?? $guide['source_search']['city'] ?? $record['municipio'] ?? '') ?>" data-query="<?= e($baseQuery) ?>" @input.stop @change.stop>
    <h4 class="font-semibold">Traer varios inmuebles en 3 pasos</h4>
    <ol class="mt-3 list-decimal space-y-3 pl-6 text-sm leading-6" aria-label="Pasos para capturar Ciencuadras">
        <li><strong>Busca y copia en Ciencuadras.</strong> Usa «Abrir búsqueda en Ciencuadras», arriba. En el portal, pulsa <strong>Enter en el campo del barrio</strong>. Espera a que aparezcan las oficinas de esa zona, haz clic en el título de la página y pulsa <strong>Ctrl+A</strong> (seleccionar la página) y <strong>Ctrl+C</strong> (copiar).</li>
        <li><strong>Vuelve aquí y pega.</strong> Haz clic en «Pega la página de resultados», debajo de esta guía, y pulsa <strong>Ctrl+V</strong>. Aparecerán las tarjetas de los inmuebles; todavía no se agregan a la matriz.</li>
        <li><strong>Selecciona y agrega.</strong> Pulsa «Seleccionar todos los disponibles», desmarca los que no quieras y pulsa «Agregar seleccionados». Comprueba el estado de guardado. Para traer otra página, repite estos pasos.</li>
    </ol>
    <p class="mt-3 text-sm font-semibold">Copia la página de resultados completa, no solo su dirección. No necesitas abrir cada inmueble.</p>
    <label for="ciencuadras-results-paste" class="mt-3 block text-sm font-semibold">Pega la página de resultados</label>
    <textarea id="ciencuadras-results-paste" class="input mt-1 min-h-24 w-full bg-white" @paste="paste($event)"
        placeholder="Haz clic aquí y pulsa Ctrl+V para preparar todos los avisos copiados." aria-describedby="ciencuadras-paste-help"></textarea>
    <p id="ciencuadras-paste-help" class="mt-2 text-xs">Solo oficinas en venta de la ciudad del expediente. No agrega al pegar. PH y fotos se completan después; revisa la ubicación publicada de cada aviso.</p>
    <p role="status" class="mt-3 text-sm font-semibold" x-text="message"></p>
    <div x-show="results.length" x-cloak class="mt-3">
        <div class="flex flex-wrap gap-2">
            <button type="button" class="btn-secondary min-h-11" @click="selectAll()">Seleccionar todos los disponibles</button>
            <button type="button" class="btn-secondary min-h-11" @click="selected = []">Desmarcar todos</button>
            <button type="button" class="btn-primary min-h-11" :disabled="busy || !selected.length" @click="add()" x-text="'Agregar seleccionados (' + selected.length + ')'">Agregar seleccionados</button>
        </div>
        <p class="mt-2 text-xs">Gris: ya registrado. Amarillo: posible coincidencia; puedes incorporarlo como por verificar y revisarlo después en la matriz. No se declara que sea otro inmueble.</p>
        <div class="mt-3 grid gap-3 md:grid-cols-2">
            <template x-for="item in results" :key="item.row.source_url">
                <article class="rounded-lg border p-3" :class="item.tone === 'registered' ? 'bg-slate-100' : (item.tone === 'review' ? 'bg-amber-50' : 'bg-white')">
                    <label class="flex min-h-11 items-center gap-2 font-semibold"><input type="checkbox" x-model="selected" :value="item.row.source_url" :disabled="item.tone === 'registered' || busy"><span x-text="'Aviso ' + item.number + ' · ' + item.row.neighborhood"></span></label>
                    <p class="text-sm" x-text="'$ ' + item.row.price_amount + ' · ' + item.row.area_m2 + ' m²'"></p>
                    <p class="mt-1 text-xs" x-text="item.label"></p>
                    <a class="mt-1 inline-flex min-h-11 items-center text-sm text-blue-700 underline" :href="item.row.source_url" target="_blank" rel="noopener">Ver inmueble</a>
                </article>
            </template>
        </div>
    </div>
</section>
