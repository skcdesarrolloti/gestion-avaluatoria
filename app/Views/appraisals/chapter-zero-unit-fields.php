                    <fieldset x-cloak class="min-w-0 grid gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 md:grid-cols-2"
                        :disabled="<?= ($unit['unit_kind'] ?? '') === 'annex' ? 'annexUnits' : 'propertyUnits' ?> < <?= e((string) (int) ($unit['unit_index'] ?? 0)) ?>"
                        x-show="<?= ($unit['unit_kind'] ?? '') === 'annex' ? 'annexUnits' : 'propertyUnits' ?> >= <?= e((string) (int) ($unit['unit_index'] ?? 0)) ?>"
                        x-data="igacUnitSelector(<?= e(json_encode([
                            'unitKind' => (string) ($unit['unit_kind'] ?? ''),
                            'constructionType' => (string) ($unit['construction_type'] ?? ''),
                            'igacCategory' => $igacCategory,
                            'igacHint' => (string) ($unit['igac_typology_hint'] ?? ''),
                        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>)"
                        x-init="if (unitKind === 'annex' || !igacCategory) syncIgacFromConstruction()">
                        <legend class="px-2 font-semibold"><?= e(($kind === 'annex' ? 'Anexo ' : 'Unidad principal ') . (int) $unit['unit_index']) ?></legend>
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
                            <label class="label"><span x-text="constructionType === 'oficina' ? 'Categoría de la referencia IGAC' : 'Buscador IGAC para unidad principal'">Buscador IGAC para unidad principal</span>
                                <select class="input" name="config_units[<?= e($key) ?>][igac_category]" x-model="igacCategory"
                                    :disabled="constructionType === 'oficina'"
                                    @change="if (!allIgacOptions.some(item => item.value === igacHint)) igacHint = ''; else selectIgacCategory()">
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
                        <label class="label">Buscar referencia constructiva IGAC
                            <input class="input" type="search" x-model="igacQuery" @input.stop @change.stop placeholder="Ej. garaje, celda de parqueo, depósito">
                            <span class="mt-1 text-xs font-normal text-slate-600">Busca en denominación, descripción y especificaciones; reconoce términos equivalentes.</span>
                        </label>
                        <label class="flex min-h-11 items-center gap-2 text-sm">
                            <input type="checkbox" x-model="igacShowAll" @change.stop> Ver toda la categoría, sin filtro por tipo de construcción
                        </label>
                        <p class="text-sm text-amber-900 md:col-span-2" x-show="constructionType === 'parqueo'" x-cloak>
                            Garaje, parqueadero y celda de parqueo comparten búsqueda. El catálogo cargado ofrece referencias relacionadas,
                            como sótanos y pavimentos; verifica si corresponden a la celda o a una construcción completa antes de elegir.
                        </p>
                        <label class="label">Tipología IGAC de apoyo
                            <select class="input" name="config_units[<?= e($key) ?>][igac_typology_hint]" x-model="igacHint"
                                @change="selectIgacCategory()"
                                :disabled="!igacCategory" x-init="$el.querySelectorAll('[data-fallback-option]').forEach(option => option.remove())">
                                <option value="" x-text="igacCategory ? '<?= e($igacSearchPlaceholder($unit)) ?>' : 'Selecciona primero categoría IGAC'"></option>
                                <?php foreach (\App\Support\AppraisalConstructionTypeCatalog::optionsForUnit($unit, $igacTypologiesByCategory) as $option): ?>
                                    <option data-fallback-option value="<?= e((string) $option['value']) ?>" <?= (string) ($unit['igac_typology_hint'] ?? '') === (string) $option['value'] ? 'selected' : '' ?>>
                                        <?= e((string) $option['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                                <template x-for="item in igacOptions" :key="item.value">
                                    <option :value="item.value" x-text="item.label"></option>
                                </template>
                            </select>
                            <span class="mt-2 block text-sm font-normal leading-6 text-teal-900" x-show="constructionType === 'oficina'" x-cloak>
                                Oficinas: referencias de Comerciales y Edificios. Consulta sus especificaciones antes de elegir.
                            </span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500" x-show="igacCategory">
                                <span x-text="igacOptions.length"></span>
                                referencia(s) mostradas. La selección guardada se conserva aunque no coincida con el filtro.
                            </span>
                            <span role="status" class="mt-2 block text-sm text-amber-900" x-show="igacCategory && !igacOptions.length" x-cloak>
                                No hay coincidencias. Prueba otro término o amplía la categoría; no se asignará una referencia automáticamente.
                            </span>
                        </label>
                        <?php require BASE_PATH . '/app/Views/appraisals/chapter-zero-igac-preview.php'; ?>
                        <?php require __DIR__ . '/chapter-zero-unit-method.php'; ?>
                        <label class="label">Tratamiento en el avalúo
                            <select class="input" name="config_units[<?= e($key) ?>][valuation_treatment]">
                                <?php foreach ($valuationTreatments as $value => $text): ?>
                                    <option value="<?= e($value) ?>" <?= $treatmentValue($unit) === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="label">Qué comprende esta unidad
                            <select class="input" name="config_units[<?= e($key) ?>][method_structure]">
                                <?php foreach (\App\Support\UnitMethodStructure::options() as $value => $text): ?>
                                    <option value="<?= e($value) ?>" <?= ($unit['method_structure'] ?? '') === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <input type="hidden" name="config_units[<?= e($key) ?>][notes]" value="<?= e((string) ($unit['notes'] ?? '')) ?>">
                    </fieldset>
