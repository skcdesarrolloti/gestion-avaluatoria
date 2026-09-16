<?php
$attributeUnitLabel = static fn (array $unit): string => ($unit['unit_kind'] === 'annex' ? 'Anexo ' : 'Unidad ') . (int) $unit['unit_index'];
$attributeUnits = array_values(array_filter($units, static fn (array $unit): bool => $unit['unit_kind'] !== 'common'));
$attrValue = static function (array $unit, string $key, string $field): string {
    $data = json_decode((string) ($unit['special_attributes_json'] ?? '{}'), true);
    return is_array($data) ? (string) ($data[$key][$field] ?? '') : '';
};
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{ activeAttributes: '<?= e($attributeUnits[0]['id'] ?? '') ?>', busyAttributes: false }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">2.4 Atributos especiales del sujeto</p>
            <h2 class="mt-2 text-2xl font-semibold">Lectura diferencial por unidad</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Aquí solo se registra qué atributos especiales tiene cada unidad, su evidencia e impacto técnico.
                Las condiciones de búsqueda y filtros de portales se trabajan después en el numeral 3.
            </p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
            <?= count($attributeUnits) ?> unidad(es)
        </span>
    </div>
    <form class="mt-6" method="post" action="<?= e(url($subjectActionBase . '/atributos')) ?>"
        @submit="busyAttributes = true">
        <?= csrf_field() ?>
        <?php if (!$attributeUnits): ?>
            <p class="rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-600">
                Primero define las unidades o anexos del predio en Tipologías IGAC.
            </p>
        <?php else: ?>
            <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
                <?php foreach ($attributeUnits as $unit): ?>
                    <button class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                        type="button" @click="activeAttributes = '<?= e($unit['id']) ?>'"
                        :class="activeAttributes === '<?= e($unit['id']) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                        <?= e($unit['label'] ?: $attributeUnitLabel($unit)) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php foreach ($attributeUnits as $unit): ?>
            <?php require BASE_PATH . '/app/Views/appraisals/subject-attributes-unit.php'; ?>
        <?php endforeach; ?>
        <?php if ($attributeUnits): ?>
            <div class="mt-5 flex justify-end">
                <button class="btn-primary" type="submit" :disabled="busyAttributes"
                    x-text="busyAttributes ? 'Guardando...' : 'Guardar atributos'">Guardar atributos</button>
            </div>
        <?php endif; ?>
    </form>
</section>
