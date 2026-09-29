<?php
$portalFields = is_array($guide['portal_fields'] ?? null) ? $guide['portal_fields'] : [];
$portalFilters = is_array($guide['portal_filters'] ?? null) ? $guide['portal_filters'] : [];
$nextStep = is_array($methodologyDecision['next_step'] ?? null) ? $methodologyDecision['next_step'] : ['8.3 Desarrollo del método', ''];
$formulaFamilies = [
    ['Mercado', 'Valor unitario = precio depurado / unidad de comparación; luego promedio, mediana, desviación, coeficiente de variación, rango y ajustes de homologación.'],
    ['Renta', 'Ingreso neto = canon bruto menos vacancia, administración no recuperable y gastos; valor = ingreso neto anual / tasa, o flujo descontado si aplica.'],
    ['Residual', 'Valor del suelo = ingresos esperados del producto menos costos directos, indirectos, financieros, utilidad, tiempos y riesgos del desarrollo.'],
    ['Costo', 'Valor = terreno + costo de reposición nuevo menos depreciación física, funcional y económica, con soporte de cantidades y precios.'],
];
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="mb-6">
        <p class="eyebrow">8.3 Desarrollo operativo del método</p>
        <h2 class="mt-2 text-2xl font-semibold"><?= e((string) ($nextStep[0] ?? 'Búsqueda y preparación técnica')) ?></h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
            Este bloque inicia después de decidir el método en 8.2. Si el método es mercado, prepara comparables;
            si es renta, residual o costo, prepara los insumos y fórmulas propias antes del análisis.
        </p>
    </div>
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Activo sujeto</p>
            <h3 class="mt-2 text-xl font-semibold"><?= e($guide['type_label'] ?? 'Tipología pendiente') ?></h3>
            <p class="mt-2 text-sm leading-6 text-slate-600"><?= e(trim((string) ($record['titulo'] ?? '')) ?: 'Ficha sin título definido') ?></p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Mercado comparable</p>
            <h3 class="mt-2 text-xl font-semibold"><?= e($guide['business_label'] ?: 'Tipo de negocio pendiente') ?></h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">La búsqueda debe conservar la misma operación: venta con venta, arriendo con arriendo.</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="eyebrow">Derecho valorado</p>
            <h3 class="mt-2 text-xl font-semibold"><?= e($guide['right_label'] ?: 'Derecho pendiente') ?></h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Si cambia el derecho jurídico, la muestra requiere observación antes de homologarse.</p>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-950">
        <strong>Regla de flujo:</strong>
        <?= e((string) ($nextStep[1] ?? 'Define primero los insumos del método seleccionado.')) ?>
        No se deben mezclar comparables, rentas, costos o residuales sin dejar claro cuál es el método principal
        y cuál opera solo como contraste.
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
            <p class="text-xs font-bold uppercase text-blue-800">Filtros iniciales</p>
            <?php if ($portalFilters === []): ?>
                <p class="mt-3 text-sm text-blue-950">Completa el expediente y el bien sujeto para formar los filtros de portal.</p>
            <?php else: ?>
                <dl class="mt-3 grid gap-2 text-sm">
                    <?php foreach ($portalFilters as $filter): ?>
                        <div class="rounded-lg bg-white/80 p-3">
                            <dt class="text-xs font-semibold uppercase text-blue-700"><?= e((string) ($filter['label'] ?? 'Filtro')) ?></dt>
                            <dd class="mt-1 font-semibold text-slate-950"><?= e((string) ($filter['value'] ?? '')) ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            <?php endif; ?>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-500">Campos que conviene capturar de cada portal</p>
            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                <?php foreach ($portalFields as $field): ?>
                    <span class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-700"><?= e($field) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php if ($factorGroups !== []): ?>
        <div class="mt-6">
            <p class="eyebrow">Matriz por tipología</p>
            <h3 class="mt-2 text-2xl font-semibold">Características que deben parecerse</h3>
            <div class="mt-4 grid gap-3 lg:grid-cols-4">
                <?php foreach ($factorGroups as $group => $items): ?>
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase text-slate-500"><?= e($group) ?></p>
                        <ul class="mt-2 space-y-1 text-sm leading-5 text-slate-700">
                            <?php foreach ($items as $item): ?><li>- <?= e($item) ?></li><?php endforeach; ?>
                        </ul>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <p class="text-xs font-bold uppercase text-slate-500">Fórmulas a confirmar antes de automatizar</p>
        <div class="mt-3 grid gap-3 lg:grid-cols-4">
            <?php foreach ($formulaFamilies as $family): ?>
                <article class="rounded-lg border border-slate-200 bg-white p-3 text-sm leading-5">
                    <h3 class="font-semibold text-slate-950"><?= e($family[0]) ?></h3>
                    <p class="mt-2 text-slate-600"><?= e($family[1]) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="mt-3 text-xs leading-5 text-slate-500">
            Estas fórmulas son el mapa de trabajo. La regla exacta, factores de ajuste y umbrales estadísticos
            deben aprobarse antes de que el sistema calcule valores de forma automática.
        </p>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-xl border border-slate-200 bg-white">
            <div class="rounded-t-xl bg-blue-900 px-4 py-3 text-sm font-semibold uppercase text-white">Criterios de búsqueda</div>
            <ol class="space-y-3 p-4 text-sm leading-6 text-slate-700">
                <?php foreach (($guide['criteria'] ?? []) as $index => $criterion): ?>
                    <li class="flex gap-3"><span class="mt-1 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-800"><?= e((string) ($index + 1)) ?></span><span><?= e($criterion) ?></span></li>
                <?php endforeach; ?>
            </ol>
        </div>
        <div class="space-y-6">
            <div class="rounded-xl border border-amber-200 bg-amber-50">
                <div class="rounded-t-xl bg-amber-600 px-4 py-3 text-sm font-semibold uppercase text-white">Evitar</div>
                <ul class="space-y-3 p-4 text-sm leading-6 text-amber-950">
                    <?php foreach (($guide['avoid'] ?? []) as $item): ?><li>- <?= e($item) ?></li><?php endforeach; ?>
                </ul>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50">
                <div class="rounded-t-xl bg-emerald-700 px-4 py-3 text-sm font-semibold uppercase text-white">Homologación posterior</div>
                <ul class="space-y-3 p-4 text-sm leading-6 text-emerald-950">
                    <?php foreach (($guide['homologation'] ?? []) as $item): ?><li>- <?= e($item) ?></li><?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>
