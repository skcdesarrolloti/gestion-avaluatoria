<?php
$midasGroups = [
    'Identificación predial MIDAS' => [
        'midas_national_cadastral_reference' => ['Número predial nacional', 'Ej. 130010103000003810024000000000'],
        'midas_cadastral_reference' => ['Referencia catastral corta', 'Ej. 010303810024000'],
        'midas_property_registry' => ['Matrícula inmobiliaria', 'Si MIDAS la reporta'],
        'midas_address' => ['Dirección MIDAS', 'Dirección leída en la ficha Predios'],
    ],
    'Ubicación, norma y condición urbana' => [
        'midas_territory' => ['Territorio / barrio MIDAS', 'Ej. Barrio Zaragocilla'],
        'midas_locality' => ['Localidad MIDAS', 'Ej. Histórica y del Caribe Norte'],
        'midas_commune_ucg' => ['Unidad Comunera de Gobierno', 'Ej. 8'],
        'midas_land_use' => ['Uso de suelo', 'Ej. Mixto 2'],
        'midas_urban_treatment' => ['Tratamiento', 'Ej. Mejoramiento integral parcial'],
        'midas_land_classification' => ['Clasificación del suelo', 'Ej. Suelo urbano'],
        'midas_risk' => ['Riesgos', 'Amenaza, riesgo o condición reportada'],
    ],
    'Catastro técnico y áreas' => [
        'midas_land_area_m2' => ['Área terreno (m²)', 'Área reportada por MIDAS'],
        'midas_built_area_m2' => ['Área construida (m²)', 'Área construida reportada por MIDAS'],
        'midas_stratum' => ['Estrato socioeconómico', 'Estrato leído en MIDAS'],
        'midas_stratum_record' => ['Acta de estratificación', 'Acta o soporte'],
        'midas_stratum_atypical' => ['Atipicidad estratificación', 'Ej. No'],
        'midas_stratum_observation' => ['Observación estratificación', 'Observación reportada'],
        'midas_building_name' => ['Nombre edificación', 'Nombre si MIDAS lo reporta'],
    ],
    'Trazabilidad DANE y actualización' => [
        'midas_dane_block_code' => ['Código manzana DANE', 'Dato de trazabilidad; no reemplaza verificación actual'],
        'midas_dane_block_side' => ['Lado manzana DANE', 'Dato de trazabilidad'],
        'midas_block_number' => ['Número de manzana', 'Número de manzana reportado'],
        'midas_property_number' => ['Número de predio', 'Número de predio reportado'],
        'midas_updated_on' => ['Fecha actualización MIDAS', 'Fecha de actualización de la ficha'],
    ],
];
$renderMidas = static function (string $key, array $meta) use ($sv, $fieldHelp): void {
    [$label, $placeholder] = $meta;
    $help = $fieldHelp($key);
    ?>
    <label class="label"><?= e($label) ?>
        <?php if ($help !== ''): ?><span class="help-dot" title="<?= e($help) ?>">?</span><?php endif; ?>
        <input class="input" <?= $key === 'midas_updated_on' ? 'type="date"' : '' ?>
            name="<?= e($key) ?>" value="<?= e($sv($key)) ?>" placeholder="<?= e($placeholder) ?>">
    </label>
    <?php
};
?>
<div id="midas" class="mt-8 rounded-2xl border border-blue-100 bg-blue-50/60 p-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Consulta MIDAS vinculada a Registro y catastro</p>
            <h3 class="mt-2 text-base font-semibold">Predios para el numeral 3 y Uso Suelo para el numeral 5</h3>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-700">
                El botón usa la referencia catastral registrada, la envía a MIDAS sin separadores, guarda la ficha Predios en este numeral
                y remite la reglamentación de Uso Suelo al capítulo 5. Servicios públicos, vías y movilidad se revisan en sus pestañas
                propias con soporte de campo o fuente vigente.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button class="btn-primary" type="submit" formaction="<?= e(url($subjectActionBase . '/midas/consultar')) ?>">
                Consultar MIDAS automáticamente
            </button>
            <a class="btn-secondary" target="_blank" rel="noopener" href="https://midas.cartagena.gov.co/#/home">Abrir MIDAS manual</a>
        </div>
    </div>
    <p class="mt-3 text-xs font-semibold text-amber-800">
        Los códigos DANE se conservan solo como trazabilidad catastral; si están rezagados, prevalece la verificación actual del predio.
    </p>
    <details class="mt-5 rounded-xl border border-slate-200 bg-white p-4" open>
        <summary class="cursor-pointer text-sm font-semibold text-slate-900">Campos MIDAS guardados para el inmueble</summary>
        <div class="mt-5 space-y-5">
            <?php foreach ($midasGroups as $title => $fields): ?>
                <section class="rounded-xl border border-slate-100 p-4">
                    <h4 class="text-sm font-semibold text-slate-800"><?= e($title) ?></h4>
                    <div class="mt-4 grid gap-4 md:grid-cols-3">
                        <?php foreach ($fields as $key => $meta) $renderMidas($key, $meta); ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    </details>
    <details class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
        <summary class="cursor-pointer text-sm font-semibold text-slate-900">Respaldo manual y trazabilidad completa</summary>
        <label class="label mt-4">Lectura completa copiada de MIDAS
            <textarea class="input min-h-40" name="midas_pasted_text" rows="7" maxlength="70000"
                placeholder="Pega el bloque Predios, Uso Suelo o el cuadro de reglamentación cuando MIDAS no responda automáticamente."></textarea>
            <span class="mt-1 block text-xs font-medium text-slate-500">
                Respaldo manual: actualiza esta ficha y también envía Uso Suelo al numeral 5 cuando el texto pegado lo contiene.
            </span>
        </label>
        <div class="mt-4 flex flex-wrap gap-3">
            <button class="btn-secondary" type="submit" formaction="<?= e(url($subjectActionBase . '/midas/procesar')) ?>">
                Procesar lectura MIDAS pegada
            </button>
            <a class="btn-secondary" href="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana#pot')) ?>">
                Ver depósito normativo del numeral 5
            </a>
        </div>
        <label class="label mt-5">Lectura predial MIDAS guardada
            <textarea class="input min-h-32" name="midas_predio_raw" rows="5" maxlength="70000"
                placeholder="Aquí queda la trazabilidad del bloque Predios procesado."><?= e($sv('midas_predio_raw')) ?></textarea>
        </label>
    </details>
</div>
