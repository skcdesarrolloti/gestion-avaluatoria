<?php
$incidenceShort = static function (string $value): string {
    $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    return mb_strlen($value) > 150 ? mb_substr($value, 0, 147) . '…' : $value;
};
$incidencePill = static function (string $state): string {
    return match ($state) {
        'ok' => '<button type="button" disabled class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Completo</button>',
        'warn' => '<button type="button" disabled class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Revisar</button>',
        default => '<button type="button" disabled class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Falta</button>',
    };
};
$incidenceCoreFields = [
    'diagnosis_text' => ['Diagnóstico preliminar de copropiedad', 'Definir si la PH está ordenada, incompleta, con soportes pendientes o con alertas.'],
    'report_text' => ['Texto final para el entregable', 'Texto integrado para pasar al informe, con salvedad técnica y vínculo jurídico.'],
];
$incidenceTechnicalFields = [
    'incidencia_funcional_ph' => ['Incidencia funcional', 'No es un campo literal de la norma; resume si accesos, circulación, ascensores, parqueo y soporte común facilitan o limitan el uso.'],
    'incidencia_comercial_ph' => ['Incidencia comercial', 'No es un campo literal de la norma; traduce imagen, deseabilidad, usuarios, mercado y comparabilidad frente a PH similares.'],
    'incidencia_operativa_ph' => ['Incidencia operativa', 'No es un campo literal de la norma; organiza seguridad, administración, mantenimiento, continuidad u operación interna según tipología.'],
    'incidencia_restricciones_regimen' => ['Incidencia por restricciones', 'Usa reglas del reglamento o visita para indicar si usos, prohibiciones, fachadas, horarios o cargue afectan la operación.'],
    'incidencia_cargas_ph' => ['Incidencia por cargas económicas', 'Usa cuota, expensas, paz y salvo, seguros, coeficientes o cargas pendientes solo cuando estén vinculados al bien sujeto.'],
    'comparacion_mercado_ph' => ['Comparación con PH similares', 'Criterio técnico derivado: estándar, superior, inferior o no concluyente frente a copropiedades de la misma tipología.'],
    'conclusion_valor_ph' => ['Conclusión para valor', 'Cierre profesional: indicar si la PH aporta, limita, es neutra o queda pendiente; no genera ajuste automático.'],
    'salvedades_validacion' => ['Salvedades antes de cerrar', 'Listar soportes o verificaciones pendientes antes de pasar al entregable.'],
];
$incidenceRows = [];
foreach ($incidenceCoreFields as $key => [$label, $missing]) {
    $value = $phText((string) $key);
    $incidenceRows[] = [$label, $incidenceShort($value) ?: 'Sin texto depurado', $value !== '' ? 'ok' : 'missing', $missing];
}
foreach ($incidenceTechnicalFields as $key => [$label, $missing]) {
    $value = $technicalValue((string) $key);
    $state = $value !== '' ? 'ok' : (in_array($key, ['incidencia_funcional_ph', 'incidencia_comercial_ph', 'conclusion_valor_ph'], true) ? 'missing' : 'warn');
    $incidenceRows[] = [$label, $incidenceShort($value) ?: 'Sin criterio depurado', $state, $missing];
}
?>
<div class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6">
    <h4 class="font-semibold text-slate-900">Cierre técnico para construir el Entregable</h4>
    <p class="mt-1 text-slate-600">Estos campos no son una fórmula normativa. Son una matriz interna para cumplir trazabilidad: qué se revisó, cuál soporte existe, qué incide en uso, mercado, operación o cargas, y qué queda como salvedad.</p>
    <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[56rem] text-left text-sm">
            <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Campo</th><th class="py-2 pr-3">Valor detectado</th><th class="py-2 pr-3">Estado</th><th class="py-2">Qué falta</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($incidenceRows as [$field, $value, $state, $missing]): ?>
                    <tr><td class="py-2 pr-3 font-semibold text-slate-800"><?= e($field) ?></td><td class="py-2 pr-3 text-slate-700"><?= e($value) ?></td><td class="py-2 pr-3"><?= $incidencePill($state) ?></td><td class="py-2 text-slate-600"><?= e($state === 'ok' ? 'Listo para texto.' : $missing) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="rounded-xl border border-slate-200 bg-white p-4">
    <h4 class="font-semibold text-slate-900">Detalle editable de incidencia y cierre PH</h4>
    <div class="mt-3 grid gap-4">
        <?php foreach ($incidenceCoreFields as $key => [$label, $help]): ?>
            <?php $renderPhTextarea((string) $key, $label, $phText((string) $key), $help, $key === 'report_text' ? 6 : 4); ?>
        <?php endforeach; ?>
        <?php foreach ($incidenceTechnicalFields as $key => [$label, $help]): ?>
            <label class="label"><?= e($label) ?>
                <textarea class="input mt-2 min-h-24" rows="3" name="ph[technical][<?= e((string) $key) ?>]" placeholder="Criterio valuatorio, soporte, conclusión o salvedad"><?= e($technicalValue((string) $key)) ?></textarea>
                <span class="mt-1 block text-xs font-normal text-slate-500"><?= e($help) ?></span>
            </label>
        <?php endforeach; ?>
    </div>
</div>
