<?php
$phApplies = (string) ($record['regimen_ph'] ?? '') === 'si';
$ph = is_array($phProfile ?? null) ? $phProfile : [];
$phText = static fn (string $key): string => (string) ($ph[$key] ?? '');
$phMap = static fn (string $group, string $key, string $field): string =>
    (string) (($ph[$group][$key][$field] ?? ''));
$technical = is_array($ph['technical'] ?? null) ? $ph['technical'] : [];
$linkage = is_array($ph['linkage'] ?? null) ? $ph['linkage'] : [];
$phSourceSummary = (string) ($ph['source_summary'] ?? '');
$phFindings = is_array($ph['findings'] ?? null) ? $ph['findings'] : [];
$statusClass = static function (string $status): string {
    return match ($status) {
        'ok' => 'border-emerald-200 bg-emerald-50',
        'warn' => 'border-amber-200 bg-amber-50',
        'risk' => 'border-red-200 bg-red-50',
        default => 'border-slate-200 bg-white',
    };
};
$renderPhInput = static function (string $name, string $label, string $value, string $help = ''): void { ?>
    <label class="label"><?= e($label) ?>
        <input class="input mt-2" name="ph[<?= e($name) ?>]" value="<?= e($value) ?>">
        <?php if ($help !== ''): ?><span class="mt-1 block text-xs font-normal text-slate-500"><?= e($help) ?></span><?php endif; ?>
    </label>
<?php };
$renderPhTextarea = static function (string $name, string $label, string $value, string $help = '', int $rows = 3): void { ?>
    <label class="label"><?= e($label) ?>
        <textarea class="input mt-2 min-h-24" rows="<?= $rows ?>" name="ph[<?= e($name) ?>]"><?= e($value) ?></textarea>
        <?php if ($help !== ''): ?><span class="mt-1 block text-xs font-normal text-slate-500"><?= e($help) ?></span><?php endif; ?>
    </label>
<?php };
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{
        phTypology: <?= e(json_encode((string) ($ph['ph_typology'] ?? ''), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        phTypologyLabels: <?= e(json_encode($phCatalog['typologies'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>, phTypologyLabel() { return this.phTypologyLabels[this.phTypology] || 'Pendiente de selección' }
    }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">3.5 Propiedad horizontal</p>
            <h2 class="mt-2 text-2xl font-semibold">Copropiedad, zonas comunes y administración</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Integra la copropiedad analizada con el bien sujeto. Esta ficha toma señales de jurídica,
                sector y visita, pero el criterio final lo conserva el analista.
            </p>
        </div>
        <span class="rounded-full <?= $phApplies ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-800' ?> px-3 py-1 text-sm font-semibold">
            <?= $phApplies ? 'PH habilitada' : 'PH no habilitada' ?>
        </span>
    </div>
    <?php if (!$phApplies): ?>
        <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm leading-6 text-amber-950">
            Para diligenciar 3.5, primero marca <strong>Régimen de propiedad horizontal (PH): Sí</strong>
            en el numeral 1. Así evitamos mezclar copropiedad en inmuebles independientes.
        </div>
    <?php else: ?>
        <?php if (!empty($phMessage)): ?>
            <p class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($phMessage) ?></p>
        <?php endif; ?>
        <?php if (!empty($phError)): ?>
            <p class="mt-5 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($phError) ?></p>
        <?php endif; ?>
        <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
            <strong>Academia del campo.</strong> La llave PH puede ser NIT, matrícula matriz o nombre normalizado
            de la copropiedad. Sirve para reconocerla en otros avalúos; por ahora guarda este expediente y
            deja preparada la migración a banco compartido. Este apartado no reemplaza estudio de títulos.
        </div>
        <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-search.php'; ?>
        <section class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
            <div class="grid gap-4 lg:grid-cols-[1fr_auto]">
                <div>
                    <h3 class="text-lg font-semibold">Preparar y cargar reglamento / soportes PH</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Sube ZIP/RAR/PDF hasta 300 MB o DOCX, TXT e imágenes hasta 50 MB. ZIP se abre siempre; RAR se procesa
                        si el servidor tiene extractor disponible y, si no, el sistema pedirá convertirlo a ZIP.
                    </p>
                    <?php $ocr = is_array($phOcrDiagnostics ?? null) ? $phOcrDiagnostics : []; ?>
                    <?php $phExternalOcr = !empty($ocr['external']); ?>
                    <p class="mt-2 text-xs font-semibold <?= !empty($ocr['pdf_ocr']) ? 'text-emerald-700' : 'text-amber-800' ?>">
                        OCR PDF escaneado: <?= !empty($ocr['pdf_ocr']) ? 'disponible' : 'incompleto' ?>
                        · Tesseract <?= !empty($ocr['tesseract']) ? 'sí' : 'no' ?>
                        · pdftoppm <?= !empty($ocr['pdftoppm']) ? 'sí' : 'no' ?>
                        · ejecución PHP <?= (!empty($ocr['shell_exec']) && !empty($ocr['exec'])) ? 'sí' : 'no' ?>
                        · IA externa <?= !empty($ocr['external']) ? 'sí' : 'no' ?>
                        <?= !empty($ocr['external']) ? '· proveedor ' . e((string) ($ocr['external_provider'] ?? 'generic')) : '' ?>
                    </p>
                    <?php if (empty($ocr['external'])): ?>
                        <p class="mt-2 rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-900">
                            Leer con IA/OCR requiere configurar PH_EXTERNAL_OCR_ENDPOINT o MINIMAX_API_KEY en el servidor. Mientras aparezca IA externa no, los soportes escaneados quedarán cargados pero sin texto automático.
                        </p>
                    <?php elseif (($ocr['external_provider'] ?? '') === 'minimax' && empty($ocr['external_pdf_render'])): ?>
                        <p class="mt-2 rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-900">
                            MiniMax leerá imágenes. Si subes PDF escaneado, el navegador convertirá hasta 300 páginas a imagen antes de enviarlas a MiniMax.
                        </p>
                    <?php endif; ?>
                </div>
                <form class="grid gap-3 lg:min-w-80" method="post" enctype="multipart/form-data"
                    action="<?= e(url($subjectActionBase . '/ph/soportes')) ?>" data-upload-progress data-ph-pdf-render
                    data-ph-pdf-max-pages="300" data-ph-pdf-max-side="1200" data-ph-pdf-quality="0.72"
                    data-upload-timeout="1800000" data-upload-chunk-url="<?= e(url($subjectActionBase . '/ph/soportes/chunk')) ?>" data-upload-finish-url="<?= e(url($subjectActionBase . '/ph/soportes/finalizar')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="return_to" value="<?= e($subjectActionBase . '#ph') ?>">
                    <select class="input" name="ph_typology" x-model="phTypology">
                        <option value="">Selecciona tipología PH de referencia</option>
                        <?php foreach ($phCatalog['typologies'] as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= (string) ($ph['ph_typology'] ?? '') === (string) $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input class="input" type="file" name="ph_document[]" multiple
                        accept=".zip,.rar,.pdf,.docx,.txt,.jpg,.jpeg,.png,.webp,.tif,.tiff">
                    <button class="btn-primary" type="submit">Leer soporte PH</button>
                    <?php require BASE_PATH . '/app/Views/appraisals/upload-progress.php'; ?>
                </form>
            </div>
            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <div class="rounded-xl bg-white p-4">
                    <h4 class="font-semibold">Resumen detectado</h4>
                    <p class="mt-2 text-sm leading-6 text-slate-600"><?= e($phSourceSummary ?: 'Aún no se ha procesado ningún soporte PH.') ?></p>
                    <?php if ($phFindings): ?>
                        <ul class="mt-3 space-y-1 text-sm text-slate-700">
                            <?php foreach ($phFindings as $finding): ?><li>• <?= e((string) $finding) ?></li><?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="rounded-xl bg-white p-4">
                    <h4 class="font-semibold">Soportes cargados</h4>
                    <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-documents.php'; ?>
                </div>
            </div>
        </section>
        <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-form.php'; ?>
    <?php endif; ?>
</section>
