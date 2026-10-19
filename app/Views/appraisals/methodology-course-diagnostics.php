<p>Estas ayudas describen la forma de los datos y señalan qué revisar. Se interpretan junto a los cuartiles y la comparación de centros; ninguna decide por sí sola el valor del avalúo.</p>
<div aria-label="Explicación de los diagnósticos de sensibilidad"><table class="block w-full text-left text-sm md:table">
    <caption class="block text-left font-semibold md:table-caption">Qué significa cada indicador en esta ejecución</caption>
    <thead class="hidden md:table-header-group"><tr><th class="p-2" scope="col">Indicador</th><th class="p-2" scope="col">Resultado</th><th class="p-2" scope="col">Qué mide</th><th class="p-2" scope="col">Cómo leer tu resultado</th></tr></thead>
    <tbody class="block md:table-row-group"><template x-for="item in courseDiagnostics()" :key="item.key"><tr class="block border-t md:table-row">
        <th class="block p-2 md:table-cell" scope="row" x-text="item.label"></th>
        <td class="block p-2 md:table-cell"><strong class="md:hidden">Resultado: </strong><span x-text="item.value"></span></td>
        <td class="block p-2 md:table-cell"><strong class="md:hidden">Qué mide: </strong><span x-text="item.definition"></span></td>
        <td class="block p-2 md:table-cell"><strong class="md:hidden">Cómo leer tu resultado: </strong><span x-text="item.reading"></span></td>
    </tr></template></tbody>
</table></div>
<p class="text-sm">Si aparece una señal, verifica sus datos en la misma tabla de inmuebles y registra tu consideración. Una muestra correcta no se elimina únicamente por ser extrema ni para conseguir un CV determinado.</p>
