<?php
$matrixRows = array_slice(array_values(is_array($comparableRows ?? null) ? $comparableRows : []), 0, 8);
$blankComparable = ['source_type' => '', 'source_name' => '', 'operation' => '', 'property_type' => '',
    'neighborhood' => '', 'project_name' => '', 'price_amount' => '', 'price_unit' => '',
    'area_m2' => '', 'admin_fee' => '', 'consulted_at' => '', 'comparability_notes' => '',
    'listing_code' => '', 'address_hint' => '', 'stratum' => '', 'age_years' => '',
    'building_condition' => '', 'conservation_state' => '', 'view_quality' => '',
    'finish_quality' => '', 'elevator' => '', 'amenities' => '', 'security_features' => '',
    'power_plant' => '', 'parking_relation' => '', 'balcony_terrace' => '',
    'noise_humidity_sun' => '', 'legal_relation_notes' => ''];
while (count($matrixRows) < 8) $matrixRows[] = $blankComparable;
$propertyUnit = [];
foreach (($units ?? []) as $unit) {
    if (($unit['unit_kind'] ?? '') === 'property') { $propertyUnit = $unit; break; }
}
$propertyUnit = $propertyUnit ?: (($units ?? [])[0] ?? []);
$catalogLabel = static fn (string $field, mixed $value): string =>
    (string) (\App\Support\AppraisalCatalog::selectFields()[$field][4][(string) $value] ?? $value);
$first = static function (mixed ...$values): string {
    foreach ($values as $value) if (trim((string) $value) !== '') return trim((string) $value);
    return '';
};
$money = static fn (mixed $value): string => $value === null || $value === '' ? '-' : '$ ' . number_format((float) $value, 0, ',', '.');
$m2 = static fn (mixed $value): string => $value === null || $value === '' ? '-' : rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',') . ' m2';
$unitValue = static function (array $row, string $kind): ?float {
    $price = (float) ($row['price_amount'] ?? 0); $area = (float) ($row['area_m2'] ?? 0);
    if ($price <= 0 || $area <= 0) return null;
    $isRent = (string) ($row['price_unit'] ?? '') === 'canon_mensual' || (string) ($row['operation'] ?? '') === 'Arriendo';
    return ($kind === 'rent') === $isRent ? $price / $area : null;
};
$average = static function (array $rows, string $kind) use ($unitValue): ?float {
    $values = array_values(array_filter(array_map(static fn (array $row): ?float => $unitValue($row, $kind), $rows)));
    return $values === [] ? null : array_sum($values) / count($values);
};
$subjectArea = $first($propertyUnit['area_adopted_m2'] ?? '', $propertyUnit['area_private_m2'] ?? '',
    $propertyUnit['built_area_adopted_m2'] ?? '', $propertyUnit['area_land_m2'] ?? '', $subject['midas_land_area_m2'] ?? '');
