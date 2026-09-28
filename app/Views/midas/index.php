<?php
$documents = is_array($documents ?? null) ? $documents : [];
$groups = is_array($groups ?? null) ? $groups : [];
$storage = is_array($storage ?? null) ? $storage : [];
$targets = [
    'Barrios / división política' => 'Numeral 2: sector, delimitación y fuente base. Numeral 3: localidad, barrio y UCG.',
    'POT / ordenamiento territorial' => 'Numeral 5: uso del suelo, tratamiento, clasificación y determinantes.',
    'Circulares urbanísticas' => 'Numeral 5 y futuro potencial: altura, parqueaderos, altillos y salvedades normativas.',
    'Servicios públicos' => 'Numeral 2: cobertura y calidad del entorno; numeral 7 si hay limitaciones.',
    'Transporte y movilidad' => 'Numeral 2: accesibilidad; numeral 6: dinámica económica y mercado objetivo.',
    'Equipamiento urbano' => 'Numerales 2 y 6: salud, educación, comercio, seguridad y servicios de soporte.',
    'Ambiente y riesgos' => 'Numeral 7: inundación, licuación, amenazas y condiciones restrictivas.',
    'Otro soporte MIDAS' => 'Se usa solo si el analista define qué campo o numeral sustenta.',
];
$formatBytes = static fn ($bytes): string => number_format(((int) $bytes) / 1024, 1, ',', '.') . ' KB';
$formatDate = static function ($value): string {
    try {
        return (new DateTimeImmutable((string) $value, new DateTimeZone('UTC')))
            ->setTimezone(new DateTimeZone('America/Bogota'))->format('d/m/Y H:i');
    } catch (Throwable) { return (string) $value; }
};
?>
<section id="biblioteca-midas" class="space-y-7" x-data="{ query: '' }">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-teal-800">Biblioteca cartográfica</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">MIDAS</h1>
            <p class="mt-3 max-w-3xl text-slate-600">
                Guarda una sola vez las descargas comunes de MIDAS: circulares urbanísticas,
                POT, división política, servicios, movilidad, equipamientos, riesgos y soportes cartográficos.
                Los avalúos solo anexan evidencias particulares del caso.
            </p>
        </div>
        <label class="block min-w-full text-sm font-medium text-slate-700 lg:min-w-80">
            Buscar en Biblioteca MIDAS
            <input class="input mt-2" type="search" placeholder="POT, barrio, riesgo, transporte" x-model.trim="query">
        </label>
    </div>
    <?php if ($message): ?><p class="rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($error) ?></p><?php endif; ?>

    <section class="grid gap-5 lg:grid-cols-[0.85fr_1.15fr]">
        <article class="rounded-xl border border-blue-100 bg-blue-50 p-5">
            <h2 class="text-lg font-semibold text-blue-950">Qué se aloja aquí</h2>
            <p class="mt-2 text-sm leading-6 text-blue-950">
                Aquí van documentos o capas generales que sirven para muchos avalúos.
                Si el archivo corresponde solo a un predio o barrio específico, guárdalo
                como soporte del avalúo en el numeral 2.
            </p>
            <div class="mt-4 grid gap-3 text-sm">
                <?php foreach ($groups as $group => $description): ?>
                    <div class="rounded-lg bg-white/70 p-3">
                        <p class="font-semibold text-blue-950"><?= e((string) $group) ?></p>
                        <p class="mt-1 leading-5 text-blue-900"><?= e((string) $description) ?></p>
                        <p class="mt-2 text-xs font-semibold leading-5 text-blue-800">
                            Nutre expediente: <?= e($targets[(string) $group] ?? 'Pendiente de clasificar por el analista.') ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">Subir documento MIDAS común</h2>
                    <p class="mt-1 text-sm text-slate-600">Puedes subir hasta 20 archivos por carga. Si alguno ya existe, se omite sin duplicarlo.</p>
                </div>
                <span class="rounded-full <?= !empty($storage['writable']) ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' ?> px-3 py-1 text-xs font-semibold">
                    <?= !empty($storage['writable']) ? 'Almacenamiento activo' : 'Revisar almacenamiento' ?>
                </span>
            </div>
            <form class="mt-5 grid gap-4 md:grid-cols-2" method="post" enctype="multipart/form-data"
                action="<?= e(url('midas/documentos')) ?>">
                <?= csrf_field() ?>
                <label class="label">Grupo de capa
                    <select class="input mt-2" name="layer_group" required>
                        <?php foreach (array_keys($groups) as $group): ?><option value="<?= e((string) $group) ?>"><?= e((string) $group) ?></option><?php endforeach; ?>
                    </select>
                </label>
                <label class="label">Código o referencia
                    <input class="input mt-2" name="document_code" maxlength="120" placeholder="Ej. CIRC-ALTILLO o POT-2001-USOS">
                    <span class="mt-1 block text-xs font-normal text-slate-500">En carga múltiple puedes dejarlo vacío; se toma del nombre de cada archivo.</span>
                </label>
                <label class="label md:col-span-2">Nombre del documento
                    <input class="input mt-2" name="title" maxlength="240" placeholder="Ej. Descarga MIDAS Localidades Histórica y del Caribe">
                    <span class="mt-1 block text-xs font-normal text-slate-500">Si subes varios, deja este campo vacío para nombrarlos uno por uno con el archivo.</span>
                </label>
                <label class="label">Estado
                    <select class="input mt-2" name="status">
                        <option value="vigente">Vigente</option>
                        <option value="historico">Histórico / referencia</option>
                        <option value="reemplazado">Reemplazado</option>
                    </select>
                </label>
                <label class="label">Archivo descargado
                    <input class="input mt-2" type="file" name="midas_file[]" accept=".pdf,.csv,.json,.geojson,.zip" multiple required>
                    <span class="mt-1 block text-xs font-normal text-slate-500">PDF, CSV, JSON, GeoJSON o ZIP. Máximo 25 MB por archivo y 20 archivos por carga.</span>
                </label>
                <label class="label md:col-span-2">Para qué es útil
                    <textarea class="input mt-2 min-h-20" name="practical_use" maxlength="700"
                        placeholder="Ej. Sustenta parqueaderos, altura, uso del suelo, riesgos o delimitación territorial."></textarea>
                </label>
                <label class="label md:col-span-2">Dónde se consulta
                    <input class="input mt-2" name="applies_to" maxlength="240" placeholder="Ej. Numeral 5, numeral 6, numeral 7 o módulo futuro 9">
                </label>
                <div class="md:col-span-2 flex justify-end"><button class="btn-primary" type="submit">Subir a Biblioteca MIDAS</button></div>
            </form>
        </article>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-slate-950">Documentos disponibles</h2>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                <?= e(count($documents)) ?> documento(s)
            </span>
        </div>
        <div class="mt-5 overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr><th class="px-4 py-3">Grupo</th><th class="px-4 py-3">Documento</th><th class="px-4 py-3">Utilidad</th><th class="px-4 py-3">Acción</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php foreach ($documents as $doc): ?>
                        <?php $term = mb_strtolower(implode(' ', [$doc['layer_group'], $doc['document_code'], $doc['title'], $doc['practical_use'], $doc['applies_to'], $doc['source_filename']])); ?>
                        <tr x-show='query === "" || <?= e(json_encode($term, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>.includes(query.toLowerCase())'>
                            <td class="px-4 py-3 font-semibold text-slate-800"><?= e($doc['layer_group']) ?></td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-950"><?= e($doc['title']) ?></p>
                                <p class="mt-1 text-xs text-slate-500"><?= e($doc['document_code']) ?> · <?= e($doc['source_filename']) ?> · <?= e($formatBytes($doc['file_size_bytes'])) ?></p>
                                <p class="mt-1 text-xs text-slate-500"><?= e($formatDate($doc['updated_at'])) ?> · <?= e($doc['status']) ?></p>
                            </td>
                            <td class="max-w-md px-4 py-3 text-slate-600">
                                <?= e($doc['practical_use'] ?: 'Utilidad pendiente de precisar.') ?>
                                <?php if ($doc['applies_to']): ?><span class="mt-1 block text-xs font-semibold text-slate-500"><?= e($doc['applies_to']) ?></span><?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a class="btn-secondary min-h-11" href="<?= e(url('midas/documentos/' . $doc['id'] . '/archivo')) ?>" data-no-fetch>Abrir</a>
                                    <form method="post" action="<?= e(url('midas/documentos/' . $doc['id'] . '/eliminar')) ?>"
                                        onsubmit="return confirm('¿Eliminar este documento de Biblioteca MIDAS?');">
                                        <?= csrf_field() ?><button class="btn-secondary min-h-11" type="submit">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$documents): ?><tr><td class="px-4 py-5 text-slate-600" colspan="4">Aún no hay documentos MIDAS cargados.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</section>
