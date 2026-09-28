<?php
$midasFiles = is_array($sectorMidasFiles ?? null) ? $sectorMidasFiles : [];
$midasLayerGroups = ['Barrios / división política', 'POT / ordenamiento territorial', 'Servicios públicos',
    'Transporte y movilidad', 'Equipamiento urbano', 'Ambiente y riesgos', 'Otro soporte MIDAS'];
$midasPlan = \App\Services\MidasLayerPlan::components();
$midasReference = preg_replace('/\D+/', '', (string) (($subject['midas_cadastral_reference'] ?? '')
    ?: ($subject['cadastral_reference'] ?? ''))) ?? '';
$midasNationalReference = preg_replace('/\D+/', '', (string) ($subject['midas_national_cadastral_reference'] ?? '')) ?? '';
$formatMidasBytes = static fn ($bytes): string => number_format(((int) $bytes) / 1024, 1, ',', '.') . ' KB';
$formatMidasDate = static function ($value): string {
    try {
        return (new DateTimeImmutable((string) $value, new DateTimeZone('UTC')))
            ->setTimezone(new DateTimeZone('America/Bogota'))->format('d/m/Y H:i');
    } catch (Throwable) {
        return (string) $value;
    }
};
$midasTabs = [
    'barrio' => 'Barrio y capas',
    'predio' => 'Predio y uso del suelo',
    'academia' => 'Academia de capas',
    'descargas' => 'Soportes del avalúo',
];
?>
<div id="midas-centro" class="mt-6">
    <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
        <?php foreach ($midasTabs as $key => $label): ?>
            <button type="button" class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                @click="midasTab = '<?= e($key) ?>'"
                :class="midasTab === '<?= e($key) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                <?= e($label) ?>
            </button>
        <?php endforeach; ?>
        <a class="btn-secondary ml-auto shrink-0" href="https://midas.cartagena.gov.co/#/home" target="_blank" rel="noopener">
            Abrir MIDAS
        </a>
    </div>
    <?php if ($midasFileMessage): ?><p class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm font-semibold text-emerald-800"><?= e($midasFileMessage) ?></p><?php endif; ?>
    <?php if ($midasFileError): ?><p class="mt-4 rounded-xl bg-red-50 p-3 text-sm font-semibold text-red-800"><?= e($midasFileError) ?></p><?php endif; ?>

    <div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="midasTab === 'barrio'">
        <div class="grid gap-4 lg:grid-cols-[1fr_0.9fr]">
            <form class="rounded-xl border border-slate-200 bg-slate-50 p-4" method="post" @submit="syncSelection()"
                action="<?= e(url('avaluos/' . $record['id'] . '/sector/barrio')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="neighborhood_id" :value="selectedId">
                <p class="text-xs font-semibold uppercase text-teal-800">Consulta de barrio / sector</p>
                <label class="label mt-2">Barrio o microsector
                    <input class="input mt-2" type="search" name="neighborhood_query" x-model="query"
                        @input="selectedId = ''; syncSelection()" @blur="syncSelection()"
                        placeholder="Ej. Bocagrande, Castillogrande, Manga">
                    <span class="mt-2 block text-xs font-normal text-slate-500" x-show="query.trim() === ''">
                        Esta consulta corresponde al sector donde se ubica el predio.
                    </span>
                    <span class="mt-2 block text-xs font-normal text-emerald-700" x-show="selectedId">
                        Barrio listo para cargar y consultar capas.
                    </span>
                    <span class="mt-2 block text-xs font-normal text-amber-700" x-show="query.trim() !== '' && !selectedId && filtered.length > 1">
                        Hay varias coincidencias; selecciona una tarjeta.
                    </span>
                </label>
                <div class="mt-4 flex flex-wrap gap-3">
                    <button class="btn-primary min-h-11" type="submit">Cargar ficha del barrio</button>
                    <?php if ($neighborhoodLabel === ''): ?>
                        <button class="btn-secondary min-h-11" type="button" disabled>Consultar capas sectoriales</button>
                    <?php else: ?>
                        <a class="btn-secondary min-h-11" href="<?= e(url('avaluos/' . $record['id'] . '/sector/midas/consultar')) ?>">
                            Consultar capas sectoriales
                        </a>
                    <?php endif; ?>
                </div>
                <div class="mt-4 grid gap-2 sm:grid-cols-2" x-show="filtered.length">
                    <template x-for="item in filtered" :key="item.id">
                        <button type="button" class="min-h-11 rounded-lg border px-3 py-2 text-left text-sm"
                            @click="choose(item)"
                            :class="selectedId === item.id ? 'border-blue-700 bg-blue-50 text-blue-900' : 'border-slate-200 bg-white text-slate-700 hover:bg-white'">
                            <span class="font-semibold" x-text="item.name"></span>
                            <span class="block text-xs text-slate-500" x-text="[item.locality_name, item.commune_ucg].filter(Boolean).join(' · ')"></span>
                        </button>
                    </template>
                </div>
            </form>
            <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
                <p class="font-semibold">Qué devuelve esta consulta</p>
                <p class="mt-2">
                    MIDAS por barrio sirve para delimitar sector, POT, servicios, movilidad, equipamientos,
                    seguridad, ambiente y riesgos. Esto soporta el capítulo 2; no reemplaza la ficha predial.
                </p>
                <p class="mt-3 rounded-lg bg-white p-3 text-xs font-semibold text-blue-900">
                    Barrio activo: <?= e($neighborhoodLabel !== '' ? $neighborhoodLabel : 'pendiente de cargar') ?>.
                </p>
            </div>
        </div>
        <?php require BASE_PATH . '/app/Views/appraisals/sector-neighborhood-history.php'; ?>
    </div>

    <div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="midasTab === 'predio'">
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                <p class="text-xs font-semibold uppercase text-blue-900">Consulta de predio</p>
                <h3 class="mt-2 text-lg font-semibold text-slate-900">Predios y Uso Suelo por referencia</h3>
                <p class="mt-2 text-sm leading-6 text-blue-950">
                    Esta consulta es distinta a la del barrio. Busca la ficha del inmueble: matrícula, dirección,
                    áreas, uso del suelo, tratamiento, riesgos y reglamentación urbanística.
                </p>
                <form class="mt-4 grid gap-3" method="post"
                    action="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto/midas/consultar')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="return_to" value="<?= e('avaluos/' . $record['id'] . '/sector#midas-centro') ?>">
                    <input type="hidden" name="midas_national_cadastral_reference" value="<?= e($midasNationalReference) ?>">
                    <label class="label">Referencia catastral o predial
                        <input class="input" name="midas_cadastral_reference" value="<?= e($midasReference) ?>"
                            placeholder="Referencia sin guiones ni espacios">
                    </label>
                    <button class="btn-primary min-h-11 justify-self-start" type="submit">Consultar Predios y Uso Suelo</button>
                </form>
            </div>
            <form class="rounded-xl border border-emerald-200 bg-emerald-50 p-4"
                method="post" action="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto/midas/procesar')) ?>"
                x-data="subjectMidasUpdater(<?= e(json_encode(url('avaluos/' . $record['id'] . '/bien-sujeto/midas/procesar'), JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>)">
                <?= csrf_field() ?>
                <input type="hidden" name="return_to" value="<?= e('avaluos/' . $record['id'] . '/sector#midas-centro') ?>">
                <p class="text-xs font-semibold uppercase text-emerald-900">Respaldo manual</p>
                <label class="label mt-2">Lectura completa copiada de MIDAS
                    <textarea class="input min-h-44" name="midas_pasted_text" rows="8" maxlength="70000"
                        placeholder="Pega Predios, Uso Suelo, área libre, frente, alturas, aislamientos, riesgos y observaciones."></textarea>
                </label>
                <input type="hidden" name="midas_unmapped_notes" value="">
                <div class="mt-4 flex flex-wrap gap-3">
                    <button class="btn-primary min-h-11" type="submit" @click.prevent="actualizar($el.form)" :disabled="busy">
                        <span x-text="busy ? 'Procesando...' : 'Procesar lectura copiada'">Procesar lectura copiada</span>
                    </button>
                    <a class="btn-secondary min-h-11" href="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto#midas')) ?>">Ver numeral 3</a>
                    <a class="btn-secondary min-h-11" href="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana#uso')) ?>">Ver numeral 5</a>
                </div>
                <div class="mt-4 rounded-xl border border-emerald-200 bg-white p-4" x-show="step || message || error">
                    <p class="text-sm font-semibold text-slate-900">Resultado del procesamiento</p>
                    <p class="mt-2 text-sm font-semibold text-emerald-800" x-show="message" x-text="message"></p>
                    <p class="mt-2 text-sm font-semibold text-red-700" x-show="error" x-text="error"></p>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="midasTab === 'academia'">
        <p class="text-sm font-semibold text-slate-950">Qué revisar en MIDAS antes de llenar el sector</p>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <?php foreach ($midasPlan as $component): ?>
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <h3 class="text-sm font-semibold text-teal-900"><?= e($component['label']) ?></h3>
                    <p class="mt-2 text-xs leading-5 text-slate-600"><?= e(implode(' · ', $component['layers'])) ?></p>
                    <p class="mt-2 text-xs font-semibold text-blue-900">
                        Alimenta subnumerales: <?= e(implode(', ', array_keys($component['fields']))) ?>
                    </p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

    <?php require BASE_PATH . '/app/Views/appraisals/sector-midas-downloads.php'; ?>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/midas-updater-script.php'; ?>
