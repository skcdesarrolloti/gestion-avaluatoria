<details class="rounded-lg border bg-slate-50 p-3">
    <summary class="min-h-11 cursor-pointer font-semibold">Comprobar Q1, mediana y Q3 · datos y operaciones</summary>
    <div class="mt-3 space-y-3">
        <p>Se ordenan todos los valores unitarios disponibles de menor a mayor; no se usan las marcas de clase. En esta explicación contamos desde 1: posición = 1 + (n − 1) × p. Es el mismo cálculo del aplicativo, expresado con la numeración de la tabla.</p>
        <p x-text="'n = '+courseResult.valid.length+' valores. Q1 usa p = 0,25; Q2 usa p = 0,50; Q3 usa p = 0,75.'"></p>
        <template x-for="c in courseQuartileCalculation().cuts" :key="c.label"><div class="rounded border bg-white p-3 space-y-2">
            <p class="font-semibold" x-text="c.label+' · posición = 1 + ('+c.n+' − 1) × '+courseNumber(c.p)+' = '+courseNumber(c.position)"></p>
            <p x-text="'Dato inferior: posición '+c.lower.position+' · '+c.lower.label+' · '+courseNumber(c.lower.y)+' COP/m². Dato superior: posición '+c.upper.position+' · '+c.upper.label+' · '+courseNumber(c.upper.y)+' COP/m².'"></p>
            <p x-text="'Fracción entre las posiciones: '+courseNumber(c.position)+' − '+c.lower.position+' = '+courseNumber(c.fraction)+'. Se toma esa proporción de la diferencia de precios.'"></p>
            <p class="font-mono" x-text="courseQuartileOperation(c)"></p>
        </div></template>
        <p>Si la posición es entera, se usa ese dato. Si los dos precios son iguales, la interpolación conserva ese mismo precio. Otra convención de cuartiles puede dar resultados diferentes: para reproducir estos, en Excel usa CUARTIL.INC o PERCENTIL.INC. Los cálculos conservan los decimales internos; las cifras mostradas se redondean.</p>
        <p class="text-sm"><a class="underline" href="https://support.microsoft.com/es-es/excel/functions/percentile-inc-function" target="_blank" rel="noopener" data-no-fetch>Microsoft · PERCENTIL.INC e interpolación inclusiva</a>. Convención de cálculo; no constituye una exigencia jurídica.</p>
        <details><summary class="min-h-11 cursor-pointer font-semibold">Ver todos los valores ordenados y sus inmuebles</summary><div class="max-h-80 overflow-auto"><table class="min-w-full text-left text-sm"><thead><tr><th class="p-2">Posición ordenada</th><th class="p-2">Inmueble de origen</th><th class="p-2">Valor COP/m²</th></tr></thead><tbody><template x-for="r in courseQuartileCalculation().ordered" :key="r.position"><tr class="border-t"><td class="p-2" x-text="r.position"></td><td class="p-2" x-text="r.label"></td><td class="p-2" x-text="courseNumber(r.y)"></td></tr></template></tbody></table></div></details>
    </div>
</details>
