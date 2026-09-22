<?php
$ruleShort = static function (string $value): string {
    $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    return mb_strlen($value) > 150 ? mb_substr($value, 0, 147) . '…' : $value;
};
$rulePill = static function (string $state): string {
    return match ($state) {
        'ok' => '<button type="button" disabled class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Completo</button>',
        'warn' => '<button type="button" disabled class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Revisar</button>',
        default => '<button type="button" disabled class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Falta</button>',
    };
};
$ruleFields = [
    'usos_permitidos' => ['Usos permitidos', 'Precisar el destino permitido y su relación con la unidad objeto.'],
    'usos_restringidos' => ['Usos restringidos o prohibidos', 'Identificar prohibiciones, limitaciones de actividad, imagen, horarios o convivencia.'],
    'reglas_constructivas' => ['Reglas constructivas y adecuaciones', 'Revisar fachadas, avisos, obras, cerramientos, licencias o autorizaciones internas.'],
    'condiciones_normativas_operativas' => ['Condiciones operativas', 'Completar residuos, movilidad, horarios, seguridad, ocupación o funcionamiento.'],
    'condiciones_usuario_operador' => ['Usuario operador o administración', 'Aplicar si hay administrador, usuario operador, autorización previa o régimen especial.'],
    'cargue_descargue' => ['Cargue, descargue y movilidad', 'Aplicar si la tipología o la unidad exige operación logística, parqueo o circulación especial.'],
];
$ruleApplicability = static fn (string $key): bool => isset($typologyTechnicalKeys[$key]) || in_array($key, ['usos_permitidos', 'usos_restringidos', 'reglas_constructivas'], true);
$ruleRows = [['Texto editable para Entregable', $technicalValue('resumen_reglas_ph') !== '' ? 'Texto construido' : 'Sin texto construido', $technicalValue('resumen_reglas_ph') !== '' ? 'ok' : 'missing', 'El texto se arma con los campos completos y se puede editar antes de pasar al Entregable.'],
    ['Restricciones de uso u operación', $ruleShort($phText('restrictions_text')) ?: 'Sin restricciones depuradas', $phText('restrictions_text') !== '' ? 'ok' : 'warn', 'Separar restricciones reales de citas extensas del reglamento.']];
foreach ($ruleFields as $key => [$label, $missing]) {
    $value = $technicalValue((string) $key);
    $applies = $ruleApplicability((string) $key);
    $state = $value !== '' ? 'ok' : ($applies ? 'warn' : 'missing');
    $prefix = $applies ? 'Aplica para la tipología seleccionada. ' : 'Podría aplicar según unidad, visita o soporte. ';
    $ruleRows[] = [$label, $ruleShort($value) ?: 'Sin dato depurado', $state, $prefix . $missing];
}
?>
<div class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 lg:col-span-2">
    <h4 class="font-semibold text-slate-900">Campos de reglas de uso y operación para construir el Entregable</h4>
    <p class="mt-1 text-slate-600">La matriz resume qué regla alimenta el texto. Azul es aplicación directa para la tipología; amarillo es regla complementaria que el analista confirma si aplica a la unidad.</p>
    <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[56rem] text-left text-sm">
            <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Campo</th><th class="py-2 pr-3">Valor detectado</th><th class="py-2 pr-3">Estado</th><th class="py-2">Qué falta</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($ruleRows as [$field, $value, $state, $missing]): ?>
                    <tr><td class="py-2 pr-3 font-semibold text-slate-800"><?= e($field) ?></td><td class="py-2 pr-3 text-slate-700"><?= e($value) ?></td><td class="py-2 pr-3"><?= $rulePill($state) ?></td><td class="py-2 text-slate-600"><?= e($state === 'ok' ? 'Listo para texto.' : $missing) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="rounded-xl border border-slate-200 bg-white p-4 lg:col-span-2">
    <h4 class="font-semibold text-slate-900">Detalle editable de reglas, restricciones y operación</h4>
    <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[56rem] text-left text-sm">
            <thead class="text-xs uppercase text-slate-500"><tr><th class="w-64 py-2 pr-3">Campo</th><th class="w-64 py-2 pr-3">Aplicabilidad</th><th class="py-2">Evidencia y observación</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <tr><td class="py-2 pr-3 font-semibold text-slate-800">Restricciones de uso u operación</td><td class="py-2 pr-3"><span class="inline-flex rounded-full border border-blue-100 bg-blue-50 px-2 py-1 text-xs font-extrabold text-blue-800">Aplica para la tipología seleccionada</span></td><td class="py-2"><textarea class="input mt-0 min-h-20 py-2 text-sm" rows="2" name="ph[restrictions_text]" placeholder="Usos, horarios, movilidad, residuos, cerramientos o adecuaciones."><?= e($phText('restrictions_text')) ?></textarea></td></tr>
                <?php foreach ($ruleFields as $key => [$label]): $applies = $ruleApplicability((string) $key); ?>
                    <tr><td class="py-2 pr-3 font-semibold text-slate-800"><?= e($label) ?></td><td class="py-2 pr-3"><span class="inline-flex rounded-full border px-2 py-1 text-xs font-extrabold <?= $applies ? 'border-blue-100 bg-blue-50 text-blue-800' : 'border-amber-300 bg-amber-200 text-amber-950' ?>"><?= e($applies ? 'Aplica para la tipología seleccionada' : 'Podría aplicar para la tipología seleccionada') ?></span></td><td class="py-2"><textarea class="input mt-0 min-h-20 py-2 text-sm" rows="2" name="ph[technical][<?= e((string) $key) ?>]" placeholder="Regla, página, cláusula o validación de visita"><?= e($technicalValue((string) $key)) ?></textarea></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
