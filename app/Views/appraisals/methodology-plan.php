<?php $planMethods=\App\Services\MethodologyWorkflow::METHODS; ?>
<section class="rounded-2xl border bg-white p-5 sm:p-8" aria-labelledby="plan-valoracion-titulo" data-ph-section>
    <p class="eyebrow">1 · Organizar la valoración</p>
    <h2 id="plan-valoracion-titulo" class="mt-2 text-2xl font-semibold">Plan de valoración del inmueble</h2>
    <p class="mt-3 leading-6">Primero identifica qué vas a valorar. Después define el método de cada parte y entra a su recorrido. Las unidades siguen siendo las registradas en los capítulos 1 y 3.</p>
    <details class="mt-4 rounded-xl border bg-teal-50 p-4">
        <summary class="min-h-11 cursor-pointer font-semibold">Ejemplo: casa con terreno por Mercado y construcción por Costo</summary>
        <p class="mt-2 text-sm leading-6">La casa conserva una sola ficha. Elige «Terreno y construcción por separado», guarda y actualiza el plan. Se abren dos recorridos vinculados a esa casa, sin crear dos unidades inmobiliarias. Asigna Mercado al terreno y Costo a la construcción, documenta sus alcances y desarrolla cada parte. En la consolidación se revisan los resultados y se evita sumar otra vez el valor de la casa completa.</p>
    </details>
    <?php if ($components===[]): ?><p class="mt-4 rounded-xl border border-dashed p-4">Registra primero las unidades del predio en capítulos 1 y 3. La academia puede consultarse sin asignar un método.</p><?php endif; ?>
    <div class="mt-5 space-y-5">
    <?php foreach ($components as $planKey=>$planUnit): if (isset($planUnit['parent_key'])) continue;
        $planSplit=!empty($planUnit['container']);
        $planParts=!empty($planUnit['plan_blocked']) ? [] : ($planSplit ? [$planKey.':terreno',$planKey.':construccion'] : [$planKey]);
    ?>
        <article class="rounded-xl border p-4">
            <h3 class="text-xl font-semibold"><?= e($planUnit['label']) ?></h3>
            <p class="mt-1 text-sm text-slate-600"><?= ($planUnit['unit']['unit_kind'] ?? '')==='annex'?'Anexo registrado':'Unidad registrada' ?> · capítulos 1 y 3</p>
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
                <div class="rounded-xl bg-slate-50 p-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <div><p class="font-semibold"><?= e(isset($planPart['part']) ? ($planPart['part']==='terreno'?'Terreno':'Construcción') : 'Unidad / alcance completo') ?></p>
                        <p class="mt-1 text-sm">Método: <?= e($planMethods[$planMethod] ?? 'Por definir') ?></p>
                        <p class="mt-1 text-xs"><?= in_array($planMethod,['mercado','renta'],true) ? count(\App\Services\MethodologyComparableScope::rows($allComparableRows,$planPartKey)).' muestras propias' : ($planMethod!==''?'Insumos propios del método':'Método pendiente de selección') ?></p></div>
                    <p class="mt-2 whitespace-pre-wrap break-words text-sm sm:mt-0"><?= e(($planItem['coverage'] ?? '') ?: 'Define qué incluye y excluye este valor antes de trabajar sus insumos.') ?></p>
                    <div class="mt-3 flex flex-wrap gap-2 sm:mt-0">
                        <a class="btn-secondary" href="<?= e($flowUrl('2',$planMethod ?: 'mercado',$planPartKey)) ?>">2 · Definir método y alcance</a>
                        <?php if ($planMethod!==''): $planPrefix=\App\Services\MethodologyWorkflow::PREFIXES[$planMethod]; ?><a class="btn-primary" href="<?= e($flowUrl('1',$planMethod,$planPartKey)) ?>">3 · Abrir recorrido <?= e($planPrefix.'1–'.$planPrefix.'5') ?></a><?php endif; ?>
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
