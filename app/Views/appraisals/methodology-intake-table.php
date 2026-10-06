<section x-show="searchTab==='matriz' && mode==='intake' && intakeView!=='consolidation'" x-cloak class="mt-4">
    <p class="mb-3 text-sm"><strong x-text="intakeTableData.rows.length"></strong> <span x-text="intakeView==='research' ? 'inmuebles consolidados' : 'muestras de '+intakePortal"></span>. Una fila por inmueble; solo datos recogidos. No publicado significa que no tenemos ese dato.</p>
    <div role="table" :aria-label="intakeView==='research' ? 'Inmuebles únicos y datos publicados' : 'Muestras y datos de '+intakePortal" tabindex="0" class="max-h-[65vh] max-w-full overflow-auto rounded-xl border">
        <div role="row" class="sticky top-0 z-10 flex w-max bg-slate-100 font-semibold">
            <div role="columnheader" class="sticky left-0 z-20 w-44 shrink-0 bg-slate-100 p-3">Inmueble</div>
            <template x-for="column in intakeTableData.columns" :key="column.key"><div role="columnheader" class="w-40 shrink-0 p-3" x-text="column.label"></div></template>
        </div>
        <template x-for="(property,index) in intakeTableData.rows" :key="property.key">
            <div role="row" class="flex w-max border-t text-sm">
                <div role="cell" class="sticky left-0 z-10 w-44 shrink-0 bg-white p-3">
                    <strong x-text="'Muestra '+(index+1)"></strong><p class="break-words" x-text="property.title"></p><p x-show="intakeView==='research'" x-text="property.source_name"></p>
                    <a x-show="/^https?:\/\//i.test(property.source_url)" :href="/^https?:\/\//i.test(property.source_url) ? property.source_url : '#'" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center text-blue-700 underline">Ver anuncio</a>
                    <button x-show="intakeView==='research'" type="button" class="btn-secondary" :disabled="researchBusy || consolidationPending>0" @click="investigateConsolidated(property.key)">Completar este inmueble</button>
                    <p x-show="intakeView==='research'" class="mt-2 text-sm" role="status" x-text="researchResults.find(item=>item.row.id===property.key)?.detailState || ''"></p>
                </div>
                <template x-for="column in intakeTableData.columns" :key="column.key"><div role="cell" class="w-40 shrink-0 whitespace-pre-wrap break-words p-3" :class="property.values[column.key]===undefined ? 'text-slate-500' : ''" x-text="property.values[column.key] ?? 'No publicado'"></div></template>
            </div>
        </template>
    </div>
    <p x-show="!intakeTableData.rows.length" class="p-3 text-sm">No hay muestras de este portal en esta vista.</p>
</section>
