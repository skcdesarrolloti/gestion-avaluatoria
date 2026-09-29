<?php
$c = is_array($simpleChapter ?? null) ? $simpleChapter : [];
$data = is_array($c['data'] ?? null) ? $c['data'] : ['sections' => [], 'text' => ''];
$text = (string) ($data['text'] ?? '');
$sections = is_array($data['sections'] ?? null) ? $data['sections'] : [];
$theme = (string) ($c['theme'] ?? 'slate');
$tone = $theme === 'red'
    ? ['border-red-100 bg-red-50', 'text-red-950', 'text-red-800']
    : ['border-sky-100 bg-sky-50', 'text-sky-950', 'text-sky-900'];
?>
<section class="mt-8 rounded-2xl border <?= e($tone[0]) ?> p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow"><?= e((string) ($c['eyebrow'] ?? 'Capítulo')) ?></p>
            <h2 class="mt-2 text-2xl font-semibold <?= e($tone[1]) ?>"><?= e((string) ($c['title'] ?? 'Texto consolidado')) ?></h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 <?= e($tone[2]) ?>"><?= e((string) ($c['help'] ?? '')) ?></p>
        </div>
        <a class="rounded-full bg-white px-4 py-2 text-sm font-bold <?= e($tone[2]) ?>" href="<?= e((string) ($c['href'] ?? '#')) ?>"><?= e((string) ($c['link'] ?? 'Editar')) ?></a>
    </div>
    <textarea class="input mt-5 min-h-80 bg-white font-mono text-sm leading-6" rows="18" readonly><?= e($text) ?></textarea>
    <div class="mt-5 grid gap-4">
        <?php foreach ($sections as $section): ?>
            <article class="rounded-xl border border-white/70 bg-white/70 p-4 text-sm leading-6">
                <h3 class="font-semibold text-slate-950"><?= e((string) ($section[0] ?? 'Sección')) ?></h3>
                <p class="mt-2 whitespace-pre-wrap text-slate-700"><?= e((string) ($section[1] ?? '')) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
