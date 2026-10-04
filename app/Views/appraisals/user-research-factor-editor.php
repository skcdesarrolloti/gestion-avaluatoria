<?php
$editFactor=$editFactor ?? [];
$editKey=$editKey ?? '';
$editTypes=$editKey!==''?\App\Services\UserResearchFactors::types($editKey):[];
$editFeedback=($factorEditorFeedback['values']['factor_key'] ?? null)===$editKey?$factorEditorFeedback:[];
if ($editFeedback) {
    $editFactor=array_replace($editFactor,array_intersect_key($editFeedback['values'],array_flip(['label','why','unit','kind','categories','group'])));
    $editFactor['catalog_version']=(int)($editFeedback['values']['version'] ?? 0); $editTypes=$editFeedback['values']['types'];
}
$editKind=$editFactor['kind'] ?? 'binary';
?>
<details class="<?= $editKey===''?'mt-3 rounded border p-3':'mt-3' ?>" <?= $editFeedback?'open':'' ?>>
    <summary class="min-h-11 cursor-pointer font-semibold"><?= $editKey===''?'Crear nuevo factor':'Editar '.$editFactor['label'] ?></summary>
    <form class="mt-3" method="post" action="<?= e(url($basePath.'/factores')) ?>" x-data="{kind:<?= e(json_encode($editKind)) ?>,levels:<?= e(json_encode($editFactor['categories'] ?? "No\nSí")) ?>}">
        <?php if ($editFeedback): ?><p class="mb-3 rounded bg-amber-50 p-3 text-amber-900" role="alert"><?= e($editFeedback['error']) ?></p><?php endif; ?>
        <?= csrf_field() ?><input type="hidden" name="factor_key" value="<?= e($editKey) ?>"><input type="hidden" name="version" value="<?= (int)($editFactor['catalog_version'] ?? 0) ?>">
        <label class="block font-semibold">Nombre del factor<input class="input" name="label" maxlength="100" required value="<?= e($editFactor['label'] ?? '') ?>" placeholder="Ej. Jacuzzi privado"></label>
        <label class="mt-2 block font-semibold">Definición: qué se observa<textarea class="input" name="why" rows="2" maxlength="600" required placeholder="Describe el atributo y cómo verificarlo"><?= e($editFactor['why'] ?? '') ?></textarea></label>
        <label class="mt-2 block font-semibold">Tipo de dato<select class="input" name="kind" x-model="kind"><option value="">Selecciona el tipo de dato</option>
            <?php foreach (['numeric'=>'Medida o cantidad real','binary'=>'Presencia: 0 No / 1 Sí','ordinal'=>'Jerarquía: de menor a mayor','categorical'=>'Clases sin jerarquía'] as $kindValue=>$kindLabel): ?><option value="<?= e($kindValue) ?>" <?= $editKind===$kindValue?'selected':'' ?>><?= e($kindLabel) ?></option><?php endforeach; ?>
        </select></label>
        <label class="mt-2 block font-semibold">Unidad de medida<input class="input" name="unit" required maxlength="30" value="<?= e($editFactor['unit'] ?? 'sí/no') ?>" placeholder="Ej. cantidad, años, nivel, sí/no"></label>
        <label class="mt-2 block font-semibold">Alcance<select class="input" name="group" required><option value="">Selecciona el alcance</option>
            <?php foreach (['Unidad privada','Celdas de parqueo','Copropiedad PH','Terreno'] as $group): ?><option value="<?= e($group) ?>" <?= ($editFactor['group'] ?? 'Unidad privada')===$group?'selected':'' ?>><?= e($group) ?></option><?php endforeach; ?>
        </select></label>
        <div class="mt-2" x-show="kind==='ordinal' || kind==='categorical'">
            <label class="block font-semibold">Clases o niveles · uno por línea<textarea class="input" name="categories" rows="5" maxlength="1200" x-model="levels" placeholder="En jerarquías escribe los niveles de menor a mayor"><?= e($editFactor['categories'] ?? "No\nSí") ?></textarea></label>
            <p class="mt-2 text-sm" x-show="kind==='ordinal'" x-text="levels.split('\n').map((label,index)=>index+' = '+label.trim()).join(' · ')"></p>
            <p class="mt-2 text-xs" x-show="kind==='categorical'">Estas clases no reciben un puesto de mejor a peor.</p>
        </div>
        <p class="mt-2 text-sm" x-show="kind==='binary'">0 = No · 1 = Sí</p>
        <p class="mt-2 text-sm" x-show="kind==='numeric'">Se conserva la medida original; no se asignan códigos.</p>
        <fieldset class="mt-3"><legend class="font-semibold">Asignar a tipos de inmueble</legend><div class="grid gap-2 sm:grid-cols-2">
            <?php foreach (\App\Services\ComparablePortalProfiles::types() as $typeKey=>$typeLabel): ?><label class="flex min-h-11 items-center gap-2"><input type="checkbox" name="types[]" value="<?= e($typeKey) ?>" <?= in_array($typeKey,$editTypes,true)?'checked':'' ?>><?= e($typeLabel) ?></label><?php endforeach; ?>
        </div></fieldset>
        <p class="mt-2 text-xs">Desconocido queda pendiente. La misma escala se usa para sujeto y comparables. Al editar, los datos anteriores se conservan; las calificaciones afectadas requieren revisión.</p>
        <button type="submit" class="btn-primary mt-3"><?= $editKey===''?'Crear factor':'Guardar cambios del factor' ?></button>
    </form>
</details>
