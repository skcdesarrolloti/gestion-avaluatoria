<?php
$sectorMatches = is_array($sectorNeighborhoodMatches ?? null) ? $sectorNeighborhoodMatches : [];
?>
<section class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="font-semibold">Avalúos guardados del barrio</h3>
            <p class="mt-1 text-sm leading-6 text-slate-600">
                Este listado ayuda a reconocer fichas previas del mismo barrio antes de reutilizar criterio sectorial.
            </p>
        </div>
        <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-slate-700">
            <?= e((string) count($sectorMatches)) ?> coincidencias
        </span>
    </div>
    <?php if ($sectorMatches): ?>
        <div class="mt-4 grid gap-3 lg:grid-cols-2">
            <?php $searchResultRoute = 'sector'; ?>
            <?php foreach ($sectorMatches as $item): ?>
                <?php require BASE_PATH . '/app/Views/appraisals/search-result-card.php'; ?>
            <?php endforeach; ?>
            <?php unset($searchResultRoute); ?>
        </div>
    <?php else: ?>
        <p class="mt-4 rounded-lg bg-white p-3 text-sm font-semibold text-slate-600">
            <?= trim((string) ($subject['neighborhood_id'] ?? '')) === ''
                ? 'Carga primero un barrio para consultar antecedentes.'
                : 'Aún no hay otros avalúos guardados para este barrio.' ?>
        </p>
    <?php endif; ?>
</section>
