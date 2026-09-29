<?php $maps = is_array($sectorMaps ?? null) ? $sectorMaps : ['general' => null, 'specific' => [], 'locality_label' => '']; ?>
<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Soporte visual del sector</p>
            <h2 class="mt-2 text-2xl font-semibold">Mapas de localización MIDAS</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                El entregable puede mostrar el mapa general de localidades y el mapa de la localidad del inmueble
                para ubicar al lector antes de entrar en el detalle del sector.
            </p>
        </div>
        <a class="btn-secondary bg-white" href="<?= e(url('midas?grupo=Localidades#documentos-midas')) ?>">Ver Biblioteca MIDAS</a>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <?php foreach ([['Mapa general de localidades', $maps['general'] ?? null]] as [$label, $doc]): ?>
            <?php require BASE_PATH . '/app/Views/appraisals/deliverable-sector-map-card.php'; ?>
        <?php endforeach; ?>
        <?php if (!empty($maps['specific'])): ?>
            <?php foreach ($maps['specific'] as $doc): ?>
                <?php $label = 'Mapa de la localidad ' . (($maps['locality_label'] ?? '') ?: 'del inmueble'); ?>
                <?php require BASE_PATH . '/app/Views/appraisals/deliverable-sector-map-card.php'; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <?php $label = 'Mapa de la localidad específica'; $doc = null; ?>
            <?php require BASE_PATH . '/app/Views/appraisals/deliverable-sector-map-card.php'; ?>
        <?php endif; ?>
    </div>
</section>
