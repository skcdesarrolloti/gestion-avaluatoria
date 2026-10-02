<?php use App\Services\MethodologyWorkflow as Workflow; ?>
<section id="elegir-componente" class="mt-6 scroll-mt-6 rounded-xl border border-teal-200 bg-teal-50 p-5" aria-label="Siguiente paso">
<?php if (in_array($stage, ['components', '1'], true) && $componentKey !== ''): ?>
    <p class="font-semibold"><?= e($componentLabel) ?> · Después de revisar la academia</p>
    <?php $nextUnitStep = empty($selected['method']) ? '2' : '3'; ?>
    <a class="btn-primary mt-3" href="<?= e($flowUrl($nextUnitStep)) ?>">Siguiente: <?= empty($selected['method']) ? 'definir método y alcance' : 'insumos del método registrado' ?> →</a>
<?php elseif ($stage === 'decision' || ($componentKey === '' && !in_array($stage, ['integration','report'], true))): ?>
    <p class="font-semibold"><?= $stage === '4' ? 'D · Elige el inmueble cuyas muestras vas a analizar.' : 'C · Elige el inmueble para continuar con sus insumos y comparables.' ?></p>
    <div class="mt-3 flex flex-wrap gap-3"><?php foreach ($components as $key => $component): ?>
        <?php $hasMethod = !empty($flow[$key]['method']); $targetStage = $hasMethod ? ($stage === '4' ? '4' : '3') : '2'; ?>
        <a class="btn-primary" href="<?= e($flowUrl($targetStage, ($flow[$key]['method'] ?? '') ?: 'mercado', $key)) ?>"><?= e($component['label']) ?> → <?= !$hasMethod ? 'Definir método en B' : ($targetStage === '4' ? 'Análisis' : 'Insumos y comparables') ?></a>
    <?php endforeach; ?></div>
<?php elseif ($stage === 'integration'): ?>
    <p>Revisa la cobertura y las conclusiones de todos los componentes antes de continuar.</p>
    <a class="btn-primary mt-3" href="<?= e($flowUrl('report')) ?>">Siguiente: revisar texto del numeral 8 →</a>
<?php elseif ($stage === 'report'): ?>
    <a class="btn-secondary" href="<?= e($flowUrl('integration')) ?>">← Volver a integración</a>
<?php else: ?>
    <p class="font-semibold"><?= e($componentLabel) ?> · <?= e($prefix . $stage . ' ' . Workflow::STAGES[$stage]) ?></p>
    <details class="mt-2"><summary class="min-h-11 cursor-pointer text-teal-800" title="Ayuda del paso actual">? ¿Qué hago aquí y qué sigue?</summary>
        <p class="text-sm"><?= e(match ($stage) {
            '1' => 'Consulta la academia del método. Después documenta la selección y su justificación para este componente.',
            '2' => 'Selecciona y justifica el método. Usa el botón del formulario para continuar con el método elegido; espera la confirmación del guardado.',
            '3' => 'Completa las muestras, su verificación y sus soportes para este componente. Después pasa al análisis.',
            '4' => 'Documenta el análisis y sus limitaciones. Después redacta la conclusión para el lector del informe.',
            default => 'Revisa la conclusión de este componente. Continúa con otro inmueble o integra los resultados.'
        }) ?></p>
    </details>
    <div class="mt-3 flex flex-wrap gap-3">
        <?php if ((int) $stage > 1): ?><a class="btn-secondary" href="<?= e($flowUrl((string) ((int) $stage - 1))) ?>">← <?= e($prefix . ((int) $stage - 1)) ?> Anterior</a><?php endif; ?>
        <?php if ($stage !== '2' && (int) $stage < 5): ?><a class="btn-primary" href="<?= e($flowUrl((string) ((int) $stage + 1))) ?>">Siguiente: <?= e($prefix . ((int) $stage + 1) . ' ' . Workflow::STAGES[(int) $stage + 1]) ?> →</a><?php endif; ?>
        <?php if ($stage === '5'): ?><a class="btn-secondary" href="<?= e($flowUrl('components')) ?>">Analizar otro inmueble</a><a class="btn-primary" href="<?= e($flowUrl('integration')) ?>">Siguiente: E · Integración →</a><?php endif; ?>
    </div>
<?php endif; ?>
</section>
