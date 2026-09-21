<?php
$currentTypology = (string) ($ph['ph_typology'] ?? '');
$priorityKeys = is_array($phCatalog['typologyPriorities'][$currentTypology] ?? null) ? $phCatalog['typologyPriorities'][$currentTypology] : [];
$phCommonStatus = static fn (string $key): string => trim($phMap('common_areas', $key, 'status'));
$phCommonApplies = static fn (string $key): bool => $phCommonStatus($key) !== 'na';
$phCommonReady = static fn (string $key): bool => in_array($phCommonStatus($key), ['ok', 'warn', 'risk'], true);
$priorityFound = array_values(array_filter($priorityKeys, $phCommonReady));
$commonStats = [];
foreach ($phCatalog['commonAreaGroups'] as $groupKey => [$groupTitle, $items]) {
    $keys = array_map('strval', array_keys($items));
    $found = count(array_filter($keys, $phCommonReady));
    $total = count(array_filter($keys, $phCommonApplies));
    $commonStats[$groupKey] = [$groupTitle, $found, $total];
}
$hasPhReading = trim($phSourceSummary) !== '' || trim((string) ($technical['dotacion_tipologia'] ?? '')) !== '';
$phDocumentCount = is_array($phDocuments ?? null) ? count($phDocuments) : 0;
$phExtractedChars = 0;
foreach (($phDocuments ?? []) as $doc) $phExtractedChars += (int) ($doc['extracted_chars'] ?? 0);
$phCoverage = 'Pendiente';
$phLowPages = 'Sin observaciones pendientes';
foreach ($phFindings as $finding) {
    $line = (string) $finding;
    if (str_contains($line, '[Cobertura:')) $phCoverage = trim($line, '[]');
    if (str_starts_with($line, 'Páginas con lectura baja')) $phLowPages = str_replace('Páginas con lectura baja o sin texto (revisar original):', 'Páginas que requieren cotejo contra original:', $line);
}
$technicalValue = static fn (string $key): string => (string) ($technical[$key] ?? '');
$renderTechTextarea = static function (string $key, string $label, string $value): void { ?>
    <label class="label"><?= e($label) ?>
        <textarea class="input mt-2 min-h-24" rows="3" name="ph[technical][<?= e($key) ?>]" placeholder="Pendiente de soporte documental"><?= e($value) ?></textarea>
    </label>
<?php };
$renderPhTabSummary = static function (string $key, string $label) use ($technicalValue): void { ?>
    <label class="label rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-emerald-950"><?= e($label) ?>
        <textarea class="input mt-2 min-h-24 bg-white" rows="3" name="ph[technical][<?= e($key) ?>]"
            placeholder="Texto profesional para revisar e incorporar luego al Entregable"><?= e($technicalValue($key)) ?></textarea>
        <span class="mt-1 block text-xs font-normal text-emerald-800">Texto para informe; el soporte documental queda abajo para revisión del analista.</span>
    </label>
<?php };
?>
<form class="mt-6 space-y-6" method="post" action="<?= e(url($subjectActionBase . '/ph')) ?>"
    data-module-autosave data-save-in-place data-autosave-endpoint="<?= e(url($subjectActionBase . '/ph/autoguardar')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="version" value="<?= (int) ($ph['version'] ?? 0) ?>">
    <input type="hidden" name="ph[ph_typology]" :value="phTypology">
    <div x-data="{ tab: '<?= $hasPhReading ? 'base' : 'identidad' ?>' }">
        <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
            <?php foreach ([
                'base' => 'Base PH común', 'trazabilidad' => 'Documento y trazabilidad', 'identidad' => 'Identificación PH',
                'tipologia' => 'Tipología y régimen', 'configuracion' => 'Configuración predial',
                'comunes' => 'Bienes comunes y soporte', 'reglas' => 'Reglas de uso y operación',
                'administracion' => 'Administración y cargas', 'incidencia' => 'Incidencia valuatoria', 'notas' => 'Notas normativas',
            ] as $key => $label): ?>
                <button class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold" type="button"
                    @click="tab = '<?= e($key) ?>'"
                    :class="tab === '<?= e($key) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                    <?= e($label) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-radiography.php'; ?>

        <section class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4" x-show="tab === 'trazabilidad'">
            <h3 class="text-lg font-semibold">Documento y trazabilidad</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600"><?= e($phSourceSummary ?: 'Aún no hay lectura documental cargada.') ?></p>
            <div class="mt-4"><?php $renderPhTabSummary('resumen_trazabilidad_ph', 'Condición especial PH para Entregable'); ?></div>
            <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-document-fields.php'; ?>
            <div class="mt-4 rounded-xl bg-white p-4"><?php require BASE_PATH . '/app/Views/appraisals/subject-ph-documents.php'; ?></div>
            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <?php $renderPhTextarea('regulation_document', 'Soporte del documento constitutivo / reglamento PH', $phText('regulation_document'), 'Referencia documental para revisión; no se copia literal al informe.', 4); ?>
                <?php $renderPhTextarea('reform_documents', 'Soporte de reformas, aclaraciones y antecedentes', $phText('reform_documents'), 'Referencia documental para ubicar antecedentes, condiciones especiales y salvedades.', 4); ?>
            </div>
        </section>

        <section class="mt-5 grid gap-4 lg:grid-cols-2" x-show="tab === 'identidad'">
            <div class="lg:col-span-2"><?php $renderPhTabSummary('resumen_identificacion_ph', 'Identificación PH para Entregable'); ?></div>
            <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950 lg:col-span-2">
                <strong>Tipología PH de referencia:</strong>
                <span x-text="phTypologyLabel()"></span>
            </div>
            <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-identity-cross.php'; ?>

            <?php $renderPhInput('ph_name', 'Nombre de la copropiedad / agrupación', $phText('ph_name'), 'Ej. Edificio, conjunto, centro comercial o zona franca.'); ?>
            <label class="label">Vínculo jurídico por matrícula inmobiliaria
                <input class="input mt-2" name="ph[linkage][legal_registration]"
                    value="<?= e((string) (($linkage['legal_registration'] ?? '') ?: ($subject['property_registry'] ?? ''))) ?>" placeholder="Matrícula del bien sujeto, confirmada en jurídica">
            </label>
            <?php $renderPhInput('private_unit', 'Unidad privada analizada', $phText('private_unit')); ?>
            <?php $renderPhInput('coefficient', 'Coeficiente de copropiedad', $phText('coefficient')); ?>
        </section>

        <section class="mt-5 grid gap-4 lg:grid-cols-2" x-show="tab === 'tipologia'">
            <div class="lg:col-span-2"><?php $renderPhTabSummary('resumen_tipologia_ph', 'Tipología y régimen para Entregable'); ?></div>
            <label class="label">Tipología seleccionada
                <select class="input mt-2" x-model="phTypology">
                    <option value="">Selecciona tipología PH</option>
                    <?php foreach ($phCatalog['typologies'] as $value => $label): ?><option value="<?= e($value) ?>"><?= e($label) ?></option><?php endforeach; ?>
                </select>
            </label>
            <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-typology-support.php'; ?>
            <?php $renderTechTextarea('regimen_especial', 'Régimen especial', $technicalValue('regimen_especial')); ?>
            <?php $renderTechTextarea('naturaleza_conjunto', 'Naturaleza del conjunto', $technicalValue('naturaleza_conjunto')); ?>
            <?php $renderTechTextarea('uso_dominante', 'Uso dominante', $technicalValue('uso_dominante')); ?>
            <?php $renderTechTextarea('usos_complementarios', 'Usos complementarios', $technicalValue('usos_complementarios')); ?>
            <?php $renderTechTextarea('relacion_funcional_usos', 'Relación funcional entre usos', $technicalValue('relacion_funcional_usos')); ?>
        </section>

        <section class="mt-5 grid gap-4 lg:grid-cols-2" x-show="tab === 'configuracion'">
            <div class="lg:col-span-2"><?php $renderPhTabSummary('resumen_configuracion_ph', 'Configuración predial para Entregable'); ?></div>
            <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-configuration-support.php'; ?>
            <?php $renderPhInput('matrix_registration', 'Matrícula matriz', $phText('matrix_registration'), 'Folio base de la copropiedad, tomado de jurídica o reglamento.'); ?>
            <?php foreach (['fecha_reglamento_ph'=>'Fecha / año de constitución o registro PH','edad_aproximada_ph'=>'Edad aproximada de la copropiedad','lotes_por_etapa'=>'Lote matriz o predio de origen','area_lote_matriz'=>'Área del lote matriz','area_construida_total'=>'Área construida o área total del conjunto','numero_unidades'=>'Número total de unidades privadas','numero_oficinas'=>'Número de oficinas','numero_locales'=>'Número de locales','numero_parqueaderos'=>'Número de parqueaderos','numero_depositos'=>'Número de depósitos','numero_edificios'=>'Bloques, torres, naves o edificios','numero_pisos'=>'Número de pisos o niveles','numero_sotanos'=>'Número de sótanos','numero_ascensores'=>'Número de ascensores','etapas_copropiedad'=>'Etapas, sectores o manzanas','resumen_areas_conjunto'=>'Cuadro general de áreas','organizacion_interna'=>'Distribución funcional interna','desarrollos_relevantes'=>'Desenglobes, integraciones o ampliaciones','ubicacion_unidad'=>'Unidad objeto dentro de la configuración'] as $key=>$label) $renderTechTextarea($key, $label, $technicalValue($key)); ?>
        </section>

        <section class="mt-5 grid gap-4 lg:grid-cols-2" x-show="tab === 'reglas'">
            <div class="lg:col-span-2"><?php $renderPhTabSummary('resumen_reglas_ph', 'Resumen depurado para Entregable'); ?></div>
            <?php $renderPhTextarea('restrictions_text', 'Restricciones de uso u operación', $phText('restrictions_text'), 'Usos, horarios, movilidad, residuos, cerramientos o adecuaciones.', 4); ?>
            <?php foreach (['usos_permitidos'=>'Usos permitidos','usos_restringidos'=>'Usos restringidos o prohibidos','reglas_constructivas'=>'Reglas constructivas','condiciones_normativas_operativas'=>'Condiciones operativas','condiciones_usuario_operador'=>'Usuario operador o administración','cargue_descargue'=>'Cargue, descargue y movilidad'] as $key=>$label) $renderTechTextarea($key, $label, $technicalValue($key)); ?>
        </section>

        <section class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4" x-show="tab === 'comunes'">
            <?php $renderPhTabSummary('resumen_comunes_ph', 'Resumen depurado para Entregable'); ?>
            <div class="mt-4 grid gap-4 lg:grid-cols-2"><?php require BASE_PATH . '/app/Views/appraisals/subject-ph-common-support.php'; ?></div>
            <div class="mt-5">
                <h3 class="text-lg font-semibold">Detalle editable de bienes comunes, amenidades y soporte</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Diligencia o depura cada campo. La matriz superior resume qué está listo, qué requiere revisión y qué falta para el Entregable.</p>
            </div>
            <?php foreach ($phCatalog['commonAreaGroups'] as $groupKey => [$groupTitle, $items]): ?>
                <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
                    <h4 class="text-base font-semibold"><?= e($groupTitle) ?></h4>
                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full min-w-[52rem] text-left text-sm">
                            <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Elemento</th><th class="py-2 pr-3">Estado</th><th class="py-2">Evidencia y observación</th></tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($items as $key => $label): ?>
                                    <?php $current = $phMap('common_areas', (string) $key, 'status'); ?>
                                    <tr class="align-top <?= e($statusClass($current)) ?>">
                                        <td class="w-64 py-2 pr-3 font-semibold text-slate-800"><?= e($label) ?></td>
                                        <td class="w-56 py-2 pr-3">
                                            <select class="input mt-0 min-h-10 py-2 text-sm" name="ph[common_areas][<?= e($key) ?>][status]">
                                                <?php foreach ($phCatalog['status'] as $value => $option): ?>
                                                    <option value="<?= e($value) ?>" <?= $current === (string) $value ? 'selected' : '' ?>><?= e($option) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="py-2">
                                            <textarea class="input mt-0 min-h-14 py-2 text-sm" rows="2" name="ph[common_areas][<?= e($key) ?>][notes]" placeholder="Página, cláusula, visita o salvedad"><?= e($phMap('common_areas', (string) $key, 'notes')) ?></textarea>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-closing.php'; ?>
    </div>
    <div class="flex flex-wrap justify-end gap-3">
        <p class="mr-auto self-center text-xs font-semibold text-slate-500" data-autosave-status>Autoguardado activo</p>
        <button class="btn-primary" type="submit">Guardar propiedad horizontal</button>
    </div>
</form>
