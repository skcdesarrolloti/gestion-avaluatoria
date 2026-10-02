<div class="mt-3 rounded-xl border border-teal-200 bg-teal-50 p-3" @input.stop @change.stop x-show="searchTab !== 'mapa'">
    <p class="text-sm font-semibold">Trabajar en Excel · descargar, completar y volver a cargar</p>
    <button type="button" class="btn-primary mt-2" :disabled="total === 0 || excelBusy || exportBusy || photoBusy || mapBusy" @click="exportExcel()" x-text="exportBusy ? 'Preparando descarga…' : 'Descargar tabla completa en Excel (.xlsx)'">Descargar tabla completa en Excel (.xlsx)</button>
    <p class="mt-2 text-sm font-semibold text-teal-900" role="status" x-text="excelUpdated"><?= e(\App\Services\ComparableExcelHistory::display(\App\Services\ComparableExcelHistory::latest($record, $componentKey ?? ''))) ?></p>
    <label class="label mt-2">Importar Excel actualizado (.xlsx, hasta 5 MB)
        <input class="input mt-1" type="file" accept=".xlsx" :disabled="excelBusy || exportBusy || photoBusy || mapBusy" @change="prepareExcel($event)">
    </label>
    <p class="mt-2 text-xs">Descarga las veces que necesites: siempre incluye todas las muestras de esta unidad/banco, no sólo la página visible. Conserva ID, encabezados y la hoja de identificación; completa campos, carga el archivo y confirma Guardar Excel actualizado. La fecha/hora se registra al guardar, no al seleccionar el archivo. Las filas ausentes se conservan; si la matriz cambió desde la exportación, se pide una exportación actual.</p>
    <p class="mt-2 text-sm" role="status" x-text="excelMessage"></p>
    <div x-show="excelChanges.length" class="mt-2 max-h-80 overflow-y-auto rounded-lg bg-white p-3">
        <template x-for="(change,index) in excelChanges" :key="index">
            <div class="border-b border-slate-100 py-2 text-sm">
                <strong x-text="change.sample + ' · ' + change.label"></strong>
                <p class="text-anywhere"><span x-text="change.before"></span> → <span class="font-semibold text-teal-800" x-text="change.after"></span></p>
            </div>
        </template>
    </div>
    <div x-show="excelReady" class="mt-3 flex flex-wrap gap-2">
        <button type="button" class="btn-primary" :disabled="excelBusy" @click="applyExcel()">Guardar Excel actualizado</button>
        <button type="button" class="btn-secondary" :disabled="excelBusy" @click="cancelExcel()">Cancelar importación</button>
    </div>
</div>
