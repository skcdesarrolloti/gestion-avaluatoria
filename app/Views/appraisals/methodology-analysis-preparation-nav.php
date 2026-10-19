<nav class="mt-3 flex flex-wrap gap-2 border-t pt-3" x-show="['preparation','samples','location'].includes(analysisModule)" aria-label="Preparar la muestra">
    <?php foreach (['preparation'=>'Configuración del estudio','samples'=>'Verificación de las muestras','location'=>'Ubicación y mapa'] as $panel=>$label): ?>
    <button type="button" class="btn-secondary" :aria-current="analysisModule==='<?= $panel ?>'?'page':null" :class="analysisModule==='<?= $panel ?>'?'bg-teal-50 ring-2 ring-teal-700 font-bold':''"
        @click="analysisModule='<?= $panel ?>'<?= $panel==='location' ? '; analysisSubjectLocation='.e($analysisSubjectLocation) : '; if(analysisRegimeApplied)analysisView=\'result\'' ?>"><?= e($label) ?></button>
    <?php if ($panel==='preparation' && isset($flowUrl)): ?><a class="btn-primary" href="<?= e($flowUrl('3')) ?>">Traer muestras de portales</a><?php endif; ?>
    <?php endforeach; ?>
</nav>
