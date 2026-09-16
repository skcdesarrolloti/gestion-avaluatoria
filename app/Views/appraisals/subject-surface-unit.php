<?php
$unitId = (string) $unit['id'];
$areaFields = [
    'area_manual_m2' => ['Manual', 'Área digitada o calculada por el analista'],
    'area_midas_m2' => ['MIDAS', 'Área tomada del visor o ficha territorial'],
    'area_tax_m2' => ['Impuesto predial', 'Área reportada en predial'],
    'area_deed_m2' => ['Escritura', 'Área textual de escritura'],
    'area_certificate_m2' => ['Certificado de Tradición', 'Área del certificado, si aparece'],
    'area_other_m2' => ['Otra fuente', 'Plano, levantamiento, reglamento PH u otro soporte'],
];
$boundaries = [
    'boundary_front' => ['1', 'Por el Frente', 'Valor textual del lindero por el frente'],
    'boundary_right' => ['2', 'Por la Derecha Entrando', 'Valor textual del lindero por la derecha entrando'],
    'boundary_left' => ['3', 'Por la Izquierda Entrando', 'Valor textual del lindero por la izquierda entrando'],
    'boundary_back' => ['4', 'Por el Fondo', 'Valor textual del lindero por el fondo'],
    'boundary_zenith' => ['5', 'Por el CENIT', 'Valor textual del cenit'],
    'boundary_nadir' => ['6', 'Por el NADIR', 'Valor textual del nadir'],
];
?>
<div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="activeSurface === '<?= e($unitId) ?>'"
    x-data="{
        activeSurfaceDetail: 'fisicas',
        areaAdopted: '<?= e($surfaceValue($unit, 'area_adopted_m2')) ?>',
        front: '<?= e($surfaceValue($unit, 'front_length_m')) ?>',
        equivalentDepth: '<?= e($surfaceValue($unit, 'equivalent_depth_m')) ?>',
        num(value) { return parseFloat(String(value || '').replace(',', '.')) || 0 },
        calcDepth() { const front = this.num(this.front); return front > 0 && this.num(this.areaAdopted) > 0 ? (this.num(this.areaAdopted) / front).toFixed(2) : '' },
        calcRatio() { const front = this.num(this.front); return front > 0 && this.num(this.equivalentDepth || this.calcDepth()) > 0 ? (this.num(this.equivalentDepth || this.calcDepth()) / front).toFixed(2) : '' }
    }">
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
    <div class="mt-5 space-y-5" x-show="activeSurfaceDetail === 'fisicas'">
        <label class="label">Fuente de linderos
            <input class="input" name="unit_surfaces[<?= e($unitId) ?>][boundary_source]"
                value="<?= e($surfaceValue($unit, 'boundary_source')) ?>"
                placeholder="Fuente: Escritura pública No. ___ de fecha ___, Notaría ___">
        </label>
        <div class="overflow-hidden rounded-xl border border-slate-200">
            <div class="grid grid-cols-[4rem_12rem_1fr] bg-blue-700 px-3 py-2 text-xs font-semibold text-white">
                <span>Ítem</span><span>Lindero</span><span>Valor textual</span>
            </div>
            <?php foreach ($boundaries as $key => [$number, $label, $placeholder]): ?>
                <div class="grid gap-3 border-t border-slate-200 bg-slate-50 p-3 md:grid-cols-[4rem_12rem_1fr]">
                    <span class="font-semibold text-slate-700"><?= e($number) ?></span>
                    <span class="font-semibold text-slate-800"><?= e($label) ?></span>
                    <input class="input mt-0" name="unit_surfaces[<?= e($unitId) ?>][<?= e($key) ?>]"
                        value="<?= e($surfaceValue($unit, $key)) ?>" placeholder="<?= e($placeholder) ?>">
                </div>
            <?php endforeach; ?>
        </div>
        <div class="grid gap-5 md:grid-cols-3">
            <label class="label">Forma del lote
                <select class="input" name="unit_surfaces[<?= e($unitId) ?>][lot_shape]">
                    <?php foreach ($shapeOptions as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $surfaceValue($unit, 'lot_shape') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="label">Topografía
                <select class="input" name="unit_surfaces[<?= e($unitId) ?>][topography]">
                    <?php foreach ($topographyOptions as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $surfaceValue($unit, 'topography') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="label">Cerramiento
                <select class="input" name="unit_surfaces[<?= e($unitId) ?>][enclosure]">
                    <?php foreach ($enclosureOptions as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $surfaceValue($unit, 'enclosure') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
    </div>
    <div class="mt-5 space-y-5" x-show="activeSurfaceDetail === 'superficie'">
        <div class="rounded-xl bg-sky-50 p-4 text-sm leading-6 text-sky-950">
            Registra todas las fuentes disponibles. La superficie adoptada es la que se usará para el fondo equivalente.
        </div>
        <div class="grid gap-5 md:grid-cols-3">
            <?php foreach ($areaFields as $key => [$label, $help]): ?>
                <label class="label"><?= e($label) ?> (m²)
                    <input class="input" name="unit_surfaces[<?= e($unitId) ?>][<?= e($key) ?>]"
                        inputmode="decimal" value="<?= e($surfaceValue($unit, $key)) ?>" placeholder="Ej. 120,50">
                    <span class="mt-1 block text-xs leading-5 text-slate-500"><?= e($help) ?></span>
                </label>
            <?php endforeach; ?>
            <label class="label">Superficie adoptada (m²)
                <input class="input" name="unit_surfaces[<?= e($unitId) ?>][area_adopted_m2]"
                    inputmode="decimal" x-model="areaAdopted" value="<?= e($surfaceValue($unit, 'area_adopted_m2')) ?>"
                    placeholder="Ej. 120,50">
            </label>
            <label class="label">Fuente adoptada
                <select class="input" name="unit_surfaces[<?= e($unitId) ?>][area_adopted_source]">
                    <option value="">Selecciona fuente adoptada</option>
                    <?php foreach ($surfaceSources as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $surfaceValue($unit, 'area_adopted_source') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
    </div>
    <div class="mt-5 grid gap-5 md:grid-cols-3" x-show="activeSurfaceDetail === 'fondo'">
        <label class="label">Frente adoptado (m)
            <input class="input" name="unit_surfaces[<?= e($unitId) ?>][front_length_m]"
                inputmode="decimal" x-model="front" value="<?= e($surfaceValue($unit, 'front_length_m')) ?>" placeholder="Ej. 7,50">
        </label>
        <label class="label">Fondo equivalente (m)
            <input class="input" name="unit_surfaces[<?= e($unitId) ?>][equivalent_depth_m]"
                inputmode="decimal" x-model="equivalentDepth" :placeholder="calcDepth() || 'Área adoptada / frente'">
        </label>
        <p class="rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">
            Fórmula: <strong>Fondo equivalente = Superficie adoptada / Frente adoptado</strong>.
        </p>
    </div>
    <div class="mt-5 grid gap-5 md:grid-cols-2" x-show="activeSurfaceDetail === 'relacion'">
        <label class="label">Relación frente-fondo
            <input class="input" name="unit_surfaces[<?= e($unitId) ?>][front_depth_ratio]"
                inputmode="decimal" value="<?= e($surfaceValue($unit, 'front_depth_ratio')) ?>"
                :placeholder="calcRatio() || 'Fondo equivalente / frente'">
        </label>
        <p class="rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">
            Fórmula: <strong>Relación frente-fondo = Fondo equivalente / Frente adoptado</strong>.
        </p>
    </div>
    <div class="mt-5 space-y-5" x-show="activeSurfaceDetail === 'dinamicas'">
        <div class="grid gap-5 md:grid-cols-2">
            <?php foreach ($dynamicOptions as $key => [$label, $options]): ?>
                <label class="label"><?= e($label) ?>
                    <select class="input" name="unit_surfaces[<?= e($unitId) ?>][<?= e($key) ?>]">
                        <option value="">Selecciona</option>
                        <?php foreach ($options as $value => $text): ?>
                            <option value="<?= e($value) ?>" <?= $surfaceValue($unit, $key) === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            <?php endforeach; ?>
        </div>
        <label class="label block">Justificación de variables dinámicas
            <textarea class="input" name="unit_surfaces[<?= e($unitId) ?>][dynamic_surface_notes]" rows="4" maxlength="1000"
                placeholder="Explica inconsistencias entre fuentes, afectaciones, servidumbres, retiros, forma o frente real."><?= e($surfaceValue($unit, 'dynamic_surface_notes')) ?></textarea>
        </label>
    </div>
    <div class="mt-5 grid gap-5" x-show="activeSurfaceDetail === 'informe'">
        <label class="label">Observaciones de superficie
            <textarea class="input" name="unit_surfaces[<?= e($unitId) ?>][surface_notes]" rows="3" maxlength="1000"
                placeholder="Justifica la fuente adoptada y las diferencias encontradas."><?= e($surfaceValue($unit, 'surface_notes')) ?></textarea>
        </label>
        <label class="label">Texto editable para el entregable
            <textarea class="input" name="unit_surfaces[<?= e($unitId) ?>][surface_report_text]" rows="4" maxlength="1500"
                placeholder="Redacción que alimentará la sección de superficies del informe."><?= e($surfaceValue($unit, 'surface_report_text')) ?></textarea>
        </label>
    </div>
</div>
