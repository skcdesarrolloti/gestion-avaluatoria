<?php
$navigationKey = empty($components[$componentKey]['container']) ? $componentKey : '';
$navigationWorking = $stage!=='plan' && $navigationKey!=='' && isset($components[$navigationKey]);
$navigationConfig=in_array($stage,['plan','2','decision'],true);
?>
<section class="mt-5 rounded-xl border bg-white p-4" aria-label="Camino del capítulo 8">
    <p class="font-semibold">Configura el alcance y los métodos; después sigue academia → insumos → análisis → entregable.</p>
    <nav class="mt-3 flex flex-wrap gap-3" aria-label="Organización del capítulo 8">
        <a class="btn-secondary <?= $navigationConfig?'bg-teal-50 text-teal-900':'' ?>" href="<?= e($flowUrl('plan',null,'')) ?>">Configuración</a>
        <?php foreach (!$navigationWorking || ($costAcademyTheoryOnly ?? false) ? [] : ['1'=>'Academia','3'=>'Insumos','4'=>'Análisis','5'=>'Entregable'] as $navStep=>$navLabel): ?>
        <a class="btn-secondary <?= $stage===(string)$navStep?'bg-teal-50 text-teal-900':'' ?>" href="<?= e($flowUrl($navigationWorking ? (string)$navStep : 'plan',null,$navigationKey)) ?>"><?= e($navLabel) ?></a>
        <?php endforeach; ?>
    </nav>
    <?php if (!$navigationWorking): ?><p class="mt-3 text-sm text-slate-600">Elige un recorrido en la configuración para consultar su academia y trabajar sus datos.</p><?php endif; ?>
    <?php if ($navigationWorking && !($costAcademyTheoryOnly ?? false)): ?>
    <p class="mt-3 text-sm"><strong>Trabajando en:</strong> <?= e($componentLabel) ?> · <?= e($methods[$selected['method'] ?? ''] ?? 'Método pendiente') ?>.</p>
    <?php endif; ?>
</section>
