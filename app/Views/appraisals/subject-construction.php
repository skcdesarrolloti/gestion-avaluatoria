<?php
$constructionUnitLabel = static function (array $unit): string {
    return ($unit['unit_kind'] === 'annex' ? 'Anexo ' : 'Unidad ') . (int) $unit['unit_index'];
};
$constructionUnits = array_values(array_filter($units, static fn (array $unit): bool => $unit['unit_kind'] !== 'common'));
$cv = static fn (array $unit, string $key): string => (string) ($unit[$key] ?? '');
$jsonValue = static function (array $unit, string $field, string $key): string {
    $data = json_decode((string) ($unit[$field] ?? '{}'), true);
    return is_array($data) ? (string) ($data[$key] ?? '') : '';
};
$constructionTabs = ['basicos' => ['1', 'Datos básicos'], 'pisos' => ['2', 'Pisos y sótanos'],
    'area' => ['3', 'Área construida'], 'vetustez' => ['4', 'Vetustez y vida útil'],
    'estado' => ['5', 'Estado de obra'], 'conservacion' => ['6', 'Conservación'],
    'informe' => ['7', 'Informe para el entregable']];
$constructionTypes = ['' => 'Selecciona tipo', 'galpon' => 'Galpón / nave industrial', 'bodega' => 'Bodega',
    'casa' => 'Casa', 'apartamento' => 'Apartamento', 'local' => 'Local comercial', 'oficina' => 'Oficina',
    'deposito' => 'Depósito / cuarto útil', 'mezanine' => 'Mezanine', 'cubierta' => 'Cubierta / techo',
    'cerramiento' => 'Cerramiento', 'muro' => 'Muro perimetral', 'porton' => 'Portón / acceso vehicular',
    'placa' => 'Placa de concreto', 'parqueo' => 'Parqueo', 'piscina' => 'Piscina', 'otro' => 'Otro'];
$measureUnits = ['m2' => 'm² · área', 'ml' => 'ml · longitud', 'm3' => 'm³ · volumen', 'und' => 'Unidad'];
$areaSources = ['manual' => 'Manual', 'midas' => 'MIDAS', 'tax' => 'Impuesto predial',
    'deed' => 'Escritura', 'certificate' => 'Certificado de Tradición', 'other' => 'Otra fuente'];
$stateOptions = ['' => 'Selecciona estado', 'completa' => 'Obra completa', 'en_construccion' => 'En construcción',
    'desmantelamiento' => 'En desmantelamiento', 'inconclusa' => 'Sin avance / obra inconclusa'];
