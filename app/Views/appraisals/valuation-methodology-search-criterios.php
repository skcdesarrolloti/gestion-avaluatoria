<div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="rounded-t-xl bg-blue-900 px-4 py-3 text-sm font-semibold uppercase text-white">Criterios de búsqueda</div>
        <ol class="space-y-3 p-4 text-sm leading-6 text-slate-700">
            <?php foreach (($guide['criteria'] ?? []) as $index => $criterion): ?>
                <li class="flex gap-3">
                    <span class="mt-1 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-800"><?= e((string) ($index + 1)) ?></span>
                    <span><?= e($criterion) ?></span>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
    <div class="space-y-6">
        <div class="rounded-xl border border-amber-200 bg-amber-50">
            <div class="rounded-t-xl bg-amber-600 px-4 py-3 text-sm font-semibold uppercase text-white">Evitar</div>
            <ul class="space-y-3 p-4 text-sm leading-6 text-amber-950">
                <?php foreach (($guide['avoid'] ?? []) as $item): ?>
                    <li>- <?= e($item) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50">
            <div class="rounded-t-xl bg-emerald-700 px-4 py-3 text-sm font-semibold uppercase text-white">Depuración estadística posterior</div>
            <ul class="space-y-3 p-4 text-sm leading-6 text-emerald-950">
                <?php foreach ($adjustments as $item): ?>
                    <li>- <?= e($item) ?></li>
                <?php endforeach; ?>
                <li>- Calcular dispersión por factor antes de adoptar una medida de tendencia central.</li>
                <li>- Justificar el estadístico con comparabilidad y condiciones del mercado; la asimetría no determina por sí sola el valor (art. 21).</li>
                <li>- Documentar valores atípicos y puntos influyentes; no usar homologación mediante factores (anexo 2.1).</li>
            </ul>
        </div>
    </div>
</div>
