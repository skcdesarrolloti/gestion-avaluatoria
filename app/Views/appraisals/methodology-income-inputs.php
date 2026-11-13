<?php
$incomeTabs=['concept'=>'1. Ficha del arriendo','search'=>'2. Preparar la búsqueda','capture'=>'3. Recoger muestras'];
$incomeInitial=is_string($_GET['renta_paso'] ?? null) && isset($incomeTabs[$_GET['renta_paso']])?$_GET['renta_paso']:'concept';
$incomeSourceList=array_merge($guide['source_search']['portal_sources'] ?? [],$guide['source_search']['agency_sources'] ?? []);
$incomeSourceLabels=array_column($incomeSourceList,'label');
$incomeInitialPortal=is_string($_GET['renta_portal'] ?? null) && in_array($_GET['renta_portal'],$incomeSourceLabels,true)?$_GET['renta_portal']:($incomeSourceLabels[0] ?? '');
?>
<section data-methodology-version-group data-income-inputs
    data-income-loaded="<?= $incomeInitial==='capture'?'1':'0' ?>" data-income-capture-url="<?= e($flowUrl('3').'&renta_paso=capture') ?>"
    data-income-state="<?= e(json_encode(['incomeTab'=>$incomeInitial,'incomePortal'=>$incomeInitialPortal,'incomeSources'=>$incomeSourceLabels,'incomePortals'=>array_column($guide['source_search']['portal_sources'] ?? [],'label')],JSON_UNESCAPED_UNICODE)) ?>"
    x-data="incomeInputs">
    <nav class="mb-5 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist" aria-label="Preparación e investigación de Renta">
        <?php $incomeKeys=array_keys($incomeTabs); foreach ($incomeKeys as $incomeIndex=>$incomeKey):
            $incomePrevious=$incomeKeys[($incomeIndex+2)%3];$incomeNext=$incomeKeys[($incomeIndex+1)%3]; ?>
        <a id="renta-tab-<?= e($incomeKey) ?>" x-ref="incomeTab<?= e($incomeKey) ?>" role="tab" aria-controls="renta-panel-<?= e($incomeKey) ?>"
            class="btn-secondary shrink-0" href="<?= e($flowUrl('3').'&renta_paso='.$incomeKey) ?>"
            aria-selected="<?= $incomeInitial===$incomeKey?'true':'false' ?>" :aria-selected="incomeTab==='<?= e($incomeKey) ?>'"
            :tabindex="incomeTab==='<?= e($incomeKey) ?>'?0:-1" :class="incomeTab==='<?= e($incomeKey) ?>'?'bg-teal-50 text-teal-900 ring-2 ring-teal-700':''"
            @click.prevent="openTab('<?= e($incomeKey) ?>')"
            @keydown.right.prevent="openTab('<?= e($incomeNext) ?>'); $refs.incomeTab<?= e($incomeNext) ?>.focus()"
            @keydown.left.prevent="openTab('<?= e($incomePrevious) ?>'); $refs.incomeTab<?= e($incomePrevious) ?>.focus()"
            @keydown.home.prevent="openTab('concept'); $refs.incomeTabconcept.focus()"
            @keydown.end.prevent="openTab('capture'); $refs.incomeTabcapture.focus()"
            @keydown.space.prevent="openTab('<?= e($incomeKey) ?>')"><?= e($incomeTabs[$incomeKey]) ?></a>
        <?php endforeach; ?>
    </nav>
    <?php require __DIR__.'/methodology-income-portal-nav.php'; ?>
    <div id="renta-panel-concept" role="tabpanel" aria-labelledby="renta-tab-concept" x-show="incomeTab==='concept'">
        <?php require __DIR__.'/methodology-income-concept.php'; ?>
        <button type="button" class="btn-primary" @click="incomeTab='search'; $refs.incomeTabsearch.focus()">Continuar a 2. Preparar la búsqueda</button>
    </div>
    <div id="renta-panel-search" role="tabpanel" aria-labelledby="renta-tab-search" x-show="incomeTab==='search'">
        <?php require __DIR__.'/methodology-income-search.php'; ?>
    </div>
    <div id="renta-panel-capture" role="tabpanel" aria-labelledby="renta-tab-capture" x-show="incomeTab==='capture'">
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
</section>
