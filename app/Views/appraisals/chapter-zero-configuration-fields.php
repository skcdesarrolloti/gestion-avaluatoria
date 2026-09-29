<?php
// Campos del numeral 1.1. Hereda $field, $selected, $selectedAppraiser y $count desde chapter-zero.php.
$configTabs = [
    'expediente' => 'Perito y expediente',
    'metodo' => 'Negocio y tipología',
    'renta' => 'Renta',
    'niif' => 'NIIF y PH',
    'notas' => 'Notas',
];
?>
<nav class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Bloques del numeral 1.1">
    <?php foreach ($configTabs as $key => $label): ?>
        <button type="button" class="min-h-10 shrink-0 rounded-lg px-3 py-2 text-xs font-semibold"
            :class="configTab === '<?= e($key) ?>' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800'"
            @click="configTab = '<?= e($key) ?>'"><?= e($label) ?></button>
    <?php endforeach; ?>
</nav>
<div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
    Diligencia un bloque a la vez. Esta configuración alimenta el numeral 8.2 y prepara las unidades que luego nombras en 3.1.
</div>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'expediente'">
    <label class="label">Perito responsable
        <select class="input" name="appraiser_id">
            <option value="">Selecciona perito</option>
            <?php foreach ($appraisers as $appraiser): ?>
                <option value="<?= e($appraiser['id']) ?>" <?= $selectedAppraiser($appraiser['id']) ?>>
                    <?= e($appraiser['code'] . ' · ' . $appraiser['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (count($appraisers) === 1 && $field('appraiser_id') === ''): ?>
            <span class="mt-1 block text-xs leading-5 text-emerald-700">
                Se selecciona automáticamente porque solo hay un perito vigente.
            </span>
        <?php elseif (count($appraisers) === 0): ?>
            <span class="mt-1 block text-xs leading-5 text-red-700">
                No hay peritos con RAA vigente. Actualiza el RAA en Maestros para asignar expediente.
            </span>
        <?php endif; ?>
    </label>
    <?php require BASE_PATH . '/app/Views/appraisals/appraisal-dossier-field.php'; ?>
</div>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'metodo'" x-cloak>
    <?php foreach (['tipo_negocio', 'tipo_inmueble', 'subtipo_funcional'] as $name) {
        require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php';
    } ?>
    <label class="label">Unidades inmobiliarias principales
        <input class="input" type="number" name="igac_property_units_count" min="0" max="50"
            x-model.number="propertyUnits" placeholder="Ej. 3">
        <span class="mt-1 block text-xs leading-5 text-slate-500">
            Casa + 2 apartamentos = 3. Cada unidad se nombrará y clasificará en 3.1.
        </span>
    </label>
    <label class="label">Anexos existentes
        <input class="input" type="number" name="igac_annex_units_count" min="0" max="50"
            x-model.number="annexUnits" placeholder="Ej. 1">
        <span class="mt-1 block text-xs leading-5 text-slate-500">
            Parqueaderos, depósitos, kioscos, piscinas, ramadas u otros anexos.
        </span>
    </label>
    <?php $name = 'estructura_metodo'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>
    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950 md:col-span-2">
        Se prepararán <strong x-text="propertyUnits || 0"></strong> unidad(es) principal(es)
        y <strong x-text="annexUnits || 0"></strong> anexo(s). En 3.1 nombras cada una,
        eliges su tipo de inmueble y desde ahí se alimentan superficies, construcción,
        diferenciales, fotos y PH cuando aplique.
    </div>
</div>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'renta'" x-cloak>
    <?php require BASE_PATH . '/app/Views/appraisals/chapter-zero-income-fields.php'; ?>
</div>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'niif'" x-cloak>
    <?php foreach (['aplica_niif', 'regimen_ph'] as $name) {
        require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php';
    } ?>
</div>

<div class="mt-5 grid gap-5 md:grid-cols-2" x-show="configTab === 'notas'" x-cloak>
    <label class="label md:col-span-2">Notas de inspección y configuración
        <textarea class="input" name="inspection_notes" rows="4" maxlength="2000"
            placeholder="Anota dudas de campo, componentes del bien o alertas técnicas."><?= e($field('inspection_notes')) ?></textarea>
    </label>
</div>
