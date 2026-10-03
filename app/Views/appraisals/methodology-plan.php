<?php $planMethods=\App\Services\MethodologyWorkflow::METHODS; ?>
<section class="rounded-2xl border bg-white p-5 sm:p-8" aria-labelledby="plan-valoracion-titulo" data-ph-section>
    <p class="eyebrow">Configuración</p>
    <h2 id="plan-valoracion-titulo" class="mt-2 text-2xl font-semibold">Configuración de la valoración</h2>
    <p class="mt-3 leading-6">Define el alcance y elige los métodos aquí. Después entra a la academia del recorrido elegido. Las unidades siguen siendo las registradas en los capítulos 1 y 3.</p>
    <details class="mt-3"><summary class="min-h-11 cursor-pointer font-semibold">Consultar las recomendaciones y su soporte técnico</summary>
        <?php require __DIR__.'/valuation-methodology-decision.php'; ?>
    </details>
    <a class="inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e($flowUrl('1','costo','').'&consult_method=costo') ?>">Consultar academia C1 · Costo sin asignarlo</a>
    <details class="mt-4 rounded-xl border bg-teal-50 p-4">
        <summary class="min-h-11 cursor-pointer font-semibold">Ejemplo: casa con terreno por Mercado y construcción por Costo</summary>
        <p class="mt-2 text-sm leading-6">La casa conserva una sola ficha. Puedes estudiar el inmueble completo o separar terreno y construcción cuando corresponda. Mercado y Renta del mismo inmueble son estimaciones alternativas; no se suman. Terreno y construcción son partes diferentes: documenta su cobertura antes de integrarlas. Si faltan ofertas de lotes, Costo no obtiene automáticamente el terreno por diferencia; sustenta el procedimiento y sus datos antes de adoptarlo.</p>
    </details>
    <?php if ($components===[]): ?><p class="mt-4 rounded-xl border border-dashed p-4">Registra primero las unidades del predio en capítulos 1 y 3. La academia puede consultarse sin asignar un método.</p><?php endif; ?>
    <div class="mt-5 space-y-5">
    <?php foreach ($components as $planKey=>$planUnit): if (isset($planUnit['parent_key']) || isset($planUnit['comparison_key'])) continue;
        $planSplit=!empty($planUnit['container']);
        $planParts=!empty($planUnit['plan_blocked']) ? [] : ($planSplit ? [$planKey.':terreno',$planKey.':construccion'] : [$planKey]);
    ?>
        <article class="rounded-xl border p-4">
            <h3 class="text-xl font-semibold"><?= e($planUnit['label']) ?></h3>
            <p class="mt-1 text-sm text-slate-600"><?= ($planUnit['unit']['unit_kind'] ?? '')==='annex'?'Anexo registrado':'Unidad registrada' ?> · capítulos 1 y 3</p>
            <p class="mt-2 text-sm">Estructura registrada: <?= e(\App\Support\UnitMethodStructure::options()[$planUnit['unit']['method_structure'] ?? ''] ?? 'Por definir en capítulo 1') ?>. Esta estructura orienta el alcance; el analista elige el método.</p>
            <form class="mt-4" method="post" action="<?= e(url($basePath.'/flujo')) ?>" data-module-autosave data-save-in-place data-autosave-endpoint="<?= e(url($basePath.'/flujo')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="component" value="<?= e($planKey) ?>">
                <input type="hidden" name="version" value="<?= (int)($record['methodology_version'] ?? 0) ?>">
                <label class="block font-semibold">Qué partes vas a valorar en <?= e($planUnit['label']) ?>
                    <select class="input" name="plan_parts">
                        <option value="whole" <?= !$planSplit?'selected':'' ?>>Unidad con un alcance propio</option>
                        <?php if ((($record['regimen_ph'] ?? '')==='no' && ($planUnit['unit']['unit_kind'] ?? '')==='property') || $planSplit): ?>
                        <option value="land_building" <?= $planSplit?'selected':'' ?>>Terreno y construcción por separado</option>
                        <?php endif; ?>
                    </select>
                </label>
                <p class="mt-2 text-xs leading-5">La separación terreno/construcción se habilita para una unidad principal con NPH confirmado. En PH conserva el alcance privado y los derechos registrados de cada unidad o anexo. Cambiar la organización conserva los métodos, muestras y conclusiones anteriores; no los redistribuye.</p>
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
