<div class="mt-3 overflow-x-auto">
    <table class="w-full border-collapse text-left text-sm">
        <thead class="bg-blue-700 text-white"><tr><th class="p-3">Ítem</th><th class="p-3">Descripción</th><th class="p-3">Documento aportado / estado</th></tr></thead>
        <tbody>
        <?php $index = 0; foreach (\App\Services\AppraisalDocumentTable::rows($record) as $row): $index++; ?>
            <tr class="border border-slate-200 odd:bg-blue-50"><td class="p-3"><?= $index ?></td><th scope="row" class="p-3 font-medium"><?= e($row['label']) ?></th><td class="p-3 whitespace-pre-wrap"><?= e($row['text'] !== '' ? $row['text'] : 'Sin información registrada') ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
