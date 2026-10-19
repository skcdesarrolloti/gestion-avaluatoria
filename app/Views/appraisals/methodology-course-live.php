<section class="rounded-lg border bg-teal-50 p-3 space-y-2" aria-label="Resultados al revisar las muestras" x-data="{review:null}" x-effect="review=courseLivePreview()">
    <h5 class="font-semibold">Resultados mientras revisas los datos</h5>
    <p class="text-sm">La columna «Al revisar» cambia al editar oferta, área o descuento. Es una vista previa: conserva la ejecución guardada y no elimina muestras.</p>
    <template x-if="review"><div class="space-y-2">
        <p class="text-sm" x-text="'Grupo actual: '+review.n+' muestras · '+review.drafts+' filas con cambios numéricos sin aplicar · Base: '+(review.basis==='adjusted'?'valor con descuento / área publicada':'oferta / área publicada')"></p>
        <table class="block w-full text-left text-sm md:table"><thead class="hidden md:table-header-group"><tr><th class="p-2">Indicador</th><th class="p-2">Ejecución guardada</th><th class="p-2">Al revisar</th><th class="p-2">Diferencia</th></tr></thead><tbody class="block md:table-row-group"><template x-for="item in review.comparison" :key="item.label"><tr class="block border-t md:table-row">
            <th class="block p-2 md:table-cell" scope="row" x-text="item.label+(item.unit?' · '+item.unit:'')"></th>
            <td class="block p-2 md:table-cell"><strong class="md:hidden">Guardado: </strong><span x-text="courseNumber(item.before)"></span></td>
            <td class="block p-2 md:table-cell"><strong class="md:hidden">Al revisar: </strong><span x-text="courseNumber(item.after)"></span></td>
            <td class="block p-2 md:table-cell"><strong class="md:hidden">Diferencia: </strong><span x-text="courseNumber(item.difference)"></span></td>
        </tr></template></tbody></table>
        <p class="font-semibold" role="status" x-text="review.status"></p>
        <template x-for="row in review.pending" :key="row.id"><p class="text-sm text-amber-900" x-text="row.label+': '+row.reasons.join(' · ')"></p></template>
    </div></template>
    <p class="text-sm">El 7,5 % se muestra como referente de comparación, no como certificación del avalúo. No cambies datos correctos ni retires muestras solo para bajar el CV; verifica y sustenta su comparabilidad.</p>
    <p class="text-sm">Para conservar una corrección: registra fuente, fecha y motivo en la fila, pulsa Aplicar corrección y espera Guardado confirmado. Después pulsa Actualizar análisis estadístico para incorporar las correcciones aplicadas a los gráficos, concepto y memoria.</p>
</section>
