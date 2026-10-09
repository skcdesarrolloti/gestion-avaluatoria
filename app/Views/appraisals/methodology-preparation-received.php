<?php
$receivedNotes = [];
foreach ($intakeGroups as $receivedGroup) {
    foreach ($receivedGroup as $receivedSource) {
        if (trim((string) ($receivedSource['source_updates'] ?? '')) !== '' || trim((string) ($receivedSource['intake_note'] ?? '')) !== '') {
            $receivedNotes[] = $receivedSource;
        }
    }
}
$receivedMissing = count(array_filter($analysisRows, static fn($row) => trim((string)($row['price_amount'] ?? '')) === '' || trim((string)($row['area_m2'] ?? '')) === ''));
?>
<details class="rounded-lg border bg-slate-50 p-3">
    <summary class="min-h-11 cursor-pointer font-semibold">Fuentes y notas conservadas de la captura</summary>
    <div class="mt-3 space-y-3">
        <p><strong><?= count($analysisRows) ?> inmuebles recibidos para este recorrido.</strong> Se reutilizan sus grupos de anuncios y la ficha principal elegida; si no hay elección explícita se utiliza la primera ficha del grupo. Aquí no se ejecuta nuevamente la consolidación ni la lectura de los portales.</p>
        <p><strong>Qué revisar primero:</strong> <?= $receivedMissing ?> fichas principales sin oferta o área registrada; <?= count($receivedNotes) ?> anuncios con notas o diferencias conservadas. Las notas pueden contener una decisión ya resuelta: léelas antes de tratarlas como un pendiente nuevo. Tener precio y área registrados tampoco verifica su exactitud o pertinencia.</p>
        <p class="text-sm">Estos conteos describen campos conservados, no una auditoría completa. El estado Proceso finalizado de Captura y consolidación señala que terminó la ejecución de lectura; consulta allí cuántas fichas se leyeron y cuántas quedaron pendientes. Esta pantalla no presume que todas fueron leídas ni verificadas.</p>
        <?php if ($receivedNotes !== []): ?>
        <details class="rounded-lg border bg-white p-3">
            <summary class="min-h-11 cursor-pointer font-semibold">Consultar notas y diferencias recibidas · todas las fuentes del grupo</summary>
            <div class="mt-3 max-h-80 overflow-auto space-y-3">
                <?php foreach ($receivedNotes as $receivedSource): ?>
                <article class="border-b pb-3">
                    <h4 class="font-semibold"><?= e(($receivedSource['source_name'] ?? 'Fuente').' · '.($receivedSource['listing_code'] ?? 'Sin código')) ?></h4>
                    <?php if (trim((string)($receivedSource['intake_note'] ?? '')) !== ''): ?><p class="whitespace-pre-wrap break-words"><strong>Nota conservada:</strong> <?= e($receivedSource['intake_note']) ?></p><?php endif; ?>
                    <?php if (trim((string)($receivedSource['source_updates'] ?? '')) !== ''): ?><p class="whitespace-pre-wrap break-words"><strong>Diferencia conservada:</strong> <?= e($receivedSource['source_updates']) ?></p><?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
        </details>
        <?php endif; ?>
        <?php if (isset($flowUrl)): ?><a class="btn-secondary inline-flex" href="<?= e($flowUrl('3')) ?>">Consultar captura y consolidación</a><?php endif; ?>
    </div>
</details>
