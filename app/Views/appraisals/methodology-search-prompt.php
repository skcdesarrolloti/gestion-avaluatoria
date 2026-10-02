<?php
$searchQuery = $guide['source_search']['query'] ?? '';
$searchPrompt = 'Buscar ' . $searchQuery . '. Priorizar inmuebles semejantes a ' . ($componentLabel ?? $guide['type_label'])
    . '. Registrar precio o canon publicado, unidad del precio, áreas en m² y su base, componentes incluidos, administración, fuente, URL, fecha y contacto. '
    . 'Conservar evidencia, señalar datos no publicados y revisar posibles duplicados. No inventar información ni adoptar valores automáticamente.';
?>
<section class="mb-5 rounded-xl border border-teal-200 bg-teal-50 p-4" x-show="searchTab === 'captura'" x-data="{ copyStatus: '' }">
    <h3 class="font-semibold">Consulta preparada para esta unidad · <?= e($guide['business_label'] ?? '') ?></h3>
    <label class="mt-3 block text-sm font-semibold">Texto de búsqueda e instrucciones de captura
        <textarea class="input mt-1" rows="4" readonly x-ref="searchPrompt" placeholder="Completa tipología y ubicación para preparar la consulta."><?= e($searchPrompt) ?></textarea>
    </label>
    <button type="button" class="btn-secondary mt-3" @click="navigator.clipboard.writeText($refs.searchPrompt.value).then(() => copyStatus = 'Consulta copiada.').catch(() => copyStatus = 'Selecciona el texto y cópialo con Ctrl+C.')">Copiar consulta</button>
    <p class="mt-2 text-sm" role="status" x-text="copyStatus"></p>
    <p class="mt-2 text-sm">Los botones de los portales aplican los filtros disponibles. El texto sirve como guía para completar filtros y verificar resultados; no todos los portales admiten instrucciones en lenguaje natural.</p>
</section>
