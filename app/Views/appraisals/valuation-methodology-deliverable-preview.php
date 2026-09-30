<?php
$deliverable82 = trim((string) ($methodologyChapterData['sections'][2][1] ?? ''));
?>
<section class="rounded-2xl border border-sky-100 bg-sky-50 p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Entregable</p>
            <h2 class="mt-2 text-2xl font-semibold text-sky-950">Texto consolidado del numeral 8</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-sky-900">
                Esta pestaña reúne la redacción larga que pasa al informe. Los numerales 8.1, 8.2 y 8.3 quedan
                para trabajo operativo; aquí se revisa el texto final sin llenar la pantalla de captura.
            </p>
        </div>
        <a class="btn-secondary min-h-11" href="<?= e(url('avaluos/' . $record['id'] . '/entregable')) ?>">
            Ver entregable general
        </a>
    </div>

    <div class="mt-6 grid gap-5 xl:grid-cols-[1.1fr_0.9fr]">
        <article class="rounded-xl border border-white/80 bg-white p-4">
            <p class="text-xs font-bold uppercase text-sky-700">8.1 Marco académico</p>
            <h3 class="mt-2 font-semibold text-slate-950">Texto base para informe</h3>
            <textarea class="input mt-3 min-h-80 bg-slate-50 font-mono text-sm leading-6" rows="18" readonly><?= e($methodologyText) ?></textarea>
        </article>
        <article class="rounded-xl border border-white/80 bg-white p-4">
            <p class="text-xs font-bold uppercase text-sky-700">8.2 Selección metodológica</p>
            <h3 class="mt-2 font-semibold text-slate-950">Texto de selección del método</h3>
            <p class="mt-3 whitespace-pre-wrap text-sm leading-6 text-slate-700"><?= e($deliverable82) ?></p>
        </article>
    </div>

    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <?php foreach ($methodologySections as $section): ?>
            <article class="rounded-xl border border-sky-100 bg-white p-4 text-sm leading-6">
                <h3 class="font-semibold text-slate-950"><?= e((string) ($section[0] ?? 'Sección')) ?></h3>
                <p class="mt-2 whitespace-pre-wrap text-slate-700"><?= e((string) ($section[1] ?? '')) ?></p>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if ($methodologyReferences !== []): ?>
        <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-5">
            <?php foreach ($methodologyReferences as $reference): ?>
                <article class="rounded-xl border border-sky-100 bg-white p-4 text-sm leading-5">
                    <p class="text-xs font-bold uppercase text-sky-700"><?= e((string) ($reference[0] ?? 'Referencia')) ?></p>
                    <h3 class="mt-2 font-semibold text-slate-950"><?= e((string) ($reference[1] ?? '')) ?></h3>
                    <p class="mt-2 text-xs leading-5 text-slate-600"><?= e((string) ($reference[2] ?? '')) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
