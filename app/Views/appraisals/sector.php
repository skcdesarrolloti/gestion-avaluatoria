<?php
$currentStep = 'sector';
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
$bankUpdatedAtText = '';
if (!empty($sectorNeighborhoodUpdatedAt)) {
    try {
        $bankUpdatedAtText = (new DateTimeImmutable((string) $sectorNeighborhoodUpdatedAt, new DateTimeZone('UTC')))
            ->setTimezone(new DateTimeZone('America/Bogota'))->format('d/m/Y H:i');
    } catch (Throwable) {
        $bankUpdatedAtText = (string) $sectorNeighborhoodUpdatedAt;
    }
}
$neighborhoodLabel = trim((string) ($subject['neighborhood_name'] ?? ''));
$bankVersion = trim((string) ($sectorBankProfileVersion ?? ''));
$neighborhoodsJson = json_encode($sectorNeighborhoods ?? [], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
$bankSectionCodesJson = json_encode(array_keys($sectorAdvancedCatalog ?? []), JSON_THROW_ON_ERROR);
$sectorFormId = 'sector-form';
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

<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{
        midasTab: 'barrio',
        neighborhoods: <?= e($neighborhoodsJson) ?>,
        query: <?= e(json_encode($neighborhoodLabel, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        selectedId: <?= e(json_encode((string) ($subject['neighborhood_id'] ?? ''), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
        label(item) { return [item.name, item.locality_name, item.commune_ucg, item.city_name].filter(Boolean).join(' · ') },
        uniqueNeighborhoods() {
            const seen = new Set();
            return this.neighborhoods.filter(item => {
                const key = this.label(item).toLowerCase();
                if (seen.has(key)) return false;
                seen.add(key);
                return true;
            });
        },
        get filtered() {
            const q = this.query.toLowerCase().trim();
            if (!q) return [];
            return this.uniqueNeighborhoods().filter(item => this.label(item).toLowerCase().includes(q)).slice(0, 8);
        },
        syncSelection() {
            const q = this.query.toLowerCase().trim();
            if (!q) { this.selectedId = ''; return; }
            const exact = this.uniqueNeighborhoods().find(item => {
                return this.label(item).toLowerCase() === q || String(item.name || '').toLowerCase() === q;
            });
            if (exact) { this.selectedId = exact.id; return; }
            const matches = this.filtered;
            this.selectedId = matches.length === 1 ? matches[0].id : '';
        },
        choose(item) { this.selectedId = item.id; this.query = this.label(item) }
    }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">MIDAS · punto de partida</p>
            <h2 class="mt-2 text-2xl font-semibold">Barrio, predio y capas de soporte</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Primero consulta MIDAS. La lectura por barrio soporta el capítulo 2; la lectura por predio
                alimenta identificación predial en el 3 y norma urbana en el 5.
            </p>
        </div>
        <?php if ($bankUpdatedAtText): ?>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-800">
                Banco actualizado <?= e($bankUpdatedAtText) ?>
            </span>
        <?php endif; ?>
    </div>
    <?php require BASE_PATH . '/app/Views/appraisals/sector-midas-support.php'; ?>
</section>
<?php require BASE_PATH . '/app/Views/appraisals/sector-midas-review.php'; ?>

<form id="<?= e($sectorFormId) ?>" method="post" action="<?= e(url('avaluos/' . $record['id'] . '/sector')) ?>"
    data-module-autosave data-autosave-endpoint="<?= e(url('avaluos/' . $record['id'] . '/sector/autoguardar')) ?>">
    <?= csrf_field() ?>
</form>
<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="sectorBankTabs(<?= e($bankSectionCodesJson) ?>)">
    <input form="<?= e($sectorFormId) ?>" x-ref="activeSector" type="hidden" name="active_sector"
        :value="'banco-' + activeBankSection">
    <input form="<?= e($sectorFormId) ?>" x-ref="targetSector" type="hidden" name="target_sector" :value="targetSector()">
    <input form="<?= e($sectorFormId) ?>" x-ref="afterSectorSave" type="hidden" name="after_sector_save" :value="afterSectorSave">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Ficha sectorial del avalúo</p>
            <h2 class="mt-2 text-2xl font-semibold">Caracterización del sector y entorno</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Trabaja por subpestañas. Las ayudas de cada campo sirven como referencia para redactar
                el capítulo sectorial del informe.
            </p>
        </div>
        <button form="<?= e($sectorFormId) ?>" class="btn-primary min-h-11" type="submit"
            @click="prepareSectorSave()" x-text="advanceLabel()">Guardar y pasar</button>
    </div>
    <p class="mt-3 text-xs font-semibold text-slate-500" data-autosave-status-for="<?= e($sectorFormId) ?>">
        Autoguardado activo
    </p>

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
                <?php if (empty($sectorHasNeighborhoodBank)): ?>
                    <span class="block font-semibold text-amber-800">Este barrio aún no tiene banco barrial consolidado; al guardar se creará o actualizará.</span>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($locationLine): ?>
                <span class="block text-blue-900/80">Referencia territorial: <?= e($locationLine) ?>.</span>
            <?php endif; ?>
            <span class="block text-blue-900/80">
                Banco barrial interno: <?= e($sectorHasNeighborhoodBank ? 'existente' : 'se creará al guardar') ?><?= $bankVersion !== '' ? ' · versión ' . e($bankVersion) : '' ?>.
            </span>
        </div>
    <?php endif; ?>
    <?php require BASE_PATH . '/app/Views/appraisals/sector-base-hidden-fields.php'; ?>
    <?php require BASE_PATH . '/app/Views/appraisals/sector-advanced-sections.php'; ?>

    <div class="mt-6 flex flex-wrap justify-end gap-3">
        <button form="<?= e($sectorFormId) ?>" class="btn-primary min-h-11" type="submit"
            @click="prepareSectorSave()" x-text="advanceLabel()">Guardar y pasar</button>
    </div>
</section>
