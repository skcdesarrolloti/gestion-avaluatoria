<template x-if="regressionTab==='diagnostics'"><div class="space-y-4">
    <h4 class="text-lg font-semibold">Gráficos y datos atípicos</h4>
    <p class="text-sm">Primero mira la distribución y los gráficos por factor; luego revisa los residuos y la influencia de cada inmueble. Las señales orientan la revisión del anuncio, las áreas, el descuento y la comparabilidad. No retiran muestras.</p>
    <p x-show="!regressionResult">Calcula la regresión en «1. Preparar y calcular» para generar los gráficos.</p>
    <template x-if="regressionResult"><div class="space-y-4">
        <p class="text-amber-900" x-show="!regressionCurrent()">Los datos cambiaron. Vuelve a preparar y calcular; este diagnóstico corresponde a la ejecución anterior.</p>
        <p class="rounded-lg bg-purple-50 p-3 text-purple-900" x-show="regressionResult.simulated">Ejemplo con datos simulados. Un ajuste alto tampoco convierte estos datos en evidencia verificada.</p>
        <p class="font-semibold" x-text="regressionEquation()"></p>
        <p x-text="'R² explica '+regressionNumber(100*regressionResult.r2)+' % de la variación en esta muestra. MAPE del ajuste: '+regressionNumber(regressionResult.diagnostics.mape)+' %. No mide desempeño fuera de la muestra.'"></p>
        <div class="overflow-auto"><table class="min-w-full text-left text-sm"><thead><tr><th class="p-2">Serie</th><th class="p-2">Media</th><th class="p-2">Mediana</th><th class="p-2">Desviación</th><th class="p-2">Asimetría</th><th class="p-2">Exceso de curtosis</th></tr></thead><tbody>
            <template x-for="series in [{label:'Valor por m²',s:regressionResult.diagnostics.summary},{label:'Residuos',s:regressionResult.diagnostics.residualSummary}]" :key="series.label"><tr><th class="p-2" x-text="series.label"></th><template x-for="key in ['mean','median','sd','skew','kurt']" :key="key"><td class="p-2" x-text="regressionNumber(series.s[key])"></td></template></tr></template>
        </tbody></table></div>
        <p class="text-sm">Asimetría y exceso de curtosis ajustados como SKEW/KURT de Excel: referencia normal 0. Curtosis positiva indica colas más pesadas; no prueba por sí sola atípicos ni normalidad. Para inferencia revisa los errores, no exige normalidad del precio o de los factores.</p>
        <details class="rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Comparación descriptiva como en el curso</summary>
            <p class="text-sm">EJ_CURSO_IGAC compara estos estimadores y su error porcentual. La media acotada omite 40 % total de ambas colas sólo para este resumen, sin retirar inmuebles del modelo.</p>
            <div class="overflow-auto"><table class="min-w-full text-left"><thead><tr><th class="p-2">Estimador</th><th class="p-2">COP/m²</th><th class="p-2">MAPE · %</th></tr></thead><tbody><template x-for="e in regressionResult.diagnostics.summary.estimators" :key="e.label"><tr><td class="p-2" x-text="e.label"></td><td class="p-2" x-text="regressionNumber(e.value)"></td><td class="p-2" x-text="regressionNumber(e.mape)"></td></tr></template></tbody></table></div>
            <p x-text="'CV del valor unitario: '+regressionNumber(regressionResult.diagnostics.summary.cv)+' %'"></p>
        </details>
        <div class="flex flex-wrap gap-3"><template x-for="c in regressionResult.diagnostics.correlations" :key="c.label"><p class="rounded-lg border p-3" x-text="c.label+' · correlación con valor por m²: '+regressionNumber(c.r)"></p></template></div>
        <p class="text-sm">Correlación simple, no efecto parcial. En antigüedad representa los códigos elegidos. Rojo: alguna señal de revisión; verde: sin señales bajo estas reglas. Pasa el cursor por los puntos para ver la muestra. En Q-Q busca una alineación aproximadamente recta.</p>
        <div class="grid gap-4 lg:grid-cols-2"><template x-for="plot in regressionResult.plots" :key="plot.title"><figure class="rounded-xl border p-2"><div x-init="regressionPlot($el,plot.svg)"></div><figcaption class="text-sm" x-text="plot.title"></figcaption></figure></template></div>
        <details class="rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">¿Qué señales usamos y cómo se calculan?</summary>
            <p>RIC = Q3 − Q1. Precio fuera de [Q1 − 1,5×RIC; Q3 + 1,5×RIC]: candidato por distribución. Cuartiles inclusivos interpolados.</p>
            <p>Residuo eᵢ = observado − estimado. Studentizado interno = eᵢ / [s√(1 − hᵢ)]; revisar |valor| &gt; 2. Apalancamiento hᵢ = diagonal de H; revisar hᵢ &gt; 2p/n. Cook Dᵢ = eᵢ²hᵢ / [p·s²·(1 − hᵢ)²]; revisar Dᵢ &gt; 4/n. p incluye intercepto.</p>
            <p>Son umbrales exploratorios, no pruebas formales. Verifica errores de captura y diferencias de mercado; no excluyas sólo para mejorar R². Toda exclusión debe conservar motivo y trazabilidad, y volver a comprobar la cantidad mínima.</p>
            <a class="inline-flex min-h-11 items-center text-blue-700 underline" href="https://www.itl.nist.gov/div898/handbook/eda/section3/eda35h.htm" target="_blank" rel="noopener">NIST: revisión de atípicos</a>
            <a class="inline-flex min-h-11 items-center text-blue-700 underline" href="https://www.itl.nist.gov/div898/handbook/eda/section3/eda35b.htm" target="_blank" rel="noopener">NIST: asimetría y curtosis</a>
        </details>
        <h5 class="font-semibold" x-text="regressionResult.diagnostics.flagged.length+' inmuebles con señales para revisar de '+regressionResult.n"></h5>
        <p class="text-sm">La tabla incluye todas las filas del cálculo. «Revisar inmueble» te lleva directamente a su fila conservada; usa «Ver anuncio» para validar el origen. No cambia la selección.</p>
        <div class="max-h-[65vh] overflow-auto rounded-xl border"><table class="min-w-full text-left text-sm"><thead class="sticky top-0 bg-slate-100"><tr><th class="p-2">Inmueble</th><th class="p-2">Observado</th><th class="p-2">Estimado</th><th class="p-2">Residuo</th><th class="p-2">Studentizado</th><th class="p-2">h</th><th class="p-2">Cook</th><th class="p-2">Señales</th></tr></thead><tbody>
            <template x-for="c in regressionResult.diagnostics.cases" :key="c.id"><tr class="border-t" :class="c.flags.length?'bg-amber-50':''"><td class="p-2"><p x-text="c.label"></p><button type="button" class="btn-secondary mt-2" :disabled="!regressionCurrent()" @click="await regressionReview(c.id)">Revisar inmueble</button></td><template x-for="key in ['y','fitted','residual','student','leverage','cook']" :key="key"><td class="p-2" x-text="regressionNumber(c[key])"></td></template><td class="p-2" x-text="c.flags.join(' · ') || 'Sin señales'"></td></tr></template>
        </tbody></table></div>
        <button type="button" class="btn-secondary" :disabled="!regressionCurrent()" @click="regressionExport()">Descargar informe con gráficos y diagnósticos</button>
    </div></template>
</div></template>
