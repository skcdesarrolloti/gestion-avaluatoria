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
    <summary class="min-h-11 cursor-pointer font-semibold">Lo recibido de Insumos · conservar el trabajo y atender pendientes</summary>
    <div class="mt-3 space-y-3">
        <p><strong><?= count($analysisRows) ?> inmuebles recibidos para este recorrido.</strong> Se reutilizan sus grupos de anuncios y la ficha principal elegida; si no hay elección explícita se utiliza la primera ficha del grupo. Aquí no se ejecuta nuevamente la consolidación ni la lectura de los portales.</p>
        <div class="overflow-auto"><table class="min-w-full text-left text-sm">
            <thead><tr><th class="p-2">En Insumos</th><th class="p-2">En Preparar los datos</th></tr></thead>
            <tbody>
                <tr><td class="p-2">Recoger por portal: anuncios, enlaces y datos publicados.</td><td class="p-2">Usar lo registrado; atender vacíos o discrepancias que afecten el cálculo.</td></tr>
                <tr><td class="p-2">Consolidación: decisiones sobre repetidos y ficha principal.</td><td class="p-2">Conservar esas decisiones; volver a Insumos si aparece nueva evidencia de identidad o una diferencia sin resolver.</td></tr>
                <tr><td class="p-2">Completar inmuebles únicos: lectura de la ficha principal.</td><td class="p-2">Aprovechar los atributos obtenidos y decidir su pertinencia, unidades y soporte para el análisis.</td></tr>
            </tbody>
        </table></div>
        <p><strong>Qué revisar primero:</strong> <?= $receivedMissing ?> fichas principales sin oferta o área registrada; <?= count($receivedNotes) ?> anuncios con notas o diferencias conservadas. Las notas pueden contener una decisión ya resuelta: léelas antes de tratarlas como un pendiente nuevo. Tener precio y área registrados tampoco verifica su exactitud o pertinencia.</p>
        <p class="text-sm">Estos conteos describen campos conservados, no una auditoría completa. El estado Proceso finalizado de Insumos señala que terminó la ejecución de lectura; consulta allí cuántas fichas se leyeron y cuántas quedaron pendientes. Esta pantalla no presume que todas fueron leídas ni verificadas.</p>
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
        <?php if (isset($flowUrl)): ?><a class="btn-secondary inline-flex" href="<?= e($flowUrl('3')) ?>">Volver a Insumos para corregir o consultar la lectura</a><?php endif; ?>
    </div>
</details>
