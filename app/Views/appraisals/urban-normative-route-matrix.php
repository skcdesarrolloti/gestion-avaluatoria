<?php
$routeTabLabel = static function (array $route): string {
    $type = match ((string) ($route['type'] ?? '')) {
        'principal' => 'Principal',
        'complementario' => 'Complementario',
        default => 'Compatible',
    };
    return $type . ' · ' . (string) ($route['label'] ?? '');
};
$matrixRoutes = $potentialRoutes ?? [];
?>
<section class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase text-teal-800">Matriz normativa por uso</p>
            <h3 class="mt-1 font-semibold text-slate-950">Factores y parámetros normativos en formato de hoja</h3>
            <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-600">
                Se toma lo que MIDAS dejó como <strong>principal</strong>, <strong>compatible</strong> y <strong>complementario</strong>. Restringido y prohibido quedan como soporte o alerta. La cabida y los cálculos se dejan para el módulo 8.
            </p>
        </div>
        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-700" x-text="isLot ? 'Lectura de edificabilidad' : 'Soporte para inmueble construido'"></span>
    </div>
    <?php if ($matrixRoutes !== []): ?>
        <nav class="mt-4 flex gap-2 overflow-x-auto rounded-lg bg-white p-2" aria-label="Pestañas de matriz normativa">
            <button class="inline-flex min-h-10 shrink-0 items-center rounded-lg px-3 py-2 text-xs font-semibold" type="button"
                :class="routeMatrixTab === 'resumen' ? 'bg-teal-700 text-white' : 'bg-slate-100 text-teal-800 hover:bg-teal-50'"
                @click="routeMatrixTab = 'resumen'">Resumen</button>
            <?php foreach ($matrixRoutes as $route): $routeKey = ($route['type'] ?? '') . ':' . ($route['slug'] ?? ''); ?>
                <button class="inline-flex min-h-10 shrink-0 items-center rounded-lg px-3 py-2 text-xs font-semibold" type="button"
                    :class="routeMatrixTab === '<?= e($routeKey) ?>' ? 'bg-teal-700 text-white' : 'bg-slate-100 text-teal-800 hover:bg-teal-50'"
                    @click="routeMatrixTab = '<?= e($routeKey) ?>'"><?= e($routeTabLabel($route)) ?></button>
            <?php endforeach; ?>
        </nav>
        <div x-show="routeMatrixTab === 'resumen'" class="mt-4 overflow-x-auto rounded-lg border border-slate-200 bg-white">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-100 text-left text-xs uppercase text-slate-600">
                    <tr>
                        <th class="px-3 py-2">Uso</th>
                        <th class="px-3 py-2">Opción normativa</th>
                        <th class="px-3 py-2">Base exigida</th>
                        <th class="px-3 py-2">Dato del predio</th>
                        <th class="px-3 py-2">Parámetros</th>
                        <th class="px-3 py-2">Estado</th>
                        <th class="px-3 py-2">Lectura para seleccionar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="row in routeOptions()" :key="row.route.type + ':' + row.route.slug + ':' + row.key">
                        <tr>
                            <td class="px-3 py-2"><span class="rounded-full px-2 py-1 text-xs font-semibold" :class="routeTypeClass(row.route.type)" x-text="typeLabel(row.route.type)"></span></td>
                            <td class="px-3 py-2">
                                <strong x-text="row.route.label"></strong><br>
                                <span class="text-xs text-slate-600" x-text="row.route.table + ' · ' + row.rule.label"></span>
                            </td>
                            <td class="px-3 py-2 text-xs leading-5 text-slate-700">
                                <span x-text="'AML: ' + (row.rule.min_area_m2 ? row.rule.min_area_m2 + ' m²' : 'manual')"></span><br>
                                <span x-text="'Frente: ' + (row.rule.min_front_m ? row.rule.min_front_m + ' m' : 'manual')"></span><br>
                                <span x-text="'IC: ' + (row.rule.construction_index || 'manual')"></span>
                            </td>
                            <td class="px-3 py-2 text-xs leading-5 text-slate-700">
                                <span x-text="'Área: ' + (land || 'pendiente')"></span><br>
                                <span x-text="'Frente: ' + (front || 'pendiente')"></span><br>
                                <span x-text="'Construido: ' + (actual || 'pendiente')"></span>
                            </td>
                            <td class="px-3 py-2 text-xs font-semibold leading-5 text-slate-900" x-text="isLot ? routeCalcSummary(row) : 'No obligatorio para inmueble construido; dejar como soporte normativo.'"></td>
                            <td class="px-3 py-2"><span class="rounded-full px-2 py-1 text-xs font-semibold" :class="routeStatusClass(row)" x-text="routeStatus(row)"></span></td>
                            <td class="max-w-sm px-3 py-2 text-xs leading-5 text-slate-700" x-text="routeSelectionText(row)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <?php foreach ($matrixRoutes as $route): $routeKey = ($route['type'] ?? '') . ':' . ($route['slug'] ?? ''); $routeJson = e(json_encode($route, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}'); ?>
            <div x-show="routeMatrixTab === '<?= e($routeKey) ?>'" class="mt-4 space-y-4" x-data="{ route: <?= $routeJson ?> }">
                <div class="rounded-lg border border-blue-100 bg-blue-50 p-3 text-sm leading-6 text-blue-950">
                    <strong x-text="typeLabel(route.type) + ': ' + route.label"></strong>
                    <span x-text="' · ' + route.table"></span>
                </div>
                <template x-for="row in currentRouteOptions(route)" :key="'decision-' + row.key">
                    <div class="rounded-lg border p-3 text-sm font-semibold leading-6" :class="routeDecisionClass(row)">
                        <span x-text="routeSelectionText(row)"></span>
                    </div>
                </template>
                <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-100 text-left text-xs uppercase text-slate-600">
                            <tr>
                                <th class="px-3 py-2">Factor</th>
                                <th class="px-3 py-2">Lo que dice la norma</th>
                                <th class="px-3 py-2">Dato / revisión</th>
                                <th class="px-3 py-2">Alcance en numeral 5</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="row in currentRouteOptions(route)" :key="row.key">
                                <template x-for="factor in [
                                    ['Opción del cuadro', row.rule.label, row.route.table, typeLabel(row.route.type)],
                                    ['Área mínima de lote', row.rule.min_area_m2 ? row.rule.min_area_m2 + ' m²' : 'No expresa mínimo automático', land || 'Pendiente', routeStatus(row)],
                                    ['Frente mínimo', row.rule.min_front_m ? row.rule.min_front_m + ' m' : 'No expresa mínimo automático', front || 'Pendiente', routeStatus(row)],
                                    ['Retiros, aislamientos y área libre', row.rule.isolation || row.rule.free_area || 'Manual según cuadro', 'Ver soporte MIDAS/POT', 'Condición de edificabilidad; cálculo en módulo 8'],
                                    ['Índice de ocupación', row.rule.occupancy_index || 'No expresa índice directo', occ || 'Pendiente/manual', 'Dato normativo; no se calcula aquí'],
                                    ['Índice de construcción', row.rule.construction_index || 'No expresa índice directo', ci || 'Pendiente/manual', 'Dato normativo; cabida en módulo 8'],
                                    ['Altura', row.rule.height || 'Manual', floors || 'Pendiente/manual', 'Revisar si depende de vía, frente o autoridad'],
                                    ['Área libre', row.rule.free_area || 'Manual', 'Ver soporte MIDAS/POT', 'Condición descriptiva de ocupación'],
                                    ['Aislamientos', row.rule.isolation || 'Manual', 'Ver soporte MIDAS/POT', 'Puede limitar la factibilidad'],
                                    ['Estacionamientos', row.rule.parking || 'Manual', 'Ver producto y áreas', 'Puede incidir en cabida del módulo 8'],
                                    ['Decisión de selección', routeStatus(row), typeLabel(row.route.type), routeSelectionText(row)],
                                    ['Alcance y salvedad', row.rule.scope || 'Sin nota adicional', 'Criterio del analista', 'Documentar si falta soporte']
                                ]">
                                    <tr>
                                        <td class="w-52 px-3 py-2 font-semibold text-slate-950" x-text="factor[0]"></td>
                                        <td class="px-3 py-2 leading-6 text-slate-800" x-text="factor[1]"></td>
                                        <td class="px-3 py-2 leading-6 text-slate-700" x-text="factor[2]"></td>
                                        <td class="px-3 py-2 leading-6 text-slate-700" x-text="factor[3]"></td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="mt-4 rounded-lg bg-amber-50 p-3 text-sm font-semibold leading-6 text-amber-900">
            Aún no hay uso principal, compatible o complementario reconocido. Procesa la lectura de MIDAS desde el numeral 2 o adopta un cuadro para que aparezcan las pestañas por uso.
        </p>
    <?php endif; ?>
</section>
