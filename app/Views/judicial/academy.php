<details class="rounded-xl border border-teal-200 bg-teal-50 p-4">
    <summary class="min-h-11 cursor-pointer font-semibold">Academia · Código General del Proceso, dictamen pericial</summary>
    <p class="my-3 text-sm">Ley 1564 de 2012. Consulta los requisitos aplicables al dictamen; las declaraciones las verifica y suscribe el perito.</p>
    <?php foreach (App\Support\JudicialExpertAcademy::articles() as $number => $article): ?>
        <details class="my-2 rounded-lg border border-slate-200 bg-white p-3">
            <summary class="min-h-11 cursor-pointer font-semibold">Artículo <?= e($article[0]) ?></summary>
            <blockquote class="whitespace-pre-line text-sm leading-6"><?= e($article[1]) ?></blockquote>
            <p class="mt-3 text-sm text-teal-900"><strong>Aplicación en el formulario:</strong> <?= e($article[2]) ?></p>
        </details>
    <?php endforeach; ?>
    <?php foreach (App\Support\JudicialExpertAcademy::related() as $label => $text): ?>
        <details class="my-2 rounded-lg border border-slate-200 bg-white p-3"><summary class="min-h-11 cursor-pointer font-semibold"><?= e(explode(':', $text, 2)[0]) ?></summary><p class="whitespace-pre-line text-sm leading-6"><?= e($text) ?></p></details>
    <?php endforeach; ?>
    <a class="btn-secondary" href="<?= e(App\Support\JudicialExpertAcademy::SOURCE) ?>" target="_blank" rel="noopener">Leer la norma en la fuente oficial</a>
</details>
