<div class="mt-6 grid gap-4 lg:grid-cols-2">
    <?php
    $embedded = true; $photoUploadEmbedded = true; $photoUploadCompact = true;
    $subjectActionBase = 'avaluos/' . $record['id'] . '/sector';
    $photoUploadReturnTo = 'avaluos/' . $record['id'] . '/sector#localizacion';
    $photoUploadCaption = 'sector:mapa-delimitacion';
    $photoUploadEyebrow = 'Soporte gráfico';
    $photoUploadTitle = 'Figura 1 · Mapa delimitado';
    $photoUploadDescription = 'Pega con Ctrl+V o sube la captura del perímetro, mapa oficial o soporte cartográfico del barrio.';
    $photoUploadNamePlaceholder = 'Ej. Delimitación del barrio ' . ($neighborhoodLabel ?: 'seleccionado');
    require BASE_PATH . '/app/Views/appraisals/photo-upload.php';
    $photoUploadCaption = 'sector:imagen-satelital';
    $photoUploadTitle = 'Figura 2 · Imagen satelital';
    $photoUploadDescription = 'Pega con Ctrl+V o sube la imagen satelital que soporte la lectura espacial del sector.';
    $photoUploadNamePlaceholder = 'Ej. Imagen satelital del barrio ' . ($neighborhoodLabel ?: 'seleccionado');
    require BASE_PATH . '/app/Views/appraisals/photo-upload.php';
    $photoUploadCaption = 'sector:mapa-vias';
    $photoUploadTitle = 'Figura 3 · Mapa vial';
    $photoUploadDescription = 'Pega con Ctrl+V o sube la captura con vías principales, accesos, corredores o señalización relevante.';
    $photoUploadNamePlaceholder = 'Ej. Vías y accesos del barrio ' . ($neighborhoodLabel ?: 'seleccionado');
    require BASE_PATH . '/app/Views/appraisals/photo-upload.php';
    $photoUploadCaption = 'sector:registro-campo';
    $photoUploadTitle = 'Figura 4 · Registro de campo';
    $photoUploadDescription = 'Pega con Ctrl+V o sube fotos de visita, equipamientos, externalidades, entorno inmediato o evidencias del sector.';
    $photoUploadNamePlaceholder = 'Ej. Evidencia de campo del sector';
    require BASE_PATH . '/app/Views/appraisals/photo-upload.php';
    ?>
</div>
