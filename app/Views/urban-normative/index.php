<?php
$stats = $stats ?? ['documents' => 0, 'available' => 0, 'missing' => 0];
$storageReport = $storageReport ?? ['dir' => '', 'configured' => false, 'writable' => false,
    'present' => 0, 'total' => 0, 'marked_missing' => 0, 'limits' => []];
$typeLabels = ['pot' => 'POT', 'cuadro_uso' => 'Cuadros de uso', 'sistema_consulta' => 'MIDAS',
    'tramite' => 'Trámite', 'ley' => 'Ley', 'decreto' => 'Decreto', 'resolucion' => 'Resolución', 'determinante' => 'Determinantes'];
?>
<section class="space-y-7" x-data="{ query: '' }">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-teal-800">Academia urbana</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">Normatividad Urbana</h1>
            <p class="mt-3 max-w-3xl text-slate-600">Aquí quedan los documentos fuente de POT, MIDAS, Planeación y determinantes urbanísticas. El capítulo 5 solo los referencia y captura la lectura del caso.</p>
        </div>
        <label class="block min-w-full text-sm font-medium text-slate-700 lg:min-w-80">
            Buscar documento urbano
            <input class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-950 shadow-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                type="search" placeholder="POT, MIDAS, resolución, uso del suelo" x-model.trim="query">
        </label>
    </div>
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-950">Biblioteca de archivos urbanos</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Sube aquí los PDF base. Cuando diligencies el numeral 5 podrás escoger el documento, cuadro o categoría aplicable sin volver pesado el formulario.</p>
        <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600"><?= e($stats['documents']) ?> documento(s)</span>
            <span class="rounded-full bg-teal-50 px-3 py-1 text-teal-700"><?= e($stats['available']) ?> PDF cargado(s)</span>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700"><?= e($stats['missing']) ?> pendiente(s)</span>
        </div>
        <?php if (!empty($importNotice)): ?>
            <?php $ok = (bool) ($importNotice['ok'] ?? false); ?>
            <div class="mt-4 rounded-lg <?= $ok ? 'bg-teal-50 text-teal-900' : 'bg-red-50 text-red-800' ?> p-4 text-sm">
                <p><?= $ok ? count($importNotice['copied'] ?? []) . ' archivo(s) urbano(s) importado(s).' : e($importNotice['message'] ?? 'No se pudo importar.') ?></p>
            </div>
        <?php endif; ?>
        <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                <div><p class="font-semibold text-slate-950">Diagnóstico de guardado urbano</p>
                    <p class="mt-1 break-words text-slate-600">Ruta: <?= e($storageReport['dir']) ?></p></div>
                <span class="rounded-full <?= $storageReport['writable'] ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' ?> px-3 py-1 text-xs font-semibold">
                    <?= $storageReport['writable'] ? 'Carpeta escribible' : 'Sin permiso de escritura' ?>
                </span>
            </div>
            <dl class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Modo</dt><dd><?= $storageReport['configured'] ? 'URBAN_NORM_STORAGE_DIR' : 'storage/ del proyecto' ?></dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Archivos disponibles</dt><dd><?= e($storageReport['present']) ?> / <?= e($storageReport['total']) ?></dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">BD sin archivo</dt><dd><?= e($storageReport['marked_missing']) ?></dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Tamaño PHP</dt><dd><?= e($storageReport['limits']['upload_max_filesize'] ?? '') ?></dd></div>
            </dl>
        </div>
    </section>
    <section class="grid gap-4 md:grid-cols-2">
        <?php foreach ($documents as $doc): ?>
            <?php $term = mb_strtolower(implode(' ', [$doc['title'], $doc['document_type'], $doc['issuer'], $doc['jurisdiction'], $doc['normative_reference'], $doc['summary']])); ?>
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm" x-show='query === "" || <?= e(json_encode($term, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>.includes(query.toLowerCase())'>
                <div class="flex items-start justify-between gap-3">
                    <div><p class="text-sm font-semibold text-teal-800"><?= e($typeLabels[$doc['document_type']] ?? $doc['document_type']) ?></p>
                        <h2 class="mt-1 text-lg font-semibold text-slate-950"><?= e($doc['title']) ?></h2></div>
                    <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700"><?= e($doc['status']) ?></span>
                </div>
                <p class="mt-3 text-sm leading-6 text-slate-600"><?= e($doc['summary'] ?? '') ?></p>
                <dl class="mt-4 grid gap-2 text-sm text-slate-600">
                    <div><dt class="font-semibold text-slate-950">Fuente</dt><dd><?= e($doc['issuer']) ?> · <?= e($doc['jurisdiction']) ?></dd></div>
                    <div><dt class="font-semibold text-slate-950">Referencia</dt><dd><?= e($doc['normative_reference']) ?></dd></div>
                    <?php if (!empty($doc['source_url'])): ?><div><dt class="font-semibold text-slate-950">URL oficial</dt><dd><a class="text-teal-700 underline" href="<?= e($doc['source_url']) ?>" target="_blank" rel="noopener">Abrir fuente externa</a></dd></div><?php endif; ?>
                </dl>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                    <span class="text-xs font-semibold <?= $doc['has_file'] ? 'text-emerald-700' : 'text-amber-700' ?>"><?= $doc['has_file'] ? 'PDF disponible' : 'Pendiente de archivo' ?></span>
                    <?php if ($doc['has_file']): ?><a class="btn-secondary" target="_blank" rel="noopener" href="<?= e(url('normatividad-urbana/' . $doc['slug'] . '/archivo')) ?>">Abrir PDF</a><?php endif; ?>
                </div>
                <form class="mt-4 grid gap-2" method="post" action="<?= e(url('normatividad-urbana/' . $doc['slug'] . '/importar')) ?>" enctype="multipart/form-data" x-data="{ busy: false }" @submit="busy = true">
                    <?= csrf_field() ?>
                    <label class="block text-xs font-semibold uppercase text-slate-500">PDF del documento urbano
                        <input class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700" type="file" name="urban_norm_file[]" accept="application/pdf,.pdf" required>
                    </label>
                    <button class="btn-primary w-full" type="submit" :disabled="busy" x-text="busy ? 'Subiendo PDF…' : '<?= $doc['has_file'] ? 'Reemplazar PDF' : 'Subir PDF' ?>'"><?= $doc['has_file'] ? 'Reemplazar PDF' : 'Subir PDF' ?></button>
                </form>
            </article>
        <?php endforeach; ?>
    </section>
</section>
