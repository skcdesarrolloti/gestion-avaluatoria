<section class="mb-4 rounded-xl border border-teal-700 bg-white p-4">
    <h2 class="mb-3 text-xl font-semibold">Análisis de mercado · 1. Preparar la muestra</h2>
    <nav class="grid items-start gap-2 sm:grid-cols-2 lg:grid-cols-4" aria-label="Etapas de M4 Análisis de mercado">
        <div>
            <a class="btn-secondary w-full bg-teal-50 text-left ring-2 ring-teal-700" aria-current="page" href="<?= e($flowUrl('3')) ?>">1. Preparar la muestra</a>
            <?php $capturePreparationMenu=true; require __DIR__.'/methodology-analysis-preparation-nav.php'; ?>
        </div>
        <?php foreach (['statistics'=>'2. Entender la muestra','regression'=>'3. Construir el modelo','diagnostics'=>'4. Revisar el modelo','validation'=>'5. Validar las predicciones','subject'=>'6. Aplicar al sujeto','memory'=>'7. Memoria y sustentación'] as $panel=>$label): ?>
        <a class="btn-secondary" href="<?= e($flowUrl('4').'&panel='.$panel) ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </nav>
    <p class="mt-3 text-sm">Recoge los anuncios por portal. Los inmuebles existentes siguen disponibles en Verificación de las muestras.</p>
</section>
<details class="mb-4 rounded-xl border bg-white p-4"><summary class="min-h-11 cursor-pointer font-semibold">Academia de captura · fuentes, comparabilidad y soportes</summary>
    <?php require __DIR__.'/valuation-methodology-method-guides.php'; ?>
</details>
