<?php
$ivMeta=($ivGroup==='rates'?\App\Services\IncomeValuationFields::rates():\App\Services\IncomeValuationFields::group($ivGroup))[$ivKey];
$ivModel=($ivDynamic?'row':'state.'.$ivGroup).'.'.$ivKey;
$ivName='data['.$ivGroup.']'.(isset($ivIndex)?'['.$ivIndex.']':'').'['.$ivKey.']';
$ivId='iv-'.$ivGroup.'-'.(isset($ivIndex)?$ivIndex.'-':'').$ivKey;
$ivIdExpr=$ivDynamic?"'iv-$ivGroup-'+index+'-$ivKey'":json_encode($ivId);
$ivPath=$ivDynamic?"'data[$ivGroup]['+index+'][$ivKey]'":json_encode($ivName);
$ivValue=isset($ivIndex)?($ivState[$ivGroup][$ivIndex][$ivKey] ?? ''):($ivState[$ivGroup][$ivKey] ?? '');
$ivOptions=$ivMeta['options'];$ivType=$ivMeta['type'];
if ($ivType==='lease') $ivOptions=array_map(static fn($l)=>$l['title'].($l['rent']===null?' · canon por revisar':' · $'.number_format($l['rent'],0,',','.').'/mes'),$ivContext['leases']);
if ($ivType==='candidate') foreach ($ivState['rates'] as $rate) if (($rate['purpose'] ?? '')===str_replace('_id','',$ivKey)) $ivOptions[$rate['id']]=$rate['name'];
$ivAttrs=' id="'.e($ivId).'" :id="'.e($ivIdExpr).'" name="'.e($ivName).'" :name="null" x-model="'.e($ivModel).'"';
$ivAttrs.=' :aria-invalid="Boolean(issues['.e($ivPath).'])" aria-describedby="'.e($ivId).'-help '.e($ivId).'-error"';
$ivAttrs.=' :aria-describedby="'.e($ivIdExpr).'+\'-help \'+'.e($ivIdExpr).'+\'-error\'"';
if ($ivType==='id'): ?>
<input type="hidden" <?= $ivAttrs ?> value="<?= e($ivValue) ?>">
<?php else: ?>
<div class="min-w-0" <?= $ivGroup==='rates' && $ivDynamic?'x-show="rateFieldVisible(\''.e($ivKey).'\',row.method)"':'' ?>>
    <label class="mb-2 block font-semibold" for="<?= e($ivId) ?>" :for="<?= e($ivIdExpr) ?>"><?= e($ivMeta['label']) ?></label>
    <?php if (in_array($ivType,['select','lease','candidate'],true)): ?>
    <select class="input w-full" <?= $ivAttrs ?>><option value="">Selecciona una opción…</option>
        <?php if ($ivType==='candidate'): ?>
        <template x-for="candidate in state.rates.filter(r=>r.purpose==='<?= e(str_replace('_id','',$ivKey)) ?>')" :key="candidate.id"><option :value="candidate.id" x-text="candidate.name || 'Alternativa sin nombre'"></option></template>
        <?php endif; ?>
        <?php foreach ($ivOptions as $val=>$label): ?><option <?= $ivType==='candidate'?'x-init="$el.remove()"':'' ?> value="<?= e($val) ?>" <?= (string)$ivValue===(string)$val?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?>
    </select>
    <?php elseif ($ivType==='textarea'): ?>
    <textarea class="input w-full" rows="2" maxlength="2000" <?= $ivAttrs ?> placeholder="<?= e($ivMeta['help']) ?>"><?= e($ivValue) ?></textarea>
    <?php else: ?>
    <input class="input w-full" type="<?= in_array($ivType,['date','month'],true)?$ivType:'text' ?>" <?= in_array($ivType,['money','rate','percent','number','integer'],true)?'inputmode="decimal"':'' ?>
        <?= $ivAttrs ?> value="<?= e($ivValue) ?>" <?= in_array($ivType,['date','month'],true)?'':'placeholder="'.e($ivMeta['help']).'"' ?>>
    <?php endif; ?>
    <p class="mt-1 text-sm text-slate-600" id="<?= e($ivId) ?>-help" :id="<?= e($ivIdExpr) ?>+'-help'"><?= e($ivMeta['help']) ?></p>
    <p class="mt-1 text-sm text-red-800" role="alert" id="<?= e($ivId) ?>-error" :id="<?= e($ivIdExpr) ?>+'-error'" x-text="issues[<?= e($ivPath) ?>] || ''"><?= e($ivIssues[$ivName] ?? '') ?></p>
</div>
<?php endif; ?>
