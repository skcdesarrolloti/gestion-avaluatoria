<?php $currentStep = 'restrictivas'; ?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Numeral 7 · Condiciones restrictivas</p>
        <h1 class="mt-2 text-3xl font-semibold">Condiciones restrictivas</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Módulo reservado para consolidar restricciones que puedan incidir en el avalúo. Queda separado
            del análisis jurídico, urbano y económico para evitar conclusiones mezcladas o prematuras.
        </p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-800">Numeral 7</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>

<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Físicas</p>
            <h2 class="mt-2 text-xl font-semibold">Condiciones del inmueble</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Espacio previsto para consolidar limitaciones observadas en visita o derivadas de la ficha técnica.
            </p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Jurídicas y urbanas</p>
            <h2 class="mt-2 text-xl font-semibold">Alertas documentadas</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Podrá tomar insumos del certificado, la norma urbana, afectaciones y soportes del expediente.
            </p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Valuatorias</p>
            <h2 class="mt-2 text-xl font-semibold">Incidencia pendiente</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                La incidencia sobre valor o comparabilidad se trabajará después, con soporte técnico verificable.
            </p>
        </div>
    </div>
</section>
