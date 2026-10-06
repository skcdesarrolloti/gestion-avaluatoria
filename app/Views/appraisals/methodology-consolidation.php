<section x-show="mode==='intake' && intakeView==='consolidation'" x-cloak class="mt-4">
    <p class="mb-3 text-sm"><strong x-text="total"></strong> anuncios recogidos → <strong x-text="intakeCount"></strong> grupos de inmuebles · <span x-text="consolidationDuplicates"></span> anuncios vinculados como respaldo · <span x-text="consolidationPending"></span> grupos por revisar.</p>
    <p class="mb-3 text-sm">Revisa las coincidencias dentro de cada fuente y entre fuentes. Vincula solo si confirmas que es el mismo inmueble. Elige una ficha principal para investigar; las demás se conservan como respaldo.</p>
    <div role="table" aria-label="Consolidación de las muestras" class="max-h-[65vh] overflow-auto rounded-xl border" tabindex="0">
        <div role="row" class="sticky top-0 z-10 flex w-max bg-slate-100 font-semibold"><div role="columnheader" class="w-72 p-3">Inmueble y anuncios recogidos</div><div role="columnheader" class="w-72 p-3">Repetidos y ficha principal</div><div role="columnheader" class="w-56 p-3">Decisión</div></div>
        <template x-for="(group,index) in consolidationRows" :key="group.key">
            <div role="row" class="flex w-max border-t text-sm">
                <div role="cell" class="w-72 shrink-0 p-3"><strong x-text="(index+1)+'. '+group.title"></strong>
                    <template x-for="ad in group.rows" :key="ad.id"><p class="mt-2 break-words" x-text="(ad.source_name || 'Fuente')+' · '+(ad.listing_code || ad.id.slice(0,8))+' · '+(ad.price_amount || 'No publicado')+' COP · '+(ad.area_m2 || 'No publicado')+' m²'"></p></template>
                </div>
                <div role="cell" class="w-72 shrink-0 p-3">
                    <p class="mb-2 text-amber-900" x-show="group.candidates.length" x-text="'Posibles coincidencias: '+group.candidates.map(c=>c.key.slice(0,8)).join(', ')"></p>
                    <label class="label">Es el mismo inmueble que<select class="input" :disabled="researchBusy" @change.stop="intakeLink(group,$event.target.value); $event.target.value=''"><option value="">Sin vincular</option><template x-for="target in intakeTargets.filter(t=>t.key!==group.key)" :key="target.key"><option :value="target.key" x-text="target.title+' · '+target.key.slice(0,8)"></option></template></select></label>
                    <label class="label mt-2">Ficha principal<select class="input" :disabled="researchBusy" :value="(group.rows.find(r=>r.research_primary==='si') || group.rows[0]).id" @change.stop="intakePrimary(group,$event.target.value)"><template x-for="ad in group.rows" :key="ad.id"><option :value="ad.id" x-text="ad.source_name+' · '+(ad.listing_code || ad.id.slice(0,8))"></option></template></select></label>
                    <template x-for="ad in group.rows.filter(r=>r.property_group)" :key="ad.id"><button type="button" class="btn-secondary mt-2" :disabled="researchBusy" @click="intakeUnlink(ad)" x-text="'Separar '+(ad.listing_code || ad.id.slice(0,8))"></button></template>
                </div>
                <div role="cell" class="w-56 shrink-0 p-3"><label class="label">Conservar este inmueble<select class="input" :disabled="researchBusy" :value="group.rows.every(r=>r.capture_confirmation==='confirmed') ? 'confirmed' : group.rows.every(r=>r.capture_confirmation==='excluded') ? 'excluded' : ''" @change.stop="intakeConfirm(group,$event.target.value)"><option value="">Por revisar</option><option value="confirmed">Sí, inmueble consolidado</option><option value="excluded">No participa</option></select></label><p class="mt-2 text-xs">No borra anuncios ni selecciona para Análisis.</p></div>
            </div>
        </template>
    </div>
    <button type="button" class="btn-primary mt-3" :disabled="researchBusy || consolidationPending>0 || !intakeConfirmedCount" @click="finishConsolidation()" x-text="'Continuar con '+intakeConfirmedCount+' inmuebles'"></button>
</section>
