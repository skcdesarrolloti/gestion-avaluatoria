<?php
$phSearch = trim((string) ($phSearchQuery ?? ''));
$phResults = is_array($phSearchResults ?? null) ? $phSearchResults : [];
$phSuggestion = trim((string) (($ph['ph_name'] ?? '') ?: ($linkage['coproperty_name'] ?? '')));
$phLoadedDocuments = is_array($phDocuments ?? null) ? $phDocuments : [];
$phReadableDocuments = array_filter($phLoadedDocuments, static fn (array $doc): bool => (int) ($doc['extracted_chars'] ?? 0) > 0);
?>
<section class="mt-5 rounded-xl border border-blue-100 bg-blue-50 p-4">
    <div class="grid gap-4 lg:grid-cols-[1fr_auto]">
        <div>
            <h3 class="font-semibold text-blue-950">Banco de copropiedades</h3>
            <p class="mt-2 text-sm leading-6 text-blue-950">
                Busca copropiedades ya guardadas en otros avalúos por nombre de copropiedad, llave PH o matrícula matriz.
                No busca por número de escritura; los soportes cargados en este avalúo se ven abajo.
            </p>
        </div>
        <form class="grid gap-2 sm:grid-cols-[minmax(220px,1fr)_auto]" method="get"
            action="<?= e(url($subjectActionBase)) ?>#ph">
            <label class="sr-only" for="ph-coproperty-search">Buscar copropiedad guardada</label>
            <input id="ph-coproperty-search" class="input bg-white" name="copropiedad"
                value="<?= e($phSearch ?: $phSuggestion) ?>" placeholder="Ej. Edificio, conjunto, llave PH o matrícula">
            <button class="btn-secondary bg-white" type="submit">Buscar</button>
        </form>
    </div>
    <?php if ($phSearch !== ''): ?>
        <?php if ($phResults): ?>
            <div class="mt-4 grid gap-3 lg:grid-cols-2">
                <?php $searchResultRoute = 'bien-sujeto#ph'; ?>
                <?php foreach ($phResults as $item): ?>
                    <div>
                        <p class="mb-2 text-xs font-semibold text-blue-900">
                            Copropiedad: <?= e((string) ($item['ph_name'] ?: $item['ph_key'] ?: 'sin nombre')) ?>
                        </p>
                        <?php require BASE_PATH . '/app/Views/appraisals/search-result-card.php'; ?>
                    </div>
                <?php endforeach; ?>
                <?php unset($searchResultRoute); ?>
            </div>
        <?php else: ?>
            <p class="mt-4 rounded-lg bg-white p-3 text-sm font-semibold text-amber-800">
                No hay copropiedades guardadas en otros avalúos con ese criterio.
                <?php if ($phLoadedDocuments && !$phReadableDocuments): ?>
                    El soporte cargado en este avalúo existe, pero quedó con 0 caracteres extraídos; por eso aún no puede alimentar una copropiedad reutilizable.
                <?php endif; ?>
            </p>
        <?php endif; ?>
    <?php endif; ?>
</section>
