<div class="mt-4 overflow-x-auto">
    <table class="w-full min-w-[36rem] text-left text-sm">
        <caption class="mb-2 text-left font-semibold">Anexos del expediente · vínculo y áreas</caption>
        <thead><tr><th class="p-2">Anexo</th><th class="p-2">Principal y relación del área</th><th class="p-2">Acción</th></tr></thead>
        <tbody class="divide-y divide-slate-200">
        <?php foreach ($components as $childKey=>$childComponent): if (($childComponent['unit']['unit_kind'] ?? '') !== 'annex') continue;
            $childScope = \App\Services\MarketPhScope::row($childComponent['unit'], $units); ?>
            <tr><th class="p-2 align-top"><?= e($childComponent['label']) ?></th>
                <td class="p-2"><span class="inline-block rounded-lg p-2 <?= e($scopeTone[$childScope['state']]) ?>"><?= e($childScope['value']) ?></span><p class="mt-1"><?= e($childScope['message']) ?></p></td>
                <td class="p-2"><a class="btn-secondary" href="<?= e($flowUrl('2', 'mercado', $childKey)) ?>#alcance-ph">Definir vínculo del anexo</a></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
