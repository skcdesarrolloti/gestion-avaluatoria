<?php if ($componentKey === ''): ?>
<p class="rounded-xl border bg-white p-5">Selecciona el inmueble o anexo desde «Inmuebles y anexos» para guardar su método. Puedes consultar las muestras anteriores en M3 o en «Muestras sin asignar».</p>
<?php else: ?>
<?php $selectionComponent=$components[$componentKey];
$selectionSuggestion=(new \App\Services\AppraisalMethodologyComponentPlanner())->components(
    \App\Services\ComparableSearchContext::record($record,$units,$componentKey),[$selectionComponent['unit']])[0] ?? [];
$selectionHelp=\App\Services\MethodologySelectionHelp::methods($componentLabel);
$selectionTreatments=\App\Services\MethodologySelectionHelp::treatments();
?>
<p class="mb-4 rounded-lg bg-teal-50 p-3 text-sm"><strong>Sugerencia:</strong>
    <?= e(($selectionComponent['part'] ?? '')==='construccion' ? 'Costo para estudiar la construcción, con reposición y depreciación sustentadas.' : ($selectionSuggestion['method'] ?? 'Revisar la evidencia disponible.')) ?>
    La decisión es del analista; puedes contrastar con otro método.</p>
<?php if (($record['regimen_ph'] ?? '') === 'si' && $method === 'mercado'): ?>
<details class="mb-4"><summary class="min-h-11 cursor-pointer font-semibold">Composición y derechos de la unidad PH</summary>
    <?php require __DIR__ . '/methodology-ph-scope.php'; ?>
