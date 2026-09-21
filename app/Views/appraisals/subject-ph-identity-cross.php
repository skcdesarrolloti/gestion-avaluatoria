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
$compareIdentity('Matrícula del bien sujeto', (string) (($phLegal['property_registration'] ?? '') ?: ($subject['property_registry'] ?? '')), (string) (($linkage['legal_registration'] ?? '') ?: ($subject['property_registry'] ?? '')));
$compareIdentity('Matrícula matriz', (string) ($phLegal['matrix_registration'] ?? ''), $phText('matrix_registration'));
$compareIdentity('Unidad privada', (string) ($phLegal['private_unit'] ?? ''), $phText('private_unit'));
$compareIdentity('Coeficiente de copropiedad', (string) ($phLegal['coefficient'] ?? ''), $phText('coefficient'));
if ($phText('ph_name') !== '') $identityMatches[] = 'Nombre de copropiedad registrado para el banco PH: ' . $phText('ph_name') . '.';
?>
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
