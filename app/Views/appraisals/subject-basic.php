<?php
$sv = static fn (string $key): string => (string) ($subject[$key] ?? '');
$subjectActionBase = $subjectActionBase ?? 'avaluos/' . $record['id'] . '/bien-sujeto';
$textLabels = [
    'point_reference' => ['Punto de referencia', 'Ej. Zona residencial consolidada'],
    'address' => ['Dirección / nomenclatura', 'Ej. Barrio Bruselas D 25 No. 49-72'],
    'alternate_nomenclature' => ['Nomenclatura alterna', 'Ej. N/A'],
    'property_registry' => ['Matrícula inmobiliaria', 'Ej. 060-260018'],
    'cadastral_reference' => ['Referencia catastral', 'Ej. 01-09-0130-0016-000'],
    'registry_office' => ['Oficina de registro / círculo registral', 'Ej. Cartagena'],
    'restrictions' => ['Restricciones / limitaciones', 'Ej. N/A'],
    'legal_urban_affectations' => ['Afectaciones jurídicas o urbanas', 'Ej. Sin afectaciones reportadas'],
    'current_use' => ['Uso actual', 'Ej. Residencial'],
    'main_potential_use' => ['Uso potencial principal', 'Ej. Residencial'],
    'complementary_potential_uses' => ['Usos potenciales complementarios', 'Ej. Comercial'],
    'main_complementary_activity' => ['Actividad complementaria principal observada', 'Ej. Servicios'],
    'secondary_complementary_activities' => ['Actividades complementarias secundarias', 'Ej. Logística'],
    'latitude' => ['Latitud', 'Ej. 10.41534968'],
    'longitude' => ['Longitud', 'Ej. -75.53213628'],
];
$groups = [
    'Ubicación y referencia' => ['point_reference', 'address', 'alternate_nomenclature'],
    'Identificación jurídica y urbana' => ['property_registry', 'cadastral_reference', 'registry_office',
        'horizontal_property', 'centrality', 'immediate_environment', 'stratum', 'urban_license',
        'permitted_use', 'urban_treatment', 'restrictions', 'legal_urban_affectations', 'road_condition'],
    'Uso, acceso y servicios' => ['access_facility', 'transport_connectivity', 'loading_unloading',
        'current_use', 'main_potential_use', 'complementary_potential_uses', 'main_complementary_activity',
        'secondary_complementary_activities', 'current_occupation', 'water_service', 'energy_service',
        'gas_service', 'sewer_service', 'internet_service', 'service_continuity'],
    'Fecha y coordenadas' => ['subject_reference_date', 'latitude', 'longitude'],
];
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{
        departments: <?= e(json_encode($geo['departments'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        cities: <?= e(json_encode($geo['cities'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        neighborhoods: <?= e(json_encode($geo['neighborhoods'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        departmentId: <?= e(json_encode($sv('department_id'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        cityId: <?= e(json_encode($sv('city_id'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        neighborhoodId: <?= e(json_encode($sv('neighborhood_id'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        saved: <?= e(json_encode([
            'department_name' => $sv('department_name'), 'city_name' => $sv('city_name'),
            'neighborhood_name' => $sv('neighborhood_name'), 'locality_name' => $sv('locality_name'),
            'commune_ucg' => $sv('commune_ucg'), 'zone_sector' => $sv('zone_sector'),
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        filteredCities() { return this.cities.filter(city => !this.departmentId || city.department_id === this.departmentId) },
        filteredNeighborhoods() { return this.neighborhoods.filter(item => !this.cityId || item.city_id === this.cityId) },
        selectedNeighborhood() { return this.neighborhoods.find(item => item.id === this.neighborhoodId) || null },
        locationValue(key) { const row = this.selectedNeighborhood(); return row ? (row[key] || '') : (this.saved[key] || '') },
        locationDisplay(key) {
            const row = this.selectedNeighborhood();
            if (!row) return this.saved[key] || '';
            return row[key] || 'Pendiente en maestro';
        },
        changeDepartment() { this.cityId = ''; this.neighborhoodId = '' },
        changeCity() { this.neighborhoodId = '' }
    }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">2.1 Ficha básica del sujeto</p>
            <h2 class="mt-2 text-2xl font-semibold">Identificación y características del inmueble</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Esta ficha concentra los datos que describen el bien sujeto. Al seleccionar barrio o microsector,
                el sistema trae localidad, comuna/UCG y zona/sector desde los maestros.
            </p>
        </div>
        <a class="btn-secondary" href="<?= e(url('maestros')) ?>">Abrir maestros</a>
    </div>
    <?php if ($subjectMessage): ?>
        <p class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($subjectMessage) ?></p>
    <?php endif; ?>
    <?php if ($subjectError): ?>
        <p class="mt-5 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($subjectError) ?></p>
    <?php endif; ?>
    <form class="mt-6 space-y-7" method="post" action="<?= e(url($subjectActionBase . '/ficha-basica')) ?>"
        x-data="{ busy: false }" @submit="busy = true">
        <?= csrf_field() ?>
        <div class="rounded-xl border border-slate-200 p-5">
            <h3 class="text-base font-semibold">Ubicación del inmueble</h3>
            <div class="mt-5 grid gap-5 md:grid-cols-3">
                <label class="label">Departamento
                    <select class="input" name="department_id" x-model="departmentId" @change="changeDepartment()">
                        <option value="">Selecciona departamento</option>
                        <template x-for="item in departments" :key="item.id">
                            <option :value="item.id" x-text="item.name"></option>
                        </template>
                    </select>
                </label>
                <label class="label">Municipio / distrito
                    <select class="input" name="city_id" x-model="cityId" @change="changeCity()">
                        <option value="">Selecciona municipio</option>
                        <template x-for="item in filteredCities()" :key="item.id">
                            <option :value="item.id" x-text="item.name"></option>
                        </template>
                    </select>
                </label>
                <label class="label">Barrio / microsector
                    <select class="input" name="neighborhood_id" x-model="neighborhoodId">
                        <option value="">Selecciona barrio</option>
                        <template x-for="item in filteredNeighborhoods()" :key="item.id">
                            <option :value="item.id" x-text="item.name"></option>
                        </template>
                    </select>
                </label>
                <label class="label">Localidad
                    <input class="input bg-slate-50" readonly :value="locationDisplay('locality_name')" placeholder="Se completa con el barrio">
                </label>
                <label class="label">Comuna / UCG
                    <input class="input bg-slate-50" readonly :value="locationDisplay('commune_ucg')" placeholder="Se completa con el barrio">
                </label>
                <label class="label">Zona / sector
                    <input class="input bg-slate-50" readonly :value="locationDisplay('zone_sector')" placeholder="Se completa con el barrio">
                </label>
            </div>
        </div>
        <?php foreach ($groups as $title => $keys): ?>
            <div class="rounded-xl border border-slate-200 p-5">
                <h3 class="text-base font-semibold"><?= e($title) ?></h3>
                <div class="mt-5 grid gap-5 md:grid-cols-3">
                    <?php foreach ($keys as $key): ?>
                        <?php if (isset($subjectCatalog[$key])): [$label, $options] = $subjectCatalog[$key]; ?>
                            <label class="label"><?= e($label) ?>
                                <select class="input" name="<?= e($key) ?>">
                                    <option value="">Selecciona opción</option>
                                    <?php foreach ($options as $value => $option): ?>
                                        <option value="<?= e($value) ?>" <?= $sv($key) === (string) $value ? 'selected' : '' ?>><?= e($option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        <?php elseif ($key === 'subject_reference_date'): ?>
                            <label class="label">Fecha de referencia del sujeto
                                <input class="input" type="date" name="subject_reference_date" value="<?= e($sv($key)) ?>">
                            </label>
                        <?php else: [$label, $placeholder] = $textLabels[$key]; ?>
                            <label class="label <?= in_array($key, ['restrictions', 'legal_urban_affectations'], true) ? 'md:col-span-2' : '' ?>">
                                <?= e($label) ?>
                                <input class="input" name="<?= e($key) ?>" value="<?= e($sv($key)) ?>" placeholder="<?= e($placeholder) ?>">
                            </label>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <label class="label block">Notas de la ficha básica
            <textarea class="input" name="notes" rows="4" maxlength="2000"
                placeholder="Observaciones del sujeto que deban pasar al análisis o al informe."><?= e($sv('notes')) ?></textarea>
        </label>
        <div class="flex justify-end">
            <button class="btn-primary" type="submit" :disabled="busy"
                x-text="busy ? 'Guardando...' : 'Guardar ficha básica'">Guardar ficha básica</button>
        </div>
    </form>
</section>