$typologyLookup = [];
foreach (($igacTypologiesByCategory ?? []) as $category => $items) {
    foreach ($items as $item) $typologyLookup[$category][$item['value']] = $item;
}
$componentDefinitions = [
    'estructura' => ['Estructura', 'Sistema portante que sostiene la construcción.'],
    'fachada' => ['Fachada', 'Acabado exterior y presentación visible de la unidad.'],
    'cubierta' => ['Cubierta', 'Sistema de protección superior o techo.'],
    'dependencias' => ['Dependencias', 'Distribución de espacios interiores o funcionales.'],
    'iluminacion' => ['Iluminación', 'Condiciones naturales o artificiales de iluminación.'],
    'ventilacion' => ['Ventilación', 'Condiciones naturales o mecánicas de ventilación.'],
    'acabados' => ['Acabados', 'Calidad general de terminaciones y presentación.'],
    'pisos' => ['Pisos', 'Material y estado de los acabados de piso.'],
    'paredes' => ['Paredes', 'Material y acabado de muros interiores.'],
    'cielorraso' => ['Cielo raso', 'Acabado inferior de cubierta o entrepiso.'],
    'puertas' => ['Puertas', 'Material y estado de accesos interiores o exteriores.'],
    'ventanas' => ['Ventanas', 'Material, perfilería y estado de vanos.'],
    'banos' => ['Baños', 'Acabados, aparatos e instalaciones sanitarias.'],
    'cocina' => ['Cocina', 'Acabados, mesones, muebles e instalaciones de cocina.'],
    'instalaciones' => ['Instalaciones', 'Redes eléctricas, hidrosanitarias o especiales.'],
    'cerramiento' => ['Cerramiento', 'Elementos perimetrales, muros, rejas o mallas.'],
    'porton' => ['Portón', 'Acceso vehicular o peatonal asociado.'],
    'equipos' => ['Equipos', 'Equipos fijos o especiales de la construcción.'],
];
$componentSets = [
    'ANEXOS' => ['estructura', 'cubierta', 'pisos', 'cerramiento', 'porton', 'instalaciones', 'equipos'],
    'INDUSTRIALES' => ['estructura', 'fachada', 'cubierta', 'pisos', 'paredes', 'iluminacion',
        'ventilacion', 'instalaciones', 'porton', 'banos'],
    'default' => ['estructura', 'fachada', 'cubierta', 'dependencias', 'iluminacion', 'ventilacion',
        'acabados', 'pisos', 'paredes', 'cielorraso', 'puertas', 'ventanas', 'banos', 'cocina', 'instalaciones'],
];
$materialOptions = [
    'estructura' => ['Concreto reforzado', 'Acero', 'Mampostería estructural', 'Madera', 'Mixta'],
    'fachada' => ['Pañete y pintura', 'Ladrillo a la vista', 'Prefabricado', 'Vidrio / aluminio', 'Sin acabado'],
    'cubierta' => ['Placa de concreto', 'Teja fibrocemento', 'Teja metálica', 'Teja de barro', 'Policarbonato'],
    'pisos' => ['Cerámica', 'Baldosa', 'Concreto afinado', 'Porcelanato', 'Tierra / sin acabado'],
    'paredes' => ['Mampostería revocada', 'Drywall', 'Concreto a la vista', 'Madera', 'Sin acabado'],
    'cielorraso' => ['Drywall', 'Machimbre', 'PVC', 'Concreto a la vista', 'No aplica'],
    'puertas' => ['Madera', 'Metálica', 'Aluminio', 'Vidrio templado', 'No aplica'],
    'ventanas' => ['Aluminio y vidrio', 'Madera', 'Metálica', 'PVC', 'No aplica'],
    'banos' => ['Enchape cerámico', 'Aparatos básicos', 'Sin baño', 'No aplica'],
    'cocina' => ['Mesón en granito', 'Enchape cerámico', 'Cocina sencilla', 'Sin cocina', 'No aplica'],
];
$componentAliases = [
    'estructura' => ['estructura', 'concreto', 'acero', 'cimientos', 'vigas', 'columnas'],
    'fachada' => ['fachada', 'exterior'], 'cubierta' => ['cubierta', 'teja', 'placa', 'techo'],
    'acabados' => ['acabado', 'enchape'], 'pisos' => ['piso', 'baldosa', 'ceramica'],
    'paredes' => ['pared', 'muro', 'mamposteria'], 'puertas' => ['puerta'], 'ventanas' => ['ventana'],
    'banos' => ['bano', 'sanitario'], 'cocina' => ['cocina', 'meson'], 'cerramiento' => ['cerramiento', 'malla'],
    'porton' => ['porton', 'acceso'], 'instalaciones' => ['instalacion', 'red', 'electrica', 'hidraulica'],
];
$suggestFromTypology = static function (string $key, string $text) use ($componentAliases): string {
    $phrases = preg_split('/[.;]/', $text) ?: [];
    $aliases = $componentAliases[$key] ?? [$key];
    foreach ($phrases as $phrase) {
        $plain = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', mb_strtolower($phrase)) ?: mb_strtolower($phrase);
        foreach ($aliases as $alias) if (str_contains($plain, $alias)) return mb_substr(trim($phrase), 0, 120);
    }
    return '';
};
$componentRows = static function (array $unit) use ($typologyLookup, $componentSets, $componentDefinitions,
    $materialOptions, $suggestFromTypology): array {
    $category = (string) ($unit['igac_category'] ?? '');
    $typology = $typologyLookup[$category][(string) ($unit['igac_typology_hint'] ?? '')] ?? [];
    $text = trim((string) ($typology['description'] ?? '') . ' ' . (string) ($typology['specifications'] ?? ''));
    $keys = $componentSets[$category] ?? $componentSets['default'];
    return array_map(static fn (string $key): array => [
        'key' => $key, 'label' => $componentDefinitions[$key][0], 'definition' => $componentDefinitions[$key][1],
        'suggestion' => $suggestFromTypology($key, $text),
        'materials' => $materialOptions[$key] ?? ['Según tipología IGAC', 'Bueno / convencional', 'Sencillo', 'Especial', 'No aplica'],
    ], $keys);
};
$typologyUsefulLife = static function (array $unit) use ($typologyLookup): string {
    $value = (string) ($typologyLookup[(string) ($unit['igac_category'] ?? '')][(string) ($unit['igac_typology_hint'] ?? '')]['useful_life'] ?? '');
    return preg_replace('/[^0-9]/', '', $value) ?: $value;
};
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{ activeConstruction: '<?= e($constructionUnits[0]['id'] ?? '') ?>', busyConstruction: false }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">2.3 Datos de la construcción</p>
            <h2 class="mt-2 text-2xl font-semibold">Construcciones por unidad del predio</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Caracteriza cada unidad o anexo: tipo constructivo, áreas por fuente, vetustez, estado,
                conservación y texto editable para el entregable. Los servicios públicos se capturan en 2.1.
            </p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"><?= count($constructionUnits) ?> unidad(es)</span>
    </div>
    <form class="mt-6" method="post" action="<?= e(url($subjectActionBase . '/construcciones')) ?>" @submit="busyConstruction = true">
        <?= csrf_field() ?>
        <?php if (!$constructionUnits): ?>
            <p class="rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-600">
                Primero define las unidades o anexos del predio en Tipologías IGAC.
            </p>
        <?php else: ?>
            <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2">
                <?php foreach ($constructionUnits as $unit): ?>
                    <button class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold" type="button"
                        @click="activeConstruction = '<?= e($unit['id']) ?>'"
                        :class="activeConstruction === '<?= e($unit['id']) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                        <?= e($unit['label'] ?: $constructionUnitLabel($unit)) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php foreach ($constructionUnits as $unit): ?>
            <?php require BASE_PATH . '/app/Views/appraisals/subject-construction-unit.php'; ?>
        <?php endforeach; ?>
        <?php if ($constructionUnits): ?>
            <div class="mt-5 flex justify-end">
                <button class="btn-primary" type="submit" :disabled="busyConstruction"
                    x-text="busyConstruction ? 'Guardando...' : 'Guardar construcción'">Guardar construcción</button>
            </div>
        <?php endif; ?>
    </form>
</section>
