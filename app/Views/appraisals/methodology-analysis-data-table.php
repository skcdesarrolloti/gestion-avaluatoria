
<p class="mb-3 text-red-700" role="alert" x-show="analysisError && ['clean','result'].includes(analysisView)" x-text="analysisError"></p>
<p class="mb-3 text-sm" x-show="analysisView==='raw'">Consulta de originales: incluye los inmuebles fuera del grupo. Para completar atributos, vuelve al grupo preparado.</p>
<div x-show="analysisView==='regime'" x-cloak>
<?php require __DIR__.'/methodology-analysis-regime-filter.php'; ?>
<button type="button" class="btn-primary mb-3" :disabled="analysisBusy || (analysisRegimeApplied && analysisScope===analysisAppliedScope && !analysisError)" @click="await analysisApplyRegime(); $dispatch('input')" x-text="analysisBusy ? 'Depurando…' : analysisRegimeApplied && analysisScope===analysisAppliedScope && !analysisError ? 'Depuración finalizada · '+analysisActiveRows().length+' muestras para trabajar' : 'Aplicar depuración de muestras'">Aplicar depuración de muestras</button>
<p class="mb-3" role="status" x-show="analysisRegimeApplied && analysisScope===analysisAppliedScope && !analysisBusy && !analysisError" x-text="analysisRows.length+' muestras recogidas → '+analysisActiveRows().length+' para trabajar · '+(analysisRows.length-analysisActiveRows().length)+' fuera del grupo, conservadas'"></p>
<?php require __DIR__.'/methodology-analysis-retired.php'; ?>
</div>

<div x-show="analysisView==='result'&&analysisModule==='samples'" x-cloak>
<?php require __DIR__.'/methodology-analysis-completion.php'; ?>
<details x-show="analysisStatistics.some(v=>v.action==='factors')" class="mb-3 rounded-xl border p-3">
    <summary class="min-h-11 cursor-pointer font-semibold">Consultar factores preparados</summary>
    <p class="mb-3 rounded-xl border p-3" role="status" x-text="'Depuración actualizada: '+analysisActiveRows().length+' muestras · '+analysisFactorCount()+' factores aplicados (incluye '+analysisModelArea().label+') · '+analysisComplete()+' filas con datos en todos los factores aplicados'"></p>
    <p class="mb-3 text-sm">El paso 3 comprobará la codificación y la cantidad de filas disponibles para los factores del modelo.</p>
    <p class="mb-3 text-sm" x-show="!analysisColumns().length">No hay factores adicionales aplicados. El área en m² sigue fija. Elige los atributos en Configuración del estudio.</p>
</details>
</div>
<section x-show="['raw','result'].includes(analysisView)" class="space-y-2">
    <h3 class="font-semibold">Verificación de las muestras</h3>
    <p class="text-sm">Para corregir precio, área o atributos, abre «Editar datos aquí» en la fila. El descuento y su soporte también se editan directamente. Desplaza la tabla para ver todas las columnas.</p>
<div x-ref="analysisTable" x-show="['raw','result'].includes(analysisView)" class="max-h-[65vh] overflow-auto rounded-xl border" role="region" aria-label="Tabla de análisis de inmuebles" tabindex="0">
<table class="w-max min-w-full text-left text-sm"><thead class="sticky top-0 bg-slate-100"><tr>
    <th class="sticky left-0 bg-slate-100 p-3">Inmueble</th><th class="p-3">Régimen PH / no PH</th><th class="p-3">Oferta · COP</th><th class="p-3">Área publicada · m²</th>
    <th class="p-3">Descuento · %</th><th class="p-3">Valor con descuento · COP</th><th class="p-3">Valor por m² · COP/m²</th>
    <template x-for="factor in (analysisView==='raw' ? analysisFactors : analysisVerificationColumns())" :key="factor.key"><th class="max-w-48 p-3" x-text="factor.label+' ('+factor.count+'/'+analysisActiveRows().length+')'"></th></template>

