<?php
$scopeUnit = $components[$componentKey]['unit'];
$scopeData = \App\Services\MarketSubjectEvidence::decode($scopeUnit);
$scopeAnnex = ($scopeUnit['unit_kind'] ?? '') === 'annex';
$scopeUrl = url($basePath . '/unidades/' . $componentKey . '/alcance-ph');
$scopeCheck = \App\Services\MarketPhScope::row($scopeUnit, $units);
$scopeTone = ['ok'=>'bg-emerald-100 text-emerald-950','missing'=>'bg-red-100 text-red-950','difference'=>'bg-amber-100 text-amber-950'];
?>
<section id="alcance-ph" class="mb-5 scroll-mt-6 rounded-2xl border border-teal-200 bg-teal-50 p-5 sm:p-8">
    <h3 class="text-xl font-semibold">PH · vínculo y composición de <?= e($componentLabel) ?></h3>
    <p class="mt-2 text-sm">Se conservan los datos del numeral 3. Aquí defines qué principal contiene cada anexo y cómo se relacionan sus áreas. La naturaleza y su soporte se guardan en la misma ficha jurídica; no se crean unidades ni se reparten valores.</p>
    <p class="mt-3 rounded-lg p-3 text-sm <?= e($scopeTone[$scopeCheck['state']]) ?>"><?= e($scopeCheck['value']) ?>. <?= e($scopeCheck['message']) ?></p>
    <form class="mt-4 grid min-w-0 gap-4 md:grid-cols-2" method="post" action="<?= e($scopeUrl) ?>"
        data-module-autosave data-save-in-place data-autosave-endpoint="<?= e($scopeUrl) ?>"
        data-autosave-topic="<?= e('appraisal:' . $record['id'] . ':market-evidence:' . $componentKey) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="version" value="<?= (int) ($scopeUnit['market_evidence_version'] ?? 0) ?>">
        <label class="label">Naturaleza jurídica según documento
            <select class="input" name="ph_scope[legal_nature]">
                <?php foreach (\App\Services\MarketPhScope::NATURES as $scopeValue=>$scopeLabel): ?>
                <option value="<?= e($scopeValue) ?>" <?= ($scopeData['legal_nature'] ?? '') === $scopeValue ? 'selected' : '' ?>><?= e($scopeLabel) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="text-xs font-normal">La ausencia de matrícula propia no acredita por sí sola que sea común de uso exclusivo.</span>
        </label>
        <label class="label">Soporte de la naturaleza y del vínculo jurídico
            <textarea class="input" name="ph_scope[legal_source]" maxlength="1200" rows="3" placeholder="Escritura, CTL, reglamento o plano aprobado; fecha y página que identifica esta unidad."><?= e($scopeData['legal_source'] ?? '') ?></textarea>
        </label>
        <?php if ($scopeAnnex): ?>
        <label class="label">Unidad principal a la que pertenece este anexo
            <select class="input" name="ph_scope[parent_unit_id]">
                <option value="">Selecciona la principal según soporte</option>
                <?php foreach ($components as $parentKey=>$parentComponent): if (($parentComponent['unit']['unit_kind'] ?? '') !== 'property') continue; ?>
                <option value="<?= e($parentKey) ?>" <?= ($scopeData['parent_unit_id'] ?? '') === $parentKey ? 'selected' : '' ?>><?= e($parentComponent['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label">Relación con el área privada registrada de la principal
            <select class="input" name="ph_scope[area_in_parent]">
                <?php foreach (\App\Services\MarketPhScope::AREAS as $scopeValue=>$scopeLabel): ?>
                <option value="<?= e($scopeValue) ?>" <?= ($scopeData['area_in_parent'] ?? '') === $scopeValue ? 'selected' : '' ?>><?= e($scopeLabel) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="text-xs font-normal">Esta declaración no cambia el área del numeral 3. Si el anexo ya está incluido, no lo sumes nuevamente.</span>
        </label>
        <label class="label">Soporte de la composición del área
            <textarea class="input" name="ph_scope[parent_area_source]" maxlength="1200" rows="3" placeholder="Documento y página donde constan áreas de oficina, garaje y depósito y su inclusión o exclusión."><?= e($scopeData['parent_area_source'] ?? '') ?></textarea>
        </label>
        <label class="label">Explicación de las áreas comprendidas
            <textarea class="input" name="ph_scope[parent_area_note]" maxlength="1200" rows="3" placeholder="Explica qué comprende el área privada de la principal y qué superficie corresponde al anexo."><?= e($scopeData['parent_area_note'] ?? '') ?></textarea>
        </label>
        <?php endif; ?>
        <div class="flex flex-wrap items-center justify-between gap-3 md:col-span-2">
            <span class="text-sm" data-autosave-status>Autoguardado activo</span>
            <button class="btn-primary" type="submit">Guardar alcance PH</button>
        </div>
    </form>
    <p class="mt-4 text-sm">Para una parte privada integrada o común de uso exclusivo, selecciona abajo «Incluido en la unidad principal». Una unidad con matrícula independiente puede tener valor separado. La depuración de los comparables se desarrollará en M4; aquí no se calcula ningún descuento.</p>
    <?php if ($scopeAnnex && in_array($scopeData['legal_nature'] ?? '', ['integrada','comun_exclusivo'], true) && ($selected['treatment'] ?? '') === 'separado'): ?>
    <p class="mt-2 rounded-lg bg-amber-100 p-3 text-sm text-amber-950">Diferencia pendiente: el tratamiento guardado es «Valor separado» y la naturaleza exige inclusión en la principal. Revisa el tratamiento abajo; no se cambia automáticamente.</p>
    <?php endif; ?>
    <div class="mt-3 flex flex-wrap gap-3">
        <a class="btn-secondary" href="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto?' . http_build_query(['section'=>'tipologias','unit'=>$componentKey,'from'=>'metodologia','check_component'=>$componentKey]) . '#ficha-basica')) ?>">Consultar datos y soportes en numeral 3</a>
        <a class="btn-secondary" href="<?= e($flowUrl('1')) ?>">Actualizar verificación M1</a>
    </div>
    <?php if (!$scopeAnnex): require __DIR__ . '/methodology-ph-children.php'; endif; ?>
</section>
