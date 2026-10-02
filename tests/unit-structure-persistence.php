<?php
declare(strict_types=1);
$structureId = $repo->create(1);
$app->prepare('UPDATE appraisals SET igac_property_units_count = 1, igac_annex_units_count = 1 WHERE id = ?')->execute([$structureId]);
$repo->ensureUnits($structureId, 1, 1, 1);
$inputStructure = ['label' => 'Cerramiento', 'construction_type' => 'cerramiento', 'notes' => 'Conservar',
    'method_structure' => 'solo_construccion'];
$rowsStructure = \App\Services\AppraisalUnitDefinitionInput::rows(['annex-1' => $inputStructure]);
$repo->saveUnitDefinitionsByKey($structureId, 1, $rowsStructure);
$readStructure = static fn () => array_values(array_filter($repo->units($structureId, 1),
    static fn ($u) => $u['unit_kind'] === 'annex'))[0];
expect($readStructure()['method_structure'] === 'solo_construccion', 'estructura del anexo persiste y recarga');
unset($inputStructure['method_structure']);
$repo->saveUnitDefinitionsByKey($structureId, 1, \App\Services\AppraisalUnitDefinitionInput::rows(['annex-1' => $inputStructure]));
expect($readStructure()['method_structure'] === 'solo_construccion', 'formulario anterior sin campo no borra estructura');
$inputStructure['method_structure'] = 'solo_terreno';
$repo->saveUnitDefinitionsByKey($structureId, 2, \App\Services\AppraisalUnitDefinitionInput::rows(['annex-1' => $inputStructure]));
expect($readStructure()['method_structure'] === 'solo_construccion', 'estructura no se modifica por propietario ajeno');
$unitStructureBefore = $readStructure();
(require BASE_PATH . '/database/migrations/202610020001_unit_method_structure.php')(new \App\Database\Schema($app));
expect($readStructure()['id'] === $unitStructureBefore['id'] && $readStructure()['notes'] === 'Conservar'
    && $readStructure()['method_structure'] === 'solo_construccion', 'migración repetida conserva identidad y datos');
