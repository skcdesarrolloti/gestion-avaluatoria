<?php $portalSearch=\App\Services\ComparablePortalSearchPrompt::build($guide,$source); ?>
<section class="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-4" x-data="{ searchCopyStatus: '' }" data-portal-search="<?= e($source['label']) ?>">
    <h4 class="font-semibold">Búsqueda para <?= e($componentLabel ?? $guide['type_label']) ?> · <?= e($source['label']) ?></h4>
    <?php if ($portalSearch['missing']): ?><p class="mt-2 text-sm text-amber-900">Completa tipo, operación y ciudad de esta unidad para precisar la búsqueda.</p><?php endif; ?>
    <label class="label mt-3">Texto breve para buscar en <?= e($source['label']) ?>
        <textarea class="input mt-1" rows="2" readonly x-ref="portalSearchText" placeholder="Búsqueda según esta unidad y su ubicación."><?= e($portalSearch['query']) ?></textarea>
    </label>
    <button type="button" class="btn-secondary mt-2" @click="navigator.clipboard.writeText($refs.portalSearchText.value).then(() => searchCopyStatus = 'Búsqueda copiada.').catch(() => searchCopyStatus = 'Selecciona el texto y copia con Ctrl+C.')">Copiar búsqueda de <?= e($source['label']) ?></button>
    <p class="mt-3 text-sm"><strong>Filtros que debes buscar:</strong> <?= e($portalSearch['filters']) ?></p>
    <details class="mt-2 text-sm"><summary class="min-h-11 cursor-pointer py-2 font-semibold">Cómo aplicar los filtros en este portal</summary>
        <p><?= e($portalSearch['help']) ?></p>
        <p class="mt-1">Si sólo admite ubicación, escribe barrio o ciudad y selecciona operación y tipo en sus filtros. No exige garaje o depósito para iniciar; si faltan en el aviso, quedan por verificar.</p>
    </details>
    <?php if ($portalSearch['google']!==''): ?>
    <details class="mt-2 text-sm"><summary class="min-h-11 cursor-pointer py-3 font-semibold">Alternativa: buscar este portal desde Google</summary>
        <label class="label">Consulta restringida a esta fuente<textarea class="input mt-1" rows="2" readonly x-ref="portalGoogleText" placeholder="Consulta con dominio del portal."><?= e($portalSearch['google']) ?></textarea></label>
        <button type="button" class="btn-secondary mt-2" @click="navigator.clipboard.writeText($refs.portalGoogleText.value).then(() => searchCopyStatus = 'Consulta para Google copiada.').catch(() => searchCopyStatus = 'Selecciona el texto y copia con Ctrl+C.')">Copiar consulta para Google</button>
    </details>
    <?php endif; ?>
    <?php if ($portalSearch['alternatives']!==[]): ?><details class="mt-2 text-sm"><summary class="min-h-11 cursor-pointer py-3 font-semibold">Otras denominaciones de esta unidad</summary><p>Prueba cada búsqueda por separado y verifica que el aviso corresponda al uso y derecho valorados.</p><ul class="mt-2 list-disc pl-5"><?php foreach ($portalSearch['alternatives'] as $alternative): ?><li><?= e($alternative) ?></li><?php endforeach; ?></ul></details><?php endif; ?>
    <p class="mt-2 text-sm" role="status" x-text="searchCopyStatus"></p>
</section>
