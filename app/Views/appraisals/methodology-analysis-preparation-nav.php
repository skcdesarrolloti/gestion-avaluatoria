<nav class="mt-3 flex flex-wrap gap-2 border-t pt-3" x-show="['preparation','samples','location'].includes(analysisModule)" aria-label="Preparar los datos">
    <?php if (isset($flowUrl)): ?><a class="btn-secondary" href="<?= e($flowUrl('3')) ?>">Captura y consolidación</a><?php endif; ?>
    <?php foreach (['samples'=>'Grupo preparado','location'=>'Ubicación y mapa'] as $panel=>$label): ?>
    <button type="button" class="btn-secondary" :aria-current="analysisModule==='<?= $panel ?>'?'page':null" :class="analysisModule==='<?= $panel ?>'?'bg-teal-50 ring-2 ring-teal-700 font-bold':''"
        @click="analysisModule='<?= $panel ?>'<?= $panel==='location' ? '; analysisSubjectLocation='.e($analysisSubjectLocation) : '' ?>"><?= e($label) ?></button>
    <?php endforeach; ?>
</nav>
