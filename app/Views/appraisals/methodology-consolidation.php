<section x-show="mode==='intake' && intakeView==='consolidation'" x-cloak class="mt-4">
    <p class="mb-3 text-sm"><strong x-text="total"></strong> anuncios guardados → <strong x-text="intakeCount"></strong> inmuebles después de reunir repetidos → <strong x-text="intakeConfirmedCount"></strong> confirmados para investigar.</p>
    <p class="mb-3 text-sm">Primero conserva los que no tienen alertas. En las parejas señaladas, abre ambos anuncios y comprueba ubicación, fotos y descripción. Si es el mismo inmueble, pulsa «Reunir». Coincidir en datos no lo confirma automáticamente.</p>
    <button type="button" class="btn-secondary mb-3" :disabled="researchBusy || removalBusy || !unambiguousGroups().length" @click="confirmUnambiguous()" x-text="'Conservar '+unambiguousGroups().length+' sin alertas de repetidos'"></button>
    <p x-show="!total" class="my-3 text-sm">Aún no hay anuncios. Empieza en «1. Recoger por portal».</p>
    <label class="mb-3 flex min-h-11 items-center gap-2 text-sm"><input type="checkbox" x-model="consolidationShowAll">Ver todos los inmuebles, incluidos los que no tienen alertas y los ya confirmados</label>
    <p x-show="total && !consolidationVisible().length" class="mb-3 text-sm">No quedan parejas pendientes de revisión. Conserva los inmuebles sin alertas y continúa cuando el guardado esté confirmado.</p>
    <div role="table" aria-label="Consolidación de las muestras" class="max-h-[65vh] overflow-auto rounded-xl border" tabindex="0">
        <div role="row" class="sticky top-0 z-10 flex w-max bg-slate-100 font-semibold"><div role="columnheader" class="w-72 p-3">Inmueble y anuncios recogidos</div><div role="columnheader" class="w-72 p-3">Repetidos y ficha principal</div><div role="columnheader" class="w-56 p-3">Decisión</div></div>
        <template x-for="(group,index) in consolidationVisible()" :key="group.key">
            <div role="row" class="flex w-max border-t text-sm">
                <div role="cell" class="w-72 shrink-0 p-3"><strong x-text="(index+1)+'. '+group.title"></strong>
                    <template x-for="ad in group.rows" :key="ad.id"><div class="mt-2"><p class="break-words" x-text="(ad.source_name || 'Fuente')+' · '+(ad.listing_code || ad.id.slice(0,8))+' · '+(ad.price_amount || 'No publicado')+' COP · '+(ad.area_m2 || 'No publicado')+' m²'"></p><a x-show="/^https?:\/\//i.test(ad.source_url)" :href="/^https?:\/\//i.test(ad.source_url) ? ad.source_url : '#'" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center text-blue-700 underline">Ver este anuncio</a></div></template>
                </div>
                <div role="cell" class="w-72 shrink-0 p-3">
                    <p x-show="!group.candidates.length" class="text-sm">Sin alertas de repetidos. Puedes conservarlo como inmueble separado.</p>
                    <template x-for="candidate in group.candidates" :key="candidate.key"><div class="mb-3 rounded-lg border border-amber-300 bg-amber-50 p-2">
                        <strong x-text="candidate.row.source_name+' · '+(candidate.row.listing_code || candidate.title)"></strong>
                        <p x-text="(candidate.row.price_amount || 'No publicado')+' COP · '+(candidate.row.area_m2 || 'No publicado')+' m²'"></p>
                        <p class="my-2 text-sm" x-text="'Coinciden: '+candidate.reasons.join(', ')"></p>
                        <a x-show="/^https?:\/\//i.test(candidate.row.source_url)" :href="/^https?:\/\//i.test(candidate.row.source_url) ? candidate.row.source_url : '#'" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center text-blue-700 underline">Ver posible repetido</a>
                        <button type="button" class="btn-secondary" :disabled="researchBusy || removalBusy" @click="intakeLink(group,candidate.key)">Reunir: confirmé que es el mismo</button>
                    </div></template>
                    <details class="mt-2"><summary class="min-h-11 cursor-pointer text-sm">Buscar otro repetido manualmente</summary><label class="label">Otro anuncio del mismo inmueble<select class="input" :disabled="researchBusy || removalBusy" @change.stop="intakeLink(group,$event.target.value); $event.target.value=''"><option value="">Elegir solo si comprobaste su identidad</option><template x-for="target in intakeTargets.filter(t=>t.key!==group.key)" :key="target.key"><option :value="target.key" x-text="target.title"></option></template></select></label></details>
                    <label x-show="group.rows.length>1" class="label mt-2">Anuncio más completo para investigar<select class="input" :disabled="researchBusy || removalBusy" :value="(group.rows.find(r=>r.research_primary==='si') || group.rows[0]).id" @change.stop="intakePrimary(group,$event.target.value)"><template x-for="ad in group.rows" :key="ad.id"><option :value="ad.id" x-text="ad.source_name+' · '+(ad.listing_code || ad.id.slice(0,8))"></option></template></select></label>
                    <template x-for="ad in group.rows.filter(r=>r.property_group)" :key="ad.id"><button type="button" class="btn-secondary mt-2" :disabled="researchBusy" @click="intakeUnlink(ad)" x-text="'Separar '+(ad.listing_code || ad.id.slice(0,8))"></button></template>
                </div>
                <div role="cell" class="w-56 shrink-0 p-3"><label class="label">Conservar este inmueble<select class="input" :disabled="researchBusy" :value="group.rows.every(r=>r.capture_confirmation==='confirmed') ? 'confirmed' : group.rows.every(r=>r.capture_confirmation==='excluded') ? 'excluded' : ''" @change.stop="intakeConfirm(group,$event.target.value)"><option value="">Por revisar</option><option value="confirmed">Sí, inmueble consolidado</option><option value="excluded">No participa</option></select></label><p class="mt-2 text-xs">No borra anuncios ni selecciona para Análisis.</p></div>
            </div>
        </template>
    </div>
    <button type="button" class="btn-primary mt-3" :disabled="researchBusy || consolidationPending>0 || !intakeConfirmedCount" @click="finishConsolidation()" x-text="'Continuar con '+intakeConfirmedCount+' inmuebles'"></button>
</section>
