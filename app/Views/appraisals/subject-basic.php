<?php
$sv = static fn (string $key): string => (string) ($subject[$key] ?? '');
$fieldHelp = static fn (string $key): string => (string) ($subjectHelp[$key] ?? '');
$subjectActionBase = $subjectActionBase ?? 'avaluos/' . $record['id'] . '/bien-sujeto';
$internalCode = 'INT_' . preg_replace('/[^A-Za-z0-9_]+/', '_', (string) ($record['titulo'] ?: substr($record['id'], 0, 8)));
$textLabels = [
    'subject_title' => ['Título o identificación', 'Ej. Lote Bruselas'],
    'address' => ['Dirección física', 'Ej. Barrio Bruselas D 25 No. 49-72'],
    'address_certificate' => ['Dirección desde Certificado de Tradición', 'Ej. Barrio Bruselas D 23 No. 44-72'],
    'address_midas' => ['Dirección desde MIDAS', 'Ej. D 23 44 72'],
    'address_tax' => ['Dirección desde Impuesto predial', 'Ej. D 23 44 72'],
    'address_deed' => ['Dirección desde Escritura', 'Ej. Barrio Bruselas D 23 No. 44-72'],
    'address_other' => ['Dirección desde otra fuente', 'Portal, visita, certificado adicional, etc.'],
    'adopted_address' => ['Ubicación adoptada', 'Dirección que se adopta técnicamente'],
    'point_reference' => ['Punto de referencia', 'Ej. Zona residencial consolidada'],
    'alternate_nomenclature' => ['Nomenclatura alterna', 'Ej. N/A'],
    'property_registry' => ['Matrícula inmobiliaria', 'Ej. 060-260018'],
    'cadastral_reference' => ['Referencia catastral', 'Ej. 01-09-0130-0016-000'],
    'registry_office' => ['Oficina de registro / círculo registral', 'Ej. Cartagena'],
    'restrictions' => ['Restricciones / limitaciones', 'Ej. N/A'],
    'legal_urban_affectations' => ['Afectaciones jurídicas o urbanas', 'Ej. Sin afectaciones reportadas'],
    'complementary_potential_uses' => ['Usos potenciales complementarios', 'Ej. Comercial'],
    'secondary_complementary_activities' => ['Actividades complementarias secundarias', 'Ej. Logística'],
    'latitude' => ['Latitud', 'Ej. 10.41534968'],
    'longitude' => ['Longitud', 'Ej. -75.53213628'],
];
$tabs = [
    'identificacion' => ['Identificación', ['subject_title', 'address']],
    'fuentes' => ['Fuentes de dirección', ['address_certificate', 'address_midas', 'address_tax',
        'address_deed', 'address_other', 'adopted_source', 'adopted_address']],
    'ubicacion' => ['Ubicación territorial', []],
    'referencia' => ['Referencia', ['point_reference', 'alternate_nomenclature']],
    'registro' => ['Registro y catastro', ['property_registry', 'cadastral_reference', 'registry_office', 'stratum']],
    'norma' => ['Norma urbana', ['urban_license', 'permitted_use', 'urban_treatment']],
    'restricciones' => ['Restricciones', ['restrictions', 'legal_urban_affectations']],
    'entorno' => ['Entorno', ['centrality', 'immediate_environment', 'road_condition']],
    'acceso' => ['Acceso y movilidad', ['access_facility', 'transport_connectivity', 'loading_unloading']],
    'usos' => ['Usos y ocupación', ['current_use', 'main_potential_use', 'complementary_potential_uses',
        'main_complementary_activity', 'secondary_complementary_activities', 'current_occupation']],
    'servicios' => ['Servicios', ['water_service', 'energy_service', 'gas_service',
        'sewer_service', 'internet_service', 'service_continuity']],
    'cierre' => ['Fecha, coordenadas y notas', ['subject_reference_date', 'latitude', 'longitude']],
    'tipologias' => ['Tipologías IGAC', []],
];
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{
        activeTab: 'identificacion',
        busy: false,
        typologyHint: <?= e(json_encode($field('igac_typology_hint'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        igacCategory: <?= e(json_encode($field('igac_category'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        propertyUnits: <?= e((string) $count('igac_property_units_count')) ?>,
        annexUnits: <?= e((string) $count('igac_annex_units_count')) ?>,
        departments: <?= e(json_encode($geo['departments'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        cities: <?= e(json_encode($geo['cities'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        neighborhoods: <?= e(json_encode($geo['neighborhoods'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        departmentId: <?= e(json_encode($sv('department_id'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        cityId: <?= e(json_encode($sv('city_id'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        neighborhoodId: <?= e(json_encode($sv('neighborhood_id'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        saved: <?= e(json_encode(['locality_name' => $sv('locality_name'), 'commune_ucg' => $sv('commune_ucg'),
            'zone_sector' => $sv('zone_sector')], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        filteredCities() { return this.cities.filter(city => !this.departmentId || city.department_id === this.departmentId) },
        filteredNeighborhoods() { return this.neighborhoods.filter(item => !this.cityId || item.city_id === this.cityId) },
        selectedNeighborhood() { return this.neighborhoods.find(item => item.id === this.neighborhoodId) || null },
        locationDisplay(key) { const row = this.selectedNeighborhood(); return row ? (row[key] || 'Pendiente en maestro') : (this.saved[key] || '') },
        changeDepartment() { this.cityId = ''; this.neighborhoodId = '' },
        changeCity() { this.neighborhoodId = '' }
    }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">2.1 Ficha básica del sujeto</p>
            <h2 class="mt-2 text-2xl font-semibold">Identificación y características del inmueble</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Misma lógica base de InversKC, organizada en subpestañas para diligenciar sin perderse.
            </p>
        </div>
        <a class="btn-secondary" href="<?= e(url('maestros')) ?>">Abrir maestros</a>
    </div>
    <?php if ($subjectMessage): ?><p class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($subjectMessage) ?></p><?php endif; ?>
    <?php if ($subjectError): ?><p class="mt-5 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($subjectError) ?></p><?php endif; ?>
    <form class="mt-6" method="post" action="<?= e(url($subjectActionBase . '/ficha-basica')) ?>" @submit="busy = true">
        <?= csrf_field() ?>
        <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
            <?php foreach ($tabs as $key => [$title]): ?>
                <button type="button" class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                    @click="activeTab = '<?= e($key) ?>'"
                    :class="activeTab === '<?= e($key) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                    <?= e($title) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="activeTab === 'identificacion'">
            <h3 class="text-base font-semibold">Identificación</h3>
            <div class="mt-5 grid gap-5 md:grid-cols-3">
                <label class="label">Código interno <span class="help-dot" title="Llave interna del sujeto. Se genera automáticamente y no debe editarse manualmente.">?</span>
                    <input class="input bg-slate-50" value="<?= e($internalCode) ?>" readonly>
                </label>
                <?php foreach ($tabs['identificacion'][1] as $key) require BASE_PATH . '/app/Views/appraisals/subject-basic-field.php'; ?>
            </div>
        </div>
        <div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="activeTab === 'ubicacion'">
            <h3 class="text-base font-semibold">Ubicación territorial</h3>
            <div class="mt-5 grid gap-5 md:grid-cols-3">
                <?php require BASE_PATH . '/app/Views/appraisals/subject-location-fields.php'; ?>
            </div>
        </div>
        <?php foreach ($tabs as $tabKey => [$title, $keys]): ?>
            <?php if (in_array($tabKey, ['identificacion', 'ubicacion', 'tipologias'], true)) continue; ?>
            <div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="activeTab === '<?= e($tabKey) ?>'">
                <h3 class="text-base font-semibold"><?= e($title) ?></h3>
                <div class="mt-5 grid gap-5 md:grid-cols-3">
                    <?php foreach ($keys as $key) require BASE_PATH . '/app/Views/appraisals/subject-basic-field.php'; ?>
                </div>
                <?php if ($tabKey === 'cierre'): ?>
                    <label class="label mt-5 block">Observaciones generales <span class="help-dot" title="<?= e($fieldHelp('notes')) ?>">?</span>
                        <textarea class="input" name="notes" rows="4" maxlength="2000"
                            placeholder="Criterio técnico, salvedades o notas útiles para las siguientes pestañas."><?= e($sv('notes')) ?></textarea>
                    </label>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <div class="mt-5 flex justify-end">
            <button class="btn-primary" type="submit" :disabled="busy"
                x-show="activeTab !== 'tipologias'"
                x-text="busy ? 'Guardando...' : 'Guardar ficha básica'">Guardar ficha básica</button>
        </div>
    </form>
    <div class="mt-5 space-y-5" x-show="activeTab === 'tipologias'">
        <?php require BASE_PATH . '/app/Views/appraisals/preclassification.php'; ?>
        <?php require BASE_PATH . '/app/Views/appraisals/unit-tabs.php'; ?>
    </div>
</section>
