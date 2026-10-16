<details class="rounded-lg border p-3" x-data="{rangeOpen:false}" @toggle.stop="rangeOpen=$el.open">
    <summary class="min-h-11 cursor-pointer font-semibold">De dónde salen el rango, la desviación y el CV</summary>
    <template x-if="rangeOpen"><div class="mt-3 space-y-3"><template x-for="audit in [courseCalculation()]" :key="courseResult.at"><div class="overflow-auto" tabindex="0" role="region" aria-label="Operaciones de dispersión"><table class="min-w-full text-left text-sm"><thead><tr><th class="p-2">Medida</th><th class="p-2">Datos y operación</th><th class="p-2">Resultado</th></tr></thead><tbody>
        <tr><th class="p-2">Mínimo · COP/m²</th><td class="p-2" x-text="audit.min.label+' · primera posición ordenada'"></td><td class="p-2" x-text="courseNumber(audit.min.y)"></td></tr>
        <tr><th class="p-2">Máximo · COP/m²</th><td class="p-2" x-text="audit.max.label+' · posición '+audit.n"></td><td class="p-2" x-text="courseNumber(audit.max.y)"></td></tr>
        <tr><th class="p-2">Rango · COP/m²</th><td class="p-2" x-text="courseNumber(audit.max.y)+' − '+courseNumber(audit.min.y)"></td><td class="p-2" x-text="courseNumber(courseResult.summary.range)"></td></tr>
        <tr><th class="p-2">Varianza muestral · (COP/m²)²</th><td class="p-2" x-text="'Suma de (x − media)² ÷ (n − 1) = '+courseNumber(audit.deviations)+' ÷ '+(audit.n-1)"></td><td class="p-2" x-text="courseNumber(courseResult.summary.variance)"></td></tr>
        <tr><th class="p-2">Desviación estándar · COP/m²</th><td class="p-2" x-text="'√'+courseNumber(courseResult.summary.variance)"></td><td class="p-2" x-text="courseNumber(courseResult.summary.sd)"></td></tr>
        <tr><th class="p-2">CV · %</th><td class="p-2" x-text="'100 × '+courseNumber(courseResult.summary.sd)+' ÷ |'+courseNumber(courseResult.summary.mean)+'|'"></td><td class="p-2" x-text="courseNumber(courseResult.summary.cv)"></td></tr>
    </tbody></table></div></template></div></template>
</details>
