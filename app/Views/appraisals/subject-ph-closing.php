<section class="mt-5 grid gap-4 lg:grid-cols-2" x-show="tab === 'administracion'">
    <div class="lg:col-span-2"><?php $renderPhTabSummary('resumen_administracion_ph', 'Resumen depurado para Entregable'); ?></div>
    <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-admin-support.php'; ?>
</section>

<section class="mt-5 grid gap-4" x-show="tab === 'incidencia'">
    <?php $renderPhTabSummary('resumen_incidencia_ph', 'Resumen depurado para Entregable'); ?>
    <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-incidence-support.php'; ?>
</section>

<section class="mt-5 grid gap-4" x-show="tab === 'notas'">
    <?php $renderPhTabSummary('resumen_notas_ph', 'Resumen depurado para Entregable'); ?>
    <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-notes-support.php'; ?>
</section>
