<section id="renta-busqueda" class="scroll-mt-64 rounded-2xl border bg-white p-5 sm:p-8">
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
    <p class="mt-5 text-sm text-slate-600">Elige el portal en la barra superior. Puedes pasar al siguiente o consultar sus muestras desde esa misma barra.</p>
    <?php foreach ($incomeSourceList as $source): ?>
    <div class="mt-4" x-show="incomePortal===<?= e(json_encode($source['label'])) ?>">
        <?php require __DIR__.'/methodology-portal-search-prompt.php'; ?>
    </div>
    <?php endforeach; ?>
    <div class="mt-4 rounded-xl bg-teal-50 p-4">
        <h3 class="font-semibold">Qué hacer después de abrir el portal</h3>
        <ol class="mt-2 list-decimal space-y-2 pl-5 text-sm leading-6">
            <li>Comprueba <strong>Arriendo</strong>, el tipo de esta unidad y la ubicación en el portal. Si el portal abre su portada, selecciona esos filtros y elige la sugerencia de barrio correspondiente a tu ciudad antes de buscar.<?php if (($guide['type_label'] ?? '')==='Oficina'): ?> Deja únicamente <strong>Oficina</strong>; desmarca Casa y Apartamento si vienen seleccionadas.<?php endif; ?></li>
            <li>En la <strong>página de resultados</strong>, pulsa <strong>Ctrl+A y después Ctrl+C</strong>. Copia los anuncios, no el texto de ayuda de este aplicativo.</li>
            <li>Regresa aquí y abre <strong>3. Recoger muestras</strong>. En el portal elegido, haz clic en <strong>Pega la página de resultados</strong> y pulsa <strong>Ctrl+V</strong>.</li>
            <li>Revisa los avisos preparados y los posibles repetidos. Pulsa <strong>Subir sin repetidos de este portal</strong> y espera <strong>Guardado confirmado</strong>. Repite para las demás páginas.</li>
        </ol>
    </div>
    <p class="mt-4 text-sm leading-6">En Metrocuadrado, el área puede no viajar al copiar el listado: queda pendiente para completarla desde la ficha. En Ciencuadras, pulsa Enter después de escribir el barrio y espera los resultados.</p>
    <p class="mt-4 text-sm leading-6">Al revisar avisos, distingue canon, periodicidad, administración, garaje y depósito incluidos. Conserva la fuente y deja por verificar lo que no se publique. Todavía no calculamos rentas ni tasas.</p>
    <button type="button" class="btn-primary mt-4" @click="openTab('capture'); $refs.incomeTabcapture.focus()">Ir a 3. Recoger muestras para pegar los resultados</button>
</section>
