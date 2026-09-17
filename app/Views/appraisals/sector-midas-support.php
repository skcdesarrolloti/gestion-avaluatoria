<?php
$midasFiles = is_array($sectorMidasFiles ?? null) ? $sectorMidasFiles : [];
$midasLayerGroups = ['Barrios / división política', 'POT / ordenamiento territorial', 'Servicios públicos',
    'Transporte y movilidad', 'Equipamiento urbano', 'Ambiente y riesgos', 'Otro soporte MIDAS'];
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
<section id="midas-soportes" class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Paso 3 · Soportes por capas</p>
            <h2 class="mt-2 text-2xl font-semibold">Subir descargas de MIDAS</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Si MIDAS bloquea la lectura automática, descarga la capa desde su visor y súbela aquí como soporte
                del barrio. Los PDF quedan como evidencia; CSV, JSON, GeoJSON o ZIP quedan preparados para lectura
                estructurada cuando la capa lo permita.
            </p>
        </div>
        <a class="btn-secondary min-h-11" href="https://midas.cartagena.gov.co/#/home" target="_blank" rel="noopener">
            Abrir MIDAS
        </a>
    </div>
    <details class="mt-5 rounded-xl border border-blue-100 bg-blue-50 p-4" open>
        <summary class="cursor-pointer text-sm font-semibold text-blue-950">Cómo descargar una capa en MIDAS</summary>
        <ol class="mt-3 space-y-2 text-sm leading-6 text-blue-950">
            <li>1. En MIDAS acepta términos y entra al visor.</li>
            <li>2. Usa <strong>Capas</strong> para activar lo que necesitas revisar: barrios, POT, servicios, transporte o equipamientos.</li>
            <li>3. En <strong>Descargas</strong>, abre la categoría y pulsa el botón de descarga del renglón de la capa.</li>
            <li>4. Sube aquí el archivo descargado y selecciona el grupo de capa correspondiente.</li>
        </ol>
    </details>
    <?php if ($midasFileMessage): ?>
        <p class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm font-semibold text-emerald-800"><?= e($midasFileMessage) ?></p>
    <?php endif; ?>
    <?php if ($midasFileError): ?>
        <p class="mt-4 rounded-xl bg-red-50 p-3 text-sm font-semibold text-red-800"><?= e($midasFileError) ?></p>
    <?php endif; ?>
    <form class="mt-5 grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-2"
        method="post" enctype="multipart/form-data"
        action="<?= e(url('avaluos/' . $record['id'] . '/sector/midas/archivos')) ?>">
        <?= csrf_field() ?>
        <label class="label">Grupo de capa
            <select class="input mt-2" name="layer_group" required>
                <?php foreach ($midasLayerGroups as $group): ?>
                    <option value="<?= e($group) ?>"><?= e($group) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label">Archivo descargado
            <input class="input mt-2" type="file" name="midas_support[]" accept=".pdf,.csv,.json,.geojson,.zip" required>
            <span class="mt-1 block text-xs font-normal text-slate-500">Máximo 25 MB. Usa el PDF de MIDAS o un dato abierto si lo permite.</span>
        </label>
        <label class="label md:col-span-2">Nota de lectura
            <textarea class="input mt-2 min-h-24" name="notes"
                placeholder="Ej. Capa de equipamientos revisada para Castillogrande; se observan instituciones educativas y escenarios deportivos."></textarea>
        </label>
        <div class="md:col-span-2 flex justify-end">
            <button class="btn-primary min-h-11" type="submit">Subir soporte MIDAS</button>
        </div>
    </form>
    <?php if ($midasFiles): ?>
        <div class="mt-5 overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr><th class="px-4 py-3">Grupo</th><th class="px-4 py-3">Archivo</th><th class="px-4 py-3">Fecha</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php foreach ($midasFiles as $file): ?>
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800"><?= e($file['layer_group']) ?></td>
                            <td class="px-4 py-3">
                                <a class="font-semibold text-blue-800 underline" href="<?= e(url('avaluos/' . $record['id'] . '/sector/midas/archivos/' . $file['id'])) ?>">
                                    <?= e($file['source_filename']) ?>
                                </a>
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
