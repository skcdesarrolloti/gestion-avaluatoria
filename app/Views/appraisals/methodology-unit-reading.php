<?php
$isAnnexReading = ($unit['unit_kind'] ?? '') === 'annex';
$unitStructure = (string) ($unit['method_structure'] ?? '');
$isLandReading = $type === 'lote' || $unitStructure === 'solo_terreno';
$contextArticles = [];
?>
<section class="mt-4 rounded-lg border bg-white p-3 text-sm leading-6">
    <h4 class="font-semibold">Qué revisar para <?= e($component['label']) ?></h4>
    <?php if ($isAnnexReading || $unitStructure === 'solo_construccion'): $contextArticles = [27, 28, 29, 30]; ?>
        <p class="mt-2">Comprueba si es una mejora constructiva (placa vial, cerramiento u otra), sus cantidades, unidad de medida, materiales, edad, estado y vínculo con el terreno. No la trates como otro lote ni le asignes otra vez el valor del suelo.</p>
        <p class="mt-2">Si corresponde valorar una construcción o mejora por separado, consulta los arts. 27–30 para evaluar Costo: reposición o reproducción, vida útil y depreciación. Esta es orientación académica; no cambia el método ni calcula valores. Verifica antes si el componente ya está incluido en otro valor y, si es PH, aplica las reglas jurídicas de abajo.</p>
    <?php elseif ($isLandReading): $contextArticles = [19, 31, 32, 33, 34]; ?>
        <p class="mt-2">Para el terreno, investiga comparables de suelo con uso permitido, ubicación, área, servicios y posibilidades de aprovechamiento semejantes. Separa el estudio de las mejoras registradas y documenta qué comprende cada precio (art. 19).</p>
        <p class="mt-2"><strong>Un lote grande no obliga a usar Residual.</strong> Evalúa esa alternativa según el potencial legal, físico y económico de desarrollo, el mayor y mejor uso y la información disponible. Los arts. 31–33 explican sus condiciones, técnicas e insumos; el 34 tiene condiciones propias para terrenos en bruto. No basta el área para decidir. Aquí solo consultas la academia.</p>
    <?php elseif ($orientationPh === 'si'): ?>
        <p class="mt-2">Comprueba el área privada, uso aprobado, estado, matrícula, coeficientes y componentes incluidos en el precio. Revisa por separado el tratamiento del inmueble sujeto (art. 36) y la depuración de sus comparables (art. 19.2), explicados a continuación.</p>
    <?php else: $contextArticles = [18, 19]; ?>
        <p class="mt-2">Comprueba área, uso, estado, régimen jurídico y qué incluye el precio de esta unidad. Para una casa u otro inmueble NPH con terreno y construcción, revisa la desagregación del art. 19.1 y las condiciones del análisis integral complementario del art. 18; sustenta la relación entre sus áreas y evita duplicar terreno o mejoras.</p>
    <?php endif; ?>
    <?php if (!in_array($orientationPh, ['si', 'no'], true)): ?>
        <p class="mt-2 font-semibold">El régimen no está confirmado. Verifica los documentos antes de aplicar la ruta PH o NPH.</p>
        <?php $contextArticles[] = 36; ?>
    <?php endif; ?>
    <?php foreach (array_unique($contextArticles) as $readingNumber): require __DIR__ . '/valuation-methodology-article-reading.php'; endforeach; ?>
</section>
