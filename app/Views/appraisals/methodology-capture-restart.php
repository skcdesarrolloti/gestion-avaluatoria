<details class="mb-4 rounded-xl border bg-white p-3" :aria-busy="removalBusy">
    <summary class="min-h-11 cursor-pointer text-sm font-semibold">Empezar de cero · muestras de esta unidad</summary>
    <p class="my-2 text-sm"><span x-text="total"></span> anuncios guardados de todos los portales e inmobiliarias de esta unidad. Estos conteos incluyen capturas anteriores; no prueban el resultado de una búsqueda nueva.</p>
    <div class="flex flex-wrap gap-2">
        <button type="button" class="btn-secondary" :disabled="!total || removalBusy || researchBusy || exportBusy" @click="exportExcel()">Descargar respaldo en Excel</button>
        <button type="button" class="btn-secondary" :disabled="!total || removalBusy || researchBusy || photoBusy" @click="requestRemoval(true)">Reiniciar muestras de esta unidad</button>
        <button type="button" x-show="undoCount" class="btn-secondary" :disabled="removalBusy || researchBusy || photoBusy" @click="undoRemoval()">Restaurar retiradas</button>
    </div>
    <div x-show="removalPending.length" x-cloak class="mt-3 rounded-lg border border-amber-600 bg-amber-50 p-3">
        <p class="text-sm">Se retirarán <strong x-text="removalPending.length"></strong> anuncios de esta colección, junto con sus atributos y decisiones de consolidación. El sujeto y las muestras de otras unidades se conservan. Después del guardado, los portales quedarán en cero.</p>
        <p class="my-2 text-sm">Descarga el respaldo antes de continuar. «Restaurar retiradas» funciona mientras esta página siga abierta; después de recargar se pierde esa restauración.</p>
        <button type="button" class="btn-primary" :disabled="removalBusy || researchBusy || photoBusy" @click="confirmRemoval()">Confirmar reinicio de las muestras</button>
        <button type="button" class="btn-secondary" :disabled="removalBusy" @click="removalPending=[]">Cancelar</button>
    </div>
    <p class="mt-2 text-sm" role="status" x-text="removalMessage"></p>
</details>
