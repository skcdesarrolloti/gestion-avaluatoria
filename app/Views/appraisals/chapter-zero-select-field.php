<?php
use App\Support\AppraisalCatalog;

[$label, $placeholder, , $help, $options] = AppraisalCatalog::selectFields()[$name];
$label = $name === 'aplica_niif' ? 'Activo empresarial' : $label;
$plain = !in_array($name, ['tipo_inmueble', 'subtipo_funcional'], true);
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
    <?php $academyValue = $name === 'tipo_inmueble' ? 'selectedPropertyType'
        : ($name === 'subtipo_funcional' ? 'selectedSubtype' : 'selected'); ?>
    <span class="mt-3 block rounded-xl border border-teal-100 bg-teal-50 p-3 text-xs leading-5 text-teal-950"
        x-show="academy('<?= e($name) ?>', <?= $academyValue ?>)">
        <strong class="block text-teal-900">Academia del campo</strong>
        <span class="mt-1 block"><strong>Qué es:</strong>
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
    </span>
</label>
