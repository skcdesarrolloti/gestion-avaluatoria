<?php use App\Services\MethodologyWorkflow as Workflow; ?>
<section id="elegir-componente" class="mt-6 scroll-mt-6 rounded-xl border border-teal-200 bg-teal-50 p-5" aria-label="Siguiente paso">
<?php if ($stage === 'components'): ?>
    <p class="font-semibold">A · Comprueba que aparecen los inmuebles y anexos estudiados.</p>
    <a class="btn-primary mt-3" href="<?= e($flowUrl('decision')) ?>">Siguiente: B · Matriz y método →</a>
<?php elseif ($stage === 'decision' || ($componentKey === '' && !in_array($stage, ['integration','report'], true))): ?>
    <p class="font-semibold">Siguiente: C · Elige el inmueble o anexo que vas a analizar.</p>
    <div class="mt-3 flex flex-wrap gap-3"><?php foreach ($components as $key => $component): ?>
        <a class="btn-primary" href="<?= e($flowUrl('1', ($flow[$key]['method'] ?? '') ?: 'mercado', $key)) ?>">Analizar <?= e($component['label']) ?> → Academia</a>
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
        <?php if ($stage === '5'): ?><a class="btn-secondary" href="<?= e($flowUrl('components')) ?>">Analizar otro inmueble</a><a class="btn-primary" href="<?= e($flowUrl('integration')) ?>">Siguiente: D · Integración →</a><?php endif; ?>
    </div>
<?php endif; ?>
</section>
