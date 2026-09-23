<?php
$phTechnicalPreview = is_array($ph['technical'] ?? null) ? $ph['technical'] : [];
$phReportText = trim((string) ($ph['report_text'] ?? ''));
$phPreviewRows = [
    'resumen_base_ph' => 'Base PH común',
    'resumen_trazabilidad_ph' => 'Documento y trazabilidad',
    'resumen_identificacion_ph' => 'Identificación PH',
    'resumen_tipologia_ph' => 'Tipología y régimen',
    'resumen_configuracion_ph' => 'Configuración predial',
    'resumen_comunes_ph' => 'Bienes comunes y soporte',
    'resumen_reglas_ph' => 'Reglas de uso y operación',
    'resumen_administracion_ph' => 'Administración y cargas',
    'resumen_incidencia_ph' => 'Incidencia valuatoria',
    'resumen_notas_ph' => 'Notas normativas y salvedades',
];
$phPreviewShort = static function (string $value): string {
    $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    return mb_strlen($value) > 190 ? mb_substr($value, 0, 187) . '…' : $value;
};
?>
<section class="mt-5 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-950">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="font-semibold">Así queda el entregable PH</h3>
            <p class="mt-1 text-xs text-emerald-800">Texto depurado que toma lo esencial de 3.5. Los extractos y soportes quedan en la ficha para revisión.</p>
        </div>
        <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-emerald-700">Vista del informe</span>
    </div>
    <div class="mt-3 rounded-lg border border-emerald-100 bg-white p-4 text-slate-800">
        <?= nl2br(e($phReportText !== '' ? $phReportText : 'Aún no hay texto PH depurado. Guarda o recalcula 3.5 para construirlo.')) ?>
    </div>
    <p class="mt-2 text-xs text-emerald-800">Para editarlo: abre la pestaña <strong>Incidencia valuatoria</strong> y ajusta <strong>Texto final para el entregable</strong>.</p>
    <details class="mt-3 rounded-lg border border-emerald-100 bg-white p-3">
        <summary class="cursor-pointer list-none font-semibold text-emerald-900">Ver qué información alimenta este texto</summary>
        <div class="mt-3 overflow-x-auto">
            <table class="w-full min-w-[56rem] text-left text-xs leading-5">
                <thead class="bg-emerald-50 uppercase text-emerald-800"><tr><th class="p-2">Bloque 3.5</th><th class="p-2">Lectura depurada</th><th class="p-2">Uso en el entregable</th></tr></thead>
                <tbody class="divide-y divide-emerald-50 text-slate-700">
                    <?php foreach ($phPreviewRows as $key => $label): ?>
                        <?php $value = trim((string) ($phTechnicalPreview[$key] ?? '')); ?>
                        <tr>
                            <td class="p-2 font-bold text-emerald-900"><?= e($label) ?></td>
                            <td class="p-2"><?= e($value !== '' ? $phPreviewShort($value) : 'Pendiente de texto depurado') ?></td>
                            <td class="p-2"><?= e($value !== '' ? 'Aporta a la narrativa PH o a sus salvedades.' : 'No entra hasta completarlo o dejar salvedad.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </details>
</section>
