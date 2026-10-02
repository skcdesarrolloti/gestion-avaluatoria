<div class="mt-3 rounded-xl border border-teal-200 bg-teal-50 p-3" @input.stop @change.stop x-show="searchTab !== 'mapa'">
    <p class="text-sm font-semibold">Trabaja en la tabla editable; Excel es opcional</p>
    <label class="label mt-2">Importar Excel actualizado (.xlsx, hasta 5 MB)
        <input class="input mt-1" type="file" accept=".xlsx" :disabled="excelBusy || exportBusy || photoBusy || mapBusy" @change="prepareExcel($event)">
    </label>
    <p class="mt-2 text-xs">Exporta desde esta unidad/banco, conserva ID y encabezados, completa los campos y carga el archivo. Las filas se actualizan por ID, incluso si las ordenaste en Excel. Las filas ausentes se conservan. Si la matriz cambió desde la exportación, se pide una exportación actual.</p>
    <p class="mt-2 text-sm" role="status" x-text="excelMessage"></p>
    <div x-show="excelChanges.length" class="mt-2 max-h-80 overflow-y-auto rounded-lg bg-white p-3">
        <template x-for="(change,index) in excelChanges" :key="index">
            <div class="border-b border-slate-100 py-2 text-sm">
                <strong x-text="change.sample + ' · ' + change.label"></strong>
                <p class="text-anywhere"><span x-text="change.before"></span> → <span class="font-semibold text-teal-800" x-text="change.after"></span></p>
            </div>
        </template>
    </div>
    <div x-show="excelChanges.length" class="mt-3 flex flex-wrap gap-2">
        <button type="button" class="btn-primary" :disabled="excelBusy" @click="applyExcel()">Aplicar cambios y guardar</button>
        <button type="button" class="btn-secondary" :disabled="excelBusy" @click="cancelExcel()">Cancelar importación</button>
    </div>
</div>
