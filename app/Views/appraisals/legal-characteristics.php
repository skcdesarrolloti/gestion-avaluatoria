<?php
$currentStep = 'juridicas';
$data = is_array($profile['data'] ?? null) ? $profile['data'] : [];
$annotations = is_array($profile['annotations'] ?? null) ? $profile['annotations'] : [];
$alerts = is_array($profile['alerts'] ?? null) ? $profile['alerts'] : [];
$latest = $certificates[0] ?? null;
$field = static fn (string $key): string => (string) ($data[$key] ?? '');
$label = static fn (string $key): string => (string) ($legalLabels[$key] ?? $key);
$isTextarea = static fn (string $key): bool => str_starts_with($key, 'reporte_')
    || in_array($key, ['cabida_linderos', 'reformas_ph', 'matriculas_derivadas'], true);
$legalTabNumbers = ['registral' => '4.1', 'catastro' => '4.2', 'ph' => '4.3', 'informe' => '4.4'];
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Numeral 4 · Características jurídicas</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Lectura registral asistida</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Sube el Certificado de Tradición y Libertad. El sistema extrae datos y propone una lectura preliminar;
            el analista conserva el control y valida antes de llevarlo al informe.
        </p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">Numeral 4</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>

<?php if ($legalMessage): ?>
    <p class="mt-6 rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($legalMessage) ?></p>
<?php endif; ?>
<?php if ($legalError): ?>
    <p class="mt-6 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($legalError) ?></p>
<?php endif; ?>

