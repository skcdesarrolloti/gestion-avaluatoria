<?php
$base='avaluos/'.$record['id'].'/metodologia-valuatoria';
$areaUrl=static fn(int $p): string=>url($base.'/comparables/areas?'.http_build_query(['component'=>$componentKey,'portal'=>$portal,'page'=>$p]));
$studyUrl=url($base.'?'.http_build_query(['component'=>$componentKey,'stage'=>'3','renta_paso'=>'capture']));
?>
<p class="eyebrow">Investigación · <?= e($componentLabel) ?></p>
<h1 class="mt-2 text-3xl font-semibold">Completar áreas de los anuncios</h1>
<p class="mt-3">Registra el área publicada, su significado y la fuente. Se actualizan las mismas muestras del estudio.</p>
<p class="mt-2 text-sm">Conserva la distinción entre área construida, privada y arrendable. Completar el área no confirma comparabilidad ni cambia exclusiones.</p>
<a href="<?= e($studyUrl) ?>" class="btn-secondary mt-4">Volver a la captura</a>
<form method="get" action="<?= e(url($base.'/comparables/areas')) ?>" class="my-5 flex flex-wrap items-end gap-3">
    <input type="hidden" name="component" value="<?= e($componentKey) ?>">
    <label class="block font-semibold">Fuente<select class="input" name="portal"><option value="">Todas las fuentes</option>
        <?php foreach ($portals as $label): ?><option value="<?= e($label) ?>" <?= $portal===$label?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?>
    </select></label><button class="btn-secondary">Filtrar</button>
</form>
<p class="my-3 font-semibold"><?= $total ?> anuncios · Página <?= $page ?> de <?= $pages ?></p>
<?php if (!$rows): ?><p>No hay anuncios de esta fuente en la investigación.</p><?php else: ?>
<form method="post" action="<?= e(url($base.'/comparables/areas')) ?>" data-module-autosave data-save-in-place
    data-autosave-endpoint="<?= e(url($base.'/comparables/areas')) ?>" x-data="{issues:{}}" @ga:validation="issues=$event.detail">
    <?= csrf_field() ?>
    <input type="hidden" name="component_scope" value="<?= e($componentKey) ?>">
    <input type="hidden" name="study_scope" value="<?= e($study['token']) ?>">
    <input type="hidden" name="version" value="<?= (int)$record['comparables_version'] ?>">
    <div class="sticky top-0 z-10 mb-4 flex flex-wrap items-center gap-3 rounded-xl border bg-white p-3">
        <button type="submit" class="btn-primary">Guardar áreas ahora</button>
        <span data-autosave-status aria-live="polite">Autoguardado activo</span>
    </div>
    <?php foreach ($rows as $row): ?>
    <article class="mb-4 rounded-xl border bg-white p-4" data-area-sample="<?= e($row['id']) ?>">
        <h2 class="text-lg font-semibold">Muestra <?= (int)$row['sample_index'] ?> · <?= e($row['source_name']) ?></h2>
        <p class="my-2 text-sm"><?= e($row['property_type']) ?> · <?= e($row['neighborhood']) ?> · <?= e($row['project_name']) ?></p>
        <?php if (preg_match('~^https?://~i',$row['source_url'])): ?><a href="<?= e($row['source_url']) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center text-blue-800 underline">Consultar anuncio original</a><?php endif; ?>
        <div class="grid gap-4 md:grid-cols-2">
        <?php foreach (['area_m2'=>['Área publicada (m²)','Ej. 98,55; sin separador de miles'],'area_basis'=>['Qué área publica la fuente','Ej. construida 98,55 m² y privada 95 m²'],'areas_source'=>['Fuente y salvedades de las áreas','Enlace, fecha de revisión y diferencias por comprobar']] as $field=>[$label,$hint]):
            $name='areas['.$row['id'].']['.$field.']';$fieldId='area-'.$row['id'].'-'.$field; ?>
        <label class="block font-semibold <?= $field==='areas_source'?'md:col-span-2':'' ?>" for="<?= e($fieldId) ?>"><?= e($label) ?>
            <?php if ($field==='area_m2'): ?>
            <input id="<?= e($fieldId) ?>" name="<?= e($name) ?>" value="<?= e((string)($row[$field] ?? '')) ?>" inputmode="decimal" class="input" placeholder="<?= e($hint) ?>"
                :aria-invalid="Boolean(issues[<?= e(json_encode($name)) ?>])" aria-describedby="<?= e($fieldId) ?>-error">
            <?php else: ?>
            <textarea id="<?= e($fieldId) ?>" name="<?= e($name) ?>" rows="3" maxlength="1600" class="input" placeholder="<?= e($hint) ?>"
                :aria-invalid="Boolean(issues[<?= e(json_encode($name)) ?>])" aria-describedby="<?= e($fieldId) ?>-error"><?= e($row[$field] ?? '') ?></textarea>
            <?php endif; ?>
            <span id="<?= e($fieldId) ?>-error" class="text-sm text-red-800" x-text="issues[<?= e(json_encode($name)) ?>] || ''"></span>
        </label>
        <?php endforeach; ?>
        </div>
    </article>
    <?php endforeach; ?>
</form>
<?php endif; ?>
<nav class="mt-4 flex flex-wrap gap-3" aria-label="Páginas de áreas">
    <?php if ($page>1): ?><a class="btn-secondary" href="<?= e($areaUrl($page-1)) ?>">Anterior</a><?php endif; ?>
    <?php if ($page<$pages): ?><a class="btn-primary" href="<?= e($areaUrl($page+1)) ?>">Siguiente</a><?php endif; ?>
</nav>
