<template x-if="analysisModule==='statistics'"><section class="space-y-4" @input.stop @change.stop>
    <h3 class="text-xl font-semibold"><span x-text="courseStep===6 ? '7. Memoria y sustentación' : '2. Entender la muestra'"></span></h3>
    <p class="rounded-lg bg-purple-50 p-3 text-purple-900">Ejercicio exploratorio. Los datos simulados conservan su identificación; este recorrido no adopta un valor ni elimina muestras.</p>
    <p x-show="courseStep===6" class="rounded-lg border bg-amber-50 p-3">Memoria disponible del análisis descriptivo. La sustentación integral, manual del modelo, validación y anexos del informe final siguen pendientes. Las notas de esta ejecución se conservan mediante su descarga.</p>
    <nav x-show="courseStep!==6" class="flex flex-wrap gap-2" aria-label="Pasos del análisis estadístico"><template x-for="(step,i) in courseSteps" :key="i"><button type="button" class="btn-secondary" x-show="i!==6" :aria-current="courseStep===i?'step':null" :class="courseStep===i?'bg-teal-50 ring-2 ring-teal-700 font-bold':''" @click="courseStep=i" x-text="(i+1)+'. '+step"></button></template></nav>
    <details class="rounded-xl border p-3"><summary class="min-h-11 cursor-pointer font-semibold">Academia y fundamentos · explicación del paso actual</summary>
        <?php require __DIR__.'/methodology-course-academy.php'; ?>
        <?php require __DIR__.'/methodology-course-sources.php'; ?>
    </details>
    <details class="rounded-xl border p-4 space-y-3" :open="!courseResult"><summary class="min-h-11 cursor-pointer font-semibold">Base y confianza del cálculo · revisar o cambiar</summary>
        <label class="label">Base del ejercicio<select class="input" x-model="courseBasis"><option value="adjusted">Valor con descuento / área publicada · preliminar</option><option value="offer">Oferta / área publicada · exploración sin descuento</option></select></label>
        <label class="label">Confianza para el intervalo de la media<select class="input" x-model="courseConfidence"><option value=".90">90 %</option><option value=".95">95 %</option><option value=".99">99 %</option></select></label>
        <p class="text-sm">Se usan todos los valores disponibles del grupo aplicado, aunque falten otros factores. Para PH, confronta área privada y componentes en su sección antes de interpretar este cociente preliminar.</p>
    </details>
    <div class="space-y-3">
        <button type="button" class="btn-primary" :disabled="courseBusy||courseBootstrapBusy" @click="await courseCalculate()" x-text="courseBusy?'Calculando estadística…':courseResult?'Actualizar análisis estadístico':'Calcular análisis estadístico'"></button>
        <p role="alert" class="text-red-700" x-show="courseError" x-text="courseError"></p>
    </div>
    <p x-show="!courseResult" class="text-sm">Pulsa Calcular análisis estadístico. Se utiliza el grupo guardado; no necesitas repetir su preparación.</p>
    <template x-if="courseResult"><div class="space-y-4">
        <p class="rounded-xl border p-3" role="status" :title="'Calculado: '+courseResult.at" x-text="courseResult.rows.length+' muestras · '+courseResult.valid.length+' valores disponibles · '+courseResult.pending.length+' pendientes'"></p>
        <p class="text-sm" x-text="'Base aplicada: '+(courseResult.basis==='adjusted'?'valor con descuento / área publicada':'oferta / área publicada')+' · confianza '+100*courseResult.summary.confidence+' %'"></p>
        <p class="text-amber-900" x-show="!courseCurrent()">La base, confianza o datos cambiaron. Actualiza antes de interpretar o descargar; estas cifras corresponden a la ejecución anterior.</p>
        <details class="rounded-xl border bg-teal-50 p-3 space-y-3" aria-label="Interpretación de los datos de este paso" tabindex="-1">
            <summary class="min-h-11 cursor-pointer font-semibold">Qué significa el resultado · explicación y siguiente paso</summary>
            <p><strong>Resultado observado: </strong><span x-text="courseInterpretation().reading"></span></p>
            <p><strong>Qué significa: </strong><span x-text="courseInterpretation().meaning"></span></p>
            <p><strong>Qué hacer ahora: </strong><span x-text="courseInterpretation().next"></span></p>
            <p class="text-sm" x-show="courseResult.simulated">Lectura del ejercicio con datos simulados; no constituye un dictamen de mercado.</p>
        </details>
        <?php require __DIR__.'/methodology-course-preparation.php'; ?>
        <?php require __DIR__.'/methodology-course-descriptive.php'; ?>
        <?php require __DIR__.'/methodology-course-precision.php'; ?>
        <div x-show="courseStep!==6" class="flex flex-wrap gap-2"><button type="button" class="btn-secondary" :disabled="courseStep===0" @click="courseStep--">Paso anterior</button><button type="button" class="btn-primary" :disabled="courseStep>=5" @click="courseStep++">Siguiente paso</button><button type="button" class="btn-secondary" :disabled="!courseCurrent()||courseBootstrapBusy" @click="courseExport()">Descargar memoria en este orden</button></div>
    </div></template>
</section></template>
