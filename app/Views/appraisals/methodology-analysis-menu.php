<?php
$marketMenu = [
    ['1. Preparar los datos', "['preparation','samples','location'].includes(analysisModule)", "analysisModule='preparation'"],
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
<p class="mt-3 text-sm" x-text="analysisRows.length+' inmuebles recogidos · '+analysisActiveRows().length+' en el grupo actual'"></p>
<p class="text-sm">El total recogido no equivale a las filas completas del cálculo. Cada procedimiento informa sus disponibles y pendientes; cambiar de menú conserva las muestras.</p>
