                    <fieldset x-cloak class="min-w-0 grid gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 md:grid-cols-2"
                        :disabled="<?= ($unit['unit_kind'] ?? '') === 'annex' ? 'annexUnits' : 'propertyUnits' ?> < <?= e((string) (int) ($unit['unit_index'] ?? 0)) ?>"
                        x-show="<?= ($unit['unit_kind'] ?? '') === 'annex' ? 'annexUnits' : 'propertyUnits' ?> >= <?= e((string) (int) ($unit['unit_index'] ?? 0)) ?>"
                        x-data="{
                            unitKind: <?= e(json_encode((string) ($unit['unit_kind'] ?? ''), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                            constructionType: <?= e(json_encode((string) ($unit['construction_type'] ?? ''), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                            igacCategory: <?= e(json_encode($igacCategory, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                            igacHint: <?= e(json_encode((string) ($unit['igac_typology_hint'] ?? ''), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                            get igacOptions() {
                                const categories = this.constructionType === 'oficina'
                                    ? [...new Set(['COMERCIALES', 'EDIFICIOS', this.igacCategory])] : [this.igacCategory]
                                return categories.flatMap(category => (typologies[category] || []).map(item => ({...item, category,
                                    label: item.label + (this.constructionType === 'oficina' ? ' · ' + category : '')})))
                            },
                            selectIgacCategory() {
                                const selected = this.igacOptions.find(item => item.value === this.igacHint)
                                if (selected) this.igacCategory = selected.category
                            },
                            syncIgacFromConstruction() {
                                if (this.constructionType === 'oficina' && this.igacHint) { this.selectIgacCategory(); return }
                                const next = constructionIgacCategories[this.constructionType] || (this.unitKind === 'annex' ? 'ANEXOS' : '')
                                if (next && next !== this.igacCategory) this.igacCategory = next
                                if (!this.igacOptions.some(item => item.value === this.igacHint)) this.igacHint = ''
                            }
                        }"
                        x-init="if (unitKind === 'annex' || !igacCategory) syncIgacFromConstruction()">
                        <legend class="px-2 font-semibold"><?= e(($kind === 'annex' ? 'Anexo ' : 'Unidad principal ') . (int) $unit['unit_index']) ?></legend>
                        <label class="label">Nombre del componente
                            <input class="input" name="config_units[<?= e($key) ?>][label]" maxlength="120"
                                value="<?= e($unitDisplay($unit)) ?>" placeholder="Ej. Casa principal, Piscina, Parqueadero 1">
                        </label>
                        <?php if (($unit['unit_kind'] ?? '') === 'property'): ?>
                            <div class="text-sm text-slate-600"><p>Clasificación actual: unidad principal. El nombre no cambia su clasificación.</p>
                                <button type="submit" class="btn-secondary mt-2" data-reclassify-unit name="convert_to_annex" value="<?= e((string) ($unit['id'] ?? '')) ?>">Cambiar a anexo o mejora</button>
                                <p class="mt-1 text-xs">Guarda los datos y conserva este componente, sus fotos y soportes.</p>
                            </div>
                        <?php else: ?><p class="text-sm font-semibold text-teal-800">Clasificación: anexo o mejora</p><?php endif; ?>
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
                                    @change="if (!igacOptions.some(item => item.value === igacHint)) igacHint = ''; else selectIgacCategory()">
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
                                Para Oficina, esta lista reúne <strong>Comerciales y Edificios</strong>, incluidos ED.Servicios_Tipo_1, Tipo_2 y Tipo_3. Cada referencia indica su categoría original. Revisa su descripción y especificaciones antes de elegir; no todas corresponden a tu oficina. La tipología la decide el analista.
                            </span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500" x-show="igacCategory">
                                <span x-text="igacOptions.length"></span>
                                referencia(s) IGAC disponibles para esta búsqueda.
                            </span>
                            <?php require BASE_PATH . '/app/Views/appraisals/chapter-zero-igac-preview.php'; ?>
                        </label>
                        <?php require __DIR__ . '/chapter-zero-unit-method.php'; ?>
                        <label class="label">Cómo se incluye en el avalúo · Tratamiento en el avalúo
                            <select class="input" name="config_units[<?= e($key) ?>][valuation_treatment]">
                                <?php foreach ($valuationTreatments as $value => $text): ?>
                                    <option value="<?= e($value) ?>" <?= $treatmentValue($unit) === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">
                                Comprueba naturaleza jurídica y cobertura. En PH distingue la liquidación del sujeto (art. 36) de la depuración de muestras (art. 19).
                            </span>
                        </label>
                        <label class="label">Estructura del método de esta unidad o anexo
                            <select class="input" name="config_units[<?= e($key) ?>][method_structure]">
                                <?php foreach (\App\Support\UnitMethodStructure::options() as $value => $text): ?>
                                    <option value="<?= e($value) ?>" <?= ($unit['method_structure'] ?? '') === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="mt-1 block text-xs text-slate-500">Define qué comprende este componente según sus datos registrados. No selecciona automáticamente Mercado, Costo ni otro método.</span>
                        </label>
                        <label class="label md:col-span-2">Descripción base
                            <input class="input" name="config_units[<?= e($key) ?>][notes]" maxlength="2000"
                                value="<?= e((string) ($unit['notes'] ?? '')) ?>" placeholder="Uso, independencia, restricciones o relación con el predio">
                        </label>
                    </fieldset>
