<div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
    <p class="text-xs font-bold uppercase text-slate-500">Preparación estadística para M4</p>
    <div class="mt-3 grid gap-3 lg:grid-cols-4">
        <?php foreach ($formulaFamilies as $family): ?>
            <article class="rounded-lg border border-slate-200 bg-white p-3 text-sm leading-5">
                <h3 class="font-semibold text-slate-950"><?= e($family[0]) ?></h3>
                <p class="mt-2 text-slate-600"><?= e($family[1]) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
    <p class="mt-3 text-xs leading-5 text-slate-500">
        Estas fórmulas son el mapa de trabajo. La muestra debe llegar depurada desde M3; en M4 se revisan
        tendencia central robusta, dispersión, intervalo con t de Student, MAPE cuando exista modelo y salvedades.
    </p>
</div>
