<?php
$phLegal = is_array($phLegalPrefill ?? null) ? $phLegalPrefill : [];
$identityMatches = $identityDiffs = $identityPending = [];
$normalizeIdentity = static fn (string $value): string => mb_strtolower(preg_replace('/[^\p{L}\p{N}]+/u', '', $value) ?? '');
$compareIdentity = static function (string $label, string $ctl, string $phValue) use (&$identityMatches, &$identityDiffs, &$identityPending, $normalizeIdentity): void {
    $ctl = trim($ctl); $phValue = trim($phValue);
    if ($ctl !== '' && $phValue !== '' && $normalizeIdentity($ctl) === $normalizeIdentity($phValue)) $identityMatches[] = $label . ' coincide: ' . $phValue . '.';
    elseif ($ctl !== '' && $phValue !== '') $identityDiffs[] = $label . ': CTL registra ' . $ctl . '; reglamento/ficha PH registra ' . $phValue . '.';
    elseif ($ctl !== '' || $phValue !== '') $identityPending[] = $label . ' solo aparece en ' . ($ctl !== '' ? 'CTL: ' . $ctl : 'reglamento/ficha PH: ' . $phValue) . '.';
    else $identityPending[] = $label . ' no está ubicado en los soportes cargados.';
};
$ctlRegistration = (string) (($phLegal['property_registration'] ?? '') ?: ($subject['property_registry'] ?? ''));
$phRegistration = (string) (($linkage['legal_registration'] ?? '') ?: ($subject['property_registry'] ?? ''));
$compareIdentity('Matrícula del bien sujeto', $ctlRegistration, $phRegistration);
$compareIdentity('Matrícula matriz', (string) ($phLegal['matrix_registration'] ?? ''), $phText('matrix_registration'));
$compareIdentity('Unidad privada', (string) ($phLegal['private_unit'] ?? ''), $phText('private_unit'));
$compareIdentity('Coeficiente de copropiedad', (string) ($phLegal['coefficient'] ?? ''), $phText('coefficient'));
if ($phText('ph_name') !== '') $identityMatches[] = 'Nombre de copropiedad registrado para el banco PH: ' . $phText('ph_name') . '.';
$identityStatusPill = static function (string $state): string {
    return match ($state) {
        'ok' => '<button type="button" disabled class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Completo</button>',
        'warn' => '<button type="button" disabled class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Revisar</button>',
        default => '<button type="button" disabled class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Falta</button>',
    };
};
$identityRows = [];
$identityTextState = $technicalValue('resumen_identificacion_ph') === '' ? 'missing' : (($identityDiffs || $identityPending) ? 'warn' : 'ok');
$identityRows[] = ['Texto editable para Entregable', $technicalValue('resumen_identificacion_ph') !== '' ? 'Texto construido' : 'Sin texto construido', $identityTextState, 'Resolver campos en rojo o amarillo antes de pasar al Entregable.'];
$identityRows[] = ['Nombre / llave técnica PH', $phText('ph_name') ?: 'Sin nombre', $phText('ph_name') !== '' ? 'ok' : 'missing', 'Nombre oficial de la copropiedad según reglamento o escritura.'];
$identityRows[] = ['Matrícula del bien sujeto', trim($phRegistration) !== '' ? $phRegistration : 'Sin matrícula', trim($phRegistration) !== '' ? 'ok' : 'missing', 'Tomar de 3.1 Registro y catastro o del módulo jurídico.'];
$matrixValue = $phText('matrix_registration') ?: (string) ($phLegal['matrix_registration'] ?? '');
$identityRows[] = ['Matrícula matriz', trim($matrixValue) !== '' ? $matrixValue : 'Sin matrícula matriz', trim($matrixValue) !== '' ? 'ok' : 'warn', 'Ubicar en CTL, reglamento o escritura.'];
$identityRows[] = ['Unidad privada analizada', $phText('private_unit') ?: 'Sin unidad privada', $phText('private_unit') !== '' ? 'ok' : 'missing', 'Tomar de 3.1 Unidades y tipologías, jurídica o reglamento.'];
$identityRows[] = ['Coeficiente de copropiedad', $phText('coefficient') ?: 'Sin coeficiente', $phText('coefficient') !== '' ? 'ok' : 'warn', 'Tomar de jurídica o cruzar contra reglamento si aplica.'];
$identityRows[] = ['Cruce CTL / reglamento', $identityDiffs ? count($identityDiffs) . ' diferencia(s)' : ($identityPending ? count($identityPending) . ' dato(s) por completar' : 'Sin diferencias automáticas'), $identityDiffs ? 'warn' : ($identityPending ? 'warn' : 'ok'), 'Revisar diferencias o datos no ubicados.'];
?>
<div class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 lg:col-span-2">
    <h4 class="font-semibold text-slate-900">Campos de identificación PH para construir el Entregable</h4>
    <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[46rem] text-left text-sm">
            <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Campo</th><th class="py-2 pr-3">Valor detectado</th><th class="py-2 pr-3">Estado</th><th class="py-2">Qué falta</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($identityRows as [$field, $value, $state, $missing]): ?>
                    <tr><td class="py-2 pr-3 font-semibold text-slate-800"><?= e($field) ?></td><td class="py-2 pr-3 text-slate-700"><?= e($value) ?></td><td class="py-2 pr-3"><?= $identityStatusPill($state) ?></td><td class="py-2 text-slate-600"><?= e($state === 'ok' ? 'Listo para texto.' : $missing) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="rounded-xl border <?= $identityDiffs ? 'border-amber-200 bg-amber-50 text-amber-950' : 'border-slate-200 bg-white text-slate-700' ?> p-4 text-sm leading-6 lg:col-span-2">
    <strong>Cruce documental CTL / reglamento:</strong>
    <?php if ($identityDiffs): ?>
        <p class="mt-2 font-semibold">Diferencias detectadas</p>
        <ul class="mt-1 space-y-1"><?php foreach ($identityDiffs as $item): ?><li>• <?= e($item) ?></li><?php endforeach; ?></ul>
    <?php else: ?>
        <p class="mt-2">No se detectan diferencias automáticas en los datos disponibles.</p>
    <?php endif; ?>
    <?php if ($identityMatches): ?><p class="mt-2 font-semibold">Coincidencias y datos útiles</p><ul class="mt-1 space-y-1"><?php foreach ($identityMatches as $item): ?><li>• <?= e($item) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <?php if ($identityPending): ?><p class="mt-2 font-semibold">Datos no ubicados o incompletos</p><ul class="mt-1 space-y-1"><?php foreach ($identityPending as $item): ?><li>• <?= e($item) ?></li><?php endforeach; ?></ul><?php endif; ?>
</div>
