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
            <p class="eyebrow">MIDAS recibido desde el numeral 2</p>
            <h3 class="mt-2 text-base font-semibold">Datos prediales guardados para el bien sujeto</h3>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-700">
                La captura completa se hace una sola vez en el numeral 2. Aquí quedan los campos prediales
                que alimentan la identificación, áreas, ubicación, riesgos y trazabilidad del inmueble.
            </p>
        </div>
        <a class="btn-primary" href="<?= e(url('avaluos/' . $record['id'] . '/sector#midas-centro')) ?>">
            Ir a MIDAS del numeral 2
        </a>
    </div>
    <p class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs font-semibold leading-5 text-amber-900">
        Si algún dato de MIDAS no corresponde con visita, escritura, predial o certificado, no lo adoptes en silencio:
        corrígelo aquí y deja la salvedad en las observaciones del numeral aplicable.
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
        <summary class="cursor-pointer text-sm font-semibold text-slate-900">Trazabilidad predial guardada</summary>
        <label class="label mt-5">Lectura predial MIDAS guardada
            <textarea class="input min-h-32" name="midas_predio_raw" rows="5" maxlength="70000"
                placeholder="Aquí queda la trazabilidad del bloque Predios procesado desde el numeral 2."><?= e($sv('midas_predio_raw')) ?></textarea>
        </label>
    </details>
    <details class="mt-4 rounded-xl border border-amber-200 bg-white p-4">
        <summary class="cursor-pointer text-sm font-semibold text-slate-900">Registro de datos MIDAS no actualizados</summary>
        <label class="label mt-5">Datos pendientes de decisión del analista
            <textarea class="input min-h-32" name="midas_unmapped_notes" rows="6" maxlength="12000"
                placeholder="Aquí quedan rótulos o secciones de MIDAS que no tuvieron campo automático."><?= e($sv('midas_unmapped_notes')) ?></textarea>
        </label>
    </details>
</div>
