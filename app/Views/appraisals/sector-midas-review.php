<?php
use App\Core\Session;
use App\Services\{AppraisalMidasReview, AppraisalSectorAdvancedPrefill};
use App\Support\AppraisalSectorAdvancedCatalog;

$showMidasReview = (bool) Session::pullFlash('sector_midas_review');
if ($showMidasReview):
    $decodeMidasRow = static function (?array $row): array {
        $data = json_decode((string) ($row['data_json'] ?? '{}'), true);
        return is_array($data) ? $data : [];
    };
    $midasCurrent = AppraisalSectorAdvancedPrefill::sections($subject ?? [], $sector ?? []);
    foreach (($sectorAdvancedRows ?? []) as $midasCode => $midasRow) {
        $midasCurrent[(string) $midasCode] = array_replace($midasCurrent[(string) $midasCode] ?? [], $decodeMidasRow($midasRow));
    }
    $midasSuggestions = AppraisalMidasReview::suggestions($subject ?? []);
    $midasDiagnostics = AppraisalMidasReview::diagnostics();
    $midasRows = AppraisalMidasReview::rows($midasCurrent, $midasSuggestions);
    $midasStats = AppraisalMidasReview::stats($midasSuggestions);
    $midasLabels = [];
    foreach (AppraisalSectorAdvancedCatalog::sections() as $midasCode => [$midasTitle, $midasFields]) {
        foreach ($midasFields as [$midasField, $midasLabel]) {
            $midasLabels[(string) $midasCode][$midasField] = $midasLabel;
        }
        $midasLabels[(string) $midasCode]['_title'] = $midasTitle;
    }
?>
<section id="midas-review" class="mt-8 rounded-2xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Revisión MIDAS</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-950">Datos encontrados para revisar</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-emerald-950">
                Esta revisión no borra información manual. Al aplicar, solo se llenan campos vacíos con datos encontrados;
                lo no leído queda pendiente y visible en gris para completar manualmente.
            </p>
        </div>
        <span class="rounded-full border border-emerald-300 bg-white px-3 py-1 text-sm font-semibold text-emerald-800">
            <?= e((string) $midasStats['fields']) ?> datos · <?= e((string) $midasStats['pending']) ?> pendientes
        </span>
    </div>
    <form class="mt-5 flex flex-wrap items-center justify-between gap-3" method="post"
        action="<?= e(url('avaluos/' . $record['id'] . '/sector/midas/aplicar')) ?>">
        <?= csrf_field() ?>
        <p class="text-sm font-semibold text-emerald-900">
            Recomendado: aplica sugerencias y luego ajusta redacción en cada pestaña.
        </p>
        <button class="btn-primary min-h-11" type="submit">Aplicar solo campos vacíos</button>
    </form>
    <?php if ($midasDiagnostics): ?>
        <details class="mt-4 rounded-xl border border-emerald-200 bg-white">
            <summary class="min-h-11 cursor-pointer px-4 py-3 text-sm font-semibold text-emerald-900">
                Ver diagnóstico de consulta por capas
            </summary>
            <ul class="space-y-2 border-t border-emerald-100 px-4 py-3 text-sm leading-6 text-slate-700">
                <?php foreach ($midasDiagnostics as $diagnostic): ?>
                    <li><?= e($diagnostic) ?></li>
                <?php endforeach; ?>
            </ul>
        </details>
    <?php endif; ?>
    <details class="mt-5 rounded-xl border border-emerald-200 bg-white">
        <summary class="flex min-h-11 cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 text-sm font-semibold text-emerald-900">
            <span>Ver detalle MIDAS encontrado</span>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs">
                <?= e((string) $midasStats['fields']) ?> datos sugeridos · <?= e((string) $midasStats['pending']) ?> pendientes
            </span>
        </summary>
        <div class="overflow-x-auto border-t border-emerald-100">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Pestaña</th>
                        <th class="px-4 py-3">Campo</th>
                        <th class="px-4 py-3">Dato MIDAS sugerido</th>
                        <th class="px-4 py-3">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($midasRows as $row): ?>
                        <?php $modeClass = $row['mode'] === 'Aplicable' ? 'text-emerald-800'
                            : ($row['mode'] === 'Pendiente' ? 'text-slate-500' : 'text-amber-700'); ?>
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800">
                                <?= e('2.' . (int) $row['code'] . ' ' . ($midasLabels[$row['code']]['_title'] ?? '')) ?>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?= e($midasLabels[$row['code']][$row['field']] ?? $row['field']) ?></td>
                            <td class="px-4 py-3 text-slate-700">
                                <?= e(is_array($row['value']) ? implode(', ', $row['value']) : (string) $row['value']) ?>
                            </td>
                            <td class="px-4 py-3 font-semibold <?= e($modeClass) ?>"><?= e($row['mode']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </details>
</section>
<?php endif; ?>
