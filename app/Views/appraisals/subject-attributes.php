<?php
$attributeUnitLabel = static fn (array $unit): string => ($unit['unit_kind'] === 'annex' ? 'Anexo ' : 'Unidad ') . (int) $unit['unit_index'];
$attributeUnits = array_values(array_filter($units, static fn (array $unit): bool => $unit['unit_kind'] !== 'common'));
$attributeCatalogForUnit = static function (array $unit) use ($record): array {
    $type = (string) (($unit['property_type'] ?? '') ?: ($record['tipo_inmueble'] ?? ''));
    return \App\Support\AppraisalSpecialAttributeCatalog::groups($type);
};
$attrValue = static function (array $unit, string $key, string $field): string {
    $data = json_decode((string) ($unit['special_attributes_json'] ?? '{}'), true);
    return is_array($data) ? (string) ($data[$key][$field] ?? '') : '';
};
$attributeScore = static function (array $unit, array $catalog): array {
    $data = json_decode((string) ($unit['special_attributes_json'] ?? '{}'), true);
    if (!is_array($data)) return ['score' => null, 'label' => 'Sin calificación', 'count' => 0];
    $allowed = [];
    foreach ($catalog as $group) $allowed = array_merge($allowed, array_keys($group[1]));
    $sum = 0; $weightSum = 0; $count = 0;
    foreach ($allowed as $key) {
        $rating = (int) ($data[$key]['rating'] ?? 0);
        $weight = (int) ($data[$key]['weight'] ?? 0);
        if ($rating < 1 || $rating > 5 || $weight < 1 || $weight > 3) continue;
        $sum += $rating * $weight; $weightSum += $weight; $count++;
    }
    if ($weightSum === 0) return ['score' => null, 'label' => 'Sin calificación', 'count' => 0];
    $score = round($sum / $weightSum, 2);
    $percent = round(($score / 5) * 100);
    $adjustment = round(max(-10, min(10, (($score - 3) / 2) * 10)), 1);
    $label = $score < 2.5 ? 'Desfavorable' : ($score < 3.5 ? 'Normal' : ($score < 4.3 ? 'Favorable' : 'Muy favorable'));
    return ['score' => $score, 'percent' => $percent, 'adjustment' => $adjustment, 'label' => $label, 'count' => $count];
};
$formatAttributeAdjustment = static function (?float $value): string {
    if ($value === null) return 'pendiente';
    if (abs($value) < 0.05) return '0%';
    return ($value > 0 ? '+' : '') . str_replace('.', ',', (string) $value) . '%';
};
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{ activeAttributes: '<?= e($attributeUnits[0]['id'] ?? '') ?>', busyAttributes: false }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">3.4 Atributos especiales del sujeto</p>
            <h2 class="mt-2 text-2xl font-semibold">Lectura diferencial por unidad</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Aquí solo se registra qué atributos especiales tiene cada unidad, su evidencia e impacto técnico.
                La calificación ponderada ayuda a ubicar el bien dentro del rango de mercado sin reemplazar
                el criterio del perito.
            </p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
            <?= count($attributeUnits) ?> unidad(es)
        </span>
    </div>
    <div class="mt-5 grid gap-4 rounded-xl border border-teal-100 bg-teal-50 p-4 text-sm leading-6 text-teal-950 lg:grid-cols-4">
        <div>
            <strong class="block">Dónde nace cada unidad</strong>
            <span>En el numeral 1 defines cuántas unidades principales y anexos existen. En 3.1 nombras cada una y eliges su tipo de inmueble.</span>
        </div>
        <div>
            <strong class="block">Cómo se calcula el índice</strong>
            <span>Comunes y específicos entran juntos. Fórmula: suma(calificación × peso) / suma(pesos). Ese índice solo mide calidad relativa.</span>
        </div>
        <div>
            <strong class="block">Cómo escoger el peso</strong>
            <span>Bajo si apenas ayuda, medio si mueve la comparación, alto si cambia claramente la percepción de valor de esa unidad.</span>
        </div>
        <div>
            <strong class="block">Ejemplo valuatorio</strong>
            <span>35 puntos / 9 pesos = 3,89 sobre 5. No sube 78%; con banda máxima del 10%, equivale a +4,5%. Sobre $1.000 millones serían $45 millones orientativos.</span>
        </div>
    </div>
    <form class="mt-6" method="post" enctype="multipart/form-data" action="<?= e(url($subjectActionBase . '/atributos')) ?>"
        data-module-autosave data-autosave-endpoint="<?= e(url($subjectActionBase . '/atributos/autoguardar')) ?>"
        @submit="busyAttributes = true">
        <?= csrf_field() ?>
        <?php if (!$attributeUnits): ?>
            <p class="rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-600">
                Primero define las unidades o anexos del predio en Tipologías IGAC.
            </p>
        <?php else: ?>
            <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
                <?php foreach ($attributeUnits as $unit): ?>
                    <?php $tabScore = $attributeScore($unit, $attributeCatalogForUnit($unit)); ?>
                    <button class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                        type="button" @click="activeAttributes = '<?= e($unit['id']) ?>'"
                        :class="activeAttributes === '<?= e($unit['id']) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                        <?= e($unit['label'] ?: $attributeUnitLabel($unit)) ?>
                        <span class="ml-2 rounded-full bg-blue-50 px-2 py-0.5 text-xs text-blue-800">
                            <?= e($formatAttributeAdjustment($tabScore['score'] === null ? null : (float) $tabScore['adjustment'])) ?>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php foreach ($attributeUnits as $unit): ?>
            <?php $unitSpecialAttributeCatalog = $attributeCatalogForUnit($unit); ?>
            <?php require BASE_PATH . '/app/Views/appraisals/subject-attributes-unit.php'; ?>
        <?php endforeach; ?>
        <?php if ($attributeUnits): ?>
            <div class="mt-5 flex justify-end">
                <p class="mr-auto self-center text-xs font-semibold text-slate-500" data-autosave-status>
                    Autoguardado activo para textos y atributos. Las evidencias marcadas como Foto se cargan en 3.6.
                </p>
                <button class="btn-primary" type="submit" :disabled="busyAttributes"
                    x-text="busyAttributes ? 'Guardando...' : 'Guardar atributos'">Guardar atributos</button>
            </div>
        <?php endif; ?>
    </form>
</section>
