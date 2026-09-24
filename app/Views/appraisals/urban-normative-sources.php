<section x-show="tab === 'fuentes'" class="rounded-2xl border border-indigo-100 bg-indigo-50 p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Academia normativa aplicada al numeral 5</p>
            <h2 class="mt-2 text-2xl font-semibold text-indigo-950">Fuentes que debes revisar antes de concluir</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-indigo-900">MIDAS trae la lectura práctica por predio; la conclusión se soporta con POT, cuadros de usos, conceptos, determinantes y normas urbanísticas pertinentes.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a class="btn-secondary" target="_blank" rel="noopener" href="<?= e(url('normatividad-urbana')) ?>">Abrir biblioteca de archivos</a>
            <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-indigo-800">MIDAS + POT + Planeación</span>
        </div>
    </div>
    <div class="mt-5 grid gap-4 md:grid-cols-2">
        <?php foreach ($academyBlocks as [$title, $rule, $use]): ?>
            <article class="rounded-xl border border-indigo-100 bg-white p-4">
                <h3 class="font-semibold text-indigo-950"><?= e($title) ?></h3>
                <p class="mt-2 text-sm leading-6 text-slate-700"><?= e($rule) ?></p>
                <p class="mt-2 text-xs font-semibold leading-5 text-teal-800"><?= e($use) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
