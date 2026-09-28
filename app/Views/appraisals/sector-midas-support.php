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
?>
<section id="midas-centro" class="mt-8 rounded-2xl border border-blue-100 bg-blue-50/60 p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">MIDAS central · numeral 2</p>
            <h2 class="mt-2 text-2xl font-semibold">Consulta única para sector, predio y norma urbana</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-700">
                Primero se revisa MIDAS aquí. La información sectorial queda en el numeral 2; la ficha Predios
                alimenta el numeral 3; Uso Suelo y parámetros urbanísticos se envían al numeral 5.
            </p>
        </div>
        <a class="btn-primary min-h-11" href="https://midas.cartagena.gov.co/#/home" target="_blank" rel="noopener">
            Abrir MIDAS
        </a>
    </div>

    <div class="mt-5 grid gap-4 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-xl border border-white bg-white p-4">
            <p class="text-sm font-semibold text-slate-950">Antes de usar MIDAS: capas que conviene revisar</p>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <?php foreach ($midasPlan as $component): ?>
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <h3 class="text-sm font-semibold text-teal-900"><?= e($component['label']) ?></h3>
                        <p class="mt-2 text-xs leading-5 text-slate-600">
                            <?= e(implode(' · ', array_slice($component['layers'], 0, 4))) ?><?= count($component['layers']) > 4 ? '…' : '' ?>
                        </p>
                        <p class="mt-2 text-xs font-semibold text-blue-900">
                            Alimenta: <?= e(implode(', ', array_keys($component['fields']))) ?>
                        </p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="rounded-xl border border-teal-100 bg-teal-50 p-4 text-sm leading-6 text-teal-950">
            <p class="font-semibold">Flujo recomendado</p>
            <ol class="mt-2 list-decimal space-y-1 pl-5">
                <li>Carga o confirma el barrio del numeral 2.</li>
                <li>Intenta lectura automática para capas sectoriales y para Predios/Uso Suelo.</li>
                <li>Si MIDAS no responde, pega la lectura completa como aparece en pantalla.</li>
                <li>Sube las descargas de capas como soporte del avalúo.</li>
            </ol>
            <p class="mt-3 rounded-lg bg-white/80 p-3 text-xs font-semibold text-teal-900">
                No cambia la numeración: el 2 captura y distribuye; el 3, 5 y el entregable reciben cada dato en su campo.
            </p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <div class="rounded-xl border border-blue-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase text-blue-900">Opción A · Automática</p>
            <h3 class="mt-2 text-lg font-semibold text-slate-900">Consultar cuando MIDAS responda</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                La consulta sectorial busca capas por barrio. La consulta predial usa la referencia catastral disponible
                y reparte Predios al 3 y Uso Suelo al 5.
            </p>
            <div class="mt-4 flex flex-wrap gap-3">
                <?php if ($neighborhoodLabel === ''): ?>
                    <button class="btn-secondary min-h-11" type="button" disabled>Consultar capas sectoriales</button>
                <?php else: ?>
                    <a class="btn-secondary min-h-11" href="<?= e(url('avaluos/' . $record['id'] . '/sector/midas/consultar')) ?>">
                        Consultar capas sectoriales
                    </a>
                <?php endif; ?>
                <form class="flex flex-wrap items-end gap-3" method="post"
                    action="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto/midas/consultar')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="return_to" value="<?= e('avaluos/' . $record['id'] . '/sector#midas-centro') ?>">
                    <input type="hidden" name="midas_national_cadastral_reference" value="<?= e($midasNationalReference) ?>">
                    <label class="label min-w-80">Referencia para MIDAS
                        <input class="input mt-1" name="midas_cadastral_reference" value="<?= e($midasReference) ?>"
                            placeholder="Referencia catastral o predial sin guiones">
                    </label>
                    <button class="btn-secondary min-h-11" type="submit">Consultar Predios y Uso Suelo</button>
                </form>
            </div>
            <p class="mt-3 text-xs font-semibold <?= $neighborhoodLabel === '' ? 'text-amber-700' : 'text-blue-900' ?>">
                <?= e($neighborhoodLabel === '' ? 'Primero carga el barrio para activar capas sectoriales.' : 'Barrio activo: ' . $neighborhoodLabel) ?>
            </p>
        </div>
        <form class="rounded-xl border border-emerald-200 bg-white p-4"
            method="post" action="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto/midas/procesar')) ?>"
            x-data="subjectMidasUpdater(<?= e(json_encode(url('avaluos/' . $record['id'] . '/bien-sujeto/midas/procesar'), JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>)">
            <?= csrf_field() ?>
            <input type="hidden" name="return_to" value="<?= e('avaluos/' . $record['id'] . '/sector#midas-centro') ?>">
            <p class="text-xs font-semibold uppercase text-emerald-900">Opción B · Manual asistida</p>
            <h3 class="mt-2 text-lg font-semibold text-slate-900">Copiar y pegar la lectura completa</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Pega Predios, Uso Suelo y parámetros en el mismo orden de MIDAS. El sistema clasifica lo reconocido
                y deja lo no actualizado en revisión del analista.
            </p>
            <label class="label mt-4">Lectura completa copiada de MIDAS
                <textarea class="input min-h-44" name="midas_pasted_text" rows="8" maxlength="70000"
                    placeholder="Pega aquí Predios, Uso Suelo, área libre, frente, alturas, aislamientos, riesgos y observaciones."></textarea>
            </label>
            <input type="hidden" name="midas_unmapped_notes" value="">
            <div class="mt-4 flex flex-wrap gap-3">
                <button class="btn-primary min-h-11" type="submit" @click.prevent="actualizar($el.form)" :disabled="busy">
                    <span x-text="busy ? 'Procesando...' : 'Procesar lectura MIDAS'">Procesar lectura MIDAS</span>
                </button>
                <a class="btn-secondary min-h-11" href="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto#midas')) ?>">
                    Revisar numeral 3
                </a>
                <a class="btn-secondary min-h-11" href="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana#uso')) ?>">
                    Revisar numeral 5
                </a>
            </div>
            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4" x-show="step || message || error">
                <p class="text-sm font-semibold text-slate-900">Progreso de actualización</p>
                <ol class="mt-3 space-y-2 text-sm leading-6 text-slate-700">
                    <li :class="['subject','urban','done'].includes(step) ? 'font-semibold text-teal-800' : ''">
                        1. Numeral 3: identificación predial, áreas y trazabilidad.
                    </li>
                    <li :class="['urban','done'].includes(step) ? 'font-semibold text-teal-800' : ''">
                        2. Numeral 5: usos, reglamentación y parámetros urbanísticos.
                    </li>
                    <li :class="step === 'done' ? 'font-semibold text-teal-800' : ''">
                        3. Datos no actualizados quedan para decisión del analista.
                    </li>
                </ol>
                <p class="mt-3 text-sm font-semibold text-emerald-800" x-show="message" x-text="message"></p>
                <p class="mt-3 text-sm font-semibold text-red-700" x-show="error" x-text="error"></p>
            </div>
        </form>
    </div>
