<section class="mb-4 rounded-xl border border-teal-700 bg-white p-4">
    <h2 class="mb-3 text-xl font-semibold">Análisis de mercado · 1. Preparar la muestra</h2>
    <nav class="grid gap-2 sm:grid-cols-2 lg:grid-cols-4" aria-label="Etapas de M4 Análisis de mercado">
        <a class="btn-secondary bg-teal-50 ring-2 ring-teal-700" aria-current="page" href="<?= e($flowUrl('3')) ?>">1. Preparar la muestra</a>
        <?php foreach (['statistics'=>'2. Entender la muestra','regression'=>'3. Construir el modelo','diagnostics'=>'4. Revisar el modelo','validation'=>'5. Validar las predicciones','subject'=>'6. Aplicar al sujeto','memory'=>'7. Memoria y sustentación'] as $panel=>$label): ?>
        <a class="btn-secondary" href="<?= e($flowUrl('4').'&panel='.$panel) ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </nav>
    <nav class="mt-3 flex flex-wrap gap-2 border-t pt-3" aria-label="Preparar la muestra">
        <a class="btn-secondary" href="<?= e($flowUrl('4').'&panel=preparation') ?>">Configuración del estudio</a>
        <a class="btn-primary" aria-current="page" href="<?= e($flowUrl('3')) ?>">Traer muestras de portales</a>
        <a class="btn-secondary" href="<?= e($flowUrl('4')) ?>">Verificación de las muestras</a>
        <a class="btn-secondary" href="<?= e($flowUrl('4').'&panel=location') ?>">Ubicación y mapa</a>
    </nav>
    <p class="mt-3 text-sm">Recoge los anuncios por portal. Los inmuebles existentes siguen disponibles en Verificación de las muestras.</p>
</section>
<details class="mb-4 rounded-xl border bg-white p-4"><summary class="min-h-11 cursor-pointer font-semibold">Academia de captura · fuentes, comparabilidad y soportes</summary>
    <?php require __DIR__.'/valuation-methodology-method-guides.php'; ?>
</details>
