<?php
use App\Services\{IncomeConcept,IncomeConceptAction,IncomePreparationNotes,IncomePreparationQuestions,IncomeSubjectContext};
if ($componentKey!=='' && ($selected['method'] ?? '')==='renta'):
$incomeOld=json_decode(\App\Core\Session::pullFlash(IncomeConceptAction::flashKey($record['id'],$componentKey)) ?: '{}',true) ?: [];
$incomeValues=array_replace($selected['income_concept'] ?? [],$incomeOld['values'] ?? []);
$incomeErrors=$incomeOld['errors'] ?? [];
$incomeEndpoint=url($basePath.'/renta/concepto');
$incomeSubject=IncomeSubjectContext::build($record,$subject ?? [],$units ?? [],$components[$componentKey] ?? [],$phProfile ?? []);
$incomeEffectiveOccupancy=($incomeValues['occupancy'] ?? '') ?: $incomeSubject['occupancy'];
$incomeConflict=IncomeSubjectContext::occupancyConflict($incomeSubject['occupancy'],(string)($incomeValues['occupancy'] ?? ''));
$incomeGroups=['subject'=>'A. Inmueble y alcance','lease'=>'B. Ocupación y contrato',
    'evidence'=>'C. Gastos y antecedentes','summary'=>'D. Preparación reunida'];
$incomeGroupFields=[];
foreach (IncomeConcept::questions() as $key=>$definition) $incomeGroupFields[$key]=IncomePreparationQuestions::group($key);
foreach (IncomeConcept::texts() as $key=>$definition) $incomeGroupFields[$key]=IncomePreparationNotes::group($key);
$incomeSection=is_string($_GET['renta_tema'] ?? null) && isset($incomeGroups[$_GET['renta_tema']])?$_GET['renta_tema']:'subject';
$incomeState=['values'=>array_replace(array_fill_keys(array_keys($incomeGroupFields),''),$incomeValues),
    'issues'=>(object)$incomeErrors,'section'=>$incomeSection,'fieldGroups'=>$incomeGroupFields,'sourceOccupancy'=>$incomeSubject['occupancy']];