</tr></thead><tbody><template x-for="(property,visibleIndex) in analysisDisplayRows()" :key="property.key"><tr class="border-t" :id="'analysis-row-'+property.key" tabindex="-1" :class="analysisEditingId===property.key?'ring-2 ring-teal-700':''">
    <th class="sticky left-0 max-w-48 bg-white p-3"><span x-text="analysisView==='result' ? (analysisOnlyMissing ? 'Inmueble pendiente '+(visibleIndex+1)+' de '+analysisDisplayRows().length : 'Fila '+(visibleIndex+1)+' de '+analysisVisibleRows().length) : 'Muestra '+(property.analysisIndex+1)"></span><p x-show="analysisView==='result'" class="text-xs font-normal" x-text="'Referencia original: muestra '+(property.analysisIndex+1)"></p><p class="font-normal" x-text="analysisRows[property.analysisIndex].property_type"></p><p class="font-normal" x-text="property.source_name"></p><a :href="/^https?:\/\//i.test(property.source_url) ? property.source_url : '#'" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center text-blue-700 underline">Ver anuncio</a><div x-data="{r:{id:property.key}}" class="font-normal"><?php require __DIR__.'/methodology-course-inline-edit.php'; ?></div></th>
    <td class="max-w-64 p-3"><label :for="'analysis-regime-'+property.key" class="block text-xs">Régimen del inmueble</label><select class="input w-48" :id="'analysis-regime-'+property.key" x-model="analysisRows[property.analysisIndex].ph_regime"><option value="por_verificar">Sin verificar</option><option value="si">PH</option><option value="no">No PH</option></select>
        <p class="mt-1 text-xs" x-text="analysisRegime(analysisRows[property.analysisIndex])"></p>
        <details><summary class="min-h-11 cursor-pointer text-xs">Soporte del régimen</summary><label :for="'analysis-regime-source-'+property.key" class="block text-xs">Documento o fuente, fecha y responsable</label><textarea class="input w-48" rows="2" maxlength="1600" :id="'analysis-regime-source-'+property.key" placeholder="Ej. reglamento PH revisado, fecha y responsable" x-model="analysisRows[property.analysisIndex].ph_regime_source"></textarea></details>
    </td>
    <td class="p-3" x-text="analysisMoney(analysisOffer(property.key))"></td><td class="max-w-48 p-3"><span x-text="property.values.area_m2 ?? 'No publicado'"></span><details x-show="analysisAreaNote(property.key)" class="text-xs text-amber-900"><summary class="min-h-11 cursor-pointer">Revisar base de área</summary><p x-text="analysisAreaNote(property.key)"></p></details></td>
    <td class="p-3"><label :for="'analysis-percent-'+property.key" class="block text-xs">Descuento · %</label><input :id="'analysis-percent-'+property.key" class="input w-28" type="number" min="0" max="100" step="any" placeholder="Ej. 10" :value="analysisPercents[property.key]" @input="analysisChange(property.key,$event.target.value)" :aria-invalid="Number(analysisPercents[property.key])<0 || Number(analysisPercents[property.key])>100"><span x-show="Number(analysisPercents[property.key])<0 || Number(analysisPercents[property.key])>100" class="block text-red-700">Usa de 0 a 100 %.</span><?php require __DIR__.'/methodology-analysis-discount-support.php'; ?></td>
    <td class="p-3" x-text="analysisMoney(analysisResult(property.key).value)"></td><td class="p-3" x-text="analysisMoney(analysisResult(property.key).perM2)"></td>
    <template x-for="factor in (analysisView==='raw' ? analysisFactors : analysisVerificationColumns())" :key="factor.key"><td class="max-w-64 p-3" :class="analysisView==='result' ? analysisCellState(property,factor)==='missing' ? 'bg-amber-50 text-amber-900' : analysisCellState(property,factor)==='example' ? 'bg-purple-50 text-purple-900' : analysisCellState(property,factor)==='manual' ? 'bg-blue-50 text-blue-900' : 'bg-emerald-50 text-emerald-900' : ''">
        <?php require __DIR__.'/methodology-analysis-factor-cell.php'; ?>
    </td></template>
</tr></template></tbody></table></div>
</section>

<details class="mb-3 rounded-lg border p-3">
    <summary class="min-h-11 cursor-pointer font-semibold">Consultar originales o revisar el grupo · opcional</summary>
    <label class="label">Datos que quieres revisar<select class="input" x-model="analysisView" :disabled="analysisBusy"><option value="" disabled>Selecciona qué datos consultar</option><option value="result" :disabled="!analysisRegimeApplied||analysisScope!==analysisAppliedScope">Grupo preparado</option><option value="raw">Consultar originales</option><option value="regime">Revisar composición del grupo</option></select></label>
</details>
