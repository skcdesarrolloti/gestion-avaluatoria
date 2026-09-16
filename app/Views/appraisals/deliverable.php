<?php $currentStep = 'entregable'; ?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Entregable</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Informe consolidado del avalúo</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Aquí se concentrará la redacción final del informe. Los numerales técnicos solo capturan datos,
            ayudas, evidencias y criterios; la narrativa del entregable se prepara en este módulo.
        </p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">Único entregable</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>

<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <p class="eyebrow">Cuerpo del informe</p>
    <h2 class="mt-2 text-2xl font-semibold">Redacción centralizada</h2>
    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
        Este módulo reemplaza los borradores parciales que estaban en Expediente, Superficie, Construcción
        y Atributos. La siguiente fase será cargar la literatura del Word y mapear cada texto con sus fuentes,
        tooltips, fotos y datos técnicos.
    </p>
    <div class="mt-6 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm leading-6 text-slate-600">
        Pendiente de diseño funcional: capítulos del informe, textos editables, inserción de fotografías,
        referencias normativas y control de coherencia con NTS.
    </div>
</section>
