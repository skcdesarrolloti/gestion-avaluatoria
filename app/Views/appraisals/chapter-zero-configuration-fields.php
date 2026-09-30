<?php
$configTabs = [
    'expediente' => 'Perito y expediente',
    'metodo' => 'Negocio y tipología',
    'renta' => 'Renta',
    'niif' => 'NIIF y PH',
    'notas' => 'Notas',
];
$unitDefinitionUnits = array_values(array_filter($units ?? [], static fn (array $unit): bool => ($unit['unit_kind'] ?? '') !== 'common'));
$igacCategories = $igacCategories ?? [];
$igacTypologiesByCategory = $igacTypologiesByCategory ?? [];
$constructionTypes = \App\Support\AppraisalConstructionTypeCatalog::types();
$valuationTreatments = \App\Support\AppraisalUnitValuationTreatmentCatalog::options();
$unitDisplay = static function (array $unit): string {
    $fallback = ($unit['unit_kind'] ?? '') === 'annex' ? 'Anexo ' : 'Unidad ';
    return trim((string) ($unit['label'] ?? '')) ?: $fallback . (int) ($unit['unit_index'] ?? 0);
};
$treatmentValue = static function (array $unit): string {
    return (string) (($unit['valuation_treatment'] ?? '')
        ?: \App\Support\AppraisalUnitValuationTreatmentCatalog::defaultFor((string) ($unit['unit_kind'] ?? ''), (string) ($unit['construction_type'] ?? '')));
};
$igacCategoryValue = static fn (array $unit): string =>
    \App\Support\AppraisalConstructionTypeCatalog::igacCategoryFor((string) ($unit['construction_type'] ?? ''))
        ?: (($unit['unit_kind'] ?? '') === 'annex' ? 'ANEXOS' : (string) ($unit['igac_category'] ?? ''));
$igacOptionsFor = static fn (string $category): array => $igacTypologiesByCategory[$category] ?? [];
$igacSearchPlaceholder = static fn (array $unit): string =>
    ($unit['unit_kind'] ?? '') === 'annex' ? 'Buscar anexo IGAC: piscina, depósito, kiosco...' : 'Buscar tipología IGAC de la unidad principal';
?>
<nav class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Bloques del numeral 1.1">
    <?php foreach ($configTabs as $key => $label): ?>
        <button type="button" class="min-h-10 shrink-0 rounded-lg px-3 py-2 text-xs font-semibold"
            :class="configTab === '<?= e($key) ?>' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800'"
            @click="configTab = '<?= e($key) ?>'"><?= e($label) ?></button>
    <?php endforeach; ?>
</nav>
<div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
    Diligencia un bloque a la vez. Esta configuración crea y describe las unidades desde 1.1; los numerales 3 y 8 la toman como base.
