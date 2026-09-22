<section class="mt-5 grid gap-4 lg:grid-cols-2" x-show="tab === 'administracion'">
    <div class="lg:col-span-2"><?php $renderPhTabSummary('resumen_administracion_ph', 'Resumen depurado para Entregable'); ?></div>
    <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-admin-support.php'; ?>
</section>

<?php foreach ([
    'riesgos' => ['risks', 'Riesgos, restricciones y afectaciones PH', $phCatalog['risks']],
    'documentos' => ['documents', 'Soportes documentales PH', $phCatalog['documents']],
    'fotos' => ['photos', 'Fotos requeridas para 3.6', $phCatalog['photos']],
] as $tabKey => [$groupKey, $title, $items]): ?>
    <section class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4" x-show="tab === 'notas'">
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
                    <textarea class="input mt-2 min-h-20" rows="2" name="ph[<?= e($groupKey) ?>][<?= e($key) ?>][notes]" placeholder="Observación del analista"><?= e($phMap($groupKey, (string) $key, 'notes')) ?></textarea></label>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>

<section class="mt-5 grid gap-4" x-show="tab === 'incidencia'">
    <?php $renderPhTabSummary('resumen_incidencia_ph', 'Resumen depurado para Entregable'); ?>
    <?php $renderPhTextarea('diagnosis_text', 'Diagnóstico preliminar de copropiedad', $phText('diagnosis_text'), 'Resume si la PH está ordenada, requiere soportes o presenta alertas.', 5); ?>
    <?php $renderPhTextarea('report_text', 'Texto para el entregable', $phText('report_text'), 'Incluye la advertencia de que es informe técnico y no estudio de títulos.', 6); ?>
    <?php $renderTechTextarea('lectura_valuatoria', 'Incidencia funcional, comercial y valuatoria', $technicalValue('lectura_valuatoria')); ?>
    <?php $renderTechTextarea('comparacion_mercado_ph', 'Comparación con copropiedades similares', $technicalValue('comparacion_mercado_ph')); ?>
</section>

<section class="mt-5 grid gap-4" x-show="tab === 'notas'">
    <?php $renderPhTabSummary('resumen_notas_ph', 'Resumen depurado para Entregable'); ?>
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-700">
        <h3 class="font-semibold text-slate-900">Notas normativas sugeridas</h3>
        <ul class="mt-2 space-y-1">
            <?php foreach ($phCatalog['normNotes'] as $note): ?><li>• <?= e((string) $note) ?></li><?php endforeach; ?>
        </ul>
    </div>
    <?php $renderTechTextarea('salvedades_reglamento', 'Salvedades del reglamento', $technicalValue('salvedades_reglamento')); ?>
    <?php $renderTechTextarea('salvedades_visita', 'Salvedades de visita', $technicalValue('salvedades_visita')); ?>
    <?php $renderTechTextarea('salvedades_validacion', 'Salvedades de validación documental', $technicalValue('salvedades_validacion')); ?>
</section>
