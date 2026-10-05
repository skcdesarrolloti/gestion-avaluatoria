<section x-show="searchTab==='matriz' && mode==='intake' && intakeView==='table'" x-cloak class="mt-4">
    <p class="mb-3 text-sm">Una fila por inmueble. Cada celda conserva el dato y la fuente; amarillo señala diferencias. Pendiente significa que falta información. Confirma la participación en «Revisar por portal».</p>
    <div role="table" aria-label="Inmuebles recogidos y atributos" class="max-h-[65vh] overflow-auto rounded-xl border">
        <div role="row" class="flex w-max bg-slate-100 font-semibold">
            <div role="columnheader" class="w-56 shrink-0 p-3">Inmueble</div><div role="columnheader" class="w-40 shrink-0 p-3">Participación</div>
            <template x-for="column in intakeTableData.columns" :key="column.key"><div role="columnheader" class="w-48 shrink-0 p-3" x-text="column.label"></div></template>
        </div>
        <div role="row" class="flex w-max border-t bg-slate-50 text-sm">
            <div role="cell" class="w-56 shrink-0 p-3 font-semibold">Sujeto · capítulo 3</div><div role="cell" class="w-40 shrink-0 p-3">Referencia</div>
            <template x-for="column in intakeTableData.columns" :key="column.key"><div role="cell" class="w-48 shrink-0 break-words p-3" x-text="column.subject || 'Pendiente / no aplica'"></div></template>
        </div>
        <template x-for="(property,index) in intakeTableData.rows" :key="property.key">
            <div role="row" class="flex w-max border-t text-sm">
                <div role="cell" class="w-56 shrink-0 p-3"><strong x-text="(index+1)+'. '+property.title"></strong><p x-text="property.rows.map(r => r.source_name).join(' / ')"></p></div>
                <div role="cell" class="w-40 shrink-0 p-3" x-text="property.confirmation"></div>
                <template x-for="column in intakeTableData.columns" :key="column.key"><div role="cell" class="w-48 shrink-0 break-words p-3" :class="property.values[column.key]?.state==='different' ? 'bg-amber-50' : ''" x-text="property.values[column.key]?.value || 'Pendiente'"></div></template>
            </div>
        </template>
    </div>
    <p x-show="!intakeTableData.rows.length" class="p-3 text-sm">Todavía no hay inmuebles incorporados.</p>
</section>
