<div id="renta-estudio" class="scroll-mt-6 mb-5 rounded-xl border border-teal-700 bg-white p-4">
    <p class="font-semibold text-teal-900" role="status" aria-live="polite">Renta · <?= e($componentLabel) ?> → <span x-text="incomeSteps[incomeStage]"><?= e($incomeSteps[in_array($incomeInitial,['search','capture'],true)?'research':$incomeInitial]) ?></span></p>
    <nav class="mt-3 grid items-start gap-2 sm:grid-cols-2 lg:grid-cols-5" aria-label="Etapas del estudio de Renta">
        <?php foreach ($incomeSteps as $incomeKey=>$incomeLabel): $incomeTarget=$incomeKey==='research'?'search':$incomeKey; ?>
        <a class="btn-secondary text-left" href="<?= e($flowUrl('3').'&renta_paso='.$incomeTarget) ?>#renta-estudio"
            :aria-current="incomeStage==='<?= e($incomeKey) ?>'?'step':null" :class="incomeStage==='<?= e($incomeKey) ?>'?'bg-teal-50 ring-2 ring-teal-700 font-bold':''"
            @click.prevent="openStage('<?= e($incomeKey) ?>')"><?= e($incomeLabel) ?></a>
        <?php endforeach; ?>
    </nav>
    <p class="mt-3 text-sm" role="status" x-show="incomeNavigating" x-cloak>Guardando cambios antes de continuar…</p>
    <p class="mt-3 text-sm text-red-800" role="alert" x-show="incomeNavigationError" x-text="incomeNavigationError" x-cloak></p>
    <a class="mt-3 inline-flex min-h-11 items-center text-sm font-semibold text-blue-800 underline" href="<?= e($flowUrl('plan',null,$sourceUnitKey)) ?>">Cambiar unidad, método o alcance</a>
</div>
<details class="mb-5 rounded-xl border bg-white p-4">
    <summary class="min-h-11 cursor-pointer font-semibold">Ayuda / Academia de esta etapa</summary>
    <?php $incomeInlineAcademy=true; require __DIR__.'/methodology-income-academy-topics.php'; unset($incomeInlineAcademy); ?>
    <a class="btn-secondary mt-4" href="<?= e($flowUrl('1','renta')) ?>#renta-orientacion">Consultar Academia completa de Renta</a>
</details>
