<?php
$legalExpectedRegistry = trim((string) ($subject['property_registry'] ?? ''));
$legalExpectedCadastral = trim((string) ($subject['cadastral_reference'] ?? ''));
$legalExpectedOffice = trim((string) ($subject['registry_office'] ?? ''));
$legalQuery = trim((string) ($legalSearchQuery ?? ''));
$legalResults = is_array($legalSearchResults ?? null) ? $legalSearchResults : [];
?>
<section class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4">
    <div class="grid gap-4 lg:grid-cols-[1fr_auto]">
        <div>
            <h3 class="font-semibold text-blue-950">Llave jurídica del expediente</h3>
            <p class="mt-2 text-sm leading-6 text-blue-950">
                Busca antecedentes por matrícula o referencia catastral antes de subir el CTL.
                El resultado muestra contexto del avalúo para ubicar correctamente la ficha.
            </p>
            <p class="mt-2 text-xs font-semibold text-blue-900">
                Matrícula: <?= e($legalExpectedRegistry ?: 'pendiente en 3.1') ?> ·
                Ref. catastral: <?= e($legalExpectedCadastral ?: 'pendiente') ?> ·
                Círculo: <?= e($legalExpectedOffice ?: 'pendiente') ?>
            </p>
        </div>
        <form class="grid gap-2 sm:grid-cols-[minmax(220px,1fr)_auto]" method="get"
            action="<?= e(url('avaluos/' . $record['id'] . '/juridicas')) ?>">
            <label class="sr-only" for="legal-matricula-search">Buscar por matrícula o referencia catastral</label>
            <input id="legal-matricula-search" class="input bg-white" name="matricula"
                value="<?= e($legalQuery ?: $legalExpectedRegistry) ?>"
                placeholder="Ej. 060-179699 o referencia catastral">
            <button class="btn-secondary bg-white" type="submit">Buscar</button>
        </form>
    </div>
    <?php if ($legalQuery !== ''): ?>
        <?php if ($legalResults): ?>
            <div class="mt-4 grid gap-3 lg:grid-cols-2">
                <?php $searchResultRoute = 'juridicas'; ?>
                <?php foreach ($legalResults as $item): ?>
                    <?php require BASE_PATH . '/app/Views/appraisals/search-result-card.php'; ?>
                <?php endforeach; ?>
                <?php unset($searchResultRoute); ?>
            </div>
        <?php else: ?>
            <p class="mt-4 rounded-lg bg-white p-3 text-sm font-semibold text-amber-800">
                No hay avalúos guardados con esa matrícula o referencia.
            </p>
        <?php endif; ?>
    <?php endif; ?>
</section>
