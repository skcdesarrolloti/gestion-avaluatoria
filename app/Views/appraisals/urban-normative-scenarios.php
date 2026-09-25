<?php
$scenarioValue = static fn (string $route, string $field): string => (string) ($normativeScenarios[$route][$field] ?? '');
$scenarioTip = static fn (string $text): string => '<span class="help-dot" title="' . e($text) . '">?</span>';
$residentialScenarioJson = e(json_encode(\App\Support\UrbanResidentialNormCatalog::standards(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}');
$scenarioInitial = static fn (string $key): string => e(json_encode((string) ($profile[$key] ?? ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: "''");
?>
<section id="escenarios" x-show="tab === 'escenarios'" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{
        standards: <?= $residentialScenarioJson ?>,
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
        maxBuild(rule) { const base = this.baseArea(); return base === null ? null : base * Number(rule.construction_index || 0) },
        potential(rule) { const max = this.maxBuild(rule), actual = this.n(this.actual); return max === null || actual === null ? null : Math.max(0, max - actual) },
        sellable(rule) { const max = this.maxBuild(rule), factor = this.n(this.factor); return max === null || factor === null ? null : max * factor },
        areaOk(rule) { const land = this.n(this.land); return land === null ? null : land >= Number(rule.min_area_m2 || 0) },
        frontOk(rule) { const front = this.n(this.front); return front === null ? null : front >= Number(rule.min_front_m || 0) },
        rowStatus(rule) { const a=this.areaOk(rule), f=this.frontOk(rule); if (a === null || f === null) return 'Falta dato'; return a && f ? 'Cumple base' : 'No cumple base' },
        rowClass(rule) { const s=this.rowStatus(rule); return s === 'Cumple base' ? 'bg-emerald-50 text-emerald-800' : (s === 'No cumple base' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-800') },
        adopt(typeKey, type, modeKey, rule) { this.adopted = 'residencial'; this.adoptedLabel = type.label + ' · ' + rule.label; const p = this.f(this.potential(rule)); this.reason = this.rowStatus(rule) + '. Se probó ' + type.label + ' / ' + rule.label + ' con AML ' + rule.min_area_m2 + ' m², frente mínimo ' + rule.min_front_m + ' m, índice ' + rule.construction_index + '. Máximo construible estimado: ' + this.f(this.maxBuild(rule)) + ' m²; construcción actual: ' + (this.actual || 'pendiente') + ' m²; potencial orientativo: ' + (p || 'pendiente') + ' m².' },
        missing() { const m=[]; if (this.n(this.land)===null) m.push('área de terreno'); if (this.n(this.front)===null) m.push('frente'); if (this.n(this.actual)===null) m.push('construcción actual'); return m.join(', ') }
    }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div><p class="eyebrow">Mayor y mejor uso</p><h2 class="mt-2 text-2xl font-semibold">Matriz simple de potencial constructivo</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Toma los datos del predio y prueba cada alternativa residencial del cuadro. El perito solo adopta la fila que tenga soporte normativo y físico.</p></div>
        <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">Área + frente + índice</span>
    </div>
    <div class="mt-6 grid gap-3 md:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase text-slate-600">Área terreno</p><p class="mt-1 text-xl font-semibold" x-text="land || 'Pendiente'"></p></div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase text-slate-600">Área base de cálculo</p><p class="mt-1 text-xl font-semibold" x-text="f(baseArea()) || 'Pendiente'"></p></div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase text-slate-600">Frente</p><p class="mt-1 text-xl font-semibold" x-text="front || 'Pendiente'"></p></div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase text-slate-600">Construcción actual</p><p class="mt-1 text-xl font-semibold" x-text="actual || 'Pendiente'"></p></div>
    </div>
    <p class="mt-3 rounded-xl bg-amber-50 p-3 text-sm text-amber-900" x-show="missing()">Para que la matriz cierre falta: <strong x-text="missing()"></strong>. Corrige esos datos en el módulo 3.</p>
    <div class="mt-6 overflow-x-auto rounded-xl border border-slate-200">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-600"><tr><th class="px-3 py-2">Cuadro / opción</th><th class="px-3 py-2">Área mínima</th><th class="px-3 py-2">Frente mínimo</th><th class="px-3 py-2">Cumple</th><th class="px-3 py-2">Índice</th><th class="px-3 py-2">Máx. construible</th><th class="px-3 py-2">Potencial</th><th class="px-3 py-2">Área vendible ref.</th><th class="px-3 py-2">Acción</th></tr></thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <template x-for="(type, typeKey) in standards" :key="typeKey"><template x-for="(rule, modeKey) in type.data" :key="typeKey + modeKey">
                    <tr><td class="px-3 py-2"><strong x-text="type.label"></strong><br><span class="text-slate-600" x-text="rule.label"></span></td><td class="px-3 py-2" x-text="rule.min_area_m2 + ' m²'"></td><td class="px-3 py-2" x-text="rule.min_front_m + ' m'"></td><td class="px-3 py-2"><span class="rounded-full px-2 py-1 text-xs font-semibold" :class="rowClass(rule)" x-text="rowStatus(rule)"></span></td><td class="px-3 py-2" x-text="rule.construction_index"></td><td class="px-3 py-2 font-semibold" x-text="f(maxBuild(rule))"></td><td class="px-3 py-2 font-semibold text-teal-800" x-text="f(potential(rule))"></td><td class="px-3 py-2" x-text="f(sellable(rule))"></td><td class="px-3 py-2"><button class="btn-secondary" type="button" @click="adopt(typeKey, type, modeKey, rule)">Adoptar / comentar</button></td></tr>
                </template></template>
            </tbody>
        </table>
    </div>
    <div class="mt-6 grid gap-4 md:grid-cols-3">
        <input type="hidden" name="adopted_normative_route" x-model="adopted">
        <label class="label md:col-span-2">Opción adoptada o comentada <?= $scenarioTip('Se llena con el botón de la matriz. Puedes ajustar el texto si el soporte dice otra cosa.') ?><input class="input" name="adopted_normative_route_label" x-model="adoptedLabel" maxlength="160" placeholder="Selecciona una fila de la matriz"></label>
        <label class="label">Factor vendible ref. <?= $scenarioTip('Opcional. Si lo diligencias en 5.2, aquí estima área vendible de referencia.') ?><input class="input bg-slate-50" type="text" :value="factor || 'Pendiente'" readonly></label>
        <div class="md:col-span-3"><label class="label">Lectura pericial de mayor y mejor uso<textarea class="input min-h-32" name="highest_best_use_reason" x-model="reason" rows="4" maxlength="5000" placeholder="Adopta, limita o descarta la opción según área, frente, restricciones, mercado y soporte normativo."></textarea></label></div>
    </div>
    <details class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <summary class="cursor-pointer font-semibold text-slate-900">Registro manual avanzado de otros usos</summary>
        <p class="mt-2 text-sm leading-6 text-slate-600">Úsalo después para institucional, comercial, mixto u otro cuadro cuando ya tengamos cargados sus parámetros mínimos e índices.</p>
        <textarea class="input mt-4 min-h-24" name="normative_scenarios[otro][observations]" rows="3" maxlength="5000" placeholder="Ej. probar Institucional 2 o Comercial 2 con fuente, restricciones y cálculo manual."><?= e($scenarioValue('otro', 'observations')) ?></textarea>
    </details>
</section>
