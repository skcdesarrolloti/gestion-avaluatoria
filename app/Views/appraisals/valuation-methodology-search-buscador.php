<?php
$baseQuery = trim((string) ($sourceSearch['query'] ?? ''));
$queryParts = is_array($sourceSearch['query_parts'] ?? null) ? $sourceSearch['query_parts'] : [];
$consultSteps = [
    'Abre la inmobiliaria y busca primero dentro de su sitio web o por contacto directo.',
    'Aplica operación, ciudad, barrio o microsector, tipología y rango de área.',
    'Si aparecen pocos resultados, amplía a la localidad o a un microsector comparable sin mezclar usos.',
    'Solo lleva a la matriz ofertas verificables con enlace, fecha, precio, área y evidencia de contacto.',
];
$explorationSteps = [
    'Mantén esta pestaña abierta y abre cada fuente en una pestaña nueva del navegador.',
    'Usa los filtros propios de cada inmobiliaria antes de leer avisos: operación, barrio, tipo, área y precio.',
    'Confirma que el aviso tenga contacto, ubicación aproximada y fecha o señal de vigencia.',
    'Cuando una oferta parezca comparable, vuelve a Captura y registra la ficha mínima antes de seguir navegando.',
];
$tip = static fn (string $text): string => '<span class="help-dot" title="' . e($text) . '">?</span>';
?>
<div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
    <p class="text-xs font-bold uppercase text-blue-800">Buscador 8.3 asistido por el bien sujeto <?= $tip('Origen: datos del expediente, bien sujeto y selección metodológica. Genera una consulta base; no descarga datos automáticamente. Luego diligencias resultados en 2. Capturar.') ?></p>
    <h3 class="mt-2 text-xl font-semibold text-blue-950">Consulta base para portales e inmobiliarias</h3>
    <p class="mt-2 text-sm leading-6 text-blue-950">
        La consulta se arma con operación, tipología, barrio, localidad y ciudad del inmueble. Primero revisa
        portales para amplitud de mercado y luego inmobiliarias locales para confirmar inventario y disponibilidad.
    </p>
    <div class="mt-4 rounded-lg bg-white p-3 font-mono text-sm font-semibold text-slate-900">
        <?= e($baseQuery ?: 'Completa el bien sujeto para formar una consulta automática.') ?>
    </div>
    <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($queryParts as $part): ?>
            <?php $value = trim((string) ($part['value'] ?? '')); ?>
            <div class="rounded-lg bg-white/80 p-3">
                <p class="text-xs font-bold uppercase text-blue-800"><?= e((string) ($part['label'] ?? 'Dato')) ?></p>
                <p class="mt-1 text-sm font-semibold <?= $value === '' ? 'text-orange-800' : 'text-slate-900' ?>">
                    <?= e($value !== '' ? $value : 'Pendiente por completar') ?>
                </p>
                <p class="mt-1 text-xs text-slate-500"><?= e((string) ($part['origin'] ?? '')) ?></p>
            </div>
        <?php endforeach; ?>
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

    <div class="mt-4">
        <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-source-links.php'; ?>
    </div>
</div>
