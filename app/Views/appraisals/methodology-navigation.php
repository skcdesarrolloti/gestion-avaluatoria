<?php
$navigationKey = empty($components[$componentKey]['container']) ? $componentKey : '';
$navigationWorking = $stage!=='plan' && $navigationKey!=='' && isset($components[$navigationKey]);
$navigationConfig=in_array($stage,['plan','2','decision'],true);
$navigationGeneral=$stage==='1' && (($_GET['academy'] ?? '')==='general' || (\App\Services\MethodologyAcademy::groups($components,$flow)===[] && !($costAcademyTheoryOnly ?? false)));
$navigationGroups=\App\Services\MethodologyAcademy::groups($components,$flow);
$navigationMethod=$flow[$navigationKey]['method'] ?? '';
if (!isset($methods[$navigationMethod])) {
    $navigationMethod=isset($navigationGroups[$method]) ? $method : (array_key_first($navigationGroups) ?? '');
    $navigationKey=array_key_first($navigationGroups[$navigationMethod] ?? []) ?? '';
}
$navigationHasRoute=$navigationKey!=='' && isset($methods[$navigationMethod]);
$navigationMarket = $method === 'mercado';
?>
<section class="mt-5 rounded-xl border bg-white p-4" aria-label="Camino del capítulo 8">
    <p class="font-semibold"><?= $navigationMarket ? 'Configuración → Análisis → Entregable. La captura y la academia están dentro del recorrido.' : 'Configura el alcance y los métodos; después sigue academia → insumos → análisis → entregable.' ?></p>
    <nav class="mt-3 flex flex-wrap gap-3" aria-label="Organización del capítulo 8">
        <a class="btn-secondary <?= $navigationConfig?'bg-teal-50 text-teal-900':'' ?>" href="<?= e($flowUrl('plan',null,'')) ?>">Configuración</a>
        <?php if (!$navigationMarket): ?><a class="btn-secondary <?= $stage==='1'?'bg-teal-50 text-teal-900':'' ?>" href="<?= e($navigationConfig || $navigationGeneral?$flowUrl('1',null,'').'&academy=general':$flowUrl('1',null,$navigationKey)) ?>">Academia</a><?php endif; ?>
        <?php foreach (($navigationMarket ? ['4'=>'Análisis','5'=>'Entregable'] : ['3'=>'Insumos','4'=>'Análisis','5'=>'Entregable']) as $navStep=>$navLabel): ?>
        <a class="btn-secondary <?= ($stage===(string)$navStep || ($navigationMarket && $navStep==='4' && $stage==='3'))?'bg-teal-50 text-teal-900':'' ?>" href="<?= e($flowUrl($navigationHasRoute ? (string)$navStep : 'plan',$navigationHasRoute ? $navigationMethod : null,$navigationKey)) ?>"><?= e($navLabel) ?></a>
        <?php endforeach; ?>
    </nav>
    <?php if ($navigationMarket && $navigationConfig): ?><details class="mt-3"><summary class="min-h-11 cursor-pointer font-semibold">Academia de configuración · alcance y fuentes generales</summary>
        <a class="btn-secondary" href="<?= e($flowUrl('1',null,'').'&academy=general') ?>">Consultar fundamentos generales del expediente</a>
    </details><?php endif; ?>
    <?php if (!$navigationWorking): ?><p class="mt-3 text-sm text-slate-600">La academia reúne los métodos elegidos en la configuración. Los insumos se trabajan por unidad y método.</p><?php endif; ?>
    <?php if (!$navigationWorking || $navigationGeneral): ?>
    <p class="mt-2 text-sm text-slate-600"><?= $navigationHasRoute ? 'Los pasos siguientes abren '.e($components[$navigationKey]['label']).' · '.e($methods[$navigationMethod]).'. Puedes cambiar de unidad dentro del paso.' : 'Para trabajar los pasos siguientes, primero elige un método en Configuración.' ?></p>
    <?php endif; ?>
    <?php if ($navigationWorking && !($costAcademyTheoryOnly ?? false) && $stage!=='1'): ?>
    <p class="mt-3 text-sm"><strong>Trabajando en:</strong> <?= e($componentLabel) ?> · <?= e($methods[$selected['method'] ?? ''] ?? 'Método pendiente') ?>.</p>
    <?php endif; ?>
</section>
