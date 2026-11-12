<aside class="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-4" aria-label="Pasos para recoger arriendos en <?= e($source['label']) ?>">
    <h4 class="font-semibold">Cómo recoger arriendos en <?= e($source['label']) ?></h4>
    <ol class="mt-2 list-decimal space-y-2 pl-5 text-sm leading-6">
        <li>Pulsa <strong>Abrir búsqueda en <?= e($source['label']) ?></strong>. En la nueva pestaña, comprueba <strong>Arriendo</strong>, <strong><?= e($guide['type_label'] ?? 'el tipo de inmueble') ?></strong> y la ubicación buscada.<?php if (!empty($source['instruction'])): ?> <?= e($source['instruction']) ?><?php endif; ?></li>
        <li>Cuando veas la <strong>página de resultados con los anuncios</strong>, pulsa <strong>Ctrl+A</strong> y después <strong>Ctrl+C</strong>. Copia esa página, no solo la dirección del navegador ni una fotografía.</li>
        <li>Regresa a esta pestaña del aplicativo. Más abajo, haz clic en <strong>Pega la página de resultados</strong> y pulsa <strong>Ctrl+V</strong>. Se preparará una vista previa; pegar todavía no guarda.</li>
        <li>Revisa los avisos y pulsa <strong>Subir sin repetidos de este portal</strong>. Espera <strong>Guardado confirmado</strong>. Para continuar con otra página del portal, usa <strong>Pegar otra página</strong> y repite la copia.</li>
    </ol>
    <?php if ($isMetrocuadrado): ?>
    <p class="mt-3 text-sm"><strong>En Metrocuadrado:</strong> si el área no llega al copiar, queda pendiente. Complétala desde la ficha del anuncio antes de usar la muestra. No necesitas pulsar «Empezar de cero» para pegar otra página.</p>
    <?php endif; ?>
</aside>
