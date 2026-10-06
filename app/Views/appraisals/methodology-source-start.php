<div class="my-3 rounded-lg border bg-white p-3">
    <p class="text-sm"><strong x-text="sourceSavedCount"></strong> anuncios incorporados de <?= e($pasteLabel) ?> en esta unidad.</p>
    <div class="mt-2 flex flex-wrap gap-2">
        <button type="button" class="btn-primary min-h-11" :disabled="busy || removalBusy" @click="captureReady=true; sourceRestartPending=false">Continuar recogiendo</button>
        <button type="button" class="btn-secondary min-h-11" :disabled="busy || removalBusy || researchBusy" @click="sourceRestartPending=true; requestSourceRemoval(<?= e(json_encode($pasteLabel)) ?>)">Empezar de cero en <?= e($pasteLabel) ?></button>
    </div>
    <div x-show="sourceRestartPending" x-cloak class="mt-3 rounded-lg border border-amber-600 bg-amber-50 p-3">
        <p class="text-sm">Se retirarán los anuncios de <strong><?= e($pasteLabel) ?></strong> de esta unidad y se vaciarán sus páginas pegadas. Las muestras de otras fuentes se conservan. Descarga el respaldo antes de confirmar.</p>
        <div class="mt-2 flex flex-wrap gap-2">
            <button type="button" class="btn-secondary min-h-11" :disabled="removalBusy || exportBusy" @click="exportExcel()">Descargar respaldo en Excel</button>
            <button type="button" class="btn-primary min-h-11" :disabled="busy || removalBusy || researchBusy" @click="requestSourceRemoval(<?= e(json_encode($pasteLabel)) ?>); removalPending.length ? confirmRemoval() : restartEmptySource()">Confirmar reinicio solo de <?= e($pasteLabel) ?></button>
            <button type="button" class="btn-secondary min-h-11" :disabled="removalBusy" @click="sourceRestartPending=false; removalPending=[]">Cancelar</button>
        </div>
        <p class="mt-2 text-xs">«Restaurar retiradas», en el reinicio de la unidad, recupera anuncios mientras esta ventana siga abierta. Las páginas que aún no subiste no tienen respaldo guardado.</p>
    </div>
    <p x-show="removalSource === <?= e(json_encode($pasteLabel)) ?>" class="mt-2 text-sm" role="status" x-text="removalMessage"></p>
</div>
