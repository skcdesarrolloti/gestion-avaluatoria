<template x-if="analysisModule==='statistics'"><section class="space-y-4" @input.stop @change.stop>
    <h3 class="text-xl font-semibold">Análisis estadístico · explicación y resultados paso a paso</h3>
    <p class="rounded-lg bg-purple-50 p-3 text-purple-900">Ejercicio exploratorio. Los datos simulados conservan su identificación; este recorrido no adopta un valor ni elimina muestras.</p>
    <p class="text-sm">Seguimos la sesión 1 del curso: preparar → tendencia central → dispersión → precisión → conclusión. Los bloques y diagnósticos robustos complementan ese recorrido. Cada paso reúne explicación, fórmula, resultado e interpretación.</p>
    <?php require __DIR__.'/methodology-course-sources.php'; ?>
    <nav class="flex flex-wrap gap-2" aria-label="Pasos del análisis estadístico"><template x-for="(step,i) in courseSteps" :key="i"><button type="button" class="btn-secondary" :aria-current="courseStep===i?'step':null" :class="courseStep===i?'bg-teal-50 ring-2 ring-teal-700 font-bold':''" @click="courseStep=i" x-text="(i+1)+'. '+step"></button></template></nav>
    <?php require __DIR__.'/methodology-course-academy.php'; ?>
    <details class="rounded-xl border p-4 space-y-3" :open="!courseResult"><summary class="min-h-11 cursor-pointer font-semibold">Base y confianza del cálculo · revisar o cambiar</summary>
        <label class="label">Base del ejercicio<select class="input" x-model="courseBasis"><option value="adjusted">Valor con descuento / área publicada · preliminar</option><option value="offer">Oferta / área publicada · exploración sin descuento</option></select></label>
        <label class="label">Confianza para el intervalo de la media<select class="input" x-model="courseConfidence"><option value=".90">90 %</option><option value=".95">95 %</option><option value=".99">99 %</option></select></label>
        <p class="text-sm">Se usan todos los valores disponibles del grupo aplicado, aunque falten otros factores. Para PH, confronta área privada y componentes en su sección antes de interpretar este cociente preliminar.</p>
    </details>
    <div class="space-y-3">
        <button type="button" class="btn-primary" :disabled="courseBusy||courseBootstrapBusy" @click="await courseCalculate()" x-text="courseBusy?'Calculando estadística…':courseResult?'Actualizar análisis estadístico':'Calcular análisis estadístico'"></button>
        <p role="alert" class="text-red-700" x-show="courseError" x-text="courseError"></p>
    </div>
    <p x-show="!courseResult">Aplica la depuración de muestras y pulsa Calcular análisis estadístico. Se mostrarán las disponibles y el motivo de cada pendiente.</p>
    <template x-if="courseResult"><div class="space-y-4">
        <p class="rounded-xl border p-3" role="status" x-text="courseResult.rows.length+' muestras conservadas · '+courseResult.valid.length+' valores unitarios disponibles · '+courseResult.pending.length+' pendientes · '+courseResult.at"></p>
        <p class="text-sm" x-text="'Base aplicada: '+(courseResult.basis==='adjusted'?'valor con descuento / área publicada':'oferta / área publicada')+' · confianza '+100*courseResult.summary.confidence+' %'"></p>
        <p class="text-amber-900" x-show="!courseCurrent()">La base, confianza o datos cambiaron. Actualiza antes de interpretar o descargar; estas cifras corresponden a la ejecución anterior.</p>
        <section class="rounded-xl border bg-teal-50 p-4 space-y-3" aria-label="Interpretación de los datos de este paso" tabindex="-1">
            <h4 class="font-semibold">Qué dicen tus datos en este paso</h4>
            <p><strong>Resultado observado: </strong><span x-text="courseInterpretation().reading"></span></p>
            <p><strong>Qué significa: </strong><span x-text="courseInterpretation().meaning"></span></p>
            <p><strong>Qué hacer ahora: </strong><span x-text="courseInterpretation().next"></span></p>
            <p class="text-sm" x-show="courseResult.simulated">Lectura del ejercicio con datos simulados; no constituye un dictamen de mercado.</p>
        </section>
        <?php require __DIR__.'/methodology-course-preparation.php'; ?>
        <?php require __DIR__.'/methodology-course-descriptive.php'; ?>
        <?php require __DIR__.'/methodology-course-precision.php'; ?>
        <div class="flex flex-wrap gap-2"><button type="button" class="btn-secondary" :disabled="courseStep===0" @click="courseStep--">Paso anterior</button><button type="button" class="btn-primary" :disabled="courseStep===6" @click="courseStep++">Siguiente paso</button><button type="button" class="btn-secondary" :disabled="!courseCurrent()||courseBootstrapBusy" @click="courseExport()">Descargar memoria en este orden</button></div>
    </div></template>
</section></template>
