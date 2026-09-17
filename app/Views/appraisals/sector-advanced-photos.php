<?php
$photoSections = [
    '02' => [
        ['sector:mapa-delimitacion', 'Soporte cartográfico', 'Figura 1 · Mapa delimitado',
            'Pega con Ctrl+V o sube el perímetro, mapa oficial o soporte cartográfico.',
            'Ej. Delimitación del barrio ' . ($neighborhoodLabel ?: 'seleccionado')],
        ['sector:imagen-satelital', 'Soporte cartográfico', 'Figura 2 · Imagen satelital',
            'Pega con Ctrl+V o sube la imagen satelital que soporte la lectura espacial.',
            'Ej. Imagen satelital del barrio ' . ($neighborhoodLabel ?: 'seleccionado')],
    ],
    '06' => [
        ['sector:mapa-vias', 'Soporte vial', 'Figura 3 · Mapa vial',
            'Pega con Ctrl+V o sube vías principales, accesos o señalización.',
            'Ej. Vías y accesos del barrio ' . ($neighborhoodLabel ?: 'seleccionado')],
    ],
    '14' => [
        ['sector:registro-campo', 'Soporte de campo', 'Figura 4 · Registro de campo',
            'Pega con Ctrl+V o sube fotos de visita, equipamientos, externalidades o evidencias.',
            'Ej. Evidencia de campo del sector'],
    ],
];
$photoRows = $photoSections[(string) $sectionCode] ?? [];
?>
<?php if ($photoRows): ?>
    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <?php foreach ($photoRows as [$caption, $eyebrow, $title, $description, $placeholder]): ?>
            <?php
            $embedded = true; $photoUploadEmbedded = true; $photoUploadCompact = true;
            $subjectActionBase = 'avaluos/' . $record['id'] . '/sector';
            $photoUploadReturnTo = 'avaluos/' . $record['id'] . '/sector#banco-' . $sectionCode;
            $photoUploadCaption = $caption;
            $photoUploadEyebrow = $eyebrow;
            $photoUploadTitle = $title;
            $photoUploadDescription = $description;
            $photoUploadNamePlaceholder = $placeholder;
            require BASE_PATH . '/app/Views/appraisals/photo-upload.php';
            ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
