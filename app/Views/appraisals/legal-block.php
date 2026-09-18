<?php
$blockScope = (string) ($blockScope ?? 'general');
$blockApprovalKey = \App\Support\AppraisalLegalCatalog::blockApprovalKey($blockScope, $blockTitle);
?>
<div class="rounded-2xl border border-slate-200 bg-white p-4">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h4 class="text-sm font-bold text-blue-950"><?= e($blockTitle) ?></h4>
        <label class="inline-flex items-center gap-2 text-xs text-slate-500">
            <input class="h-4 w-4 rounded border-slate-300" type="checkbox"
                name="<?= e($blockApprovalKey) ?>" value="Sí" <?= $field($blockApprovalKey) === 'Sí' ? 'checked' : '' ?>>
            <span>Bloque validado</span>
        </label>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <?php foreach ($blockFields as $key): ?>
            <?php $approvalKey = \App\Support\AppraisalLegalCatalog::fieldApprovalKey($key); ?>
            <?php $wide = in_array($key, ['cabida_linderos', 'matriculas_derivadas', 'reformas_ph'], true)
                || str_starts_with($key, 'reporte_') || str_starts_with($key, 'revision_'); ?>
            <div class="<?= $wide ? 'md:col-span-2' : '' ?>">
                <div class="mb-2 flex items-center justify-between gap-3">
                    <label class="text-sm font-semibold text-slate-800" for="legal-<?= e($key) ?>"><?= e($label($key)) ?></label>
                    <label class="inline-flex items-center gap-2 text-xs text-slate-500">
                        <input class="h-4 w-4 rounded border-slate-300" type="checkbox"
                            name="<?= e($approvalKey) ?>" value="Sí" <?= $field($approvalKey) === 'Sí' ? 'checked' : '' ?>>
                        <span>Aprobado</span>
                    </label>
                </div>
                <?php if (isset($selectOptions[$key])): ?>
                    <select class="input" id="legal-<?= e($key) ?>" name="<?= e($key) ?>">
                        <?php foreach ($selectOptions[$key] as $optionValue => $text): ?>
                            <option value="<?= e($optionValue) ?>" <?= $field($key) === $optionValue ? 'selected' : '' ?>><?= e($text) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif (in_array($key, $checkboxFields, true)): ?>
                    <label class="flex min-h-12 items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold">
                        <input class="h-5 w-5 rounded border-slate-300 text-teal-700" type="checkbox"
                            name="<?= e($key) ?>" value="Sí" <?= $field($key) === 'Sí' ? 'checked' : '' ?>>
                        <?= e($label($key)) ?>
                    </label>
                <?php elseif ($wide): ?>
                    <textarea class="input min-h-28" id="legal-<?= e($key) ?>" name="<?= e($key) ?>"
                        placeholder="Pendiente de lectura o revisión manual"><?= e($field($key)) ?></textarea>
                <?php else: ?>
                    <input class="input <?= $field($key) === '' ? 'bg-slate-50' : '' ?>" id="legal-<?= e($key) ?>"
                        name="<?= e($key) ?>" value="<?= e($field($key)) ?>" placeholder="Pendiente de lectura o revisión manual">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
