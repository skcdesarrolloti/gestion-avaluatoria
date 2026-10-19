<?php
$preparationLabels = \App\Support\AppraisalCatalog::selectFields();
$preparationFields = ['finalidad'=>'Finalidad del informe','base_valor'=>'Base de valor','tipo_derecho'=>'Derecho valorado','tipo_negocio'=>'Venta o arriendo','tipo_inmueble'=>'Tipo del componente'];
$preparationPending = [];
foreach ($preparationFields as $field=>$label) {
    $value = (string) ($analysisContext[$field] ?? '');
    if (!isset($preparationLabels[$field][4][$value])) $preparationPending[] = $label;
}
$preparationDate = trim((string) ($analysisContext['value_date'] ?? ''));
if ($preparationDate === '') $preparationPending[] = 'Fecha de valor';
?>
<section class="mt-4 space-y-4" aria-label="Objetivo y unidad de análisis">
    <h3 class="text-xl font-semibold">1. Preparar la muestra</h3>
    <p>Antes de estudiar la dispersión o construir una regresión, precisamos el encargo y la unidad que representa cada observación. Este paso consulta la información ya registrada en la ficha.</p>
    <div class="rounded-xl border p-4 space-y-3">
        <h4 class="text-lg font-semibold">1.1 Objetivo y unidad de análisis</h4>
        <dl class="grid gap-4 sm:grid-cols-2">
            <div><dt class="font-semibold">Componente que estamos estudiando</dt><dd class="break-words"><?= e($componentLabel ?? 'Componente sin identificar') ?></dd></div>
            <div><dt class="font-semibold">Fecha de valor del encargo</dt><dd><?= e($preparationDate !== '' ? $preparationDate : 'Pendiente de registrar') ?></dd></div>
            <?php foreach ($preparationFields as $field=>$label): ?>
            <div><dt class="font-semibold"><?= e($label) ?></dt><dd><?= e($preparationLabels[$field][4][$analysisContext[$field] ?? ''] ?? 'Pendiente de registrar o revisar') ?></dd></div>
            <?php endforeach; ?>
            <div><dt class="font-semibold">Unidad de observación</dt><dd>Un inmueble por grupo de anuncios confirmado o seleccionado en Captura y consolidación. Se utiliza su anuncio principal; los demás se conservan como fuentes.</dd></div>
        </dl>
        <?php if ($preparationPending !== []): ?><p class="rounded-lg bg-amber-50 p-3"><strong>Información pendiente:</strong> <?= e(implode(' · ', $preparationPending)) ?>. Complétala en la ficha del encargo antes de sustentar una conclusión.</p><?php endif; ?>
        <p class="text-sm text-slate-600">La finalidad, el derecho, la base y la fecha proceden del encargo general. El tipo se consulta en el contexto del componente actual. Esta consulta no confirma por sí sola la comparabilidad de las muestras.</p>
    </div>
    <div class="rounded-xl border p-4 space-y-3">
        <h4 class="font-semibold">Qué dejamos preparado</h4>
        <p>El grupo de inmuebles, sus fuentes, precio, área, descuento sustentado, componentes y atributos se revisan en Grupo preparado. La localización se atiende en Ubicación y mapa. Una oferta publicada no acredita un precio de cierre; el área debe corresponder al precio y a los componentes estudiados.</p>
        <p class="text-sm"><strong>Para el paso 2:</strong> se utilizarán los valores disponibles de este grupo para describir sus precios. <strong>Para el paso 3:</strong> se reutilizarán sus atributos al definir la variable que se quiere explicar (Y) y los factores explicativos (X). No necesitas repetir la captura o aplicar de nuevo la selección para continuar.</p>
    </div>
    <details class="rounded-xl border p-4">
        <summary class="min-h-11 cursor-pointer font-semibold">Acompañamiento académico · por qué empezamos aquí</summary>
        <p class="mt-3">La preparación conserva la identidad, las fuentes y los motivos de selección de cada inmueble. Compartir régimen o tener una fila completa no demuestra comparabilidad: la decisión requiere evidencia sobre su pertinencia frente al sujeto.</p>
        <p class="mt-3 text-sm">Referencia de estudio: Sesión 1, preparación y distribución; Sesión 2, robustez y depuración; Sesión 3, modelación y validación. El marco normativo del expediente se consulta en Academia y en las advertencias de Análisis.</p>
    </details>
    <p class="text-sm">Consulta Grupo preparado para utilizar la selección guardada y Ubicación y mapa para atender localizaciones pendientes. La captura se realiza una sola vez en Captura y consolidación.</p>
</section>
