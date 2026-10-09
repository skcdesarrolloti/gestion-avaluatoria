<?php
$marketMenu = [
    ['1. Preparar los datos', "['preparation','samples','location'].includes(analysisModule)", "analysisModule='samples'"],
    ['2. Entender la muestra', "analysisModule==='statistics'&&courseStep!==6", "analysisModule='statistics';courseStep=0"],
    ['3. Construir el modelo', "analysisModule==='regression'&&regressionTab==='application'", "analysisModule='regression';regressionTab='application'"],
    ['4. Revisar el modelo', "analysisModule==='regression'&&regressionTab==='diagnostics'", "analysisModule='regression';regressionTab='diagnostics'"],
    ['5. Validar las predicciones', "analysisModule==='validation'", "analysisModule='validation';analysisPendingStep=0"],
    ['6. Aplicar al sujeto', "analysisModule==='subject'", "analysisModule='subject';analysisPendingStep=0"],
    ['7. Memoria y sustentación', "analysisModule==='statistics'&&courseStep===6", "analysisModule='statistics';courseStep=6"],
];
?>
<p class="mb-3 font-semibold text-teal-900" role="status" aria-live="polite">Estás en: M4 Análisis de mercado<?= !empty($componentLabel) ? ' · '.e($componentLabel) : '' ?> → <span x-text="analysisModule==='statistics' ? (courseStep===6?'Memoria y sustentación':'Entender la muestra → '+courseSteps[courseStep]) : analysisModule==='regression' ? (regressionTab==='diagnostics'?'Revisar el modelo':'Construir el modelo') : analysisModule==='validation' ? 'Validar las predicciones' : analysisModule==='subject' ? 'Aplicar al sujeto' : 'Preparar los datos'"></span></p>
<nav class="grid gap-2 sm:grid-cols-2 lg:grid-cols-4" aria-label="Etapas de M4 Análisis de mercado">
    <?php foreach ($marketMenu as [$title,$active,$action]): ?>
        <button type="button" class="btn-secondary text-left" :aria-current="<?= e($active) ?>?'page':null" :class="<?= e($active) ?>?'bg-teal-50 ring-2 ring-teal-700 font-bold':''" @click="<?= e($action) ?>"><?= e($title) ?></button>
    <?php endforeach; ?>
</nav>
<p class="mt-3 font-semibold text-teal-900" role="status" x-text="analysisRegimeApplied?'Grupo de trabajo aplicado: '+analysisWorkingCount()+' inmuebles':'Preparación del grupo: pendiente de aplicar'"></p>
<p class="text-sm" x-show="analysisRegimeApplied">Se conserva la preparación guardada. Consultar antecedentes no reaplica filtros ni reincorpora muestras.</p>
<details class="mt-3 rounded-lg border p-3">
    <summary class="min-h-11 cursor-pointer font-semibold">Consultar antecedentes · total recogido e historial</summary>
    <p class="my-3 text-sm" x-text="analysisRows.length+' inmuebles recogidos · '+analysisWorkingCount()+' en el grupo aplicado · '+(analysisRows.length-analysisWorkingCount())+' fuera de ese grupo, conservados'"></p>
    <?php require __DIR__.'/methodology-analysis-statistics.php'; ?>
    <?php require __DIR__.'/methodology-preparation-received.php'; ?>
</details>
