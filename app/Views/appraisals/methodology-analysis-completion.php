<section class="mb-4 rounded-xl border p-3" aria-label="Completar datos y evaluar combinaciones">
    <p x-show="analysisHasSimulated()" class="mb-2 rounded bg-purple-50 p-2 font-semibold text-purple-900">Ejercicio numérico con datos simulados. Los originales se conservan; estos complementos no son datos verificados.</p>
    <h3 class="font-semibold">Completar muestras para el análisis</h3>
    <p class="my-2" aria-live="polite" x-text="analysisActiveRows().length+' inmuebles conservados · '+analysisComplete()+' con datos completos · '+(analysisActiveRows().length-analysisComplete())+' con datos pendientes'"></p>
    <div class="flex flex-wrap gap-2 text-sm"><span class="rounded bg-emerald-50 p-2 text-emerald-900">Verde: dato recogido</span><span class="rounded bg-amber-50 p-2 text-amber-900">Amarillo: falta completar</span><span class="rounded bg-blue-50 p-2 text-blue-900">Azul: dato manual con soporte</span></div>
    <p class="my-2 text-sm">Completa las celdas amarillas en la tabla, con el dato y su fuente. Los originales se conservan. Vacío no equivale a cero. El borrador se autoguarda; después registra otro resultado para conservar esta etapa en el historial.</p>
    <label class="flex min-h-11 items-center gap-2"><input type="checkbox" x-model="analysisOnlyMissing">Mostrar solo inmuebles con datos pendientes</label>
    <button type="button" class="btn-primary my-2" @click="analysisRecordManual(); $dispatch('input')">Actualizar resultado con los datos completados</button>
    <h4 class="mt-3 font-semibold">Combinaciones sugeridas por cantidad de datos</h4>
    <p class="my-2 text-sm">Área obligatoria más dos factores compatibles, con al menos 50 % de cobertura y variación. Se cuenta la coincidencia de datos en cada inmueble, con oferta y área válidas. Cumplir la cantidad permite continuar a validar la regresión; todavía falta codificar, revisar correlación y colinealidad y completar descuentos.</p>
    <template x-for="option in analysisCombinations()" :key="option.labels"><article class="mt-2 rounded-lg border p-3" :class="option.meets ? 'bg-emerald-50 text-emerald-900' : 'bg-amber-50 text-amber-900'">
        <p class="font-semibold" x-text="option.labels"></p>
        <p x-text="option.count+' muestras completas / '+option.required+' necesarias · '+(option.meets ? 'Cumple la cantidad para 3 factores' : 'Faltan '+(option.required-option.count)+' muestras completas')"></p>
        <button type="button" class="btn-secondary mt-2" @click="analysisChooseCombination(option); $dispatch('input'); $nextTick(() => { if(analysisError)return; const target=Array.from($refs.analysisTable.querySelectorAll('input[placeholder=&quot;Digita el dato verificado&quot;]')).find(input => input.getClientRects().length) || $refs.analysisTable; target.focus(); target.scrollIntoView({block:'center',behavior:'smooth'}); })" x-text="option.meets ? 'Ver inmuebles de esta combinación' : 'Completar inmuebles de esta combinación'">Completar inmuebles de esta combinación</button>
    </article></template>
    <p class="text-sm" x-show="!analysisCombinations().length">Aún no hay dos factores adicionales elegibles para proponer una combinación.</p>
</section>
