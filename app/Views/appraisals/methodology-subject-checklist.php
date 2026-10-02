<?php
$subjectChecks = (new \App\Services\MarketSubjectChecklist())->build($record, $subject ?? [], $unit, $phProfile ?? [], $item ?? [], $units ?? []);
$checkUnitId = (string) ($unit['id'] ?? '');
$checkComponent = (string) ($key ?? $checkUnitId);
$checkBase = 'avaluos/' . $record['id'] . '/bien-sujeto';
$checkStatus = ['ok'=>'✓ OK · dato y soporte', 'missing'=>'Diligenciar', 'difference'=>'Diferencia', 'na'=>'No aplica'];
$checkTone = ['ok'=>'bg-emerald-100 text-emerald-950', 'missing'=>'bg-red-100 text-red-950', 'difference'=>'bg-amber-100 text-amber-950', 'na'=>'bg-slate-100 text-slate-700'];
$checkRefresh = url('avaluos/' . $record['id'] . '/metodologia-valuatoria?' . http_build_query([
    'component'=>$checkComponent, 'stage'=>'1', 'method'=>$academicMethod ?? 'mercado', 'academy'=>'unidad']));
?>
<div class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h5 class="font-semibold">Verificación de datos guardados · <?= e($component['label']) ?></h5>
        <a class="btn-secondary min-h-11" href="<?= e($checkRefresh) ?>">Actualizar verificación</a>
    </div>
    <?php $trafficChecks = $subjectChecks; require __DIR__ . '/methodology-check-traffic.php'; ?>
    <p class="mt-2 text-xs text-slate-600">Consulta realizada: <?= e(date('Y-m-d H:i:s')) ?>. Se cuentan controles completos, no campos individuales diligenciados.</p>
    <p class="mt-2 text-xs text-slate-600">OK indica dato y soporte registrados y coincidencias automáticas donde son comparables. No certifica validez documental ni comparabilidad del precio. Completa datos físicos y jurídicos en numeral 3, y vínculo y composición PH en M2; espera «Guardado», vuelve y actualiza.</p>
    <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[42rem] text-left text-sm">
            <thead><tr><th class="p-2">Variable</th><th class="p-2">Dato y origen</th><th class="p-2">Estado</th><th class="p-2">Confrontación / acción</th></tr></thead>
            <tbody class="divide-y divide-slate-200">
            <?php foreach ($subjectChecks['rows'] as $checkRow):
                $checkHash = ['surface'=>'superficies','construction'=>'construccion','tipologias'=>'ficha-basica','methodology'=>'alcance-ph'][$checkRow['section']];
                $checkUrl = url($checkBase . '?' . http_build_query(['section'=>$checkRow['section'] === 'tipologias' ? 'tipologias' : '',
                    'unit'=>$checkRow['target_unit'] ?? $checkUnitId, 'detail'=>$checkRow['detail'], 'from'=>'metodologia',
                    'check_component'=>$checkComponent, 'market_check'=>$checkRow['key']]) . '#' . $checkHash);
                if ($checkRow['section'] === 'methodology') $checkUrl = $flowUrl('2', 'mercado', $checkComponent) . '#alcance-ph';
            ?>
                <tr>
                    <th class="p-2 align-top font-semibold"><?= e($checkRow['label']) ?></th>
                    <td class="p-2 align-top break-words"><p><?= e($checkRow['value']) ?></p><p class="mt-1 text-xs text-slate-600"><?= e($checkRow['source']) ?></p></td>
                    <td class="p-2 align-top"><span class="inline-block rounded-full px-2 py-1 text-xs font-semibold <?= e($checkTone[$checkRow['state']]) ?>"><?= e($checkStatus[$checkRow['state']]) ?></span></td>
                    <td class="p-2 align-top"><p><?= e($checkRow['message']) ?></p>
                        <?php if ($checkRow['state'] !== 'na'): ?><a class="inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e($checkUrl) ?>"><?= $checkRow['section'] === 'methodology' ? 'Revisar vínculo y alcance en M2' : ($checkRow['state'] === 'ok' ? 'Consultar dato en numeral 3' : 'Diligenciar / corregir en numeral 3') ?></a><?php endif; ?>
                        <?php if ($checkRow['key'] === 'scope' && $checkRow['state'] === 'difference'): ?><a class="btn-secondary min-h-11" href="<?= e($flowUrl('2', 'mercado', $checkComponent)) ?>">Revisar tratamiento en capítulo 8</a><?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
