<?php if (($unit['unit_kind'] ?? '') === 'annex'):
    $annexType = (string) ($unit['construction_type'] ?? '');
    $annexTypeLabel = \App\Support\AppraisalConstructionTypeCatalog::types()[$annexType] ?? '';
    $annexTypeLink = url('avaluos/' . $record['id'] . '/bien-sujeto?' . http_build_query([
        'unit'=>$unit['id'], 'detail'=>'basicos', 'from'=>'metodologia',
        'check_component'=>$unit['id']]) . '#construccion');
?>
<div class="label">
    <p>Tipo del anexo registrado en 3.3</p>
    <p class="input bg-slate-50 font-semibold"><?= e($annexType !== '' ? $annexTypeLabel : 'Pendiente de clasificar') ?></p>
    <input type="hidden" name="units[<?= e($unit['id']) ?>][property_type]" value="<?= e((string) ($unit['property_type'] ?? '')) ?>">
    <p class="text-xs font-normal text-slate-600">Depósito / cuarto útil y garaje / celda de parqueo se clasifican por separado. Este dato se consulta de 3.3 y se refleja en el capítulo 8.</p>
    <a class="inline-flex min-h-11 items-center font-semibold text-teal-800 underline" href="<?= e($annexTypeLink) ?>"><?= $annexType !== '' ? 'Consultar o cambiar tipo en 3.3' : 'Diligenciar tipo del anexo en 3.3' ?></a>
</div>
<?php else: ?>
<label class="label">Tipo de inmueble de esta unidad
    <select class="input" name="units[<?= e($unit['id']) ?>][property_type]">
        <option value="">Usar tipo general del avalúo</option>
        <?php foreach (($catalog['selects']['tipo_inmueble'][4] ?? []) as $value => $text): ?>
        <option value="<?= e($value) ?>" <?= (string) ($unit['property_type'] ?? '') === (string) $value ? 'selected' : '' ?>><?= e($text) ?></option>
        <?php endforeach; ?>
    </select>
    <span class="mt-1 block text-xs leading-5 text-slate-500">Activa los atributos especiales propios de esta unidad en 3.4.</span>
</label>
<?php endif; ?>
