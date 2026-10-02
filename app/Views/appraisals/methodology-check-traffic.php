<?php $trafficApplicable = count($trafficChecks['rows']) - $trafficChecks['na']; ?>
<div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold" role="status" aria-label="Semáforo de controles">
    <span class="rounded-full px-2 py-1 <?= $trafficChecks['ok'] > 0 ? 'bg-emerald-100 text-emerald-950' : 'bg-slate-100 text-slate-700' ?>"><?= $trafficChecks['ok'] > 0 ? '✓' : '○' ?> <?= $trafficChecks['ok'] ?> de <?= $trafficApplicable ?> controles completos</span>
    <?php if ($trafficChecks['missing'] > 0): ?><span class="rounded-full bg-red-100 px-2 py-1 text-red-950">● <?= $trafficChecks['missing'] ?> con datos faltantes</span><?php endif; ?>
    <?php if ($trafficChecks['differences'] > 0): ?><span class="rounded-full bg-amber-100 px-2 py-1 text-amber-950">● <?= $trafficChecks['differences'] ?> con diferencias</span><?php endif; ?>
    <?php if ($trafficChecks['na'] > 0): ?><span class="rounded-full bg-slate-100 px-2 py-1 text-slate-700"><?= $trafficChecks['na'] ?> no aplican</span><?php endif; ?>
</div>
