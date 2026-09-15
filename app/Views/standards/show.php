<?php
$bytes = static function (?int $value): string {
    if ($value === null) {
        return 'No importado';
    }
    return $value >= 1048576 ? round($value / 1048576, 1) . ' MB' : round($value / 1024, 1) . ' KB';
};
?>
<section class="space-y-8">
    <nav class="text-sm text-slate-500">
        <a class="font-medium text-teal-800 hover:text-teal-950" href="<?= e(url('normas-tecnicas-sectoriales')) ?>">Normas Técnicas Sectoriales</a>
        <span aria-hidden="true">/</span>
        <span><?= e($standard['standard_code']) ?></span>
    </nav>
    <div class="grid gap-8 lg:grid-cols-[1fr_18rem]">
        <article>
            <p class="text-sm font-semibold uppercase tracking-wide text-teal-800"><?= e($standard['category_code']) ?> · <?= e($standard['category_name']) ?></p>
            <h1 class="mt-3 text-3xl font-semibold text-slate-950"><?= e($standard['title']) ?></h1>
            <dl class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Código</dt>
                    <dd class="mt-1 text-slate-950"><?= e($standard['standard_code']) ?></dd>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tipo</dt>
                    <dd class="mt-1 text-slate-950"><?= e($standard['kind']) ?></dd>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Archivo fuente</dt>
                    <dd class="mt-1 break-words text-slate-950"><?= e($standard['source_filename']) ?></dd>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tamaño</dt>
                    <dd class="mt-1 text-slate-950"><?= e($bytes($standard['file_size_bytes'])) ?></dd>
                </div>
            </dl>
        </article>
        <aside class="self-start rounded-lg border border-slate-200 bg-white p-5">
            <p class="text-sm font-semibold text-slate-950">Documento PDF</p>
            <p class="mt-2 text-sm text-slate-600"><?= $standard['has_file'] ? 'Disponible en almacenamiento privado.' : 'Pendiente de importar desde la carpeta de normas.' ?></p>
            <?php if ($standard['has_file']): ?>
                <a class="btn-primary mt-5 w-full justify-center" target="_blank" rel="noopener" href="<?= e(url('normas-tecnicas-sectoriales/' . $standard['slug'] . '/archivo')) ?>">Abrir PDF</a>
            <?php endif; ?>
        </aside>
    </div>
</section>
