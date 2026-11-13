<?php
$ivTitles=['costs'=>'Costo','events'=>'Evento mensual','rates'=>'Alternativa de tasa'];
$ivFields=$ivGroup==='rates'?\App\Services\IncomeValuationFields::rates():\App\Services\IncomeValuationFields::group($ivGroup);
$ivDynamic=true;unset($ivIndex);
?>
<div class="mt-4 space-y-3">
    <p class="text-sm" x-show="state.<?= e($ivGroup) ?>.length===0">Sin filas registradas. Agrega solo lo que puedas sustentar.</p>
    <template x-for="(row,index) in state.<?= e($ivGroup) ?>" :key="<?= $ivGroup==='rates'?'row.id':'index' ?>">
        <details class="rounded-xl border p-4" x-init="if (!row.name && !row.month) $el.open=true">
            <summary class="min-h-11 cursor-pointer font-semibold" x-text="row.name || row.month || '<?= e($ivTitles[$ivGroup]) ?> '+(index+1)"></summary>
            <div class="mt-3 grid gap-4 md:grid-cols-2"><?php foreach (array_keys($ivFields) as $ivKey) require __DIR__.'/income-valuation-field.php'; ?></div>
            <button type="button" class="btn-secondary mt-3" @click="removeRow('<?= e($ivGroup) ?>',index)">Retirar esta fila del borrador</button>
        </details>
    </template>
    <noscript><?php $ivDynamic=false;foreach ($ivState[$ivGroup] as $ivIndex=>$entry): ?>
        <div class="rounded-xl border p-4"><h4><?= e($ivTitles[$ivGroup]) ?> <?= $ivIndex+1 ?></h4><div class="grid gap-4 md:grid-cols-2">
        <?php foreach (array_keys($ivFields) as $ivKey) require __DIR__.'/income-valuation-field.php'; ?></div></div>
    <?php endforeach;unset($ivIndex); ?></noscript>
    <button type="button" class="btn-secondary" @click="addRow('<?= e($ivGroup) ?>')">Agregar <?= e(strtolower($ivTitles[$ivGroup])) ?></button>
    <noscript><button type="submit" class="btn-secondary" name="operation" value="add_<?= e($ivGroup) ?>">Agregar <?= e(strtolower($ivTitles[$ivGroup])) ?></button></noscript>
</div>
<?php $ivDynamic=false;unset($ivIndex); ?>
