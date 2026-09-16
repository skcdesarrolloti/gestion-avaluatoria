<?php
$unitId = (string) $unit['id'];
$builtAreaFields = [
    'built_area_manual_m2' => ['Manual', 'Área digitada o calculada por el analista'],
    'built_area_midas_m2' => ['MIDAS', 'Área construida desde visor o ficha territorial'],
    'built_area_tax_m2' => ['Impuesto predial', 'Área construida reportada en predial'],
    'built_area_deed_m2' => ['Escritura', 'Área construida textual de escritura'],
    'built_area_certificate_m2' => ['Certificado de Tradición', 'Área construida del certificado, si aparece'],
    'built_area_other_m2' => ['Otra fuente', 'Plano, licencia, levantamiento u otro soporte'],
];
?>
<div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="activeConstruction === '<?= e($unitId) ?>'"
    x-data="{
        activeConstructionDetail: 'basicos',
        yearBuilt: '<?= e($cv($unit, 'construction_year')) ?>',
        age: '<?= e($cv($unit, 'construction_age_years')) ?>',
        currentYear: new Date().getFullYear(),
        calcAge() { const year = parseInt(this.yearBuilt || '0'); return year > 0 ? Math.max(0, this.currentYear - year) : '' }
    }">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h3 class="text-base font-semibold"><?= e($unit['label'] ?: $constructionUnitLabel($unit)) ?></h3>
        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
            <?= e($unit['igac_typology_hint'] ?: 'Tipología pendiente') ?>
        </span>
    </div>
    <div class="mt-5 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2">
        <?php foreach ($constructionTabs as $key => [$number, $label]): ?>
            <button class="min-h-10 shrink-0 rounded-lg px-3 py-2 text-xs font-semibold" type="button"
                @click="activeConstructionDetail = '<?= e($key) ?>'"
                :class="activeConstructionDetail === '<?= e($key) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                <span class="mr-2 rounded-full bg-blue-700 px-2 py-0.5 text-white"><?= e($number) ?></span><?= e($label) ?>
            </button>
        <?php endforeach; ?>
    </div>
    <div class="mt-5 grid gap-5 md:grid-cols-3" x-show="activeConstructionDetail === 'basicos'">
        <label class="label">Tipo de construcción o anexo
            <select class="input" name="unit_constructions[<?= e($unitId) ?>][construction_type]">
                <?php foreach ($constructionTypes as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $cv($unit, 'construction_type') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label">Unidad de medición
            <select class="input" name="unit_constructions[<?= e($unitId) ?>][construction_measure_unit]">
                <?php foreach ($measureUnits as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= ($cv($unit, 'construction_measure_unit') ?: 'm2') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label">Cantidad
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][construction_quantity]"
                inputmode="decimal" value="<?= e($cv($unit, 'construction_quantity')) ?>" placeholder="Ej. 85,20">
        </label>
    </div>
    <div class="mt-5 grid gap-5 md:grid-cols-2" x-show="activeConstructionDetail === 'pisos'">
        <label class="label">Número de pisos
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][construction_floors]"
                inputmode="numeric" value="<?= e($cv($unit, 'construction_floors')) ?>" placeholder="Ej. 2">
        </label>
        <label class="label">Número de sótanos
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][construction_basements]"
                inputmode="numeric" value="<?= e($cv($unit, 'construction_basements')) ?>" placeholder="Ej. 0">
        </label>
    </div>
    <div class="mt-5 space-y-5" x-show="activeConstructionDetail === 'area'">
        <div class="rounded-xl bg-sky-50 p-4 text-sm leading-6 text-sky-950">
            Registra las fuentes disponibles y adopta el área construida que se usará en reposición.
        </div>
        <div class="grid gap-5 md:grid-cols-3">
            <?php foreach ($builtAreaFields as $key => [$label, $help]): ?>
                <label class="label"><?= e($label) ?> (m²)
                    <input class="input" name="unit_constructions[<?= e($unitId) ?>][<?= e($key) ?>]"
                        inputmode="decimal" value="<?= e($cv($unit, $key)) ?>" placeholder="Ej. 85,20">
                    <span class="mt-1 block text-xs leading-5 text-slate-500"><?= e($help) ?></span>
                </label>
            <?php endforeach; ?>
            <label class="label">Área construida adoptada (m²)
                <input class="input" name="unit_constructions[<?= e($unitId) ?>][built_area_adopted_m2]"
                    inputmode="decimal" value="<?= e($cv($unit, 'built_area_adopted_m2')) ?>" placeholder="Ej. 85,20">
            </label>
            <label class="label">Fuente adoptada
                <select class="input" name="unit_constructions[<?= e($unitId) ?>][built_area_adopted_source]">
                    <option value="">Selecciona fuente adoptada</option>
                    <?php foreach ($areaSources as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $cv($unit, 'built_area_adopted_source') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
    </div>
    <div class="mt-5 grid gap-5 md:grid-cols-3" x-show="activeConstructionDetail === 'vetustez'">
        <label class="label">Año de construcción
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][construction_year]"
                inputmode="numeric" x-model="yearBuilt" value="<?= e($cv($unit, 'construction_year')) ?>" placeholder="Ej. 2010">
        </label>
        <label class="label">Vetustez adoptada (años)
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][construction_age_years]"
                inputmode="numeric" x-model="age" :placeholder="calcAge() || 'Año referencia - año construcción'">
        </label>
        <p class="rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">
            Fórmula guía: <strong>Vetustez = año de referencia - año de construcción</strong>. Puede ajustarse si hubo remodelación integral.
        </p>
    </div>
    <div class="mt-5 grid gap-5 md:grid-cols-3" x-show="activeConstructionDetail === 'estado'">
        <label class="label">Estado de la construcción
            <select class="input" name="unit_constructions[<?= e($unitId) ?>][construction_state]">
                <?php foreach ($stateOptions as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $cv($unit, 'construction_state') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label">% avance de obra
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][construction_progress_percent]"
                inputmode="decimal" value="<?= e($cv($unit, 'construction_progress_percent')) ?>" placeholder="Ej. 80">
        </label>
        <label class="label">% integridad si desmantela
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][construction_integrity_percent]"
                inputmode="decimal" value="<?= e($cv($unit, 'construction_integrity_percent')) ?>" placeholder="Ej. 65">
        </label>
    </div>
    <div class="mt-5 space-y-4" x-show="activeConstructionDetail === 'conservacion'">
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
            Los componentes se proponen desde la tipología constructiva de esta unidad. Marca B = Bueno,
            R = Regular, M = Malo o N/A cuando el elemento no exista en esta construcción.
        </div>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-blue-900 text-xs uppercase tracking-wide text-white">
                    <tr>
                        <th class="px-3 py-3">#</th>
                        <th class="px-3 py-3">Elemento</th>
                        <th class="px-3 py-3">Definición</th>
                        <th class="px-3 py-3">Material / descripción</th>
                        <th class="px-3 py-3">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($componentRows($unit) as $index => $row): ?>
                        <?php $materialKey = 'material_' . $row['key']; ?>
                        <tr class="<?= $index % 2 === 0 ? 'bg-white' : 'bg-slate-50' ?>">
                            <td class="px-3 py-3 text-slate-600"><?= $index + 1 ?></td>
                            <td class="px-3 py-3 font-semibold text-slate-950"><?= e($row['label']) ?></td>
                            <td class="px-3 py-3 text-slate-600"><?= e($row['definition']) ?></td>
                            <td class="px-3 py-3">
                                <select class="input min-w-64" name="unit_constructions[<?= e($unitId) ?>][specifics][<?= e($materialKey) ?>]">
                                    <option value="">Selecciona material</option>
                                    <?php if ($row['suggestion'] !== ''): ?>
                                        <option value="<?= e($row['suggestion']) ?>" <?= $jsonValue($unit, 'construction_specifics_json', $materialKey) === $row['suggestion'] ? 'selected' : '' ?>>
                                            Según tipología: <?= e($row['suggestion']) ?>
                                        </option>
                                    <?php endif; ?>
                                    <?php foreach ($row['materials'] as $material): ?>
                                        <option value="<?= e($material) ?>" <?= $jsonValue($unit, 'construction_specifics_json', $materialKey) === $material ? 'selected' : '' ?>>
                                            <?= e($material) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="px-3 py-3">
                                <select class="input min-w-36" name="unit_constructions[<?= e($unitId) ?>][conservation][<?= e($row['key']) ?>]">
                                    <?php foreach (['' => 'No verificado', 'B' => 'Bueno', 'R' => 'Regular', 'M' => 'Malo', 'NA' => 'No aplica'] as $value => $text): ?>
                                        <option value="<?= e($value) ?>" <?= $jsonValue($unit, 'construction_conservation_json', $row['key']) === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <label class="label mt-5 block" x-show="activeConstructionDetail === 'aspectos'">Aspectos generales
        <textarea class="input" name="unit_constructions[<?= e($unitId) ?>][construction_general_aspects]" rows="4" maxlength="1000"
            placeholder="Describe materiales, acabados, distribución, calidad constructiva y observaciones de visita."><?= e($cv($unit, 'construction_general_aspects')) ?></textarea>
    </label>
    <div class="mt-5 grid gap-5 md:grid-cols-3" x-show="activeConstructionDetail === 'servicios'">
        <?php foreach ($serviceOptions as $key => $label): ?>
            <label class="label"><?= e($label) ?>
                <select class="input" name="unit_constructions[<?= e($unitId) ?>][services][<?= e($key) ?>]">
                    <?php foreach (['' => 'No verificado', 'si' => 'Sí', 'no' => 'No', 'parcial' => 'Parcial'] as $value => $text): ?>
                        <option value="<?= e($value) ?>" <?= $jsonValue($unit, 'construction_services_json', $key) === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        <?php endforeach; ?>
    </div>
    <div class="mt-5 grid gap-5 md:grid-cols-2" x-show="activeConstructionDetail === 'especificos'">
        <?php foreach (['uso' => 'Uso específico', 'altura_libre' => 'Altura libre (m)', 'cubierta' => 'Tipo de cubierta', 'estructura' => 'Material estructura'] as $key => $label): ?>
            <label class="label"><?= e($label) ?>
                <input class="input" name="unit_constructions[<?= e($unitId) ?>][specifics][<?= e($key) ?>]"
                    value="<?= e($jsonValue($unit, 'construction_specifics_json', $key)) ?>" placeholder="<?= e($label) ?>">
            </label>
        <?php endforeach; ?>
    </div>
    <label class="label mt-5 block" x-show="activeConstructionDetail === 'informe'">Texto editable para el entregable
        <textarea class="input" name="unit_constructions[<?= e($unitId) ?>][construction_report_text]" rows="5" maxlength="1500"
            placeholder="Redacción que alimentará la descripción constructiva del informe."><?= e($cv($unit, 'construction_report_text')) ?></textarea>
    </label>
</div>
