<?php
$surfaceUnitLabel = static function (array $unit): string {
    return ($unit['unit_kind'] === 'annex' ? 'Anexo ' : 'Unidad ') . (int) $unit['unit_index'];
};
$surfaceUnits = array_values(array_filter($units, static fn (array $unit): bool => $unit['unit_kind'] !== 'common'));
$surfaceValue = static fn (array $unit, string $key): string => (string) ($unit[$key] ?? '');
$surfaceSources = [
    '' => 'Selecciona fuente',
    'visita' => 'Medición en visita',
    'certificado_catastral' => 'Certificado catastral',
    'escritura' => 'Escritura pública',
    'plano' => 'Plano arquitectónico',
    'licencia' => 'Licencia urbanística',
    'reglamento_ph' => 'Reglamento PH',
    'levantamiento' => 'Levantamiento topográfico',
    'otro' => 'Otro soporte',
];
$shapeOptions = ['' => 'Selecciona forma', 'regular' => 'Regular', 'irregular' => 'Irregular',
    'esquinero' => 'Esquinero', 'medianero' => 'Medianero', 'otro' => 'Otro'];
$topographyOptions = ['' => 'Selecciona topografía', 'plana' => 'Plana', 'ondulada' => 'Ondulada',
    'inclinada' => 'Inclinada', 'quebrada' => 'Quebrada', 'mixta' => 'Mixta'];
$enclosureOptions = ['' => 'Selecciona cerramiento', 'sin_cerramiento' => 'Sin cerramiento',
    'parcial' => 'Parcial', 'total' => 'Total', 'no_verificado' => 'No verificado'];
