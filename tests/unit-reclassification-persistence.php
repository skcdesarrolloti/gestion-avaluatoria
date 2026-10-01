<?php
declare(strict_types=1);
$reclassId = $repo->create(1);
$app->prepare('UPDATE appraisals SET igac_property_units_count = 2, igac_annex_units_count = 1 WHERE id = ?')->execute([$reclassId]);
$repo->ensureUnits($reclassId, 1, 2, 2);
$all = $repo->units($reclassId, 1);
$source = array_values(array_filter($all, fn ($u) => $u['unit_kind'] === 'property' && (int) $u['unit_index'] === 2))[0];
$app->prepare('UPDATE appraisal_units SET label = "Anexo 2", construction_type = "cerramiento", notes = "Conservar detalle" WHERE id = ?')->execute([$source['id']]);
$oldVersion = (int) $repo->find($reclassId, 1)['version'];
$photoId = bin2hex(random_bytes(16));
$repo->addPhoto($reclassId, 1, ['id' => $photoId, 'unit_id' => $source['id'], 'source_filename' => 'test.png',
    'storage_filename' => 'test-' . $photoId . '.png', 'mime_type' => 'image/png', 'file_size_bytes' => 1,
    'caption' => 'Prueba', 'display_name' => 'Soporte prueba', 'file_blob' => 'x']);
expectStatus(404, fn () => $repo->convertUnitToAnnex($reclassId, 2, $source['id'], $oldVersion), 'reclasificación rechaza propietario ajeno');
expectStatus(409, fn () => $repo->convertUnitToAnnex($reclassId, 1, $source['id'], $oldVersion - 1), 'reclasificación rechaza versión antigua');
$repo->convertUnitToAnnex($reclassId, 1, $source['id'], $oldVersion);
$converted = array_values(array_filter($repo->units($reclassId, 1), fn ($u) => $u['id'] === $source['id']))[0];
expect($converted['unit_kind'] === 'annex' && (int) $converted['unit_index'] === 2 && $converted['notes'] === 'Conservar detalle', 'anexo conserva ID y datos al reclasificar');
expect($repo->findPhoto($photoId, 1)['unit_id'] === $source['id'], 'foto conserva vínculo al componente reclasificado');
$counts = $repo->find($reclassId, 1);
expect((int) $counts['igac_property_units_count'] === 1 && (int) $counts['igac_annex_units_count'] === 2, 'reclasificación ajusta cantidades activas');
$q = $app->prepare('SELECT COUNT(*) FROM appraisal_units WHERE appraisal_id = ?'); $q->execute([$reclassId]);
expect((int) $q->fetchColumn() === 4, 'reclasificación conserva también unidades inactivas');
