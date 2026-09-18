<?php
$currentStep = 'juridicas';
$data = is_array($profile['data'] ?? null) ? $profile['data'] : [];
$annotations = is_array($profile['annotations'] ?? null) ? $profile['annotations'] : [];
$alerts = is_array($profile['alerts'] ?? null) ? $profile['alerts'] : [];
$latest = $certificates[0] ?? null;
$field = static fn (string $key): string => (string) ($data[$key] ?? '');
$label = static fn (string $key): string => (string) ($legalLabels[$key] ?? $key);
$isTextarea = static fn (string $key): bool => str_starts_with($key, 'reporte_')
    || str_starts_with($key, 'revision_') || in_array($key, ['cabida_linderos', 'reformas_ph',
        'matriculas_derivadas', 'salvedad_final'], true);
$checkboxFields = ['check_tradicion', 'check_gravamenes', 'check_limitaciones_dominio',
    'check_medidas_cautelares', 'check_propiedad_horizontal', 'check_otras'];
$selectOptions = [
    'semaforo_manual' => ['' => 'Pendiente de lectura o revisión manual', 'Normal' => 'Normal',
        'Atención' => 'Atención', 'Crítico' => 'Crítico'],
    'clasificacion_manual' => ['' => 'Pendiente de lectura o revisión manual',
        'Sin alertas automáticas relevantes' => 'Sin alertas automáticas relevantes',
        'Con alertas para revisión jurídica' => 'Con alertas para revisión jurídica',
        'Requiere estudio jurídico especializado' => 'Requiere estudio jurídico especializado'],
];
$legalSections = \App\Support\AppraisalLegalView::sections();
$matrixRows = \App\Support\AppraisalLegalView::matrixRows();
$legalAnnotationGroups = \App\Support\AppraisalLegalView::groupedAnnotations($annotations);
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Numeral 4 · Características jurídicas</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Lectura registral asistida</h1>
        <p class="mt-3 max-w-3xl text-slate-600">Sube el Certificado de Tradición y Libertad. El sistema extrae datos y propone una lectura preliminar; el analista conserva el control y valida antes de llevarlo al informe.</p>
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
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">La lectura se hace solamente sobre el archivo subido. Si el PDF o la imagen no trae texto legible, el sistema lo dejará visible como pendiente para OCR o diligenciamiento manual.</p>
        </div>
        <?php if ($latest): ?>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">
                <?= e($latest['analysis_status']) ?> · <?= e((string) $latest['extracted_chars']) ?> caracteres
            </span>
        <?php endif; ?>
    </div>
    <form class="mt-6 grid gap-4 lg:grid-cols-[1fr_auto]" method="post" enctype="multipart/form-data"
        data-legal-certificate-form
        action="<?= e(url('avaluos/' . $record['id'] . '/caracteristicas-juridicas/certificado')) ?>">
        <?= csrf_field() ?>
        <textarea name="client_extracted_text" hidden data-client-extracted-text></textarea>
        <label class="label">Certificado registral
            <input class="input mt-2" type="file" name="legal_certificate"
                accept=".pdf,.docx,.txt,.jpg,.jpeg,.png,.webp,.tif,.tiff,application/pdf,text/plain,image/jpeg,image/png,image/webp,image/tiff">
            <span class="mt-2 block text-sm font-normal leading-6 text-slate-500">Formatos permitidos: PDF, DOCX, TXT o imagen JPG, PNG, WEBP o TIFF. Máximo 25 MB.</span>
        </label>
        <button class="btn-primary self-start lg:mt-8" type="submit">Subir y analizar certificado</button>
        <p class="text-sm font-semibold text-teal-800 lg:col-span-2" data-legal-reader-status aria-live="polite"></p>
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

