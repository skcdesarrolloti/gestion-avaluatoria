<?php
$portalLinks = $portalSources ?? ($guide['source_search']['portal_sources'] ?? []);
$agencyLinks = $agencySources ?? ($guide['source_search']['agency_sources'] ?? []);
$sourceTabs = array_merge($portalLinks, $agencyLinks);
?>
<section id="capture-sources" class="mb-6 rounded-xl border border-blue-100 bg-blue-50 p-4" x-data="{ sourceTab: 0, sourceTask: 'search', sourceLabels: <?= e(json_encode(array_column($sourceTabs, 'label'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?> }" x-init="sourcePortal=sourcePortal || sourceLabels[0]; sourceTab=Math.max(0,sourceLabels.indexOf(sourcePortal)); $watch('sourceTab', value => { sourceTask='search'; sourcePortal=sourceLabels[value] }); $watch('sourcePortal', value => { const index=sourceLabels.findIndex(label => label.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/ inmuebles$/,'')===String(value).normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/ inmuebles$/,'')); if(index>=0) sourceTab=index })">
    <h3 class="text-lg font-semibold text-blue-950" x-text="'Buscar en ' + <?= e(json_encode(array_column($sourceTabs, 'label'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>[sourceTab]">Buscar en <?= e($sourceTabs[0]['label'] ?? 'la fuente seleccionada') ?></h3>
    <?php require __DIR__ . '/methodology-source-buttons.php'; ?>
    <?php if (($guide['type_label'] ?? '') === 'Tipología pendiente'): ?><p class="mt-2 text-sm text-amber-900">Completa el tipo de esta unidad en el capítulo 3 antes de buscar.</p><?php endif; ?>
    <nav class="my-3 flex flex-wrap gap-2" aria-label="Trabajo en el portal">
        <button type="button" class="btn-secondary" :aria-pressed="sourceTask==='search'" @click="sourceTask='search'">Preparar búsqueda</button>
        <button type="button" class="btn-secondary" :aria-pressed="sourceTask==='capture'" @click="sourceTask='capture'">Traer anuncios</button>
    </nav>
    <?php foreach ($sourceTabs as $sourceIndex => $source):
        $isFincaraiz = str_contains(strtolower($source['label']), 'fincaraiz');
        $isMetrocuadrado = str_contains(strtolower($source['label']), 'metrocuadrado');
        $isAgency = $sourceIndex >= count($portalLinks);
    ?>
        <section id="source-panel-<?= $sourceIndex ?>" aria-label="<?= e($source['label']) ?>"
            x-show="sourceTab === <?= $sourceIndex ?>" <?= $sourceIndex ? 'x-cloak' : '' ?> class="mt-4 rounded-xl bg-white p-4">
            <div x-show="sourceTask==='search'"><?php require __DIR__ . '/methodology-portal-search-prompt.php'; ?></div>
            <div x-show="sourceTask==='capture'"><?php require __DIR__ . '/methodology-portal-prompt.php'; ?></div>
            <?php if (($isFincaraiz || $isMetrocuadrado) && isset($record['id'])): ?>
                <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-fincaraiz-zone.php'; ?>
            <?php else: ?>
            <div x-show="sourceTask==='search'">
                <a class="btn-primary min-h-11" href="<?= e($source['url']) ?>" target="_blank" rel="noopener">Abrir búsqueda en <?= e($source['label']) ?></a>
                <?php if (($source['network'] ?? '') === 'Proppit'): ?><p class="mt-2 text-xs">Fuente de captura: Properati · red Proppit.</p><?php endif; ?>
            </div>
            <div x-show="sourceTask==='capture'">
            <?php if ($isFincaraiz): ?>
                <p class="mt-3 text-sm leading-6">El botón aplica los filtros disponibles; no necesitas pegar un prompt. En los resultados, haz clic en el <strong>título o foto de un inmueble</strong>. Copia la dirección de esa ficha, que termina en un código numérico.</p>
                <details class="mt-2 text-sm">
                    <summary class="min-h-11 cursor-pointer py-3 font-semibold">¿Solo aparece un resultado?</summary>
                    <p class="leading-6">Es la oferta que muestra ese portal con esos filtros, no un límite de esta tabla. Continúa en otra fuente. Si amplías el barrio dentro del portal, registra el sector real y justifica su comparabilidad; no cambies el tipo de inmueble solo para completar cantidad.</p>
                </details>
                <?php if (isset($record['id'])) require BASE_PATH . '/app/Views/appraisals/valuation-methodology-comparable-url.php'; ?>
            <?php endif; ?>
            <?php if ($isFincaraiz): ?><details class="mt-3"><summary class="min-h-11 cursor-pointer py-3 text-sm font-semibold">Si la lectura falla: pegar enlace y texto</summary><?php endif; ?>
            <?php
            $batchPortal = ['Ciencuadras' => 'ciencuadras', 'Properati' => 'properati', 'Mercado Libre Inmuebles' => 'mercadolibre'][$source['label']] ?? '';
            require BASE_PATH . '/app/Views/appraisals/' . ($batchPortal && ($guide['type_label'] ?? '') === 'Oficina' && ($guide['business_label'] ?? '') === 'Venta'
                ? 'valuation-methodology-' . $batchPortal . '-paste.php' : 'valuation-methodology-source-paste.php'); ?>
            <?php if ($isFincaraiz): ?></details><?php endif; ?>
            </div>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
    <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
        <button type="button" class="btn-secondary min-h-11" @click="intakeNavigate('review')">Revisar anuncios de este portal</button>
        <span data-autosave-status>Consulta el guardado al incorporar.</span>
    </div>
    <details class="mt-3"><summary class="min-h-11 cursor-pointer py-2 text-sm font-semibold">Resumen de recogida y captura manual (<span x-text="total"></span> anuncios)</summary>
        <?php require __DIR__ . '/methodology-portal-counts.php'; ?>
        <button type="button" class="btn-secondary my-2" @click="searchTab='matriz'; mode='cards'; add()">Capturar muestra manual</button>
    </details>
    <div x-show="sourceTask==='capture'"><?php require __DIR__ . '/methodology-capture-reading.php'; ?></div>
</section>
