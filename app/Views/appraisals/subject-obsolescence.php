<?php
use App\Support\AppraisalObsolescenceCatalog;

$obs = is_array($obsolescenceProfile ?? null) ? $obsolescenceProfile : AppraisalObsolescenceCatalog::defaults();
$groups = AppraisalObsolescenceCatalog::groups();
$scores = AppraisalObsolescenceCatalog::scores();
$scoreHelp = AppraisalObsolescenceCatalog::scoreHelp();
$metaHelp = AppraisalObsolescenceCatalog::metaHelp();
$factorHelp = AppraisalObsolescenceCatalog::factorHelp();
$readerGuidance = AppraisalObsolescenceCatalog::readerGuidance();
$valuationGuidance = AppraisalObsolescenceCatalog::valuationGuidance();
$factor = static fn (string $g, string $k, string $f): string => (string) ($obs['factors'][$g]['items'][$k][$f] ?? '');
$meta = static fn (string $g): string => (string) ($obs['factors'][$g]['meta'] ?? '');
$short = static function (string $value): string { $value = trim(preg_replace('/\s+/u', ' ', $value) ?? ''); return mb_strlen($value) > 120 ? mb_substr($value, 0, 117) . '…' : $value; };
$levelFromIeo = static fn (float $ieo): string => $ieo == 0.0 ? 'Sin hallazgos' : ($ieo <= 33.33 ? 'Leve' : ($ieo <= 66.67 ? 'Relevante' : 'Crítica'));
$metric = static function (string $groupKey, array $items) use ($factor, $levelFromIeo): array {
    $sum = $applicable = $missingEvidence = 0; $findings = [];
    foreach ($items as $key => $label) {
        $score = $factor($groupKey, (string) $key, 'score');
        if ($score === '' || $score === 'na') continue;
        $number = max(0, min(3, (int) $score)); $sum += $number; $applicable++;
        if ($number >= 2 && trim($factor($groupKey, (string) $key, 'evidence')) === '') $missingEvidence++;
        if ($number > 0) $findings[] = (string) $label;
    }
    $ieo = $applicable > 0 ? round(($sum / ($applicable * 3)) * 100, 1) : 0.0;
    return ['ieo'=>$ieo, 'level'=>$levelFromIeo($ieo), 'applicable'=>$applicable, 'sum'=>$sum, 'missing'=>$missingEvidence, 'findings'=>$findings];
};
$metrics = []; $scoreStateAll = []; $evidenceStateAll = [];
foreach ($groups as $groupKey => [, , , , $items]) {
    $metrics[$groupKey] = $metric((string) $groupKey, $items);
    $scoreStateAll[$groupKey] = []; $evidenceStateAll[$groupKey] = [];
    foreach ($items as $itemKey => $_label) {
        $scoreStateAll[$groupKey][(string) $itemKey] = $factor((string) $groupKey, (string) $itemKey, 'score');
        $evidenceStateAll[$groupKey][(string) $itemKey] = $factor((string) $groupKey, (string) $itemKey, 'evidence');
    }
}
$totalSum = array_sum(array_column($metrics, 'sum'));
$totalApplicable = array_sum(array_column($metrics, 'applicable'));
$globalIeo = $totalApplicable > 0 ? round(($totalSum / ($totalApplicable * 3)) * 100, 1) : 0.0;
$globalLevel = $levelFromIeo($globalIeo);
$activeFindings = [];
foreach ($groups as $groupKey => [, $title]) if ($metrics[(string) $groupKey]['findings']) $activeFindings[] = $title . ': ' . implode(', ', array_slice($metrics[(string) $groupKey]['findings'], 0, 4));
$noFindingText = 'Obsolescencia física: ' . $readerGuidance['fisica']['no_finding'] . ' Obsolescencia funcional: ' . $readerGuidance['funcional']['no_finding'] . ' Obsolescencia externa: ' . $readerGuidance['externa']['no_finding'] . ' El IEO es una lectura de control y no genera depreciación automática.';
$generated = $activeFindings ? 'Obsolescencias: se registran hallazgos de nivel ' . mb_strtolower($globalLevel) . ' con IEO diagnóstico global de ' . number_format($globalIeo, 1, ',', '.') . ' %. ' . implode('; ', $activeFindings) . '. La incidencia económica se sustenta aparte solo si el efecto es material.' : $noFindingText;
$summary = trim((string) ($obs['summary_text'] ?? '')) ?: $generated;
$pill = static function (string $state): string { return match ($state) { 'ok' => '<span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Completo</span>', 'warn' => '<span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Revisar</span>', default => '<span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">Opcional</span>' }; };
$groupInfo = [];
foreach ($groups as $groupKey => [$code, $title, , , $items]) {
    $groupInfo[$groupKey] = ['code' => $code, 'title' => $title, 'items' => $items, 'no_finding' => $readerGuidance[$groupKey]['no_finding']];
}
$scoreJson = json_encode($scoreStateAll, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_THROW_ON_ERROR);
$evidenceJson = json_encode($evidenceStateAll, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_THROW_ON_ERROR);
$scoreHelpJson = json_encode($scoreHelp, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_THROW_ON_ERROR);
$groupInfoJson = json_encode($groupInfo, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_THROW_ON_ERROR);
?>
<form class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" method="post" action="<?= e(url($subjectActionBase . '/obsolescencias')) ?>" data-module-autosave data-save-in-place data-autosave-endpoint="<?= e(url($subjectActionBase . '/obsolescencias/autoguardar')) ?>" data-obsolescence-scores='<?= e($scoreJson) ?>' data-obsolescence-evidence='<?= e($evidenceJson) ?>' data-obsolescence-score-help='<?= e($scoreHelpJson) ?>' data-obsolescence-groups='<?= e($groupInfoJson) ?>' data-obsolescence-summary='<?= e($summary) ?>' x-data="obsolescenceLive">
    <?= csrf_field() ?>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div><p class="eyebrow">3.6 Obsolescencias</p><h2 class="mt-2 text-2xl font-semibold">Lectura técnica de obsolescencias</h2><p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Pensado para sustentar el texto del informe. La calificación ordena la revisión; la afectación del valor se decide aparte si hay soporte material.</p></div>
        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800" x-text="globalLabel()">IEO diagnóstico global: <?= e(number_format($globalIeo, 1, ',', '.')) ?> % · <?= e($globalLevel) ?></span>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-[1.3fr_1fr]">
        <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-sm leading-6 text-indigo-950">
            <h3 class="font-semibold">Academia para el analista</h3>
            <div class="mt-3 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                <p><strong>1. Revise factor por factor.</strong> Si está bien, use “0 · Sin hallazgo”.</p>
                <p><strong>2. Califique solo lo observado.</strong> Pendiente y N/A no entran al índice.</p>
                <p><strong>3. Soporte hallazgos.</strong> Relevante y crítica deben explicar la evidencia.</p>
                <p><strong>4. Lea el resultado.</strong> IEO = puntos obtenidos / puntos posibles revisados.</p>
                <p><strong>5. No castigue automático.</strong> El IEO no es depreciación ni descuento.</p>
                <p><strong>6. Si afecta valor, sustente.</strong> Use costos, mercado, comparables o criterio verificable.</p>
            </div>
        </div>
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
            <h3 class="font-semibold">Dónde se afecta el avalúo si aplica</h3>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                <?php foreach ($valuationGuidance as $note): ?><li><?= e($note) ?></li><?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="mt-6 grid gap-4 xl:grid-cols-[1fr_1fr]">
        <label class="label rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-emerald-950">Texto editable para el Entregable<textarea class="input mt-2 min-h-36 bg-white" rows="5" name="summary_text" x-model="summaryText" placeholder="Texto profesional de obsolescencias para incorporar al informe"><?= e($summary) ?></textarea><span class="mt-1 block text-xs font-normal text-emerald-800">Este es el texto que se guarda. Puedes usar el sugerido y luego ajustarlo.</span></label>
        <div class="rounded-xl border border-emerald-100 bg-white p-4 text-sm leading-6 text-slate-700"><div class="flex flex-wrap items-center justify-between gap-3"><p class="font-semibold text-emerald-900">Resultado sugerido para el entregable</p><button type="button" class="rounded-lg bg-emerald-100 px-3 py-2 text-xs font-bold text-emerald-800" @click="summaryText = deliverableText()">Usar este texto</button></div><p class="mt-2 whitespace-pre-wrap" x-text="deliverableText()"><?= e($generated) ?></p></div>
    </div>

    <div class="mt-5 rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6">
        <h3 class="font-semibold text-slate-900">Matriz de control para construir el Entregable</h3>
        <div class="mt-3 overflow-x-auto"><table class="w-full min-w-[54rem] text-left text-sm"><thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Campo</th><th class="py-2 pr-3">Lectura</th><th class="py-2 pr-3">Estado</th><th class="py-2">Qué hacer</th></tr></thead><tbody class="divide-y divide-slate-100">
            <tr><td class="py-2 pr-3 font-semibold">Texto editable</td><td class="py-2 pr-3">Texto para informe</td><td class="py-2 pr-3"><?= $pill('ok') ?></td><td class="py-2 text-slate-600">Ajustar con el criterio del analista.</td></tr>
            <?php foreach ($groups as $groupKey => [$code, $title]): ?>
                <tr><td class="py-2 pr-3 font-semibold"><?= e($title) ?></td><td class="py-2 pr-3" x-text="groupSummary('<?= e($code) ?>', '<?= e((string) $groupKey) ?>')"><?= e($code . ' · ' . number_format((float) $metrics[(string) $groupKey]['ieo'], 1, ',', '.') . ' % · ' . $metrics[(string) $groupKey]['level']) ?></td><td class="py-2 pr-3"><span class="rounded-full px-3 py-1 text-xs font-bold" :class="stateClass('<?= e((string) $groupKey) ?>')" x-text="stateText('<?= e((string) $groupKey) ?>')"><?= e($metrics[(string) $groupKey]['missing'] > 0 ? 'Revisar' : ($metrics[(string) $groupKey]['applicable'] > 0 ? 'Completo' : 'Opcional')) ?></span></td><td class="py-2 text-slate-600" x-text="actionText('<?= e((string) $groupKey) ?>')"><?= e($metrics[(string) $groupKey]['missing'] > 0 ? 'Agregar soporte breve en hallazgos relevantes o críticos.' : ($metrics[(string) $groupKey]['applicable'] > 0 ? 'Listo para lectura.' : 'Pendiente; marque 0 si ya revisó y no encontró hallazgos.')) ?></td></tr>
            <?php endforeach; ?>
            <tr><td class="py-2 pr-3 font-semibold">Incidencia económica</td><td class="py-2 pr-3"><?= e($short((string) ($obs['quantification_text'] ?? '')) ?: 'Sin efecto económico definido') ?></td><td class="py-2 pr-3"><?= $pill(trim((string) ($obs['quantification_text'] ?? '')) !== '' ? 'ok' : 'warn') ?></td><td class="py-2 text-slate-600">Definir si no hay efecto material o si se cuantifica aparte.</td></tr>
        </tbody></table></div>
    </div>

    <div class="mt-5 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2"><?php foreach ($groups as $groupKey => [$code, $title]): ?><button type="button" class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold" @click="activeObs='<?= e((string) $groupKey) ?>'" :class="activeObs === '<?= e((string) $groupKey) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'"><?= e($code . ' · ' . $title) ?></button><?php endforeach; ?></div>

    <?php foreach ($groups as $groupKey => [$code, $title, $metaLabel, $metaOptions, $items]): ?>
        <?php require BASE_PATH . '/app/Views/appraisals/subject-obsolescence-group.php'; ?>
    <?php endforeach; ?>

    <div class="mt-5 grid gap-4 lg:grid-cols-3"><label class="label">Diagnóstico técnico<textarea class="input min-h-20" rows="3" name="diagnosis_text" placeholder="Ejemplo: revisada la condición física, funcional y externa, no se evidencian hallazgos materiales."><?= e((string) ($obs['diagnosis_text'] ?? '')) ?></textarea></label><label class="label">Incidencia económica<textarea class="input min-h-20" rows="3" name="quantification_text" placeholder="Ejemplo: sin efecto material observado; no se aplica descuento por obsolescencia."><?= e((string) ($obs['quantification_text'] ?? '')) ?></textarea></label><label class="label">Fundamento técnico<textarea class="input min-h-20" rows="3" name="normative_text" placeholder="Ejemplo: revisión técnica, visita, soportes documentales, mercado y metodología del informe."><?= e((string) ($obs['normative_text'] ?? '')) ?></textarea></label></div>
    <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950"><strong>Regla práctica:</strong> si todo está bien, marque los factores revisados como “0 · Sin hallazgo”, use el texto de “si está bien” y deje la incidencia económica como “sin efecto material observado”.</div><div class="mt-6 flex justify-end"><button class="btn-primary" type="submit">Guardar obsolescencias</button></div>
</form>
