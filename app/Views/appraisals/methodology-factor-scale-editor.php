<?php if (in_array($factor['kind'],['categorical','ordinal'],true)): ?>
<details class="mt-3">
    <summary class="min-h-11 cursor-pointer font-semibold">Definir jerarquía / clases de <?= e($factor['label']) ?></summary>
    <form data-ph-section class="mt-2" x-data="{kind:<?= e(json_encode($factor['kind'])) ?>,levels:<?= e(json_encode($factor['categories'])) ?>}" method="post" action="<?= e(url($basePath.'/escalas')) ?>" data-module-autosave data-save-in-place data-autosave-endpoint="<?= e(url($basePath.'/escalas')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="version" value="<?= (int)($factorScales[$factorKey]['version'] ?? 0) ?>">
        <input type="hidden" name="factor_key" value="<?= e($factorKey) ?>">
        <label class="block font-semibold">Clasificación<select class="input" name="kind" x-model="kind"><option value="categorical">Clases sin orden natural</option><option value="ordinal">Jerarquía de menor a mayor</option></select></label>
        <label class="mt-2 block font-semibold">Clases o niveles · uno por línea<textarea class="input" name="categories" x-model="levels" rows="5" maxlength="1200" placeholder="Sin cobertura&#10;Cobertura parcial&#10;Cobertura total"></textarea></label>
        <p class="mt-2 text-xs font-semibold" x-text="kind==='ordinal'?levels.split('\n').filter(s=>s.trim()).map((s,i)=>`${i} = ${s.trim()}`).join(' · '):'Clases sin jerarquía numérica'"></p>
        <p class="mt-2 text-xs">En jerarquía, primera línea = 0; después 1, 2… de menor a mayor. En clases sin orden, no se impone una jerarquía. Nunca incluyas desconocido como cero.</p>
        <p class="mt-2 text-xs">Se reutiliza en tus avalúos y tipos que tengan este factor. Los planes anteriores conservan su escala hasta que elijas usar ésta en Insumos.</p>
        <button type="submit" class="btn-secondary mt-2">Guardar escala</button><span class="mt-2 block text-xs" data-autosave-status role="status">Autoguardado de escala activo.</span>
    </form>
</details>
<?php endif; ?>
