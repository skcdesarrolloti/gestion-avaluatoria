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
    'analysis_factor' => '', 'comparability_notes' => '', 'rejection_reason' => ''];
$rowCount = max(15, count($savedRows));
while (count($savedRows) < $rowCount) $savedRows[] = $blank;
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
?>
<form class="mt-6 rounded-xl border border-slate-200 bg-white p-4 shadow-sm" method="post"
    action="<?= e(url('avaluos/' . $record['id'] . '/metodologia-valuatoria/comparables')) ?>"
    data-module-autosave data-save-in-place
    data-autosave-endpoint="<?= e(url('avaluos/' . $record['id'] . '/metodologia-valuatoria/comparables/autoguardar')) ?>">
    <?= csrf_field() ?>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Tabla madre de comparables</p>
            <h3 class="mt-2 text-xl font-semibold">Captura de investigación verificable</h3>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Diligencia una fila por cada oferta, transacción o dato de mercado. El sujeto queda fuera de esta tabla:
                aquí solo van las muestras comparables que luego pasarán a depuración, variables y fórmulas.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-sm font-semibold text-slate-500" data-autosave-status>Autoguardado activo</span>
            <button type="submit" class="btn-primary min-h-11">Guardar captura</button>
        </div>
    </div>
    <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200">
        <table class="min-w-[4200px] divide-y divide-slate-200 text-left text-sm">
            <thead class="bg-slate-50 text-xs font-bold uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-3">#</th><th class="px-3 py-3">Usar</th><th class="px-3 py-3">Estado</th>
                    <th class="px-3 py-3">Factor 8.4</th>
                    <th class="px-3 py-3">Tipo fuente</th><th class="px-3 py-3">Fuente</th><th class="px-3 py-3">Enlace</th>
                    <th class="px-3 py-3">Consulta</th><th class="px-3 py-3">Operación</th><th class="px-3 py-3">Tipo inmueble</th>
                    <th class="px-3 py-3">Barrio/sector</th><th class="px-3 py-3">Dirección</th><th class="px-3 py-3">Edificio/proyecto</th>
                    <th class="px-3 py-3">Precio/canon</th><th class="px-3 py-3">Unidad</th><th class="px-3 py-3">Área m2</th>
                    <th class="px-3 py-3">Admin.</th><th class="px-3 py-3">IVA</th><th class="px-3 py-3">Alc.</th>
                    <th class="px-3 py-3">Baños</th><th class="px-3 py-3">Parq.</th><th class="px-3 py-3">Piso</th>
                    <th class="px-3 py-3">Estrato</th><th class="px-3 py-3">Edad</th><th class="px-3 py-3">Estado edif.</th>
                    <th class="px-3 py-3">Conservación</th><th class="px-3 py-3">Vista</th><th class="px-3 py-3">Acabados</th>
                    <th class="px-3 py-3">Ascensor</th><th class="px-3 py-3">Amenidades</th><th class="px-3 py-3">Seguridad</th>
                    <th class="px-3 py-3">Planta</th><th class="px-3 py-3">Relación parq.</th><th class="px-3 py-3">Balcón/terraza</th>
                    <th class="px-3 py-3">Ruido/humedad/sol</th><th class="px-3 py-3">Nota jurídica</th>
                    <th class="px-3 py-3">Contacto</th><th class="px-3 py-3">Teléfono</th><th class="px-3 py-3">Código</th>
                    <th class="px-3 py-3">Fecha aviso</th><th class="px-3 py-3">Consulta</th><th class="px-3 py-3">Observación</th><th class="px-3 py-3">Descartar por</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($savedRows as $index => $row): $row += $blank; $base = 'comparables[' . $index . ']'; ?>
                    <tr class="align-top">
                        <td class="px-3 py-3 font-bold text-slate-500"><?= e((string) ($index + 1)) ?><input type="hidden" name="<?= e($base) ?>[id]" value="<?= e((string) $row['id']) ?>"></td>
                        <td class="px-3 py-3"><?php $select($base . '[active]', $row['active'], ['si' => 'Sí', 'no' => 'No'], 'min-w-24'); ?></td>
                        <td class="px-3 py-3"><?php $select($base . '[status]', $row['status'], $statuses); ?></td>
                        <td class="px-3 py-3"><?php $select($base . '[analysis_factor]', $row['analysis_factor'], $factorOptions ?? ['' => 'Seleccionar factor'], 'min-w-48'); ?></td>
                        <td class="px-3 py-3"><?php $select($base . '[source_type]', $row['source_type'], $sourceTypes); ?></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-48" name="<?= e($base) ?>[source_name]" value="<?= e((string) $row['source_name']) ?>" placeholder="Araújo & Segovia"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-64" name="<?= e($base) ?>[source_url]" value="<?= e((string) $row['source_url']) ?>" placeholder="https://..."></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-64" name="<?= e($base) ?>[query_used]" value="<?= e((string) $row['query_used']) ?>" placeholder="venta apartamento Castillogrande"></td>
                        <td class="px-3 py-3"><?php $select($base . '[operation]', $row['operation'], $operations); ?></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-40" name="<?= e($base) ?>[property_type]" value="<?= e((string) $row['property_type']) ?>" placeholder="Apartamento"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-44" name="<?= e($base) ?>[neighborhood]" value="<?= e((string) $row['neighborhood']) ?>" placeholder="Castillogrande"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-52" name="<?= e($base) ?>[address_hint]" value="<?= e((string) $row['address_hint']) ?>" placeholder="Dirección aproximada"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-52" name="<?= e($base) ?>[project_name]" value="<?= e((string) $row['project_name']) ?>" placeholder="Edificio o conjunto"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-40" data-money-input name="<?= e($base) ?>[price_amount]" value="<?= e($money($row['price_amount'])) ?>" placeholder="$ 0"></td>
                        <td class="px-3 py-3"><?php $select($base . '[price_unit]', $row['price_unit'], $priceUnits); ?></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-28" name="<?= e($base) ?>[area_m2]" value="<?= e($number($row['area_m2'])) ?>" placeholder="0"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-36" data-money-input name="<?= e($base) ?>[admin_fee]" value="<?= e($money($row['admin_fee'])) ?>" placeholder="$ 0"></td>
                        <td class="px-3 py-3"><?php $select($base . '[vat_applies]', $row['vat_applies'], $yesNo, 'min-w-28'); ?></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-24" name="<?= e($base) ?>[bedrooms]" value="<?= e((string) $row['bedrooms']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-24" name="<?= e($base) ?>[bathrooms]" value="<?= e((string) $row['bathrooms']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-24" name="<?= e($base) ?>[parking_spaces]" value="<?= e((string) $row['parking_spaces']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-28" name="<?= e($base) ?>[floor_level]" value="<?= e((string) $row['floor_level']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-24" name="<?= e($base) ?>[stratum]" value="<?= e((string) $row['stratum']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-24" name="<?= e($base) ?>[age_years]" value="<?= e((string) $row['age_years']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-40" name="<?= e($base) ?>[building_condition]" value="<?= e((string) $row['building_condition']) ?>" placeholder="Nuevo, usado"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-40" name="<?= e($base) ?>[conservation_state]" value="<?= e((string) $row['conservation_state']) ?>" placeholder="Bueno, regular"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-36" name="<?= e($base) ?>[view_quality]" value="<?= e((string) $row['view_quality']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-40" name="<?= e($base) ?>[finish_quality]" value="<?= e((string) $row['finish_quality']) ?>"></td>
                        <td class="px-3 py-3"><?php $select($base . '[elevator]', $row['elevator'], $yesNo, 'min-w-28'); ?></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-56" name="<?= e($base) ?>[amenities]" value="<?= e((string) $row['amenities']) ?>" placeholder="Piscina, gimnasio, salón"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-44" name="<?= e($base) ?>[security_features]" value="<?= e((string) $row['security_features']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-36" name="<?= e($base) ?>[power_plant]" value="<?= e((string) $row['power_plant']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-52" name="<?= e($base) ?>[parking_relation]" value="<?= e((string) $row['parking_relation']) ?>" placeholder="Privado, comunal, asignado"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-44" name="<?= e($base) ?>[balcony_terrace]" value="<?= e((string) $row['balcony_terrace']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-52" name="<?= e($base) ?>[noise_humidity_sun]" value="<?= e((string) $row['noise_humidity_sun']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-56" name="<?= e($base) ?>[legal_relation_notes]" value="<?= e((string) $row['legal_relation_notes']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-44" name="<?= e($base) ?>[contact_name]" value="<?= e((string) $row['contact_name']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-36" name="<?= e($base) ?>[contact_phone]" value="<?= e((string) $row['contact_phone']) ?>"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-36" name="<?= e($base) ?>[listing_code]" value="<?= e((string) $row['listing_code']) ?>"></td>
                        <td class="px-3 py-3"><input type="date" class="input mt-0 min-w-40" name="<?= e($base) ?>[listing_date]" value="<?= e((string) $row['listing_date']) ?>"></td>
                        <td class="px-3 py-3"><input type="date" class="input mt-0 min-w-40" name="<?= e($base) ?>[consulted_at]" value="<?= e((string) $row['consulted_at']) ?>"></td>
                        <td class="px-3 py-3"><textarea class="input mt-0 min-h-24 min-w-64" name="<?= e($base) ?>[comparability_notes]" placeholder="Por qué sirve o qué ajuste requiere"><?= e((string) $row['comparability_notes']) ?></textarea></td>
                        <td class="px-3 py-3"><textarea class="input mt-0 min-h-24 min-w-56" name="<?= e($base) ?>[rejection_reason]" placeholder="Motivo si se descarta"><?= e((string) $row['rejection_reason']) ?></textarea></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</form>
