<?php
$portalLinks = $portalSources ?? ($guide['source_search']['portal_sources'] ?? []);
$agencyLinks = $agencySources ?? ($guide['source_search']['agency_sources'] ?? []);
$sourceTabs = array_merge($portalLinks, $agencyLinks);
?>
<section id="capture-sources" class="mb-6 rounded-xl border border-blue-100 bg-blue-50 p-4" x-data="{ sourceTab: 0, sourceTask: 'search', sourceLabels: <?= e(json_encode(array_column($sourceTabs, 'label'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?> }" x-init="sourcePortal=sourcePortal || sourceLabels[0]; sourceTab=Math.max(0,sourceLabels.indexOf(sourcePortal)); $watch('sourceTab', value => { sourceTask='search'; sourcePortal=sourceLabels[value] }); $watch('sourcePortal', value => { const index=sourceLabels.findIndex(label => label.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/ inmuebles$/,'')===String(value).normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/ inmuebles$/,'')); if(index>=0) sourceTab=index })">
    <h3 class="text-lg font-semibold text-blue-950" x-text="'Buscar en ' + <?= e(json_encode(array_column($sourceTabs, 'label'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>[sourceTab]">Buscar en <?= e($sourceTabs[0]['label'] ?? 'la fuente seleccionada') ?></h3>
    <?php require __DIR__ . '/methodology-source-buttons.php'; ?>
    <?php if (($guide['type_label'] ?? '') === 'Tipología pendiente'): ?><p class="mt-2 text-sm text-amber-900">Completa el tipo de esta unidad en el capítulo 3 antes de buscar.</p><?php endif; ?>
    <?php foreach ($sourceTabs as $sourceIndex => $source):
        $isFincaraiz = str_contains(strtolower($source['label']), 'fincaraiz');
        $isMetrocuadrado = str_contains(strtolower($source['label']), 'metrocuadrado');
        $isAgency = $sourceIndex >= count($portalLinks);
        $sourceHome = !empty($source['domain']) ? 'https://www.' . $source['domain'] . '/' : $source['url'];
        $sourceOpen = ($source['kind'] ?? '') === 'Búsqueda en Google' ? $sourceHome : $source['url'];
    ?>
        <section id="source-panel-<?= $sourceIndex ?>" aria-label="<?= e($source['label']) ?>"
            x-show="sourceTab === <?= $sourceIndex ?>" <?= $sourceIndex ? 'x-cloak' : '' ?> class="mt-4 rounded-xl bg-white p-4">
            <a class="btn-primary my-3 min-h-11" href="<?= e($sourceOpen) ?>" target="_blank" rel="noopener">Abrir búsqueda en <?= e($source['label']) ?></a>
            <details><summary class="min-h-11 cursor-pointer py-3 text-sm font-semibold">Cómo preparar los filtros</summary><?php require __DIR__ . '/methodology-portal-search-prompt.php'; ?></details>
            <?php
            $batchPortal = ['Ciencuadras'=>'ciencuadras','Properati'=>'properati','Mercado Libre Inmuebles'=>'mercadolibre'][$source['label']] ?? '';
            if ($batchPortal && ($guide['type_label'] ?? '') === 'Oficina' && ($guide['business_label'] ?? '') === 'Venta') {
                require __DIR__ . '/valuation-methodology-' . $batchPortal . '-paste.php';
            } else {
                $pasteComponent = 'sourceResultsPaste(' . json_encode($sourceHome) . ',' . json_encode($source['label']) . ',' . ($isAgency ? 'true' : 'false') . ')';
                $pasteLabel = $source['label']; $pasteSearchHelp = ''; $pasteGeneral = true;
                require __DIR__ . '/valuation-methodology-portal-results-paste.php';
                unset($pasteGeneral);
            }
            ?>
            <details class="mt-3"><summary class="min-h-11 cursor-pointer py-3 text-sm font-semibold">Otras formas de captura · sólo si falla el pegado</summary>
            <p class="text-sm">Puedes leer una ficha por su enlace o pegar su texto. Para recoger una página completa, usa el campo de resultados y «Subir no repetidos».</p>
            <?php if (($isFincaraiz || $isMetrocuadrado) && isset($record['id'])): ?>
            <details class="mt-3"><summary class="min-h-11 cursor-pointer py-3 text-sm font-semibold">Búsqueda automática por barrio y lector de ficha (opcional)</summary>
                <?php require __DIR__ . '/valuation-methodology-fincaraiz-zone.php'; ?>
            </details>
            <?php endif; ?>
            <details class="mt-2"><summary class="min-h-11 cursor-pointer py-2 text-sm">Guía de extracción de esta fuente</summary><?php require __DIR__ . '/methodology-portal-prompt.php'; ?></details>
            </details>
        </section>
    <?php endforeach; ?>
    <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
        <button type="button" class="btn-secondary min-h-11" @click="intakeNavigate('review')">Revisar anuncios de este portal</button>
        <button type="submit" form="tabla-madre-83" class="btn-secondary min-h-11">Guardar matriz</button>
        <span role="status" aria-live="polite" data-autosave-status>Al agregar avisos se confirmará aquí su guardado.</span>
    </div>
    <details class="mt-3"><summary class="min-h-11 cursor-pointer py-2 text-sm font-semibold">Resumen de recogida y captura manual (<span x-text="total"></span> anuncios)</summary>
        <?php require __DIR__ . '/methodology-portal-counts.php'; ?>
        <button type="button" class="btn-secondary my-2" @click="searchTab='matriz'; mode='cards'; add()">Capturar muestra manual</button>
    </details>
</section>
