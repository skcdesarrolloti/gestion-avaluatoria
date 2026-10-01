<?php
$savedRows = is_array($comparableRows ?? null) ? array_values($comparableRows) : [];
$blank = ['id' => '', 'active' => 'si', 'status' => 'por_verificar', 'source_type' => '',
    'source_name' => '', 'source_url' => '', 'query_used' => '', 'operation' => (string) ($guide['business_label'] ?? ''),
    'property_type' => (string) ($guide['type_label'] ?? ''), 'neighborhood' => '', 'address_hint' => '',
    'project_name' => '', 'price_amount' => '', 'price_unit' => '', 'area_m2' => '', 'admin_fee' => '',
    'vat_applies' => '', 'bedrooms' => '', 'bathrooms' => '', 'parking_spaces' => '', 'floor_level' => '',
    'contact_name' => '', 'contact_phone' => '', 'listing_code' => '', 'listing_date' => '',
    'consulted_at' => date('Y-m-d'), 'stratum' => '', 'age_years' => '', 'building_condition' => '',
    'conservation_state' => '', 'view_quality' => '', 'finish_quality' => '', 'elevator' => '',
    'amenities' => '', 'security_features' => '', 'power_plant' => '', 'parking_relation' => '',
    'balcony_terrace' => '', 'noise_humidity_sun' => '', 'legal_relation_notes' => '',
    'analysis_factor' => '', 'latitude' => '', 'longitude' => '', 'location_precision' => '',
    'ph_regime' => 'por_verificar', 'map_notes' => '', 'comparability_notes' => '', 'rejection_reason' => ''];
$rowCount = max(1, count($savedRows));
while (count($savedRows) < $rowCount) $savedRows[] = array_replace($blank, ['id' => bin2hex(random_bytes(16))]);
$money = static fn (mixed $value): string => $value === null || $value === '' ? '' : '$ ' . number_format((float) $value, 0, ',', '.');
$number = static fn (mixed $value): string => $value === null || $value === '' ? '' : rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',');
$select = static function (string $name, mixed $value, array $options, string $class = ''): void { ?>
    <select name="<?= e($name) ?>" class="min-h-11 w-full min-w-36 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm <?= e($class) ?>">
        <?php foreach ($options as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= (string) $value === (string) $key ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
<?php };
$sourceTypes = ['' => 'Seleccionar', 'portal' => 'Portal', 'inmobiliaria' => 'Inmobiliaria', 'directa' => 'Directa', 'oficial' => 'Oficial', 'otro' => 'Otra'];
$statuses = ['por_verificar' => 'Por verificar', 'preseleccionada' => 'Preseleccionada', 'descartada' => 'Descartada', 'usada' => 'Usada'];
$operations = ['' => 'Seleccionar', 'Venta' => 'Venta', 'Arriendo' => 'Arriendo'];
$priceUnits = ['' => 'Seleccionar', 'precio_total' => 'Precio total', 'canon_mensual' => 'Canon mensual', 'valor_m2' => 'Valor/m2'];
$yesNo = ['' => 'No definido', 'si' => 'Sí', 'no' => 'No'];
$locationPrecisions = ['' => 'No definida', 'exacta' => 'Exacta', 'aproximada' => 'Aproximada', 'sector' => 'Solo sector'];
$tip = static fn (string $text): string => '<span class="help-dot" title="' . e($text) . '">?</span>';
?>
