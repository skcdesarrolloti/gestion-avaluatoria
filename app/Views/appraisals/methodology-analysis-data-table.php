<p class="mb-3 text-sm">Una fila por inmueble. Factores ordenados por cantidad de muestras con dato, sin límite de seis. Valor con descuento = oferta × (1 − descuento / 100); valor por m² = valor con descuento / área publicada.</p>
<nav class="mb-3 flex flex-wrap gap-2" aria-label="Pasos del análisis">
    <button type="button" class="btn-secondary" :aria-current="analysisView==='raw'?'step':null" :class="analysisView==='raw'?'ring-2 ring-teal-700':''" @click="analysisView='raw'; $dispatch('input')">1. Información recogida</button>
    <button type="button" class="btn-secondary" :aria-current="analysisView==='clean'?'step':null" :class="analysisView==='clean'?'ring-2 ring-teal-700':''" @click="analysisView='clean'; $dispatch('input')">2. Seleccionar factores</button>
    <button type="button" class="btn-secondary" :aria-current="analysisView==='result'?'step':null" :class="analysisView==='result'?'ring-2 ring-teal-700':''" @click="analysisUpdate(); $dispatch('input')">3. Resultado depurado</button>
</nav>
<p class="mb-3 text-sm" x-show="analysisView==='raw'">Toda la información recogida. Continúa en 2. Seleccionar factores para depurar.</p>
<div x-show="analysisView==='clean'" x-cloak>
<?php require __DIR__.'/methodology-analysis-regime-filter.php'; ?>
<p class="mb-3 text-sm">Regla de depuración: al menos 50 % con dato, compatible con el tipo y con variación. En oficinas, Habitaciones y Estrato no participan. No se borran datos. Para regresión faltará validar codificación, valor por m², correlación y colinealidad.</p>
<p class="mb-3 text-sm" aria-live="polite" x-text="analysisColumns().length+' factores marcados. Después de cambiar la selección, pulsa Actualizar depuración y ver resultado.'"></p>
<button type="button" class="btn-primary mb-3" @click="analysisUpdate(); $dispatch('input')">Actualizar depuración y ver resultado</button>
<div class="mb-3 rounded-xl border p-3"><h3 class="font-semibold">Validaciones y selección de factores</h3>
    <p class="text-sm">Marca o desmarca candidatos. Los descartados quedan bloqueados y conservados en Información recogida.</p>
    <div class="grid gap-2 sm:grid-cols-2"><template x-for="factor in analysisFactors" :key="factor.key"><label class="flex min-h-11 items-center gap-2"><input type="checkbox" :checked="analysisEligible(factor) && analysisSelected.includes(factor.key)" @change="analysisSelected=$event.target.checked ? [...analysisSelected,factor.key] : analysisSelected.filter(k=>k!==factor.key)" :disabled="!analysisEligible(factor)"><span><span class="block" x-text="factor.label+' · '+factor.count+'/'+analysisActiveRows().length+' con dato ('+Math.round(factor.count/analysisActiveRows().length*100)+'%)'"></span><span class="block text-xs text-slate-600" x-text="analysisSuggestion(factor)"></span></span></label></template></div>
</div>
<button type="button" class="btn-primary mb-3" @click="analysisUpdate(); $dispatch('input')">Actualizar depuración y ver resultado</button>
</div>
<div x-show="analysisView==='result'" x-cloak>
    <p class="mb-3 rounded-xl border p-3" role="status" x-text="'Depuración actualizada: '+analysisActiveRows().length+' muestras · '+analysisColumns().length+' factores aplicados · '+analysisComplete()+' filas con datos en todos los factores aplicados'"></p>
    <p class="mb-3 text-sm text-amber-900" x-show="analysisColumns().length && analysisComplete()<=analysisColumns().length+1">Las filas completas no superan factores + 1. Vuelve a Seleccionar factores o completa datos antes de preparar la regresión.</p>
    <p class="mb-3 text-sm" x-show="!analysisColumns().length">No hay factores aplicados. Vuelve a 2. Seleccionar factores.</p>
