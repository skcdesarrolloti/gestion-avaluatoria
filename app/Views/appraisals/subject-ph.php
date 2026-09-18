<?php
$phApplies = (string) ($record['regimen_ph'] ?? '') === 'si';
$ph = is_array($phProfile ?? null) ? $phProfile : [];
$legalPh = is_array($phLegalPrefill ?? null) ? $phLegalPrefill : [];
$phText = static fn (string $key): string => (string) (($ph[$key] ?? '') ?: ($legalPh[$key] ?? ''));
$phMap = static fn (string $group, string $key, string $field): string =>
    (string) (($ph[$group][$key][$field] ?? ''));
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
        <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
            <strong>Academia del campo.</strong> La llave PH puede ser NIT, matrícula matriz o nombre normalizado
            de la copropiedad. Sirve para reconocerla en otros avalúos; por ahora guarda este expediente y
            deja preparada la migración a banco compartido. Este apartado no reemplaza estudio de títulos.
        </div>
        <form class="mt-6 space-y-6" method="post" action="<?= e(url($subjectActionBase . '/ph')) ?>"
            data-module-autosave data-autosave-endpoint="<?= e(url($subjectActionBase . '/ph/autoguardar')) ?>">
            <?= csrf_field() ?>
            <div x-data="{ tab: 'identidad' }">
                <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
                    <?php foreach ([
                        'identidad' => 'Identificación', 'juridico' => 'Jurídica PH',
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
                    <?php $renderPhInput('ph_name', 'Nombre de la copropiedad / agrupación', $phText('ph_name'), 'Ej. Edificio, conjunto, centro comercial o zona franca.'); ?>
                    <?php $renderPhInput('ph_key', 'Llave técnica PH', $phText('ph_key'), 'NIT, matrícula matriz o nombre normalizado para reutilizarla luego.'); ?>
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
