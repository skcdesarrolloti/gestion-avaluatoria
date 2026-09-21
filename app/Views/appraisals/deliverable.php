<?php $currentStep = 'entregable'; ?>
<?php
$ph = is_array($phProfile ?? null) ? $phProfile : [];
$phTechnical = is_array($ph['technical'] ?? null) ? $ph['technical'] : [];
$phSummaryKeys = [
    'resumen_base_ph' => 'Base PH común',
    'resumen_trazabilidad_ph' => 'Documento y trazabilidad',
    'resumen_identificacion_ph' => 'Identificación PH',
    'resumen_tipologia_ph' => 'Tipología y régimen',
    'resumen_configuracion_ph' => 'Configuración predial',
    'resumen_comunes_ph' => 'Bienes comunes y soporte',
    'resumen_reglas_ph' => 'Reglas de uso y operación',
    'resumen_administracion_ph' => 'Administración y cargas',
    'resumen_incidencia_ph' => 'Incidencia valuatoria',
    'resumen_notas_ph' => 'Notas normativas',
];
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Entregable</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Informe consolidado del avalúo</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Aquí se concentrará la redacción final del informe. Los numerales técnicos y jurídicos capturan datos,
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

<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <p class="eyebrow">Propiedad horizontal</p>
    <h2 class="mt-2 text-2xl font-semibold">Texto PH depurado para incorporar</h2>
    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
        Este bloque toma el resumen aprobado en el numeral 3.5. Sirve como base del cuerpo del informe,
        manteniendo los extractos OCR en la ficha PH como soporte de revisión.
    </p>
    <div class="mt-5 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-950">
        <?= nl2br(e((string) (($ph['report_text'] ?? '') ?: 'Aún no hay texto PH depurado. Revisa el numeral 3.5 y guarda la ficha.'))) ?>
    </div>
    <div class="mt-5 grid gap-3 lg:grid-cols-2">
        <?php foreach ($phSummaryKeys as $key => $label): ?>
            <?php $text = trim((string) ($phTechnical[$key] ?? '')); ?>
            <?php if ($text === '') continue; ?>
            <article class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6">
                <h3 class="font-semibold text-slate-900"><?= e($label) ?></h3>
                <p class="mt-2 text-slate-700"><?= e($text) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
