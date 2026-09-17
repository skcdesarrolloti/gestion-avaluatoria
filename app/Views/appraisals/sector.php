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
            <p class="eyebrow">Banco barrial</p>
            <h2 class="mt-2 text-2xl font-semibold">Buscar y cargar barrio</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Busca el barrio del avalúo. Si ya existe ficha sectorial, se carga completa; si no existe,
                se prepara una generación inicial para completar y guardar.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php if ($bankUpdatedAtText): ?>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-800">
                    Banco actualizado <?= e($bankUpdatedAtText) ?>
                </span>
            <?php endif; ?>
            <form method="post" action="<?= e(url('avaluos/' . $record['id'] . '/sector/fuentes/actualizar')) ?>">
                <?= csrf_field() ?>
                <button class="btn-secondary min-h-11" type="submit">Actualizar barrio desde fuentes</button>
            </form>
        </div>
    </div>
    <form class="mt-5 grid gap-4 lg:grid-cols-[1fr_auto]" method="post" @submit="syncSelection()"
        action="<?= e(url('avaluos/' . $record['id'] . '/sector/barrio')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="neighborhood_id" :value="selectedId">
        <label class="label">Barrio / microsector
            <input class="input mt-2" type="search" name="neighborhood_query" x-model="query"
                @input="selectedId = ''; syncSelection()" @blur="syncSelection()"
                placeholder="Busca por nombre del barrio, localidad o comuna">
            <span class="mt-2 block text-xs font-normal text-slate-500" x-show="query.trim() === ''">
                Escribe el barrio o microsector para ver coincidencias.
            </span>
            <span class="mt-2 block text-xs font-normal text-emerald-700" x-show="selectedId">
                Barrio listo para cargar.
            </span>
            <span class="mt-2 block text-xs font-normal text-amber-700" x-show="query.trim() !== '' && !selectedId && filtered.length > 1">
                Hay varias coincidencias; selecciona una tarjeta.
            </span>
        </label>
        <button class="btn-primary min-h-11 self-end" type="submit">Cargar ficha del barrio</button>
        <div class="lg:col-span-2 grid gap-2 sm:grid-cols-2 lg:grid-cols-4" x-show="filtered.length">
            <template x-for="item in filtered" :key="item.id">
                <button type="button" class="min-h-11 rounded-lg border px-3 py-2 text-left text-sm"
                    @click="choose(item)"
                    :class="selectedId === item.id ? 'border-blue-700 bg-blue-50 text-blue-900' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'">
                    <span class="font-semibold" x-text="item.name"></span>
                    <span class="block text-xs text-slate-500" x-text="[item.locality_name, item.commune_ucg].filter(Boolean).join(' · ')"></span>
                </button>
            </template>
        </div>
    </form>
</section>

<form id="sector-form" class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    method="post" action="<?= e(url('avaluos/' . $record['id'] . '/sector')) ?>"
    x-data="{
        activeSector: location.hash && !location.hash.startsWith('#banco-') ? location.hash.slice(1) : '<?= e($firstSectorTab) ?>',
        activeBankSection: (location.hash.match(/^#banco-(\d{2})$/) || [])[1] || '01'
    }"
    @submit="$refs.activeSector.value = 'banco-' + activeBankSection">
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
    <?php require BASE_PATH . '/app/Views/appraisals/sector-advanced-sections.php'; ?>
    <?php require BASE_PATH . '/app/Views/appraisals/sector-legacy-sections.php'; ?>

    <div class="mt-6 flex flex-wrap justify-end gap-3">
        <a class="btn-secondary min-h-11" href="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto')) ?>">
            Continuar a Bien sujeto
        </a>
        <button class="btn-primary min-h-11" type="submit">Guardar numeral 2</button>
    </div>
</form>
<?php require BASE_PATH . '/app/Views/appraisals/sector-photo-tabs.php'; ?>
