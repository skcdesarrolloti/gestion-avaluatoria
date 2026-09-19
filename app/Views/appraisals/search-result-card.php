<?php
$resultId = (string) ($item['id'] ?? $item['appraisal_id'] ?? '');
$target = (string) ($searchResultRoute ?? 'bien-sujeto');
$resultTitle = trim((string) ($item['titulo'] ?? $item['subject_title'] ?? ''));
$resultAddress = trim((string) (($item['address'] ?? '') ?: ($item['direccion'] ?? '')));
$resultCity = trim((string) (($item['city_name'] ?? '') ?: ($item['municipio'] ?? '')));
$resultNeighborhood = trim((string) ($item['neighborhood_name'] ?? ''));
$resultClient = trim((string) ($item['client_name'] ?? ''));
$resultOwner = trim((string) ($item['property_owner_name'] ?? ''));
$resultRegistry = trim((string) ($item['property_registry'] ?? $item['matrix_registration'] ?? ''));
$resultCadastral = trim((string) ($item['cadastral_reference'] ?? ''));
$resultUpdated = trim((string) ($item['updated_at'] ?? ''));
$label = $resultTitle !== '' ? $resultTitle : 'Avalúo ' . substr($resultId, 0, 8);
?>
<article class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 text-slate-700">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="font-semibold text-slate-950"><?= e($label) ?></p>
            <p class="text-xs font-semibold uppercase text-slate-500">ID <?= e(substr($resultId, 0, 8)) ?></p>
        </div>
        <?php if ($resultId !== ''): ?>
            <a class="btn-secondary min-h-9 px-3 py-1 text-xs" href="<?= e(url('avaluos/' . $resultId . '/' . $target)) ?>">Abrir</a>
        <?php endif; ?>
    </div>
    <p class="mt-2"><strong>Cliente:</strong> <?= e($resultClient ?: 'sin cliente') ?> · <strong>Propietario:</strong> <?= e($resultOwner ?: 'sin propietario') ?></p>
    <p><strong>Ubicación:</strong> <?= e(trim(implode(' · ', array_filter([$resultAddress, $resultNeighborhood, $resultCity]))) ?: 'sin ubicación') ?></p>
    <p><strong>Matrícula/ref.:</strong> <?= e(trim(implode(' · ', array_filter([$resultRegistry, $resultCadastral]))) ?: 'sin dato registral') ?></p>
    <p class="text-xs font-semibold text-slate-500">Actualizado: <?= e($resultUpdated ?: 'sin fecha') ?></p>
</article>
