<?php
$scenarioValue = static function (string $route, string $field) use ($normativeScenarios): string {
    return (string) ($normativeScenarios[$route][$field] ?? '');
};
$scenarioSelected = static fn (string $route, string $field, string $value): string
    => $scenarioValue($route, $field) === $value ? 'selected' : '';
$scenarioChecked = static fn (string $route): string
    => !empty($normativeScenarios[$route]['enabled']) ? 'checked' : '';
?>
<section id="escenarios" x-show="tab === 'escenarios'" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Mayor y mejor uso</p>
            <h2 class="mt-2 text-2xl font-semibold">Escenarios normativos posibles</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Activa las vías que puedan aplicar al predio. Un lote o inmueble adaptable puede revisarse por varias rutas antes de adoptar una conclusión.</p>
        </div>
        <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">Decisión del perito</span>
    </div>
    <div class="mt-6 grid gap-4 md:grid-cols-3">
        <label class="label">Vía normativa adoptada
            <select class="input" name="adopted_normative_route">
                <option value="">Pendiente de adoptar</option>
                <?php foreach ($normativeScenarioRoutes as $key => $route): ?>
                    <option value="<?= e($key) ?>" <?= e($selected('adopted_normative_route', (string) $key)) ?>><?= e($route['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label md:col-span-2">Etiqueta o sustento corto de la vía adoptada
            <input class="input" type="text" name="adopted_normative_route_label" maxlength="160"
                value="<?= e($value('adopted_normative_route_label')) ?>" placeholder="Ej. Residencial C por mayor y mejor uso">
        </label>
        <div class="md:col-span-3"><?php $textarea('highest_best_use_reason', 'Justificación de mayor y mejor uso', 'Explica por qué se adopta esa vía: vocación del inmueble, mercado, POT, restricciones y soporte revisado.', 4); ?></div>
    </div>
    <div class="mt-6 grid gap-4">
        <?php foreach ($normativeScenarioRoutes as $key => $route): ?>
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <label class="inline-flex items-start gap-3 font-semibold text-slate-950">
                        <input class="mt-1" type="checkbox" name="normative_scenarios[<?= e($key) ?>][enabled]" value="1" <?= e($scenarioChecked((string) $key)) ?>>
                        <span><?= e($route['label']) ?><small class="mt-1 block font-normal leading-5 text-slate-600"><?= e($route['hint']) ?></small></span>
                    </label>
                    <label class="label min-w-52">Resultado
                        <select class="input" name="normative_scenarios[<?= e($key) ?>][result]">
                            <?php foreach ($normativeScenarioResults as $resultKey => $label): ?>
                                <option value="<?= e($resultKey) ?>" <?= e($scenarioSelected((string) $key, 'result', (string) $resultKey)) ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <label class="label">Documento
                        <select class="input" name="normative_scenarios[<?= e($key) ?>][document_slug]"><option value="">Fuente si aplica</option>
                            <?php foreach ($urbanDocuments as $doc): ?><option value="<?= e($doc['slug']) ?>" <?= e($scenarioSelected((string) $key, 'document_slug', (string) $doc['slug'])) ?>><?= e($doc['title']) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label class="label">Cuadro POT
                        <select class="input" name="normative_scenarios[<?= e($key) ?>][table_slug]"><option value="">Cuadro si aplica</option>
                            <?php foreach ($urbanDocuments as $doc): foreach (($doc['tables'] ?? []) as $table): ?><option value="<?= e($table['slug']) ?>" <?= e($scenarioSelected((string) $key, 'table_slug', (string) $table['slug'])) ?>><?= e($table['table_code'] . ' · ' . $table['title']) ?></option><?php endforeach; endforeach; ?>
                        </select>
                    </label>
                    <label class="label">Categoría / actividad
                        <select class="input" name="normative_scenarios[<?= e($key) ?>][category_slug]"><option value="">Categoría si aplica</option>
                            <?php foreach ($urbanCategories as $cat): ?><option value="<?= e($cat['slug']) ?>" <?= e($scenarioSelected((string) $key, 'category_slug', (string) $cat['slug'])) ?>><?= e($cat['table_code'] . ' · ' . $cat['code'] . ' · ' . $cat['name']) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label class="label">Actividad evaluada
                        <input class="input" type="text" name="normative_scenarios[<?= e($key) ?>][activity]" maxlength="180"
                            value="<?= e($scenarioValue((string) $key, 'activity')) ?>" placeholder="Ej. vivienda, comercio 2, institucional 3">
                    </label>
                    <label class="label md:col-span-2">Parámetros relevantes
                        <textarea class="input min-h-24" name="normative_scenarios[<?= e($key) ?>][parameters_summary]" rows="3" maxlength="5000" placeholder="Altura, índice, frente, área libre o restricciones del cuadro."><?= e($scenarioValue((string) $key, 'parameters_summary')) ?></textarea>
                    </label>
                    <label class="label md:col-span-3">Observaciones de este escenario
                        <textarea class="input min-h-24" name="normative_scenarios[<?= e($key) ?>][observations]" rows="3" maxlength="5000" placeholder="Por qué se considera o descarta esta vía."><?= e($scenarioValue((string) $key, 'observations')) ?></textarea>
                    </label>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
