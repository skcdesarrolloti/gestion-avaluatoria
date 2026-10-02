<article class="md:col-span-2 mt-3 grid gap-3 rounded-lg border border-slate-200 bg-white p-3 sm:grid-cols-[7rem_1fr]"
    x-show="selectedTypology(igacCategory, igacHint)">
    <a class="block aspect-[4/3] overflow-hidden rounded-md border border-slate-200 bg-white"
        :href="imageUrl(selectedTypology(igacCategory, igacHint))" target="_blank" rel="noopener" data-no-fetch>
        <img class="h-full w-full object-contain" :src="imageUrl(selectedTypology(igacCategory, igacHint))"
            alt="Referencia visual IGAC" loading="lazy">
    </a>
    <div class="min-w-0">
        <p class="text-anywhere text-sm font-semibold text-slate-950" x-text="selectedTypology(igacCategory, igacHint)?.label"></p>
        <dl class="mt-2 grid gap-2 text-xs font-normal text-slate-700 sm:grid-cols-2">
            <div><dt class="font-semibold">Unidad de referencia</dt><dd x-text="selectedTypology(igacCategory, igacHint)?.unit || 'No informada'"></dd></div>
            <div><dt class="font-semibold">Vida útil del catálogo (años)</dt><dd x-text="selectedTypology(igacCategory, igacHint)?.useful_life || 'No informada'"></dd></div>
            <div><dt class="font-semibold">Página de la fuente</dt><dd x-text="selectedTypology(igacCategory, igacHint)?.source_page || 'No informada'"></dd></div>
            <div><dt class="font-semibold">Costo de reposición y fecha base</dt><dd>No disponibles en el catálogo cargado.</dd></div>
        </dl>
        <p class="mt-2 text-xs font-normal leading-5 text-slate-600">La tipología ayuda a contrastar especificaciones y costos de reposición.
            Un costo histórico requiere fuente, fecha, ubicación y alcance comparables antes de actualizarlo. Elegir esta referencia no cambia el método ni calcula el valor de la unidad.</p>
        <a class="mt-2 inline-flex min-h-11 items-center text-xs font-semibold text-teal-800" href="<?= e(url('igac')) ?>" target="_blank" rel="noopener" data-no-fetch>Consultar biblioteca IGAC y documentos fuente</a>
    </div>
        <details class="mt-1 text-xs leading-5 text-slate-600 sm:col-span-2">
            <summary class="min-h-11 cursor-pointer py-3 font-semibold">Leer descripción y especificaciones completas</summary>
            <p class="text-anywhere" x-text="selectedTypology(igacCategory, igacHint)?.description"></p>
            <p class="text-anywhere mt-2" x-text="selectedTypology(igacCategory, igacHint)?.specifications"></p>
        </details>
</article>
