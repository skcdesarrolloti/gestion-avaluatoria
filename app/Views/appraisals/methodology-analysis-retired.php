<div x-show="analysisBusy || analysisFilterDone" x-cloak class="mb-3 rounded-xl border p-3" role="status">
    <p x-text="analysisBusy ? 'Depurando muestras…' : 'Depuración finalizada'"></p>
    <progress class="h-4 w-full" :value="analysisProcessed" :max="Math.max(1,analysisRows.length)" aria-label="Progreso de depuración de muestras"></progress>
    <p class="text-sm" x-text="analysisProcessed+' de '+analysisRows.length+' muestras revisadas · '+Math.round(analysisProcessed/Math.max(1,analysisRows.length)*100)+'%'"></p>
</div>
<p class="mb-3 text-red-700" role="alert" x-show="analysisError" x-text="analysisError"></p>
<details x-show="analysisRegimeApplied && analysisRetired().length" x-cloak class="mb-3 rounded-xl border p-3" open>
    <summary class="min-h-11 cursor-pointer font-semibold">Historial de muestras apartadas y reincorporadas</summary>
    <p class="text-sm" x-text="analysisRetired().filter(v=>v.reason==='other').length+' apartadas por régimen distinto · '+analysisRetired().filter(v=>v.reason==='unknown').length+' por régimen sin verificar · '+analysisRetired().filter(v=>v.restored_at).length+' reincorporadas por el analista'"></p>
    <p class="text-sm">Se conservan sus datos. Marca Reincorporar para habilitar una muestra; su régimen original permanece. Revisa su comparabilidad antes de usarla.</p>
    <div class="max-h-96 overflow-auto"><template x-for="review in analysisRetired()" :key="review.id"><div class="border-t py-3">
        <p class="font-semibold" x-text="review.row.source_name+' · '+(review.row.listing_code || review.row.property_type || 'Inmueble')"></p>
        <p class="text-sm" x-text="'Oferta: '+analysisMoney(analysisOffer(review.id))+' · Área: '+(review.row.area_m2 || 'Sin dato')+' m²'"></p>
        <p class="text-sm" x-text="review.reason==='other' ? 'Motivo del retiro: régimen diferente al del sujeto.' : 'Motivo del retiro: régimen sin verificar, sin indicios suficientes del régimen del sujeto.'"></p>
        <p class="text-xs" x-text="'Apartada: '+new Date(review.at).toLocaleString('es-CO')+(review.restored_at ? ' · Reincorporada: '+new Date(review.restored_at).toLocaleString('es-CO') : '')"></p>
        <a class="inline-flex min-h-11 items-center text-blue-700 underline" :href="/^https?:\/\//i.test(review.row.source_url) ? review.row.source_url : '#'" target="_blank" rel="noopener">Ver anuncio</a>
        <label class="flex min-h-11 items-center gap-2"><input type="checkbox" :checked="!!review.restored_at" :disabled="analysisBusy || analysisAppliedScope==='all'" @change="await analysisReinclude(review.id,$event.target.checked); $dispatch('input')"><span>Reincorporar esta muestra</span></label>
        <p class="text-sm" x-text="analysisActiveRows().some(r=>r.id===review.id) ? 'Habilitada en el grupo actual' : 'Fuera del grupo actual'"></p>
    </div></template></div>
</details>
