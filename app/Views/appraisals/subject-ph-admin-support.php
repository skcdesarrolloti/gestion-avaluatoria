<?php
$adminShort = static function (string $value): string {
    $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    return mb_strlen($value) > 150 ? mb_substr($value, 0, 147) . '…' : $value;
};
$adminPill = static function (string $state): string {
    return match ($state) {
        'ok' => '<button type="button" disabled class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Completo</button>',
        'warn' => '<button type="button" disabled class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Revisar</button>',
        default => '<button type="button" disabled class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Falta</button>',
    };
};
$adminCoreFields = [
    'administration_name' => ['Administración / razón social', 'Identificar administrador o razón social si existe soporte vigente.'],
    'administration_contact' => ['Contacto de administración', 'Completar contacto responsable cuando exista certificado o visita.'],
    'administration_phone' => ['Teléfono', 'Registrar teléfono útil de administración, no teléfono de notaría u OCR incidental.'],
    'administration_email' => ['Correo', 'Registrar correo vigente si aparece en soporte actual.'],
    'monthly_fee' => ['Cuota de administración', 'Precisar valor de cuota ordinaria si aplica al bien sujeto.'],
    'fee_status' => ['Estado de expensas / paz y salvo', 'Confirmar paz y salvo, mora, pendiente o por confirmar.'],
    'reserve_fund' => ['Fondo / imprevistos', 'Depurar fondo, expensas extraordinarias o reservas comunes si inciden en cargas.'],
    'insurance_status' => ['Seguros comunes', 'Confirmar póliza o seguros comunes vigentes cuando existan.'],
];
$adminTechnicalFields = [
    'coeficientes_copropiedad' => ['Coeficientes y módulos', 'Vincular coeficientes solo si corresponden al bien sujeto.'],
    'expensas_cuotas' => ['Expensas, cuotas y cargas', 'Identificar expensas ordinarias, extraordinarias o cargas comunes.'],
    'responsabilidades_bienes_comunes' => ['Responsabilidades sobre bienes comunes', 'Precisar obligaciones de conservación, mantenimiento o uso común.'],
    'cargas_comercializacion' => ['Cargas que afecten operación o comercialización', 'Registrar restricciones económicas u operativas que incidan en negociación.'],
];
$adminRows = [['Texto editable para Entregable', $technicalValue('resumen_administracion_ph') !== '' ? 'Texto construido' : 'Sin texto construido', $technicalValue('resumen_administracion_ph') !== '' ? 'ok' : 'missing', 'Construir el párrafo con soportes vigentes y datos del bien sujeto.']];
foreach ($adminCoreFields as $key => [$label, $missing]) {
    $value = $phText((string) $key);
    $adminRows[] = [$label, $adminShort($value) ?: 'Sin dato depurado', $value !== '' ? 'ok' : 'warn', $missing];
}
foreach ($adminTechnicalFields as $key => [$label, $missing]) {
    $value = $technicalValue((string) $key);
    $adminRows[] = [$label, $adminShort($value) ?: 'Sin dato depurado', $value !== '' ? 'ok' : 'warn', $missing];
}
?>
<div class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 lg:col-span-2">
    <h4 class="font-semibold text-slate-900">Campos de administración y cargas para construir el Entregable</h4>
    <p class="mt-1 text-slate-600">Separa datos de administración, obligaciones económicas y soportes vigentes. El texto debe usar solo hechos depurados y aplicables al bien sujeto.</p>
    <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[56rem] text-left text-sm">
            <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Campo</th><th class="py-2 pr-3">Valor detectado</th><th class="py-2 pr-3">Estado</th><th class="py-2">Qué falta</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($adminRows as [$field, $value, $state, $missing]): ?>
                    <tr><td class="py-2 pr-3 font-semibold text-slate-800"><?= e($field) ?></td><td class="py-2 pr-3 text-slate-700"><?= e($value) ?></td><td class="py-2 pr-3"><?= $adminPill($state) ?></td><td class="py-2 text-slate-600"><?= e($state === 'ok' ? 'Listo para texto.' : $missing) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="rounded-xl border border-slate-200 bg-white p-4 lg:col-span-2">
    <h4 class="font-semibold text-slate-900">Detalle editable de administración, expensas y cargas</h4>
    <div class="mt-3 grid gap-4 lg:grid-cols-2">
        <?php foreach ($adminCoreFields as $key => [$label, $help]): ?>
            <?php $renderPhInput((string) $key, $label, $phText((string) $key), $help); ?>
        <?php endforeach; ?>
        <?php foreach ($adminTechnicalFields as $key => [$label, $help]): ?>
            <label class="label"><?= e($label) ?>
                <textarea class="input mt-2 min-h-20" rows="2" name="ph[technical][<?= e((string) $key) ?>]" placeholder="Soporte, valor, obligación o salvedad aplicable al bien sujeto"><?= e($technicalValue((string) $key)) ?></textarea>
                <span class="mt-1 block text-xs font-normal text-slate-500"><?= e($help) ?></span>
            </label>
        <?php endforeach; ?>
    </div>
</div>