</div>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'expediente'">
    <label class="label">Perito responsable
        <select class="input" name="appraiser_id">
            <option value="">Selecciona perito</option>
            <?php foreach ($appraisers as $appraiser): ?>
                <option value="<?= e($appraiser['id']) ?>" <?= $selectedAppraiser($appraiser['id']) ?>>
                    <?= e($appraiser['code'] . ' · ' . $appraiser['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (count($appraisers) === 1 && $field('appraiser_id') === ''): ?>
            <span class="mt-1 block text-xs leading-5 text-emerald-700">
                Se selecciona automáticamente porque solo hay un perito vigente.
            </span>
        <?php elseif (count($appraisers) === 0): ?>
            <span class="mt-1 block text-xs leading-5 text-red-700">
                No hay peritos con RAA vigente. Actualiza el RAA en Maestros para asignar expediente.
            </span>
        <?php endif; ?>
    </label>
    <?php require BASE_PATH . '/app/Views/appraisals/appraisal-dossier-field.php'; ?>
</div>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'metodo'" x-cloak>
    <?php foreach (['tipo_negocio', 'tipo_inmueble', 'subtipo_funcional'] as $name) {
        require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php';
    } ?>
    <label class="label">Unidades inmobiliarias principales
        <input class="input" type="number" name="igac_property_units_count" min="0" max="50"
            x-model.number="propertyUnits" placeholder="Ej. 3">
        <span class="mt-1 block text-xs leading-5 text-slate-500">
            Casa principal + 2 apartamentos construidos = 3 unidades principales; después se nombra y clasifica cada una.
        </span>
    </label>
    <label class="label">Anexos existentes
        <input class="input" type="number" name="igac_annex_units_count" min="0" max="50"
            x-model.number="annexUnits" placeholder="Ej. 1">
        <span class="mt-1 block text-xs leading-5 text-slate-500">
            Usa anexos para parqueaderos, depósitos, piscinas, kioscos, ramadas o mejoras accesorias, no para apartamentos independientes.
        </span>
    </label>
    <?php $name = 'estructura_metodo'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>
    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950 md:col-span-2">
        Se prepararán <strong x-text="propertyUnits || 0"></strong> unidad(es) principal(es)
        y <strong x-text="annexUnits || 0"></strong> anexo(s). Guarda este bloque para crear los campos
        y describirlos aquí mismo; 3.1, 3.3 y 8 tomarán esta definición como punto de partida.
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-4 md:col-span-2">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="eyebrow">Definición temprana de unidades y anexos</p>
                <h3 class="mt-2 text-lg font-semibold text-slate-950">Qué compone el predio</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Esta información alimenta la descripción del numeral 3 y la ruta metodológica del numeral 8. Los anexos pueden integrarse al inmueble principal o valorarse por separado solo cuando el analista lo decida.
                </p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600" x-text="(propertyUnits || 0) + (annexUnits || 0) + ' activo(s)'"></span>
        </div>
        <?php if ($unitDefinitionUnits === []): ?>
            <p class="mt-4 rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-600" x-show="(propertyUnits || 0) + (annexUnits || 0) > 0">Define las cantidades de unidades y anexos, guarda el expediente, y aquí aparecerán los campos para describirlos.</p>
        <?php else: ?>
            <p class="mt-4 rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-600" x-show="(propertyUnits || 0) + (annexUnits || 0) === 0">No hay componentes activos. Si necesitas describir unidades, define la cantidad arriba y guarda para crear los campos.</p>
            <div class="mt-4 grid gap-4">
                <?php foreach ($unitDefinitionUnits as $unit): ?>
                    <?php $key = ($unit['unit_kind'] === 'annex' ? 'annex' : 'property') . '-' . (int) $unit['unit_index']; ?>
                    <?php $igacCategory = $igacCategoryValue($unit); ?>
                    <article class="grid gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 md:grid-cols-2"
                        x-show="<?= ($unit['unit_kind'] ?? '') === 'annex' ? 'annexUnits' : 'propertyUnits' ?> >= <?= e((string) (int) ($unit['unit_index'] ?? 0)) ?>"
                        x-data="{
                            unitKind: <?= e(json_encode((string) ($unit['unit_kind'] ?? ''), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                            constructionType: <?= e(json_encode((string) ($unit['construction_type'] ?? ''), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                            igacCategory: <?= e(json_encode($igacCategory, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                            igacHint: <?= e(json_encode((string) ($unit['igac_typology_hint'] ?? ''), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                            syncIgacFromConstruction() {
                                const next = constructionIgacCategories[this.constructionType] || (this.unitKind === 'annex' ? 'ANEXOS' : '')
                                if (next && next !== this.igacCategory) this.igacCategory = next
                                if (!(typologies[this.igacCategory] || []).some(item => item.value === this.igacHint)) this.igacHint = ''
                            }
                        }"
                        x-init="syncIgacFromConstruction()">
                        <label class="label">Nombre del componente
                            <input class="input" name="config_units[<?= e($key) ?>][label]" maxlength="120"
                                value="<?= e($unitDisplay($unit)) ?>" placeholder="Ej. Casa principal, Piscina, Parqueadero 1">
                        </label>
                        <?php if (($unit['unit_kind'] ?? '') === 'property'): ?>
                            <label class="label">Tipo de inmueble de la unidad
                                <select class="input" name="config_units[<?= e($key) ?>][property_type]">
                                    <option value="">Usar tipo general del avalúo</option>
                                    <?php foreach (($catalog['selects']['tipo_inmueble'][4] ?? []) as $value => $text): ?>
                                        <option value="<?= e((string) $value) ?>" <?= (string) ($unit['property_type'] ?? '') === (string) $value ? 'selected' : '' ?>><?= e((string) $text) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        <?php else: ?>
                            <input type="hidden" name="config_units[<?= e($key) ?>][property_type]" value="">
                        <?php endif; ?>
                        <label class="label"><?= ($unit['unit_kind'] ?? '') === 'annex' ? 'Tipo de anexo o mejora' : 'Tipo de construcción' ?>
                            <select class="input" name="config_units[<?= e($key) ?>][construction_type]" x-model="constructionType"
                                @change="syncIgacFromConstruction()">
                                <?php foreach ($constructionTypes as $value => $text): ?>
                                    <option value="<?= e($value) ?>" <?= (string) ($unit['construction_type'] ?? '') === $value ? 'selected' : '' ?>>
                                        <?= e($text) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <?php if (($unit['unit_kind'] ?? '') === 'property'): ?>
                            <label class="label">Buscador IGAC para unidad principal
                                <select class="input" name="config_units[<?= e($key) ?>][igac_category]" x-model="igacCategory"
                                    @change="if (!(typologies[igacCategory] || []).some(item => item.value === igacHint)) igacHint = ''">
                                    <option value="">Selecciona categoría constructiva</option>
                                    <?php foreach ($igacCategories as $category): ?>
                                        <option value="<?= e((string) $category['code']) ?>" <?= $igacCategory === (string) $category['code'] ? 'selected' : '' ?>><?= e((string) $category['name']) ?> · <?= e((string) $category['count']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        <?php else: ?>
                            <input type="hidden" name="config_units[<?= e($key) ?>][igac_category]" value="<?= e($igacCategory) ?>" :value="igacCategory">
                            <div class="rounded-lg border border-teal-100 bg-teal-50 p-3 text-sm text-teal-950">
                                <strong class="block text-xs uppercase text-teal-800">Buscador IGAC para anexos</strong>
                                El tipo elegido arriba define la categoría IGAC y filtra las tipologías disponibles.
                            </div>
                        <?php endif; ?>
                        <label class="label">Tipología IGAC de apoyo
                            <select class="input" name="config_units[<?= e($key) ?>][igac_typology_hint]" x-model="igacHint"
                                :disabled="!igacCategory" x-init="$el.querySelectorAll('[data-fallback-option]').forEach(option => option.remove())">
                                <option value="" x-text="igacCategory ? '<?= e($igacSearchPlaceholder($unit)) ?>' : 'Selecciona primero categoría IGAC'"></option>
                                <?php foreach ($igacOptionsFor($igacCategory) as $option): ?>
                                    <option data-fallback-option value="<?= e((string) $option['value']) ?>" <?= (string) ($unit['igac_typology_hint'] ?? '') === (string) $option['value'] ? 'selected' : '' ?>>
                                        <?= e((string) $option['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                                <template x-for="item in (typologies[igacCategory] || [])" :key="item.value">
                                    <option :value="item.value" x-text="item.label"></option>
                                </template>
                            </select>
                            <span class="mt-1 block text-xs leading-5 text-slate-500" x-show="igacCategory">
                                <span x-text="(typologies[igacCategory] || []).length"></span>
                                referencia(s) IGAC disponibles para esta búsqueda.
                            </span>
                            <?php require BASE_PATH . '/app/Views/appraisals/chapter-zero-igac-preview.php'; ?>
                        </label>
                        <label class="label">Tratamiento en el avalúo
                            <select class="input" name="config_units[<?= e($key) ?>][valuation_treatment]">
                                <?php foreach ($valuationTreatments as $value => $text): ?>
                                    <option value="<?= e($value) ?>" <?= $treatmentValue($unit) === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">
                                Parqueaderos y depósitos suelen integrarse al comparable del apartamento; sepáralos solo por decisión técnica.
                            </span>
                        </label>
                        <label class="label md:col-span-2">Descripción base
                            <input class="input" name="config_units[<?= e($key) ?>][notes]" maxlength="2000"
                                value="<?= e((string) ($unit['notes'] ?? '')) ?>" placeholder="Uso, independencia, restricciones o relación con el predio">
                        </label>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'renta'" x-cloak>
    <?php require BASE_PATH . '/app/Views/appraisals/chapter-zero-income-fields.php'; ?>
</div>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'niif'" x-cloak>
    <?php foreach (['aplica_niif', 'regimen_ph'] as $name) {
        require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php';
    } ?>
</div>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'notas'" x-cloak>
    <label class="label md:col-span-2">Notas de inspección y configuración
        <textarea class="input" name="inspection_notes" rows="4" maxlength="2000"
            placeholder="Anota dudas de campo, componentes del bien o alertas técnicas."><?= e($field('inspection_notes')) ?></textarea>
    </label>
</div>
