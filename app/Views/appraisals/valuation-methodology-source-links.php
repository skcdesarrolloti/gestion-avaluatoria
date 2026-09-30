<?php
$portalLinks = $portalSources ?? ($guide['source_search']['portal_sources'] ?? []);
$agencyLinks = $agencySources ?? ($guide['source_search']['agency_sources'] ?? []);
$sourceTabs = array_merge($portalLinks, $agencyLinks);
?>
<section id="capture-sources" class="mb-6 rounded-xl border border-blue-100 bg-blue-50 p-4" x-data="{ sourceTab: 0 }">
    <h3 class="text-lg font-semibold text-blue-950">Busca y captura desde la pestaña de cada fuente</h3>
    <p class="mt-2 text-sm leading-6">Cada aviso incorporado agrega una fila a la misma tabla. Puedes repetir la captura y cambiar de fuente sin perder las muestras incorporadas.</p>
    <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
        <strong><span x-text="total"><?= count($comparableRows ?? []) ?></span> muestras en la tabla</strong>
        <a class="btn-secondary min-h-11" href="#capture-review">Ver y revisar tabla</a>
        <span data-autosave-status>Consulta el estado de guardado al incorporar.</span>
    </div>
    <div class="mt-4 flex gap-2" aria-label="Grupo de fuentes">
        <button type="button" class="btn-secondary min-h-11" @click="sourceTab = 0" :aria-pressed="sourceTab < <?= count($portalLinks) ?>">Portales</button>
        <?php if ($agencyLinks): ?><button type="button" class="btn-secondary min-h-11" @click="sourceTab = <?= count($portalLinks) ?>" :aria-pressed="sourceTab >= <?= count($portalLinks) ?>">Inmobiliarias</button><?php endif; ?>
    </div>
    <div role="tablist" aria-label="Fuentes de mercado" class="mt-2 flex flex-wrap gap-2"
        @keydown.arrow-right.prevent="sourceTab = (sourceTab + 1) % <?= count($sourceTabs) ?: 1 ?>; $nextTick(() => $el.querySelectorAll('[role=tab]')[sourceTab].focus())"
        @keydown.arrow-left.prevent="sourceTab = (sourceTab + <?= count($sourceTabs) - 1 ?>) % <?= count($sourceTabs) ?: 1 ?>; $nextTick(() => $el.querySelectorAll('[role=tab]')[sourceTab].focus())">
        <?php foreach ($sourceTabs as $sourceIndex => $source): ?>
            <button type="button" role="tab" id="source-tab-<?= $sourceIndex ?>" aria-controls="source-panel-<?= $sourceIndex ?>"
                x-show="(sourceTab < <?= count($portalLinks) ?>) === <?= $sourceIndex < count($portalLinks) ? 'true' : 'false' ?>"
                :aria-selected="sourceTab === <?= $sourceIndex ?>" :tabindex="sourceTab === <?= $sourceIndex ?> ? 0 : -1"
                @click="sourceTab = <?= $sourceIndex ?>" class="min-h-11 rounded-lg border px-3 py-2 text-sm font-semibold"
                :class="sourceTab === <?= $sourceIndex ?> ? 'bg-blue-700 text-white border-blue-700' : 'bg-white text-blue-900 border-blue-200'">
                <?= e($source['label']) ?>
            </button>
        <?php endforeach; ?>
    </div>
    <?php foreach ($sourceTabs as $sourceIndex => $source):
        $isFincaraiz = str_contains(strtolower($source['label']), 'fincaraiz');
        $isAgency = $sourceIndex >= count($portalLinks);
    ?>
        <section role="tabpanel" id="source-panel-<?= $sourceIndex ?>" aria-labelledby="source-tab-<?= $sourceIndex ?>"
            x-show="sourceTab === <?= $sourceIndex ?>" <?= $sourceIndex ? 'x-cloak' : '' ?> class="mt-4 rounded-xl bg-white p-4">
            <h4 class="font-semibold text-blue-950">1. Buscar en <?= e($source['label']) ?></h4>
            <p class="mt-2 text-sm font-semibold">Tu búsqueda para esta fuente</p>
            <p class="text-anywhere mt-1 rounded-lg bg-slate-50 p-3 text-sm"><?= e($baseQuery ?: 'Completa tipo de inmueble, operación y ubicación en el expediente.') ?></p>
            <p class="mt-2 text-sm leading-6"><?= e($source['instruction'] ?? '') ?></p>
            <p class="mt-1 text-xs text-slate-600"><?= $isAgency ? 'Selecciona los filtros dentro de la inmobiliaria; el enlace abre su sitio.' : e($source['kind'] ?? '') ?></p>
            <a class="btn-primary mt-3 min-h-11" href="<?= e($source['url']) ?>" target="_blank" rel="noopener">Abrir búsqueda en <?= e($source['label']) ?></a>
            <?php if ($isFincaraiz): ?>
                <p class="mt-3 text-sm leading-6">El botón aplica los filtros disponibles; no necesitas pegar un prompt. En los resultados, haz clic en el <strong>título o foto de un inmueble</strong>. Copia la dirección de esa ficha, que termina en un código numérico.</p>
                <details class="mt-2 text-sm">
                    <summary class="min-h-11 cursor-pointer py-3 font-semibold">¿Solo aparece un resultado?</summary>
                    <p class="leading-6">Es la oferta que muestra ese portal con esos filtros, no un límite de esta tabla. Continúa en otra fuente. Si amplías el barrio dentro del portal, registra el sector real y justifica su comparabilidad; no cambies el tipo de inmueble solo para completar cantidad.</p>
                </details>
                <?php if (isset($record['id'])) require BASE_PATH . '/app/Views/appraisals/valuation-methodology-comparable-url.php'; ?>
            <?php endif; ?>
            <?php if ($isFincaraiz): ?><details class="mt-3"><summary class="min-h-11 cursor-pointer py-3 text-sm font-semibold">Si la lectura falla: pegar enlace y texto</summary><?php endif; ?>
            <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-source-paste.php'; ?>
            <?php if ($isFincaraiz): ?></details><?php endif; ?>
        </section>
    <?php endforeach; ?>
</section>
