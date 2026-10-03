<?php
use App\Services\CostMethodScope;
$costScope=$selected['cost_scope'] ?? [];
?>
<fieldset class="mt-5 rounded-xl border border-teal-200 bg-teal-50 p-5" x-show="chosenMethod === 'costo'" :disabled="chosenMethod !== 'costo'" <?= ($selected['method'] ?? '')==='costo'?'':'x-cloak' ?>>
    <legend class="px-2 font-semibold">C2 · Alcance específico del costo</legend>
    <p class="text-sm">Construcción existente y presupuesto de retiro tienen tratamientos distintos. Registra su alcance antes de capturar precios; no se asignan porcentajes ni costos por defecto.</p>
    <div class="mt-4 grid gap-4 md:grid-cols-3">
        <?php foreach (CostMethodScope::options() as $name=>$option): ?>
        <label class="block font-semibold"><?= e($option['label']) ?>
            <select class="input" aria-label="<?= e($option['label']) ?>" name="cost_scope[<?= e($name) ?>]" <?= ($selected['method'] ?? '')==='costo'?'':'disabled' ?> :disabled="chosenMethod !== 'costo'">
                <option value="">Selecciona una opción</option>
                <?php foreach ($option['values'] as $value=>$label): ?><option value="<?= e($value) ?>" <?= ($costScope[$name] ?? '')===$value?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?>
            </select>
        </label>
        <?php endforeach; ?>
    </div>
    <p class="mt-4 rounded-lg bg-white p-3 text-sm"><strong>Depreciación: Ross–Heideck continuo.</strong> El presupuesto de retiro no es un valor depreciado de la construcción. Revisa excepciones y doble conteo antes de C4.</p>
    <div class="mt-4 grid gap-4 md:grid-cols-2">
        <?php foreach (CostMethodScope::texts() as $name=>[$label,$help]): ?>
        <label class="block font-semibold"><?= e($label) ?>
            <textarea class="input" aria-label="<?= e($label) ?>" rows="3" maxlength="2000" name="cost_scope[<?= e($name) ?>]" <?= ($selected['method'] ?? '')==='costo'?'':'disabled' ?> :disabled="chosenMethod !== 'costo'" placeholder="<?= e($help) ?>"><?= e($costScope[$name] ?? '') ?></textarea>
            <span class="mt-1 block text-xs font-normal leading-5 text-slate-600"><?= e($help) ?></span>
        </label>
        <?php endforeach; ?>
    </div>
</fieldset>