<section class="mt-7 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-5">
        <div>
            <p class="eyebrow">Certificado de tradición y libertad</p>
            <h2 class="mt-2 text-2xl font-semibold">Cargar y analizar documento</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                La lectura se hace solamente sobre el archivo subido. Si el PDF viene escaneado,
                el sistema lo dejará visible como pendiente para OCR o diligenciamiento manual.
            </p>
        </div>
        <?php if ($latest): ?>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">
                <?= e($latest['analysis_status']) ?> · <?= e((string) $latest['extracted_chars']) ?> caracteres
            </span>
        <?php endif; ?>
    </div>
    <form class="mt-6 grid gap-4 lg:grid-cols-[1fr_auto]" method="post" enctype="multipart/form-data"
        action="<?= e(url('avaluos/' . $record['id'] . '/caracteristicas-juridicas/certificado')) ?>">
        <?= csrf_field() ?>
        <label class="label">Certificado registral
            <input class="input mt-2" type="file" name="legal_certificate" accept=".pdf,.docx,.txt,application/pdf,text/plain">
            <span class="mt-2 block text-sm font-normal leading-6 text-slate-500">
                Formatos permitidos: PDF, DOCX o TXT. Máximo 25 MB.
            </span>
        </label>
        <button class="btn-primary self-start lg:mt-8" type="submit">Subir y analizar certificado</button>
    </form>
    <?php if ($latest): ?>
        <form class="mt-4 flex flex-wrap items-center gap-3" method="post"
            action="<?= e(url('avaluos/' . $record['id'] . '/caracteristicas-juridicas/reanalizar')) ?>">
            <?= csrf_field() ?>
            <button class="btn-secondary" type="submit">Reanalizar último certificado cargado</button>
            <span class="text-sm leading-6 text-slate-500">
                Usa esta opción si el archivo ya aparece en la tabla y quieres volver a llenar campos vacíos.
            </span>
        </form>
    <?php endif; ?>
    <?php if ($certificates): ?>
        <div class="mt-6 overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                    <tr><th class="px-4 py-3">Archivo</th><th class="px-4 py-3">Lectura</th><th class="px-4 py-3">Fecha</th><th class="px-4 py-3">Acción</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($certificates as $certificate): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <a class="font-semibold text-blue-800 underline" href="<?= e(url('avaluos/' . $record['id'] . '/caracteristicas-juridicas/certificados/' . $certificate['id'])) ?>">
                                    <?= e($certificate['source_filename']) ?>
                                </a>
                                <span class="block text-xs text-slate-500"><?= e(number_format((int) $certificate['file_size_bytes'] / 1024, 1, ',', '.')) ?> KB</span>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?= e($certificate['analysis_message']) ?></td>
                            <td class="px-4 py-3 text-slate-600"><?= e($certificate['created_at']) ?></td>
                            <td class="px-4 py-3">
                                <form method="post"
                                    action="<?= e(url('avaluos/' . $record['id'] . '/caracteristicas-juridicas/certificados/' . $certificate['id'] . '/eliminar')) ?>"
                                    onsubmit="return confirm('¿Eliminar este certificado del avalúo? Los campos ya diligenciados se conservarán.');">
                                    <?= csrf_field() ?>
                                    <button class="btn-secondary min-h-9 px-3 py-1 text-xs text-red-700" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<form class="mt-7 space-y-6" method="post" action="<?= e(url('avaluos/' . $record['id'] . '/caracteristicas-juridicas')) ?>">
    <?= csrf_field() ?>
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-5">
            <div>
                <p class="eyebrow">Ficha jurídica del avalúo</p>
                <h2 class="mt-2 text-2xl font-semibold">Campos revisables</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                    Los campos vacíos quedan pendientes. Lo que corrijas manualmente se guarda como criterio del analista.
                </p>
            </div>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-800">
                <?= e($profile['status'] ?? 'Pendiente de revisión') ?>
            </span>
        </div>
        <div class="mt-6" x-data="{ activeLegalTab: 'registral' }">
            <div class="rounded-2xl bg-slate-100 p-2">
                <div class="flex gap-2 overflow-x-auto pb-2" role="tablist" aria-label="Submenús jurídicos">
                    <?php foreach ($legalGroups as $groupKey => [$groupTitle, $keys]): ?>
                        <button class="min-h-14 shrink-0 rounded-xl px-5 py-3 text-left text-sm font-semibold transition"
                            type="button" role="tab" @click="activeLegalTab = '<?= e($groupKey) ?>'"
                            :aria-selected="activeLegalTab === '<?= e($groupKey) ?>'"
                            :class="activeLegalTab === '<?= e($groupKey) ?>' ? 'bg-teal-700 text-white shadow-sm' : 'bg-white text-teal-800 hover:bg-teal-50'">
                            <span class="block text-xs opacity-80"><?= e($legalTabNumbers[$groupKey] ?? '4') ?></span>
                            <?= e($groupTitle) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php foreach ($legalGroups as $groupKey => [$groupTitle, $keys]): ?>
                <section class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4"
                    x-show="activeLegalTab === '<?= e($groupKey) ?>'">
                    <h3 class="text-lg font-semibold text-slate-900">
                        <?= e(($legalTabNumbers[$groupKey] ?? '4') . ' ' . $groupTitle) ?>
                    </h3>
                    <div class="mt-5 grid gap-5 md:grid-cols-2">
                        <?php foreach ($keys as $key): ?>
                            <label class="label <?= $isTextarea($key) ? 'md:col-span-2' : '' ?>"><?= e($label($key)) ?>
                                <?php if ($isTextarea($key)): ?>
                                    <textarea class="input mt-2 min-h-28" name="<?= e($key) ?>" placeholder="Pendiente de lectura o revisión manual"><?= e($field($key)) ?></textarea>
                                <?php else: ?>
                                    <input class="input mt-2 <?= $field($key) === '' ? 'bg-slate-50' : '' ?>" name="<?= e($key) ?>"
                                        value="<?= e($field($key)) ?>" placeholder="Pendiente de lectura o revisión manual">
                                <?php endif; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6">
            <h2 class="text-lg font-semibold text-amber-950">Alertas automáticas</h2>
            <?php if ($alerts): ?>
                <ul class="mt-4 space-y-2 text-sm leading-6 text-amber-900">
                    <?php foreach ($alerts as $alert): ?><li>• <?= e($alert) ?></li><?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="mt-4 text-sm leading-6 text-amber-900">Sin alertas automáticas. Igual valida el certificado completo.</p>
            <?php endif; ?>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-semibold">Control de anotaciones</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Detectadas: <strong><?= e((string) count($annotations)) ?></strong>.
                Vigentes con revisión:
                <strong><?= e((string) count(array_filter($annotations, static fn ($a): bool => ($a['requiere_revision'] ?? '') === 'Sí'))) ?></strong>.
            </p>
        </div>
    </section>

    <?php if ($annotations): ?>
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-xl font-semibold">Anotaciones clasificadas</h2>
            <div class="mt-5 overflow-x-auto rounded-xl border border-slate-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr><th class="px-4 py-3">No.</th><th class="px-4 py-3">Categoría</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3">Lectura</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($annotations as $annotation): ?>
                            <tr>
                                <td class="px-4 py-3 font-semibold"><?= e($annotation['orden'] ?? '') ?></td>
                                <td class="px-4 py-3"><?= e($annotation['categoria'] ?? '') ?></td>
                                <td class="px-4 py-3"><?= e($annotation['estado_juridico'] ?? '') ?></td>
                                <td class="px-4 py-3 text-slate-700"><?= e($annotation['impacto_resumen'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>

    <details class="rounded-2xl border border-slate-200 bg-white p-6">
        <summary class="cursor-pointer text-lg font-semibold">Texto extraído del certificado</summary>
        <pre class="mt-4 max-h-96 overflow-auto whitespace-pre-wrap rounded-xl bg-slate-950 p-4 text-xs leading-5 text-slate-100"><?= e((string) ($profile['extracted_text'] ?? '')) ?></pre>
    </details>

    <div class="flex flex-wrap justify-end gap-3">
        <button class="btn-secondary" type="submit">Guardar numeral 4</button>
        <button class="btn-primary" type="submit" name="next" value="deliverable">Guardar y pasar a Entregable</button>
    </div>
</form>
