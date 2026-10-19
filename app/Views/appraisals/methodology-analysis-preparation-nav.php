<nav id="market-preparation-actions" x-ref="preparationActions" class="absolute left-0 top-full z-30 mt-2 flex max-h-[60vh] w-72 max-w-[calc(100vw-3rem)] flex-col gap-1 overflow-y-auto rounded-xl border border-teal-700 bg-white p-2 shadow-lg" x-show="preparationMenuOpen" x-cloak @click="if($event.target.closest('button,a'))preparationMenuOpen=false" aria-label="Opciones de Preparar la muestra">
    <?php foreach (['preparation'=>'Configuración del estudio','samples'=>'Verificación de las muestras','location'=>'Ubicación y mapa'] as $panel=>$label): ?>
    <?php if (!empty($capturePreparationMenu)): ?>
    <a class="btn-secondary justify-start text-left" href="<?= e($flowUrl('4').($panel==='samples'?'':'&panel='.$panel)) ?>"><?= e($label) ?></a>
    <?php else: ?>
    <button type="button" class="btn-secondary justify-start text-left" :aria-current="analysisModule==='<?= $panel ?>'?'page':null" :class="analysisModule==='<?= $panel ?>'?'bg-teal-50 ring-2 ring-teal-700 font-bold':''"
        @click="analysisModule='<?= $panel ?>'<?= $panel==='location' ? '; analysisSubjectLocation='.e($analysisSubjectLocation) : '; if(analysisRegimeApplied)analysisView=\'result\'' ?>"><?= e($label) ?></button>
    <?php endif; ?>
    <?php if ($panel==='preparation' && isset($flowUrl)): ?><a class="btn-secondary justify-start text-left <?= !empty($capturePreparationMenu)?'bg-teal-50 ring-2 ring-teal-700 font-bold':'' ?>" <?= !empty($capturePreparationMenu)?'aria-current="page"':'' ?> href="<?= e($flowUrl('3')) ?>">Traer muestras de portales</a><?php endif; ?>
    <?php endforeach; ?>
</nav>
