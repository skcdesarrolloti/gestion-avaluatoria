<?php
$typologyLabel = (string) ($phCatalog['typologies'][$currentTypology] ?? 'Sin tipología seleccionada');
$nature = $technicalValue('naturaleza_conjunto');
$dominantUse = $technicalValue('uso_dominante');
$complementaryUses = $technicalValue('usos_complementarios');
$functionalRelation = $technicalValue('relacion_funcional_usos');
$specialRegime = $technicalValue('regimen_especial');
$typologyText = mb_strtolower(trim(implode(' ', [$nature, $dominantUse, $complementaryUses, $specialRegime])));
$containsSignal = static fn (array $words): bool => array_reduce($words,
    static fn (bool $found, string $word): bool => $found || str_contains($typologyText, $word), false);
$signals = [];
if ($containsSignal(['oficina', 'consultorio', 'corporativ', 'servicios'])) $signals[] = 'Vocación corporativa o de servicios.';
if ($containsSignal(['local', 'comercio', 'comercial'])) $signals[] = 'Componente comercial o de atención al público.';
if ($containsSignal(['bodega', 'logistic', 'industrial', 'zona franca'])) $signals[] = 'Operación logística, industrial o régimen especial.';
if ($containsSignal(['vivienda', 'residencial', 'habitacional'])) $signals[] = 'Uso habitacional.';
if ($containsSignal(['mixto', 'complementario', 'afin'])) $signals[] = 'Mezcla o complementariedad de usos.';
$signals = array_values(array_unique($signals));
$comparisonRule = match ($currentTypology) {
    'oficinas' => 'Comparar con edificios corporativos, oficinas o consultorios de escala, localización y soporte común semejantes.',
    'comercio' => 'Comparar con copropiedades comerciales con flujo, visibilidad, parqueo y administración similares.',
    'bodegas' => 'Comparar con parques industriales, logísticos o empresariales con soporte operativo equivalente.',
    'residencial' => 'Comparar con conjuntos residenciales de estrato, amenidades, seguridad y administración semejantes.',
    default => 'Seleccionar tipología para fijar la regla de comparación.',
};
$typologyStatusPill = static function (string $state): string {
    return match ($state) {
        'ok' => '<button type="button" disabled class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Completo</button>',
        'warn' => '<button type="button" disabled class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Revisar</button>',
        default => '<button type="button" disabled class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Falta</button>',
    };
};
$shortValue = static function (string $value): string {
    $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    return mb_strlen($value) > 150 ? mb_substr($value, 0, 147) . '…' : $value;
};
$hasSpecialSignal = $containsSignal(['zona franca', 'usuario operador', 'parque empresarial', 'industrial', 'logistic']);
$needsRelation = $complementaryUses !== '' || $currentTypology === 'mixto';
$textState = $technicalValue('resumen_tipologia_ph') === '' ? 'missing' : (($nature === '' || $dominantUse === '' || !$signals) ? 'warn' : 'ok');
$typologyRows = [
    ['Texto editable para Entregable', $technicalValue('resumen_tipologia_ph') !== '' ? 'Texto construido' : 'Sin texto construido', $textState, 'Completar o revisar los campos marcados antes de pasar al Entregable.'],
    ['Tipología seleccionada', $typologyLabel, $currentTypology !== '' ? 'ok' : 'missing', 'Seleccionar residencial, oficinas, comercio, bodegas/logística o mixto.'],
    ['Naturaleza del conjunto', $shortValue($nature) ?: 'Sin naturaleza definida', $nature !== '' ? 'ok' : 'warn', 'Indicar si es edificio corporativo, comercial, residencial, logístico, industrial, mixto o similar.'],
    ['Uso dominante', $shortValue($dominantUse) ?: 'Sin uso dominante', $dominantUse !== '' ? 'ok' : 'missing', 'Precisar el destino principal permitido por reglamento y visita.'],
    ['Usos complementarios', $shortValue($complementaryUses) ?: 'Sin usos complementarios', $complementaryUses !== '' ? 'ok' : 'warn', 'Indicar usos secundarios si existen; si no aplican, dejar constancia.'],
    ['Relación funcional entre usos', $shortValue($functionalRelation) ?: 'Sin relación funcional', $functionalRelation !== '' ? 'ok' : ($needsRelation ? 'warn' : 'ok'), 'Explicar cómo conviven usuarios, actividades, accesos y servicios comunes.'],
    ['Régimen especial', $shortValue($specialRegime) ?: 'Sin régimen especial registrado', $specialRegime !== '' ? 'ok' : ($hasSpecialSignal ? 'warn' : 'ok'), 'Completar si hay zona franca, usuario operador, parque empresarial o regla interna especial.'],
    ['Regla de comparación', $comparisonRule, $currentTypology !== '' ? 'ok' : 'missing', 'La regla depende de la tipología seleccionada.'],
    ['Señales del reglamento', $signals ? implode(' ', $signals) : 'Sin señales suficientes', $signals ? 'ok' : 'warn', 'Revisar destino, usos permitidos, restricciones y normas internas del reglamento.'],
];
?>
<div class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 lg:col-span-2">
    <h4 class="font-semibold text-slate-900">Campos de tipología y régimen para construir el Entregable</h4>
    <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[52rem] text-left text-sm">
            <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Campo</th><th class="py-2 pr-3">Valor detectado</th><th class="py-2 pr-3">Estado</th><th class="py-2">Qué falta</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($typologyRows as [$field, $value, $state, $missing]): ?>
                    <tr><td class="py-2 pr-3 font-semibold text-slate-800"><?= e($field) ?></td><td class="py-2 pr-3 text-slate-700"><?= e($value) ?></td><td class="py-2 pr-3"><?= $typologyStatusPill($state) ?></td><td class="py-2 text-slate-600"><?= e($state === 'ok' ? 'Listo para texto.' : $missing) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
