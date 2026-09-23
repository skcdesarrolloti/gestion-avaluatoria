<?php $currentStep = 'entregable'; ?>
<?php
$ph = is_array($phProfile ?? null) ? $phProfile : [];
$phTechnical = is_array($ph['technical'] ?? null) ? $ph['technical'] : [];
$chapterOneData = is_array($chapterOne ?? null) ? $chapterOne : ['sections' => [], 'text' => ''];
$chapterOneText = (string) ($chapterOneData['text'] ?? '');
$chapterOneSections = is_array($chapterOneData['sections'] ?? null) ? $chapterOneData['sections'] : [];
$chapter = is_array($subjectChapter ?? null) ? $subjectChapter : ['sections' => [], 'text' => ''];
$chapterText = (string) ($chapter['text'] ?? '');
$chapterSections = is_array($chapter['sections'] ?? null) ? $chapter['sections'] : [];
$phSummaryKeys = [
    'resumen_base_ph' => 'Base PH común',
    'resumen_trazabilidad_ph' => 'Documento y trazabilidad',
    'resumen_identificacion_ph' => 'Identificación PH',
    'resumen_tipologia_ph' => 'Tipología y régimen',
    'resumen_configuracion_ph' => 'Configuración predial',
    'resumen_comunes_ph' => 'Bienes comunes y soporte',
    'resumen_reglas_ph' => 'Reglas de uso y operación',
    'resumen_administracion_ph' => 'Administración y cargas',
    'resumen_incidencia_ph' => 'Incidencia valuatoria',
    'resumen_notas_ph' => 'Notas normativas',
];
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Entregable</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Informe consolidado del avalúo</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Aquí se arma la narrativa que pasa al informe. Los capítulos consolidados toman los datos del expediente,
            bien sujeto, PH, obsolescencias y soportes normativos visibles.
        </p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">Único entregable</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>


<section class="mt-8 rounded-2xl border border-blue-100 bg-blue-50 p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Capítulo 1 · Memoria descriptiva</p>
            <h2 class="mt-2 text-2xl font-semibold text-blue-950">Texto consolidado del expediente valuatorio</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-blue-900">
                Se construye con solicitante, encargo, activo, derecho valuado, uso previsto, base de valor,
                fechas, documentos e insumos. Si falta un dato, el texto lo deja como pendiente.
            </p>
        </div>
        <a class="rounded-full bg-white px-4 py-2 text-sm font-bold text-blue-800" href="<?= e(url('avaluos/' . $record['id'] . '/expediente')) ?>">Editar expediente</a>
    </div>
    <textarea class="input mt-5 min-h-80 bg-white font-mono text-sm leading-6" rows="18" readonly><?= e($chapterOneText) ?></textarea>
</section>

<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <p class="eyebrow">Capítulo 1 por secciones</p>
    <h2 class="mt-2 text-2xl font-semibold">Cómo queda la memoria descriptiva</h2>
    <div class="mt-5 grid gap-4">
        <?php foreach ($chapterOneSections as $section): ?>
            <article class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6">
                <h3 class="font-semibold text-slate-950"><?= e((string) ($section[0] ?? 'Sección')) ?></h3>
                <p class="mt-2 whitespace-pre-wrap text-slate-700"><?= e((string) ($section[1] ?? '')) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="mt-8 rounded-2xl border border-emerald-100 bg-emerald-50 p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Capítulo 3 · Bien sujeto</p>
            <h2 class="mt-2 text-2xl font-semibold text-emerald-950">Texto consolidado para el informe</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-emerald-800">
                Este texto se construye con los datos vivos del módulo 3. Mantiene la redacción de informe,
                deja las fuentes y salvedades normativas a la vista y evita copiar matrices internas completas.
            </p>
        </div>
        <a class="rounded-full bg-white px-4 py-2 text-sm font-bold text-emerald-800" href="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto')) ?>">Editar módulo 3</a>
    </div>
    <textarea class="input mt-5 min-h-80 bg-white font-mono text-sm leading-6" rows="18" readonly><?= e($chapterText) ?></textarea>
    <p class="mt-2 text-xs leading-5 text-emerald-800">
        Si el texto necesita ajuste fino, modifica los campos fuente en 3.1 a 3.7. Los soportes extensos quedan en cada ficha.
    </p>
</section>

<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <p class="eyebrow">Lectura por secciones</p>
    <h2 class="mt-2 text-2xl font-semibold">Cómo queda estructurado el capítulo</h2>
    <div class="mt-5 grid gap-4">
        <?php foreach ($chapterSections as $section): ?>
            <article class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6">
                <h3 class="font-semibold text-slate-950"><?= e((string) ($section[0] ?? 'Sección')) ?></h3>
                <p class="mt-2 whitespace-pre-wrap text-slate-700"><?= e((string) ($section[1] ?? '')) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <p class="eyebrow">Propiedad horizontal</p>
    <h2 class="mt-2 text-2xl font-semibold">Soportes PH que quedan en ficha</h2>
    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
        El texto PH ya está integrado en el capítulo 3 cuando existe. Estos bloques conservan la trazabilidad interna
        del numeral 3.5 para revisar qué se tomó como soporte.
    </p>
    <div class="mt-5 grid gap-3 lg:grid-cols-2">
        <?php foreach ($phSummaryKeys as $key => $label): ?>
            <?php $text = trim((string) ($phTechnical[$key] ?? '')); ?>
            <?php if ($text === '') continue; ?>
            <article class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6">
                <h3 class="font-semibold text-slate-900"><?= e($label) ?></h3>
                <p class="mt-2 text-slate-700"><?= e($text) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
