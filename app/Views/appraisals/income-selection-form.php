<form id="income-selection-form" x-ref="form" class="mt-6 rounded-xl border p-4" method="post" action="<?= e($selectionEndpoint) ?>"
    data-module-autosave data-save-in-place data-workflow-version data-autosave-endpoint="<?= e($selectionEndpoint) ?>"
    @ga:validation="issues=$event.detail">
    <?= csrf_field() ?>
    <input type="hidden" name="version" value="<?= e((string)($selectionFlash['old']['version'] ?? $record['methodology_version'] ?? 0)) ?>">
    <input type="hidden" name="samples_version" value="<?= e((string)($selectionFlash['old']['samples_version'] ?? $record['comparables_version'] ?? 0)) ?>">
    <input type="hidden" name="component" value="<?= e($componentKey) ?>"><input type="hidden" name="study_scope" value="renta:arriendo">
    <input type="hidden" name="operation" value="draft">
    <h3 class="font-semibold">Motivo y soporte de la decisión</h3>
    <?php if (!empty($selectionFlash['message'])): ?><p class="mt-2 text-red-800" role="alert"><?= e($selectionFlash['message']) ?></p><?php endif; ?>
    <label class="mt-3 block font-semibold" for="income-selection-sample">Muestra que vas a revisar</label>
    <select id="income-selection-sample" x-ref="sample" name="sample" :value="sample" @change="if(sample!==$event.target.value) choose($event.target.value)"
        class="input" :inert="busy || applied" :aria-invalid="issues.sample?'true':'false'" aria-describedby="income-sample-error">
        <option value="">Selecciona una muestra</option><?php foreach ($selectionRows as $sample): ?>
        <option value="<?= e($sample['id']) ?>" <?= $sample['id']===$selectionSample?'selected':'' ?>><?= e($sample['label']) ?></option><?php endforeach; ?>
    </select><p id="income-sample-error" class="text-sm text-red-800" x-text="issues.sample || ''"><?= e($selectionFlash['errors']['sample'] ?? '') ?></p>
    <label class="mt-3 block font-semibold" for="income-selection-category">Razón técnica</label>
    <select id="income-selection-category" name="category" class="input" x-model="category" :inert="busy || applied" :aria-invalid="issues.category?'true':'false'" aria-describedby="income-category-error">
        <option value="">Selecciona la razón</option><?php foreach (\App\Services\IncomeSampleSelection::REASONS as $code=>$label): ?>
        <option value="<?= e($code) ?>" <?= ($selectionDraft['category'] ?? '')===$code?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?>
    </select><p id="income-category-error" class="text-sm text-red-800" x-text="issues.category || ''"><?= e($selectionFlash['errors']['category'] ?? '') ?></p>
    <?php foreach (['reason'=>['Justificación concreta','Ej. Se ofrece por puestos con servicios incluidos; no corresponde al arriendo de una oficina convencional.'],
        'support'=>['Soporte de la revisión','Ej. URL y apartado del anuncio, contacto y fecha, o documento que sustenta la diferencia.']] as $field=>[$label,$placeholder]): ?>
    <label class="mt-3 block font-semibold" for="income-selection-<?= e($field) ?>"><?= e($label) ?></label>
    <textarea id="income-selection-<?= e($field) ?>" <?= $field==='reason'?'x-ref="reason"':'' ?> name="<?= e($field) ?>" x-model="<?= e($field) ?>" class="input" rows="3" maxlength="1600"
        placeholder="<?= e($placeholder) ?>" :inert="busy || applied" :aria-invalid="issues.<?= e($field) ?>?'true':'false'" aria-describedby="income-<?= e($field) ?>-error income-decision-help"><?= e($selectionDraft[$field] ?? '') ?></textarea>
    <p id="income-<?= e($field) ?>-error" class="text-sm text-red-800" x-text="issues.<?= e($field) ?> || ''"><?= e($selectionFlash['errors'][$field] ?? '') ?></p>
    <?php endforeach; ?>
    <p id="income-decision-help" class="mt-2 text-sm">Máximo 1.600 caracteres por campo. Motivo y soporte requeridos para aplicar. Escribirlos solo guarda un borrador; todavía no cambia la muestra.</p>
    <p class="mt-3 text-sm" data-autosave-status role="status" aria-live="polite">Sin cambios pendientes.</p>
    <div class="mt-3 flex flex-wrap gap-3">
        <button type="submit" class="btn-secondary" :disabled="busy || applied || !sample">Guardar motivo / reintentar</button>
        <button type="button" class="btn-primary" @click="decide('exclude')" :disabled="busy || applied || !sample || excluded[sample]">Excluir del análisis</button>
        <button type="button" class="btn-secondary" @click="decide('restore')" :disabled="busy || applied || !sample || !excluded[sample]">Reincorporar al análisis</button>
        <noscript><button type="submit" class="btn-primary" name="decision" value="exclude">Excluir del análisis</button><button type="submit" class="btn-secondary" name="decision" value="restore">Reincorporar al análisis</button></noscript>
    </div>
    <p class="mt-3" role="status" aria-live="polite" x-text="message"></p>
    <button type="button" class="btn-secondary mt-2" x-show="applied" x-cloak @click="refresh()">Actualizar resultados guardados</button>
</form>
