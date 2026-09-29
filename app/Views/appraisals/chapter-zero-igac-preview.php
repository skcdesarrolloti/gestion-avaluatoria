<article class="mt-3 grid gap-3 rounded-lg border border-slate-200 bg-white p-3 sm:grid-cols-[7rem_1fr]"
    x-show="selectedTypology(igacCategory, igacHint)">
    <a class="block aspect-[4/3] overflow-hidden rounded-md border border-slate-200 bg-white"
        :href="imageUrl(selectedTypology(igacCategory, igacHint))" target="_blank" rel="noopener" data-no-fetch>
        <img class="h-full w-full object-contain" :src="imageUrl(selectedTypology(igacCategory, igacHint))"
            alt="Referencia visual IGAC" loading="lazy">
    </a>
    <div class="min-w-0">
        <p class="text-anywhere text-sm font-semibold text-slate-950" x-text="selectedTypology(igacCategory, igacHint)?.label"></p>
        <p class="text-anywhere mt-1 line-clamp-3 text-xs leading-5 text-slate-600" x-text="selectedTypology(igacCategory, igacHint)?.description"></p>
    </div>
</article>
