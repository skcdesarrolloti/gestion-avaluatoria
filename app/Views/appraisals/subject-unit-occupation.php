<?php
$occupationOld=json_decode(\App\Core\Session::pullFlash('occupation_'.$unit['id']) ?: '{}',true) ?: [];
$occupation=array_replace(['occupancy'=>'','date'=>'','source'=>''],\App\Services\UnitOccupation::read($unit),$occupationOld['values'] ?? []);
$occupationErrors=$occupationOld['errors'] ?? [];
$occupationEndpoint=url('avaluos/'.$record['id'].'/bien-sujeto/unidades/'.$unit['id'].'/ocupacion');
?>
<form id="ocupacion-<?= e($unit['id']) ?>" class="mt-5 space-y-4 rounded-xl border p-5" method="post" action="<?= e($occupationEndpoint) ?>"
    data-module-autosave data-save-in-place data-autosave-endpoint="<?= e($occupationEndpoint) ?>"
    x-data="{issues:<?= e(json_encode((object)$occupationErrors,JSON_THROW_ON_ERROR)) ?>}" @ga:validation="issues=$event.detail || {}">
    <?= csrf_field() ?><input type="hidden" name="version" value="<?= (int)($occupationOld['version'] ?? $unit['occupation_version'] ?? 0) ?>">
    <h3 class="text-lg font-semibold">Ocupación de <?= e($unit['label']) ?></h3>
    <p class="text-sm">Situación de esta unidad, consultada desde Renta. La ocupación general del expediente no se asigna automáticamente a la oficina, garaje o depósito.</p>
    <?php if ($occupationOld): ?><p class="rounded-lg bg-amber-50 p-3" role="alert"><?= e($occupationOld['message']) ?></p><?php endif; ?>
    <label class="label">Ocupación actual
        <select class="input" name="occupation[occupancy]" aria-describedby="occupation-error-<?= e($unit['id']) ?>" :aria-invalid="Boolean(issues['occupation[occupancy]'])">
            <option value="">Por completar</option>
            <?php foreach (\App\Services\IncomeConcept::questions()['occupancy']['values'] as $value=>$label): ?>
            <option value="<?= e($value) ?>" <?= $occupation['occupancy']===$value?'selected':'' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label class="label">Fecha de la información
        <input class="input" name="occupation[date]" type="date" value="<?= e($occupation['date']) ?>" aria-describedby="occupation-error-<?= e($unit['id']) ?>" :aria-invalid="Boolean(issues['occupation[date]'])">
        <span class="text-xs font-normal">Fecha de visita o del soporte que describe la ocupación.</span>
    </label>
    <label class="label">Fuente y detalle de ocupación
        <textarea class="input" name="occupation[source]" maxlength="1000" rows="3" placeholder="Ej. Visita y contrato; identifica espacios arrendados, vacíos o de uso propio." aria-describedby="occupation-error-<?= e($unit['id']) ?>" :aria-invalid="Boolean(issues['occupation[source]'])"><?= e($occupation['source']) ?></textarea>
    </label>
    <p id="occupation-error-<?= e($unit['id']) ?>" class="text-sm text-red-700" role="alert" x-text="Object.values(issues).join(' ')"><?= e(implode(' ',$occupationErrors)) ?></p>
    <button class="btn-secondary" type="submit">Guardar ocupación / reintentar</button><p data-autosave-status role="status" class="text-sm">Sin cambios pendientes.</p>
</form>
