<details class="mt-3 rounded-lg border p-3" x-data="{allFactors:false}" @toggle="comparisonOpen=$el.open">
    <summary class="min-h-11 cursor-pointer font-semibold">Cuadro de atributos · sujeto y portales</summary>
    <p class="my-2 text-xs">Verde: coinciden las fuentes disponibles. Amarillo: diferencia por validar. Sin dato no significa cero. La coincidencia no confirma identidad ni comparabilidad.</p>
    <label class="my-2 flex min-h-11 items-center gap-2 text-sm"><input type="checkbox" x-model="allFactors" @change.stop>Ver también factores sin datos en los portales</label>
    <div class="overflow-x-auto" tabindex="0" aria-label="Comparación de atributos por anuncio">
        <div role="table" class="table w-full text-sm">
            <div role="row" class="table-row border-b text-left"><div role="columnheader" class="table-cell p-2">Dato / factor</div><div role="columnheader" class="table-cell p-2">Sujeto · referencia</div>
                <template x-for="ad in card.rows" :key="ad.id"><div role="columnheader" class="table-cell min-w-36 p-2"><span x-text="ad.source_name || 'Fuente pendiente'"></span><span class="block text-xs font-normal" x-text="ad.listing_code || ad.id.slice(0,8)"></span></div></template>
                <div role="columnheader" class="table-cell min-w-36 p-2">Validación</div>
            </div>
            <template x-for="(factor, index) in intakeComparison(card).filter(item => allFactors || item.values.some(value => value !== '') || ['price_amount','area_m2','area_basis'].includes(item.sample))" :key="index"><div role="row" class="table-row border-b">
                <div role="columnheader" class="table-cell min-w-40 p-2 text-left font-medium" x-text="factor.label"></div>
                <div role="cell" class="table-cell bg-slate-100 p-2" x-text="factor.subject || 'Pendiente / no aplica'"></div>
                <template x-for="(value, sourceIndex) in factor.values" :key="sourceIndex"><div role="cell" class="table-cell p-2" x-text="value || 'No publicado'"></div></template>
                <div role="cell" class="table-cell p-2" :class="factor.state === 'same' ? 'bg-emerald-50 text-emerald-900' : factor.state === 'different' ? 'bg-amber-50 text-amber-900' : 'bg-slate-50 text-slate-600'" x-text="factor.validation"></div>
            </div></template>
        </div>
    </div>
    <p class="mt-2 text-xs">Se conservan todos los atributos recogidos. La selección de predictores se hará en Análisis con correlación y revisión del modelo.</p>
</details>
