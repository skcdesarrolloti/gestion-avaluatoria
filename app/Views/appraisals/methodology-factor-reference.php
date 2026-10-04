<?php $referenceTypes=\App\Services\ComparablePortalProfiles::types(); ?>
<section class="mt-5 rounded-xl border bg-white p-4" x-show="configTab==='factors'" x-cloak x-data="{referenceType:<?= e(json_encode($planReferenceType)) ?>}" aria-label="Catálogo permanente de factores">
    <h3 class="text-xl font-semibold">Factores por tipo de inmueble · catálogo permanente</h3>
    <p class="mt-2 text-sm">Define aquí las escalas que reutilizarás en tus avalúos. Las clasificaciones guardadas anteriores se conservan. En Insumos eliges qué investigar, hasta cuatro candidatos y cómo conseguir el dato.</p>
    <a class="mt-2 inline-flex min-h-11 items-center text-blue-700 underline" href="<?= e($flowUrl('plan',null,$planActive).'&factor_catalog=1') ?>">Actualizar catálogo después de guardar escalas</a>
    <label class="mt-3 block text-sm font-semibold">Tipo de inmueble del catálogo<select class="input mt-1" x-model="referenceType">
        <option value="">Selecciona el tipo de inmueble</option>
        <?php foreach ($referenceTypes as $referenceKey=>$referenceLabel): ?><option value="<?= e($referenceKey) ?>"><?= e($referenceLabel) ?></option><?php endforeach; ?>
    </select></label>
    <p class="mt-3 text-xs">«Referenciado en portal» significa que la investigación de fichas públicas menciona ese campo o atributo en texto; no garantiza dato en cada aviso ni lectura automática. Si no está documentado, planifica contacto, visita o soporte manual. Desconocido no equivale a cero. Destinación es filtro y queda fuera de los factores.</p>
    <p class="mt-3 rounded bg-teal-50 p-3 text-sm">Área en m²: base obligatoria de cálculo, no candidato. COP/m² = precio / área compatible de terreno, construcción o privada según el alcance.</p>
    <?php foreach ($referenceTypes as $referenceKey=>$referenceLabel): ?>
    <div x-show="referenceType===<?= e(json_encode($referenceKey)) ?>" class="mt-3 max-w-full overflow-x-auto" tabindex="0" role="region" aria-label="Factores de <?= e($referenceLabel) ?>">
        <table class="w-full text-left text-sm"><caption class="sr-only">Catálogo de factores para <?= e($referenceLabel) ?></caption>
            <thead><tr><th scope="col" class="min-w-40 p-3">Factor</th><th scope="col" class="min-w-60 p-3">Definición y jerarquía / clases</th><th scope="col" class="min-w-52 p-3">Dónde focalizar la investigación</th></tr></thead>
            <tbody><?php foreach (\App\Services\ResearchFactorScaleInput::catalog(\App\Services\ResearchFactorCatalog::forType($referenceKey),$factorScales ?? []) as $factorKey=>$factor): if (in_array($factorKey,['destination','area','land','built'],true)) continue;
                $references=\App\Services\ResearchFactorReference::portals($referenceKey,$factorKey); ?>
                <tr class="border-t"><th scope="row" class="p-3"><?= e($factor['label']) ?> · <?= e($factor['unit']) ?></th>
                    <td class="p-3"><p><?= e($factor['why']) ?></p><p class="mt-2 font-semibold"><?= e(\App\Services\ResearchFactorReference::scale($factor)) ?></p><?php require __DIR__.'/methodology-factor-scale-editor.php'; ?></td>
                    <td class="p-3"><?php if ($references): ?><span class="rounded bg-emerald-50 px-2 py-1 text-emerald-900">Referenciado en portal</span>
                        <?php foreach ($references as $reference): ?><a class="mt-2 inline-flex min-h-11 items-center text-blue-700 underline" href="<?= e($reference['url']) ?>" target="_blank" rel="noopener noreferrer"><?= e($reference['label']) ?></a> <?php endforeach; ?>
                        <p class="text-xs">Completa manualmente los faltantes.</p>
                    <?php else: ?><span class="rounded bg-amber-50 px-2 py-1 text-amber-900">No documentado en portales investigados</span><p class="mt-2">Investigación manual si es relevante.</p><?php endif; ?></td>
                </tr>
            <?php endforeach; ?></tbody>
        </table>
    </div><?php endforeach; ?>
</section>
