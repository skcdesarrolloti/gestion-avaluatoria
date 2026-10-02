<?php
$coverageChecks = new \App\Services\MarketSubjectChecklist();
$coverageStates = ['ok'=>'OK · descripción registrada', 'missing'=>'Diligenciar', 'difference'=>'Diferencia'];
?>
<section class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
    <h3 class="text-lg font-semibold">Verificación de la unidad principal y los anexos</h3>
    <p class="mt-2 text-sm">Cada fila consulta su propia descripción del numeral 3. Los datos de la oficina no completan los del garaje o depósito. Abre cada unidad para ver sus controles y actualizar la consulta.</p>
    <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[40rem] text-left text-sm">
            <thead><tr><th class="p-2">Unidad</th><th class="p-2">Descripción registrada en 3.1</th><th class="p-2">Verificación</th><th class="p-2">Acción</th></tr></thead>
            <tbody class="divide-y divide-slate-200">
            <?php foreach ($components as $coverageKey=>$coverageComponent):
                $coverageUnit = $coverageComponent['unit'];
                $coverageFlow = $flow[$coverageKey] ?? [];
                $coverageMethod = ($coverageFlow['method'] ?? '') ?: 'mercado';
                $coverageDescription = \App\Services\MarketUnitDescriptionCheck::row($coverageUnit);
                $coverageMarket = $coverageMethod === 'mercado' ? $coverageChecks->build($record, $subject ?? [], $coverageUnit, $phProfile ?? [], $coverageFlow, $units ?? []) : null;
                $coverageSection = $coverageDescription['section'];
                $coverageLink = url('avaluos/' . $record['id'] . '/bien-sujeto?' . http_build_query([
                    'section'=>$coverageSection === 'tipologias' ? 'tipologias' : '', 'unit'=>$coverageKey,
                    'detail'=>$coverageDescription['detail'], 'from'=>'metodologia', 'check_component'=>$coverageKey,
                    'market_check'=>'description']) . '#' . ($coverageSection === 'construction' ? 'construccion' : 'ficha-basica'));
            ?>
                <tr>
                    <th class="p-2 align-top"><?= e($coverageComponent['label']) ?><span class="block text-xs font-normal"><?= ($coverageUnit['unit_kind'] ?? '') === 'annex' ? 'Anexo' : 'Unidad principal' ?></span></th>
                    <td class="max-w-sm p-2 align-top"><p class="mb-2 font-semibold">Tipo: <?= e($coverageDescription['type_label']) ?></p><p class="whitespace-pre-wrap break-words"><?= e(trim((string) ($coverageUnit['notes'] ?? '')) ?: 'Sin descripción propia') ?></p></td>
                    <td class="p-2 align-top"><p class="font-semibold"><?= e($coverageStates[$coverageDescription['state']]) ?></p>
                        <p class="mt-1 text-xs"><?= e($coverageDescription['message']) ?></p>
                        <?php if ($coverageMarket !== null): ?><p class="mt-2 font-semibold">Datos para Mercado: <?= $coverageMarket['ok'] ?> OK · <?= $coverageMarket['pending'] ?> pendientes / diferencias</p><?php endif; ?>
                    </td>
                    <td class="p-2 align-top"><a class="inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e($coverageLink) ?>"><?= $coverageDescription['state'] === 'ok' ? 'Consultar descripción en numeral 3' : 'Diligenciar / corregir en numeral 3' ?></a>
                        <a class="btn-secondary mt-2" href="<?= e($flowUrl('1', $coverageMethod, $coverageKey)) ?>">Ver controles de <?= e($coverageComponent['label']) ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
