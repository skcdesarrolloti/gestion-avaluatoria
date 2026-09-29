<div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
    <p class="text-xs font-bold uppercase text-slate-500">Fórmulas a confirmar antes de automatizar</p>
    <div class="mt-3 grid gap-3 lg:grid-cols-4">
        <?php foreach ($formulaFamilies as $family): ?>
            <article class="rounded-lg border border-slate-200 bg-white p-3 text-sm leading-5">
                <h3 class="font-semibold text-slate-950"><?= e($family[0]) ?></h3>
                <p class="mt-2 text-slate-600"><?= e($family[1]) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
    <p class="mt-3 text-xs leading-5 text-slate-500">
        Estas fórmulas son el mapa de trabajo. La regla exacta, factores de ajuste y umbrales estadísticos
        deben aprobarse antes de que el sistema calcule valores de forma automática.
    </p>
</div>
