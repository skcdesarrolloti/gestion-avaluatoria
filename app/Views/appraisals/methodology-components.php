<?php
$unassignedCount = count(\App\Services\MethodologyComparableScope::rows($allComparableRows, ''));
$treatments = ['separado' => 'Valor separado', 'integrado' => 'Incluido en otro componente', 'descriptivo' => 'Solo descriptivo'];
$priorComponents = (new \App\Services\AppraisalMethodologyComponentPlanner())->components($record, $units);
$priorById = array_column($priorComponents, null, 'id');
?>
<section class="mt-6 rounded-2xl border bg-white p-5 sm:p-8">
    <h2 class="text-2xl font-semibold"><?= $stage === 'integration' ? 'Integración y control de cobertura' : 'Inmuebles y anexos del expediente' ?></h2>
    <p class="mt-3 text-slate-600">Cada unidad o anexo conserva su identidad. El terreno separado es opcional: úsalo solo si corresponde y documenta qué incluye cada valor para evitar duplicaciones.</p>
    <div class="mt-4 flex flex-wrap gap-3">
        <a class="btn-secondary" href="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto')) ?>">Revisar componentes del predio</a>
        <a class="btn-secondary" href="<?= e($flowUrl('3', 'mercado', '')) ?>">Banco sin asignar (<?= $unassignedCount ?>)</a>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-2">
    <?php foreach ($components as $key => $component):
        $item = $flow[$key] ?? [];
        $samples = \App\Services\MethodologyComparableScope::rows($allComparableRows, $key);
        $prior = $priorById[$key] ?? [];
    ?>
        <article class="rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-600"><?= $key === 'terreno' ? 'Opción adicional' : (($component['unit']['unit_kind'] ?? '') === 'annex' ? 'Anexo' : 'Inmueble') ?></p>
            <h3 class="text-lg font-semibold"><?= e($component['label']) ?></h3>
            <?php if ($prior !== []): ?>
                <p class="mt-2 text-sm"><?= e($prior['type_label'] ?? '') ?></p>
                <p class="mt-2 text-sm">Ruta según datos del expediente: <?= e($prior['method']) ?></p>
                <p class="mt-2 text-sm"><?= e($prior['note']) ?></p>
            <?php endif; ?>
            <p class="mt-2">Método: <strong><?= e($methods[$item['method'] ?? ''] ?? 'Por seleccionar') ?></strong></p>
            <p class="mt-2 text-sm"><?= e($treatments[$item['treatment'] ?? ''] ?? 'Tratamiento pendiente') ?> · <?= count($samples) ?> muestras asignadas</p>
            <p class="mt-2 whitespace-pre-wrap text-sm"><?= e($item['coverage'] ?? 'Falta definir el alcance y los elementos incluidos.') ?></p>
            <?php if ($stage === 'integration'): ?>
                <p class="mt-3 whitespace-pre-wrap text-sm"><?= e(($item['method'] ?? '') === 'mercado' ? ($item['conclusion'] ?? 'Conclusión pendiente.') : 'Desarrollo del método pendiente; no hay valor adoptado automáticamente.') ?></p>
                <?php if (($item['evidence_hash'] ?? '') !== \App\Services\MethodologyWorkflow::fingerprint($samples) && !empty($item['conclusion'])): ?>
                <p class="mt-2 font-semibold text-amber-800">Las muestras cambiaron: revisa la conclusión.</p>
                <?php endif; ?>
            <?php endif; ?>
            <a class="btn-primary mt-4" href="<?= e($flowUrl('2', ($item['method'] ?? '') ?: 'mercado', $key)) ?>">Seleccionar método y continuar</a>
            <div class="mt-3 flex flex-wrap gap-2" aria-label="Etapas de <?= e($component['label']) ?>">
            <?php foreach (\App\Services\MethodologyWorkflow::STAGES as $number => $label): ?>
                <a class="btn-secondary" href="<?= e($flowUrl((string) $number, ($item['method'] ?? '') ?: 'mercado', $key)) ?>"><?= e(\App\Services\MethodologyWorkflow::PREFIXES[($item['method'] ?? '') ?: 'mercado'] . $number . ' ' . $label) ?></a>
            <?php endforeach; ?>
            </div>
        </article>
    <?php endforeach; ?>
    </div>
    <?php if ($stage === 'integration'): ?>
        <p class="mt-5 rounded-xl bg-amber-50 p-4">La integración conserva las conclusiones por componente. No presenta un total automático: los valores de Costo, Renta y Residual todavía están pendientes de desarrollo.</p>
    <?php endif; ?>
</section>
