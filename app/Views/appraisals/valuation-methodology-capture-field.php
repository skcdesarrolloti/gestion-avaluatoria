<td class="px-3 py-3">
    <?php if ($type === 'choice'): ?>
        <?php $select($base . '[' . $field . ']', $row[$field] ?? '', \App\Services\ComparablePhCapture::options($field), 'min-w-52'); ?>
    <?php elseif (in_array($type, ['number', 'integer'], true)): ?>
        <input type="number" min="0" step="<?= $type === 'integer' ? '1' : '0.0001' ?>" <?= $type === 'integer' ? 'max="999"' : '' ?> class="input min-w-36" name="<?= e($base . '[' . $field . ']') ?>" value="<?= e($row[$field] ?? '') ?>" placeholder="<?= $type === 'integer' ? 'Cantidad; vacío si no se informa' : 'm²; sin separadores de miles' ?>">
    <?php else: ?>
        <textarea class="input min-w-52" name="<?= e($base . '[' . $field . ']') ?>" maxlength="1600" rows="3" placeholder="<?= e($label) ?>; indica lo pendiente de verificar."><?= e($row[$field] ?? '') ?></textarea>
    <?php endif; ?>
</td>
