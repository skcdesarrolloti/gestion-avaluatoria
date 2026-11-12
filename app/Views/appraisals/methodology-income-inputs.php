<?php
$incomeTabs=['concept'=>'1. Comprender el inmueble','search'=>'2. Preparar la búsqueda','capture'=>'3. Recoger muestras'];
$incomeInitial=array_key_exists((string)($_GET['renta_paso'] ?? ''),$incomeTabs)?(string)$_GET['renta_paso']:'concept';
$incomeSourceList=array_merge($guide['source_search']['portal_sources'] ?? [],$guide['source_search']['agency_sources'] ?? []);
?>
<section data-methodology-version-group data-income-inputs
    x-data="<?= e(json_encode(['incomeTab'=>$incomeInitial,'incomePortal'=>$incomeSourceList[0]['label'] ?? ''],JSON_UNESCAPED_UNICODE)) ?>"
    x-init="if (location.hash==='#renta-concepto') incomeTab='concept'; if (location.hash==='#renta-busqueda') incomeTab='search'; if (location.hash==='#captura-83') incomeTab='capture'">
    <nav class="mb-5 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist" aria-label="Preparación e investigación de Renta">
        <?php $incomeKeys=array_keys($incomeTabs); foreach ($incomeKeys as $incomeIndex=>$incomeKey):
            $incomePrevious=$incomeKeys[($incomeIndex+2)%3];$incomeNext=$incomeKeys[($incomeIndex+1)%3]; ?>
        <a id="renta-tab-<?= e($incomeKey) ?>" x-ref="incomeTab<?= e($incomeKey) ?>" role="tab" aria-controls="renta-panel-<?= e($incomeKey) ?>"
            class="btn-secondary shrink-0" href="<?= e($flowUrl('3').'&renta_paso='.$incomeKey) ?>"
            aria-selected="<?= $incomeInitial===$incomeKey?'true':'false' ?>" :aria-selected="incomeTab==='<?= e($incomeKey) ?>'"
            :tabindex="incomeTab==='<?= e($incomeKey) ?>'?0:-1" :class="incomeTab==='<?= e($incomeKey) ?>'?'bg-teal-50 text-teal-900 ring-2 ring-teal-700':''"
            @click.prevent="incomeTab='<?= e($incomeKey) ?>'"
            @keydown.right.prevent="incomeTab='<?= e($incomeNext) ?>'; $refs.incomeTab<?= e($incomeNext) ?>.focus()"
            @keydown.left.prevent="incomeTab='<?= e($incomePrevious) ?>'; $refs.incomeTab<?= e($incomePrevious) ?>.focus()"
            @keydown.home.prevent="incomeTab='concept'; $refs.incomeTabconcept.focus()"
            @keydown.end.prevent="incomeTab='capture'; $refs.incomeTabcapture.focus()"
            @keydown.space.prevent="incomeTab='<?= e($incomeKey) ?>'"><?= e($incomeTabs[$incomeKey]) ?></a>
        <?php endforeach; ?>
    </nav>
    <div id="renta-panel-concept" role="tabpanel" aria-labelledby="renta-tab-concept" x-show="incomeTab==='concept'">
        <?php require __DIR__.'/methodology-income-concept.php'; ?>
        <button type="button" class="btn-primary" @click="incomeTab='search'; $refs.incomeTabsearch.focus()">Continuar a 2. Preparar la búsqueda</button>
    </div>
    <div id="renta-panel-search" role="tabpanel" aria-labelledby="renta-tab-search" x-show="incomeTab==='search'">
        <?php require __DIR__.'/methodology-income-search.php'; ?>
    </div>
    <div id="renta-panel-capture" role="tabpanel" aria-labelledby="renta-tab-capture" x-show="incomeTab==='capture'">
        <?php require __DIR__.'/methodology-unassigned.php'; require __DIR__.'/valuation-methodology-search.php'; ?>
    </div>
</section>
