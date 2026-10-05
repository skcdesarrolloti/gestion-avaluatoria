<div class="mt-4 space-y-3">
    <?php foreach (['Portales' => $portalLinks, 'Inmobiliarias' => $agencyLinks] as $sourceGroup => $links): ?>
    <?php if ($sourceGroup === 'Inmobiliarias'): ?><details><summary class="min-h-11 cursor-pointer py-2 font-semibold">Otras fuentes · inmobiliarias</summary><?php else: ?><div><?php endif; ?>
        <p class="font-semibold"><?= e($sourceGroup) ?></p>
        <nav class="mt-2 flex flex-wrap gap-2" aria-label="<?= e($sourceGroup) ?> de captura">
            <?php foreach ($links as $i => $link): $tabIndex = $i + ($sourceGroup === 'Inmobiliarias' ? count($portalLinks) : 0); ?>
            <button type="button" class="btn-secondary" @click="sourceTab = <?= $tabIndex ?>"
                :aria-pressed="sourceTab === <?= $tabIndex ?>"
                :class="sourceTab === <?= $tabIndex ?> ? 'ring-2 ring-teal-700' : ''"><?= e($link['label']) ?></button>
            <?php endforeach; ?>
        </nav>
    <?php if ($sourceGroup === 'Inmobiliarias'): ?></details><?php else: ?></div><?php endif; ?>
    <?php endforeach; ?>
    <p class="text-sm">Selecciona la fuente y usa su buscador o enlace. Se conservan los filtros disponibles para esa fuente; cuando no admite filtros automáticos, aplícalos en su sitio y pega los resultados aquí. El texto de búsqueda sirve de referencia, no es una instrucción de IA que el portal ejecute.</p>
</div>
