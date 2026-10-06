<section x-show="mode==='intake' && intakeView==='research'" x-cloak class="mt-4">
    <p class="mb-3 text-sm"><span x-text="total"></span> anuncios recogidos → <strong x-text="intakeConfirmedCount"></strong> inmuebles consolidados. Se leerá una ficha principal por inmueble, de su portal o inmobiliaria.</p>
    <p x-show="consolidationPending" class="mb-3 text-sm text-amber-900">Termina las decisiones pendientes en Consolidación de las muestras.</p>
    <button type="button" class="btn-primary" :disabled="researchBusy || consolidationPending>0 || !intakeConfirmedCount" @click="investigateConsolidated()" x-text="researchBusy ? 'Leyendo y guardando…' : 'Completar '+intakeConfirmedCount+' inmuebles'"></button>
    <p class="mt-2 text-sm" role="status" x-text="researchMessage"></p>
    <details class="mt-3" x-show="researchResults.length"><summary class="min-h-11 cursor-pointer font-semibold">Resultado de la lectura por inmueble</summary><template x-for="item in researchResults" :key="item.row.id"><p class="mt-2 text-sm" x-text="item.row.source_name+' · '+(item.row.listing_code || item.row.id.slice(0,8))+': '+item.detailState"></p></template></details>
</section>
