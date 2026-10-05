<td class="px-3 py-3" data-capture-scope="<?= e($scope) ?>">
    <?php if ($type === 'facts'): ?>
        <input type="hidden" name="<?= e($base . '[' . $field . ']') ?>" value="<?= e($row[$field] ?? '') ?>">
    <?php elseif ($type === 'choice'): ?>
        <?php $select($base . '[' . $field . ']', $row[$field] ?? '', \App\Services\ComparableCaptureDetail::options($field), 'min-w-52'); ?>
    <?php elseif ($type === 'calculated'): ?>
        <?php if (in_array($field, ['unit_area_label', 'unit_price_status'], true)): ?>
            <textarea class="input min-w-64" readonly rows="4" name="<?= e($base . '[' . $field . ']') ?>" aria-describedby="unit-price-help"><?= e($row[$field] ?? '') ?></textarea>
        <?php else: ?>
            <input class="input capture-calculated min-w-52" readonly name="<?= e($base . '[' . $field . ']') ?>" value="<?= e($row[$field] ?? '') ?>" placeholder="Pendiente de datos verificados" aria-describedby="unit-price-help negotiation-help">
        <?php endif; ?>
    <?php elseif (in_array($type, ['number', 'integer'], true)): ?>
        <input type="number" min="0" step="<?= $type === 'integer' ? '1' : '0.0001' ?>" <?= $type === 'integer' ? 'max="999"' : '' ?> class="input min-w-36" name="<?= e($base . '[' . $field . ']') ?>" value="<?= e($row[$field] ?? '') ?>" placeholder="<?= $field === 'negotiation_discount' ? 'Importe; 0 sólo si se confirma' : ($type === 'integer' ? 'Cantidad; vacío si no se informa' : 'm²; sin separadores de miles') ?>">
    <?php else: ?>
        <textarea class="input min-w-52" name="<?= e($base . '[' . $field . ']') ?>" maxlength="<?= $type === 'source_text' ? 16000 : 1600 ?>" rows="3" placeholder="<?= e($label) ?>; indica lo pendiente de verificar."><?= e($row[$field] ?? '') ?></textarea>
    <?php endif; ?>
</td>
