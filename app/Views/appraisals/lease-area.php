<?php
$areaNames=['area_private_m2'=>'Área privada','area_adopted_m2'=>'Superficie adoptada','built_area_adopted_m2'=>'Área construida adoptada'];
$fieldName='lease[analysis_area_reference]'; $areaError=$leaseErrors[$fieldName] ?? '';
?>
<details class="mt-4 rounded-lg bg-slate-50 p-3" <?= $areaError!==''?'open':'' ?>>
    <summary class="min-h-11 cursor-pointer font-semibold">Área y espacios para la futura comparación por m²</summary>
    <label class="label" for="lease-analysis_area_reference">Área de referencia del capítulo 3</label>
    <select class="input" id="lease-analysis_area_reference" name="<?= $fieldName ?>" x-model="values.analysis_area_reference"
        aria-describedby="lease-area-help lease-area-error" aria-invalid="<?= $areaError!==''?'true':'false' ?>" :aria-invalid="Boolean(issues['<?= $fieldName ?>'])">
        <option value="">Por definir con los soportes del capítulo 3</option>
        <?php if (!empty($leaseValues['analysis_area_reference']) && !in_array(explode(':',$leaseValues['analysis_area_reference'])[0],array_column($units,'id'),true)): ?>
        <option value="<?= e($leaseValues['analysis_area_reference']) ?>" selected>Referencia anterior conservada · revisar unidad en capítulo 3</option>
        <?php endif; ?>
        <?php foreach ($units as $areaUnit): if (($areaUnit['unit_kind'] ?? '')==='common') continue;
            foreach ($areaNames as $areaKey=>$areaLabel): $areaValue=$areaUnit['id'].':'.$areaKey; ?>
        <option value="<?= e($areaValue) ?>" <?= ($leaseValues['analysis_area_reference'] ?? '')===$areaValue?'selected':'' ?>><?= e($areaUnit['label'].' · '.$areaLabel.' · '.(($areaUnit[$areaKey] ?? '')!==''?$areaUnit[$areaKey].' m²':'pendiente en capítulo 3')) ?></option>
        <?php endforeach; endforeach; ?>
    </select>
    <p class="mt-2 text-sm" id="lease-area-help">Consulta el área en su origen. Una referencia de área no separa el canon de los anexos ni convierte toda la superficie en arrendable. Si cubre varios espacios o una parte, documenta su tratamiento.</p>
    <p id="lease-area-error" class="text-sm text-red-700" role="alert" x-text="issues['<?= $fieldName ?>'] || ''"><?= e($areaError) ?></p>
    <div class="mt-4 grid gap-4 md:grid-cols-2"><?php foreach (['analysis_area_scope','analysis_area_notes'] as $leaseField) require __DIR__.'/lease-field.php'; ?></div>
    <a class="inline-flex min-h-11 items-center text-blue-800 underline" href="<?= e(url($incomeSubject['source_url'].'&section=surface#superficies')) ?>">Consultar o completar las áreas en capítulo 3</a>
</details>
