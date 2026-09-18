<?php
$photoUnits = [['id' => '', 'label' => 'Sujeto general', 'kind' => 'general', 'typology' => 'Predio']];
$unitLabel = static fn (array $unit): string => ($unit['unit_kind'] === 'annex' ? 'Anexo ' : 'Unidad ') . (int) $unit['unit_index'];
foreach (array_values(array_filter($units, static fn (array $unit): bool => $unit['unit_kind'] !== 'common')) as $unit) {
    $photoUnits[] = ['id' => (string) $unit['id'], 'label' => (string) ($unit['label'] ?: $unitLabel($unit)),
        'kind' => (string) $unit['unit_kind'], 'typology' => (string) $unit['igac_typology_hint']];
}
$photoCategories = [
    'portada' => ['Portada', 'Foto principal. Puede ser horizontal cuando se requiere abarcar toda la propiedad.'],
    'fachada' => ['Fachada', 'Vista frontal o acceso principal de la unidad.'],
    'interior' => ['Interior', 'Espacios interiores representativos, si aplican.'],
    'terreno' => ['Lote / terreno', 'Panorámica del suelo, áreas libres o forma del lote.'],
    'construccion' => ['Construcción', 'Fachadas secundarias, cubiertas, anexos o detalles constructivos.'],
    'documento' => ['Documento / soporte', 'Capturas o fotos de documentos útiles para el entregable.'],
    'adicional' => ['Fotos adicionales', 'Agrega evidencias complementarias con nombre propio para el informe.'],
];
$photoTabMap = [];
foreach ($photoUnits as $photoUnit) {
    $photoTabMap['fotos-' . ($photoUnit['id'] ?: 'general')] = $photoUnit['kind'] . ':' . $photoUnit['id'];
}
$attributeLabels = \App\Support\AppraisalSpecialAttributeCatalog::labels();
$storedAttributePhotos = [];
foreach ($photos as $photo) {
    $caption = (string) ($photo['caption'] ?? '');
    if (!str_starts_with($caption, 'attribute:')) continue;
    $storedAttributePhotos[(string) ($photo['unit_id'] ?? '') . '|' . $caption] = true;
}
$attributePhotoRequirements = [];
foreach (array_values(array_filter($units, static fn (array $unit): bool => $unit['unit_kind'] !== 'common')) as $unit) {
    $unitId = (string) $unit['id'];
    $unitData = json_decode((string) ($unit['special_attributes_json'] ?? '{}'), true);
    if (!is_array($unitData)) continue;
    foreach ($unitData as $key => $item) {
        if (!is_array($item) || (string) ($item['evidence'] ?? '') !== 'foto') continue;
        $caption = 'attribute:' . $key;
        $attributePhotoRequirements[] = [
            'unit_id' => $unitId,
            'key' => (string) $key,
            'caption' => $caption,
            'label' => $attributeLabels[$key] ?? ucfirst(str_replace('_', ' ', (string) $key)),
            'covered' => isset($storedAttributePhotos[$unitId . '|' . $caption]),
        ];
    }
}
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{ activePhotoUnit: '', photoMap: <?= e(json_encode($photoTabMap, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>, syncPhotoUnit() { this.activePhotoUnit = this.photoMap[location.hash.slice(1)] || '<?= e($photoUnits[0]['kind'] . ':' . $photoUnits[0]['id']) ?>' }, activeAnchor() { return Object.keys(this.photoMap).find(key => this.photoMap[key] === this.activePhotoUnit) || 'fotos-general' }, focusAdditional() { const card = document.getElementById(this.activeAnchor() + '-adicional'); card?.scrollIntoView({ behavior: 'smooth', block: 'start' }); card?.querySelector('input[name=photo_name]')?.focus({ preventScroll: true }) } }"
    x-init="syncPhotoUnit()" @hashchange.window="syncPhotoUnit()">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">3.6 Registro fotográfico del sujeto</p>
            <h2 class="mt-2 text-2xl font-semibold">Fotos organizadas para el entregable</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Carga o pega las imágenes que irán al informe. Todas se muestran con el mismo formato visual;
                la portada puede tomarse horizontal cuando sea necesario para cubrir toda la propiedad. Si en 3.4
                un atributo exige foto, aquí queda visible como pendiente hasta que cargues su evidencia.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button class="btn-secondary min-h-10 text-xs" type="button" @click="focusAdditional()">Agregar foto</button>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                <?= count($photoUnits) ?> grupo(s)
            </span>
        </div>
    </div>
    <div class="mt-6 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
        <?php foreach ($photoUnits as $photoUnit): ?>
            <?php $tabKey = $photoUnit['kind'] . ':' . $photoUnit['id']; ?>
            <?php $tabAnchor = 'fotos-' . ($photoUnit['id'] ?: 'general'); ?>
            <button class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold" type="button"
                @click="activePhotoUnit = '<?= e($tabKey) ?>'; history.replaceState(null, '', '#<?= e($tabAnchor) ?>')"
                :class="activePhotoUnit === '<?= e($tabKey) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                <?= e($photoUnit['label']) ?>
            </button>
        <?php endforeach; ?>
    </div>
    <?php foreach ($photoUnits as $photoUnit): ?>
        <?php $tabKey = $photoUnit['kind'] . ':' . $photoUnit['id']; ?>
        <?php $tabAnchor = 'fotos-' . ($photoUnit['id'] ?: 'general'); ?>
        <div id="<?= e($tabAnchor) ?>" class="mt-6 scroll-mt-6" x-show="activePhotoUnit === '<?= e($tabKey) ?>'">
            <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
                Las fotos de este grupo se usarán como soporte visual del entregable. Mantén una portada clara;
                si el frente es amplio, usa foto horizontal y conserva el encuadre completo.
            </div>
            <?php $unitRequirements = array_values(array_filter($attributePhotoRequirements,
                static fn (array $row): bool => (string) $row['unit_id'] === (string) $photoUnit['id'])); ?>
            <?php if ($unitRequirements): ?>
                <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <h3 class="text-sm font-semibold text-amber-950">Evidencias marcadas en 3.4</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <?php foreach ($unitRequirements as $requirement): ?>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?= $requirement['covered']
                                ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' ?>">
                                <?= e($requirement['label']) ?> · <?= $requirement['covered'] ? 'foto cargada' : 'foto faltante' ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <p class="mt-3 text-xs leading-5 text-amber-900">
                        Para cerrar un pendiente, carga o pega aquí la imagen correspondiente. Si dejas el nombre vacío,
                        el sistema usará el nombre del atributo.
                    </p>
                </div>
            <?php endif; ?>
            <?php if ($unitRequirements): ?>
                <div class="mt-5 grid gap-5 xl:grid-cols-2">
                    <?php foreach ($unitRequirements as $requirement): ?>
                        <?php
                        $photoUploadEmbedded = true;
                        $photoUploadCompact = true;
                        $photoUploadUnitId = $photoUnit['id'];
                        $photoUploadUnitLabel = $photoUnit['label'];
                        $photoUploadTypology = $photoUnit['typology'];
                        $photoUploadEyebrow = 'Evidencia marcada en 3.4';
                        $photoUploadTitle = $requirement['label'];
                        $photoUploadDescription = 'Carga la imagen soporte de este atributo diferencial. Si el campo nombre queda vacío, se guardará como ' . $requirement['label'] . '.';
                        $photoUploadCaption = $requirement['caption'];
                        $photoUploadNamePlaceholder = $requirement['label'];
                        $photoUploadReturnTo = $subjectActionBase . '#' . $tabAnchor;
                        require BASE_PATH . '/app/Views/appraisals/photo-upload.php';
                        unset($photoUploadEmbedded, $photoUploadCompact, $photoUploadUnitId, $photoUploadUnitLabel,
                            $photoUploadTypology, $photoUploadEyebrow, $photoUploadTitle, $photoUploadDescription,
                            $photoUploadCaption, $photoUploadNamePlaceholder, $photoUploadReturnTo);
                        ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="mt-5 grid gap-5 xl:grid-cols-2">
                <?php foreach ($photoCategories as $categoryKey => [$title, $description]): ?>
                    <div id="<?= e($tabAnchor . '-' . $categoryKey) ?>" class="scroll-mt-6">
                        <?php
                    $photoUploadEmbedded = true;
                    $photoUploadUnitId = $photoUnit['id'];
                    $photoUploadUnitLabel = $photoUnit['label'];
                    $photoUploadTypology = $photoUnit['typology'];
                    $photoUploadEyebrow = 'Foto para entregable';
                    $photoUploadTitle = $title;
                    $photoUploadDescription = $description . ' Pega con Ctrl+V o sube archivo; la vista previa conserva tamaño uniforme.';
                    $photoUploadCaption = 'registro:' . ($photoUnit['id'] ?: 'general') . ':' . $categoryKey;
                    $photoUploadReturnTo = $subjectActionBase . '#' . $tabAnchor;
                    $photoUploadCompact = true;
                    require BASE_PATH . '/app/Views/appraisals/photo-upload.php';
                    unset($photoUploadEmbedded, $photoUploadUnitId, $photoUploadUnitLabel, $photoUploadTypology,
                        $photoUploadEyebrow, $photoUploadTitle, $photoUploadDescription, $photoUploadCaption,
                        $photoUploadReturnTo, $photoUploadCompact);
                    ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>
