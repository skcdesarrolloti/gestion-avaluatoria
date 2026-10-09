<fieldset :disabled="analysisBusy || !analysisRegimeApplied || analysisScope!==analysisAppliedScope">
<legend class="sr-only">Selección de factores del modelo</legend>
<p class="mb-3 text-sm text-amber-900" role="status" x-show="analysisFactorsPending()">Selección modificada: pendiente de actualizar. El resultado anterior se conserva en el historial.</p>
<p class="mb-3 text-sm">Regla de depuración: al menos 50 % con dato, compatible con el tipo y con variación. En oficinas, Habitaciones y Estrato no participan. No se borran datos. Para regresión faltará validar codificación, valor por m², correlación y colinealidad.</p>
<p class="mb-3 text-sm" aria-live="polite" x-text="analysisFactorCount()+' factores marcados · área del modelo: '+analysisModelArea().label+'. Para aplicar esta selección, pulsa Aplicar factores al modelo.'"></p>
<p class="mb-3 text-sm" aria-live="polite" x-text="'Mínimo 3 factores y 30 muestras completas; 10 por factor: '+analysisSampleRule().factors+' factores requieren '+analysisSampleRule().required+' muestras; esta selección tiene '+analysisSampleRule().complete+' filas completas.'"></p>
<button type="button" class="btn-primary mb-3" @click="analysisUpdate(); if(!analysisError)regressionConfirmed=false; $dispatch('input')">Aplicar factores al modelo</button>
<div class="mb-3 rounded-xl border p-3"><h3 class="font-semibold">Validaciones y selección de factores</h3>
    <p class="my-2 text-sm" x-text="'Área obligatoria del modelo: '+analysisModelArea().label+'. Área publicada se conserva para calcular el valor por m²; no se suma como otro factor al elegir Área privada.'"></p>
    <p class="text-sm">Marca o desmarca candidatos. Los descartados quedan bloqueados y disponibles al consultar los originales.</p>
    <div class="grid gap-2 sm:grid-cols-2"><template x-for="factor in analysisFactors" :key="factor.key"><label class="flex min-h-11 items-center gap-2"><input type="checkbox" :checked="analysisEligible(factor) && analysisSelected.includes(factor.key)" @change="analysisSelected=$event.target.checked ? [...analysisSelected,factor.key] : analysisSelected.filter(k=>k!==factor.key)" :disabled="!analysisEligible(factor)"><span><span class="block" x-text="factor.label+' · '+factor.count+'/'+analysisActiveRows().length+' con dato ('+Math.round(factor.count/analysisActiveRows().length*100)+'%)'"></span><span class="block text-xs text-slate-600" x-text="analysisSuggestion(factor)"></span></span></label></template></div>
</div>
</fieldset>
