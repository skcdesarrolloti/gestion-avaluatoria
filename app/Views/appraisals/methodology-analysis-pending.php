<?php
$pendingStages = [
    'validation'=>['5. Validar las predicciones', ['Protocolo','Evaluación dentro y fuera de muestra','Métricas','Segmentos','Estabilidad'], [
        'Definir qué se validará, cómo separar los datos y cómo evitar que la evaluación reutilice información del ajuste. Pendiente de acordar e implementar.',
        'Distinguir ajuste interno de evaluación con inmuebles no utilizados para construir el modelo. La validación fuera de muestra todavía no está implementada.',
        'Declarar la fórmula, unidad y conjunto de evaluación de cada medida de error. Los diagnósticos existentes informan ajuste interno, sin certificar predicción externa.',
        'Revisar desempeño por grupos pertinentes del mercado, con evidencia y suficientes observaciones. Pendiente de desarrollo.',
        'Estudiar cuánto cambian las conclusiones al variar datos, fechas o especificaciones justificadas. Pendiente de desarrollo.',
    ]],
    'subject'=>['6. Aplicar al sujeto', ['Atributos','Compatibilidad','Extrapolación','Predicción e intervalos','Contraste'], [
        'Verificar los atributos ya registrados del sujeto y su correspondencia con las variables del modelo. Esta pantalla no los modifica.',
        'Comprobar si tipo, fecha, ubicación, unidades y características permiten aplicar el modelo al sujeto. Pendiente de desarrollo.',
        'Identificar aplicación fuera del ámbito observado: rangos, categorías y mercado. Todavía no hay comprobación automática.',
        'Estimar el sujeto y distinguir incertidumbre de la media estimada de un intervalo de predicción individual. Cálculo todavía no implementado.',
        'Contrastar el resultado con evidencia de mercado y otros métodos pertinentes, documentando razones y límites. Pendiente de desarrollo.',
    ]],
];
?>
<?php foreach ($pendingStages as $key=>[$title,$steps,$explanations]): ?>
<template x-if="analysisModule==='<?= e($key) ?>'"><section class="space-y-4">
    <h3 class="text-xl font-semibold"><?= e($title) ?></h3>
    <p class="rounded-lg border bg-amber-50 p-3"><strong>Etapa pendiente de desarrollo.</strong> Sus entradas ya están organizadas para trabajarlas una por una. Aquí no se ejecutan cálculos ni se adopta un valor.</p>
    <nav class="flex flex-wrap gap-2" aria-label="Submenús de <?= e($title) ?>">
        <?php foreach ($steps as $i=>$step): ?>
        <button type="button" class="btn-secondary" :aria-current="analysisPendingStep===<?= $i ?>?'step':null" @click="analysisPendingStep=<?= $i ?>"><?= e(($key==='validation'?'5.':'6.').($i+1).' '.$step) ?></button>
        <?php endforeach; ?>
    </nav>
    <?php foreach ($explanations as $i=>$explanation): ?>
    <div x-show="analysisPendingStep===<?= $i ?>" class="rounded-xl border p-4"><h4 class="font-semibold"><?= e($steps[$i]) ?></h4><p class="mt-3"><?= e($explanation) ?></p></div>
    <?php endforeach; ?>
</section></template>
<?php endforeach; ?>
