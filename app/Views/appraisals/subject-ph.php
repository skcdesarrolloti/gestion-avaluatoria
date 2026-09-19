<?php
$phApplies = (string) ($record['regimen_ph'] ?? '') === 'si';
$ph = is_array($phProfile ?? null) ? $phProfile : [];
$legalPh = is_array($phLegalPrefill ?? null) ? $phLegalPrefill : [];
$phText = static fn (string $key): string => (string) (($ph[$key] ?? '') ?: ($legalPh[$key] ?? ''));
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
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
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
                        Sube ZIP/RAR hasta 300 MB o documentos sueltos hasta 50 MB. ZIP se abre siempre; RAR se procesa
                        si el servidor tiene extractor disponible y, si no, el sistema pedirá convertirlo a ZIP.
                    </p>
                </div>
                <form class="grid gap-3 lg:min-w-80" method="post" enctype="multipart/form-data"
                    action="<?= e(url($subjectActionBase . '/ph/soportes')) ?>" data-upload-progress>
                    <?= csrf_field() ?>
                    <select class="input" name="ph_typology">
                        <option value="">Tipología PH para orientar lectura</option>
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
                    <?php if (!empty($phDocuments)): ?>
                        <ul class="mt-2 space-y-1 text-sm text-slate-700">
                            <?php foreach (array_slice($phDocuments, 0, 5) as $doc): ?>
                                <li><?= e($doc['source_filename']) ?> · <?= e((string) $doc['extracted_chars']) ?> caracteres</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="mt-2 text-sm text-slate-600">Sin soportes cargados.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <form class="mt-6 space-y-6" method="post" action="<?= e(url($subjectActionBase . '/ph')) ?>"
            data-module-autosave data-autosave-endpoint="<?= e(url($subjectActionBase . '/ph/autoguardar')) ?>">
            <?= csrf_field() ?>
            <div x-data="{ tab: 'identidad' }">
                <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
                    <?php foreach ([
                        'identidad' => 'Identificación', 'juridico' => 'Jurídica PH', 'tecnica' => 'Descripción técnica',
                        'administracion' => 'Administración', 'comunes' => 'Zonas comunes',
                        'riesgos' => 'Riesgos y soportes', 'informe' => 'Informe',
                    ] as $key => $label): ?>
                        <button class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold" type="button"
                            @click="tab = '<?= e($key) ?>'"
                            :class="tab === '<?= e($key) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                            <?= e($label) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <section class="mt-5 grid gap-4 lg:grid-cols-2" x-show="tab === 'identidad'">
                    <label class="label">Tipología PH de referencia
                        <select class="input mt-2" name="ph[ph_typology]">
                            <option value="">Selecciona tipología PH</option>
                            <?php foreach ($phCatalog['typologies'] as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= (string) ($ph['ph_typology'] ?? '') === (string) $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <?php $renderPhInput('ph_name', 'Nombre de la copropiedad / agrupación', $phText('ph_name'), 'Ej. Edificio, conjunto, centro comercial o zona franca.'); ?>
                    <?php $renderPhInput('ph_key', 'Llave técnica PH', $phText('ph_key'), 'NIT, matrícula matriz o nombre normalizado para reutilizarla luego.'); ?>
                    <label class="label">Vínculo con sector por barrio
                        <input class="input mt-2" name="ph[linkage][sector_neighborhood]"
                            value="<?= e((string) (($linkage['sector_neighborhood'] ?? '') ?: ($subject['neighborhood_name'] ?? ''))) ?>">
                    </label>
                    <label class="label">Vínculo jurídico por matrícula inmobiliaria
                        <input class="input mt-2" name="ph[linkage][legal_registration]"
                            value="<?= e((string) (($linkage['legal_registration'] ?? '') ?: ($subject['property_registry'] ?? ''))) ?>">
                    </label>
                    <label class="label">Vínculo de copropiedad por nombre
                        <input class="input mt-2" name="ph[linkage][coproperty_name]"
                            value="<?= e((string) (($linkage['coproperty_name'] ?? '') ?: $phText('ph_name'))) ?>">
                    </label>
                    <?php $renderPhInput('private_unit', 'Unidad privada analizada', $phText('private_unit')); ?>
                    <?php $renderPhInput('coefficient', 'Coeficiente de copropiedad', $phText('coefficient')); ?>
                </section>

                <section class="mt-5 grid gap-4 lg:grid-cols-2" x-show="tab === 'juridico'">
                    <?php $renderPhInput('matrix_registration', 'Matrícula matriz', $phText('matrix_registration'), 'Puede venir del certificado de tradición.'); ?>
                    <?php $renderPhTextarea('regulation_document', 'Constitución / reglamento PH', $phText('regulation_document'), 'Documento base del régimen PH.', 4); ?>
                    <?php $renderPhTextarea('reform_documents', 'Reformas PH identificadas', $phText('reform_documents'), 'Reformas o aclaraciones que deban tenerse presentes.', 4); ?>
                    <?php $renderPhTextarea('restrictions_text', 'Restricciones de uso o convivencia', $phText('restrictions_text'), 'Avisos, horarios, actividades, arriendos, mascotas u otras reglas internas.', 4); ?>
                </section>

                <section class="mt-5 grid gap-4 lg:grid-cols-2" x-show="tab === 'administracion'">
                    <?php $renderPhInput('administration_name', 'Administración / razón social', $phText('administration_name')); ?>
                    <?php $renderPhInput('administration_contact', 'Contacto de administración', $phText('administration_contact')); ?>
                    <?php $renderPhInput('administration_phone', 'Teléfono', $phText('administration_phone')); ?>
                    <?php $renderPhInput('administration_email', 'Correo', $phText('administration_email')); ?>
                    <?php $renderPhInput('monthly_fee', 'Cuota de administración', $phText('monthly_fee')); ?>
                    <?php $renderPhInput('fee_status', 'Estado de expensas', $phText('fee_status'), 'Paz y salvo, pendiente o por confirmar.'); ?>
                    <?php $renderPhInput('reserve_fund', 'Fondo / imprevistos', $phText('reserve_fund')); ?>
                    <?php $renderPhInput('insurance_status', 'Seguros comunes', $phText('insurance_status')); ?>
                </section>

                <section class="mt-5" x-show="tab === 'tecnica'">
                    <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-technical.php'; ?>
                </section>

                <?php foreach ([
                    'comunes' => ['common_areas', 'Zonas comunes y servicios PH', $phCatalog['commonAreas']],
                    'riesgos' => ['risks', 'Riesgos, restricciones y afectaciones PH', $phCatalog['risks']],
                    'documentos' => ['documents', 'Soportes documentales PH', $phCatalog['documents']],
                    'fotos' => ['photos', 'Fotos requeridas para 3.6', $phCatalog['photos']],
                ] as $tabKey => [$groupKey, $title, $items]): ?>
                    <section class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4" x-show="tab === '<?= e(in_array($tabKey, ['documentos', 'fotos'], true) ? 'riesgos' : $tabKey) ?>'">
                        <h3 class="text-lg font-semibold"><?= e($title) ?></h3>
                        <div class="mt-4 grid gap-3 xl:grid-cols-2">
                            <?php foreach ($items as $key => $label): ?>
                                <?php $current = $phMap($groupKey, (string) $key, 'status'); ?>
                                <div class="rounded-xl border p-3 <?= e($statusClass($current)) ?>">
                                    <label class="label text-sm"><?= e($label) ?>
                                        <select class="input mt-2" name="ph[<?= e($groupKey) ?>][<?= e($key) ?>][status]">
                                            <?php foreach ($phCatalog['status'] as $value => $option): ?>
                                                <option value="<?= e($value) ?>" <?= $current === (string) $value ? 'selected' : '' ?>><?= e($option) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                    <textarea class="input mt-2 min-h-20" rows="2"
                                        name="ph[<?= e($groupKey) ?>][<?= e($key) ?>][notes]"
                                        placeholder="Observación del analista"><?= e($phMap($groupKey, (string) $key, 'notes')) ?></textarea>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>

                <section class="mt-5 grid gap-4" x-show="tab === 'informe'">
                    <?php $renderPhTextarea('diagnosis_text', 'Diagnóstico preliminar de copropiedad', $phText('diagnosis_text'), 'Resume si la PH está ordenada, requiere soportes o presenta alertas.', 5); ?>
                    <?php $renderPhTextarea('report_text', 'Texto para el entregable', $phText('report_text'), 'Incluye la advertencia de que es informe técnico y no estudio de títulos.', 6); ?>
                </section>
            </div>
            <div class="flex flex-wrap justify-end gap-3">
                <p class="mr-auto self-center text-xs font-semibold text-slate-500" data-autosave-status>Autoguardado activo</p>
                <button class="btn-primary" type="submit">Guardar propiedad horizontal</button>
            </div>
        </form>
    <?php endif; ?>
</section>
