<?php
$documentRows = \App\Services\AppraisalDocumentTable::rows([
    'source_document_details' => $field('source_document_details'), 'source_documents_json' => $field('source_documents_json'),
]);
?>
<div class="md:col-span-2 min-w-0">
    <p class="mb-3 text-sm text-slate-600">Relaciona cada documento y su referencia: número, fecha, entidad o estado («Aportado», «No suministrado» o «No aplica», según corresponda). Hasta 1000 caracteres por fila. No se requiere adjuntar archivos aquí.</p>
    <?php foreach ($selectedDocumentKeys as $key): ?>
        <input type="hidden" name="source_documents_selected[]" value="<?= e((string) $key) ?>">
    <?php endforeach; ?>
    <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="w-full table-fixed text-left text-sm">
            <caption class="bg-blue-50 p-3 text-left font-semibold">Documentos aportados · relación para el entregable</caption>
            <colgroup><col class="w-12"><col class="w-1/3"><col></colgroup>
            <thead class="bg-blue-700 text-white"><tr><th class="p-3">Ítem</th><th class="p-3">Descripción</th><th class="p-3">Documento aportado / estado</th></tr></thead>
            <tbody>
            <?php $documentIndex = 0; foreach ($documentRows as $key => $row): $documentIndex++; ?>
                <tr class="border-t border-slate-200 odd:bg-blue-50">
                    <td class="p-3 align-top"><?= $documentIndex ?></td>
                    <th scope="row" class="p-3 align-top font-medium"><label for="document-<?= e($key) ?>"><?= e($row['label']) ?></label></th>
                    <td class="p-3"><textarea id="document-<?= e($key) ?>" class="input min-w-48" rows="3" name="source_document_details[<?= e($key) ?>]" maxlength="1000" placeholder="Referencia del documento o estado de entrega"><?= e($row['text']) ?></textarea></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="mt-2 text-xs text-slate-500">Las marcas anteriores se conservan como antecedentes. Confirma su significado en cada fila; un campo vacío no significa que el documento no se haya suministrado.</p>
</div>
