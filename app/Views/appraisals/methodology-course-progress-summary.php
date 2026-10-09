<section class="rounded-lg border bg-teal-50 p-3 space-y-2" aria-label="Balance del análisis y siguiente paso">
    <h5 class="font-semibold">Qué podemos afirmar hasta aquí</h5>
    <p x-text="courseProgressSummary().approximation"></p>
    <p x-text="courseProgressSummary().distribution"></p>
    <details><summary class="min-h-11 cursor-pointer font-semibold">Por qué usamos la mediana en los cuartiles</summary><p class="mt-2" x-text="courseProgressSummary().median"></p></details>
    <p x-text="courseProgressSummary().next"></p>
    <p class="text-sm" x-text="courseProgressSummary().scope"></p>
</section>
