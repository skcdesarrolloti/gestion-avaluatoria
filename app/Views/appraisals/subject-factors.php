<?php
$factorUnits=array_values(array_filter($units,static fn($unit)=>$unit['unit_kind']!=='common'));
$factorRecord=$record+['factor_principal_count'=>count(array_filter($factorUnits,static fn($unit)=>$unit['unit_kind']==='property'))];
?>
<section class="rounded-2xl border bg-white p-5 sm:p-8" x-data="{ factorUnit: '<?= e($factorUnits[0]['id'] ?? '') ?>' }">
    <p class="eyebrow">3.4 · Datos de referencia para la investigación</p>
    <h2 class="mt-2 text-2xl font-semibold">Factores para investigación · módulo 8</h2>
    <p class="mt-3 text-sm">Las características ya registradas en el módulo 3 se vinculan aquí y se reutilizan en el módulo 8. Completa únicamente los datos faltantes o las clasificaciones que necesiten precisión. Desconocido queda pendiente, nunca cero.</p>
    <p class="mt-2 text-sm">Las áreas se diligencian en 3.2 y sirven de base para COP/m². Destinación es filtro. En Insumos decides qué factores investigar y proponer para el modelo.</p>
    <p class="mt-2 text-sm">Incluye los atributos observables de la calificación valuatoria actual, según el tipo de inmueble. Los que tienen otra clasificación se verifican con soporte; no se convierten los pesos ni el ajuste anterior en coeficientes de regresión. Puedes investigar todos los factores.</p>
    <?php if ($factorUnits===[]): ?><p class="mt-4">Registra primero las unidades del avalúo en el capítulo 1.</p><?php endif; ?>
    <div class="mt-4 flex flex-wrap gap-2" role="tablist" aria-label="Unidades para captura de factores">
    <?php foreach ($factorUnits as $factorUnit): ?>
        <button type="button" role="tab" class="btn-secondary" @click="factorUnit='<?= e($factorUnit['id']) ?>'" :aria-selected="factorUnit==='<?= e($factorUnit['id']) ?>'" :class="factorUnit==='<?= e($factorUnit['id']) ?>' ? 'bg-teal-50 text-teal-900' : ''"><?= e($factorUnit['label'] ?: 'Unidad '.$factorUnit['unit_index']) ?></button>
    <?php endforeach; ?>
    </div>
    <?php foreach ($factorUnits as $factorUnit):
        $captureCatalog=\App\Services\SubjectFactorCapture::catalog($factorUnit,$factorRecord,$factorScales ?? []);
        $captureSaved=\App\Services\SubjectFactorCapture::decode($factorUnit);
        $captureEndpoint=url($subjectActionBase.'/unidades/'.$factorUnit['id'].'/factores'); ?>
    <div x-show="factorUnit==='<?= e($factorUnit['id']) ?>'" class="mt-5" role="tabpanel">
        <h3 class="text-xl font-semibold"><?= e($factorUnit['label'] ?: 'Unidad '.$factorUnit['unit_index']) ?></h3>
        <p class="mt-2 text-sm"><?= count($captureCatalog) ?> factores según su tipo. Los campos sin información pueden guardarse pendientes.</p>
        <?php if ($captureCatalog===[]): ?><p class="mt-2 text-amber-900">Define primero el tipo de esta unidad en 3.1, o el tipo de anexo en 3.3. No se heredan los factores de la unidad principal.</p><?php endif; ?>
        <form method="post" action="<?= e($captureEndpoint) ?>" data-module-autosave data-save-in-place data-autosave-endpoint="<?= e($captureEndpoint) ?>" x-data="{ factorSearch: '' }">
            <?= csrf_field() ?><input type="hidden" name="version" value="<?= (int)($factorUnit['subject_factors_version'] ?? 0) ?>">
            <label class="mt-4 block font-semibold">Buscar factor en <?= e($factorUnit['label'] ?: 'esta unidad') ?><input class="input" type="search" placeholder="Ej. Vista, baños, ascensor" x-model="factorSearch" @input.stop @change.stop></label>
            <?php $captureGroups=[]; foreach ($captureCatalog as $captureKey=>$captureFactor) $captureGroups[!empty($captureFactor['supplemental'])?'Diferenciales valuatorios para investigar':($captureFactor['group'] ?? 'Características de la unidad')][$captureKey]=$captureFactor; ?>
            <?php foreach ($captureGroups as $captureGroup=>$captureFields): ?>
            <details class="mt-4 rounded-xl border p-3" <?= in_array($captureGroup,['Copropiedad PH','Diferenciales valuatorios para investigar'],true)?'':'open' ?>>
                <summary class="min-h-11 cursor-pointer font-semibold"><?= e($captureGroup) ?> · <?= count($captureFields) ?> datos</summary>
                <?php if ($captureGroup==='Copropiedad PH'): ?><p class="mt-2 text-sm">Atributos comunes que sirven a esta unidad. Conserva edificio, fuente y soporte; permiten investigar edificios similares. No se suman valores ni se asignan pesos aquí; su incorporación al modelo se resolverá en Análisis.</p><?php endif; ?>
                <?php if ($captureGroup==='Celdas de parqueo'): ?><p class="mt-2 text-sm">Cantidad y características en un mismo apartado, con datos separados. No se convierten automáticamente en un puntaje compuesto.</p><?php endif; ?>
                <?php if ($captureGroup==='Datos anteriores · revisar alcance'): ?><p class="mt-2 text-amber-900">Datos históricos conservados. No se trasladan automáticamente a la copropiedad ni a las nuevas clasificaciones; verifica su alcance.</p><?php endif; ?>
                <div class="mt-4 grid gap-3 lg:grid-cols-2"><?php foreach ($captureFields as $captureKey=>$captureFactor): require __DIR__.'/subject-factor-field.php'; endforeach; ?></div>
            </details>
            <?php endforeach; ?>
            <button type="submit" class="btn-primary mt-4">Guardar factores de <?= e($factorUnit['label'] ?: 'esta unidad') ?></button>
            <span class="mt-2 block text-sm" data-autosave-status role="status">Autoguardado activo · espera la confirmación.</span>
        </form>
    </div>
    <?php endforeach; ?>
</section>