</div>
<div x-show="analysisView!=='clean'" class="max-h-[65vh] overflow-auto rounded-xl border" role="region" aria-label="Tabla de análisis de inmuebles" tabindex="0">
<table class="w-max min-w-full text-left text-sm"><thead class="sticky top-0 bg-slate-100"><tr>
    <th class="sticky left-0 bg-slate-100 p-3">Inmueble</th><th class="p-3">Régimen PH / no PH</th><th class="p-3">Oferta · COP</th><th class="p-3">Área publicada · m²</th>
    <template x-for="factor in analysisColumns()" :key="factor.key"><th class="max-w-48 p-3" x-text="factor.label+' ('+factor.count+'/'+analysisActiveRows().length+')'"></th></template>
    <th class="p-3">Descuento · %</th><th class="p-3">Valor con descuento · COP</th><th class="p-3">Valor por m² · COP/m²</th>
</tr></thead><tbody><template x-for="property in analysisVisibleRows()" :key="property.key"><tr class="border-t">
    <th class="sticky left-0 max-w-48 bg-white p-3"><span x-text="'Muestra '+(property.analysisIndex+1)"></span><p class="font-normal" x-text="analysisRows[property.analysisIndex].property_type"></p><p class="font-normal" x-text="property.source_name"></p><a :href="/^https?:\/\//i.test(property.source_url) ? property.source_url : '#'" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center text-blue-700 underline">Ver anuncio</a></th>
    <td class="max-w-64 p-3"><label :for="'analysis-regime-'+property.key" class="block text-xs">Régimen del inmueble</label><select class="input w-48" :id="'analysis-regime-'+property.key" x-model="analysisRows[property.analysisIndex].ph_regime"><option value="por_verificar">Sin verificar</option><option value="si">PH</option><option value="no">No PH</option></select>
        <p class="mt-1 text-xs" x-text="analysisRegime(analysisRows[property.analysisIndex])"></p>
        <details><summary class="min-h-11 cursor-pointer text-xs">Soporte del régimen</summary><label :for="'analysis-regime-source-'+property.key" class="block text-xs">Documento o fuente, fecha y responsable</label><textarea class="input w-48" rows="2" maxlength="1600" :id="'analysis-regime-source-'+property.key" placeholder="Ej. reglamento PH revisado, fecha y responsable" x-model="analysisRows[property.analysisIndex].ph_regime_source"></textarea></details>
    </td>
    <td class="p-3" x-text="analysisMoney(analysisOffer(property.key))"></td><td class="max-w-48 p-3"><span x-text="property.values.area_m2 ?? 'No publicado'"></span><p class="mt-1 text-xs text-amber-900" x-text="analysisAreaNote(property.key)"></p></td>
    <template x-for="factor in analysisColumns()" :key="factor.key"><td class="max-w-48 whitespace-pre-wrap p-3" x-text="property.values[factor.key] ?? 'No publicado'"></td></template>
    <td class="p-3"><label :for="'analysis-percent-'+property.key" class="block text-xs">Descuento · %</label><input :id="'analysis-percent-'+property.key" class="input w-28" type="number" min="0" max="100" step="any" placeholder="Ej. 10" :value="analysisPercents[property.key]" @input="analysisChange(property.key,$event.target.value)" :aria-invalid="Number(analysisPercents[property.key])<0 || Number(analysisPercents[property.key])>100"><span x-show="Number(analysisPercents[property.key])<0 || Number(analysisPercents[property.key])>100" class="block text-red-700">Usa de 0 a 100 %.</span></td>
    <td class="p-3" x-text="analysisMoney(analysisResult(property.key).value)"></td><td class="p-3" x-text="analysisMoney(analysisResult(property.key).perM2)"></td>
</tr></template></tbody></table></div>
<p x-show="analysisView!=='clean'" class="mt-2 text-sm text-slate-600">Vacío no equivale a cero. Son cálculos sobre oferta y área publicada; no sustituyen la depuración de componentes ni el valor adoptado. Registra el soporte del descuento en la muestra.</p>
