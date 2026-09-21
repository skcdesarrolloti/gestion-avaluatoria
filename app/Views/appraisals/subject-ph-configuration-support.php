<?php
$configShort = static function (string $value): string {
    $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    return mb_strlen($value) > 150 ? mb_substr($value, 0, 147) . '…' : $value;
};
$configPill = static function (string $state): string {
    return match ($state) {
        'ok' => '<button type="button" disabled class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Completo</button>',
        'warn' => '<button type="button" disabled class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Revisar</button>',
        default => '<button type="button" disabled class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Falta</button>',
    };
};
$configText = $technicalValue('resumen_configuracion_ph');
$matrix = $phText('matrix_registration');
$lot = $technicalValue('lotes_por_etapa');
$lotArea = $technicalValue('area_lote_matriz');
$totalArea = $technicalValue('area_construida_total');
$units = $technicalValue('numero_unidades');
$buildings = $technicalValue('numero_edificios');
$stages = $technicalValue('etapas_copropiedad');
$areas = $technicalValue('resumen_areas_conjunto');
$organization = $technicalValue('organizacion_interna');
$developments = $technicalValue('desarrollos_relevantes');
$unitLocation = $technicalValue('ubicacion_unidad');
$configRows = [
    ['Texto editable para Entregable', $configText !== '' ? 'Texto construido' : 'Sin texto construido', $configText !== '' ? 'ok' : 'missing', 'Construir el párrafo con los campos completos.'],
    ['Matrícula matriz', $matrix ?: 'Sin matrícula matriz', $matrix !== '' ? 'ok' : 'missing', 'Tomar de jurídica, CTL, escritura o reglamento.'],
    ['Lote matriz o predio de origen', $configShort($lot) ?: 'Sin lote matriz descrito', $lot !== '' ? 'ok' : 'warn', 'Precisar el predio sobre el que se desarrolló la PH.'],
    ['Área del lote matriz', $configShort($lotArea) ?: 'Sin área de lote', $lotArea !== '' ? 'ok' : 'warn', 'Ubicar área de lote o cabida en escritura, CTL o cuadro de áreas.'],
    ['Área construida o total del conjunto', $configShort($totalArea) ?: ($configShort($areas) ?: 'Sin área total'), ($totalArea !== '' || $areas !== '') ? 'ok' : 'warn', 'Tomar del cuadro de áreas, planos o reglamento.'],
    ['Número de unidades privadas', $configShort($units) ?: 'Sin número de unidades', $units !== '' ? 'ok' : 'missing', 'Precisar oficinas, locales, parqueaderos, depósitos, bodegas o viviendas según aplique.'],
    ['Bloques, torres, edificios o naves', $configShort($buildings) ?: 'Sin bloques o edificios', $buildings !== '' ? 'ok' : 'warn', 'Describir la organización física de la copropiedad.'],
    ['Número de pisos, sótanos o niveles', $configShort($organization) ?: 'Sin distribución funcional', $organization !== '' ? 'ok' : 'warn', 'Registrar sótanos, pisos, niveles y destinación por nivel si se conoce.'],
    ['Etapas, sectores o manzanas', $configShort($stages) ?: 'Sin etapas o sectores', $stages !== '' ? 'ok' : 'warn', 'Indicar fases, sectores o manzanas cuando existan.'],
    ['Unidad objeto dentro de la configuración', $configShort($unitLocation) ?: 'Sin ubicación de la unidad objeto', $unitLocation !== '' ? 'ok' : 'missing', 'Ubicar oficina, local, parqueadero, depósito, piso, torre o nave del avalúo.'],
    ['Desenglobes o antecedentes prediales', $configShort($developments) ?: 'Sin antecedentes descritos', $developments !== '' ? 'ok' : 'warn', 'Revisar si hay desenglobes, integraciones, reformas o lotes resultantes.'],
];
?>
<div class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 lg:col-span-2">
    <h4 class="font-semibold text-slate-900">Campos de configuración predial para construir el Entregable</h4>
    <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[52rem] text-left text-sm">
            <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Campo</th><th class="py-2 pr-3">Valor detectado</th><th class="py-2 pr-3">Estado</th><th class="py-2">Qué falta</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($configRows as [$field, $value, $state, $missing]): ?>
                    <tr><td class="py-2 pr-3 font-semibold text-slate-800"><?= e($field) ?></td><td class="py-2 pr-3 text-slate-700"><?= e($value) ?></td><td class="py-2 pr-3"><?= $configPill($state) ?></td><td class="py-2 text-slate-600"><?= e($state === 'ok' ? 'Listo para texto.' : $missing) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
