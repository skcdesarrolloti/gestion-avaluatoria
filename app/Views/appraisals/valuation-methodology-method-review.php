<?php $methodReview = \App\Services\Resolution941MethodReview::for($guideKey); ?>
<details class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6">
    <summary class="min-h-11 cursor-pointer py-3 font-semibold">Revisión de <?= e($methodGuide['label']) ?> · requisitos y pendientes</summary>
    <p class="mt-2 font-semibold"><?= e($methodReview['reference']) ?></p>
    <p class="mt-2"><strong>Disponible hoy:</strong> <?= e($methodReview['available']) ?></p>
    <ul class="mt-3 list-disc space-y-2 pl-5">
        <?php foreach ($methodReview['checks'] as $check): ?><li><?= e($check) ?></li><?php endforeach; ?>
    </ul>
    <p class="mt-3"><strong>Por completar:</strong> <?= e($methodReview['pending']) ?></p>
    <a class="inline-flex min-h-11 items-center underline" target="_blank" rel="noopener" data-no-fetch
        href="<?= e(\App\Support\IgacDocumentLibrary::find('anexo-tecnico-resolucion-igac-941-2026')['archivo_descarga'] . '#page=' . $methodReview['page']) ?>">Leer sección del anexo técnico oficial</a>
</details>
