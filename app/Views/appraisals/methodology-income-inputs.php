<?php
$incomeSteps=\App\Services\IncomeStudyNavigation::steps();
$incomeInitial=\App\Services\IncomeStudyNavigation::initial($_GET,$stage ?? '3');
$incomeSourceList=array_merge($guide['source_search']['portal_sources'] ?? [],$guide['source_search']['agency_sources'] ?? []);
$incomeSourceLabels=array_column($incomeSourceList,'label');
$incomeInitialPortal=is_string($_GET['renta_portal'] ?? null) && in_array($_GET['renta_portal'],$incomeSourceLabels,true)?$_GET['renta_portal']:($incomeSourceLabels[0] ?? '');
?>
<section data-methodology-version-group data-income-inputs
    data-income-loaded="<?= $incomeInitial==='capture'?'1':'0' ?>" data-income-capture-url="<?= e($flowUrl('3').'&renta_paso=capture') ?>"
    data-income-state="<?= e(json_encode(['incomeSteps'=>$incomeSteps,'incomeTab'=>$incomeInitial,'incomePortal'=>$incomeInitialPortal,'incomeSources'=>$incomeSourceLabels,'incomePortals'=>array_column($guide['source_search']['portal_sources'] ?? [],'label')],JSON_UNESCAPED_UNICODE)) ?>"
    x-data="incomeInputs">
    <?php require __DIR__.'/methodology-income-navigation.php'; ?>
    <nav class="mb-4 flex flex-wrap gap-2" x-show="incomeStage==='research'" <?= in_array($incomeInitial,['search','capture','analysis'],true)?'':'x-cloak' ?> aria-label="Tareas de investigación de arriendos">
        <a class="btn-secondary" href="<?= e($flowUrl('3').'&renta_paso=search') ?>" @click.prevent="openTab('search')" :aria-current="incomeTab==='search'?'page':null">Preparar la búsqueda</a>
        <a class="btn-secondary" href="<?= e($flowUrl('3').'&renta_paso=capture') ?>" @click.prevent="openTab('capture')" :aria-current="incomeTab==='capture'?'page':null">Recoger y revisar muestras</a>
        <a class="btn-secondary" href="<?= e($flowUrl('3').'&renta_paso=analysis') ?>#renta-analisis" :aria-current="incomeTab==='analysis'?'page':null">Análisis y exclusiones</a>
    </nav>
    <?php require __DIR__.'/methodology-income-portal-nav.php'; ?>
    <div id="renta-panel-concept" x-show="incomeTab==='concept'" <?= $incomeInitial==='concept'?'':'x-cloak' ?>>
        <?php require __DIR__.'/methodology-income-concept.php'; ?>
    </div>
    <div id="renta-panel-search" x-show="incomeTab==='search'" <?= $incomeInitial==='search'?'':'x-cloak' ?>>
        <?php require __DIR__.'/methodology-income-search.php'; ?>
    </div>
    <section class="mb-4 rounded-2xl border bg-white p-5" x-show="incomeTab==='evidence'" <?= $incomeInitial==='evidence'?'':'x-cloak' ?>>
        <h2 class="text-2xl font-semibold">3. Ingresos y gastos</h2>
        <p class="mt-2">Un historial documentado y supuestos compartidos por capitalización directa y FCD.</p>
        <nav class="mt-4 flex flex-wrap gap-3" aria-label="Historial, proyección y costos">
            <?php foreach (['history'=>'Historial y contrato','projection'=>'Proyección futura','costs'=>'Costos con soporte'] as $pane=>$label): ?>
            <button type="button" class="btn-secondary" :aria-current="incomePane==='<?= $pane ?>'?'page':null" @click="openIncomePane('<?= $pane ?>')"><?= e($label) ?></button>
            <?php endforeach; ?>
        </nav>
    </section>
    <?php require __DIR__.'/income-origin-editor.php'; ?>
    <section class="mb-5 min-w-0 rounded-2xl border bg-white p-5" x-show="incomeTab==='concept' || (incomeTab==='evidence' && incomePane==='history')" <?= in_array($incomeInitial,['concept','evidence'],true)?'':'x-cloak' ?>>
        <?php require __DIR__.'/lease-form.php'; ?>
    </section>
    <div id="renta-panel-capture" x-show="incomeTab==='capture'" <?= $incomeInitial==='capture'?'':'x-cloak' ?>>
        <div class="mb-4 rounded-xl border bg-white p-4" x-show="!incomeLoaded" :aria-busy="incomeLoading">
            <p role="status" aria-live="polite" x-text="incomeLoading?'Cargando las muestras de esta unidad…':'Las muestras se cargan al abrir esta pestaña.'">Las muestras se cargan al abrir esta pestaña.</p>
            <p class="mt-2 text-red-800" role="alert" x-text="incomeError"></p>
            <button type="button" class="btn-secondary mt-3" @click="openTab('capture')" :disabled="incomeLoading">Cargar muestras / reintentar</button>
            <noscript><a class="btn-secondary mt-3" href="<?= e($flowUrl('3').'&renta_paso=capture') ?>">Abrir las muestras de esta unidad</a></noscript>
        </div>
        <div x-ref="incomeCapture" data-income-capture-content>
            <?php if ($incomeInitial==='capture'): require __DIR__.'/methodology-unassigned.php'; require __DIR__.'/valuation-methodology-search.php'; endif; ?>
        </div>
    </div>
    <div x-show="incomeTab==='analysis'" <?= $incomeInitial==='analysis'?'':'x-cloak' ?>>
        <?php if ($incomeInitial==='analysis') require __DIR__.'/income-selection.php'; ?>
    </div>
    <?php require __DIR__.'/methodology-income-later-stages.php'; ?>
    <nav class="mt-5 flex flex-wrap gap-3" aria-label="Continuar el estudio de Renta">
        <button type="button" class="btn-secondary" :disabled="incomeNavigating || incomeStepIndex===0" @click="moveStep(-1)">← Anterior</button>
        <button type="button" class="btn-primary" :disabled="incomeNavigating || incomeStepIndex===4" @click="moveStep(1)">Siguiente →</button>
        <a class="btn-secondary" href="<?= e($flowUrl('plan',null,$sourceUnitKey)) ?>" x-show="incomeStage==='review'">Volver a la configuración</a>
    </nav>
</section>
