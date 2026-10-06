<?php
$stale = !empty($selected['analysis']) && ($selected['evidence_hash'] ?? '') !== \App\Services\MethodologyWorkflow::fingerprint($comparableRows);
?>
<section class="rounded-2xl border bg-white p-5 sm:p-8">
    <h2 class="text-2xl font-semibold">M<?= e($stage) ?> <?= $stage === '4' ? 'Análisis' : 'Entregable' ?> · <?= e($componentLabel) ?></h2>
    <?php require __DIR__ . '/methodology-ph-guidance.php'; ?>
    <?php if ($componentKey === '' || ($selected['method'] ?? '') !== 'mercado'): ?>
        <p class="mt-4 rounded-xl bg-amber-50 p-4">Selecciona Mercado para un componente en M2 antes de documentar su análisis. Las muestras siguen conservadas.</p>
    <?php else: ?>
    <?php if ($stale): ?><p role="alert" class="mt-4 rounded-xl bg-amber-50 p-4">Las muestras cambiaron desde la última redacción. Revisa el análisis y la conclusión antes de utilizarlos.</p><?php endif; ?>
    <?php if ($stage === '4'): ?>
        <?php require __DIR__ . '/methodology-intake-analysis.php'; ?>
        <details class="mt-4 rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Academia y criterio de adopción · artículo 21</summary>
            <?php $readingNumber = 21; require __DIR__ . '/valuation-methodology-article-reading.php'; ?>
            <p class="mt-3 text-sm">La dispersión no sustituye la revisión de comparabilidad. No se eliminan datos atípicos automáticamente ni se declara cumplimiento por obtener un CV bajo.</p>
        </details>
    <?php endif; ?>
    <form class="mt-5" method="post" action="<?= e(url($basePath . '/flujo')) ?>" data-module-autosave data-save-in-place data-autosave-endpoint="<?= e(url($basePath . '/flujo')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="version" value="<?= (int) ($record['methodology_version'] ?? 0) ?>">
        <input type="hidden" name="component" value="<?= e($componentKey) ?>">
        <?php if ($stage === '4'): ?>
        <label class="block font-semibold">Memoria del análisis del componente
            <textarea class="input" rows="8" name="analysis" maxlength="6000" placeholder="Documenta selección y exclusión de muestras, negociación, ajustes sustentados, cálculos y criterio de adopción."><?= e($selected['analysis'] ?? '') ?></textarea>
        </label>
        <?php else: ?>
        <p class="mb-4 whitespace-pre-wrap text-sm"><?= e($selected['reason'] ?? '') ?></p>
        <p class="mb-4 whitespace-pre-wrap text-sm"><?= e($selected['coverage'] ?? '') ?></p>
        <label class="block font-semibold">Conclusión para el entregable de Mercado
            <textarea class="input" rows="10" name="conclusion" maxlength="6000" placeholder="Redacta el resultado sustentado, unidad, fecha de valor, alcance y salvedades para el lector del informe."><?= e($selected['conclusion'] ?? '') ?></textarea>
        </label>
        <?php endif; ?>
        <p class="mt-3 text-sm" data-autosave-status>Autoguardado activo · máximo 6000 caracteres.</p>
        <button type="submit" class="btn-primary mt-3">Guardar ahora</button>
        <a class="btn-secondary mt-3" href="<?= e($flowUrl($stage === '4' ? '5' : 'integration')) ?>"><?= $stage === '4' ? 'Continuar a M5 Entregable' : 'Revisar integración' ?></a>
    </form>
    <?php endif; ?>
</section>
