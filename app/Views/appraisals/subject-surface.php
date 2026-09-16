<?php
$surfaceUnitLabel = static function (array $unit): string {
    return ($unit['unit_kind'] === 'annex' ? 'Anexo ' : 'Unidad ') . (int) $unit['unit_index'];
};
$surfaceUnits = array_values(array_filter($units, static fn (array $unit): bool => $unit['unit_kind'] !== 'common'));
$surfaceValue = static fn (array $unit, string $key): string => (string) ($unit[$key] ?? '');
$surfaceTabs = [
    'fisicas' => ['1', 'Características físicas y linderos'],
    'superficie' => ['2', 'Superficie del terreno'],
    'fondo' => ['3', 'Fondo equivalente'],
    'relacion' => ['4', 'Relación frente-fondo'],
    'dinamicas' => ['5', 'Variables dinámicas'],
];
$surfaceSources = ['manual' => 'Manual', 'midas' => 'MIDAS', 'tax' => 'Impuesto predial',
    'deed' => 'Escritura', 'certificate' => 'Certificado de Tradición', 'other' => 'Otra fuente'];
$shapeOptions = ['' => 'Selecciona forma', 'rectangular' => 'Rectangular', 'cuadrado' => 'Cuadrado',
    'regular' => 'Regular', 'irregular' => 'Irregular', 'muy_irregular' => 'Muy irregular'];
$topographyOptions = ['' => 'Selecciona topografía', 'plana' => 'Plana', 'levemente_inclinada' => 'Levemente inclinada',
    'ondulada' => 'Ondulada moderada', 'inclinada' => 'Inclinada', 'escarpada' => 'Escarpada'];
$enclosureOptions = ['' => 'Selecciona cerramiento', 'sin_cerramiento' => 'Sin cerramiento',
    'parcial' => 'Parcial', 'total' => 'Total', 'no_verificado' => 'No verificado'];
$dynamicOptions = [
    'dynamic_normative_compatibility' => ['Compatibilidad normativa', ['5' => '5 - Altamente compatible', '4' => '4 - Compatible',
        '3' => '3 - Condicionado', '2' => '2 - Restringido', '1' => '1 - No compatible']],
    'dynamic_environment_conditions' => ['Condiciones del entorno', ['5' => '5 - Altamente favorable', '4' => '4 - Favorable',
        '3' => '3 - Neutro', '2' => '2 - Limitante', '1' => '1 - Muy limitante']],
    'dynamic_service_quality' => ['Calidad de servicios', ['5' => '5 - Excelente', '4' => '4 - Muy buena',
        '3' => '3 - Buena', '2' => '2 - Media', '1' => '1 - Inferior']],
    'dynamic_service_availability' => ['Disponibilidad de servicios', ['5' => '5 - Inmediata completa', '4' => '4 - Completa',
        '3' => '3 - Parcial útil', '2' => '2 - Limitada', '1' => '1 - Deficitaria']],
    'dynamic_road_condition' => ['Condición de la vía', ['5' => '5 - Principal pavimentada', '4' => '4 - Secundaria pavimentada',
        '3' => '3 - Mixta', '2' => '2 - Destapada', '1' => '1 - Acceso precario']],
    'dynamic_urban_development' => ['Desarrollo urbanístico', ['5' => '5 - Urbanizado consolidado', '4' => '4 - Urbanizado',
        '3' => '3 - En desarrollo', '2' => '2 - Urbanizable', '1' => '1 - Rural/suburbano']],
    'dynamic_affectations' => ['Afectaciones', ['5' => '5 - Sin afectaciones relevantes', '4' => '4 - Afectación menor',
        '3' => '3 - Afectación moderada', '2' => '2 - Afectación alta', '1' => '1 - Afectación crítica']],
    'dynamic_restrictions' => ['Restricciones normativas', ['5' => '5 - Muy favorables', '4' => '4 - Favorables',
        '3' => '3 - Neutros', '2' => '2 - Exigentes', '1' => '1 - Muy restrictivos']],
];
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{ activeSurface: '<?= e($surfaceUnits[0]['id'] ?? '') ?>', busySurface: false }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">3.2 Datos de la superficie</p>
            <h2 class="mt-2 text-2xl font-semibold">Superficies por unidad del predio</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Primero captura las fuentes de área, luego adopta la superficie técnica. El fondo equivalente
                se calcula con <strong>área adoptada / frente</strong> y la relación frente-fondo con
                <strong>fondo equivalente / frente</strong>, por cada unidad donde aplique.
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
            <?php require BASE_PATH . '/app/Views/appraisals/subject-surface-unit.php'; ?>
        <?php endforeach; ?>
        <?php if ($surfaceUnits): ?>
            <div class="mt-5 flex justify-end">
                <button class="btn-primary" type="submit" :disabled="busySurface"
                    x-text="busySurface ? 'Guardando...' : 'Guardar superficies'">Guardar superficies</button>
            </div>
        <?php endif; ?>
    </form>
</section>
