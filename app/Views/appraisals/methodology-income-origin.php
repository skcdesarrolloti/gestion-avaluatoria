<div class="space-y-4">
    <h3 class="text-xl font-semibold">El inmueble que estamos estudiando</h3>
    <p>Consulta los datos del capítulo 3 por unidad. Conservan su origen y fecha; estar registrados no significa que estén verificados.</p>
    <?php $incomeOriginRows=$incomeSubject['rows']; require __DIR__.'/methodology-income-origin-rows.php'; ?>
    <p class="rounded-lg bg-teal-50 p-3 text-sm">El área privada o construida no equivale automáticamente al área arrendable. El número de unidades rentables tampoco identifica sus contratos.</p>
    <details class="rounded-lg border px-4">
        <summary class="min-h-11 cursor-pointer py-3 font-semibold">Consultar características y soportes del sujeto</summary>
        <?php $incomeOriginRows=$incomeSubject['physical']; require __DIR__.'/methodology-income-origin-rows.php'; ?>
        <p class="mt-3 text-sm">Las características y calificaciones no se convierten automáticamente en descuentos, vacancia o tasas.</p>
        <div class="my-3 flex flex-wrap gap-3"><?php foreach ($incomeSubject['references'] as $reference): ?>
            <a class="inline-flex min-h-11 items-center text-sm text-blue-800 underline" href="<?= e(url($reference['url'])) ?>"><?= e($reference['label']) ?></a>
        <?php endforeach; ?></div>
    </details>
    <details class="rounded-lg border px-4">
        <summary class="min-h-11 cursor-pointer py-3 font-semibold">Consultar encargo y contexto general del expediente</summary>
        <p class="text-sm">Estos antecedentes corresponden al expediente. Si existen varias unidades o partes, comprueba su alcance antes de atribuirlos a este recorrido.</p>
        <?php $incomeOriginRows=$incomeSubject['context']; require __DIR__.'/methodology-income-origin-rows.php'; ?>
    </details>
</div>
