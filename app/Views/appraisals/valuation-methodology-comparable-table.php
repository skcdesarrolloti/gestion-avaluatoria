<?php require __DIR__ . '/valuation-methodology-comparable-config.php'; ?>
<form id="tabla-madre-83" class="mt-6 scroll-mt-6 rounded-xl border border-slate-200 bg-white p-4 shadow-sm" method="post"
    action="<?= e(url('avaluos/' . $record['id'] . '/metodologia-valuatoria/comparables')) ?>"
    x-data="comparableWorkbench" :data-comparable-mode="searchTab === 'mapa' ? 'cards' : mode" @input="refresh()" @change="refresh()"
    @comparable-imported="showImported($event.detail)"
    data-module-autosave data-save-in-place data-comparable-json
    data-subject-latitude="<?= e($subject['latitude'] ?? '') ?>" data-subject-longitude="<?= e($subject['longitude'] ?? '') ?>"
    data-autosave-endpoint="<?= e(url('avaluos/' . $record['id'] . '/metodologia-valuatoria/comparables/autoguardar')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="component_scope" value="<?= e($componentKey ?? '') ?>">
    <input type="hidden" name="version" value="<?= (int) ($record['comparables_version'] ?? 0) ?>">
    <div x-show="searchTab === 'captura'"><?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-source-links.php'; ?></div>
    <section x-show="['matriz', 'mapa'].includes(searchTab)" x-effect="if (['matriz', 'mapa'].includes(searchTab)) $nextTick(() => syncWidth())">
    <div class="mb-4 flex flex-wrap items-center gap-2" aria-label="Vistas de las muestras">
        <button type="button" class="btn-secondary min-h-11" :aria-pressed="searchTab === 'matriz'" @click="searchTab = 'matriz'">Tabla de muestras (<span x-text="total"></span>)</button>
        <button type="button" class="btn-secondary min-h-11" :aria-pressed="searchTab === 'mapa'" @click="searchTab = 'mapa'">Mapas, coordenadas y fotos</button>
    </div>
    <div id="capture-review" x-show="searchTab === 'matriz'" class="scroll-mt-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Tabla madre de comparables</p>
            <h3 class="mt-2 text-xl font-semibold">Matriz de datos</h3>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Diligencia una fila por cada oferta, transacción o dato de mercado. El sujeto queda fuera de esta tabla:
                aquí se preparan las muestras que se analizarán en M4. Completa ubicación y soporte en «Mapas, coordenadas y fotos».
                Un inmueble por fila y sus datos por columnas. Sin límite de cantidad de muestras; revisa los pendientes, PH y fotos por inmueble.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-sm font-semibold text-slate-500" data-autosave-status>Autoguardado activo</span>
            <button type="submit" class="btn-primary min-h-11">Guardar matriz</button>
            <button type="button" @click="searchTab = 'captura'" class="btn-secondary min-h-11">Seguir capturando</button>
        </div>
    </div>
    <div x-show="searchTab === 'matriz'"><?php require __DIR__ . '/valuation-methodology-capture-areas.php'; ?></div>
    <div x-show="searchTab === 'mapa'"><?php require __DIR__ . '/valuation-methodology-search-map.php'; ?></div>
    <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-comparable-tools.php'; ?>
    <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-comparable-photos.php'; ?>
    <div class="comparable-grid mt-4 overflow-x-auto rounded-xl border border-slate-200" x-ref="grid" x-show="shown > 0"
        @scroll="$refs.topScroll.scrollLeft = $el.scrollLeft">
        <table class="min-w-[4700px] divide-y divide-slate-200 text-left text-sm">
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-comparable-table-head.php'; ?>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($savedRows as $index => $row): $row += $blank; if ($row['id'] === '') $row['id'] = bin2hex(random_bytes(16)); $base = 'comparables[' . $index . ']'; ?>
                    <tr class="align-top">
                        <td class="px-3 py-3 font-bold text-slate-500"><label class="flex min-h-11 items-center gap-2" @input.stop @change.stop><input x-show="searchTab !== 'mapa'" type="checkbox" value="<?= $index ?>" x-model="removalSelection" :disabled="removalBusy || photoBusy || !usedIndexes.includes('<?= $index ?>')" aria-label="Seleccionar muestra <?= $index + 1 ?> para eliminar"><?= $index + 1 ?></label><input type="hidden" name="<?= e($base) ?>[id]" value="<?= e((string) $row['id']) ?>"><button type="button" class="btn-secondary mt-2 min-h-11" @click="openPhotos(<?= $index ?>)" :disabled="removalBusy || mapBusy || photoBusy || photoRetry">Fotos y soporte</button></td>
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
                        <td class="px-3 py-3"><input class="input mt-0 min-w-32" name="<?= e($base) ?>[latitude]" value="<?= e((string) $row['latitude']) ?>" placeholder="10.391000"></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-32" name="<?= e($base) ?>[longitude]" value="<?= e((string) $row['longitude']) ?>" placeholder="-75.550000"></td>
                        <td class="px-3 py-3"><?php $select($base . '[location_precision]', $row['location_precision'], $locationPrecisions, 'min-w-36'); ?></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-40" data-money-input name="<?= e($base) ?>[price_amount]" value="<?= e($money($row['price_amount'])) ?>" placeholder="$ 0"></td>
                        <td class="px-3 py-3"><?php $select($base . '[price_unit]', $row['price_unit'], $priceUnits); ?></td>
                        <td class="px-3 py-3"><input class="input mt-0 min-w-28" name="<?= e($base) ?>[area_m2]" value="<?= e($number($row['area_m2'])) ?>" placeholder="0"></td>
                        <td class="px-3 py-3"><?php $select($base . '[ph_regime]', $row['ph_regime'], ['por_verificar' => 'Por verificar', 'si' => 'Sí, PH', 'no' => 'No PH']); ?></td>
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
                        <td class="px-3 py-3"><textarea class="input mt-0 min-h-24 min-w-52" name="<?= e($base) ?>[map_notes]" placeholder="Ubicación exacta, aproximada o tomada del portal"><?= e((string) $row['map_notes']) ?></textarea></td>
                        <td class="px-3 py-3"><textarea class="input mt-0 min-h-24 min-w-64" name="<?= e($base) ?>[comparability_notes]" placeholder="Por qué sirve o qué ajuste requiere"><?= e((string) $row['comparability_notes']) ?></textarea></td>
                        <td class="px-3 py-3"><textarea class="input mt-0 min-h-24 min-w-56" name="<?= e($base) ?>[rejection_reason]" placeholder="Motivo si se descarta"><?= e((string) $row['rejection_reason']) ?></textarea></td>
                        <?php foreach (\App\Services\ComparableCaptureDetail::fields() as $field => [$label, $type, $scope]): ?>
                            <td class="px-3 py-3"><?php if ($type === 'number'): ?><input type="number" min="0" step="0.0001" class="input min-w-36" name="<?= e($base . '[' . $field . ']') ?>" value="<?= e($row[$field] ?? '') ?>" placeholder="m²; sin separadores de miles"><?php else: ?><textarea class="input min-w-52" name="<?= e($base . '[' . $field . ']') ?>" maxlength="1600" rows="3" placeholder="<?= e($label) ?>; indica lo pendiente de verificar."><?= e($row[$field] ?? '') ?></textarea><?php endif; ?></td>
                        <?php endforeach; ?>
                        <td class="px-3 py-3"><?php $select($base . '[ph_special]', $row['ph_special'] ?? '', ['' => 'Sin tratamiento especial confirmado', 'condominio' => 'Condominio / PH asimilable a NPH (art. 19.2.c)']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </section>
    <input type="hidden" name="matrix_complete" value="1">
</form>

