<?php
$midasKeys = ['midas_national_cadastral_reference', 'midas_property_registry', 'midas_address',
    'midas_cadastral_reference', 'midas_territory', 'midas_locality', 'midas_commune_ucg',
    'midas_land_use', 'midas_urban_treatment', 'midas_risk', 'midas_land_classification', 'midas_dane_block_code', 'midas_dane_block_side',
    'midas_block_number', 'midas_property_number', 'midas_stratum', 'midas_stratum_record', 'midas_stratum_atypical',
    'midas_stratum_observation', 'midas_building_name', 'midas_land_area_m2', 'midas_built_area_m2', 'midas_updated_on'];
?>
<div class="flex flex-wrap items-start justify-between gap-4">
    <div>
        <p class="eyebrow">MIDAS desde el numeral 3</p>
        <h3 class="mt-2 text-base font-semibold">Ficha del predio y lectura de Uso Suelo</h3>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
            Pega aquí la lectura completa de MIDAS. Si contiene el bloque Predios, se actualiza esta ficha;
            si contiene Uso Suelo o el cuadro POT, también se envía al numeral 5.
        </p>
    </div>
    <a class="btn-secondary" target="_blank" rel="noopener" href="https://midas.cartagena.gov.co/#/home">Consultar MIDAS</a>
</div>
<label class="label mt-5">Lectura completa copiada de MIDAS
    <textarea class="input min-h-48" name="midas_pasted_text" rows="9" maxlength="70000"
        placeholder="Pega el bloque Predios, Uso Suelo o el cuadro de reglamentación. Espera a que MIDAS cargue todo antes de copiar."></textarea>
    <span class="mt-1 block text-xs font-medium text-slate-500">
        MIDAS no recibe guiones en la búsqueda; usa la referencia catastral solo con números.
    </span>
</label>
<div class="mt-4 flex flex-wrap gap-3">
    <button class="btn-primary" type="submit" formaction="<?= e(url($subjectActionBase . '/midas/procesar')) ?>">
        Procesar lectura MIDAS
    </button>
    <a class="btn-secondary" href="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana#pot')) ?>">
        Ver depósito normativo del numeral 5
    </a>
</div>
<div class="mt-6 grid gap-5 md:grid-cols-3">
    <?php foreach ($midasKeys as $key) require BASE_PATH . '/app/Views/appraisals/subject-basic-field.php'; ?>
</div>
<label class="label mt-5">Lectura predial MIDAS guardada
    <textarea class="input min-h-32" name="midas_predio_raw" rows="5" maxlength="70000"
        placeholder="Aquí queda la trazabilidad del bloque Predios procesado."><?= e($sv('midas_predio_raw')) ?></textarea>
</label>
