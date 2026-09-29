<?php
$documents = is_array($documents ?? null) ? $documents : [];
$groups = is_array($groups ?? null) ? $groups : [];
$storage = is_array($storage ?? null) ? $storage : [];
$activeGroup = isset($groups[$activeGroup ?? '']) ? (string) $activeGroup : (string) array_key_first($groups);
$groupStats = is_array($groupStats ?? null) ? $groupStats : [];
$activeDocuments = array_values(array_filter($documents, static fn (array $doc): bool => ($doc['layer_group'] ?? '') === $activeGroup));
$targets = [
    'Localidades' => 'Numeral 2: sector y fuente territorial. Numeral 3: localidad del predio.',
    'Unidades comuneras de gobierno' => 'Numeral 2: contexto urbano. Numeral 3: UCG del inmueble.',
    'Barrios / división política' => 'Numeral 2: barrio o sector, delimitación y fuente territorial. Numeral 3: localidad, barrio y UCG.',
    'Uso del suelo y tratamientos' => 'Numeral 5: lectura MIDAS del predio, actividad, usos permitidos, tratamiento y conclusión urbana.',
    'Circulares MIDAS' => 'Numeral 5 y futuro módulo 9: criterios complementarios de Planeación.',
    'Circulares urbanísticas' => 'Numeral 5 y futuro potencial: altura, parqueaderos, altillos y salvedades normativas.',
    'Servicios públicos' => 'Numeral 2: cobertura y calidad del entorno; numeral 7 si hay limitaciones.',
    'Transporte y movilidad' => 'Numeral 2: accesibilidad; numeral 6: dinámica económica y mercado objetivo.',
    'Equipamiento urbano' => 'Numerales 2 y 6: salud, educación, comercio, seguridad y servicios de soporte.',
    'Educación' => 'Numerales 2 y 6: equipamientos educativos y atracción sectorial.',
    'Salud' => 'Numerales 2 y 6: concentración de servicios de salud y mercado objetivo.',
    'Seguridad' => 'Numeral 7: lectura de seguridad y condiciones restrictivas.',
    'Cultura' => 'Numerales 2 y 6: equipamientos culturales y dinámica urbana.',
    'Ambiente y riesgos' => 'Numeral 7: inundación, licuación, amenazas y condiciones restrictivas.',
    'Cambio climático' => 'Numeral 7: amenazas, vulnerabilidad, adaptación y salvedades ambientales.',
    'Otro soporte MIDAS' => 'Se usa solo si el analista define qué campo o numeral sustenta.',
];
$sections = [
    'Base territorial' => ['Localidades', 'Unidades comuneras de gobierno', 'Barrios / división política'],
    'Norma urbana' => ['Uso del suelo y tratamientos', 'Circulares MIDAS'],
    'Entorno y restricciones' => ['Servicios públicos', 'Transporte y movilidad', 'Equipamiento urbano', 'Educación', 'Salud', 'Seguridad', 'Cultura', 'Ambiente y riesgos', 'Cambio climático', 'Otro soporte MIDAS'],
];
$formatBytes = static fn ($bytes): string => number_format(((int) $bytes) / 1024, 1, ',', '.') . ' KB';
$formatDate = static function ($value): string {
    try {
        return (new DateTimeImmutable((string) $value, new DateTimeZone('UTC')))
            ->setTimezone(new DateTimeZone('America/Bogota'))->format('d/m/Y H:i');
    } catch (Throwable) { return (string) $value; }
};
$limits = is_array($storage['limits'] ?? null) ? $storage['limits'] : [];
?>
<section id="biblioteca-midas" class="space-y-7" x-data="{ query: '', guide: 'Base territorial' }">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-teal-800">Biblioteca cartográfica</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">MIDAS</h1>
            <p class="mt-3 max-w-3xl text-slate-600">
                Guarda una sola vez las descargas comunes de MIDAS: circulares urbanísticas, división política,
                servicios, movilidad, equipamientos, riesgos y soportes cartográficos.
                El POT base se conserva en Normatividad Urbana; aquí solo van capas MIDAS o evidencias reutilizables.
            </p>
        </div>
        <label class="block min-w-full text-sm font-medium text-slate-700 lg:min-w-80">
            Buscar en Biblioteca MIDAS
            <input class="input mt-2" type="search" placeholder="barrio, riesgo, transporte" x-model.trim="query">
        </label>
    </div>
    <?php if ($message): ?><p class="rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($error) ?></p><?php endif; ?>

    <section class="grid gap-5 xl:grid-cols-[1.05fr_0.95fr]">
        <?php require BASE_PATH . '/app/Views/midas/guide.php'; ?>

        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">Subir documento MIDAS común</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Puedes subir por lotes. No se autoguarda: queda en biblioteca cuando pulses Subir.
                        Si alguno ya existe, se omite sin duplicarlo.
                    </p>
                </div>
                <span class="rounded-full <?= !empty($storage['writable']) ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' ?> px-3 py-1 text-xs font-semibold">
                    <?= !empty($storage['writable']) ? 'Almacenamiento activo' : 'Revisar almacenamiento' ?>
                </span>
            </div>
            <form class="mt-5 grid gap-4 md:grid-cols-2" method="post" enctype="multipart/form-data"
                action="<?= e(url('midas/documentos')) ?>" data-upload-progress data-upload-label="documento MIDAS" data-upload-timeout="600000">
                <?= csrf_field() ?>
                <label class="label">Grupo de capa
                    <select class="input mt-2" name="layer_group" required>
                        <?php foreach (array_keys($groups) as $group): ?>
                            <option value="<?= e((string) $group) ?>" <?= (string) $group === $activeGroup ? 'selected' : '' ?>>
                                <?= e((string) $group) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="label">Código o referencia
                    <input class="input mt-2" name="document_code" maxlength="120" placeholder="Ej. BARRIO-BOCAGRANDE o CIRC-ALTILLO">
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
                    <input class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700"
                        type="file" name="midas_file[]" accept=".pdf,.csv,.json,.geojson,.zip" multiple required>
                    <span class="mt-1 block text-xs font-normal text-slate-500">
                        PDF, CSV, JSON, GeoJSON o ZIP. Máximo interno 25 MB por archivo; el lote también respeta post_max_size.
                    </span>
                </label>
                <div class="rounded-lg bg-slate-50 p-3 text-xs leading-5 text-slate-600 md:col-span-2">
                    <span class="font-semibold text-slate-700">Límites del servidor:</span>
                    archivo <?= e((string) ($limits['upload_max_filesize'] ?? '')) ?>,
                    lote <?= e((string) ($limits['post_max_size'] ?? '')) ?>,
                    cantidad <?= e((string) ($limits['max_file_uploads'] ?? '')) ?>.
                    Si son varios PDF pesados, súbelos en lotes pequeños.
                </div>
                <label class="label md:col-span-2">Para qué es útil
                    <textarea class="input mt-2 min-h-20" name="practical_use" maxlength="700"
                        placeholder="Ej. Sustenta parqueaderos, altura, uso del suelo, riesgos o delimitación territorial."></textarea>
                </label>
                <label class="label md:col-span-2">Dónde se consulta
                    <input class="input mt-2" name="applies_to" maxlength="240" placeholder="Ej. Numeral 5, numeral 6, numeral 7 o módulo futuro 9">
                </label>
                <div class="md:col-span-2"><?php require BASE_PATH . '/app/Views/appraisals/upload-progress.php'; ?></div>
                <div class="md:col-span-2 flex justify-end"><button class="btn-primary" type="submit">Subir a Biblioteca MIDAS</button></div>
            </form>
        </article>
    </section>

    <nav class="rounded-lg bg-slate-200/70 p-2" aria-label="Grupos de documentos MIDAS">
        <div class="flex gap-2 overflow-x-auto">
            <?php foreach ($groups as $group => $description): ?>
                <?php $isActive = (string) $group === $activeGroup; ?>
                <a class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-md px-3 py-2 text-xs font-semibold transition <?= $isActive ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70' ?>"
                    href="<?= e(url('midas?grupo=' . rawurlencode((string) $group) . '#documentos-midas')) ?>"
                    <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <span class="max-w-48 truncate"><?= e((string) $group) ?></span>
                    <span class="rounded-full bg-white/80 px-2 py-0.5 text-[11px] text-slate-500"><?= e((string) ($groupStats[(string) $group] ?? 0)) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <section id="documentos-midas" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-950"><?= e($activeGroup) ?></h2>
                <p class="mt-1 text-sm leading-6 text-slate-600"><?= e((string) ($groups[$activeGroup] ?? 'Documentos MIDAS.')) ?></p>
                <p class="mt-2 text-xs font-semibold leading-5 text-blue-800">Se inserta en: <?= e($targets[$activeGroup] ?? 'Pendiente de clasificar por el analista.') ?></p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                <?= e(count($activeDocuments)) ?> documento(s)
            </span>
        </div>
        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            <?php foreach ($activeDocuments as $doc): ?>
                <?php $term = mb_strtolower(implode(' ', [$doc['layer_group'], $doc['document_code'], $doc['title'], $doc['practical_use'], $doc['applies_to'], $doc['source_filename']])); ?>
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
                    x-show='query === "" || <?= e(json_encode($term, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>.includes(query.toLowerCase())'>
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-anywhere text-sm font-semibold text-teal-800"><?= e($doc['document_code']) ?></p>
                            <h3 class="text-anywhere mt-2 font-semibold leading-6 text-slate-950"><?= e($doc['title']) ?></h3>
                        </div>
                        <span class="text-anywhere max-w-36 shrink-0 rounded-full bg-slate-100 px-3 py-1 text-center text-xs font-semibold text-slate-600">
                            <?= e($doc['layer_group']) ?>
                        </span>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-slate-600">
                        <?= e($doc['practical_use'] ?: 'Utilidad pendiente de precisar.') ?>
                    </p>
                    <p class="text-anywhere mt-4 text-sm text-slate-500">
                        <?= e($doc['source_filename']) ?> · <?= e($formatBytes($doc['file_size_bytes'])) ?>
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        <?= e($formatDate($doc['updated_at'])) ?> · <?= e($doc['status']) ?>
                    </p>
                    <p class="mt-4 rounded-lg bg-blue-50 p-3 text-xs font-semibold leading-5 text-blue-800">
                        Se inserta en: <?= e($doc['applies_to'] ?: ($targets[$doc['layer_group']] ?? 'Pendiente de clasificar por el analista.')) ?>
                    </p>
                    <div class="mt-5 flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                        <a class="btn-secondary min-h-11" href="<?= e(url('midas/documentos/' . $doc['id'] . '/archivo')) ?>" data-no-fetch>Abrir</a>
                        <form method="post" action="<?= e(url('midas/documentos/' . $doc['id'] . '/eliminar')) ?>"
                            onsubmit="return confirm('¿Eliminar este documento de Biblioteca MIDAS?');">
                            <?= csrf_field() ?>
                            <button class="btn-secondary min-h-11" type="submit">Eliminar</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
            <?php if (!$activeDocuments): ?>
                <p class="rounded-xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-600 lg:col-span-2">
                    Aún no hay documentos MIDAS cargados en este grupo.
                </p>
            <?php endif; ?>
        </div>
    </section>
</section>
