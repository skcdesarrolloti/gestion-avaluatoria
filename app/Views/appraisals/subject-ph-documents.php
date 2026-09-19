<?php if (!empty($phDocuments)): ?>
    <ul class="mt-2 space-y-2 text-sm text-slate-700">
        <?php foreach (array_slice($phDocuments, 0, 8) as $doc): ?>
            <li class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-100 bg-slate-50 px-3 py-2">
                <span><?= e($doc['source_filename']) ?> · <?= e((string) $doc['extracted_chars']) ?> caracteres</span>
                <div class="flex flex-wrap gap-2">
                    <form method="post" action="<?= e(url($subjectActionBase . '/ph/soportes/' . $doc['id'] . '/cargar')) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="return_to" value="<?= e($subjectActionBase . '#ph') ?>">
                        <input type="hidden" name="ph_typology" value="<?= e((string) ($ph['ph_typology'] ?? '')) ?>">
                        <button class="btn-secondary min-h-9 px-3 py-1 text-xs" type="submit">Cargar</button>
                    </form>
                    <form method="post" action="<?= e(url($subjectActionBase . '/ph/soportes/' . $doc['id'] . '/eliminar')) ?>"
                        onsubmit="return confirm('¿Eliminar este soporte PH del avalúo? Los campos ya diligenciados se conservarán.');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="return_to" value="<?= e($subjectActionBase . '#ph') ?>">
                        <button class="btn-secondary min-h-9 px-3 py-1 text-xs text-red-700" type="submit">Eliminar</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p class="mt-2 text-sm text-slate-600">Sin soportes cargados.</p>
<?php endif; ?>
