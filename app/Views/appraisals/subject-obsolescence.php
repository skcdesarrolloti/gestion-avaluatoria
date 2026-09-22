<?php
use App\Support\AppraisalObsolescenceCatalog;
$obs = is_array($obsolescenceProfile ?? null) ? $obsolescenceProfile : AppraisalObsolescenceCatalog::defaults();
$groups = AppraisalObsolescenceCatalog::groups(); $scores = AppraisalObsolescenceCatalog::scores();
$factor = static fn (string $g, string $k, string $f): string => (string) ($obs['factors'][$g]['items'][$k][$f] ?? '');
$meta = static fn (string $g): string => (string) ($obs['factors'][$g]['meta'] ?? '');
$short = static function (string $value): string { $value = trim(preg_replace('/\s+/u', ' ', $value) ?? ''); return mb_strlen($value) > 130 ? mb_substr($value, 0, 127) . '…' : $value; };
$metric = static function (string $groupKey, array $items) use ($factor): array {
    $sum = $applicable = $missingEvidence = 0; $high = [];
    foreach ($items as $key => $label) {
        $score = $factor($groupKey, (string) $key, 'score'); if ($score === '' || $score === 'na') continue;
        $number = max(0, min(3, (int) $score)); $sum += $number; $applicable++;
        if ($number >= 2 && trim($factor($groupKey, (string) $key, 'evidence')) === '') $missingEvidence++;
        if ($number >= 2) $high[] = (string) $label;
    }
    $ieo = $applicable > 0 ? round(($sum / ($applicable * 3)) * 100, 1) : 0.0;
    $level = $ieo == 0.0 ? 'No evidenciada' : ($ieo <= 33.33 ? 'Baja' : ($ieo <= 66.67 ? 'Media' : 'Alta'));
    return ['ieo'=>$ieo, 'level'=>$level, 'applicable'=>$applicable, 'sum'=>$sum, 'missing'=>$missingEvidence, 'high'=>$high];
};
$metrics = []; foreach ($groups as $groupKey => [, , , , $items]) $metrics[$groupKey] = $metric((string) $groupKey, $items);
$totalSum = array_sum(array_column($metrics, 'sum')); $totalApplicable = array_sum(array_column($metrics, 'applicable'));
$globalIeo = $totalApplicable > 0 ? round(($totalSum / ($totalApplicable * 3)) * 100, 1) : 0.0;
$globalLevel = $globalIeo == 0.0 ? 'No evidenciada' : ($globalIeo <= 33.33 ? 'Baja' : ($globalIeo <= 66.67 ? 'Media' : 'Alta'));
$generated = 'Obsolescencias: diagnóstico preliminar ' . mb_strtolower($globalLevel) . ' con IEO global de ' . number_format($globalIeo, 1, ',', '.') . ' %. Este índice es un soporte de evidencia y no corresponde automáticamente a un porcentaje de depreciación; cualquier incidencia económica debe cuantificarse de forma separada y sustentada.';
$summary = trim((string) ($obs['summary_text'] ?? '')) ?: $generated;
$pill = static function (string $state): string { return match ($state) { 'ok' => '<span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Completo</span>', 'warn' => '<span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Revisar</span>', default => '<span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Falta</span>' }; };
?>
<form class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" method="post"
    action="<?= e(url($subjectActionBase . '/obsolescencias')) ?>" data-module-autosave data-save-in-place
    data-autosave-endpoint="<?= e(url($subjectActionBase . '/obsolescencias/autoguardar')) ?>">
    <?= csrf_field() ?>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div><p class="eyebrow">3.6 Obsolescencias</p><h2 class="mt-2 text-2xl font-semibold">Diagnóstico técnico de obsolescencias</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Evalúa evidencia física, funcional y externa/económica. El IEO orienta el diagnóstico; no es depreciación automática.</p></div>
        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800">IEO global: <?= e(number_format($globalIeo, 1, ',', '.')) ?> % · <?= e($globalLevel) ?></span>
    </div>
    <label class="label mt-6 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-emerald-950">Texto editable para el Entregable
        <textarea class="input mt-2 min-h-24 bg-white" rows="3" name="summary_text" placeholder="Texto profesional de obsolescencias para incorporar al informe"><?= e($summary) ?></textarea>
        <span class="mt-1 block text-xs font-normal text-emerald-800">El analista debe editar el texto antes de pasarlo al entregable.</span>
    </label>
    <div class="mt-5 rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6">
        <h3 class="font-semibold text-slate-900">Campos de obsolescencia para construir el Entregable</h3>
        <div class="mt-3 overflow-x-auto"><table class="w-full min-w-[56rem] text-left text-sm"><thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Campo</th><th class="py-2 pr-3">Valor detectado</th><th class="py-2 pr-3">Estado</th><th class="py-2">Qué falta</th></tr></thead><tbody class="divide-y divide-slate-100">
            <tr><td class="py-2 pr-3 font-semibold">Texto editable</td><td class="py-2 pr-3">Texto construido</td><td class="py-2 pr-3"><?= $pill('ok') ?></td><td class="py-2 text-slate-600">Listo para edición.</td></tr>
            <?php foreach ($groups as $groupKey => [$code, $title, $metaLabel, $_metaOptions, $_items]): $m = $metrics[(string) $groupKey]; $state = $m['missing'] > 0 ? 'warn' : ($m['applicable'] > 0 ? 'ok' : 'missing'); ?>
                <tr><td class="py-2 pr-3 font-semibold"><?= e($title) ?></td><td class="py-2 pr-3"><?= e($code . ' · IEO ' . number_format((float) $m['ieo'], 1, ',', '.') . ' % · ' . $m['level']) ?></td><td class="py-2 pr-3"><?= $pill($state) ?></td><td class="py-2 text-slate-600"><?= e($state === 'ok' ? 'Listo para texto.' : ($m['missing'] > 0 ? 'Completar evidencia en calificaciones 2 o 3.' : 'Calificar factores aplicables.')) ?></td></tr>
            <?php endforeach; ?>
            <tr><td class="py-2 pr-3 font-semibold">Cuantificación económica</td><td class="py-2 pr-3"><?= e($short((string) ($obs['quantification_text'] ?? '')) ?: 'Sin criterio de cuantificación') ?></td><td class="py-2 pr-3"><?= $pill(trim((string) ($obs['quantification_text'] ?? '')) !== '' ? 'ok' : 'warn') ?></td><td class="py-2 text-slate-600">Indicar si requiere cuantificación separada o no tiene efecto material.</td></tr>
        </tbody></table></div>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-3">
        <?php foreach ($groups as $groupKey => [$code, $title, $metaLabel, $metaOptions, $items]): ?>
            <?php $m = $metrics[(string) $groupKey]; ?>
            <section class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-start justify-between gap-3"><div><p class="text-xs font-bold text-teal-800"><?= e($code) ?></p><h3 class="mt-1 font-semibold text-slate-900"><?= e($title) ?></h3></div><span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-700">IEO <?= e(number_format((float) $m['ieo'], 1, ',', '.')) ?> %</span></div>
                <label class="label mt-3"><?= e($metaLabel) ?><select class="input" name="obsolescence[factors][<?= e((string) $groupKey) ?>][meta]"><option value="">Selecciona</option><?php foreach ($metaOptions as $value => $label): ?><option value="<?= e((string) $value) ?>" <?= $meta((string) $groupKey) === (string) $value ? 'selected' : '' ?>><?= e((string) $label) ?></option><?php endforeach; ?></select></label>
                <div class="mt-3 space-y-3">
                    <?php foreach ($items as $key => $label): $score = $factor((string) $groupKey, (string) $key, 'score'); ?>
                        <div class="rounded-lg border border-slate-200 bg-white p-3">
                            <div class="grid gap-3 sm:grid-cols-[1fr_12rem]"><label class="label"><?= e((string) $label) ?><textarea class="input min-h-16" rows="2" name="obsolescence[factors][<?= e((string) $groupKey) ?>][items][<?= e((string) $key) ?>][evidence]" placeholder="Fotografía, documento, comparación, cálculo o criterio técnico"><?= e($factor((string) $groupKey, (string) $key, 'evidence')) ?></textarea></label>
                                <label class="label">Puntaje<select class="input" name="obsolescence[factors][<?= e((string) $groupKey) ?>][items][<?= e((string) $key) ?>][score]"><option value="">Pendiente</option><?php foreach ($scores as $value => $labelScore): ?><option value="<?= e((string) $value) ?>" <?= $score === (string) $value ? 'selected' : '' ?>><?= e((string) $labelScore) ?></option><?php endforeach; ?></select></label></div>
                            <?php if (in_array($score, ['2','3'], true) && trim($factor((string) $groupKey, (string) $key, 'evidence')) === ''): ?><p class="mt-2 text-xs font-semibold text-red-700">Falta evidencia para esta calificación.</p><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-3">
        <label class="label">Diagnóstico técnico<textarea class="input min-h-24" rows="3" name="diagnosis_text" placeholder="Conclusión técnica del diagnóstico"><?= e((string) ($obs['diagnosis_text'] ?? '')) ?></textarea></label>
        <label class="label">Cuantificación o incidencia económica<textarea class="input min-h-24" rows="3" name="quantification_text" placeholder="Método o salvedad: costo de subsanación, mercado, no material, pendiente, etc."><?= e((string) ($obs['quantification_text'] ?? '')) ?></textarea></label>
        <label class="label">Fundamento normativo y técnico<textarea class="input min-h-24" rows="3" name="normative_text" placeholder="IVS, IGAC, NTS, NIIF o norma aplicable según el propósito"><?= e((string) ($obs['normative_text'] ?? '')) ?></textarea></label>
    </div>
    <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950"><strong>Regla metodológica:</strong> el IEO es diagnóstico. Si hay efecto económico material, la afectación debe cuantificarse separadamente y con soporte técnico.</div>
    <div class="mt-6 flex justify-end"><button class="btn-primary" type="submit">Guardar obsolescencias</button></div>
</form>
