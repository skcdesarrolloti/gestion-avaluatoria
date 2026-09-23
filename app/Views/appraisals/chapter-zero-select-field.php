<?php
use App\Support\AppraisalCatalog;

[$label, $placeholder, , $help, $options] = AppraisalCatalog::selectFields()[$name];
$label = $name === 'aplica_niif' ? 'Activo empresarial' : $label;
$plain = !in_array($name, ['tipo_inmueble', 'subtipo_funcional'], true);
$fieldSupport = AppraisalCatalog::fieldSupport($name);
?>
<label class="label" <?= $plain ? 'x-data="{ selected: ' . e(json_encode($field($name), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) . ' }"' : '' ?>><?= e($label) ?>
    <?php if ($name === 'tipo_inmueble'): ?>
        <select class="input" name="<?= e($name) ?>" x-model="selectedPropertyType" @change="syncSubtype()">
            <option value=""><?= e($placeholder) ?></option>
            <?php foreach ($options as $value => $text): ?>
                <option value="<?= e($value) ?>"><?= e($text) ?></option>
            <?php endforeach; ?>
        </select>
    <?php elseif ($name === 'subtipo_funcional'): ?>
        <select class="input" name="<?= e($name) ?>" x-model="selectedSubtype" :disabled="!selectedPropertyType">
            <option value="" x-text="selectedPropertyType ? '<?= e($placeholder) ?>' : 'Selecciona primero tipo de inmueble'"></option>
            <template x-for="(text, value) in subtypeOptions()" :key="value">
                <option :value="value" x-text="text"></option>
            </template>
        </select>
    <?php else: ?>
        <select class="input" name="<?= e($name) ?>" x-model="selected">
            <option value=""><?= e($placeholder) ?></option>
            <?php foreach ($options as $value => $text): ?>
                <option value="<?= e($value) ?>" <?= $selected($name, $value) ?>><?= e($text) ?></option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>
    <span class="mt-1 block text-xs leading-5 text-slate-500"><?= e($help) ?></span>
    <?php if ($fieldSupport !== ''): ?><span class="mt-1 block text-xs leading-5 text-slate-500">Soporte: <?= e($fieldSupport) ?></span><?php endif; ?>
    <?php $academyValue = $name === 'tipo_inmueble' ? 'selectedPropertyType'
        : ($name === 'subtipo_funcional' ? 'selectedSubtype' : 'selected'); ?>
    <?php if ($name === 'base_valor'): ?>
        <div class="mt-3 rounded-xl border border-blue-100 bg-blue-50 p-3 text-xs leading-5 text-blue-950"
            x-show="academy('base_valor', selected)">
            <strong>Definición aplicada:</strong>
            <span x-text="academy('base_valor', selected)?.report || academy('base_valor', selected)?.what"></span>
        </div>
    <?php endif; ?>
    <details class="mt-3 rounded-xl border border-teal-100 bg-teal-50 p-3 text-xs leading-5 text-teal-950"
        x-show="academy('<?= e($name) ?>', <?= $academyValue ?>)">
        <summary class="cursor-pointer font-semibold text-teal-900">Ver academia del campo</summary>
        <span class="mt-2 block"><strong>Qué es:</strong>
            <span x-text="academy('<?= e($name) ?>', <?= $academyValue ?>)?.what"></span></span>
        <span class="mt-1 block"><strong>Cuándo aplica:</strong>
            <span x-text="academy('<?= e($name) ?>', <?= $academyValue ?>)?.when"></span></span>
        <span class="mt-1 block"><strong>Soporte:</strong>
            <span x-text="academy('<?= e($name) ?>', <?= $academyValue ?>)?.basis"></span></span>
        <span class="mt-2 block rounded-lg bg-white/70 p-2"
            x-show="academy('<?= e($name) ?>', <?= $academyValue ?>)?.report">
            <strong>Justificación para el informe:</strong>
            <span x-text="academy('<?= e($name) ?>', <?= $academyValue ?>)?.report"></span>
        </span>
    </details>
</label>
