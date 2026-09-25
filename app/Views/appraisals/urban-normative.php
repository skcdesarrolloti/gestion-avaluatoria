<?php
$currentStep = 'urbana';
$value = static fn (string $key): string => (string) ($profile[$key] ?? '');
$checked = static fn (string $key): string => !empty($profile[$key]) ? 'checked' : '';
$selected = static fn (string $key, string $val): string => (string) ($profile[$key] ?? '') === $val ? 'selected' : '';
$textarea = static function (string $name, string $label, string $placeholder, int $rows = 4, int $limit = 5000) use ($value): void { ?>
    <label class="label"><?= e($label) ?>
        <textarea class="input min-h-32" name="<?= e($name) ?>" rows="<?= e((string) $rows) ?>" maxlength="<?= e((string) $limit) ?>" placeholder="<?= e($placeholder) ?>"><?= e($value($name)) ?></textarea>
    </label>
<?php };
$input = static function (string $name, string $label, string $placeholder = '', string $type = 'text') use ($value): void { ?>
    <label class="label"><?= e($label) ?>
        <input class="input" type="<?= e($type) ?>" name="<?= e($name) ?>" value="<?= e($value($name)) ?>" maxlength="220" placeholder="<?= e($placeholder) ?>">
    </label>
<?php };
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Numeral 5 · Normatividad urbana</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Uso del suelo, POT y determinantes urbanísticas</h1>
        <p class="mt-3 max-w-3xl text-slate-600">Registra MIDAS, POT, cuadros, soportes y salvedades urbanísticas para el entregable.</p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">Capítulo 5</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>
<?php if ($urbanMessage): ?><p class="mt-6 rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($urbanMessage) ?></p><?php endif; ?>
<?php if ($urbanError): ?><p class="mt-6 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($urbanError) ?></p><?php endif; ?>
<div class="mt-7 space-y-6" x-data="{
    tab: (() => {
        const base = ((location.hash || '#midas').slice(1).split('-')[0]);
        return base === 'pot' ? 'uso' : (['midas','uso','escenarios','determinantes','fuentes','cierre'].includes(base) ? base : 'midas');
    })()
}">
<nav class="rounded-xl bg-slate-200/70 p-2" aria-label="Submenú normatividad urbana">
    <div class="flex gap-2 overflow-x-auto">
        <?php foreach ([['midas','5.1 MIDAS'],['uso','5.2 Uso del suelo'],['escenarios','5.3 Escenarios POT'],['determinantes','5.4 Determinantes'],['fuentes','5.5 Soportes'],['cierre','5.6 Cierre']] as [$key, $label]): ?>
            <button class="inline-flex min-h-11 shrink-0 items-center rounded-lg px-4 py-2 text-sm font-semibold"
                :class="tab === '<?= e($key) ?>' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:bg-blue-50'"
                type="button" @click="tab = '<?= e($key) ?>'; history.replaceState(null, '', '#<?= e($key) ?>')"><?= e($label) ?></button>
        <?php endforeach; ?>
    </div>
</nav>
<form class="space-y-6" method="post" action="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana')) ?>" data-module-autosave data-autosave-endpoint="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana/autoguardar')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="version" value="<?= e((string) ($profile['version'] ?? 0)) ?>">
    <input type="hidden" name="active_tab" :value="tab">
    <?php require BASE_PATH . '/app/Views/appraisals/urban-normative-midas.php'; ?>
    <?php require BASE_PATH . '/app/Views/appraisals/urban-normative-use.php'; ?>
    <?php require BASE_PATH . '/app/Views/appraisals/urban-normative-scenarios.php'; ?>
    <section id="determinantes" x-show="tab === 'determinantes'" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="eyebrow">Determinantes y concepto</p><h2 class="mt-2 text-2xl font-semibold">Planeación, patrimonio, ambiente y riesgo</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <?php $input('planning_concept_number', 'Radicado o número de concepto de uso del suelo'); ?>
            <?php $input('planning_concept_date', 'Fecha del concepto oficial', '', 'date'); ?>
            <?php $textarea('official_concept_scope', 'Alcance del concepto oficial', 'Actividad, respuesta, salvedades y autoridad.', 4); ?>
            <?php $textarea('heritage_context', 'Patrimonio, conservación o Centro Histórico', 'Área de influencia, BIC, conservación o autoridad patrimonial.', 4); ?>
            <?php $textarea('environmental_context', 'Determinantes ambientales o protección', 'Rondas, protección, autoridad ambiental, restricciones o pendientes.', 4); ?>
            <?php $textarea('risk_context', 'Riesgo, amenaza o afectaciones externas', 'Amenaza, riesgo, reserva vial, espacio público, servidumbres urbanísticas o cargas externas.', 4); ?>
            <?php $textarea('legal_urban_affectations', 'Afectaciones jurídicas o urbanísticas sustentadas', 'Afectación vial, reserva, protección, patrimonio, servidumbre o carga con fuente.', 4); ?>
        </div>
    </section>
    <?php require BASE_PATH . '/app/Views/appraisals/urban-normative-sources.php'; ?>
    <section id="cierre" x-show="tab === 'cierre'" class="rounded-2xl border border-emerald-100 bg-emerald-50 p-6 shadow-sm sm:p-8">
        <p class="eyebrow">Texto para el entregable</p><h2 class="mt-2 text-2xl font-semibold">Conclusión urbanística del analista</h2>
        <div class="mt-6 grid gap-4">
            <?php $textarea('restrictions', 'Restricciones o condiciones urbanísticas', 'Condiciones que limitan el uso o desarrollo.', 4); ?>
            <?php $textarea('conclusion', 'Conclusión que debe pasar al numeral 5', 'Conclusión urbanística con fuente, uso y efecto valuatorio.', 5); ?>
            <?php $textarea('source_limitations', 'Limitaciones de la consulta', 'MIDAS no disponible, falta concepto, contradicción o pendiente de Planeación.', 4); ?>
            <?php $textarea('support_summary', 'Soportes revisados', 'MIDAS, concepto, POT, certificado, plano, licencia, resolución o visita.', 3); ?>
            <?php $textarea('analyst_notes', 'Notas internas del analista', 'Observaciones internas.', 3); ?>
        </div>
    </section>
    <div class="flex flex-wrap justify-end gap-3">
        <button class="btn-secondary" type="submit">Guardar numeral 5</button>
        <button class="btn-primary" type="submit" name="next" value="deliverable">Guardar y pasar a Entregable</button>
    </div>
</form>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/report-extra-notes.php'; ?>
