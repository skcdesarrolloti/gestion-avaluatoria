<?php $baseScale=\App\Services\ResearchFactorCatalog::all()[$factorKey]; ?>
<?php if ($factorKey!=='view' && $factor['scale_help']!==''): ?><p class="mt-2 text-xs"><?= e($factor['scale_help']) ?></p><?php endif; ?>
<?php if (!$factor['scale_valid']): ?><p class="mt-2 rounded bg-amber-50 p-2 text-sm text-amber-900">Escala anterior por revisar: conserva sus datos, pero su clasificación no sigue la pauta actual. Corrige el catálogo y adopta expresamente la escala en Insumos.</p><?php endif; ?>
<?php if ($baseScale['kind']==='categorical' && $factorKey!=='view'): ?>
<details class="mt-3">
    <summary class="min-h-11 cursor-pointer font-semibold">Definir clases de <?= e($factor['label']) ?></summary>
    <form data-ph-section class="mt-2" method="post" action="<?= e(url($basePath.'/escalas')) ?>" data-module-autosave data-save-in-place data-autosave-endpoint="<?= e(url($basePath.'/escalas')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="version" value="<?= (int)($factorScales[$factorKey]['version'] ?? 0) ?>">
        <input type="hidden" name="factor_key" value="<?= e($factorKey) ?>">
        <input type="hidden" name="kind" value="categorical">
        <p class="font-semibold">Clases sin orden natural · no reciben puestos de mejor a peor.</p>
        <label class="mt-2 block font-semibold">Clases · una por línea<textarea class="input" name="categories" rows="5" maxlength="1200" placeholder="Escribe las clases observables del atributo"><?= e($factor['categories']) ?></textarea></label>
        <p class="mt-2 text-xs">Ordenar esta lista no cambia el valor de las clases. Desconocido queda pendiente; no lo incluyas como cero.</p>
        <p class="mt-2 text-xs">Se reutiliza en tus avalúos y tipos que tengan este factor. Los planes anteriores conservan su escala hasta que elijas usar ésta en Insumos.</p>
        <button type="submit" class="btn-secondary mt-2">Guardar escala</button><span class="mt-2 block text-xs" data-autosave-status role="status">Autoguardado de escala activo.</span>
    </form>
</details>
<?php endif; ?>
<?php if (($baseScale['kind']==='ordinal' || $factorKey==='view') && !$factor['scale_valid']): ?>
<p class="text-xs">Pauta actual: <?= e(\App\Services\ResearchFactorReference::scale($baseScale)) ?></p>
<form data-ph-section method="post" action="<?= e(url($basePath.'/escalas')) ?>">
    <?= csrf_field() ?><input type="hidden" name="version" value="<?= (int)($factorScales[$factorKey]['version'] ?? 0) ?>">
    <input type="hidden" name="factor_key" value="<?= e($factorKey) ?>"><input type="hidden" name="kind" value="<?= e($baseScale['kind']) ?>">
    <input type="hidden" name="categories" value="<?= e($baseScale['categories']) ?>">
    <button class="btn-secondary mt-2" type="submit">Restablecer clasificación definida del catálogo</button>
</form>
<?php endif; ?>
