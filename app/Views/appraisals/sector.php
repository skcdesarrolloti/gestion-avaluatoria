<?php
$currentStep = 'sector';
$firstSectorTab = array_key_first($sectorSections);
$locationLine = trim(implode(' · ', array_filter([
    $subject['neighborhood_name'] ?? '',
    $subject['locality_name'] ?? '',
    $subject['commune_ucg'] ?? '',
    $subject['city_name'] ?? '',
])));
$updatedAtText = '';
if (!empty($sectorUpdatedAt)) {
    try {
        $updatedAtText = (new DateTimeImmutable((string) $sectorUpdatedAt, new DateTimeZone('UTC')))
            ->setTimezone(new DateTimeZone('America/Bogota'))->format('d/m/Y H:i');
    } catch (Throwable) {
        $updatedAtText = (string) $sectorUpdatedAt;
    }
}
$neighborhoodLabel = trim((string) ($subject['neighborhood_name'] ?? ''));
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Numeral 2 · Sector y entorno</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Informe del sector</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Caracteriza el contexto urbano, económico y de mercado antes de entrar al bien sujeto.
            Esta ficha orienta la lectura del entorno y después alimentará el entregable.
        </p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-800">Numeral 2</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>

<?php if ($sectorMessage): ?>
    <p class="mt-6 rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($sectorMessage) ?></p>
<?php endif; ?>
<?php if ($sectorError): ?>
    <p class="mt-6 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($sectorError) ?></p>
<?php endif; ?>

<form id="sector-form" class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    method="post" action="<?= e(url('avaluos/' . $record['id'] . '/sector')) ?>"
    x-data="{ activeSector: location.hash ? location.hash.slice(1) : '<?= e($firstSectorTab) ?>' }"
    @submit="$refs.activeSector.value = activeSector">
    <?= csrf_field() ?>
    <input x-ref="activeSector" type="hidden" name="active_sector" value="<?= e($firstSectorTab) ?>">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Ficha sectorial del avalúo</p>
            <h2 class="mt-2 text-2xl font-semibold">Caracterización del sector y entorno</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Trabaja por subpestañas. Las ayudas de cada campo sirven como referencia para redactar
                el capítulo sectorial del informe.
            </p>
        </div>
        <button class="btn-primary min-h-11" type="submit">Guardar numeral 2</button>
    </div>

    <?php if ($locationLine || ($sectorPrefillSource ?? '') !== 'expediente'): ?>
        <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
            <strong>Barrio de trabajo:</strong> <?= e($neighborhoodLabel !== '' ? $neighborhoodLabel : 'sin barrio seleccionado') ?>.
            <?php if (($sectorPrefillSource ?? '') === 'banco_barrial'): ?>
                Se cargó la ficha sectorial completa guardada para este barrio.
                <span class="font-semibold">Última actualización: <?= e($updatedAtText ?: 'sin fecha registrada') ?>.</span>
            <?php elseif (($sectorPrefillSource ?? '') === 'bien_sujeto'): ?>
                Este barrio todavía no tiene ficha sectorial guardada. Completa o genera esta lectura y guárdala para crear el banco barrial.
                <span class="font-semibold">Fecha de actualización: se registrará al guardar.</span>
            <?php else: ?>
                Estás trabajando con la ficha ya fijada en este avalúo.
                <span class="font-semibold">Última actualización: <?= e($updatedAtText ?: 'sin fecha registrada') ?>.</span>
            <?php endif; ?>
            <?php if ($locationLine): ?>
                <span class="block text-blue-900/80">Referencia territorial: <?= e($locationLine) ?>.</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <nav class="mt-6 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Subsecciones de sector">
        <?php foreach ($sectorSections as $key => [$number, $label]): ?>
            <button type="button" class="min-h-12 shrink-0 rounded-lg px-4 py-2 text-left text-sm font-semibold"
                @click="activeSector = '<?= e($key) ?>'; history.replaceState(null, '', '#<?= e($key) ?>')"
                :class="activeSector === '<?= e($key) ?>' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:bg-white/70'">
                <span class="block text-xs opacity-80"><?= e($number) ?></span>
                <?= e($label) ?>
            </button>
        <?php endforeach; ?>
    </nav>

    <?php foreach ($sectorSections as $key => [$number, $label, $fields]): ?>
        <section id="<?= e($key) ?>" class="mt-6 rounded-xl border border-slate-200 p-5 scroll-mt-6"
            x-show="activeSector === '<?= e($key) ?>'">
            <p class="eyebrow"><?= e($number) ?></p>
            <h3 class="mt-2 text-xl font-semibold"><?= e($label) ?></h3>
            <div class="mt-5 grid gap-5 md:grid-cols-2">
                <?php foreach ($fields as [$field, $fieldLabel, $type]): ?>
                    <?php require BASE_PATH . '/app/Views/appraisals/sector-field.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <div class="mt-6 flex flex-wrap justify-end gap-3">
        <a class="btn-secondary min-h-11" href="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto')) ?>">
            Continuar a Bien sujeto
        </a>
        <button class="btn-primary min-h-11" type="submit">Guardar numeral 2</button>
    </div>
</form>
