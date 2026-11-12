<section x-show="intakeView==='review'" class="my-4 rounded-xl border border-teal-200 bg-teal-50 p-4" aria-label="Revisión sencilla de arriendos por portal">
    <h4 class="text-lg font-semibold" x-text="'Revisar muestras de '+intakePortal">Revisar muestras del portal</h4>
    <p class="mt-2 text-sm"><strong x-text="intakeTableData.rows.length"></strong> anuncios recogidos de este portal. Revisa uno por vez; guardar un anuncio no confirma su comparabilidad.</p>
    <label class="label mt-3">Buscar en las muestras de este portal
        <input class="input mt-1" x-model="intakeSearch" @input.stop="intakePage=1; rebuildIntake()" placeholder="Escribe edificio, barrio, código o una palabra del anuncio">
    </label>
    <div class="my-3 flex flex-wrap items-center gap-3">
        <button type="button" class="btn-secondary min-h-11" :disabled="intakePage<=1" @click="intakePage--; rebuildIntake()">Muestra anterior</button>
        <span class="text-sm font-semibold" x-text="intakeCards.length?'Muestra '+intakePage+' de '+intakePages:'Sin muestras con esta búsqueda'"></span>
        <button type="button" class="btn-secondary min-h-11" :disabled="intakePage>=intakePages" @click="intakePage++; rebuildIntake()">Muestra siguiente</button>
    </div>
    <template x-for="card in intakeCards" :key="card.key">
        <article class="rounded-xl border bg-white p-4">
            <template x-for="row in card.rows" :key="row.id">
                <div>
                    <h5 class="font-semibold" x-text="row.listing_title || row.project_name || row.property_type || 'Anuncio por revisar'"></h5>
                    <p x-show="/\/oficina-en-venta-/i.test(row.source_url||'')" class="mt-3 rounded-lg border border-amber-600 bg-amber-50 p-3 text-sm font-semibold">El enlace indica VENTA. La etiqueta de arriendo debe revisarse; no uses este anuncio como muestra de renta hasta aclararlo.</p>
                    <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-2">
                        <div><dt class="font-semibold">Portal</dt><dd x-text="row.source_name || 'Por verificar'"></dd></div>
                        <div><dt class="font-semibold">Operación registrada</dt><dd x-text="row.operation || 'Por verificar'"></dd></div>
                        <div><dt class="font-semibold">Código del anuncio</dt><dd x-text="row.listing_code || row.source_url?.match(/\/((?:\d+-M)?\d+)\/?(?:\?.*)?$/)?.[1] || 'No publicado'"></dd></div>
                        <div><dt class="font-semibold">Ubicación publicada</dt><dd x-text="row.published_location || row.neighborhood || 'Pendiente de verificar en la ficha'"></dd></div>
                        <div><dt class="font-semibold">Importe publicado · COP</dt><dd x-text="row.price_amount || 'No publicado'"></dd></div>
                        <div><dt class="font-semibold">Área publicada · m²</dt><dd x-text="row.area_m2 || 'Pendiente de completar desde la ficha'"></dd></div>
                    </dl>
                    <a class="btn-secondary mt-3 min-h-11" x-show="/^https?:\/\//i.test(row.source_url||'')" :href="/^https?:\/\//i.test(row.source_url||'')?row.source_url:'#'" target="_blank" rel="noopener">Abrir anuncio original para verificar</a>
                    <details class="mt-3"><summary class="min-h-11 cursor-pointer font-semibold">Leer descripción y observaciones conservadas</summary><p class="whitespace-pre-wrap break-words text-sm" x-text="row.published_text || row.evidence_detail || row.comparability_notes || 'Sin descripción recibida'"></p></details>
                </div>
            </template>
        </article>
    </template>
    <p x-show="!intakeCards.length" class="mt-3 text-sm">No hay anuncios con estos filtros. Usa «Buscar y pegar anuncios» para recoger muestras de este portal.</p>
</section>
