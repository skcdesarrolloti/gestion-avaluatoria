<td class="px-3 py-3" data-capture-scope="<?= e($scope) ?>">
    <?php if ($type === 'choice'): ?>
        <?php $select($base . '[' . $field . ']', $row[$field] ?? '', \App\Services\ComparableCaptureDetail::options($field), 'min-w-52'); ?>
    <?php elseif ($type === 'calculated'): ?>
        <input class="input capture-calculated min-w-52" readonly name="<?= e($base . '[' . $field . ']') ?>" value="<?= e($row[$field] ?? '') ?>" placeholder="Pendiente de oferta y descuento" aria-describedby="negotiation-help">
    <?php elseif (in_array($type, ['number', 'integer'], true)): ?>
        <input type="number" min="0" step="<?= $type === 'integer' ? '1' : '0.0001' ?>" <?= $type === 'integer' ? 'max="999"' : '' ?> class="input min-w-36" name="<?= e($base . '[' . $field . ']') ?>" value="<?= e($row[$field] ?? '') ?>" placeholder="<?= $field === 'negotiation_discount' ? 'Importe; 0 sólo si se confirma' : ($type === 'integer' ? 'Cantidad; vacío si no se informa' : 'm²; sin separadores de miles') ?>">
    <?php else: ?>
        <textarea class="input min-w-52" name="<?= e($base . '[' . $field . ']') ?>" maxlength="1600" rows="3" placeholder="<?= e($label) ?>; indica lo pendiente de verificar."><?= e($row[$field] ?? '') ?></textarea>
    <?php endif; ?>
</td>
