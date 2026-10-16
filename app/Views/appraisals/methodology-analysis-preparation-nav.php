<nav class="mt-3 flex flex-wrap gap-2 border-t pt-3" x-show="['preparation','samples','location'].includes(analysisModule)" aria-label="Preparar los datos">
    <?php foreach (['samples'=>'Grupo preparado','location'=>'Ubicación y mapa'] as $panel=>$label): ?>
    <button type="button" class="btn-secondary" :aria-current="analysisModule==='<?= $panel ?>'?'page':null" :class="analysisModule==='<?= $panel ?>'?'bg-teal-50 ring-2 ring-teal-700 font-bold':''"
        @click="analysisModule='<?= $panel ?>'<?= $panel==='location' ? '; analysisSubjectLocation='.e($analysisSubjectLocation) : '; if(analysisRegimeApplied)analysisView=\'result\'' ?>"><?= e($label) ?></button>
    <?php endforeach; ?>
</nav>
<?php if (isset($flowUrl)): ?><details class="mt-3" x-show="['preparation','samples','location'].includes(analysisModule)"><summary class="min-h-11 cursor-pointer font-semibold">Agregar inmuebles o consultar la captura</summary><p class="mb-2 text-sm">Abre los lectores y la consolidación de anuncios. Para corregir un inmueble existente, usa la tabla del grupo preparado.</p><a class="btn-secondary" href="<?= e($flowUrl('3')) ?>">Abrir captura y consolidación</a></details><?php endif; ?>
