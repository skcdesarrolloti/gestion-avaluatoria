<section class="space-y-4" aria-label="Configuración del estudio">
    <h3 class="text-xl font-semibold">Configuración del estudio</h3>
    <p class="text-sm">Selecciona los atributos que revisarás. Se habilitan en la misma tabla de Verificación de las muestras; desmarcarlos conserva sus datos.</p>
    <?php require __DIR__.'/methodology-study-basis.php'; ?>
    <?php require __DIR__.'/methodology-regression-factor-selection.php'; ?>
    <button type="button" class="btn-primary" @click="analysisModule='samples';if(analysisRegimeApplied)analysisView='result'">Verificar las muestras</button>
    <details class="rounded-xl border p-3">
        <summary class="min-h-11 cursor-pointer font-semibold">Academia y encargo · alcance de la preparación</summary>
        <?php require __DIR__.'/methodology-analysis-preparation.php'; ?>
    </details>
</section>
