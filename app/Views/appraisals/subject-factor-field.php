<?php
$captureItem=$captureSaved[$captureKey] ?? [];
$captureValue=(string)($captureItem['value'] ?? '');
$captureStale=$captureItem!==[] && !\App\Services\SubjectFactorCapture::compatible($captureItem,$captureFactor);
$captureOptions=explode("\n",$captureFactor['categories']);
$captureName='factors['.$captureKey.']';
$captureSource=\App\Services\SubjectFactorSource::resolve($captureKey,$captureFactor,$factorUnit);
$captureLinked=trim($captureValue)==='' && trim((string)($captureItem['support'] ?? ''))==='' && !empty($captureSource['usable']);
$captureCodeMap=[];
foreach ($captureOptions as $optionLabel) $captureCodeMap[$optionLabel]=\App\Services\SubjectFactorCapture::code($optionLabel,$captureFactor);
$captureLiveCode=$captureFactor['kind']==='numeric'?'captureValue === "" ? "Pendiente" : "Medida: " + captureValue':json_encode($captureCodeMap,JSON_UNESCAPED_UNICODE).'[captureValue] || "Pendiente"';
if ($captureStale) $captureLiveCode='(!captureConfirmed && captureValue === '.json_encode($captureValue,JSON_UNESCAPED_UNICODE).') ? "Pendiente por cambio de clasificación" : ('.$captureLiveCode.')';
?>
<fieldset class="rounded-xl border p-4" x-show="<?= e(json_encode(mb_strtolower($captureFactor['label']),JSON_UNESCAPED_UNICODE)) ?>.includes(factorSearch.toLowerCase())" x-data="{ captureValue: <?= e(json_encode($captureValue,JSON_UNESCAPED_UNICODE)) ?>, captureConfirmed: <?= $captureStale?'false':'true' ?> }">
    <legend class="px-1 font-semibold"><?= e($captureFactor['label']) ?> · <?= e($captureFactor['unit']) ?></legend>
    <p class="text-xs"><?= e($captureFactor['why']) ?></p>
    <p class="mt-2 text-sm font-semibold"><?= e(\App\Services\ResearchFactorReference::scale($captureFactor)) ?></p>
    <?php foreach (\App\Services\SubjectAttributeResearch::previous($factorUnit,$captureFactor) as $capturePrevious): ?>
    <div class="mt-2 rounded bg-blue-50 p-2 text-xs">
        <strong>Calificación valuatoria actual · <?= e($capturePrevious['label']) ?>:</strong>
        <?= e($capturePrevious['observed']) ?> · calificación <?= e($capturePrevious['rating'] ?: 'pendiente') ?>/5 · peso <?= e($capturePrevious['weight'] ?: 'pendiente') ?>.
        <?php if ($capturePrevious['notes']!==''): ?><p><?= e($capturePrevious['notes']) ?></p><?php endif; ?>
        <p>Registro anterior conservado; su calificación y su peso permanecen independientes.</p>
    </div>
    <?php endforeach; ?>
    <?php if ($captureLinked): ?>
        <div class="mt-3 rounded bg-slate-100 p-3 text-sm">
            <label class="block font-semibold">Dato de <?= e($captureFactor['label']) ?> · ya registrado
                <input class="mt-1 block min-h-11 w-full rounded-lg border border-slate-300 bg-slate-200 px-3 py-2 text-slate-800" type="text" value="<?= e($captureSource['value']) ?>" readonly aria-readonly="true" placeholder="Dato tomado del registro original">
            </label>
            <p class="mt-2 text-xs">Dato proveniente de <?= e($captureSource['section']) ?> · <?= e($captureSource['field']) ?>; no digitado en esta sección.</p>
            <p><?= e(\App\Services\SubjectFactorCapture::code($captureSource['value'],$captureFactor)) ?></p>
            <p class="mt-1 text-xs"><?= e($captureSource['support']) ?></p>
            <a class="mt-2 inline-block min-h-11 font-semibold text-blue-700" href="<?= e($captureSource['hash']) ?>">Editar en <?= e($captureSource['section']) ?></a>
            <p class="text-xs">Se reutiliza en el módulo 8. Al corregir el registro original y volver a esta sección, se muestra actualizado.</p>
        </div>
    <?php else: ?>
    <?php if ($captureSource!==[]): ?><p class="mt-2 rounded bg-slate-100 p-2 text-xs">
        <?php if ($captureSource['value']===''): ?>Dato proveniente de <?= e($captureSource['section']) ?> · <?= e($captureSource['field']) ?>: no digitado en su origen.
        <?php else: ?>Dato original: <?= e($captureSource['value']) ?> · módulo <?= e($captureSource['section']) ?>. <?= $captureItem!==[]?'Se conserva la clasificación de investigación ya guardada.':'Requiere precisar su equivalencia con esta escala; no se infiere una medida o categoría distinta.' ?><?php endif; ?></p>
    <?php endif; ?>
    <?php if ($captureStale): ?><p class="mt-2 rounded bg-amber-50 p-2 text-xs">Clasificación anterior conservada: <?= e($captureValue ?: 'Pendiente') ?>. Revisa el dato y confirma expresamente su clasificación actual.</p><?php endif; ?>
    <input type="hidden" name="<?= e($captureName) ?>[scale_kind]" value="<?= e($captureFactor['kind']) ?>">
    <input type="hidden" name="<?= e($captureName) ?>[catalog_signature]" value="<?= e(\App\Services\SubjectFactorCapture::signature($captureFactor)) ?>">
    <input type="hidden" name="<?= e($captureName) ?>[scale_categories]" value="<?= e($captureFactor['categories']) ?>">
    <label class="mt-3 block text-sm font-semibold">Dato de <?= e($captureFactor['label']) ?> · sujeto
    <?php if ($captureFactor['kind']==='numeric'): ?>
        <input class="input" type="text" inputmode="decimal" name="<?= e($captureName) ?>[value]" value="<?= e($captureValue) ?>" maxlength="120" placeholder="Medida en <?= e($captureFactor['unit']) ?>; vacío si se desconoce" x-model="captureValue">
    <?php else: ?>
        <select class="input" name="<?= e($captureName) ?>[value]" x-model="captureValue">
            <option value="">Pendiente de verificar</option>
            <?php if ($captureValue!=='' && !in_array($captureValue,$captureOptions,true)): ?><option value="<?= e($captureValue) ?>" selected>Anterior por revisar: <?= e($captureValue) ?></option><?php endif; ?>
            <?php foreach ($captureOptions as $optionIndex=>$optionLabel): ?>
                <option value="<?= e($optionLabel) ?>" <?= $captureValue===$optionLabel?'selected':'' ?>><?= e(in_array($captureFactor['kind'],['binary','ordinal'],true)?$optionIndex.' = '.$optionLabel:$optionLabel) ?></option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>
    </label>
    <label class="mt-3 block text-sm font-semibold">Soporte de <?= e($captureFactor['label']) ?><textarea class="input" name="<?= e($captureName) ?>[support]" rows="2" maxlength="600" placeholder="Ej. Inspección, fecha, foto o documento y página"><?= e($captureItem['support'] ?? '') ?></textarea></label>
    <?php if ($captureStale): ?><label class="mt-2 flex min-h-11 items-center gap-2 text-sm"><input type="checkbox" name="<?= e($captureName) ?>[confirm_scale]" value="1" x-model="captureConfirmed">Confirmo el dato con la clasificación actual</label><?php endif; ?>
    <p class="mt-1 text-xs" x-text="<?= e($captureLiveCode) ?>"></p>
    <?php if (!$captureFactor['scale_valid']): ?><p class="mt-2 text-xs text-amber-900">Corrige primero esta escala en Configuración → Catálogo de factores del capítulo 8.</p><?php endif; ?>
    <?php endif; ?>
</fieldset>