$surfaceTabs = [
    'fisicas' => ['1', 'Características físicas del lote'],
    'superficie' => ['2', 'Superficie del terreno'],
    'fondo' => ['3', 'Fondo equivalente'],
    'relacion' => ['4', 'Relación frente-fondo'],
    'dinamicas' => ['5', 'Variables dinámicas'],
    'informe' => ['6', 'Informe para el entregable'],
];
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{ activeSurface: '<?= e($surfaceUnits[0]['id'] ?? '') ?>', busySurface: false }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">2.2 Datos de la superficie</p>
            <h2 class="mt-2 text-2xl font-semibold">Superficies por unidad del predio</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Captura las áreas y medidas por cada unidad o anexo definido en Tipologías IGAC.
                Estos datos quedan separados para soportar reposición, comparaciones y descripción técnica.
            </p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
            <?= count($surfaceUnits) ?> unidad(es)
        </span>
    </div>
    <form class="mt-6" method="post" action="<?= e(url($subjectActionBase . '/superficies')) ?>"
        @submit="busySurface = true">
        <?= csrf_field() ?>
        <?php if (!$surfaceUnits): ?>
            <p class="rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-600">
                Primero define cuántas unidades o anexos tiene el predio en la subpestaña Tipologías IGAC.
            </p>
        <?php else: ?>
            <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
                <?php foreach ($surfaceUnits as $unit): ?>
                    <button class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                        type="button" @click="activeSurface = '<?= e($unit['id']) ?>'"
                        :class="activeSurface === '<?= e($unit['id']) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                        <?= e($unit['label'] ?: $surfaceUnitLabel($unit)) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php foreach ($surfaceUnits as $unit): ?>
            <div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="activeSurface === '<?= e($unit['id']) ?>'"
                x-data="{ activeSurfaceDetail: 'fisicas' }">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-base font-semibold"><?= e($unit['label'] ?: $surfaceUnitLabel($unit)) ?></h3>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        <?= e($unit['igac_typology_hint'] ?: 'Tipología pendiente') ?>
                    </span>
                </div>
                <div class="mt-5 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2">
                    <?php foreach ($surfaceTabs as $key => [$number, $label]): ?>
                        <button class="min-h-10 shrink-0 rounded-lg px-3 py-2 text-xs font-semibold" type="button"
                            @click="activeSurfaceDetail = '<?= e($key) ?>'"
                            :class="activeSurfaceDetail === '<?= e($key) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                            <span class="mr-2 rounded-full bg-blue-700 px-2 py-0.5 text-white"><?= e($number) ?></span><?= e($label) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <div class="mt-5 grid gap-5 md:grid-cols-3" x-show="activeSurfaceDetail === 'fisicas'">
                    <label class="label">Forma del lote
                        <select class="input" name="unit_surfaces[<?= e($unit['id']) ?>][lot_shape]">
                            <?php foreach ($shapeOptions as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= $surfaceValue($unit, 'lot_shape') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="label">Topografía
                        <select class="input" name="unit_surfaces[<?= e($unit['id']) ?>][topography]">
                            <?php foreach ($topographyOptions as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= $surfaceValue($unit, 'topography') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="label">Cerramiento
                        <select class="input" name="unit_surfaces[<?= e($unit['id']) ?>][enclosure]">
                            <?php foreach ($enclosureOptions as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= $surfaceValue($unit, 'enclosure') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="label md:col-span-3">Linderos, forma y observaciones físicas
                        <textarea class="input" name="unit_surfaces[<?= e($unit['id']) ?>][boundaries]" rows="3" maxlength="1000"
                            placeholder="Describe linderos, irregularidades, topografía observada o restricciones físicas."><?= e($surfaceValue($unit, 'boundaries')) ?></textarea>
                    </label>
                </div>
                <div class="mt-5 grid gap-5 md:grid-cols-3" x-show="activeSurfaceDetail === 'superficie'">
                    <?php foreach ([['area_land_m2', 'Área de terreno o lote (m²)', '120,50'],
                        ['area_built_m2', 'Área construida (m²)', '85,20'], ['area_private_m2', 'Área privada o útil (m²)', '78,00'],
                        ['area_common_m2', 'Área común asignada (m²)', '12,00']] as [$key, $label, $placeholder]): ?>
                        <label class="label"><?= e($label) ?>
                            <input class="input" name="unit_surfaces[<?= e($unit['id']) ?>][<?= e($key) ?>]"
                                inputmode="decimal" value="<?= e($surfaceValue($unit, $key)) ?>" placeholder="Ej. <?= e($placeholder) ?>">
                        </label>
                    <?php endforeach; ?>
                    <label class="label md:col-span-2">Fuente de las superficies
                        <select class="input" name="unit_surfaces[<?= e($unit['id']) ?>][surface_source]">
                            <?php foreach ($surfaceSources as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= $surfaceValue($unit, 'surface_source') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
                <div class="mt-5 grid gap-5 md:grid-cols-2" x-show="activeSurfaceDetail === 'fondo'">
                    <?php foreach ([['front_length_m', 'Frente (m)', '7,50'], ['depth_length_m', 'Fondo (m)', '16,00'],
                        ['equivalent_depth_m', 'Fondo equivalente (m)', '14,20']] as [$key, $label, $placeholder]): ?>
                        <label class="label"><?= e($label) ?>
                            <input class="input" name="unit_surfaces[<?= e($unit['id']) ?>][<?= e($key) ?>]"
                                inputmode="decimal" value="<?= e($surfaceValue($unit, $key)) ?>" placeholder="Ej. <?= e($placeholder) ?>">
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="mt-5 grid gap-5 md:grid-cols-2" x-show="activeSurfaceDetail === 'relacion'">
                    <label class="label">Relación frente-fondo
                        <input class="input" name="unit_surfaces[<?= e($unit['id']) ?>][front_depth_ratio]"
                            inputmode="decimal" value="<?= e($surfaceValue($unit, 'front_depth_ratio')) ?>" placeholder="Ej. 0,47">
                    </label>
                    <p class="rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">
                        Se deja editable hasta confirmar la fórmula técnica; debe ser coherente con frente, fondo y forma del lote.
                    </p>
                </div>
                <label class="label mt-5 block" x-show="activeSurfaceDetail === 'dinamicas'">Variables dinámicas del terreno
                    <textarea class="input" name="unit_surfaces[<?= e($unit['id']) ?>][dynamic_surface_notes]" rows="4" maxlength="1000"
                        placeholder="Anota variables que cambian la lectura del área: servidumbres, retiros, ocupación, afectaciones o pendientes de verificación."><?= e($surfaceValue($unit, 'dynamic_surface_notes')) ?></textarea>
                </label>
                <div class="mt-5 grid gap-5" x-show="activeSurfaceDetail === 'informe'">
                    <label class="label">Observaciones de superficie
                        <textarea class="input" name="unit_surfaces[<?= e($unit['id']) ?>][surface_notes]" rows="3" maxlength="1000"
                            placeholder="Aclara si el área fue tomada de documentos, visita, planos, PH o si requiere verificación."><?= e($surfaceValue($unit, 'surface_notes')) ?></textarea>
                    </label>
                    <label class="label">Texto editable para el entregable
                        <textarea class="input" name="unit_surfaces[<?= e($unit['id']) ?>][surface_report_text]" rows="4" maxlength="1500"
                            placeholder="Redacción que podrá alimentar la sección de superficies del informe."><?= e($surfaceValue($unit, 'surface_report_text')) ?></textarea>
                    </label>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if ($surfaceUnits): ?>
            <div class="mt-5 flex justify-end">
                <button class="btn-primary" type="submit" :disabled="busySurface"
                    x-text="busySurface ? 'Guardando...' : 'Guardar superficies'">Guardar superficies</button>
            </div>
        <?php endif; ?>
    </form>
</section>
