<template x-if="analysisModule==='regression'"><section class="space-y-4">
    <h3 class="text-xl font-semibold">Modelo de regresión lineal múltiple</h3>
    <nav class="flex flex-wrap gap-2" aria-label="Pasos de la regresión">
        <button type="button" class="btn-secondary" :aria-current="regressionTab==='application'?'step':null" :class="regressionTab==='application'?'bg-teal-50 ring-2 ring-teal-700 font-bold':''" @click="regressionTab='application'">1. Preparar y calcular</button>
        <button type="button" class="btn-secondary" :aria-current="regressionTab==='diagnostics'?'step':null" :class="regressionTab==='diagnostics'?'bg-teal-50 ring-2 ring-teal-700 font-bold':''" @click="regressionTab='diagnostics'">2. Gráficos y datos atípicos</button>
    </nav>
    <?php require __DIR__.'/methodology-regression-academy.php'; ?>
    <details x-show="regressionTab==='application'" class="rounded-xl border p-4 space-y-3"><summary class="min-h-11 cursor-pointer font-semibold">Qué hacemos · fórmulas e interpretación del modelo</summary>
        <p>El modelo relaciona el valor por m² con los factores elegidos. Cada coeficiente mide el cambio asociado a un factor manteniendo los otros constantes; la asociación no demuestra causalidad.</p>
        <p class="font-mono">yᵢ = β₀ + β₁xᵢ₁ + … + βₖxᵢₖ + εᵢ</p>
        <p>y: oferta / área publicada, o valor con descuento / área publicada. x: factores numéricos del inmueble, con el área del modelo contada una sola vez. β₀: intercepto; β: coeficientes; ε: error.</p>
        <p class="font-mono">Valor con descuento = Oferta × (1 − descuento / 100)<br>y = Valor con descuento / área publicada<br>β̂ = (XᵀX)⁻¹Xᵀy · minimizar Σ(yᵢ − ŷᵢ)²<br>eᵢ = yᵢ − ŷᵢ<br>R² = 1 − SSE / SST<br>R² ajustado = 1 − (1 − R²)(n − 1)/(n − k − 1)<br>Error residual = √[SSE/(n − k − 1)]<br>VIFⱼ = 1/(1 − R²ⱼ)</p>
        <p>La fórmula matricial requiere rango completo; el cálculo usa QR para mayor estabilidad. Antes de interpretar, revisar linealidad, residuos, independencia, varianza del error, correlaciones y colinealidad. La normalidad de los errores afecta las inferencias e intervalos; no se declara validación por un R² alto.</p>
        <p>Regla de trabajo acordada: mínimo 3 factores y 30 filas completas, con 10 filas por factor. Es un criterio del ejercicio; no garantiza calidad estadística. Rangos de antigüedad requieren una codificación explícita. Un código ordinal supone un efecto lineal por escalón y no equivale a años exactos. Categorías sin orden requieren otro tratamiento, como variables indicadoras.</p>
        <a class="text-blue-700 underline" href="https://www.itl.nist.gov/div898/handbook/pmd/section1/pmd141.htm" target="_blank" rel="noopener">Fuente académica: NIST · mínimos cuadrados lineales</a>
    </details>
    <div x-show="regressionTab==='application'" class="space-y-4">
        <p class="rounded-lg bg-purple-50 p-3 text-purple-900" x-show="analysisHasSimulated()">Ejercicio con datos simulados. Los resultados deben conservar esta identificación en el entregable.</p>
        <p x-text="'Factores aplicados: '+regressionColumns().map(f=>f.label).join(' + ')"></p>
        <label class="label">Variable dependiente<select class="input" x-model="regressionBasis" @change="regressionConfirmed=false"><option value="offer">Oferta / área publicada · exploratorio sin descuento</option><option value="adjusted">Valor con descuento / área publicada · solo descuentos registrados</option></select></label>
        <p class="text-sm">Vacío no equivale a cero. Se usan los factores ya aplicados en Resultado depurado. Las filas sin valor numérico quedan pendientes; se conservan en la matriz original.</p>
        <div class="rounded-xl border p-4">
            <h4 class="font-semibold">Codificación de textos y rangos</h4>
            <p class="text-sm">Asigna un número sustentado por categoría. Ejemplo ordinal de antigüedad: 1–8 → 1; 9–15 → 2; 16–30 → 3; más de 30 → 4. Se interpreta como categoría, no como edad en años.</p>
            <template x-for="(category,i) in regressionCategories()" :key="category.key"><label class="label mt-3"><span x-text="category.factor+' · '+category.value"></span><input class="input" :id="'regression-code-'+i" type="number" min="0" step="any" placeholder="Código numérico confirmado, ej. 2" x-model="regressionCodes[category.key]" @input="regressionConfirmed=false"></label></template>
            <label class="flex min-h-11 items-center gap-2 mt-3"><input type="checkbox" x-model="regressionConfirmed">Confirmé la codificación, sus unidades y la pertinencia del tratamiento lineal.</label>
        </div>
        <p role="status" x-text="regressionMatrix().complete.length+' filas numéricas completas de '+regressionMatrix().rows.length+' · '+regressionColumns().length+' factores · '+Math.max(30,regressionColumns().length*10)+' filas necesarias'"></p>
        <p class="text-sm">Treinta es el mínimo del ejercicio, no el máximo. Todas las filas numéricas completas se utilizan. Las demás se conservan y se detallan abajo.</p>
        <details x-show="regressionMatrix().rows.some(r=>r.reasons.length)" class="rounded-lg border p-3"><summary class="min-h-11 cursor-pointer font-semibold">¿Qué falta en las muestras pendientes?</summary><template x-for="r in regressionMatrix().rows.filter(r=>r.reasons.length)" :key="r.id"><div class="mt-3"><p x-text="r.label+' · '+r.reasons.join(' · ')"></p><button type="button" class="btn-secondary" @click="await regressionPending(r)" x-text="r.codeKeys.length?'Completar codificación':'Completar este inmueble'"></button></div></template></details>
        <button type="button" class="btn-primary" :disabled="regressionBusy || !regressionConfirmed" @click="await regressionRun()" x-text="regressionBusy?'Calculando regresión…':'Calcular regresión'"></button>
        <p class="text-red-700" role="alert" x-show="regressionError" x-text="regressionError"></p>
        <div x-show="regressionResult" class="rounded-xl border p-4 space-y-3">
            <p role="status" x-show="!regressionCurrent()" class="text-amber-900">Los datos o la selección cambiaron. Recalcula antes de exportar.</p>
            <p x-text="regressionResult?'n='+regressionResult.n+' · R²='+regressionResult.r2.toFixed(4)+' · R² ajustado='+regressionResult.adjustedR2.toFixed(4)+' · Error residual='+regressionResult.rmse.toFixed(2)+' COP/m²':''"></p>
            <p class="font-semibold" x-text="regressionEquation()"></p>
            <p class="text-sm">Resultado en COP/m². Antigüedad representa la categoría codificada, no años exactos. El descuento se usa para calcular el valor final; no cuenta como factor.</p>
            <div class="overflow-auto"><table class="min-w-full text-left"><thead><tr><th class="p-2">Variable</th><th class="p-2">Coeficiente</th><th class="p-2">VIF</th></tr></thead><tbody><template x-for="(coefficient,i) in (regressionResult?.coefficients || [])" :key="i"><tr><td class="p-2" x-text="i?regressionResult.matrix.cols[i-1].label:'Intercepto'"></td><td class="p-2" x-text="coefficient.toFixed(4)"></td><td class="p-2" x-text="i?regressionResult.vif[i-1].toFixed(3):'—'"></td></tr></template></tbody></table></div>
            <p class="text-sm">Resultado exploratorio. Revisa distribución, residuos e influencia en «2. Gráficos y datos atípicos» antes de interpretar o aplicar al sujeto.</p>
            <button type="button" class="btn-primary" :disabled="!regressionCurrent()" @click="regressionTab='diagnostics'">Ver gráficos y revisar datos atípicos</button>
            <button type="button" class="btn-secondary" :disabled="!regressionCurrent()" @click="regressionExport()">Descargar informe de regresión para el entregable</button>
        </div>
        <p class="text-sm">La codificación se guarda como preparación. El cálculo se ejecuta al pulsar el botón; descarga su informe para conservar esta ejecución.</p>
    </div>
    <?php require __DIR__.'/methodology-regression-diagnostics.php'; ?>
</section></template>
