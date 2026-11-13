<?php
$originOld=json_decode(\App\Core\Session::pullFlash('income_origin_'.$record['id']) ?: '{}',true) ?: [];
$originFields=['value_date'=>['Fecha del avalúo · encargo','date'],'label'=>['Nombre de la unidad · capítulo 3','text'],
    'property_type'=>['Tipo de inmueble · capítulo 3','select'],'area_private_m2'=>['Área privada (m²)','text'],
    'area_adopted_m2'=>['Área de terreno adoptada (m²)','text'],'built_area_adopted_m2'=>['Área construida adoptada (m²)','text'],
    'surface_source'=>['Fuente de las superficies · capítulo 3','text']];
$originValues=array_replace($leaseUnit,['value_date'=>$record['value_date'] ?? ''],$originOld['old']['origin'] ?? []);
$originOccupation=array_replace(['occupancy'=>'','date'=>'','source'=>''],$leaseOccupation,$originOld['old']['occupation'] ?? []);
$originEndpoint=url($basePath.'/renta/origen');$originErrors=$originOld['errors'] ?? [];
?>
<details id="renta-origen" class="mb-4 rounded-xl border bg-white p-5" <?= $originOld?'open':'' ?> x-show="incomeTab==='concept' || incomeTab==='evidence'">
    <summary class="min-h-11 cursor-pointer font-semibold">Consultar o corregir fecha del avalúo, inmueble y ocupación · sin salir de Renta</summary>
    <p class="mt-3 text-sm">Editas los mismos datos del encargo y del capítulo 3, sin duplicarlos. Las áreas conservan su significado; no se convierten en área arrendable. Si cambias el tipo, revisa también la tipología constructiva conservada en el capítulo 3.</p>
    <form class="mt-4" method="post" action="<?= e($originEndpoint) ?>" data-module-autosave data-save-in-place data-income-origin-version data-autosave-endpoint="<?= e($originEndpoint) ?>"
        x-data="{issues:<?= e(json_encode((object)$originErrors,JSON_THROW_ON_ERROR)) ?>}" @ga:validation="issues=$event.detail" @ga:saved="originSaved($event.detail,$el)">
        <?= csrf_field() ?><input type="hidden" name="component" value="<?= e($componentKey) ?>">
        <input type="hidden" name="version" value="<?= (int)($originOld['old']['version'] ?? $record['version']) ?>">
        <input type="hidden" name="occupation_version" value="<?= (int)($originOld['old']['occupation_version'] ?? $leaseUnit['occupation_version'] ?? 0) ?>">
        <div class="grid gap-4 md:grid-cols-2"><?php foreach ($originFields as $key=>[$label,$type]): $path='origin['.$key.']'; ?>
            <div><label class="label" for="origin-<?= e($key) ?>"><?= e($label) ?></label>
                <?php if ($type==='select'): ?><select class="input" id="origin-<?= e($key) ?>" name="<?= e($path) ?>" :aria-invalid="Boolean(issues['<?= e($path) ?>'])" aria-describedby="origin-<?= e($key) ?>-help origin-<?= e($key) ?>-error"><option value="">Por verificar</option><?php foreach (\App\Support\AppraisalCatalog::selectFields()['tipo_inmueble'][4] as $value=>$text): ?><option value="<?= e($value) ?>" <?= ($originValues[$key] ?? '')===$value?'selected':'' ?>><?= e($text) ?></option><?php endforeach; ?></select>
                <?php else: ?><input class="input" id="origin-<?= e($key) ?>" type="<?= e($type) ?>" name="<?= e($path) ?>" value="<?= e((string)($originValues[$key] ?? '')) ?>" <?= $type==='date'?'':'placeholder="'.e(str_ends_with($key,'_m2')?'Ej. 80,50; vacío si se desconoce':'Dato documentado en su fuente').'"' ?> :aria-invalid="Boolean(issues['<?= e($path) ?>'])" aria-describedby="origin-<?= e($key) ?>-help origin-<?= e($key) ?>-error"><?php endif; ?>
                <p class="text-xs" id="origin-<?= e($key) ?>-help"><?= $type==='date'?'Fecha a la que corresponde la valoración.':'Conserva la fuente y el alcance del dato original.' ?></p>
                <p class="text-xs text-red-800" id="origin-<?= e($key) ?>-error" x-text="issues['<?= e($path) ?>'] || ''"><?= e($originErrors[$path] ?? '') ?></p>
            </div><?php endforeach; ?>
            <label class="label">Ocupación de la unidad<select class="input" name="occupation[occupancy]" aria-describedby="origin-occupation-help origin-occupation-error" :aria-invalid="Boolean(issues['occupation[occupancy]'])"><option value="">Por verificar</option><?php foreach (\App\Services\IncomeConcept::questions()['occupancy']['values'] as $v=>$label): ?><option value="<?= e($v) ?>" <?= $originOccupation['occupancy']===$v?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
            <label class="label">Fecha del soporte de ocupación<input class="input" type="date" name="occupation[date]" value="<?= e($originOccupation['date']) ?>" aria-describedby="origin-occupation-help origin-occupation-error" :aria-invalid="Boolean(issues['occupation[date]'])"></label>
            <label class="label">Soporte de ocupación<textarea class="input" name="occupation[source]" placeholder="Ej. Visita, contrato y espacios ocupados" aria-describedby="origin-occupation-help origin-occupation-error" :aria-invalid="Boolean(issues['occupation[source]'])"><?= e($originOccupation['source']) ?></textarea></label>
        </div>
        <p class="mt-2 text-xs" id="origin-occupation-help">Ocupación actual de esta unidad según visita o soporte; el historial mensual se documenta aparte.</p>
        <p class="mt-2 text-red-800" id="origin-occupation-error" role="alert" x-text="Object.values(issues).join(' ')"><?= e($originOld['message'] ?? '') ?></p>
        <button type="submit" class="btn-primary mt-4">Guardar en su origen / reintentar</button><p class="mt-2 text-sm" data-autosave-status role="status">Sin cambios pendientes.</p>
    </form>
</details>
