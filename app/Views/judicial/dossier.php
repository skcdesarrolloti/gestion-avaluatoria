<?php $endpoint = url('avaluos/' . $record['id'] . '/judicial'); $frozen = !empty($stored['presented_on']); ?>
<section class="mx-auto max-w-5xl space-y-6">
    <a class="btn-secondary" href="<?= e(url('avaluos/' . $record['id'] . '/expediente')) ?>">Volver al expediente</a>
    <header><p class="eyebrow">Expediente · finalidad judicial</p><h1 class="text-2xl font-semibold">Anexo del perito · Código General del Proceso</h1><p><?= e($expert['full_name'] ?? 'Perito pendiente') ?></p></header>
    <?php require __DIR__ . '/academy.php'; ?>
    <?php if (!$eligible): ?><p class="rounded-xl bg-amber-50 p-4">Primero guarda la finalidad <strong>Judicial</strong> y asigna un perito responsable en el expediente. Después podrás preparar y exportar estos campos.</p><?php else: ?>
        <a class="btn-secondary" href="<?= e(url('maestros/peritos/' . $expert['id'] . '/judicial')) ?>">Abrir antecedentes del perito en Maestros</a>
        <?php if ($frozen): ?><p class="rounded-xl bg-teal-50 p-4">Presentación registrada el <?= e($stored['presented_on']) ?>. El texto exportable conserva la versión registrada y ya alimenta el historial del perito.</p><?php else: ?>
            <p class="rounded-xl bg-blue-50 p-4">1. Completa el maestro y estas declaraciones. 2. Guarda y actualiza la vista previa para exportar el anexo. 3. Después de presentarlo al juzgado, registra la presentación. Guardar o exportar no radica el dictamen.</p>
            <form id="judicial-dossier" method="post" action="<?= e($endpoint) ?>" data-module-autosave data-save-in-place data-autosave-endpoint="<?= e($endpoint) ?>" class="space-y-5 rounded-xl border bg-white p-5">
                <?= csrf_field() ?><input type="hidden" name="version" value="<?= e($stored['version']) ?>">
                <?php require __DIR__ . '/dossier-fields.php'; ?>
                <div class="flex flex-wrap items-center gap-3"><button type="submit" class="btn-primary">Guardar ahora</button><span role="status" data-autosave-status>Autoguardado activo</span></div>
            </form>
            <a class="btn-secondary" href="<?= e($endpoint) ?>">Guardar pendientes y actualizar vista previa</a>
        <?php endif; ?>
        <section class="space-y-3 rounded-xl border bg-white p-5"><h2 class="text-xl font-semibold">Vista previa guardada · exportación por campo</h2><p class="text-sm">El borrador debe revisarlo y firmarlo el perito. El texto no incorpora archivos adjuntos. Los cambios pendientes no aparecen hasta actualizar esta vista.</p>
            <?php if (($formData['directed'] ?? '') === 'si'): ?><a class="btn-primary" download href="<?= e($endpoint . '/exportar') ?>">Exportar anexo completo · TXT</a><?php else: ?><p>Marca y guarda «Dirigido a un juzgado» para habilitar la exportación.</p><?php endif; ?>
            <?php foreach ($sections as $number => [$label, $text]): ?><details class="rounded-lg border p-3"><summary class="min-h-11 cursor-pointer font-semibold"><?= e($number . '. ' . $label) ?></summary><p class="whitespace-pre-line text-sm leading-6"><?= e($text) ?></p><?php if (($formData['directed'] ?? '') === 'si'): ?><a class="btn-secondary mt-3" download href="<?= e($endpoint . '/exportar?campo=' . rawurlencode($number)) ?>">Exportar este campo · TXT</a><?php endif; ?></details><?php endforeach; ?>
        </section>
        <?php if (!$frozen): ?>
            <form class="space-y-4 rounded-xl border bg-white p-5" method="post" action="<?= e($endpoint . '/presentar') ?>">
                <?= csrf_field() ?><input type="hidden" name="version" value="<?= e($stored['version']) ?>"><input type="hidden" name="profile_version" value="<?= e($profile['version']) ?>">
                <h2 class="text-xl font-semibold">Registrar presentación efectiva</h2><p class="text-sm">Usa este botón después de actualizar y revisar la vista previa. Guarda una copia del anexo y añade este caso al historial; no envía documentos al juzgado. La copia registrada queda de solo lectura.</p>
                <label class="label">Fecha de presentación<input class="input" type="date" name="presented_on" required><span class="text-xs">Debe corresponder a la constancia de presentación y no puede ser futura.</span></label>
                <label class="flex min-h-11 items-center gap-3"><input type="checkbox" name="confirm_presented" value="si" required>Confirmo que el perito revisó y firmó el dictamen y que ya fue presentado al juzgado.</label>
                <button class="btn-primary" type="submit">Registrar presentación al juzgado</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</section>
