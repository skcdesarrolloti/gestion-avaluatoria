<section id="renta-busqueda" class="rounded-2xl border bg-white p-5 sm:p-8">
    <p class="eyebrow">Renta · Preparar la investigación · <?= e($componentLabel) ?></p>
    <h2 class="mt-2 text-2xl font-semibold">2. Preparar la búsqueda de arriendos</h2>
    <p class="mt-3 leading-6">Revisa los datos del expediente y elige dónde buscar. Esta guía prepara el texto y los filtros que aplicarás dentro del portal; no cambia sus filtros automáticamente.</p>
    <details class="mt-3 rounded-lg bg-teal-50 p-4">
        <summary class="min-h-11 cursor-pointer font-semibold">(?) ¿Por qué revisar estos criterios?</summary>
        <p class="mt-2 text-sm leading-6">Permiten buscar inmuebles semejantes a esta unidad y distinguir lo publicado de lo que falta comprobar. Revisa ubicación, uso, área y qué incluye el arriendo; no descartes automáticamente un aviso por información no publicada.</p>
        <a class="mt-2 inline-flex min-h-11 items-center text-blue-800 underline" href="<?= e($flowUrl('1')) ?>#renta-tema-informacion">Consultar soportes IGAC, IVS y NTS/GTS en el tema 3 de Academia de Renta</a>
    </details>
    <h3 class="mt-5 font-semibold">Criterios disponibles del expediente</h3>
    <p class="mt-2 text-sm text-slate-600">Son datos de consulta. Corrige su origen si están incompletos; aquí no se inventan rangos ni se adoptan cánones.</p>
    <dl class="mt-3 grid gap-3 sm:grid-cols-2">
        <?php if (empty($guide['portal_filters'])): ?><div class="rounded-lg bg-amber-50 p-3"><dt class="font-semibold">Criterios pendientes</dt><dd>Completa los datos del expediente y de esta unidad antes de precisar la búsqueda.</dd></div><?php endif; ?>
        <?php foreach (($guide['portal_filters'] ?? []) as $incomeFilter): ?>
        <div class="rounded-lg bg-slate-50 p-3"><dt class="text-sm font-semibold"><?= e($incomeFilter['label'] ?? '') ?></dt><dd class="mt-1"><?= e($incomeFilter['value'] ?? '') ?></dd></div>
        <?php endforeach; ?>
    </dl>
    <a class="mt-3 inline-flex min-h-11 items-center text-blue-800 underline" href="<?= e(url('avaluos/'.$record['id'].'/bien-sujeto?unit='.$sourceUnitKey)) ?>">Consultar o completar los datos de esta unidad · capítulo 3</a>
    <label class="mt-5 block font-semibold" for="renta-search-source">Portal o inmobiliaria para preparar la búsqueda</label>
    <select id="renta-search-source" class="input" x-model="incomePortal" aria-describedby="renta-search-source-hint">
        <option value="">Selecciona una fuente</option>
        <?php foreach ($incomeSourceList as $incomeSource): ?><option value="<?= e($incomeSource['label']) ?>"><?= e($incomeSource['label']) ?></option><?php endforeach; ?>
    </select>
    <p id="renta-search-source-hint" class="mt-2 text-sm text-slate-600">La selección organiza esta consulta; no se guarda como un dato del avalúo. Los criterios del portal pueden variar.</p>
    <?php foreach ($incomeSourceList as $source): ?>
    <div class="mt-4" x-show="incomePortal===<?= e(json_encode($source['label'])) ?>">
        <?php require __DIR__.'/methodology-portal-search-prompt.php'; ?>
    </div>
    <?php endforeach; ?>
    <p class="mt-4 text-sm leading-6">Al revisar avisos, distingue canon, periodicidad, administración, garaje y depósito incluidos. Conserva la fuente y deja por verificar lo que no se publique. Todavía no calculamos rentas ni tasas.</p>
    <button type="button" class="btn-primary mt-4" @click="openTab('capture'); $refs.incomeTabcapture.focus()">Continuar a 3. Recoger muestras</button>
</section>