</section>

<section id="midas-soportes" class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Soportes por capas MIDAS</p>
            <h2 class="mt-2 text-2xl font-semibold">Alojar descargas de MIDAS</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Usa este repositorio para guardar PDF, CSV, JSON, GeoJSON o ZIP descargados desde MIDAS.
                Así el soporte queda asociado al avalúo aunque la lectura automática falle.
            </p>
        </div>
    </div>
    <details class="mt-5 rounded-xl border border-blue-100 bg-blue-50 p-4">
        <summary class="cursor-pointer text-sm font-semibold text-blue-950">Cómo descargar una capa en MIDAS</summary>
        <ol class="mt-3 space-y-2 text-sm leading-6 text-blue-950">
            <li>1. En MIDAS acepta términos y entra al visor.</li>
            <li>2. Usa Capas para activar barrios, POT, servicios, transporte, equipamientos o riesgos.</li>
            <li>3. En Descargas, abre la categoría y descarga la capa revisada.</li>
            <li>4. Sube aquí el archivo descargado y selecciona su grupo.</li>
        </ol>
    </details>
    <?php if ($midasFileMessage): ?><p class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm font-semibold text-emerald-800"><?= e($midasFileMessage) ?></p><?php endif; ?>
    <?php if ($midasFileError): ?><p class="mt-4 rounded-xl bg-red-50 p-3 text-sm font-semibold text-red-800"><?= e($midasFileError) ?></p><?php endif; ?>
    <form class="mt-5 grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-2"
        method="post" enctype="multipart/form-data"
        action="<?= e(url('avaluos/' . $record['id'] . '/sector/midas/archivos')) ?>">
        <?= csrf_field() ?>
        <label class="label">Grupo de capa
            <select class="input mt-2" name="layer_group" required>
                <?php foreach ($midasLayerGroups as $group): ?><option value="<?= e($group) ?>"><?= e($group) ?></option><?php endforeach; ?>
            </select>
        </label>
        <label class="label">Archivo descargado
            <input class="input mt-2" type="file" name="midas_support[]" accept=".pdf,.csv,.json,.geojson,.zip" required>
            <span class="mt-1 block text-xs font-normal text-slate-500">Máximo 25 MB.</span>
        </label>
        <label class="label md:col-span-2">Nota de lectura
            <textarea class="input mt-2 min-h-24" name="notes" placeholder="Ej. Capa de transporte revisada; se identifican rutas y paraderos cercanos."></textarea>
        </label>
        <div class="md:col-span-2 flex justify-end"><button class="btn-primary min-h-11" type="submit">Subir soporte MIDAS</button></div>
    </form>
    <?php if ($midasFiles): ?>
        <div class="mt-5 overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Grupo</th><th class="px-4 py-3">Archivo</th><th class="px-4 py-3">Fecha</th></tr></thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php foreach ($midasFiles as $file): ?>
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800"><?= e($file['layer_group']) ?></td>
                            <td class="px-4 py-3">
                                <a class="font-semibold text-blue-800 underline" href="<?= e(url('avaluos/' . $record['id'] . '/sector/midas/archivos/' . $file['id'])) ?>"><?= e($file['source_filename']) ?></a>
                                <span class="block text-xs text-slate-500"><?= e($formatMidasBytes($file['file_size_bytes'])) ?></span>
                                <?php if (!empty($file['notes'])): ?><span class="block text-xs text-slate-600"><?= e($file['notes']) ?></span><?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?= e($formatMidasDate($file['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require BASE_PATH . '/app/Views/appraisals/midas-updater-script.php'; ?>