?>
<section id="renta-concepto" class="mb-6 rounded-2xl border bg-white p-5 sm:p-8" data-income-preparation-version="2">
    <p class="eyebrow">Renta · Preparación conceptual · <?= e($componentLabel) ?></p>
    <h2 class="mt-2 text-2xl font-semibold">1. Comprender el inmueble y su arriendo</h2>
    <p class="mt-3 leading-6">Consulta lo que ya consta en el expediente y completa lo que falta para comprender el inmueble urbano y su arriendo.</p>
    <p class="mt-3 rounded-lg bg-teal-50 p-3 font-semibold">Preparación común: capitalización directa y flujo de caja descontado.</p>
    <p class="mt-3 text-sm">Las respuestas preparan la descripción y los soportes del estudio. Se guardan como borrador por recorrido; no calculan valores, no se incorporan automáticamente al informe final ni modifican filtros de portales.</p>
    <a class="mt-2 inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e($flowUrl('1')) ?>#renta-orientacion">Consultar los cuatro temas de Academia y sus soportes</a>
    <form id="income-concept-form" class="mt-4" method="post" action="<?= e($incomeEndpoint) ?>"
        data-module-autosave data-save-in-place data-workflow-version data-autosave-endpoint="<?= e($incomeEndpoint) ?>"
        data-income-preparation="<?= e(json_encode($incomeState,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)) ?>"
        x-data="preparation()" @ga:validation="validation($event.detail)">
        <?= csrf_field() ?>
        <input type="hidden" name="component" value="<?= e($componentKey) ?>">
        <input type="hidden" name="version" value="<?= (int)($incomeOld['version'] ?? $record['methodology_version'] ?? 0) ?>">
        <input type="hidden" name="income_section" value="<?= e($incomeSection) ?>" :value="section">
        <?php if (!empty($incomeOld['message'])): ?><p role="alert" class="mb-4 rounded-xl bg-amber-50 p-4"><?= e($incomeOld['message']) ?></p><?php endif; ?>
        <nav class="mb-5 flex flex-wrap gap-2" aria-label="Temas para comprender el inmueble">
            <?php foreach ($incomeGroups as $groupKey=>$groupLabel): ?>
            <button type="button" class="btn-secondary" @click="openSection('<?= e($groupKey) ?>')"
                :aria-current="section==='<?= e($groupKey) ?>'?'step':null"
                :class="section==='<?= e($groupKey) ?>'?'bg-teal-50 text-teal-900 ring-2 ring-teal-700':''"><?= e($groupLabel) ?></button>
            <?php endforeach; ?>
        </nav>
        <?php foreach ($incomeGroups as $incomeGroup=>$incomeGroupLabel): ?>
        <section id="income-section-<?= e($incomeGroup) ?>" class="space-y-4" x-show="section==='<?= e($incomeGroup) ?>'" aria-label="<?= e($incomeGroupLabel) ?>">
            <?php if ($incomeGroup==='subject'): require __DIR__.'/methodology-income-origin.php'; endif; ?>
            <?php if ($incomeGroup==='lease'): require __DIR__.'/methodology-income-lease-context.php'; endif; ?>
            <?php if ($incomeGroup==='evidence'): ?>
            <h3 class="text-xl font-semibold">Responsabilidades y antecedentes disponibles</h3>
            <p>Identifica quién asume finalmente cada gasto y con qué soporte. La cuota de administración no prueba por sí sola quién debe soportarla.</p>
            <p class="rounded-lg bg-teal-50 p-3 text-sm">Sin registro no significa cero vacancia, cero mora ni cero gasto. La mora de expensas de PH es distinta de la falta de pago del arriendo.</p>
            <?php endif; ?>
            <?php if ($incomeGroup==='summary'): require __DIR__.'/methodology-income-summary.php'; endif; ?>
            <?php foreach (IncomeConcept::questions() as $name=>$question):
                if (IncomePreparationQuestions::group($name)!==$incomeGroup) continue;
                if ($name==='occupancy' && $incomeSubject['occupancy']!=='' && empty($incomeValues['occupancy']) && !isset($incomeErrors['income_concept[occupancy]'])) continue;
                require __DIR__.'/methodology-income-question.php';
            endforeach; ?>
            <?php foreach (IncomeConcept::texts() as $textKey=>$textDefinition):
                if (IncomePreparationNotes::group($textKey)!==$incomeGroup || IncomePreparationNotes::parent($textKey)!=='') continue;
                require __DIR__.'/methodology-income-note.php';
            endforeach; ?>
            <?php $incomeGroupKeys=array_keys($incomeGroups); $incomeFollowing=$incomeGroupKeys[array_search($incomeGroup,$incomeGroupKeys,true)+1] ?? ''; ?>
            <?php if ($incomeFollowing!==''): ?><button class="btn-secondary mt-4" type="button" @click="openSection('<?= e($incomeFollowing) ?>')">Continuar a <?= e($incomeGroups[$incomeFollowing]) ?></button><?php endif; ?>
        </section>
        <?php endforeach; ?>
        <div class="mt-6 flex flex-wrap items-center gap-3 border-t pt-4">
            <button type="submit" class="btn-primary">Guardar ahora / reintentar</button>
            <p data-autosave-status role="status" aria-live="polite" class="text-sm">Sin cambios pendientes. Los cambios se autoguardan como borrador.</p>
        </div>
        <p class="mt-3 text-sm text-slate-600">El guardado se confirma después de la respuesta del servidor. Sin conexión, conserva esta página abierta; cerrar sin confirmar puede perder cambios.</p>
    </form>
</section>
<?php else: ?>
<p class="mb-5 rounded-xl border bg-white p-5">Selecciona Renta para una unidad en Configuración antes de diligenciar su preparación conceptual.</p>
<?php endif; ?>
