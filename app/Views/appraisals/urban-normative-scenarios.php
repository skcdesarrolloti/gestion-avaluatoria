<?php
$scenarioValue = static fn (string $route, string $field): string => (string) ($normativeScenarios[$route][$field] ?? '');
$scenarioSelected = static fn (string $route, string $field, string $value): string
    => $scenarioValue($route, $field) === $value ? 'selected' : '';
$scenarioChecked = static fn (string $route): string => !empty($normativeScenarios[$route]['enabled']) ? 'checked' : '';
$scenarioJson = e(json_encode($normativeScenarios, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}');
?>
<section id="escenarios" x-show="tab === 'escenarios'" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{
        rows: <?= $scenarioJson ?>,
        n(v) { const x = parseFloat(String(v || '').replace(',', '.').replace(/[^0-9.-]/g, '')); return Number.isFinite(x) ? x : null },
        f(v) { return Number.isFinite(v) ? v.toFixed(2) : '' },
        idx(k) { const r=this.rows[k]||{}, m=this.n(r.construction_index); if (m!==null) return m; const o=this.n(r.occupancy_index), p=this.n(r.max_floors); return o===null||p===null ? null : o*p },
        max(k) { const r=this.rows[k]||{}, m=this.n(r.max_built_area_m2); if (m!==null) return m; const net=this.n(r.net_land_area_m2)||this.n(r.land_area_m2), i=this.idx(k); return net===null||i===null ? null : net*i },
        pot(k) { const max=this.max(k), built=this.n((this.rows[k]||{}).existing_built_area_m2); return max===null||built===null ? null : Math.max(0, max-built) },
        sell(k) { const r=this.rows[k]||{}, m=this.n(r.sellable_area_m2); if (m!==null) return m; const max=this.max(k), factor=this.n(r.sellable_factor); return max===null||factor===null ? null : max*factor }
    }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div><p class="eyebrow">Mayor y mejor uso</p><h2 class="mt-2 text-2xl font-semibold">Comparativo de escenarios normativos</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Compara rutas residencial, institucional, comercial, mixta u otra. El cálculo orienta; la adopción exige soporte legal, físico, de mercado y económico.</p></div>
        <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">Decisión del perito</span>
    </div>
    <div class="mt-6 grid gap-4 md:grid-cols-3">
        <label class="label">Vía normativa adoptada<select class="input" name="adopted_normative_route"><option value="">Pendiente de adoptar</option>
            <?php foreach ($normativeScenarioRoutes as $key => $route): ?><option value="<?= e($key) ?>" <?= e($selected('adopted_normative_route', (string) $key)) ?>><?= e($route['label']) ?></option><?php endforeach; ?>
        </select></label>
        <label class="label md:col-span-2">Etiqueta o sustento corto<input class="input" type="text" name="adopted_normative_route_label" maxlength="160" value="<?= e($value('adopted_normative_route_label')) ?>" placeholder="Ej. Institucional 3 por mayor potencial y compatibilidad"></label>
        <div class="md:col-span-3"><?php $textarea('highest_best_use_reason', 'Justificación de mayor y mejor uso', 'Explica legalidad, posibilidad física, soporte de mercado, coherencia económica y por qué se adopta o descarta cada vía.', 4); ?></div>
    </div>
    <div class="mt-6 overflow-x-auto rounded-xl border border-slate-200">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-600"><tr><th class="px-3 py-2">Ruta</th><th class="px-3 py-2">Resultado</th><th class="px-3 py-2">Máx. construible</th><th class="px-3 py-2">Actual</th><th class="px-3 py-2">Potencial</th><th class="px-3 py-2">Vendible ref.</th><th class="px-3 py-2">Estado</th></tr></thead>
            <tbody class="divide-y divide-slate-100 bg-white">
            <?php foreach ($normativeScenarioRoutes as $key => $route): ?><tr x-show="rows['<?= e((string) $key) ?>']?.enabled"><td class="px-3 py-2 font-semibold"><?= e($route['label']) ?></td><td class="px-3 py-2" x-text="rows['<?= e((string) $key) ?>'].result || 'pendiente'"></td><td class="px-3 py-2" x-text="f(max('<?= e((string) $key) ?>'))"></td><td class="px-3 py-2" x-text="rows['<?= e((string) $key) ?>'].existing_built_area_m2"></td><td class="px-3 py-2 font-semibold text-teal-800" x-text="f(pot('<?= e((string) $key) ?>'))"></td><td class="px-3 py-2" x-text="f(sell('<?= e((string) $key) ?>'))"></td><td class="px-3 py-2" x-text="rows['<?= e((string) $key) ?>'].feasibility || 'pendiente'"></td></tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-6 grid gap-4">
        <?php foreach ($normativeScenarioRoutes as $key => $route): ?>
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <label class="inline-flex items-start gap-3 font-semibold text-slate-950"><input class="mt-1" type="checkbox" name="normative_scenarios[<?= e($key) ?>][enabled]" value="1" x-model="rows['<?= e($key) ?>'].enabled" <?= e($scenarioChecked((string) $key)) ?>><span><?= e($route['label']) ?><small class="mt-1 block font-normal leading-5 text-slate-600"><?= e($route['hint']) ?></small></span></label>
                    <label class="label min-w-52">Resultado<select class="input" name="normative_scenarios[<?= e($key) ?>][result]" x-model="rows['<?= e($key) ?>'].result"><?php foreach ($normativeScenarioResults as $resultKey => $label): ?><option value="<?= e($resultKey) ?>" <?= e($scenarioSelected((string) $key, 'result', (string) $resultKey)) ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
                </div>
                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <label class="label">Documento<select class="input" name="normative_scenarios[<?= e($key) ?>][document_slug]"><option value="">Fuente si aplica</option><?php foreach ($urbanDocuments as $doc): ?><option value="<?= e($doc['slug']) ?>" <?= e($scenarioSelected((string) $key, 'document_slug', (string) $doc['slug'])) ?>><?= e($doc['title']) ?></option><?php endforeach; ?></select></label>
                    <label class="label">Cuadro POT<select class="input" name="normative_scenarios[<?= e($key) ?>][table_slug]"><option value="">Cuadro si aplica</option><?php foreach ($urbanDocuments as $doc): foreach (($doc['tables'] ?? []) as $table): ?><option value="<?= e($table['slug']) ?>" <?= e($scenarioSelected((string) $key, 'table_slug', (string) $table['slug'])) ?>><?= e($table['table_code'] . ' · ' . $table['title']) ?></option><?php endforeach; endforeach; ?></select></label>
                    <label class="label">Categoría / actividad<select class="input" name="normative_scenarios[<?= e($key) ?>][category_slug]"><option value="">Categoría si aplica</option><?php foreach ($urbanCategories as $cat): ?><option value="<?= e($cat['slug']) ?>" <?= e($scenarioSelected((string) $key, 'category_slug', (string) $cat['slug'])) ?>><?= e($cat['table_code'] . ' · ' . $cat['code'] . ' · ' . $cat['name']) ?></option><?php endforeach; ?></select></label>
                    <label class="label">Actividad evaluada<input class="input" type="text" name="normative_scenarios[<?= e($key) ?>][activity]" maxlength="180" value="<?= e($scenarioValue((string) $key, 'activity')) ?>" placeholder="Ej. vivienda, comercio 2, institucional 3"></label>
                    <?php foreach ([['land_area_m2','Terreno m²'],['net_land_area_m2','Área neta m²'],['occupancy_index','Ocupación'],['max_floors','Pisos'],['construction_index','Índice const.'],['max_built_area_m2','Máx. construible'],['existing_built_area_m2','Construido actual'],['potential_area_m2','Potencial adoptado'],['sellable_factor','Factor vendible'],['sellable_area_m2','Área vendible']] as [$field,$label]): ?>
                        <label class="label"><?= e($label) ?><input class="input" type="text" name="normative_scenarios[<?= e($key) ?>][<?= e($field) ?>]" x-model="rows['<?= e($key) ?>'].<?= e($field) ?>" inputmode="decimal" maxlength="40" value="<?= e($scenarioValue((string) $key, $field)) ?>"></label>
                    <?php endforeach; ?>
                    <label class="label">Estado<select class="input" name="normative_scenarios[<?= e($key) ?>][feasibility]" x-model="rows['<?= e($key) ?>'].feasibility"><?php foreach ($normativeScenarioFeasibilities as $fKey => $label): ?><option value="<?= e($fKey) ?>" <?= e($scenarioSelected((string) $key, 'feasibility', (string) $fKey)) ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
                    <label class="label md:col-span-2">Parámetros relevantes<textarea class="input min-h-24" name="normative_scenarios[<?= e($key) ?>][parameters_summary]" rows="3" maxlength="5000" placeholder="Altura, índice, frente, área libre o restricciones."><?= e($scenarioValue((string) $key, 'parameters_summary')) ?></textarea></label>
                    <label class="label md:col-span-3">Observaciones del escenario<textarea class="input min-h-24" name="normative_scenarios[<?= e($key) ?>][observations]" rows="3" maxlength="5000" placeholder="Por qué se considera, limita o descarta esta vía."><?= e($scenarioValue((string) $key, 'observations')) ?></textarea></label>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
