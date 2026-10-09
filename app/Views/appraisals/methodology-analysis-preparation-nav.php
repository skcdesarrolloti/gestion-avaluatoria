<nav class="mt-3 flex flex-wrap gap-2 border-t pt-3" x-show="['preparation','samples','location'].includes(analysisModule)" aria-label="Preparar los datos">
    <?php foreach (['preparation'=>'1.1 Objetivo y unidad de análisis','samples'=>'1.2 Muestras y depuración','location'=>'1.3 Coordenadas y mapa comparativo'] as $panel=>$label): ?>
    <button type="button" class="btn-secondary" :aria-current="analysisModule==='<?= $panel ?>'?'page':null" :class="analysisModule==='<?= $panel ?>'?'bg-teal-50 ring-2 ring-teal-700 font-bold':''"
        @click="analysisModule='<?= $panel ?>'<?= $panel==='location' ? '; analysisSubjectLocation='.e($analysisSubjectLocation) : '' ?>"><?= e($label) ?></button>
    <?php endforeach; ?>
</nav>
