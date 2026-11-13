<?php
use App\Services\IncomeSampleSelection;
$selectionState=$selected['income_selection'] ?? [];
$selectionRows=IncomeSampleSelection::rows($comparableRows ?? []);
$selectionReport=IncomeSampleSelection::report($selectionRows,$selectionState);
$selectionFlash=json_decode((string)(\App\Core\Session::pullFlash('income_selection_'.$record['id'].'_'.$componentKey) ?: '{}'),true) ?: [];
$selectionSample=$selectionFlash['old']['sample'] ?? $selectionState['last_sample'] ?? '';
$selectionDraft=$selectionFlash['old'] ?? $selectionState['drafts'][$selectionSample] ?? [];
$selectionEndpoint=url('avaluos/'.$record['id'].'/metodologia-valuatoria/renta/seleccion');
$selectionFormat=static fn($n,$d=2)=>$n===null?'No calculable':number_format((float)$n,$d,',','.');
$selectionEvents=$selectionState['events'] ?? [];
$selectionLast=$selectionEvents?end($selectionEvents):null;
$selectionChanged=$selectionLast && $selectionLast['dataset']!==hash('sha256',json_encode($selectionRows,JSON_THROW_ON_ERROR));
$selectionPage=max(1,(int)($_GET['decision_page'] ?? 1));
?>
<section id="renta-analisis" data-income-selection class="rounded-xl border bg-white p-4 sm:p-6" x-data="selection"
    data-selection-state="<?= e(json_encode(['sample'=>$selectionSample,'category'=>$selectionDraft['category'] ?? '',
        'reason'=>$selectionDraft['reason'] ?? '','support'=>$selectionDraft['support'] ?? '',
        'drafts'=>$selectionState['drafts'] ?? new \stdClass(),'excluded'=>$selectionState['excluded'] ?? new \stdClass(),
        'total'=>count($selectionRows),'issues'=>$selectionFlash['errors'] ?? new \stdClass()],JSON_UNESCAPED_UNICODE)) ?>">
    <h2 class="text-xl font-semibold">Análisis y exclusiones de Renta</h2>
    <p class="mt-2">Excluir retira una muestra de este cálculo, conserva el anuncio y registra la decisión. Puedes reincorporarla.</p>
    <p class="mt-2 text-sm text-slate-600">Base exploratoria: importe anunciado mensual / área publicada, en COP/m²/mes. No separa automáticamente administración, IVA ni anexos; no adopta el canon ni el valor del inmueble.</p>
    <?php if ($selectionChanged): ?><p class="mt-3 rounded-lg bg-amber-50 p-3" role="status">La matriz cambió desde la última decisión. Los resultados actuales usan las cifras nuevas; revisa la comparabilidad y las exclusiones. La constancia anterior permanece intacta.</p><?php endif; ?>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm"><caption class="text-left font-semibold">Comparación antes y después de tus exclusiones</caption>
            <thead><tr><th class="p-2 text-left">Medida</th><th class="p-2 text-left">Colección calculable actual</th><th class="p-2 text-left">Muestra conservada</th></tr></thead>
            <tbody><?php foreach (['n'=>'Anuncios','mean'=>'Media COP/m²/mes','median'=>'Mediana COP/m²/mes','sd'=>'Desviación estándar','cv'=>'CV (%)'] as $field=>$label): ?>
                <tr class="border-t"><th class="p-2 text-left font-normal"><?= e($label) ?></th><?php foreach (['baseline','current'] as $which): ?>
                <td class="p-2 text-left"><?= e($selectionFormat($selectionReport[$which][$field],$field==='n'?0:2)) ?></td><?php endforeach; ?></tr>
            <?php endforeach; ?></tbody>
        </table>
    </div>
    <p class="mt-3 font-semibold"><?php $cv=$selectionReport['current']['cv']; ?>
        <?= $cv===null?'CV no calculable: se necesitan al menos dos importes por m².':($cv<=7.5?'CV ≤ 7,5 %: umbral alcanzado; falta sustentar la comparabilidad y adopción.':'CV > 7,5 %: el promedio no cumple el umbral definido.') ?>
    </p>
    <p class="mt-1 text-sm"><?= (int)$selectionReport['total'] ?> anuncios conservados; <?= (int)$selectionReport['blocked'] ?> sin cociente calculable o no participantes en captura; <?= (int)$selectionReport['excluded'] ?> calculables excluidos por decisión.</p>
    <details class="mt-4 rounded-lg border p-3"><summary class="cursor-pointer font-semibold">Academia · excluir con fundamento</summary>
        <p class="mt-2">Un valor alto o bajo es una señal para investigar. Registra el error, la diferencia de modalidad, el duplicado o la evidencia que impide compararlo. Alcanzar 7,5 % no basta como justificación, y el sistema no elimina muestras automáticamente.</p>
        <p class="mt-2">Cuartiles: fuera de Q1 − 1,5 RIC o Q3 + 1,5 RIC. Z robusto: distancia a la mediana mayor de 3,5 usando MAD. Chauvenet: n × probabilidad normal bilateral &lt; 0,5; requiere revisar la hipótesis normal. Ninguna señal demuestra por sí sola un error. Se recalculan sobre la muestra conservada.</p>
        <p class="mt-2"><a class="text-teal-800 underline" href="https://www.igac.gov.co/node/53595" target="_blank" rel="noopener">Resolución IGAC 0941 de 2026 · art. 21</a> · <a class="text-teal-800 underline" href="https://www.nist.gov/publications/detection-outliers-measurements" target="_blank" rel="noopener">NIST · supuestos de las pruebas de atípicos</a></p>
    </details>
    <?php require __DIR__.'/income-selection-samples.php'; require __DIR__.'/income-selection-form.php'; require __DIR__.'/income-selection-history.php'; ?>
</section>
