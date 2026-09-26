<?php
$scenarioValue = static fn (string $route, string $field): string => (string) ($normativeScenarios[$route][$field] ?? '');
$scenarioTip = static fn (string $text): string => '<span class="help-dot" title="' . e($text) . '">?</span>';
$potentialRoutes = \App\Support\UrbanNormPotentialCatalog::routesForProfile($profile);
$potentialRoutesJson = e(json_encode($potentialRoutes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]');
$scenarioInitial = static fn (string $key): string => e(json_encode((string) ($profile[$key] ?? ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: "''");
?>
<section id="escenarios" x-show="tab === 'escenarios'" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{
        routes: <?= $potentialRoutesJson ?>,
        categorySlug: <?= $scenarioInitial('category_slug') ?>,
        categoryLabel: <?= $scenarioInitial('applicable_activity') ?>,
        land: <?= $scenarioInitial('land_area_normative_m2') ?>,
        net: <?= $scenarioInitial('net_land_area_m2') ?>,
        front: <?= $scenarioInitial('lot_front_normative_m') ?>,
        actual: <?= $scenarioInitial('actual_built_area_m2') ?>,
        factor: <?= $scenarioInitial('sellable_area_factor') ?>,
        adopted: <?= $scenarioInitial('adopted_normative_route') ?>,
        adoptedLabel: <?= $scenarioInitial('adopted_normative_route_label') ?>,
        reason: <?= $scenarioInitial('highest_best_use_reason') ?>,
        n(v) { const x = parseFloat(String(v || '').replace(',', '.').replace(/[^0-9.-]/g, '')); return Number.isFinite(x) ? x : null },
        f(v) { return Number.isFinite(v) ? v.toFixed(2) : '' },
        baseArea() { return this.n(this.net) ?? this.n(this.land) },
        typeLabel(type) { return type === 'principal' ? 'Principal' : 'Compatible' },
        rows() {
            const out = [];
            for (const route of this.routes) for (const [key, rule] of Object.entries(route.options || {})) out.push({route, key, rule});
            return out;
        },
        hasRows() { return this.rows().length > 0 },
        canCalculate(row) { return this.n(row.rule.construction_index) !== null },
        maxBuild(row) { const base = this.baseArea(), index = this.n(row.rule.construction_index); return base === null || index === null ? null : base * index },
        potential(row) { const max = this.maxBuild(row), actual = this.n(this.actual); return max === null || actual === null ? null : Math.max(0, max - actual) },
        sellable(row) { const max = this.maxBuild(row), factor = this.n(this.factor); return max === null || factor === null ? null : max * factor },
        areaOk(row) { const min = this.n(row.rule.min_area_m2), land = this.n(this.land); if (min === null) return null; return land === null ? undefined : land >= min },
        frontOk(row) { const min = this.n(row.rule.min_front_m), front = this.n(this.front); if (min === null) return null; return front === null ? undefined : front >= min },
        rowStatus(row) {
            if (!this.canCalculate(row)) return 'Condicionado';
            const a=this.areaOk(row), f=this.frontOk(row);
            if (a === undefined || f === undefined) return 'Falta dato';
            return (a !== false && f !== false) ? 'Cumple base' : 'No cumple base';
        },
        rowClass(row) { const s=this.rowStatus(row); return s === 'Cumple base' ? 'bg-emerald-50 text-emerald-800' : (s === 'No cumple base' ? 'bg-red-50 text-red-700' : (s === 'Condicionado' ? 'bg-blue-50 text-blue-800' : 'bg-amber-50 text-amber-800')) },
        adopt(row) {
            this.adopted = row.route.type + ':' + row.route.slug + ':' + row.key;
            this.adoptedLabel = this.typeLabel(row.route.type) + ' · ' + row.route.label + ' · ' + row.rule.label;
            const status = this.rowStatus(row), max = this.f(this.maxBuild(row)), pot = this.f(this.potential(row));
            this.reason = status + '. Se revisa ' + this.adoptedLabel + ' (' + row.route.table + '). AML ' + (row.rule.min_area_m2 || 'condicionado') + ', frente ' + (row.rule.min_front_m || 'condicionado') + ', indice ' + (row.rule.construction_index || 'pendiente de cabida') + '. Maximo construible orientativo: ' + (max || 'pendiente') + ' m²; potencial frente a construccion actual: ' + (pot || 'pendiente') + ' m². Area libre / condicion: ' + (row.rule.free_area || 'sin nota');
        },
        missing() { const m=[]; if (this.n(this.land)===null) m.push('area de terreno'); if (this.n(this.front)===null) m.push('frente'); if (this.n(this.actual)===null) m.push('construccion actual'); return m.join(', ') }
    }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Mayor y mejor uso</p>
            <h2 class="mt-2 text-2xl font-semibold">Matriz POT principal y compatible</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">La matriz toma los usos <strong>principal</strong> y <strong>compatible</strong> cargados desde MIDAS o el cuadro POT. Complementarios, restringidos y prohibidos quedan como soporte, no como potencial adoptable automático.</p>
        </div>
        <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">Decreto 0977 de 2001</span>
    </div>
    <div class="mt-6 grid gap-3 md:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase text-slate-600">Área terreno</p><p class="mt-1 text-xl font-semibold" x-text="land || 'Pendiente'"></p></div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase text-slate-600">Área base cálculo</p><p class="mt-1 text-xl font-semibold" x-text="f(baseArea()) || 'Pendiente'"></p></div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase text-slate-600">Frente</p><p class="mt-1 text-xl font-semibold" x-text="front || 'Pendiente'"></p></div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase text-slate-600">Construcción actual</p><p class="mt-1 text-xl font-semibold" x-text="actual || 'Pendiente'"></p></div>
    </div>
    <p class="mt-3 rounded-xl bg-amber-50 p-3 text-sm text-amber-900" x-show="missing()">Para que la matriz cierre falta: <strong x-text="missing()"></strong>. Corrige esos datos en el módulo 3.</p>
    <div class="mt-6 overflow-x-auto rounded-xl border border-slate-200" x-show="hasRows()">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-600">
                <tr><th class="px-3 py-2">Uso</th><th class="px-3 py-2">Cuadro / opción</th><th class="px-3 py-2">Área mínima</th><th class="px-3 py-2">Frente</th><th class="px-3 py-2">Cumple</th><th class="px-3 py-2">Índice</th><th class="px-3 py-2">Altura / área libre</th><th class="px-3 py-2">Máx. construible</th><th class="px-3 py-2">Potencial</th><th class="px-3 py-2">Área vendible ref.</th><th class="px-3 py-2">Acción</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <template x-for="row in rows()" :key="row.route.type + '-' + row.route.slug + '-' + row.key">
                    <tr>
                        <td class="px-3 py-2"><span class="rounded-full px-2 py-1 text-xs font-semibold" :class="row.route.type === 'principal' ? 'bg-teal-50 text-teal-800' : 'bg-blue-50 text-blue-800'" x-text="typeLabel(row.route.type)"></span></td>
                        <td class="px-3 py-2"><strong x-text="row.route.label"></strong><br><span class="text-slate-600" x-text="row.route.table + ' · ' + row.rule.label"></span></td>
                        <td class="px-3 py-2" x-text="row.rule.min_area_m2 ? row.rule.min_area_m2 + ' m²' : 'Condicionado'"></td>
                        <td class="px-3 py-2" x-text="row.rule.min_front_m ? row.rule.min_front_m + ' m' : 'Condicionado'"></td>
                        <td class="px-3 py-2"><span class="rounded-full px-2 py-1 text-xs font-semibold" :class="rowClass(row)" x-text="rowStatus(row)"></span></td>
                        <td class="px-3 py-2" x-text="row.rule.construction_index || 'Cabida'"></td>
                        <td class="px-3 py-2 max-w-xs text-xs leading-5 text-slate-700"><span x-text="row.rule.height || ''"></span><br><span x-text="row.rule.free_area || ''"></span></td>
                        <td class="px-3 py-2 font-semibold" x-text="f(maxBuild(row)) || 'Pendiente'"></td>
                        <td class="px-3 py-2 font-semibold text-teal-800" x-text="f(potential(row)) || 'Pendiente'"></td>
                        <td class="px-3 py-2" x-text="f(sellable(row)) || 'Pendiente'"></td>
                        <td class="px-3 py-2"><button class="btn-secondary" type="button" @click="adopt(row)">Adoptar / comentar</button></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-950" x-show="!hasRows()">
        <p class="font-semibold">Aún no hay principal o compatible listo para matriz.</p>
        <p class="mt-1">Carga MIDAS Uso Suelo o aplica una categoría del Decreto 0977 en 5.2. La matriz no usa complementarios, restringidos ni prohibidos para calcular potencial.</p>
    </div>
    <div class="mt-6 grid gap-4 md:grid-cols-3">
        <input type="hidden" name="adopted_normative_route" x-model="adopted">
        <label class="label md:col-span-2">Opción adoptada o comentada <?= $scenarioTip('Se llena con el botón de la matriz. Puedes ajustar el texto si el soporte dice otra cosa.') ?><input class="input" name="adopted_normative_route_label" x-model="adoptedLabel" maxlength="160" placeholder="Selecciona una fila de la matriz"></label>
        <label class="label">Factor vendible ref. <?= $scenarioTip('Opcional. Si lo diligencias en 5.2, aquí estima área vendible de referencia.') ?><input class="input bg-slate-50" type="text" :value="factor || 'Pendiente'" readonly></label>
        <div class="md:col-span-3"><label class="label">Lectura pericial de mayor y mejor uso<textarea class="input min-h-32" name="highest_best_use_reason" x-model="reason" rows="4" maxlength="5000" placeholder="Adopta, limita o descarta la opción según área, frente, restricciones, mercado y soporte normativo."></textarea></label></div>
    </div>
    <details class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <summary class="cursor-pointer font-semibold text-slate-900">Registro manual avanzado de otros usos</summary>
        <p class="mt-2 text-sm leading-6 text-slate-600">Úsalo para dejar una salvedad o una cabida técnica externa. La matriz automática solo propone escenarios desde principal y compatible.</p>
        <textarea class="input mt-4 min-h-24" name="normative_scenarios[otro][observations]" rows="3" maxlength="5000" placeholder="Ej. concepto de Planeación, cabida arquitectónica, licencia o instrumento especial."><?= e($scenarioValue('otro', 'observations')) ?></textarea>
    </details>
</section>
