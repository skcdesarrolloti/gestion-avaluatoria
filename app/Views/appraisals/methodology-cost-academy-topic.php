<h5 class="font-semibold"><?= e($topic['title']) ?></h5>
<p class="mt-2 text-xs font-semibold text-indigo-800"><?= e($topic['reference']) ?></p>
<?php if ($topic['id']==='vidas'): ?>
<p class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm"><strong>Los 100 años siguen como referencia para ciertas construcciones permanentes.</strong> No son una vida universal. La vida remanente y la prolongada requieren verificar sus condiciones; no se asignan automáticamente.</p>
<?php endif; ?>
<ul class="mt-3 list-disc space-y-3 pl-5 text-sm leading-6"><?php foreach ($topic['items'] as $bullet): ?><li><?= e($bullet) ?></li><?php endforeach; ?></ul>
<?php if (isset($topic['rows'])): ?>
<div class="mt-4 overflow-x-auto rounded-lg border" tabindex="0" role="region" aria-label="Tabla de <?= e($topic['title']) ?>">
    <table class="w-full text-left text-sm">
        <caption class="px-3 py-2 text-left text-xs">Síntesis académica: consulta la tabla original y sus condiciones antes de adoptar parámetros.</caption>
        <thead class="bg-indigo-50"><tr><?php foreach ($topic['headers'] as $heading): ?><th scope="col" class="px-3 py-3 font-semibold"><?= e($heading) ?></th><?php endforeach; ?></tr></thead>
        <tbody><?php foreach ($topic['rows'] as $row): ?><tr class="border-t"><?php foreach ($row as $index=>$cell): ?><?php if ($index===0): ?><th scope="row" class="px-3 py-3 font-semibold"><?= e($cell) ?></th><?php else: ?><td class="px-3 py-3"><?= e($cell) ?></td><?php endif; ?><?php endforeach; ?></tr><?php endforeach; ?></tbody>
    </table>
</div>
<?php endif; ?>
<a class="mt-3 inline-flex min-h-11 items-center text-sm font-semibold text-blue-800 underline" href="<?= e(isset($topic['page']) ? \App\Services\CostMethodAcademy::ANNEX_URL.'#page='.$topic['page'] : \App\Services\CostMethodAcademy::RESOLUTION_URL) ?>" target="_blank" rel="noopener" data-no-fetch>Consultar fuente de este apartado</a>
