<?php
$portalLinks = $portalSources ?? ($guide['source_search']['portal_sources'] ?? []);
$agencyLinks = $agencySources ?? ($guide['source_search']['agency_sources'] ?? []);
$sourceTabs = array_merge($portalLinks, $agencyLinks);
?>
<section id="capture-sources" class="mb-6 rounded-xl border border-blue-100 bg-blue-50 p-4" x-data="{ sourceTab: 0 }">
    <h3 class="text-lg font-semibold text-blue-950" x-text="'Captura en ' + <?= e(json_encode(array_column($sourceTabs, 'label'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>[sourceTab]">Captura en <?= e($sourceTabs[0]['label'] ?? 'la fuente seleccionada') ?></h3>
    <p class="mt-2 text-sm leading-6">Trabaja con esta fuente. Cada aviso que agregues se suma a tu tabla. Conservamos la lectura por portal, el pegado de avisos y la revisión de repetidos.</p>
    <?php if (($guide['type_label'] ?? '') === 'Tipología pendiente'): ?>
        <p class="mt-2 rounded-lg bg-amber-50 p-3 text-sm text-amber-950">Falta definir el tipo de esta unidad en los capítulos 1 o 3. Por ahora la búsqueda es general de inmuebles; completa su tipo para obtener comparables pertinentes.</p>
    <?php endif; ?>
    <p class="mt-2 text-sm leading-6">Si el portal no permite lectura directa, copia su contenido con Ctrl+A y Ctrl+C y pégalo con Ctrl+V en el lector de esa fuente. Revisa los inmuebles reconocidos antes de incorporarlos. Los datos que el portal no publique se completan manualmente o quedan pendientes; no se inventan.</p>
    <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
        <strong><span x-text="total"><?= count($comparableRows ?? []) ?></span> muestras en la tabla</strong>
        <button type="button" class="btn-secondary min-h-11" @click="searchTab = 'matriz'">Ver Matriz de datos</button>
        <button type="button" class="btn-secondary min-h-11" @click="searchTab = 'matriz'; mode = 'cards'; add()">Capturar muestra manual</button>
        <button type="button" class="btn-secondary min-h-11" @click="searchTab = 'matriz'; mode = 'intake'">Revisar anuncios y soportes</button>
        <span data-autosave-status>Consulta el estado de guardado al incorporar.</span>
    </div>
    <?php require __DIR__ . '/methodology-source-buttons.php'; ?>
    <?php require __DIR__ . '/methodology-portal-counts.php'; ?>
    <details class="mt-3" x-ref="sourcePicker">
        <summary class="min-h-11 cursor-pointer py-3 text-sm font-semibold text-blue-800">Cambiar de fuente</summary>
        <label for="market-source-choice" class="block text-sm font-semibold">Elige el portal o la inmobiliaria para continuar</label>
        <select id="market-source-choice" class="input mt-1 w-full" :value="sourceTab" @input.stop
            @change.stop="sourceTab = Number($event.target.value); $refs.sourcePicker.open = false; $refs.sourcePicker.querySelector('summary').focus()">
            <option value="" disabled>Selecciona una fuente</option>
            <optgroup label="Portales">
                <?php foreach ($portalLinks as $sourceIndex => $source): ?>
                    <option value="<?= $sourceIndex ?>"><?= e($source['label'] . (isset($source['network']) ? ' · Red ' . $source['network'] : '')) ?></option>
                <?php endforeach; ?>
            </optgroup>
            <optgroup label="Inmobiliarias">
                <?php foreach ($agencyLinks as $sourceIndex => $source): ?>
                    <option value="<?= count($portalLinks) + $sourceIndex ?>"><?= e($source['label']) ?></option>
                <?php endforeach; ?>
            </optgroup>
        </select>
    </details>
    <?php foreach ($sourceTabs as $sourceIndex => $source):
        $isFincaraiz = str_contains(strtolower($source['label']), 'fincaraiz');
        $isMetrocuadrado = str_contains(strtolower($source['label']), 'metrocuadrado');
        $isAgency = $sourceIndex >= count($portalLinks);
    ?>
        <section id="source-panel-<?= $sourceIndex ?>" aria-label="<?= e($source['label']) ?>"
            x-show="sourceTab === <?= $sourceIndex ?>" <?= $sourceIndex ? 'x-cloak' : '' ?> class="mt-4 rounded-xl bg-white p-4">
            <?php require __DIR__ . '/methodology-portal-search-prompt.php'; ?>
            <?php require __DIR__ . '/methodology-portal-prompt.php'; ?>
            <?php if (($isFincaraiz || $isMetrocuadrado) && isset($record['id'])): ?>
                <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-fincaraiz-zone.php'; ?>
            <?php else: ?>
            <h4 class="font-semibold text-blue-950">1. Buscar en <?= e($source['label']) ?></h4>
            <?php if (($source['network'] ?? '') === 'Proppit'): ?>
                <p class="mt-2 text-sm">Red Proppit · Captura actual: Properati. Conservamos el portal y el enlace de cada aviso para revisar publicaciones del mismo inmueble en otras fuentes.</p>
                <details class="mt-2 text-sm"><summary class="min-h-11 cursor-pointer py-3">Portales de la red</summary><p>Properati, Mitula, Punto Propiedad, Trovit, Nuroa y Nestoria. Trabajamos una fuente a la vez; este pegado reconoce Properati. Proppit gestiona la publicación y no garantiza que los inventarios de todos los portales sean iguales.</p></details>
            <?php endif; ?>
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
            <?php
            $batchPortal = ['Ciencuadras' => 'ciencuadras', 'Properati' => 'properati', 'Mercado Libre Inmuebles' => 'mercadolibre'][$source['label']] ?? '';
            require BASE_PATH . '/app/Views/appraisals/' . ($batchPortal && ($guide['type_label'] ?? '') === 'Oficina' && ($guide['business_label'] ?? '') === 'Venta'
                ? 'valuation-methodology-' . $batchPortal . '-paste.php' : 'valuation-methodology-source-paste.php'); ?>
            <?php if ($isFincaraiz): ?></details><?php endif; ?>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
    <?php require __DIR__ . '/methodology-capture-reading.php'; ?>
</section>
