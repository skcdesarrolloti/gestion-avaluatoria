<?php
$summary = is_array($sectorBankSummary ?? null) ? $sectorBankSummary : [];
$sections = is_array($sectorBankSections ?? null) ? $sectorBankSections : [];
$level = (string) ($summary['level'] ?? 'ROJO');
$levelClass = $level === 'VERDE' ? 'bg-emerald-50 text-emerald-800 border-emerald-200'
    : ($level === 'AMARILLO' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-red-50 text-red-800 border-red-200');
$fmtDate = static function ($value): string {
    if (empty($value)) return 'Pendiente';
    try {
        return (new DateTimeImmutable((string) $value, new DateTimeZone('UTC')))
            ->setTimezone(new DateTimeZone('America/Bogota'))->format('d/m/Y H:i');
    } catch (Throwable) {
        return (string) $value;
    }
};
?>
<section class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Arquitectura del banco barrial</p>
            <h3 class="mt-2 text-xl font-semibold">Ficha sectorial por secciones</h3>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Esta capa conserva la avanzada de InversKC: fuentes, trazabilidad, validación de campo
                y copia congelada para el avalúo.
            </p>
        </div>
        <span class="rounded-full border px-3 py-1 text-sm font-semibold <?= e($levelClass) ?>">
            <?= e($level) ?> · <?= e((string) ($summary['ready'] ?? 0)) ?>/<?= e((string) ($summary['total'] ?? 0)) ?>
        </span>
    </div>
    <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
        <?php foreach ($sections as $row): ?>
            <article class="rounded-lg border border-slate-200 bg-white p-4">
                <div class="flex items-start gap-3">
                    <span class="rounded-full bg-blue-700 px-2 py-1 text-xs font-bold text-white">
                        <?= e((string) ($row['section_code'] ?? '')) ?>
                    </span>
                    <div>
                        <h4 class="font-semibold text-slate-900"><?= e((string) ($row['section_title'] ?? '')) ?></h4>
                        <p class="mt-1 text-xs leading-5 text-slate-600">
                            Fuente: <?= e((string) ($row['source_name'] ?? 'Pendiente')) ?><br>
                            Actualización: <?= e($fmtDate($row['updated_at'] ?? null)) ?>
                        </p>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if ($sections === []): ?>
            <p class="rounded-lg border border-dashed border-slate-300 bg-white p-4 text-sm text-slate-600">
                Selecciona un barrio para crear la ficha sectorial por secciones.
            </p>
        <?php endif; ?>
    </div>
</section>
