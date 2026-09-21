<?php
$typologyLabel = (string) ($phCatalog['typologies'][$currentTypology] ?? 'Pendiente de selección');
$typologyText = mb_strtolower(trim(implode(' ', [
    $technicalValue('naturaleza_conjunto'), $technicalValue('uso_dominante'),
    $technicalValue('usos_complementarios'), $technicalValue('regimen_especial'),
])));
$signals = [];
$containsSignal = static fn (array $words): bool => array_reduce($words,
    static fn (bool $found, string $word): bool => $found || str_contains($typologyText, $word), false);
if ($containsSignal(['oficina', 'consultorio', 'corporativ', 'servicios'])) $signals[] = 'Vocación corporativa o de servicios.';
if ($containsSignal(['local', 'comercio', 'comercial'])) $signals[] = 'Componente comercial o de atención al público.';
if ($containsSignal(['bodega', 'logistic', 'industrial', 'zona franca'])) $signals[] = 'Operación logística, industrial o régimen especial.';
if ($containsSignal(['vivienda', 'residencial', 'habitacional'])) $signals[] = 'Uso habitacional.';
if ($containsSignal(['mixto', 'complementario', 'afin'])) $signals[] = 'Mezcla o complementariedad de usos.';
$comparisonRule = match ($currentTypology) {
    'oficinas' => 'Comparar con edificios corporativos o de servicios de escala y localización semejante.',
    'comercio' => 'Comparar con copropiedades comerciales con flujo, visibilidad y parqueo similares.',
    'bodegas' => 'Comparar con parques industriales, logísticos o empresariales con soporte operativo equivalente.',
    'residencial' => 'Comparar con conjuntos residenciales de estrato, amenidades y administración semejantes.',
    default => 'Selecciona la tipología para fijar la regla de comparación.',
};
?>
<div class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 text-slate-700 lg:col-span-2">
    <strong>Lectura técnica de tipología:</strong>
    <div class="mt-3 grid gap-3 md:grid-cols-3">
        <div class="rounded-lg bg-slate-50 p-3"><span class="font-semibold">Tipología aplicada</span><br><?= e($typologyLabel) ?></div>
        <div class="rounded-lg bg-slate-50 p-3"><span class="font-semibold">Regla de comparación</span><br><?= e($comparisonRule) ?></div>
        <div class="rounded-lg bg-slate-50 p-3"><span class="font-semibold">Régimen especial</span><br><?= $technicalValue('regimen_especial') !== '' ? 'Con mención documental' : 'Sin mención registrada' ?></div>
    </div>
    <p class="mt-3 font-semibold">Señales del reglamento</p>
    <?php if ($signals): ?>
        <ul class="mt-1 space-y-1"><?php foreach (array_unique($signals) as $item): ?><li>• <?= e($item) ?></li><?php endforeach; ?></ul>
    <?php else: ?>
        <p class="mt-1">Aún no hay señales suficientes de uso; conserva la tipología seleccionada como criterio del analista.</p>
    <?php endif; ?>
</div>
