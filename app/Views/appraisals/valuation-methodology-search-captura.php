<?php
$captureNext = [
    'Captura entre 5 y 10 ofertas o transacciones comparables cuando el mercado lo permita.',
    'Descarta duplicados, avisos sin datos mínimos o inmuebles con uso, derecho o escala no comparable.',
    'Registra enlace, fecha, fuente, precio, área, administración, IVA si aplica y observaciones verificables.',
    'Con las muestras depuradas, pasa a variables y fórmulas antes de cerrar el análisis.',
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
    'Observación de comparabilidad',
];
?>
<div class="grid gap-6 lg:grid-cols-2">
    <section class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
        <p class="text-xs font-bold uppercase text-emerald-800">Protocolo de captura</p>
        <?php if ($captureProtocol === []): ?>
            <p class="mt-3 text-sm leading-6 text-emerald-950">Completa la configuración del método para proponer el protocolo de captura.</p>
        <?php else: ?>
            <ol class="mt-3 space-y-2 text-sm leading-6 text-emerald-950">
                <?php foreach ($captureProtocol as $index => $step): ?>
                    <li><strong><?= e((string) ($index + 1)) ?>.</strong> <?= e((string) $step) ?></li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </section>
    <section class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <p class="text-xs font-bold uppercase text-slate-500">Qué haces después de buscar</p>
        <ol class="mt-3 space-y-2 text-sm leading-6 text-slate-700">
            <?php foreach ($captureNext as $index => $step): ?>
                <li><strong><?= e((string) ($index + 1)) ?>.</strong> <?= e($step) ?></li>
            <?php endforeach; ?>
        </ol>
    </section>
    <section class="rounded-xl border border-blue-100 bg-blue-50 p-4 lg:col-span-2">
        <p class="text-xs font-bold uppercase text-blue-800">Ficha mínima de cada muestra</p>
        <p class="mt-2 text-sm leading-6 text-blue-950">
            Mientras no exista la tabla persistente de comparables, estos son los datos que no se pueden perder
            al explorar portales o páginas de inmobiliarias.
        </p>
        <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($minimumFields as $field): ?>
                <span class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-700"><?= e($field) ?></span>
            <?php endforeach; ?>
        </div>
    </section>
</div>
