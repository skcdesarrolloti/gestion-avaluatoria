<p class="mt-3 text-sm leading-6 text-slate-600">
    Detectadas: <strong><?= e((string) count($annotations)) ?></strong>.
    Vigentes con revisión:
    <strong><?= e((string) count(array_filter($annotations, static fn ($a): bool => ($a['requiere_revision'] ?? '') === 'Sí'))) ?></strong>.
</p>
<div class="mt-4 flex flex-wrap gap-2 text-xs font-bold">
    <?php foreach ([
        ['Rojo', 'border-red-200 bg-red-100 text-red-800'],
        ['Amarillo', 'border-amber-200 bg-amber-100 text-amber-900'],
        ['Verde', 'border-emerald-200 bg-emerald-100 text-emerald-800'],
    ] as [$tone, $toneClass]): ?>
        <span class="rounded-full border px-3 py-1 <?= e($toneClass) ?>"><?= e($tone) ?>: <?= e((string) $trafficCounts[$tone]) ?></span>
    <?php endforeach; ?>
</div>
