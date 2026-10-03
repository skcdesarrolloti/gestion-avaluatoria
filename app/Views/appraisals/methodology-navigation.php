<?php
$navigationKey = empty($components[$componentKey]['container']) ? $componentKey : '';
$navigationWorking = $navigationKey!=='' && isset($components[$navigationKey]);
$navigationStep = match ($stage) {'plan'=>'plan','2','decision'=>'2','integration','report'=>'integration',default=>'development'};
?>
<section class="mt-5 rounded-xl border bg-white p-4" aria-label="Camino del capítulo 8">
    <p class="font-semibold">Tu recorrido: organizar → definir métodos → desarrollar cada parte → consolidar</p>
    <nav class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4" aria-label="Organización del capítulo 8">
        <?php foreach (['plan'=>'1 · Plan de valoración','2'=>'2 · Métodos y alcances','development'=>'3 · Desarrollo por componente','integration'=>'4 · Consolidación'] as $navStep=>$navLabel):
            $navStep=(string)$navStep;
            $navTarget = match($navStep) {'development'=>$navigationWorking?'1':'plan','2'=>$navigationWorking?'2':'plan',default=>$navStep}; ?>
        <a class="btn-secondary <?= $navigationStep===$navStep?'bg-teal-50 text-teal-900':'' ?>" <?= $navigationStep===$navStep?'aria-current="step"':'' ?> href="<?= e($flowUrl($navTarget,null,$navigationKey)) ?>"><?= e($navLabel) ?></a>
        <?php endforeach; ?>
    </nav>
    <?php if (!$navigationWorking): ?><p class="mt-3 text-sm text-slate-600">Para los pasos 2 y 3, elige una fila del plan de valoración. La consolidación reúne los recorridos activos.</p><?php endif; ?>
    <?php if ($navigationWorking && !($costAcademyTheoryOnly ?? false)): ?>
        <p class="mt-3 text-sm"><strong>Trabajando en:</strong> <?= e($componentLabel) ?> · <?= e($methods[$selected['method'] ?? ''] ?? 'Método pendiente') ?>. <a class="font-semibold text-blue-800 underline" href="<?= e($flowUrl('plan',null,'')) ?>">Cambiar de unidad o parte</a></p>
    <?php endif; ?>
    <details class="mt-3"><summary class="min-h-11 cursor-pointer text-teal-800">Herramientas y consultas del expediente</summary>
        <div class="mt-2 flex flex-wrap gap-3">
            <a class="btn-secondary" href="<?= e($flowUrl('decision')) ?>">Matriz técnica · consulta</a>
            <a class="btn-secondary" href="<?= e($flowUrl('1','costo','').'&consult_method=costo') ?>">Consultar academia C1 · Costo</a>
            <a class="btn-secondary" href="<?= e($flowUrl('3','mercado','')) ?>">Banco y muestras anteriores (<?= count(\App\Services\MethodologyComparableScope::rows($allComparableRows,'')) ?> sin asignar)</a>
            <a class="btn-secondary" href="<?= e($flowUrl('report')) ?>">Texto del numeral 8</a>
        </div>
    </details>
</section>
