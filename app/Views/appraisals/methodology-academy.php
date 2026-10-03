<?php
$academyGroups=\App\Services\MethodologyAcademy::groups($components,$flow);
$academyConsult=($_GET['consult_method'] ?? '')==='costo' || ($costAcademyTheoryOnly ?? false);
$academyGeneral=($_GET['academy'] ?? '')==='general' || ($academyGroups===[] && !$academyConsult);
$academyCentralized=true;
if (!$academyConsult && !isset($academyGroups[$method]) && $academyGroups!==[]) $method=(string)array_key_first($academyGroups);
$prefix=\App\Services\MethodologyWorkflow::PREFIXES[$method];
$academyUnits=$academyGroups[$method] ?? [];
$methodologyGuides=array_values(array_filter($methodologyChapter['method_guides'],static fn($g)=>$g['key']===$method));
?>
<section class="rounded-2xl border bg-white p-5 sm:p-8">
    <p class="eyebrow">Academia del expediente</p>
    <h2 class="mt-2 text-2xl font-semibold">Una academia por método</h2>
    <p class="mt-3 text-sm leading-6">La teoría se consulta una sola vez para todas las unidades que usan el mismo método. Si un alcance tiene dos métodos, aparecen ambas academias; sus estimaciones se contrastan y no se suman automáticamente.</p>
    <nav class="mt-4 flex flex-wrap gap-2" aria-label="Métodos de la academia">
        <a class="btn-secondary <?= $academyGeneral?'bg-teal-50 text-teal-900':'' ?>" <?= $academyGeneral?'aria-current="page"':'' ?> href="<?= e($flowUrl('1',null,'').'&academy=general') ?>">General · todos los métodos</a>
        <?php foreach ($academyGroups as $academyMethod=>$group): $academyKey=(string)array_key_first($group); ?>
        <a class="btn-secondary <?= !$academyGeneral && !$academyConsult && $method===$academyMethod?'bg-teal-50 text-teal-900':'' ?>" <?= !$academyGeneral && !$academyConsult && $method===$academyMethod?'aria-current="page"':'' ?> href="<?= e($flowUrl('1',$academyMethod,$academyKey)) ?>"><?= e($methods[$academyMethod]) ?> · <?= count($group) ?> recorrido<?= count($group)===1?'':'s' ?></a>
        <?php endforeach; ?>
    </nav>
    <?php if ($academyGeneral): ?><p class="mt-4 text-sm font-semibold">Consulta común para todo el expediente. Las reglas especiales se aplican según el encargo, el método y los derechos valorados.</p>
    <?php elseif ($academyUnits!==[]): ?>
        <p class="mt-4 text-sm font-semibold">Aplicable a: <?= e(implode(' · ',array_column($academyUnits,'label'))) ?>.</p>
    <?php else: ?>
        <p class="mt-4 rounded-xl bg-teal-50 p-4 text-sm">Consulta teórica de <?= e($methods[$method]) ?>. Ninguna unidad tiene este método asignado; aquí no hay datos de una unidad que verificar.</p>
    <?php endif; ?>
</section>
<?php if ($academyGeneral): require __DIR__.'/methodology-academy-general.php';
else: ?>
<?php if ($method==='costo'): require __DIR__.'/methodology-cost-academy-reading.php';
else: require __DIR__.'/valuation-methodology-method-guides.php'; endif; ?>
<a class="mt-4 inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e($flowUrl('1',null,'').'&academy=general') ?>">Consultar principios, parámetros, informe y reglas de PH en Academia General</a>
<?php if ($academyUnits!==[]): ?>
<details class="mt-5 rounded-xl border bg-white p-4" <?= ($_GET['academy'] ?? '')==='unidad'?'open':'' ?>>
    <summary class="min-h-11 cursor-pointer font-semibold">Aplicación y verificación particular de cada unidad · <?= e($methods[$method]) ?></summary>
    <p class="mt-3 text-sm leading-6">La academia es compartida; las áreas, los derechos y los soportes de cada unidad se verifican por separado. Abre la unidad que necesitas revisar.</p>
    <nav class="mt-3 flex flex-wrap gap-2" aria-label="Unidades para aplicar la academia">
        <?php foreach ($academyUnits as $academyKey=>$academyComponent): ?>
        <a class="btn-secondary <?= $componentKey===$academyKey?'bg-teal-50 text-teal-900':'' ?>" href="<?= e($flowUrl('1',$method,$academyKey).'&academy=unidad') ?>"><?= e($academyComponent['label']) ?></a>
        <?php endforeach; ?>
    </nav>
    <?php
    if (isset($academyUnits[$componentKey])):
    $key=$componentKey;
    $component=$academyUnits[$key]; $unit=$component['unit']; $item=$flow[$key] ?? [];
    $academicMethod=$method; $orientationPh=$record['regimen_ph'] ?? '';
    $type=\App\Services\ComparableSearchContext::record($record,$units,$key)['tipo_inmueble'] ?? '';
    ?>
    <?php if ($method==='mercado'): require __DIR__.'/methodology-subject-checklist.php'; endif; ?>
    <?php if ($method==='costo'): ?>
        <?php $academySharedTheory=true; $componentKey=$key; $selected=$item; $componentLabel=$component['label']; $sourceUnitKey=$component['parent_key'] ?? $key; require __DIR__.'/methodology-cost-academy.php'; ?>
    <?php endif; ?>
    <?php require __DIR__.'/methodology-unit-reading.php'; ?>
    <a class="btn-primary mt-4" href="<?= e($flowUrl('3',$method,$key)) ?>">Continuar a insumos de <?= e($component['label']) ?></a>
    <?php else: ?><p class="mt-4 text-sm">Selecciona una de las unidades de arriba para consultar sus datos propios.</p><?php endif; ?>
</details>
<?php endif; ?>
<?php endif; ?>
