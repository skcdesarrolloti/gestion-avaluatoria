<details class="mt-2 rounded-lg border p-2">
    <summary class="min-h-11 cursor-pointer text-sm">Características y soporte · <?= e($announcement['source_name'] ?? '') ?></summary>
    <p class="whitespace-pre-wrap text-sm"><?= e(($announcement['evidence_detail'] ?? '') ?: ($announcement['comparability_notes'] ?? '')) ?></p>
    <dl class="mt-2 grid gap-2 text-sm sm:grid-cols-2">
        <?php $sourceLabels = ['source_url'=>'Enlace original','consulted_at'=>'Fecha de consulta','address_hint'=>'Dirección publicada',
            'contact_name'=>'Contacto','contact_phone'=>'Teléfono','admin_fee'=>'Administración COP','parking_spaces'=>'Parqueaderos',
            'bedrooms'=>'Alcobas','bathrooms'=>'Baños','floor_level'=>'Piso','stratum'=>'Estrato','age_years'=>'Edad publicada'];
        foreach (\App\Services\ComparableCaptureDetail::fields() as $key=>[$label]) {
            if (!in_array($key,['component_key','property_group','intake_state','location_verification','published_attributes'],true)) $sourceLabels[$key]=$label;
        }
        foreach ($sourceLabels as $key=>$label): $value=trim((string)($announcement[$key] ?? '')); if ($value==='') continue; ?>
            <div class="min-w-0"><dt class="font-semibold"><?= e($label) ?></dt><dd class="whitespace-pre-wrap break-words"><?= e($value) ?></dd></div>
        <?php endforeach; ?>
        <?php foreach (json_decode($announcement['published_attributes'] ?? '{}',true) ?: [] as $label=>$value): ?>
            <div class="min-w-0"><dt class="font-semibold">Publicado: <?= e($label) ?></dt><dd class="whitespace-pre-wrap break-words"><?= e($value) ?></dd></div>
        <?php endforeach; ?>
    </dl>
</details>
