<?php
$planMethods=\App\Services\MethodologyWorkflow::METHODS;
$planUnits=array_filter($components,static fn($c)=>!isset($c['parent_key']) && !isset($c['comparison_key']));
$planActive=$components[$componentKey]['unit']['id'] ?? $componentKey;
if (!isset($planUnits[$planActive])) $planActive=(string)(array_key_first($planUnits) ?? '');
$planExample=\App\Services\MethodologySelectionHelp::example($record,$planUnits[$planActive] ?? null);
?>
<section class="rounded-2xl border bg-white p-5 sm:p-8" aria-labelledby="plan-valoracion-titulo" data-ph-section>
    <p class="eyebrow">Configuración</p>
    <h2 id="plan-valoracion-titulo" class="mt-2 text-2xl font-semibold">Configuración de la valoración</h2>
    <p class="mt-3 leading-6">Define el alcance y elige los métodos aquí. Después entra a la academia del recorrido elegido. Las unidades siguen siendo las registradas en los capítulos 1 y 3.</p>
    <details class="mt-3"><summary class="min-h-11 cursor-pointer font-semibold">Consultar las recomendaciones y su soporte técnico</summary>
        <?php require __DIR__.'/valuation-methodology-decision.php'; ?>
    </details>
    <a class="inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e($flowUrl('1','costo','').'&consult_method=costo') ?>">Consultar academia C1 · Costo sin asignarlo</a>
    <?php if ($components===[]): ?><p class="mt-4 rounded-xl border border-dashed p-4">Registra primero las unidades del predio en capítulos 1 y 3. La academia puede consultarse sin asignar un método.</p><?php endif; ?>
    <?php if ($planUnits!==[]): ?>
    <nav class="mt-5 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Unidades en configuración">
        <?php foreach ($planUnits as $tabKey=>$tabUnit): ?>
        <a class="btn-secondary shrink-0 <?= $tabKey===$planActive?'bg-white text-teal-900':'' ?>" <?= $tabKey===$planActive?'aria-current="page"':'' ?> href="<?= e($flowUrl('plan',null,$tabKey)) ?>"><?= e($tabUnit['label']) ?></a>
        <?php endforeach; ?>
    </nav>
    <?php endif; ?>
    <details class="mt-4 rounded-xl border bg-teal-50 p-4">
        <summary class="min-h-11 cursor-pointer font-semibold"><?= e($planExample[0]) ?></summary>
        <p class="mt-2 text-sm leading-6"><?= e($planExample[1]) ?></p>
    </details>
    <div class="mt-5 space-y-5">
    <?php foreach ($components as $planKey=>$planUnit): if (isset($planUnit['parent_key']) || isset($planUnit['comparison_key'])) continue;
        if ($planKey!==$planActive) continue;
        $planSplit=!empty($planUnit['container']);
        $planParts=!empty($planUnit['plan_blocked']) ? [] : ($planSplit ? [$planKey.':terreno',$planKey.':construccion'] : [$planKey]);
    ?>
        <article class="rounded-xl border p-4">
            <h3 class="text-xl font-semibold"><?= e($planUnit['label']) ?></h3>
            <p class="mt-1 text-sm text-slate-600"><?= ($planUnit['unit']['unit_kind'] ?? '')==='annex'?'Anexo registrado':'Unidad registrada' ?> · capítulos 1 y 3</p>
            <p class="mt-2 text-sm">Estructura registrada: <?= e(\App\Support\UnitMethodStructure::options()[$planUnit['unit']['method_structure'] ?? ''] ?? 'Por definir en capítulo 1') ?>. Esta estructura orienta el alcance; el analista elige el método.</p>
            <form x-data="{ organization: <?= e(json_encode($planSplit?'land_building':'whole')) ?> }" class="mt-4" method="post" action="<?= e(url($basePath.'/flujo')) ?>" data-module-autosave data-save-in-place data-autosave-endpoint="<?= e(url($basePath.'/flujo')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="component" value="<?= e($planKey) ?>">
                <input type="hidden" name="version" value="<?= (int)($record['methodology_version'] ?? 0) ?>">
                <label class="block font-semibold">Qué partes vas a valorar en <?= e($planUnit['label']) ?>
                    <select class="input" name="plan_parts" x-model="organization">
                        <option value="whole" <?= !$planSplit?'selected':'' ?>>Unidad con un alcance propio</option>
                        <option value="land_building" <?= $planSplit?'selected':'' ?> <?= (($record['regimen_ph'] ?? '')==='no' && ($planUnit['unit']['unit_kind'] ?? '')==='property') || $planSplit?'':'disabled' ?>>Terreno y construcción por separado</option>
                    </select>
                </label>
                <p class="mt-2 rounded-lg bg-teal-50 p-3 text-sm" x-show="organization==='whole'">Se estudia <?= e($planUnit['label']) ?> con sus áreas y derechos registrados, indicando qué incluye su valoración y cómo se relaciona con los garajes, depósitos u otros anexos existentes. Puedes utilizar uno o varios métodos para este mismo alcance.</p>
                <p class="mt-2 rounded-lg bg-teal-50 p-3 text-sm" x-show="organization==='land_building'" <?= $planSplit?'':'x-cloak' ?>>Se estudian el terreno y la construcción en recorridos separados de la misma ficha. Cada parte tendrá método y alcance propios; después se revisa su integración sin volver a sumar el inmueble completo.</p>
                <p class="mt-2 text-xs leading-5">Terreno y construcción por separado se habilita para una unidad principal con NPH confirmado. En PH y anexos aparece como alternativa no disponible. Cambiar la organización conserva los datos anteriores sin redistribuirlos.</p>
                <p class="mt-2 text-sm" data-autosave-status>Autoguardado activo · espera la confirmación y actualiza el plan.</p>
                <button type="submit" class="btn-secondary mt-3">Guardar organización</button>
                <a class="btn-primary mt-3" href="<?= e($flowUrl('plan',null,$planKey)) ?>">Actualizar plan</a>
            </form>
            <?php if (!empty($planUnit['plan_blocked'])): ?><p role="alert" class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm">Revisa la organización: el régimen o el tipo de unidad cambió desde que separaste terreno y construcción. Los recorridos anteriores están conservados e inactivos. Confirma NPH en el expediente o guarda «Unidad con un alcance propio» para reorganizar esta valoración.</p><?php endif; ?>
            <div class="mt-4 space-y-3">
            <?php foreach ($planParts as $planPartKey): $planPart=$components[$planPartKey]; $planItem=$flow[$planPartKey] ?? []; $planMethod=$planItem['method'] ?? ''; ?>
                <div class="rounded-xl bg-slate-50 p-4">
                    <div><p class="font-semibold"><?= e(isset($planPart['part']) ? ($planPart['part']==='terreno'?'Terreno':'Construcción') : 'Unidad / alcance completo') ?></p>
                        <p class="mt-1 text-sm">Método: <?= e($planMethods[$planMethod] ?? 'Por definir') ?></p>
                        <p class="mt-1 text-xs"><?= in_array($planMethod,['mercado','renta'],true) ? count(\App\Services\MethodologyComparableScope::rows($allComparableRows,$planPartKey)).' muestras propias' : ($planMethod!==''?'Insumos propios del método':'Método pendiente de selección') ?></p></div>
                    <p class="mt-2 whitespace-pre-wrap break-words text-sm sm:mt-0"><?= e(($planItem['coverage'] ?? '') ?: 'Define qué incluye y excluye este valor antes de trabajar sus insumos.') ?></p>
                    <?php (static function() use ($record,$units,$components,$flow,$methods,$basePath,$flowUrl,$planPartKey,$planPart,$planItem,$planMethod): void {
                        $componentKey=$planPartKey; $componentLabel=$planPart['label']; $selected=$planItem; $method=$planMethod ?: 'mercado';
                        require __DIR__.'/methodology-selection.php';
                    })(); ?>
                    <div class="mt-4 space-y-2">
                    <?php foreach ([$planPartKey,...array_keys(array_filter($components,static fn($c)=>($c['comparison_key'] ?? '')===$planPartKey))] as $studyKey):
                        $studyMethod=$flow[$studyKey]['method'] ?? ''; if ($studyMethod==='') continue; ?>
                        <p class="font-semibold"><?= e($planMethods[$studyMethod]) ?><?= $studyKey!==$planPartKey?' · contraste del mismo alcance':'' ?></p>
                        <?php if ($studyKey!==$planPartKey): ?>
                        <a class="inline-flex min-h-11 items-center text-sm font-semibold text-blue-800 underline" href="<?= e($flowUrl('2',$studyMethod,$studyKey)) ?>">Definir alcance y justificación del contraste</a>
                        <?php endif; ?>
                        <a class="btn-secondary" href="<?= e($flowUrl('1',$studyMethod,$studyKey)) ?>">Abrir academia de <?= e($planMethods[$studyMethod]) ?></a>
                    <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
            <?php if ($planSplit): ?>
                <p class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm">La ficha original agrupa terreno y construcción; su método y conclusión anteriores se conservan, pero quedan fuera de los recorridos activos y de la consolidación. <?= count(\App\Services\MethodologyComparableScope::rows($allComparableRows,$planKey)) ?> muestras anteriores permanecen vinculadas a esa ficha. Reasígnalas expresamente desde el banco si corresponden a una parte.</p>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
    </div>
</section>
