<?php
$marketUnits = array_values(array_filter($units, static fn ($row) => ($row['unit_kind'] ?? '') !== 'common'));
$marketSelectedId = \App\Services\SubjectUnitNavigation::selected($marketUnits);
?>
<section class="rounded-xl border border-teal-200 bg-teal-50 p-4" x-init="$nextTick(() => { if (<?= isset($_GET['market_check']) && $_GET['market_check'] !== 'description' && ($_GET['section'] ?? '') === 'tipologias' ? 'true' : 'false' ?>) $el.scrollIntoView() })" x-data="{ marketUnit: <?= e(json_encode($marketSelectedId)) ?> }">
    <h3 class="text-lg font-semibold">Datos y soportes de cada unidad para Mercado</h3>
    <p class="mt-2 text-sm">Estos campos alimentan la verificación del capítulo 8. Guarda aquí el dato y su soporte; la lista se actualiza al consultarla de nuevo.</p>
    <?php require __DIR__ . '/methodology-return.php'; ?>
    <nav class="mt-3 flex gap-2 overflow-x-auto" aria-label="Soporte de Mercado por unidad">
        <?php foreach ($marketUnits as $marketUnit): ?>
        <button class="btn-secondary shrink-0" type="button" @click="marketUnit = <?= e(json_encode($marketUnit['id'])) ?>"
            :aria-pressed="marketUnit === <?= e(json_encode($marketUnit['id'])) ?>"><?= e($marketUnit['label']) ?></button>
        <?php endforeach; ?>
    </nav>
    <?php foreach ($marketUnits as $marketUnit): $marketData = \App\Services\MarketSubjectEvidence::decode($marketUnit); ?>
    <form class="mt-4 grid min-w-0 gap-4 md:grid-cols-2" method="post"
        x-show="marketUnit === <?= e(json_encode($marketUnit['id'])) ?>" x-cloak
        action="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto/unidades/' . $marketUnit['id'] . '/mercado')) ?>"
        data-module-autosave data-save-in-place
        data-autosave-endpoint="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto/unidades/' . $marketUnit['id'] . '/mercado')) ?>"
        data-autosave-topic="<?= e('appraisal:' . $record['id'] . ':market-evidence:' . $marketUnit['id']) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="version" value="<?= e((string) ($marketUnit['market_evidence_version'] ?? 0)) ?>">
        <label class="label">Origen de la identificación de esta unidad
            <select class="input" name="market_evidence[identity_scope]">
                <?php foreach ([''=>'Selecciona el origen', 'propia'=>'Datos propios de esta unidad', 'sujeto'=>'Vincular expresamente con ficha del sujeto 3.1 y PH 3.5'] as $value=>$label): ?>
                <option value="<?= e($value) ?>" <?= ($marketData['identity_scope'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="text-xs font-normal">Al vincular, se consultan matrícula, uso observado y datos PH si los campos propios están vacíos. Confirma que pertenecen a esta unidad.</span>
        </label>
        <label class="label">Naturaleza jurídica de esta unidad
            <select class="input" name="market_evidence[legal_nature]">
                <?php foreach ([''=>'Selecciona según soporte', 'privada'=>'Unidad privada con matrícula independiente', 'integrada'=>'Parte integrada de otra unidad privada', 'comun_exclusivo'=>'Bien común de uso exclusivo'] as $value=>$label): ?>
                <option value="<?= e($value) ?>" <?= ($marketData['legal_nature'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label">Resultado del contraste de usos cuando los textos difieren
            <select class="input" name="market_evidence[use_contrast]">
                <?php foreach ([''=>'Por definir', 'compatible'=>'Compatible según soporte', 'condicionado'=>'Condicionado / restringido', 'incompatible'=>'Incompatible', 'pendiente'=>'Pendiente de verificar'] as $value=>$label): ?>
                <option value="<?= e($value) ?>" <?= ($marketData['use_contrast'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <?php foreach (\App\Services\MarketSubjectEvidence::fields() as $marketField => [$marketLabel, $marketPlaceholder]): ?>
        <label class="label"><?= e($marketLabel) ?>
            <textarea class="input" name="market_evidence[<?= e($marketField) ?>]" maxlength="1200" rows="2"
                placeholder="<?= e($marketPlaceholder) ?>"><?= e((string) ($marketData[$marketField] ?? '')) ?></textarea>
        </label>
        <?php endforeach; ?>
        <p class="text-sm md:col-span-2">Área privada y su soporte: <a class="font-semibold text-teal-800 underline" href="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto?unit=' . $marketUnit['id'] . '&detail=superficie&from=metodologia&check_component=' . $marketUnit['id'] . '#superficies')) ?>">diligenciar en 3.2 para <?= e($marketUnit['label']) ?></a>. Estado y conservación se consultan de 3.3.</p>
        <div class="flex flex-wrap items-center justify-between gap-3 md:col-span-2">
            <p class="text-xs font-semibold" data-autosave-status>Autoguardado activo</p>
            <button class="btn-primary" type="submit">Guardar soporte de <?= e($marketUnit['label']) ?></button>
        </div>
    </form>
    <?php endforeach; ?>
</section>
