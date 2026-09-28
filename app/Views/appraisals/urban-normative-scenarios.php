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
        typeLabel(type) { return type === 'principal' ? 'Principal' : (type === 'complementario' ? 'Complementario' : 'Compatible') },
        typeClass(type) { return type === 'principal' ? 'bg-teal-50 text-teal-800' : (type === 'complementario' ? 'bg-amber-50 text-amber-800' : 'bg-blue-50 text-blue-800') },
        rows() {
            const out = [];
            for (const route of this.routes) for (const [key, rule] of Object.entries(route.options || {})) out.push({route, key, rule});
            return out;
        },
        hasRows() { return this.rows().length > 0 },
        areaOk(row) { const min = this.n(row.rule.min_area_m2), land = this.n(this.land); if (min === null) return null; return land === null ? undefined : land >= min },
        frontOk(row) { const min = this.n(row.rule.min_front_m), front = this.n(this.front); if (min === null) return null; return front === null ? undefined : front >= min },
        rowStatus(row) {
            const a=this.areaOk(row), f=this.frontOk(row);
            if (a === undefined || f === undefined) return 'Falta dato';
            if (a === null && f === null) return 'Revisión normativa';
            return (a !== false && f !== false) ? 'Cumple base' : 'No cumple';
        },
        rowClass(row) { const s=this.rowStatus(row); return s === 'Cumple base' ? 'bg-emerald-50 text-emerald-800' : (s === 'No cumple' ? 'bg-red-50 text-red-700' : (s === 'Revisión normativa' ? 'bg-blue-50 text-blue-800' : 'bg-amber-50 text-amber-800')) },
        rowFailures(row) {
            const fails=[], area=this.n(this.land), front=this.n(this.front), minArea=this.n(row.rule.min_area_m2), minFront=this.n(row.rule.min_front_m);
            if (minArea !== null && area !== null && area < minArea) fails.push('área mínima ' + minArea + ' m², predio ' + this.f(area) + ' m²');
            if (minFront !== null && front !== null && front < minFront) fails.push('frente mínimo ' + minFront + ' m, predio ' + this.f(front) + ' m');
            return fails.join(' y ');
        },
        adopt(row) {
            this.adopted = row.route.type + ':' + row.route.slug + ':' + row.key;
            this.adoptedLabel = this.typeLabel(row.route.type) + ' · ' + row.route.label + ' · ' + row.rule.label;
            const status = this.rowStatus(row);
            const extra = row.route.type === 'complementario' ? ' Al tratarse de uso complementario, exige justificar su relación funcional con el uso principal y con la factibilidad del predio.' : '';
            const base = 'Se documenta ' + this.adoptedLabel + ' (' + row.route.table + '). AML ' + (row.rule.min_area_m2 || 'condicionado') + ', frente ' + (row.rule.min_front_m || 'condicionado') + ', indice/edificabilidad ' + (row.rule.construction_index || 'pendiente o no expreso') + ', altura ' + (row.rule.height || 'segun cuadro o concepto') + ', area libre/condicion: ' + (row.rule.free_area || 'sin nota') + '.' + extra;
            this.reason = status === 'No cumple' ? 'No cumple la base normativa porque ' + (this.rowFailures(row) || 'no supera la revisión mínima') + '. ' + base + ' La cuantificación, si procede, se desarrolla en el módulo 8.' : status + '. ' + base + ' La cuantificación, si procede, se desarrolla en el módulo 8.';
        },
        missing() { const m=[]; if (this.n(this.land)===null) m.push('area de terreno'); if (this.n(this.front)===null) m.push('frente'); return m.join(', ') }
    }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Edificabilidad normativa</p>
            <h2 class="mt-2 text-2xl font-semibold">Factibilidad POT por principal, compatible y complementario</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">La matriz toma los usos <strong>principal</strong>, <strong>compatible</strong> y <strong>complementario</strong> cargados desde MIDAS o el cuadro POT. Aquí solo se documenta factibilidad urbanística; la cuantificación se desarrolla en el módulo 8.</p>
        </div>
        <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">Decreto 0977 de 2001</span>
    </div>
    <div class="mt-6 grid gap-3 md:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase text-slate-600">Área terreno</p><p class="mt-1 text-xl font-semibold" x-text="land || 'Pendiente'"></p></div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase text-slate-600">Área normativa base</p><p class="mt-1 text-xl font-semibold" x-text="f(baseArea()) || 'Pendiente'"></p></div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase text-slate-600">Frente</p><p class="mt-1 text-xl font-semibold" x-text="front || 'Pendiente'"></p></div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase text-slate-600">Alcance</p><p class="mt-1 text-sm font-semibold leading-6">Sin cálculo de cabida</p></div>
    </div>
    <p class="mt-3 rounded-xl bg-amber-50 p-3 text-sm text-amber-900" x-show="missing()">Para que la matriz cierre falta: <strong x-text="missing()"></strong>. Corrige esos datos en el módulo 3.</p>
    <div class="mt-6 overflow-x-auto rounded-xl border border-slate-200" x-show="hasRows()">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-600">
                <tr><th class="px-3 py-2">Uso</th><th class="px-3 py-2">Cuadro / opción</th><th class="px-3 py-2">Área mínima</th><th class="px-3 py-2">Frente</th><th class="px-3 py-2">Lectura</th><th class="px-3 py-2">Índice / ocupación</th><th class="px-3 py-2">Altura / área libre</th><th class="px-3 py-2">Aislamientos</th><th class="px-3 py-2">Estacionamientos</th><th class="px-3 py-2">Acción</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <template x-for="row in rows()" :key="row.route.type + '-' + row.route.slug + '-' + row.key">
                    <tr>
                        <td class="px-3 py-2"><span class="rounded-full px-2 py-1 text-xs font-semibold" :class="typeClass(row.route.type)" x-text="typeLabel(row.route.type)"></span></td>
                        <td class="px-3 py-2"><strong x-text="row.route.label"></strong><br><span class="text-slate-600" x-text="row.route.table + ' · ' + row.rule.label"></span></td>
                        <td class="px-3 py-2" x-text="row.rule.min_area_m2 ? row.rule.min_area_m2 + ' m²' : 'Condicionado'"></td>
                        <td class="px-3 py-2" x-text="row.rule.min_front_m ? row.rule.min_front_m + ' m' : 'Condicionado'"></td>
                        <td class="px-3 py-2"><span class="rounded-full px-2 py-1 text-xs font-semibold" :class="rowClass(row)" x-text="rowStatus(row)"></span></td>
                        <td class="px-3 py-2" x-text="row.rule.construction_index || row.rule.occupancy_index || 'Manual / no expreso'"></td>
                        <td class="px-3 py-2 max-w-xs text-xs leading-5 text-slate-700"><span x-text="row.rule.height || ''"></span><br><span x-text="row.rule.free_area || ''"></span></td>
                        <td class="px-3 py-2 max-w-xs text-xs leading-5 text-slate-700" x-text="row.rule.isolation || 'Manual / verificar soporte'"></td>
                        <td class="px-3 py-2 max-w-xs text-xs leading-5 text-slate-700" x-text="row.rule.parking || 'Manual / verificar soporte'"></td>
                        <td class="px-3 py-2"><button class="btn-secondary" type="button" @click="adopt(row)">Documentar</button></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-950" x-show="!hasRows()">
        <p class="font-semibold">Aún no hay principal, compatible o complementario listo para matriz.</p>
        <p class="mt-1">Carga MIDAS Uso Suelo o aplica una categoría del Decreto 0977 en 5.2. La matriz no usa restringidos ni prohibidos como factibilidad favorable.</p>
    </div>
    <div class="mt-6 grid gap-4 md:grid-cols-3">
        <input type="hidden" name="adopted_normative_route" x-model="adopted">
        <label class="label md:col-span-3">Opción documentada o comentada <?= $scenarioTip('Se llena con el botón de la matriz. Puedes ajustar el texto si el soporte dice otra cosa.') ?><input class="input" name="adopted_normative_route_label" x-model="adoptedLabel" maxlength="160" placeholder="Selecciona una fila de la matriz"></label>
        <div class="md:col-span-3"><label class="label">Lectura pericial de factibilidad normativa<textarea class="input min-h-32" name="highest_best_use_reason" x-model="reason" rows="4" maxlength="5000" placeholder="Describe si la opción es factible, condicionada o descartada por área, frente, retiros, altura, afectaciones o soporte normativo. No cuantifiques cabida aquí."></textarea></label></div>
    </div>
    <details class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <summary class="cursor-pointer font-semibold text-slate-900">Registro manual avanzado de otros usos</summary>
        <p class="mt-2 text-sm leading-6 text-slate-600">Úsalo para dejar una salvedad normativa externa. La matriz propone lecturas desde principal, compatible y complementario reconocido.</p>
        <textarea class="input mt-4 min-h-24" name="normative_scenarios[otro][observations]" rows="3" maxlength="5000" placeholder="Ej. concepto de Planeación, licencia, plan parcial, instrumento especial o limitación normativa."><?= e($scenarioValue('otro', 'observations')) ?></textarea>
    </details>
</section>
