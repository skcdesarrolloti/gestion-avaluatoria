<?php
$currentTypology = (string) ($ph['ph_typology'] ?? '');
$priorityKeys = is_array($phCatalog['typologyPriorities'][$currentTypology] ?? null) ? $phCatalog['typologyPriorities'][$currentTypology] : [];
$priorityFound = array_values(array_filter($priorityKeys, static fn (string $key): bool => trim($phMap('common_areas', $key, 'status')) !== ''));
$commonStats = [];
foreach ($phCatalog['commonAreaGroups'] as $groupKey => [$groupTitle, $items]) {
    $found = 0;
    foreach ($items as $key => $label) if (trim($phMap('common_areas', (string) $key, 'status')) !== '') $found++;
    $commonStats[$groupKey] = [$groupTitle, $found, count($items)];
}
$hasPhReading = trim($phSourceSummary) !== '' || trim((string) ($technical['dotacion_tipologia'] ?? '')) !== '';
?>
<form class="mt-6 space-y-6" method="post" action="<?= e(url($subjectActionBase . '/ph')) ?>"
    data-module-autosave data-save-in-place data-autosave-endpoint="<?= e(url($subjectActionBase . '/ph/autoguardar')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="version" value="<?= (int) ($ph['version'] ?? 0) ?>">
    <input type="hidden" name="ph[ph_typology]" :value="phTypology">
    <div x-data="{ tab: '<?= $hasPhReading ? 'radiografia' : 'identidad' ?>' }">
        <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
            <?php foreach ([
                'radiografia' => 'Radiografía PH', 'identidad' => 'Identificación', 'juridico' => 'Jurídica PH', 'tecnica' => 'Descripción técnica',
                'administracion' => 'Administración', 'comunes' => 'Comunes y amenidades',
                'riesgos' => 'Riesgos y soportes', 'informe' => 'Informe',
            ] as $key => $label): ?>
                <button class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold" type="button"
                    @click="tab = '<?= e($key) ?>'"
                    :class="tab === '<?= e($key) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                    <?= e($label) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <section class="mt-5 grid gap-4" x-show="tab === 'radiografia'">
            <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                <div class="grid gap-4 lg:grid-cols-[1fr_18rem]">
                    <div>
                        <h3 class="text-lg font-semibold text-blue-950">Radiografía de la copropiedad</h3>
                        <p class="mt-2 text-sm leading-6 text-blue-950">
                            Vista ejecutiva para saber qué se leyó del reglamento, qué tipología gobierna el análisis
                            y qué tan dotada aparece la copropiedad frente a otras de su mismo tipo.
                        </p>
                    </div>
                    <label class="label text-blue-950">Tipología aplicada
                        <select class="input bg-white" x-model="phTypology">
                            <option value="">Selecciona tipología PH</option>
                            <?php foreach ($phCatalog['typologies'] as $value => $label): ?>
                                <option value="<?= e($value) ?>"><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
            </div>
            <div class="grid gap-4 xl:grid-cols-4">
                <?php foreach ($commonStats as [$groupTitle, $found, $total]): ?>
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase text-slate-500"><?= e($groupTitle) ?></p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900"><?= (int) $found ?> / <?= (int) $total ?></p>
                        <p class="mt-1 text-xs text-slate-600">menciones con soporte documental o manual</p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="grid gap-4 lg:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <h4 class="font-semibold">Dotación prioritaria por tipología</h4>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        <?= e((string) ($technical['dotacion_tipologia'] ?? 'Selecciona la tipología y recarga el soporte para generar la matriz comparativa.')) ?>
                    </p>
                    <p class="mt-3 rounded-lg bg-slate-50 p-3 text-sm leading-6 text-slate-700">
                        <?= e((string) ($technical['nivel_dotacion_comparativa'] ?? 'Sin nivel comparativo calculado para esta lectura.')) ?>
                    </p>
                    <?php if ($priorityKeys): ?>
                        <p class="mt-3 text-xs font-semibold text-slate-500">
                            Prioritarios detectados: <?= count($priorityFound) ?> de <?= count($priorityKeys) ?>.
                        </p>
                    <?php endif; ?>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <h4 class="font-semibold">Qué debería revisar el analista</h4>
                    <ul class="mt-2 space-y-1 text-sm leading-6 text-slate-700">
                        <li>• Si la tipología seleccionada corresponde al inmueble sujeto.</li>
                        <li>• Si los bienes comunes marcados existen hoy y funcionan según visita.</li>
                        <li>• Si el reglamento leído es el vigente o hay reformas posteriores.</li>
                        <li>• Si la unidad privada, coeficiente, áreas y expensas corresponden al bien sujeto.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="mt-5 grid gap-4 lg:grid-cols-2" x-show="tab === 'identidad'">
            <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950 lg:col-span-2">
                <strong>Tipología PH de referencia:</strong>
                <span x-text="phTypologyLabel()"></span>
            </div>
            <?php $renderPhInput('ph_name', 'Nombre de la copropiedad / agrupación', $phText('ph_name'), 'Ej. Edificio, conjunto, centro comercial o zona franca.'); ?>
            <?php $renderPhInput('ph_key', 'Llave técnica PH', $phText('ph_key'), 'NIT, matrícula matriz o nombre normalizado para reutilizarla luego.'); ?>
            <label class="label">Vínculo con sector por barrio
                <input class="input mt-2" name="ph[linkage][sector_neighborhood]"
                    value="<?= e((string) ($linkage['sector_neighborhood'] ?? '')) ?>" placeholder="Barrio validado de la copropiedad">
            </label>
            <label class="label">Vínculo jurídico por matrícula inmobiliaria
                <input class="input mt-2" name="ph[linkage][legal_registration]"
                    value="<?= e((string) (($linkage['legal_registration'] ?? '') ?: ($subject['property_registry'] ?? ''))) ?>" placeholder="Matrícula del bien sujeto, confirmada en jurídica">
            </label>
            <label class="label">Vínculo de copropiedad por nombre
                <input class="input mt-2" name="ph[linkage][coproperty_name]"
                    value="<?= e((string) (($linkage['coproperty_name'] ?? '') ?: $phText('ph_name'))) ?>" placeholder="Nombre según el reglamento">
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

        <section class="mt-5" x-show="tab === 'tecnica'"><?php require BASE_PATH . '/app/Views/appraisals/subject-ph-technical.php'; ?></section>

        <section class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4" x-show="tab === 'comunes'">
            <div class="grid gap-4 lg:grid-cols-[1fr_22rem]">
                <div>
                    <h3 class="text-lg font-semibold">Bienes comunes, amenidades y soporte comparable</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Separa bienes esenciales, amenidades, áreas de uso exclusivo y soporte operativo.
                        Compara solo contra copropiedades de la misma tipología y escala.
                    </p>
                </div>
                <div class="rounded-xl border border-blue-100 bg-blue-50 p-3 text-sm leading-6 text-blue-950">
                    <strong>Dotación por tipología:</strong>
                    <?php foreach ($phCatalog['typologyPriorities'] as $typology => $priorityKeys): ?>
                        <p class="mt-2" x-show="phTypology === '<?= e((string) $typology) ?>'">
                            <?= e((string) ($phCatalog['typologies'][$typology] ?? $typology)) ?>:
                            <?= e(implode(', ', array_map(static fn (string $key): string =>
                                (string) ($phCatalog['commonAreas'][$key] ?? $key), $priorityKeys))) ?>.
                        </p>
                    <?php endforeach; ?>
                    <p class="mt-2" x-show="!phTypology">Selecciona una tipología para ver los factores prioritarios.</p>
                </div>
            </div>
            <?php foreach ($phCatalog['commonAreaGroups'] as $groupKey => [$groupTitle, $items]): ?>
                <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
                    <h4 class="text-base font-semibold"><?= e($groupTitle) ?></h4>
                    <div class="mt-3 grid gap-3 xl:grid-cols-2">
                        <?php foreach ($items as $key => $label): ?>
                            <?php $current = $phMap('common_areas', (string) $key, 'status'); ?>
                            <div class="rounded-xl border p-3 <?= e($statusClass($current)) ?>">
                                <label class="label text-sm"><?= e($label) ?>
                                    <select class="input mt-2" name="ph[common_areas][<?= e($key) ?>][status]">
                                        <?php foreach ($phCatalog['status'] as $value => $option): ?>
                                            <option value="<?= e($value) ?>" <?= $current === (string) $value ? 'selected' : '' ?>><?= e($option) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <label class="label mt-2">Evidencia y observación
                                <textarea class="input mt-2 min-h-20" rows="2" name="ph[common_areas][<?= e($key) ?>][notes]"
                                    placeholder="Página, cláusula, visita o salvedad"><?= e($phMap('common_areas', (string) $key, 'notes')) ?></textarea></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <?php foreach ([
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
                            <label class="label mt-2">Evidencia y observación
                            <textarea class="input mt-2 min-h-20" rows="2" name="ph[<?= e($groupKey) ?>][<?= e($key) ?>][notes]"
                                placeholder="Observación del analista"><?= e($phMap($groupKey, (string) $key, 'notes')) ?></textarea></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>

        <section class="mt-5 grid gap-4" x-show="tab === 'informe'">
            <?php $renderPhTextarea('diagnosis_text', 'Diagnóstico preliminar de copropiedad', $phText('diagnosis_text'), 'Resume si la PH está ordenada, requiere soportes o presenta alertas.', 5); ?>
            <?php $renderPhTextarea('report_text', 'Texto para el entregable', $phText('report_text'), 'Incluye la advertencia de que es informe técnico y no estudio de títulos.', 6); ?>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-700">
                <h3 class="font-semibold text-slate-900">Notas normativas sugeridas</h3>
                <ul class="mt-2 space-y-1">
                    <?php foreach ($phCatalog['normNotes'] as $note): ?><li>• <?= e((string) $note) ?></li><?php endforeach; ?>
                </ul>
            </div>
        </section>
    </div>
    <div class="flex flex-wrap justify-end gap-3">
        <p class="mr-auto self-center text-xs font-semibold text-slate-500" data-autosave-status>Autoguardado activo</p>
        <button class="btn-primary" type="submit">Guardar propiedad horizontal</button>
    </div>
</form>
