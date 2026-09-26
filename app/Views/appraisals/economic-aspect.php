<?php $currentStep = 'economico'; ?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Numeral 6 · Aspecto económico</p>
        <h1 class="mt-2 text-3xl font-semibold">Aspecto económico</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Módulo reservado para documentar el análisis económico del avalúo. Se deja creado para mantener
            la estructura del informe y trabajarlo después sin mezclarlo con el bien sujeto ni con comparables.
        </p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-800">Numeral 6</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>

<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Alcance</p>
            <h2 class="mt-2 text-xl font-semibold">Lectura económica</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Aquí se organizarán los elementos de mercado, dinámica económica y soportes que afecten el valor.
            </p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Insumos futuros</p>
            <h2 class="mt-2 text-xl font-semibold">Mercado y entorno</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                El desarrollo podrá apoyarse en el sector, la normatividad y las muestras comparables cuando existan.
            </p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Control</p>
            <h2 class="mt-2 text-xl font-semibold">Sin cálculo aún</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Esta pantalla no genera valores ni conclusiones; solo reserva el capítulo para desarrollo posterior.
            </p>
        </div>
    </div>
</section>
