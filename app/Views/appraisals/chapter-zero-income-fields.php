<?php
$incomeOptions = ['' => 'Selecciona una opción', 'si' => 'Sí', 'no' => 'No', 'pendiente' => 'Pendiente por verificar'];
$periodOptions = ['' => 'Selecciona periodicidad', 'mensual' => 'Mensual', 'trimestral' => 'Trimestral', 'anual' => 'Anual', 'otro' => 'Otro'];
$vatOptions = ['' => 'Selecciona una opción', 'si' => 'Sí', 'no' => 'No', 'no_aplica' => 'No aplica', 'pendiente' => 'Pendiente por verificar'];
$moneyValue = static function (string $name) use ($field): string {
    if ($field($name) === '') return '';
    $value = (float) $field($name);
    return '$ ' . number_format($value, fmod($value, 1.0) !== 0.0 ? 2 : 0, ',', '.');
};
?>
<div class="md:col-span-2 rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
    <h3 class="text-sm font-bold text-emerald-950">Contexto de renta del inmueble o activo</h3>
    <p class="mt-1 text-xs leading-5 text-emerald-900">
        Estos datos no cambian por sí solos el método. Sirven para saber si la renta documentada debe considerarse
        como antecedente económico, soporte de consistencia o insumo principal cuando el encargo sea de renta.
    </p>
    <div class="mt-4 grid gap-4 md:grid-cols-2">
        <label class="label">¿El inmueble o activo produce renta actualmente?
            <select class="input" name="income_producing">
                <?php foreach ($incomeOptions as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $selected('income_producing', $value) ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label">Canon o ingreso periódico informado
            <input class="input" name="rent_amount" inputmode="decimal" maxlength="20" data-money-input
                value="<?= e($moneyValue('rent_amount')) ?>" placeholder="Ej. 3.500.000">
        </label>
        <label class="label">Periodicidad del canon
            <select class="input" name="rent_period">
                <?php foreach ($periodOptions as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $selected('rent_period', $value) ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label">Administración periódica si aplica PH
            <input class="input" name="ph_admin_fee_amount" inputmode="decimal" maxlength="20" data-money-input
                value="<?= e($moneyValue('ph_admin_fee_amount')) ?>" placeholder="Ej. 850.000">
        </label>
        <label class="label">¿El canon causa o cobra IVA?
            <select class="input" name="rent_charges_vat">
                <?php foreach ($vatOptions as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $selected('rent_charges_vat', $value) ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="mt-1 block text-xs leading-5 text-slate-500">
                Útil en inmuebles comerciales, oficinas, locales, bodegas o activos con explotación económica.
            </span>
        </label>
        <label class="label">Observaciones sobre renta, administración o IVA
            <textarea class="input" name="income_notes" rows="3" maxlength="1500"
                placeholder="Ej. canon informado por el propietario; administración incluida o no incluida; IVA pendiente de confirmar."><?= e($field('income_notes')) ?></textarea>
        </label>
    </div>
</div>
