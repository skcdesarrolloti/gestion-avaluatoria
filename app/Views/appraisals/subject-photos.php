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
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{ activePhotoUnit: '', photoMap: <?= e(json_encode($photoTabMap, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>, syncPhotoUnit() { this.activePhotoUnit = this.photoMap[location.hash.slice(1)] || '<?= e($photoUnits[0]['kind'] . ':' . $photoUnits[0]['id']) ?>' } }"
    x-init="syncPhotoUnit()" @hashchange.window="syncPhotoUnit()">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">3.5 Registro fotográfico del sujeto</p>
            <h2 class="mt-2 text-2xl font-semibold">Fotos organizadas para el entregable</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Carga o pega las imágenes que irán al informe. Todas se muestran con el mismo formato visual;
                la portada puede tomarse horizontal cuando sea necesario para cubrir toda la propiedad.
            </p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
            <?= count($photoUnits) ?> grupo(s)
        </span>
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
            <div class="mt-5 grid gap-5 xl:grid-cols-2">
                <?php foreach ($photoCategories as $categoryKey => [$title, $description]): ?>
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
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>
