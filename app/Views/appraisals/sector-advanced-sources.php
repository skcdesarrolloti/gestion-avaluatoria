<?php
use App\Support\AppraisalSectorAdvancedCatalog;
$sourceKeys = AppraisalSectorAdvancedCatalog::sourceKeys((string) $sectionCode);
$photoSupports = AppraisalSectorAdvancedCatalog::photoSupports((string) $sectionCode);
?>
<div class="mt-5 grid gap-4 lg:grid-cols-[1fr_.65fr]">
    <details class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <summary class="cursor-pointer text-sm font-semibold text-slate-800">
            Trazabilidad de fuentes
            <span class="ml-2 rounded-full bg-white px-2 py-0.5 text-xs text-blue-900"><?= e((string) count($sourceKeys)) ?></span>
        </summary>
        <div class="mt-3 flex flex-wrap gap-2">
        <?php foreach ($sourceKeys as $sourceKey): ?>
            <?php $source = $sectorBankSourcesByKey[$sourceKey] ?? null; ?>
            <?php if ($source): ?>
                <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs text-slate-700"
                    title="<?= e((string) ($source[3] ?? 'Entidad pendiente')) ?>">
                    <?= e((string) ($source[2] ?? 'Fuente')) ?>
                    <?= !empty($source[6]) ? ' · ' . e((string) $source[6]) : '' ?>
                </span>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
    </details>
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-700">
        <p class="text-xs font-semibold uppercase text-slate-600">Control de avance</p>
        <p class="mt-2"><strong>Estado:</strong> <?= e($sectionStatus) ?></p>
        <p><strong>Actualización:</strong> <?= e($sectionDate) ?></p>
        <?php if ($photoSupports): ?>
            <p class="mt-2 text-xs font-semibold text-teal-800">
                Soportes: <?= e(implode(' · ', $photoSupports)) ?>.
                Se cargan en esta misma pestaña.
            </p>
        <?php endif; ?>
    </div>
</div>
