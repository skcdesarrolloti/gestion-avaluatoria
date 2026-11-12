<?php
use App\Services\{IncomeConcept,IncomeConceptAction};
if ($componentKey!=='' && ($selected['method'] ?? '')==='renta'):
$incomeOld=json_decode(\App\Core\Session::pullFlash(IncomeConceptAction::flashKey($record['id'],$componentKey)) ?: '{}',true) ?: [];
$incomeValues=array_replace($selected['income_concept'] ?? [],$incomeOld['values'] ?? []);
$incomeErrors=$incomeOld['errors'] ?? [];
$incomeEndpoint=url($basePath.'/renta/concepto');
$incomeState=['occupancy'=>$incomeValues['occupancy'] ?? '', 'scope'=>$incomeValues['scope'] ?? '',
    'support'=>$incomeValues['support'] ?? '', 'issues'=>(object)$incomeErrors];
?>
<section id="renta-concepto" class="mb-6 rounded-2xl border bg-white p-5 sm:p-8">
    <p class="eyebrow">Renta · Preparación conceptual · <?= e($componentLabel) ?></p>
    <h2 class="mt-2 text-2xl font-semibold">1. Comprender el inmueble y su arriendo</h2>
    <p class="mt-3 leading-6">Empieza con estas tres preguntas. Si falta información, puedes dejarla pendiente y continuar después. Aquí todavía no se solicitan importes ni tasas.</p>
    <aside class="mt-4 rounded-xl bg-teal-50 p-4" aria-labelledby="renta-concepto-utilidad">
        <h3 id="renta-concepto-utilidad" class="font-semibold">¿Para qué sirve esta información en el informe?</h3>
        <p class="mt-2 text-sm leading-6">Estas respuestas preparan la descripción de la ocupación, la parte del inmueble que se estudiará y los soportes disponibles o pendientes de verificar. Se guardan como borrador en el expediente; todavía no se incorporan automáticamente al informe final.</p>
        <p class="mt-2 text-sm leading-6"><strong>No modifican los filtros de búsqueda en portales.</strong> Los criterios para buscar arriendos comparables se preparan por separado.</p>
    </aside>
    <p class="mt-2 text-sm text-slate-600">Las opciones ayudan a describir el caso; no son categorías impuestas literalmente por las normas. El signo (?) explica cada pregunta.</p>
    <a class="mt-3 inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e($flowUrl('1')) ?>#renta-orientacion">Consultar orientación y resumen en Academia de Renta</a>
    <form id="income-concept-form" class="mt-5 space-y-5" method="post" action="<?= e($incomeEndpoint) ?>"
        data-module-autosave data-save-in-place data-workflow-version data-autosave-endpoint="<?= e($incomeEndpoint) ?>"
        x-data="<?= e(json_encode($incomeState,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)) ?>" @ga:validation="issues = $event.detail">
        <?= csrf_field() ?>
        <input type="hidden" name="component" value="<?= e($componentKey) ?>">
        <input type="hidden" name="version" value="<?= (int)($incomeOld['version'] ?? $record['methodology_version'] ?? 0) ?>">
        <?php if (!empty($incomeOld['message'])): ?><p role="alert" class="rounded-xl bg-amber-50 p-4"><?= e($incomeOld['message']) ?></p><?php endif; ?>
        <?php foreach (IncomeConcept::questions() as $name=>$question): $field='income_concept['.$name.']'; $fieldId='income-'.$name; ?>
        <div class="rounded-xl border p-4 sm:p-5">
            <label for="<?= e($fieldId) ?>" class="block font-semibold"><?= e($question['label']) ?></label>
            <select id="<?= e($fieldId) ?>" name="<?= e($field) ?>" class="input" x-model="<?= e($name) ?>"
                aria-describedby="<?= e($fieldId) ?>-hint <?= e($fieldId) ?>-error"
                aria-invalid="<?= isset($incomeErrors[$field])?'true':'false' ?>" :aria-invalid="Boolean(issues[<?= e(json_encode($field)) ?>])">
                <option value="">Selecciona una opción</option>
                <?php if (($incomeValues[$name] ?? '')!=='' && !isset($question['values'][$incomeValues[$name]])): ?>
                <option value="<?= e($incomeValues[$name]) ?>" selected>Respuesta no admitida: selecciona una opción válida</option>
                <?php endif; ?>
                <?php foreach ($question['values'] as $value=>$label): ?><option value="<?= e($value) ?>" <?= ($incomeValues[$name] ?? '')===$value?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?>
            </select>
            <p id="<?= e($fieldId) ?>-hint" class="mt-2 text-sm text-slate-600"><?= e($question['meaning']) ?></p>
            <p id="<?= e($fieldId) ?>-error" class="mt-2 text-sm text-red-800" role="alert" x-text="issues[<?= e(json_encode($field)) ?>] || ''"><?= e($incomeErrors[$field] ?? '') ?></p>
            <?php require __DIR__.'/methodology-income-help.php'; ?>
            <?php foreach (IncomeConcept::texts() as $textKey=>[$textLabel,$placeholder,$condition]):
                if (!str_starts_with($textKey,$name.'_')) continue;
                $textField='income_concept['.$textKey.']'; ?>
            <div class="mt-4" x-show="<?= e($condition) ?>">
                <label class="block font-semibold" for="income-<?= e($textKey) ?>"><?= e($textLabel) ?></label>
                <textarea class="input" id="income-<?= e($textKey) ?>" name="<?= e($textField) ?>" rows="3" maxlength="1000"
                    placeholder="<?= e($placeholder) ?>" aria-describedby="income-<?= e($textKey) ?>-hint income-<?= e($textKey) ?>-error"
                    aria-invalid="<?= isset($incomeErrors[$textField])?'true':'false' ?>" :aria-invalid="Boolean(issues[<?= e(json_encode($textField)) ?>])"><?= e($incomeValues[$textKey] ?? '') ?></textarea>
                <p id="income-<?= e($textKey) ?>-hint" class="mt-1 text-sm text-slate-600">Opcional en el borrador · máximo 1000 caracteres. Completa si corresponde a tu respuesta; cambiar la opción conserva esta nota.</p>
                <p id="income-<?= e($textKey) ?>-error" class="mt-2 text-sm text-red-800" role="alert" x-text="issues[<?= e(json_encode($textField)) ?>] || ''"><?= e($incomeErrors[$textField] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
            <?php if ($name==='occupancy'): ?>
            <p class="mt-3 rounded-lg bg-teal-50 p-3 text-sm" x-show="occupancy === 'owner'">Si lo ocupa el propietario, más adelante se investigará una renta de mercado. Esta respuesta no acredita un contrato ni un ingreso cobrado.</p>
            <?php elseif ($name==='support'): ?>
            <p class="mt-3 rounded-lg bg-amber-50 p-3 text-sm" x-show="['verbal','none','pending'].includes(support)">Queda pendiente corroborar la información disponible. Si no hay contrato porque no está arrendado, la investigación de mercado se preparará después.</p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <div class="rounded-xl bg-slate-50 p-4">
            <h3 class="font-semibold">Tu preparación hasta aquí</h3>
            <?php foreach (IncomeConcept::questions() as $name=>$question): ?>
            <p class="mt-2 text-sm"><strong><?= e($question['label']) ?></strong>
                <span x-text="<?= e(json_encode($question['values'],JSON_UNESCAPED_UNICODE)) ?>[<?= e($name) ?>] || 'Sin responder todavía'"><?= e($question['values'][$incomeValues[$name] ?? ''] ?? 'Sin responder todavía') ?></span></p>
            <?php endforeach; ?>
            <p class="mt-3 text-sm">Es un borrador conceptual: las selecciones no verifican documentos ni confirman la viabilidad del método. Las muestras de arriendo existentes están en la pestaña «3. Recoger muestras».</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="btn-primary">Guardar ahora / reintentar</button>
            <p data-autosave-status role="status" aria-live="polite" class="text-sm">Sin cambios pendientes. Los cambios se autoguardan como borrador.</p>
        </div>
        <p class="text-sm text-slate-600">El guardado se confirma después de la respuesta del servidor. Sin conexión, conserva esta página abierta; cerrar sin confirmar puede perder cambios.</p>
    </form>
</section>
<?php else: ?>
<p class="mb-5 rounded-xl border bg-white p-5">Selecciona Renta para una unidad en Configuración antes de diligenciar su preparación conceptual.</p>
<?php endif; ?>
