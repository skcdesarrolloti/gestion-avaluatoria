<?php
$sources = is_array($sectorBankSources ?? null) ? $sectorBankSources : [];
$sourceGroups = [];
foreach ($sources as $source) {
    $sourceGroups[(string) ($source[1] ?? 'Fuentes')][] = $source;
}
$validationItems = [
    'Uso predominante observado',
    'Estado y funcionalidad de vías de acceso',
    'Accesibilidad peatonal y vehicular',
    'Movilidad y transporte observable',
    'Infraestructura urbana visible',
    'Equipamientos y servicios del entorno inmediato',
    'Condiciones ambientales y riesgos',
    'Externalidades positivas y negativas',
    'Dinámica comercial o residencial observable',
    'Registro fotográfico y ubicación de evidencias',
];
?>
<section class="mt-6 grid gap-4 lg:grid-cols-[1.1fr_.9fr]">
    <article class="rounded-xl border border-slate-200 bg-white p-5">
        <p class="eyebrow">Fuentes para nutrir el módulo</p>
        <h3 class="mt-2 text-xl font-semibold">Conexiones previstas</h3>
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <?php foreach ($sourceGroups as $group => $items): ?>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <h4 class="text-sm font-semibold text-slate-900"><?= e($group) ?></h4>
                    <ul class="mt-2 space-y-1 text-xs leading-5 text-slate-600">
                        <?php foreach (array_slice($items, 0, 5) as $item): ?>
                            <li><?= e((string) ($item[2] ?? 'Fuente')) ?> · <?= e((string) ($item[5] ?? 'NO')) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
    <article class="rounded-xl border border-slate-200 bg-white p-5">
        <p class="eyebrow">Validación y trazabilidad</p>
        <h3 class="mt-2 text-xl font-semibold">Controles heredados de InversKC</h3>
        <ol class="mt-4 space-y-2 text-sm leading-6 text-slate-700">
            <li>1. La ficha barrial alimenta el avalúo, pero no reemplaza la verificación del analista.</li>
            <li>2. Cada avalúo conserva una instantánea de la versión sectorial usada.</li>
            <li>3. El semáforo solo sube cuando las secciones estén validadas, aprobadas o disponibles.</li>
        </ol>
        <div class="mt-4 rounded-lg border border-blue-100 bg-blue-50 p-3 text-xs leading-5 text-blue-950">
            <?= e(implode(' · ', array_slice($validationItems, 0, 6))) ?>.
        </div>
    </article>
</section>
