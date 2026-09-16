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
            <div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="activeSurface === '<?= e($unit['id']) ?>'">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-base font-semibold"><?= e($unit['label'] ?: $surfaceUnitLabel($unit)) ?></h3>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        <?= e($unit['igac_typology_hint'] ?: 'Tipología pendiente') ?>
                    </span>
                </div>
                <div class="mt-5 grid gap-5 md:grid-cols-3">
                    <label class="label">Área de terreno o lote (m²)
                        <input class="input" name="unit_surfaces[<?= e($unit['id']) ?>][area_land_m2]"
                            inputmode="decimal" value="<?= e($surfaceValue($unit, 'area_land_m2')) ?>"
                            placeholder="Ej. 120,50">
                    </label>
                    <label class="label">Área construida (m²)
                        <input class="input" name="unit_surfaces[<?= e($unit['id']) ?>][area_built_m2]"
                            inputmode="decimal" value="<?= e($surfaceValue($unit, 'area_built_m2')) ?>"
                            placeholder="Ej. 85,20">
                    </label>
                    <label class="label">Área privada o útil (m²)
                        <input class="input" name="unit_surfaces[<?= e($unit['id']) ?>][area_private_m2]"
                            inputmode="decimal" value="<?= e($surfaceValue($unit, 'area_private_m2')) ?>"
                            placeholder="Ej. 78,00">
                    </label>
                    <label class="label">Área común asignada (m²)
                        <input class="input" name="unit_surfaces[<?= e($unit['id']) ?>][area_common_m2]"
                            inputmode="decimal" value="<?= e($surfaceValue($unit, 'area_common_m2')) ?>"
                            placeholder="Ej. 12,00">
                    </label>
                    <label class="label">Frente (m)
                        <input class="input" name="unit_surfaces[<?= e($unit['id']) ?>][front_length_m]"
                            inputmode="decimal" value="<?= e($surfaceValue($unit, 'front_length_m')) ?>"
                            placeholder="Ej. 7,50">
                    </label>
                    <label class="label">Fondo (m)
                        <input class="input" name="unit_surfaces[<?= e($unit['id']) ?>][depth_length_m]"
                            inputmode="decimal" value="<?= e($surfaceValue($unit, 'depth_length_m')) ?>"
                            placeholder="Ej. 16,00">
                    </label>
                    <label class="label md:col-span-3">Fuente de las superficies
                        <select class="input" name="unit_surfaces[<?= e($unit['id']) ?>][surface_source]">
                            <?php foreach ($surfaceSources as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= $surfaceValue($unit, 'surface_source') === $value ? 'selected' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="label md:col-span-3">Observaciones de superficie
                        <textarea class="input" name="unit_surfaces[<?= e($unit['id']) ?>][surface_notes]" rows="3" maxlength="1000"
                            placeholder="Aclara si el área fue tomada de documentos, visita, planos, PH o si requiere verificación."><?= e($surfaceValue($unit, 'surface_notes')) ?></textarea>
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
