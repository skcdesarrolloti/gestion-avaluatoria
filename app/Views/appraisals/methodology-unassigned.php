<?php
$assignmentPools = ['' => \App\Services\MethodologyComparableScope::rows($allComparableRows, '')];
foreach ($allComparableRows as $sample) {
    $savedKey = $sample['component_key'] ?? '';
    if ($savedKey !== '' && !isset($components[$savedKey])) $assignmentPools[$savedKey][] = $sample;
}
if ($componentKey !== '') $assignmentPools[$componentKey] = $comparableRows;
?>
<?php foreach ($assignmentPools as $sourceScope => $bank): if ($bank === []) continue; ?>
<details class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4" <?= $componentKey === '' ? 'open' : '' ?>>
    <summary class="min-h-11 cursor-pointer font-semibold"><?= $sourceScope === '' ? count($bank) . ' muestras conservadas sin asignación' : (isset($components[$sourceScope]) ? 'Reasignar muestras de este componente' : count($bank) . ' muestras con asignación anterior: vincular a una unidad registrada') ?></summary>
    <p class="mt-2 text-sm">Marca las muestras que corresponden al componente. Conservan sus datos y fotos; esta acción no las duplica ni las incorpora automáticamente al análisis.</p>
    <form method="post" action="<?= e(url($basePath . '/asignar-muestras')) ?>" class="mt-4">
        <?= csrf_field() ?>
        <input type="hidden" name="source_scope" value="<?= e($sourceScope) ?>">
        <input type="hidden" name="version" value="<?= (int) ($record['comparables_version'] ?? 0) ?>">
        <label class="block font-semibold">Componente destino
            <select name="component" class="input"><option value="">Banco sin asignar</option>
            <?php foreach ($components as $key => $component): if ($key === $sourceScope) continue; ?>
                <option value="<?= e($key) ?>" <?= $key === $componentKey ? 'selected' : '' ?>><?= e($component['label']) ?></option>
            <?php endforeach; ?>
            </select>
        </label>
        <div class="mt-3 max-h-72 overflow-y-auto rounded-lg border bg-white p-3">
        <?php foreach ($bank as $sample): ?>
            <label class="flex min-h-11 items-center gap-3 border-b py-2 text-sm">
                <input type="checkbox" name="samples[]" value="<?= e($sample['id']) ?>">
                <span>#<?= (int) $sample['sample_index'] ?> · <?= e($sample['source_name']) ?> · <?= e($sample['neighborhood']) ?> · $<?= e(number_format((float) $sample['price_amount'], 0, ',', '.')) ?> · <?= e($sample['area_m2'] ?? '') ?> m²</span>
            </label>
        <?php endforeach; ?>
        </div>
        <button type="submit" class="btn-primary mt-3">Asignar las muestras marcadas</button>
    </form>
</details>
<?php endforeach; ?>
<p class="mb-4 rounded-xl bg-teal-50 p-4 text-sm">Las capturas de esta pantalla se guardan en: <strong><?= e($componentLabel) ?></strong>. Cambiar de etapa espera la confirmación del guardado.</p>
