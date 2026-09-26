<?php
$selectValue = static fn (string $key, string $value): string => $cv($unit, $key) === $value ? 'selected' : '';
$serviceOptions = ['' => 'No verificado', 'no_aplica' => 'No aplica', 'alcoba' => 'Alcoba de servicio',
    'bano' => 'Baño de servicio', 'alcoba_bano' => 'Alcoba y baño de servicio'];
$accessOptions = ['' => 'No verificado', 'peatonal' => 'Peatonal', 'vehicular' => 'Vehicular',
    'mixto' => 'Mixto', 'cargue' => 'Cargue y descargue', 'restringido' => 'Restringido'];
$viewOptions = ['' => 'No verificado', 'interior' => 'Interior', 'exterior' => 'Exterior',
    'panoramica' => 'Panorámica', 'esquinera' => 'Esquinera', 'sin_vista' => 'Sin vista relevante'];
$finishOptions = ['' => 'No verificado', 'basico' => 'Básico / económico', 'medio' => 'Medio',
    'bueno' => 'Bueno', 'alto' => 'Alto', 'lujo' => 'Superior / lujo', 'obra_gris' => 'Obra gris'];
?>
<div class="mt-5 space-y-5" x-show="activeConstructionDetail === 'funcionales'">
    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
        Estas variables complementan la tipología para buscar comparables equivalentes.
        Diligencia solo las que apliquen según el tipo de inmueble y deja las demás vacías o en N/A.
    </div>
    <div class="grid gap-5 md:grid-cols-3">
        <label class="label">Habitaciones
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][functional_bedrooms_count]"
                inputmode="numeric" value="<?= e($cv($unit, 'functional_bedrooms_count')) ?>" placeholder="Ej. 3">
            <span class="mt-1 block text-xs text-slate-500">Aplica principalmente a vivienda, hospedaje o unidades habitacionales.</span>
        </label>
        <label class="label">Baños
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][functional_bathrooms_count]"
                inputmode="decimal" value="<?= e($cv($unit, 'functional_bathrooms_count')) ?>" placeholder="Ej. 2 o 2,5">
        </label>
        <label class="label">Alcoba / baño de servicio
            <select class="input" name="unit_constructions[<?= e($unitId) ?>][functional_service_room_bathroom]">
                <?php foreach ($serviceOptions as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $selectValue('functional_service_room_bathroom', $value) ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label">Celdas de parqueo
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][functional_parking_spaces_count]"
                inputmode="numeric" value="<?= e($cv($unit, 'functional_parking_spaces_count')) ?>" placeholder="Ej. 1">
        </label>
        <label class="label">Muelles / puntos de cargue
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][functional_loading_bays_count]"
                inputmode="numeric" value="<?= e($cv($unit, 'functional_loading_bays_count')) ?>" placeholder="Ej. 2">
            <span class="mt-1 block text-xs text-slate-500">Útil en bodegas, industria, locales grandes o logística.</span>
        </label>
        <label class="label">Altura libre (m)
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][functional_clear_height_m]"
                inputmode="decimal" value="<?= e($cv($unit, 'functional_clear_height_m')) ?>" placeholder="Ej. 4,50">
        </label>
        <label class="label">Área de oficina / apoyo (m²)
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][functional_office_area_m2]"
                inputmode="decimal" value="<?= e($cv($unit, 'functional_office_area_m2')) ?>" placeholder="Ej. 25">
        </label>
        <label class="label">Tipo de acceso
            <select class="input" name="unit_constructions[<?= e($unitId) ?>][functional_access_type]">
                <?php foreach ($accessOptions as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $selectValue('functional_access_type', $value) ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label">Vista del inmueble
            <select class="input" name="unit_constructions[<?= e($unitId) ?>][functional_view]">
                <?php foreach ($viewOptions as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $selectValue('functional_view', $value) ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label">Acabados del inmueble
            <select class="input" name="unit_constructions[<?= e($unitId) ?>][functional_finish_quality]">
                <?php foreach ($finishOptions as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $selectValue('functional_finish_quality', $value) ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label md:col-span-3">Notas de variables funcionales
            <textarea class="input" name="unit_constructions[<?= e($unitId) ?>][functional_notes]" rows="3"
                placeholder="Aclara variables no comparables, datos pendientes o equivalencias por tipología."><?= e($cv($unit, 'functional_notes')) ?></textarea>
        </label>
    </div>
</div>
