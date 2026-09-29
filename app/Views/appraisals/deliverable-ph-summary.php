<?php
$phSummaryKeys = [
    'resumen_base_ph' => 'Base PH común', 'resumen_trazabilidad_ph' => 'Documento y trazabilidad',
    'resumen_identificacion_ph' => 'Identificación PH', 'resumen_tipologia_ph' => 'Tipología y régimen',
    'resumen_configuracion_ph' => 'Configuración predial', 'resumen_comunes_ph' => 'Bienes comunes y soporte',
    'resumen_reglas_ph' => 'Reglas de uso y operación', 'resumen_administracion_ph' => 'Administración y cargas',
    'resumen_incidencia_ph' => 'Incidencia valuatoria', 'resumen_notas_ph' => 'Notas normativas',
];
?>
<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <p class="eyebrow">Propiedad horizontal</p>
    <h2 class="mt-2 text-2xl font-semibold">Soportes PH que quedan en ficha</h2>
    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
        El texto PH ya está integrado en el capítulo 3 cuando existe. Estos bloques conservan la trazabilidad interna del numeral 3.5.
    </p>
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
