<article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
    <h3 class="font-semibold text-slate-950"><?= e($label) ?></h3>
    <?php if (is_array($doc)): ?>
        <?php $fileUrl = url('midas/documentos/' . $doc['id'] . '/archivo'); ?>
        <p class="mt-2 text-sm leading-6 text-slate-600"><?= e((string) ($doc['title'] ?? 'Documento MIDAS')) ?></p>
        <?php if (($doc['mime_type'] ?? '') === 'application/pdf'): ?>
            <object class="mt-3 h-80 w-full rounded-lg border border-slate-200 bg-white" data="<?= e($fileUrl) ?>" type="application/pdf">
                <a class="font-semibold text-blue-800 underline" href="<?= e($fileUrl) ?>" data-no-fetch>Abrir mapa MIDAS</a>
            </object>
        <?php else: ?>
            <p class="mt-3 rounded-lg bg-white p-3 text-sm text-slate-600">El soporte no es PDF; ábrelo para revisarlo.</p>
        <?php endif; ?>
        <a class="btn-secondary mt-3 inline-flex bg-white" href="<?= e($fileUrl) ?>" data-no-fetch>Abrir mapa</a>
    <?php else: ?>
        <p class="mt-3 rounded-lg bg-amber-50 p-3 text-sm leading-6 text-amber-900">
            Pendiente cargar o clasificar este mapa en Biblioteca MIDAS, grupo Localidades.
        </p>
    <?php endif; ?>
</article>
