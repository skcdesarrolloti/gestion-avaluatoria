<section class="mt-3 rounded-lg border border-teal-100 bg-teal-50 p-3" data-comparable-bulk-panel
    data-default-query="<?= e($baseQuery) ?>">
    <h4 class="font-semibold">2. Capturar aviso de <?= e($source['label']) ?></h4>
    <p class="mt-2 text-sm leading-6">Copia el enlace y el texto de la ficha: precio, área y características. Esta fuente usa lectura de texto; un enlace solo no descarga sus datos. Puedes pegar varios avisos separados por una línea vacía.</p>
    <label for="source-paste-<?= $sourceIndex ?>" class="mt-3 block text-sm font-semibold">Enlace y texto del aviso</label>
    <textarea id="source-paste-<?= $sourceIndex ?>" class="input mt-1 min-h-36 w-full bg-white" data-comparable-bulk-input
        @input.stop @change.stop placeholder="Pega el enlace del inmueble y debajo el texto que copiaste: precio, área, barrio y características."></textarea>
    <button type="button" class="btn-primary mt-3 min-h-11" data-comparable-bulk-apply>Agregar a la tabla como por verificar</button>
    <p role="status" class="mt-2 text-sm" data-comparable-bulk-message></p>
    <p class="mt-2 text-xs">Revisa los campos reconocidos y conserva el soporte del aviso. No se adjuntan fotos ni PDF desde este campo.</p>
</section>
