<template x-if="analysisModule==='regression'"><section class="space-y-4">
    <h3 class="text-xl font-semibold" x-text="regressionTab==='diagnostics'?'4. Revisar el modelo':'3. Construir el modelo'"></h3>
    <p class="text-sm" x-show="regressionTab==='application'">Con el grupo preparado: elige el valor a explicar, confirma los factores y calcula la regresión múltiple.</p>

    <div x-show="regressionTab==='application'" class="space-y-4">
        <p class="rounded-lg bg-purple-50 p-3 text-purple-900" x-show="analysisHasSimulated()">Ejercicio con datos simulados. Los resultados deben conservar esta identificación en el entregable.</p>
        <p x-text="'Factores aplicados: '+regressionColumns().map(f=>f.label).join(' + ')"></p>
        <p class="rounded-lg bg-amber-50 p-3" x-show="!analysisRegimeApplied || analysisScope!==analysisAppliedScope">Primero prepara el grupo en 1. Preparar los datos. La selección de factores conserva ese grupo.</p>
        <p class="text-red-700" role="alert" x-show="analysisError" x-text="analysisError"></p>
        <details class="rounded-lg border p-3"><summary class="min-h-11 cursor-pointer font-semibold">Cambiar factores · opcional</summary>
            <?php require __DIR__.'/methodology-regression-combinations.php'; ?>
            <?php require __DIR__.'/methodology-regression-factor-selection.php'; ?>
        </details>
        <label class="label">1. Valor que queremos explicar (Y)<select class="input" x-model="regressionBasis" @change="regressionConfirmed=false"><option value="offer">Oferta / área publicada · exploratorio sin descuento</option><option value="adjusted">Valor con descuento / área publicada · solo descuentos registrados</option></select></label>
        <p class="text-sm">Se usan los factores preparados. Las filas sin valores numéricos quedan pendientes, conservadas en el grupo.</p>
        <div class="rounded-xl border p-4">
            <h4 class="font-semibold">2. Confirmar los factores</h4>
            <details x-show="regressionCategories().length" :open="regressionCategories().length>0"><summary class="min-h-11 cursor-pointer font-semibold">Codificar textos y rangos pendientes</summary>
            <p class="text-sm">Asigna un número sustentado por categoría. El código no convierte un rango de antigüedad en años exactos; consulta el ejemplo en Ayuda y sustento del modelo.</p>
            <template x-for="(category,i) in regressionCategories()" :key="category.key"><label class="label mt-3"><span x-text="category.factor+' · '+category.value"></span><input class="input" :id="'regression-code-'+i" type="number" min="0" step="any" placeholder="Código numérico confirmado, ej. 2" x-model="regressionCodes[category.key]" @input="regressionConfirmed=false"></label></template>
            </details>
            <label class="flex min-h-11 items-center gap-2 mt-3"><input type="checkbox" x-model="regressionConfirmed">Confirmé la codificación, sus unidades y la pertinencia del tratamiento lineal.</label>
        </div>
        <p role="status" x-text="regressionMatrix().complete.length+' filas numéricas completas de '+regressionMatrix().rows.length+' · '+regressionColumns().length+' factores · '+Math.max(30,regressionColumns().length*10)+' filas necesarias'"></p>
        <p class="text-sm">Se utilizan todas las filas completas. El mínimo indicado es un criterio del ejercicio, no una garantía de calidad.</p>
        <details x-show="regressionMatrix().rows.some(r=>r.reasons.length)" class="rounded-lg border p-3"><summary class="min-h-11 cursor-pointer font-semibold">¿Qué falta en las muestras pendientes?</summary><template x-for="r in regressionMatrix().rows.filter(r=>r.reasons.length)" :key="r.id"><div class="mt-3"><p x-text="r.label+' · '+r.reasons.join(' · ')"></p><button type="button" class="btn-secondary" @click="await regressionPending(r)" x-text="r.codeKeys.length?'Completar codificación':'Completar este inmueble'"></button></div></template></details>
        <h4 class="font-semibold">3. Calcular y revisar el resultado</h4>
        <button type="button" class="btn-primary" :disabled="regressionBusy || !regressionConfirmed" @click="await regressionRun()" x-text="regressionBusy?'Calculando regresión…':'Calcular regresión'"></button>
        <p class="text-red-700" role="alert" x-show="regressionError" x-text="regressionError"></p>
        <div x-show="regressionResult" class="rounded-xl border p-4 space-y-3">
            <p role="status" x-show="!regressionCurrent()" class="text-amber-900">Los datos o la selección cambiaron. Recalcula antes de exportar.</p>
            <p x-text="regressionResult?'n='+regressionResult.n+' · R²='+regressionResult.r2.toFixed(4)+' · R² ajustado='+regressionResult.adjustedR2.toFixed(4)+' · Error residual='+regressionResult.rmse.toFixed(2)+' COP/m²':''"></p>
            <p class="font-semibold" x-text="regressionEquation()"></p>
            <p class="text-sm">Resultado en COP/m². Antigüedad representa la categoría codificada, no años exactos. El descuento se usa para calcular el valor final; no cuenta como factor.</p>
            <details><summary class="min-h-11 cursor-pointer font-semibold">Detalle de coeficientes y colinealidad (VIF)</summary><div class="overflow-auto"><table class="min-w-full text-left"><thead><tr><th class="p-2">Variable</th><th class="p-2">Coeficiente</th><th class="p-2">VIF</th></tr></thead><tbody><template x-for="(coefficient,i) in (regressionResult?.coefficients || [])" :key="i"><tr><td class="p-2" x-text="i?regressionResult.matrix.cols[i-1].label:'Intercepto'"></td><td class="p-2" x-text="coefficient.toFixed(4)"></td><td class="p-2" x-text="i?regressionResult.vif[i-1].toFixed(3):'—'"></td></tr></template></tbody></table></div></details>
            <p class="text-sm">Resultado exploratorio. Revisa distribución, residuos e influencia en «4. Revisar el modelo» antes de interpretar o aplicar al sujeto.</p>
            <button type="button" class="btn-primary" :disabled="!regressionCurrent()" @click="regressionTab='diagnostics'">Ver gráficos y revisar datos atípicos</button>
            <button type="button" class="btn-secondary" :disabled="!regressionCurrent()" @click="regressionExport()">Descargar informe de regresión para el entregable</button>
        </div>
        <p class="text-sm">La codificación se guarda como preparación. El cálculo se ejecuta al pulsar el botón; descarga su informe para conservar esta ejecución.</p>
    </div>
    <details class="rounded-xl border p-3"><summary class="min-h-11 cursor-pointer font-semibold">Ayuda y sustento del modelo · ejemplos, fórmulas y fuentes</summary><div class="mt-3 space-y-3">
        <?php require __DIR__.'/methodology-regression-academy.php'; ?>
        <?php require __DIR__.'/methodology-regression-formulas.php'; ?>
    </div></details>
    <?php require __DIR__.'/methodology-regression-diagnostics.php'; ?>
</section></template>
