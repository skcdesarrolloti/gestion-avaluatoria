<?php
$researchContext=\App\Services\ComparableSearchContext::forMethod($record,$units ?? [],$componentKey,$method ?? 'mercado');
$researchType=\App\Services\ComparablePortalProfiles::defaultType((string)($researchContext['tipo_inmueble'] ?? ''));
$researchCatalog=\App\Services\ResearchFactorCatalog::forType($researchType,$components[$componentKey]['part'] ?? '');
$researchUnit=$components[$componentKey]['unit'] ?? [];
$researchPart=$components[$componentKey]['part'] ?? '';
if (($researchContext['regimen_ph'] ?? '')!=='si' && isset($researchCatalog['area'])) {
    $researchCatalog['area']['label']='Área construida de comparación';
    $researchCatalog['area']['subject']='built_area_adopted_m2';
}
$researchPlan=['target_ratio'=>$selected['research_plan']['target_ratio'] ?? 10,'factors'=>[]];
foreach ($researchCatalog as $key=>$factor) $researchPlan['factors'][$key]=array_replace(
    ['decision'=>'','kind'=>$factor['kind'],'reason'=>'','definition'=>'','categories'=>$factor['categories']],$selected['research_plan']['factors'][$key] ?? []);
$researchEvidence=\App\Services\ResearchPlanEvidence::build($researchCatalog,$researchUnit,$comparableRows,$researchContext,$subject);
$researchConfig=['plan'=>$researchPlan,'catalog'=>$researchCatalog,'evidence'=>$researchEvidence];
?>
<div x-show="searchTab === 'investigacion'" x-cloak>
    <h2 class="text-2xl font-semibold">Plan de investigación · <?= e($componentLabel ?? 'Selecciona una unidad') ?></h2>
    <p class="mt-3 text-sm leading-6">Define qué investigar, dónde hay datos y por qué cada factor puede servir. El sujeto es la referencia; esta configuración prepara el análisis posterior.</p>
    <p class="mt-2 text-sm">Los usos «Filtro» e «Investigar» documentan la intención del analista: no cambian la captura ni descartan anuncios. Consulta la investigación por portal en la pestaña 3.</p>
    <p class="mt-2 text-sm">Si un dato sólo está descrito en texto o el sujeto usa otra clasificación, queda por conciliar. Por ejemplo, «piso alto» no se convierte automáticamente en un número de piso.</p>
    <?php if ($componentKey==='' || $researchCatalog===[] || empty($selected['method'])): ?>
    <p class="mt-4 rounded-xl bg-amber-50 p-4">Selecciona una unidad, confirma su tipo en el numeral 3 y guarda su método en Configuración para preparar su plan.</p>
    <?php else: ?>
    <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm leading-6">
        <strong>Contexto de búsqueda:</strong> <?= e($guide['type_label'] ?? $researchType) ?> · <?= e($researchEvidence['operation']) ?> ·
        <?= e(($researchContext['regimen_ph'] ?? '')==='si'?'PH: área privada y derechos por verificar':'Régimen y composición por verificar') ?>.
        Zona, tipo, operación, régimen y anexos delimitan la investigación; no son ajustes de precio.
        <p>Registra la naturaleza jurídica, inclusión de parqueaderos y depósitos y base del área. La ubicación precisa y sus coordenadas se verifican manualmente en Análisis.</p>
    </div>
    <form x-data="researchPlan(<?= e(json_encode($researchConfig,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)) ?>)" method="post"
        action="<?= e(url($basePath.'/flujo')) ?>" data-module-autosave data-save-in-place data-autosave-endpoint="<?= e(url($basePath.'/flujo')) ?>" class="mt-5">
        <?= csrf_field() ?>
        <input type="hidden" name="component" value="<?= e($componentKey) ?>">
        <input type="hidden" name="version" value="<?= (int)($record['methodology_version'] ?? 0) ?>">
        <input type="hidden" name="research_plan" :value="payload" value="<?= e(json_encode($researchPlan,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)) ?>">
        <?php require __DIR__.'/methodology-research-comparison.php'; ?>
        <div class="rounded-xl border p-4">
            <h3 class="font-semibold">Viabilidad preliminar de la investigación</h3>
            <p class="mt-2 text-sm"><?= count($researchEvidence['groups']) ?> inmuebles potenciales; los anuncios sólo cuentan juntos si su identidad fue vinculada por el analista.
                <?= (int)$researchEvidence['excluded'] ?> grupos no seleccionados o descartados quedan fuera.</p>
            <p class="mt-2 font-semibold" x-text="`${summary.selected} factores candidatos · ${summary.parameters} coeficientes previstos · ${summary.joint} inmuebles con datos conjuntos legibles`"></p>
            <p class="mt-2 text-sm" x-text="`${summary.areaReady} inmuebles con área compatible en m² y contexto legible. El conteo conjunto exige esa área aunque no se elija como factor del modelo.`"></p>
            <label class="mt-3 block text-sm font-semibold">Referencia de inmuebles por factor
                <input type="number" min="1" max="100" x-model.number="plan.target_ratio" class="input mt-2 max-w-40" placeholder="Ej. 10">
            </label>
            <p class="mt-2 text-sm" x-text="`Meta orientativa: ${summary.target} inmuebles. Faltan ${Math.max(0,summary.target-summary.joint)} respecto a esa referencia.`"></p>
            <p class="mt-2 text-sm">Puedes investigar todos los factores y elegir como máximo cuatro candidatos para el modelo. Con diez inmuebles por factor: uno requiere una meta de 10; cuatro, de 40. Son inmuebles diferentes con los datos conjuntos, no anuncios duplicados.</p>
            <p class="mt-2 text-sm">Es una meta de planificación configurable, no una exigencia normativa ni garantía estadística. Las categorías pueden generar varios coeficientes; su suficiencia y codificación se revisarán en Análisis. Aquí todavía no se ajusta una regresión.</p>
            <details class="mt-3" x-show="summary.warnings.length"><summary class="min-h-11 cursor-pointer font-semibold text-amber-800">Pendientes de la configuración</summary>
                <ul class="list-disc pl-5 text-sm"><template x-for="warning in summary.warnings" :key="warning"><li x-text="warningLabel(warning)"></li></template></ul>
            </details>
        </div>
        <p class="my-4 text-sm">Datos de las muestras guardadas al abrir esta consulta. Guarda la captura antes de <a class="font-semibold text-blue-700 underline" href="<?= e(url($basePath.'?component='.rawurlencode($componentKey).'&stage=3&research=1')) ?>">Actualizar consulta</a>.
            Los conteos son de disponibilidad, no de comparables aprobados ni valores adoptados.</p>
        <details class="rounded-xl border p-4">
        <summary class="min-h-11 cursor-pointer font-semibold">Clasificar factores y preparar el modelo · máximo cuatro candidatos</summary>
        <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <?php foreach ($researchCatalog as $key=>$factor): ?>
            <article class="rounded-xl border p-4">
                <h3 class="font-semibold"><?= e($factor['label']) ?> · <?= e($factor['unit']) ?></h3>
                <p class="mt-2 text-sm"><strong>Sujeto:</strong> <span x-text="subjectLabel('<?= e($key) ?>')"></span>.
                    <a class="text-blue-700 underline" href="<?= e(url('avaluos/'.$record['id'].'/bien-sujeto?'.http_build_query(['unit'=>$researchUnit['id'] ?? '',
                        'section'=>$factor['section']==='tipologias'?'tipologias':'','from'=>'metodologia','check_component'=>$componentKey]).
                        '#'.(['surface'=>'superficies','construction'=>'construccion','attributes'=>'atributos'][$factor['section']] ?? 'ficha-basica'))) ?>">Consultar / completar numeral 3</a></p>
                <p class="mt-2 text-sm text-slate-600"><?= e($factor['why']) ?></p>
                <p class="mt-2 text-sm" x-text="`${summary.stats.<?= e($key) ?>.ready.length} inmuebles legibles · ${summary.stats.<?= e($key) ?>.variation} valores o clases diferentes`"></p>
                <p class="mt-1 text-sm text-amber-800" x-text="`${summary.stats.<?= e($key) ?>.missing} sin dato · ${summary.stats.<?= e($key) ?>.formats} por codificar/conciliar · ${summary.stats.<?= e($key) ?>.conflicts} diferencias o relecturas · ${summary.stats.<?= e($key) ?>.context} contextos por verificar`"></p>
                <label class="mt-3 block text-sm font-semibold">Uso propuesto
                    <select class="input mt-1" x-model="plan.factors.<?= e($key) ?>.decision"><option value="">Selecciona el uso</option>
                        <option value="filter">Filtro de contexto</option><option value="investigate">Investigar disponibilidad</option>
                        <option value="model" :disabled="modelUnavailable('<?= e($key) ?>')">Candidato para el modelo · máximo 4</option><option value="defer">Dejar pendiente</option>
                    </select>
                </label>
                <details class="mt-3"><summary class="min-h-11 cursor-pointer text-sm font-semibold">Dónde hay datos y cómo definir el factor</summary>
                    <div class="text-sm"><template x-for="(counts,portal) in summary.stats.<?= e($key) ?>.portals" :key="portal">
                        <p class="mt-2" x-text="`${portal}: ${counts.present}/${counts.ads} anuncios con dato; ${counts.readable} legibles con esta definición.`"></p>
                    </template></div>
                    <p class="mt-2 text-xs">Los anuncios por portal pueden pertenecer a un mismo inmueble. Dato complementario no equivale a dato confirmado; un formato sin equivalencia definida queda pendiente.</p>
                    <label class="mt-3 block text-sm font-semibold">Tipo de variable<select class="input mt-1" x-model="plan.factors.<?= e($key) ?>.kind">
                        <option value="numeric">Numérica: cantidad o medida</option><option value="binary">Binaria: sí=1, no=0</option><option value="categorical">Categorías: clases sin peso económico</option></select></label>
                    <label class="mt-3 block text-sm font-semibold">Definición y forma de medición<textarea class="input mt-1" maxlength="600" rows="2" x-model="plan.factors.<?= e($key) ?>.definition" placeholder="Ej. Número de baños privados; misma definición en sujeto y muestras"></textarea></label>
                    <label class="mt-3 block text-sm font-semibold" x-show="plan.factors.<?= e($key) ?>.kind === 'categorical'">Categorías admitidas: una por línea<textarea class="input mt-1" maxlength="1200" rows="3" x-model="plan.factors.<?= e($key) ?>.categories" placeholder="Sin vista&#10;Interior&#10;Exterior"></textarea></label>
                    <p class="mt-2 text-xs">Las categorías son etiquetas. Codificarlas 0, 1, 2… no les impone distancias de precio; su representación estadística se definirá en Análisis. Desconocido no significa cero.</p>
                    <label class="mt-3 block text-sm font-semibold">Por qué se propone este uso<textarea class="input mt-1" maxlength="600" rows="2" x-model="plan.factors.<?= e($key) ?>.reason" placeholder="Explica relevancia para esta unidad y posibilidad de conseguir datos"></textarea></label>
                </details>
            </article>
        <?php endforeach; ?>
        </div>
        </details>
        <div class="mt-5 flex flex-wrap items-center gap-3"><button type="submit" class="button-primary min-h-11">Guardar plan ahora</button>
            <span data-autosave-status role="status" class="text-sm">Autoguardado activo · el plan se guarda por unidad y método.</span></div>
    </form>
    <?php endif; ?>
</div>