<form class="mt-7 space-y-6" method="post" action="<?= e(url('avaluos/' . $record['id'] . '/caracteristicas-juridicas')) ?>"
    data-module-autosave
    data-autosave-endpoint="<?= e(url('avaluos/' . $record['id'] . '/caracteristicas-juridicas/autoguardar')) ?>">
    <?= csrf_field() ?>
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-5">
            <div>
                <p class="eyebrow">Ficha jurídica del avalúo</p>
                <h2 class="mt-2 text-2xl font-semibold">Campos revisables</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                    Los campos vacíos quedan pendientes. Lo que corrijas manualmente se guarda como criterio del analista.
                </p>
                <p class="mt-2 text-xs font-semibold text-teal-800" role="status" aria-live="polite"
                    data-autosave-status>Autoguardado activo</p>
            </div>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-800">
                <?= e($profile['status'] ?? 'Pendiente de revisión') ?>
            </span>
        </div>
        <div class="mt-6" x-data="{ activeLegalTab: 'registral' }">
            <div class="rounded-2xl bg-slate-100 p-2">
                <div class="flex gap-2 overflow-x-auto pb-2" role="tablist" aria-label="Submenús jurídicos">
                    <?php foreach ($legalSections as $sectionKey => [$sectionTitle]): ?>
                        <button class="min-h-14 shrink-0 rounded-xl px-5 py-3 text-left text-sm font-semibold transition"
                            type="button" role="tab" @click="activeLegalTab = '<?= e($sectionKey) ?>'"
                            :aria-selected="activeLegalTab === '<?= e($sectionKey) ?>'"
                            :class="activeLegalTab === '<?= e($sectionKey) ?>' ? 'bg-blue-800 text-white shadow-sm' : 'bg-white text-blue-900 hover:bg-blue-50'">
                            <?= e($sectionTitle) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php foreach ($legalSections as $sectionKey => [$sectionTitle, $sectionHelp, $blocks]): ?>
                <section class="mt-5 space-y-5 rounded-xl border border-slate-200 bg-slate-50 p-4"
                    x-show="activeLegalTab === '<?= e($sectionKey) ?>'">
                    <p class="text-sm leading-6 text-slate-600"><?= e($sectionHelp) ?></p>
                    <?php if ($sectionKey === 'tradicion'): ?>
                        <?php foreach ([
                            'Cadena de tradición' => 'tradicion',
                            'Gravámenes' => 'gravamen',
                            'Limitaciones al dominio' => 'limitacion_dominio',
                            'Medidas cautelares / judiciales' => 'medida_cautelar',
                            'Otras anotaciones relevantes' => 'otras',
                        ] as $tableTitle => $tableKey): ?>
                            <?php $tableRows = $legalAnnotationGroups[$tableKey] ?? []; ?>
                            <?php require BASE_PATH . '/app/Views/appraisals/legal-annotation-table.php'; ?>
                        <?php endforeach; ?>
                    <?php elseif ($sectionKey === 'informe'): ?>
                        <?php $matrixReadonly = false; require BASE_PATH . '/app/Views/appraisals/legal-matrix.php'; unset($matrixReadonly); ?>
                        <?php $blockTitle = 'Validación y criterio del analista';
                        $blockFields = ['semaforo_manual', 'clasificacion_manual', 'revision_analista',
                            'salvedad_final', 'reporte_conclusion_entregable']; ?>
                        <?php $blockScope = $sectionKey; require BASE_PATH . '/app/Views/appraisals/legal-block.php'; unset($blockScope); ?>
                    <?php elseif ($sectionKey === 'impresion'): ?>
                        <?php $matrixReadonly = true; require BASE_PATH . '/app/Views/appraisals/legal-matrix.php'; unset($matrixReadonly); ?>
                        <?php $blockTitle = 'Texto profesional para el entregable';
                        $blockFields = ['reporte_profesional_entregable']; ?>
                        <?php $blockScope = $sectionKey; require BASE_PATH . '/app/Views/appraisals/legal-block.php'; unset($blockScope); ?>
                    <?php else: ?>
                        <?php foreach ($blocks as [$blockTitle, $blockFields]): ?>
                            <?php $blockScope = $sectionKey; require BASE_PATH . '/app/Views/appraisals/legal-block.php'; unset($blockScope); ?>
                        <?php endforeach; ?>
                        <?php if ($sectionKey === 'ph'): ?>
                            <?php $tableTitle = 'Actos de propiedad horizontal identificados';
                            $tableRows = $legalAnnotationGroups['ph'] ?? []; ?>
                            <?php require BASE_PATH . '/app/Views/appraisals/legal-annotation-table.php'; ?>
                        <?php endif; ?>
                    <?php endif; ?>
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

    <details class="rounded-2xl border border-slate-200 bg-white p-6">
        <summary class="cursor-pointer text-lg font-semibold">Texto extraído del certificado</summary>
        <pre class="mt-4 max-h-96 overflow-auto whitespace-pre-wrap rounded-xl bg-slate-950 p-4 text-xs leading-5 text-slate-100"><?= e((string) ($profile['extracted_text'] ?? '')) ?></pre>
    </details>

    <div class="flex flex-wrap justify-end gap-3">
        <button class="btn-secondary" type="submit">Guardar numeral 4</button>
        <button class="btn-primary" type="submit" name="next" value="deliverable">Guardar y pasar a Entregable</button>
    </div>
</form>
<script type="module" src="<?= e(asset_url('assets/legal-certificate-reader.js')) ?>"></script>
