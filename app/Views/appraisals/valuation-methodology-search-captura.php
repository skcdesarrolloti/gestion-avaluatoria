<?php
$captureNext = [
    'Captura muestras verificables hasta acercarte a la meta de 15 observaciones por cada factor que realmente vas a analizar.',
    'Descarta duplicados, avisos sin datos mínimos o inmuebles con uso, derecho o escala no comparable.',
    'Registra enlace, fecha, fuente, precio, área, administración, IVA si aplica y observaciones verificables.',
    'Con las muestras depuradas, pasa a variables y prepara el análisis estadístico robusto del numeral 8.4.',
];
$minimumFields = [
    'Fuente y enlace del aviso',
    'Fecha de consulta',
    'Operación: venta o arriendo',
    'Precio o canon publicado',
    'Área y unidad de comparación',
    'Barrio, sector o dirección aproximada',
    'Administración e IVA si aplica',
    'Teléfono, contacto o inmobiliaria',
    'Factor que soporta para el análisis 8.4',
    'Observación de comparabilidad',
];
$baseQuery = trim((string) ($sourceSearch['query'] ?? ''));
$tip = static fn (string $text): string => '<span class="help-dot" title="' . e($text) . '">?</span>';
?>
<?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-source-links.php'; ?>
<?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-comparable-table.php'; ?>
<details class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
    <summary class="cursor-pointer text-sm font-bold text-slate-700">Ver protocolo y ficha mínima de soporte</summary>
    <div class="mt-4 grid gap-6 lg:grid-cols-2">
        <section class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
        <p class="text-xs font-bold uppercase text-emerald-800">Protocolo de captura <?= $tip('Origen: método seleccionado en 8.2, tipología del bien y diseño de muestra de 8.3. Sirve para decidir si una oferta entra: comparable, verificable y con datos mínimos.') ?></p>
        <?php if ($captureProtocol === []): ?>
            <p class="mt-3 text-sm leading-6 text-emerald-950">
                Si este protocolo aún no aparece, no te detengas: usa la ficha mínima y empieza por la tabla madre.
                Luego el sistema irá alimentando contadores, matriz, mapa y revisión.
            </p>
        <?php else: ?>
            <ol class="mt-3 space-y-2 text-sm leading-6 text-emerald-950">
                <?php foreach ($captureProtocol as $index => $step): ?>
                    <li><strong><?= e((string) ($index + 1)) ?>.</strong> <?= e((string) $step) ?></li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
        </section>
        <section class="rounded-xl border border-slate-200 bg-white p-4">
        <p class="text-xs font-bold uppercase text-slate-500">Qué haces después de buscar <?= $tip('Origen: flujo operativo de 8.3. Después de abrir fuentes, vuelves a esta pestaña, registras una fila por oferta y descartas lo no comparable o sin soporte.') ?></p>
        <ol class="mt-3 space-y-2 text-sm leading-6 text-slate-700">
            <?php foreach ($captureNext as $index => $step): ?>
                <li><strong><?= e((string) ($index + 1)) ?>.</strong> <?= e($step) ?></li>
            <?php endforeach; ?>
        </ol>
        </section>
        <section class="rounded-xl border border-blue-100 bg-blue-50 p-4 lg:col-span-2">
        <p class="text-xs font-bold uppercase text-blue-800">Ficha mínima de cada muestra <?= $tip('Origen: requisitos de trazabilidad del método de mercado y depuración posterior. Se diligencia en la tabla madre; precio, área, fuente, fecha y ubicación mínima son esenciales.') ?></p>
        <p class="mt-2 text-sm leading-6 text-blue-950">
            Estos son los datos mínimos que deben quedar registrados al explorar portales o páginas de inmobiliarias.
            El cuadro siguiente guarda la investigación y deja trazabilidad para la depuración técnica.
        </p>
        <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($minimumFields as $field): ?>
                <span class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-700"><?= e($field) ?></span>
            <?php endforeach; ?>
        </div>
        </section>
    </div>
</details>