</details>
<?php endif; ?>
<form x-data="<?= e(json_encode(['chosenMethod'=>$selected['method'] ?? '', 'chosenTreatment'=>$selected['treatment'] ?? '',
    'methodHelp'=>$selectionHelp,'treatmentHelp'=>$selectionTreatments,'reasonText'=>$selected['reason'] ?? '', 'coverageText'=>$selected['coverage'] ?? ''],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)) ?>" method="post" action="<?= e(url($basePath . '/flujo')) ?>" data-module-autosave data-save-in-place
    data-autosave-endpoint="<?= e(url($basePath . '/flujo')) ?>" class="rounded-2xl border bg-white p-5 sm:p-8">
    <?= csrf_field() ?>
    <input type="hidden" name="component" value="<?= e($componentKey) ?>">
    <input type="hidden" name="version" value="<?= (int) ($record['methodology_version'] ?? 0) ?>">
    <h2 class="text-xl font-semibold">Método y alcance · <?= e($componentLabel) ?></h2>
    <p class="mt-3 text-sm text-slate-600">La selección es del perito. Un terreno no se asigna automáticamente a Residual; la evidencia y la finalidad determinan el método.</p>
    <div class="mt-5 grid gap-5 sm:grid-cols-2">
        <label class="block font-semibold">Método del componente
            <select name="method" aria-label="Método del componente" aria-describedby="method-help-<?= e($componentKey) ?>" class="input" x-model="chosenMethod"><option value="">Selecciona el método</option>
            <?php foreach ($methods as $key => $label): if (isset($selectionComponent['alternate_method']) && $key!==$selectionComponent['alternate_method']) continue; ?><option value="<?= e($key) ?>" <?= ($selected['method'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
            </select>
            <span id="method-help-<?= e($componentKey) ?>" class="mt-2 block text-sm font-normal leading-6 text-slate-600" x-text="methodHelp[chosenMethod]?.concept || 'Elige el método según el alcance y la evidencia disponible.'"><?= e($selectionHelp[$selected['method'] ?? '']['concept'] ?? 'Elige el método según el alcance y la evidencia disponible.') ?></span>
        </label>
        <label class="block font-semibold">Tratamiento en la integración
            <select name="treatment" aria-label="Tratamiento en la integración" aria-describedby="treatment-help-<?= e($componentKey) ?>" class="input" x-model="chosenTreatment"><option value="">Selecciona el tratamiento</option>
            <?php foreach (['separado' => 'Valor separado', 'integrado' => 'Incluido en la unidad principal', 'descriptivo' => 'Solo descriptivo'] as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= ($selected['treatment'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
            </select>
            <span id="treatment-help-<?= e($componentKey) ?>" class="mt-2 block text-sm font-normal leading-6 text-slate-600" x-text="treatmentHelp[chosenTreatment] || 'Define cómo se presentará este alcance al revisar los resultados.'"><?= e($selectionTreatments[$selected['treatment'] ?? ''] ?? 'Define cómo se presentará este alcance al revisar los resultados.') ?></span>
        </label>
    </div>
    <?php if (!isset($selectionComponent['alternate_method'])): ?>
    <fieldset class="mt-4"><legend class="font-semibold">Otros métodos para contrastar este mismo alcance (opcional)</legend>
        <input type="hidden" name="additional_methods_present" value="1">
        <div class="mt-2 flex flex-wrap gap-4">
        <?php foreach ($methods as $otherKey=>$otherLabel): ?>
        <label class="inline-flex min-h-11 items-center gap-2" x-show="chosenMethod !== '<?= e($otherKey) ?>'">
            <input type="checkbox" name="additional_methods[]" value="<?= e($otherKey) ?>" <?= in_array($otherKey,$selected['additional_methods'] ?? [],true)?'checked':'' ?>> <?= e($otherLabel) ?>
        </label>
        <?php endforeach; ?></div>
        <p class="mt-2 text-xs">Cada método conserva un recorrido propio. Sus valores se contrastan; no se suman ni se promedian automáticamente. Desmarcar conserva los datos del recorrido inactivo.</p>
    </fieldset>
    <?php else: ?><p class="mt-4 text-sm">Este es un contraste del mismo alcance. Configura los métodos activos desde la configuración del inmueble.</p><?php endif; ?>
    <details class="mt-4"><summary class="min-h-11 cursor-pointer font-semibold">Alcance, justificación y soporte del método</summary>
    <label class="mt-5 block font-semibold">Justificación de la selección
        <textarea name="reason" x-ref="reason" x-model="reasonText" maxlength="2000" class="input" rows="4" placeholder="Explica la evidencia disponible y por qué este método corresponde al componente."><?= e($selected['reason'] ?? '') ?></textarea>
    </label>
    <div class="mt-2 rounded-lg bg-teal-50 p-3 text-sm" x-show="methodHelp[chosenMethod] && reasonText.trim() === ''" <?= empty($selected['method']) || !empty($selected['reason'])?'x-cloak':'' ?>>
        <p class="font-semibold">Texto sugerido para adaptar a la evidencia</p>
        <p class="mt-1 leading-6" x-text="methodHelp[chosenMethod]?.reason"></p>
        <button type="button" class="btn-secondary mt-2" :disabled="!methodHelp[chosenMethod] || reasonText.trim() !== ''" @click="reasonText=methodHelp[chosenMethod].reason; $nextTick(() => $refs.reason.dispatchEvent(new Event('input', { bubbles: true })))">Usar sugerencia de justificación</button>
    </div>
    <label class="mt-5 block font-semibold">Alcance y control de doble conteo
        <textarea name="coverage" x-ref="coverage" x-model="coverageText" maxlength="2000" class="input" rows="4" placeholder="Indica qué incluye y excluye el valor; si está integrado, identifica el componente que lo contiene."><?= e($selected['coverage'] ?? '') ?></textarea>
    </label>
    <div class="mt-2 rounded-lg bg-teal-50 p-3 text-sm" x-show="methodHelp[chosenMethod] && coverageText.trim() === ''" <?= empty($selected['method']) || !empty($selected['coverage'])?'x-cloak':'' ?>>
        <p class="font-semibold">Texto sugerido del alcance</p>
        <p class="mt-1 leading-6" x-text="[methodHelp[chosenMethod]?.coverage, treatmentHelp[chosenTreatment]].filter(Boolean).join(' ')"></p>
        <button type="button" class="btn-secondary mt-2" :disabled="!methodHelp[chosenMethod] || coverageText.trim() !== ''" @click="coverageText=[methodHelp[chosenMethod].coverage, treatmentHelp[chosenTreatment]].filter(Boolean).join(' '); $nextTick(() => $refs.coverage.dispatchEvent(new Event('input', { bubbles: true })))">Usar sugerencia de alcance</button>
    </div>
    <p class="mt-2 text-xs leading-5">Las sugerencias son borradores, no acreditan datos ni evidencia. Revisa y completa los soportes del caso. Los botones sólo rellenan campos vacíos para conservar lo que ya escribiste.</p>
    <?php require __DIR__.'/methodology-cost-scope.php'; ?>
    </details>
    <p class="mt-3 text-sm" data-autosave-status>Autoguardado activo · máximo 2000 caracteres por explicación.</p>
    <button type="submit" class="btn-primary mt-4">Guardar ahora</button>
    <a class="btn-secondary mt-4" href="<?= e($flowUrl('plan',null,$selectionComponent['unit']['id'])) ?>">Actualizar configuración y recorridos</a>
</form>
<?php endif; ?>
