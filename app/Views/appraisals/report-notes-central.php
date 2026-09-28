<?php
/** @var array<string, array{notes: array<int, array>, sections: array<string, string>}> $chapters */
$currentStep = 'ampliaciones';
$chapterLabels = [
    '1' => '1 · Expediente',
    '2' => '2 · Sector y entorno',
    '3' => '3 · Bien sujeto',
    '4' => '4 · Jurídicas',
    '5' => '5 · Normatividad urbana',
];
?>
<a href="<?= e(url('avaluos/' . $record['id'] . '/entregable')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Entregable</a>
<div class="mt-3">
    <p class="eyebrow">Maestro del entregable</p>
    <h1 class="mt-2 text-3xl font-semibold tracking-tight">Ampliaciones generales del informe</h1>
    <p class="mt-3 max-w-3xl text-slate-600">
        Agrega aclaraciones, salvedades o desarrollos complementarios en una sola pantalla. Cada texto se inserta debajo del numeral elegido sin recargar los módulos de trabajo.
    </p>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>

<?php if ($message): ?><p class="mt-5 rounded-xl bg-emerald-100 p-3 text-sm font-semibold text-emerald-800"><?= e($message) ?></p><?php endif; ?>
<?php if ($error): ?><p class="mt-5 rounded-xl bg-red-100 p-3 text-sm font-semibold text-red-800"><?= e($error) ?></p><?php endif; ?>

<div class="mt-7 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6"
    x-data="{activeReportChapter: (location.hash || '#capitulo-1').replace('#capitulo-', '')}"
    x-init="$watch('activeReportChapter', value => history.replaceState(null, '', '#capitulo-' + value))">
    <div class="grid gap-2 rounded-xl bg-slate-100 p-2 md:grid-cols-5">
        <?php foreach ($chapterLabels as $chapter => $label): ?>
            <button type="button" class="rounded-lg px-4 py-3 text-left text-sm font-semibold"
                @click="activeReportChapter='<?= e($chapter) ?>'"
                :class="activeReportChapter === '<?= e($chapter) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                <span class="block"><?= e($label) ?></span>
                <span class="text-xs font-normal"><?= e(count($chapters[$chapter]['notes'] ?? [])) ?> ampliación(es)</span>
            </button>
        <?php endforeach; ?>
    </div>
    <?php foreach ($chapterLabels as $chapter => $label): ?>
        <div x-show="activeReportChapter === '<?= e($chapter) ?>'" x-cloak>
            <?php
            $reportNotes = $chapters[$chapter]['notes'] ?? [];
            $reportNoteSections = $chapters[$chapter]['sections'] ?? [];
            $reportNoteChapter = $chapter;
            $reportNoteReturn = 'avaluos/' . $record['id'] . '/ampliaciones-entregable#capitulo-' . $chapter;
            $reportNoteHideFlash = true;
            require BASE_PATH . '/app/Views/appraisals/report-extra-notes.php';
            ?>
        </div>
    <?php endforeach; ?>
</div>
