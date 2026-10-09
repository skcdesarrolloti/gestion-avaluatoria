    <details class="mt-3 rounded-lg border p-3">
    <summary class="min-h-11 cursor-pointer font-semibold">Elegir factores del modelo · combinaciones disponibles</summary>
    <h4 class="mt-3 font-semibold">Alternativas para construir la regresión</h4>
    <p class="my-2 text-sm">Utiliza los datos ya recibidos y el grupo aplicado. Esta herramienta propone área y dos factores según su disponibilidad; no vuelve a leer portales ni completar inmuebles. Aplicar una combinación cambia los factores seleccionados y registra una etapa; no crea datos ni reincorpora muestras. La cantidad disponible no demuestra calidad del modelo.</p>
    <template x-for="option in analysisCombinations()" :key="option.labels"><article class="mt-2 rounded-lg border p-3" :class="option.meets ? 'bg-emerald-50 text-emerald-900' : 'bg-amber-50 text-amber-900'">
        <p class="font-semibold" x-text="option.labels"></p>
        <p x-text="option.count+' muestras completas / '+option.required+' necesarias · '+(option.meets ? 'Cumple la cantidad para 3 factores' : 'Faltan '+(option.required-option.count)+' muestras completas')"></p>
        <button type="button" class="btn-secondary mt-2" @click="analysisChooseCombination(option); if(!analysisError)regressionConfirmed=false; $dispatch('input')">Aplicar estos factores al modelo</button>
    </article></template>
    <p class="text-sm" x-show="!analysisCombinations().length">No hay dos factores adicionales elegibles con los datos actuales. La captura y corrección de datos se consulta en Insumos.</p>
    </details>
