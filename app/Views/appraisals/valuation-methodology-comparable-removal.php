<div class="mt-3 rounded-lg border border-slate-300 bg-white p-3" :aria-busy="removalBusy">
    <p class="text-sm font-semibold">Retirar muestras o empezar de cero</p>
    <div class="mt-2 flex flex-wrap gap-2">
        <button type="button" class="btn-secondary min-h-11" @click="selectRemovalPage()" :disabled="removalBusy || photoBusy || !total">Seleccionar esta página</button>
        <button type="button" class="btn-secondary min-h-11" @click="removalSelection = []" :disabled="removalBusy || !removalSelection.length">Quitar selección</button>
        <button type="button" class="btn-secondary min-h-11" @click="requestRemoval()" :disabled="removalBusy || photoBusy || !removalSelection.length">Eliminar seleccionadas (<span x-text="removalSelection.length"></span>)</button>
        <button type="button" class="btn-secondary min-h-11" @click="requestRemoval(true)" :disabled="removalBusy || photoBusy || !total">Vaciar matriz (<span x-text="total"></span>)</button>
        <button type="button" class="btn-secondary min-h-11" x-show="undoCount" @click="undoRemoval()" :disabled="removalBusy || photoBusy">Restaurar retiradas (<span x-text="undoCount"></span>)</button>
    </div>
    <div x-show="removalPending.length" x-cloak class="mt-3 rounded border border-amber-600 bg-amber-50 p-3">
        <p class="text-sm">Vas a retirar <strong x-text="removalPending.length"></strong> muestras de la matriz, incluidas las seleccionadas en otras páginas. El expediente no se modifica. Las fotos conservan su vínculo anterior y no se asignan a muestras nuevas.</p>
        <p class="mt-1 text-sm">Podrás restaurarlas mientras mantengas esta página abierta. Después de recargar, esta opción de restauración se pierde.</p>
        <div class="mt-2 flex flex-wrap gap-2">
            <button type="button" class="btn-primary min-h-11" @click="confirmRemoval()" :disabled="removalBusy || photoBusy">Confirmar eliminación de la matriz</button>
            <button type="button" class="btn-secondary min-h-11" @click="removalPending = []" :disabled="removalBusy">Cancelar</button>
        </div>
    </div>
    <p class="mt-2 text-sm" role="status" x-text="removalMessage"></p>
    <p x-show="undoCount" class="mt-1 text-xs">Restauración disponible solo en esta página abierta. Verifica el guardado antes de salir.</p>
</div>
