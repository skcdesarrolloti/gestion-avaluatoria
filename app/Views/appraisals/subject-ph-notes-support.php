<?php
$notesShort = static function (string $value): string {
    $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    return mb_strlen($value) > 150 ? mb_substr($value, 0, 147) . '…' : $value;
};
$notesPill = static function (string $state): string {
    return match ($state) {
        'ok' => '<button type="button" disabled class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Completo</button>',
        'warn' => '<button type="button" disabled class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Revisar</button>',
        default => '<button type="button" disabled class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Falta</button>',
    };
};
$notesMetric = static function (string $groupKey, array $items) use ($phMap): array {
    $ok = $warn = $risk = 0;
    foreach ($items as $key => $_) {
        $status = trim($phMap($groupKey, (string) $key, 'status'));
        if ($status === 'ok') $ok++;
        if ($status === 'warn') $warn++;
        if ($status === 'risk') $risk++;
    }
    $total = count($items); $state = $risk > 0 ? 'warn' : ($ok + $warn > 0 ? 'ok' : 'warn');
    return ["{$ok} verificados, {$warn} por confirmar, {$risk} alertas de {$total}", $state];
};
[$riskValue, $riskState] = $notesMetric('risks', $phCatalog['risks']);
[$docValue, $docState] = $notesMetric('documents', $phCatalog['documents']);
[$photoValue, $photoState] = $notesMetric('photos', $phCatalog['photos']);
$normText = $technicalValue('notas_normativas_ph') ?: implode("\n", $phCatalog['normNotes']);
$noteRows = [
    ['Texto editable para Entregable', $technicalValue('resumen_notas_ph') !== '' ? 'Texto construido' : 'Sin texto construido', $technicalValue('resumen_notas_ph') !== '' ? 'ok' : 'missing', 'Construir la nota normativa y de alcance antes de cerrar.'],
    ['Marco normativo PH', $notesShort($normText) ?: 'Sin notas normativas', $normText !== '' ? 'ok' : 'missing', 'Registrar solo normas pertinentes: PH, coeficientes, expensas y alcance del avalúo.'],
    ['Salvedades del reglamento', $notesShort($technicalValue('salvedades_reglamento')) ?: 'Sin salvedad del reglamento', $technicalValue('salvedades_reglamento') !== '' ? 'ok' : 'warn', 'Precisar qué proviene del reglamento y qué requiere confirmación.'],
    ['Salvedades de visita', $notesShort($technicalValue('salvedades_visita')) ?: 'Sin salvedad de visita', $technicalValue('salvedades_visita') !== '' ? 'ok' : 'warn', 'Indicar lo que debe validarse físicamente: estado, uso, administración o bienes comunes.'],
    ['Salvedades de validación documental', $notesShort($technicalValue('salvedades_validacion')) ?: 'Sin salvedad documental', $technicalValue('salvedades_validacion') !== '' ? 'ok' : 'warn', 'Listar documentos pendientes: CTL, paz y salvo, pólizas, certificado de administración, planos o cuotas.'],
    ['Observaciones de lectura OCR', $notesShort($technicalValue('observaciones_extraccion')) ?: 'Sin observaciones de lectura', $technicalValue('observaciones_extraccion') !== '' ? 'ok' : 'warn', 'Mantener advertencias de baja lectura o revisión contra original.'],
    ['Riesgos y afectaciones PH', $riskValue, $riskState, 'Revisar alertas y salvedades antes de cerrar.'],
    ['Soportes documentales PH', $docValue, $docState, 'Confirmar soportes vigentes para administración, expensas, pólizas y reglamento.'],
    ['Fotos requeridas para 3.6', $photoValue, $photoState, 'Completar evidencias de visita cuando el informe lo requiera.'],
];
?>
<div class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6">
    <h4 class="font-semibold text-slate-900">Campos de notas normativas y salvedades para construir el Entregable</h4>
    <p class="mt-1 text-slate-600">Esta pestaña cierra el alcance: normas pertinentes, salvedades, documentos pendientes, fotografías y límites del análisis técnico.</p>
    <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[56rem] text-left text-sm">
            <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Campo</th><th class="py-2 pr-3">Valor detectado</th><th class="py-2 pr-3">Estado</th><th class="py-2">Qué falta</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($noteRows as [$field, $value, $state, $missing]): ?>
                    <tr><td class="py-2 pr-3 font-semibold text-slate-800"><?= e($field) ?></td><td class="py-2 pr-3 text-slate-700"><?= e($value) ?></td><td class="py-2 pr-3"><?= $notesPill($state) ?></td><td class="py-2 text-slate-600"><?= e($state === 'ok' ? 'Listo para texto.' : $missing) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="rounded-xl border border-slate-200 bg-white p-4">
    <h4 class="font-semibold text-slate-900">Detalle editable de notas normativas y salvedades</h4>
    <div class="mt-3 grid gap-4">
        <label class="label">Notas normativas aplicables
            <textarea class="input mt-2 min-h-28" rows="4" name="ph[technical][notas_normativas_ph]" placeholder="Normas pertinentes y alcance técnico aplicable al análisis PH"><?= e($normText) ?></textarea>
            <span class="mt-1 block text-xs font-normal text-slate-500">No es para volcar leyes completas; solo referencias normativas que soportan el alcance y las salvedades del avalúo.</span>
        </label>
        <?php $renderTechTextarea('salvedades_reglamento', 'Salvedades del reglamento', $technicalValue('salvedades_reglamento')); ?>
        <?php $renderTechTextarea('salvedades_visita', 'Salvedades de visita', $technicalValue('salvedades_visita')); ?>
        <?php $renderTechTextarea('salvedades_validacion', 'Salvedades de validación documental', $technicalValue('salvedades_validacion')); ?>
        <?php $renderTechTextarea('observaciones_extraccion', 'Observaciones de lectura y OCR', $technicalValue('observaciones_extraccion')); ?>
    </div>
</div>
<?php foreach ([
    'riesgos' => ['risks', 'Riesgos, restricciones y afectaciones PH', $phCatalog['risks']],
    'documentos' => ['documents', 'Soportes documentales PH', $phCatalog['documents']],
    'fotos' => ['photos', 'Fotos requeridas para 3.6', $phCatalog['photos']],
] as $tabKey => [$groupKey, $title, $items]): ?>
    <section class="rounded-xl border border-slate-200 bg-white p-4">
        <h4 class="font-semibold text-slate-900"><?= e($title) ?></h4>
        <div class="mt-3 grid gap-3 xl:grid-cols-2">
            <?php foreach ($items as $key => $label): ?>
                <?php $current = $phMap($groupKey, (string) $key, 'status'); ?>
                <div class="rounded-xl border p-3 <?= e($statusClass($current)) ?>">
                    <label class="label text-sm"><?= e($label) ?>
                        <select class="input mt-2" name="ph[<?= e($groupKey) ?>][<?= e($key) ?>][status]">
                            <?php foreach ($phCatalog['status'] as $value => $option): ?>
                                <option value="<?= e($value) ?>" <?= $current === (string) $value ? 'selected' : '' ?>><?= e($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="label mt-2">Evidencia y observación
                    <textarea class="input mt-2 min-h-20" rows="2" name="ph[<?= e($groupKey) ?>][<?= e($key) ?>][notes]" placeholder="Observación del analista"><?= e($phMap($groupKey, (string) $key, 'notes')) ?></textarea></label>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>
