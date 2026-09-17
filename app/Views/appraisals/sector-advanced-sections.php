<?php
$advancedCatalog = is_array($sectorAdvancedCatalog ?? null) ? $sectorAdvancedCatalog : [];
$advancedRows = is_array($sectorAdvancedRows ?? null) ? $sectorAdvancedRows : [];
$bankRows = array_column(is_array($sectorBankSections ?? null) ? $sectorBankSections : [], null, 'section_code');
$sectorBankSourcesByKey = array_column(is_array($sectorBankSources ?? null) ? $sectorBankSources : [], null, 0);
$advancedDefaults = \App\Services\AppraisalSectorAdvancedPrefill::sections($subject ?? [], $sector ?? []);
$summary = is_array($sectorBankSummary ?? null) ? $sectorBankSummary : [];
$level = (string) ($summary['level'] ?? 'ROJO');
$levelClass = $level === 'VERDE' ? 'bg-emerald-50 text-emerald-800 border-emerald-200'
    : ($level === 'AMARILLO' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-red-50 text-red-800 border-red-200');
$decodeData = static function (?array $row): array {
    if (!$row || empty($row['data_json'])) return [];
    $data = json_decode((string) $row['data_json'], true);
    return is_array($data) ? $data : [];
};
$fmtAdvancedDate = static function ($value): string {
    if (empty($value)) return 'Pendiente';
    try {
        return (new DateTimeImmutable((string) $value, new DateTimeZone('UTC')))
            ->setTimezone(new DateTimeZone('America/Bogota'))->format('d/m/Y H:i');
    } catch (Throwable) {
        return (string) $value;
    }
};
?>
<section class="mt-8 rounded-xl border border-slate-200 bg-white p-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Banco barrial interno</p>
            <h3 class="mt-2 text-xl font-semibold">Ficha barrial reutilizable dentro del avalúo</h3>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Completa aquí la avanzada del barrio. Al guardar, esta ficha queda disponible para nuevos avalúos
                del mismo barrio, y este expediente conserva su propia copia fechada.
            </p>
        </div>
        <span class="rounded-full border px-3 py-1 text-sm font-semibold <?= e($levelClass) ?>">
            <?= e($level) ?> · <?= e((string) ($summary['ready'] ?? 0)) ?>/<?= e((string) count($advancedCatalog)) ?>
        </span>
    </div>
    <?php if (!empty($sectorMessage)): ?>
        <p class="mt-5 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm font-semibold leading-6 text-emerald-800">
            <?= e($sectorMessage) ?>
        </p>
    <?php endif; ?>
    <nav class="mt-5 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Secciones avanzadas del barrio">
        <?php foreach ($advancedCatalog as $sectionCode => [$sectionTitle]): ?>
            <?php $sectionNumber = '2.' . (int) $sectionCode; ?>
            <button type="button" class="min-h-12 shrink-0 rounded-lg px-4 py-2 text-left text-sm font-semibold"
                @click="activeBankSection = '<?= e((string) $sectionCode) ?>'; history.replaceState(null, '', '#banco-<?= e((string) $sectionCode) ?>')"
                :class="activeBankSection === '<?= e((string) $sectionCode) ?>' ? 'bg-teal-700 text-white shadow-sm' : 'bg-white text-teal-800 hover:bg-white/70'">
                <span class="block text-xs opacity-80"><?= e($sectionNumber) ?></span>
                <?= e((string) $sectionTitle) ?>
            </button>
        <?php endforeach; ?>
    </nav>
    <?php foreach ($advancedCatalog as $sectionCode => [$sectionTitle, $fields]): ?>
        <?php
        $row = $advancedRows[$sectionCode] ?? null;
        $bankRow = $bankRows[$sectionCode] ?? null;
        $storedValues = $decodeData($row) ?: $decodeData($bankRow);
        $sectionValues = \App\Services\AppraisalSectorAdvancedPrefill::merge(
            $advancedDefaults[(string) $sectionCode] ?? [], $storedValues);
        $sectionStatus = (string) (($row['status'] ?? null) ?: ($bankRow['status'] ?? 'Pendiente'));
        $sectionDate = $fmtAdvancedDate(($row['updated_at'] ?? null) ?: ($bankRow['updated_at'] ?? null));
        $sectionNumber = '2.' . (int) $sectionCode;
        ?>
        <section class="mt-5 rounded-xl border border-slate-200 p-5 scroll-mt-6"
            x-show="activeBankSection === '<?= e((string) $sectionCode) ?>'">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="eyebrow"><?= e($sectionNumber) ?></p>
                    <h4 class="mt-2 text-lg font-semibold"><?= e((string) $sectionTitle) ?></h4>
                </div>
                <p class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                    <?= e($sectionStatus) ?> · <?= e($sectionDate) ?>
                </p>
            </div>
            <div class="mt-5 grid gap-5 md:grid-cols-2">
                <?php foreach ($fields as $field): ?>
                    <?php
                    [$fieldName, $fieldLabel, $fieldType] = $field;
                    $optionKey = $field[3] ?? null;
                    require BASE_PATH . '/app/Views/appraisals/sector-advanced-field.php';
                    ?>
                <?php endforeach; ?>
            </div>
            <?php require BASE_PATH . '/app/Views/appraisals/sector-advanced-sources.php'; ?>
            <?php require BASE_PATH . '/app/Views/appraisals/sector-advanced-photos.php'; ?>
        </section>
    <?php endforeach; ?>
</section>
