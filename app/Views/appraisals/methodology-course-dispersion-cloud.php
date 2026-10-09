<div class="space-y-3">
    <p>Cada punto representa un inmueble: la altura corresponde a su valor unitario y el eje horizontal conserva el orden del listado. Los puntos rojos, marcados también con una cruz, están fuera de los límites exploratorios. La línea azul representa la media de toda la muestra.</p>
    <div class="overflow-auto" tabindex="0" aria-label="Gráfico de dispersión · desplazamiento horizontal en pantallas pequeñas"><div class="min-w-[640px]" :key="courseResult.at" x-effect="coursePlot($el,courseDispersionPlot())"></div></div>
    <p class="text-sm" x-text="'Límite inferior: '+courseNumber(courseResult.summary.lower)+' COP/m² · Límite superior: '+courseNumber(courseResult.summary.upper)+' COP/m² · '+courseOutsideLimits().length+' inmuebles fuera de límites.'"></p>
    <?php require __DIR__.'/methodology-course-quartiles.php'; ?>
    <details class="rounded-lg border bg-slate-50 p-3">
        <summary class="min-h-11 cursor-pointer font-semibold">Límites exploratorios · fórmula e interpretación</summary>
        <div class="mt-3 space-y-3">
            <p class="font-mono">RIC = Q3 − Q1<br>Límite inferior = Q1 − 1,5 × RIC<br>Límite superior = Q3 + 1,5 × RIC</p>
            <div class="space-y-2 font-mono"><template x-for="line in courseQuartileLimitOperations()" :key="line"><p x-text="line"></p></template></div>
            <p>Q1 y Q3 son los cuartiles primero y tercero: delimitan el 50 % central de los valores ordenados. RIC es el rango intercuartílico, es decir, la amplitud de ese tramo. Se utiliza la interpolación de cuartiles del cálculo existente.</p>
            <p>Un valor estrictamente inferior o superior a estos límites se señala para investigar su precio, área, fuente y comparabilidad. Un valor exactamente en el límite permanece dentro. El rojo indica una señal exploratoria; no demuestra un error ni autoriza excluir el inmueble.</p>
            <p>Estos límites son distintos del intervalo de confianza de la media y del umbral del CV. No representan precios mínimos o máximos admisibles para el avalúo. La media del gráfico conserva su cálculo original; el centro seleccionado por MAPE pertenece a la comparación del paso anterior.</p>
            <p class="text-sm"><a class="underline" href="https://www.itl.nist.gov/div898/handbook/eda/section3/boxplot.htm" target="_blank" rel="noopener" data-no-fetch>NIST · límites mediante 1,5 rangos intercuartílicos</a>. Herramienta estadística exploratoria.</p>
        </div>
    </details>
    <div class="space-y-2" x-show="courseOutsideLimits().length">
        <p class="font-semibold">Inmuebles señalados para revisión</p>
        <template x-for="r in courseOutsideLimits()" :key="r.id"><p class="flex flex-wrap items-center gap-3 text-rose-800"><span x-text="r.label+' · '+courseNumber(r.y)+' COP/m² · Fuera de límites'"></span><button type="button" class="btn-secondary" @click="await courseReview(r.id)">Revisar inmueble</button></p></template>
    </div>
</div>
