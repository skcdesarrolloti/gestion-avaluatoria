<h3 class="mt-6 font-semibold">Revisar las muestras</h3>
<?php if (!$selectionRows): ?><p class="mt-2">Todavía no hay anuncios de esta investigación. Recoge las muestras antes de analizarlas.</p><?php else: ?>
<div class="mt-3 overflow-x-auto">
    <table class="w-full text-sm"><thead><tr><th class="p-2 text-left">Anuncio</th><th class="p-2 text-left">COP/m²/mes</th><th class="p-2 text-left">Condición y señales</th><th class="p-2 text-left">Participación</th></tr></thead>
    <tbody><?php foreach ($selectionRows as $i=>$sample): $isExcluded=!empty($selectionState['excluded'][$sample['id']]); ?>
    <tr class="border-t" x-show="page===<?= (int)floor($i/20) ?>">
        <td class="p-2"><button type="button" class="btn-secondary text-left" @click="choose('<?= e($sample['id']) ?>')" :disabled="busy || applied"><?= e($sample['label']) ?></button>
            <?php if (filter_var($sample['url'],FILTER_VALIDATE_URL) && preg_match('~^https?://~i',$sample['url'])): ?><a class="block py-3 text-teal-800 underline" href="<?= e($sample['url']) ?>" target="_blank" rel="noopener">Consultar anuncio original</a><?php endif; ?>
            <span class="block text-slate-600">Importe: <?= e($selectionFormat($sample['price'],0)) ?> · área: <?= e($selectionFormat($sample['area'])) ?> m²</span>
        </td>
        <td class="p-2 text-left"><?= e($selectionFormat($sample['value'])) ?></td>
        <td class="p-2"><?= e($sample['condition']) ?><span class="block text-slate-600"><?= e(implode(' · ',$sample['blocked'])) ?></span>
            <span class="block font-semibold"><?= e(implode(' · ',$selectionReport['current']['flags'][$sample['id']] ?? [])) ?></span>
        </td>
        <td class="p-2"><?= $isExcluded?'Excluida con constancia':($sample['blocked']?'Sin cociente calculable':'Incluida · exploratorio') ?></td>
    </tr><?php endforeach; ?></tbody></table>
</div>
<nav class="mt-3 flex flex-wrap items-center gap-3" aria-label="Páginas de muestras de Renta">
    <button type="button" class="btn-secondary" :disabled="page===0" @click="page--">Anteriores</button>
    <span x-text="'Página '+(page+1)+' de '+Math.max(1,Math.ceil(total/20))">Todos los anuncios</span>
    <button type="button" class="btn-secondary" :disabled="(page+1)*20>=total" @click="page++">Siguientes</button>
</nav>
<?php endif; ?>
