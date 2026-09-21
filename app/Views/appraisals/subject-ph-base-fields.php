<?php
$phStatusPill = static function (string $state): string {
    return match ($state) {
        'ok' => '<button type="button" disabled class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Completo</button>',
        'warn' => '<button type="button" disabled class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Revisar</button>',
        default => '<button type="button" disabled class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Falta</button>',
    };
};
$baseRows = [];
$cityValue = trim((string) ($technical['ciudad_municipio'] ?? ''));
$keyParts = array_filter([$phText('ph_name'), $currentTypology ? ($phCatalog['typologies'][$currentTypology] ?? $currentTypology) : '', $cityValue]);
$baseRows[] = ['Texto editable para Entregable', $technicalValue('resumen_base_ph') !== '' ? 'Texto construido' : 'Sin texto construido', $technicalValue('resumen_base_ph') !== '' ? 'ok' : 'missing', 'Construir el resumen desde la matriz.'];
$baseRows[] = ['Tipología PH de referencia', $currentTypology ? ($phCatalog['typologies'][$currentTypology] ?? $currentTypology) : 'No seleccionada', $currentTypology ? 'ok' : 'missing', 'Seleccionar la tipología comparable.'];
$baseRows[] = ['Llave principal', $keyParts ? implode(' · ', $keyParts) : 'Sin llave completa', count($keyParts) >= 3 ? 'ok' : ($keyParts ? 'warn' : 'missing'), 'Nombre + tipología + ciudad.'];
$baseRows[] = ['Clasificación de dotación', trim((string) ($technical['nivel_dotacion_comparativa'] ?? '')) ?: 'Sin clasificación', trim((string) ($technical['nivel_dotacion_comparativa'] ?? '')) !== '' ? 'ok' : 'missing', 'Nivel comparable de dotación.'];
foreach ($commonStats as [$groupTitle, $found, $total]) {
    $state = $found >= max(1, ceil($total * 0.6)) ? 'ok' : ($found > 0 ? 'warn' : 'missing');
    $baseRows[] = [$groupTitle, (int) $found . ' de ' . (int) $total . ' menciones', $state, 'Completar desde reglamento, visita o soporte manual.'];
}
?>
<div class="rounded-xl border border-slate-200 bg-white p-4 lg:col-span-2">
    <h4 class="font-semibold">Campos base para construir el Entregable</h4>
    <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[42rem] text-left text-sm">
            <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Campo</th><th class="py-2 pr-3">Valor detectado</th><th class="py-2 pr-3">Estado</th><th class="py-2">Qué falta</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($baseRows as [$field, $value, $state, $missing]): ?>
                    <tr><td class="py-2 pr-3 font-semibold text-slate-800"><?= e($field) ?></td><td class="py-2 pr-3 text-slate-700"><?= e($value) ?></td><td class="py-2 pr-3"><?= $phStatusPill($state) ?></td><td class="py-2 text-slate-600"><?= e($state === 'ok' ? 'Listo para texto.' : $missing) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