$subjectRows = [
    'Área base' => $m2($subjectArea),
    'Tipo de inmueble' => $guide['type_label'] ?? '',
    'Tipo de negocio' => $guide['business_label'] ?? '',
    'Destinación' => $catalogLabel('destinacion', $record['destinacion'] ?? ''),
    'Código del inmueble' => $record['expediente_number'] ?? $record['id'],
    'Procedencia del dato' => 'Sujeto',
    'Ciudad' => $first($subject['city_name'] ?? '', $record['municipio'] ?? ''),
    'Ubicación / microsector' => $first($subject['neighborhood_name'] ?? '', $subject['adopted_address'] ?? ''),
    'Estrato' => $first($subject['stratum'] ?? ''),
    'Piso o nivel' => $first($propertyUnit['construction_floors'] ?? ''),
    'Habitaciones' => $first($propertyUnit['functional_bedrooms_count'] ?? ''),
    'Baños' => $first($propertyUnit['functional_bathrooms_count'] ?? ''),
    'Parqueaderos' => $first($propertyUnit['functional_parking_spaces_count'] ?? ''),
    'Edad / vetustez' => $first($propertyUnit['construction_age_years'] ?? '', $propertyUnit['construction_apparent_age_years'] ?? ''),
    'Estado de conservación' => $catalogLabel('construction_state', $propertyUnit['construction_state'] ?? ''),
    'Vista' => $first($propertyUnit['functional_view'] ?? ''),
    'Acabados' => $first($propertyUnit['functional_finish_quality'] ?? ''),
    'Ascensor' => $first($phProfile['has_elevator'] ?? ''),
    'Amenidades' => $first($phProfile['amenities_summary'] ?? '', $phProfile['common_amenities'] ?? ''),
    'Seguridad' => $first($phProfile['security_summary'] ?? ''),
    'Administración' => $money($record['ph_admin_fee_amount'] ?? ''),
    'Planta eléctrica' => $first($phProfile['power_plant_scope'] ?? ''),
    'Relación jurídica del parqueadero' => $first($propertyUnit['functional_notes'] ?? ''),
    'Balcón / terraza' => '-',
    'Ruido, humedad o asoleamiento' => '-',
    'Valor pedido venta' => '-',
    'Valor pedido arriendo' => '-',
    'Fecha de consulta' => '-',
    'Vr/m2 venta' => '-',
    'Vr/m2 arriendo' => '-',
    'Observación técnica' => 'Dato del bien sujeto tomado de los numerales 1 y 3.',
];
$valueFor = static function (array $row, string $label) use ($money, $m2, $unitValue): string {
    return match ($label) {
        'Área base' => $m2($row['area_m2'] ?? ''),
        'Tipo de inmueble' => (string) ($row['property_type'] ?? ''),
        'Tipo de negocio' => (string) ($row['operation'] ?? ''),
        'Destinación' => '-',
        'Código del inmueble' => (string) ($row['listing_code'] ?? ''),
        'Procedencia del dato' => trim((string) (($row['source_type'] ?? '') . ' ' . ($row['source_name'] ?? ''))),
        'Ciudad' => '-',
        'Ubicación / microsector' => trim((string) (($row['neighborhood'] ?? '') . ' ' . ($row['address_hint'] ?? ''))),
        'Estrato' => (string) ($row['stratum'] ?? ''),
        'Piso o nivel' => (string) ($row['floor_level'] ?? ''),
        'Habitaciones' => (string) ($row['bedrooms'] ?? ''),
        'Baños' => (string) ($row['bathrooms'] ?? ''),
        'Parqueaderos' => (string) ($row['parking_spaces'] ?? ''),
        'Edad / vetustez' => (string) ($row['age_years'] ?? ''),
        'Estado de conservación' => trim((string) (($row['building_condition'] ?? '') . ' ' . ($row['conservation_state'] ?? ''))),
        'Vista' => (string) ($row['view_quality'] ?? ''),
        'Acabados' => (string) ($row['finish_quality'] ?? ''),
        'Ascensor' => (string) ($row['elevator'] ?? ''),
        'Amenidades' => (string) ($row['amenities'] ?? ''),
        'Seguridad' => (string) ($row['security_features'] ?? ''),
        'Administración' => $money($row['admin_fee'] ?? ''),
        'Planta eléctrica' => (string) ($row['power_plant'] ?? ''),
        'Relación jurídica del parqueadero' => (string) ($row['parking_relation'] ?? ''),
        'Balcón / terraza' => (string) ($row['balcony_terrace'] ?? ''),
        'Ruido, humedad o asoleamiento' => (string) ($row['noise_humidity_sun'] ?? ''),
        'Valor pedido venta' => ($row['price_unit'] ?? '') === 'canon_mensual' ? '-' : $money($row['price_amount'] ?? ''),
        'Valor pedido arriendo' => ($row['price_unit'] ?? '') === 'canon_mensual' ? $money($row['price_amount'] ?? '') : '-',
        'Fecha de consulta' => (string) ($row['consulted_at'] ?? ''),
        'Vr/m2 venta' => (($v = $unitValue($row, 'sale')) === null ? '-' : $money($v)),
        'Vr/m2 arriendo' => (($v = $unitValue($row, 'rent')) === null ? '-' : $money($v)),
        'Observación técnica' => (string) ($row['comparability_notes'] ?? ''),
        default => '',
    };
};
?>
<section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Matriz operativa</p>
            <h3 class="mt-2 text-xl font-semibold">Cruce sujeto vs comparables</h3>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Esta vista replica la lógica del cuadro antiguo: el sujeto queda en la segunda columna y las
                muestras capturadas se comparan en paralelo. Edita los datos base en la pestaña Captura.
            </p>
        </div>
    </div>
    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-bold uppercase text-slate-500">Área base sujeto</p><p class="mt-1 font-semibold"><?= e($m2($subjectArea)) ?></p></div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-bold uppercase text-slate-500">Comparables capturados</p><p class="mt-1 font-semibold"><?= e((string) count(array_filter($matrixRows, static fn (array $row): bool => trim((string) ($row['source_name'] ?? '')) !== '' || trim((string) ($row['price_amount'] ?? '')) !== ''))) ?></p></div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-bold uppercase text-slate-500">Promedio vr/m2 venta</p><p class="mt-1 font-semibold"><?= e(($v = $average($matrixRows, 'sale')) === null ? '-' : $money($v)) ?></p></div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-bold uppercase text-slate-500">Promedio vr/m2 arriendo</p><p class="mt-1 font-semibold"><?= e(($v = $average($matrixRows, 'rent')) === null ? '-' : $money($v)) ?></p></div>
    </div>
    <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200">
        <table class="min-w-[1800px] divide-y divide-slate-200 text-left text-sm">
            <thead class="bg-slate-50 text-xs font-bold uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-3">Variables a considerar</th>
                    <th class="px-3 py-3">Valores del sujeto</th>
                    <?php foreach (range(1, 8) as $number): ?>
                        <th class="px-3 py-3">Comparable #<?= e((string) $number) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($subjectRows as $label => $subjectValue): ?>
                    <tr class="align-top">
                        <td class="w-56 px-3 py-3 font-semibold text-slate-800"><?= e($label) ?></td>
                        <td class="w-56 bg-amber-50/40 px-3 py-3 font-medium text-slate-900"><?= e($subjectValue ?: '-') ?></td>
                        <?php foreach ($matrixRows as $row): ?>
                            <td class="w-48 px-3 py-3 text-slate-700"><?= e($valueFor($row, (string) $label) ?: '-') ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
