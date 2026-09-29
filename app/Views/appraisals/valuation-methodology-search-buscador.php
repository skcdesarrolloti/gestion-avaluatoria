<?php
$baseQuery = trim((string) ($sourceSearch['query'] ?? ''));
$consultSteps = [
    'Abre una fuente y pega la consulta sugerida en su buscador interno o en Google.',
    'Aplica primero operación, ciudad, barrio o microsector, tipología y rango de área.',
    'Si aparecen pocos resultados, amplía a la localidad o a un microsector comparable sin mezclar usos.',
    'Solo lleva a la matriz ofertas verificables con enlace, fecha, precio, área y evidencia de contacto.',
];
$explorationSteps = [
    'Mantén esta pestaña abierta y abre cada fuente en una pestaña nueva del navegador.',
    'En portales, usa sus filtros propios antes de leer avisos: operación, ciudad, tipo de inmueble, área y precio.',
    'En páginas de inmobiliarias, confirma que el aviso tenga contacto, ubicación aproximada y fecha o señal de vigencia.',
    'Cuando una oferta parezca comparable, vuelve a Captura y registra la ficha mínima antes de seguir navegando.',
];
?>
<div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
    <p class="text-xs font-bold uppercase text-blue-800">Buscador 8.3 asistido por el bien sujeto</p>
    <h3 class="mt-2 text-xl font-semibold text-blue-950">Consulta base para portales y fuentes oficiales</h3>
    <p class="mt-2 text-sm leading-6 text-blue-950">
        La consulta se arma con operación, tipología, barrio, localidad y ciudad del inmueble. No adopta
        resultados automáticamente; prepara la búsqueda para que el analista capture muestras verificables.
    </p>
    <div class="mt-4 rounded-lg bg-white p-3 font-mono text-sm font-semibold text-slate-900">
        <?= e($baseQuery ?: 'Completa el bien sujeto para formar una consulta automática.') ?>
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-[0.9fr_1.1fr]">
        <div class="rounded-lg bg-white/80 p-4">
            <p class="text-xs font-bold uppercase text-blue-800">Cómo hacer cada consulta</p>
            <ol class="mt-3 space-y-2 text-sm leading-6 text-blue-950">
                <?php foreach ($consultSteps as $index => $step): ?>
                    <li><strong><?= e((string) ($index + 1)) ?>.</strong> <?= e($step) ?></li>
                <?php endforeach; ?>
            </ol>
        </div>
        <div class="rounded-lg bg-white/80 p-4 text-sm leading-6 text-blue-950">
            <p><strong>Qué sigue ahora:</strong> abre las fuentes, filtra, selecciona muestras y luego pasa a la pestaña Captura para registrar qué debe quedar documentado antes del análisis.</p>
        </div>
    </div>

    <div class="mt-4 rounded-lg border border-blue-100 bg-white p-4">
        <p class="text-xs font-bold uppercase text-blue-800">Cuando empiezas a explorar portales e inmobiliarias</p>
        <ol class="mt-3 grid gap-2 text-sm leading-6 text-blue-950 lg:grid-cols-2">
            <?php foreach ($explorationSteps as $index => $step): ?>
                <li><strong><?= e((string) ($index + 1)) ?>.</strong> <?= e($step) ?></li>
            <?php endforeach; ?>
        </ol>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-2">
        <div>
            <p class="text-xs font-bold uppercase text-blue-800">Portales inmobiliarios</p>
            <div class="mt-3 grid gap-3">
                <?php foreach ($portalSources as $source): ?>
                    <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-source-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-blue-800">Ciudad y fuentes oficiales</p>
            <div class="mt-3 grid gap-3">
                <?php foreach ($officialSources as $source): ?>
                    <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-source-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
