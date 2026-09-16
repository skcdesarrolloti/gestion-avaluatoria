<?php
$currentStep = 'sector';
$sectorTabs = [
    'localizacion' => ['2.1', 'Localización y delimitación', 'Ubicación, área de influencia y referencia urbana del sector.'],
    'entorno' => ['2.2', 'Caracterización del entorno', 'Usos predominantes, consolidación, centralidad y vocación.'],
    'infraestructura' => ['2.3', 'Infraestructura y movilidad', 'Vías, accesibilidad, transporte, servicios y conectividad.'],
    'equipamientos' => ['2.4', 'Equipamientos y actividad', 'Dotacionales, comercio, servicios cercanos y dinámica cotidiana.'],
    'mercado' => ['2.5', 'Dinámica de mercado', 'Oferta, demanda, tendencias y señales observables del sector.'],
];
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Numeral 2 · Sector y entorno</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Informe del sector</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Este numeral queda separado del Bien sujeto para documentar el contexto urbano, económico y de mercado
            antes de caracterizar el inmueble. La estructura se homologará con InversKC punto por punto.
        </p>
    </div>
    <span class="rounded-full bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-800">En revisión</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>

<section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{ activeSector: 'localizacion' }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Estructura del numeral 2</p>
            <h2 class="mt-2 text-2xl font-semibold">Caracterización del sector y entorno</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Lo dejamos como capítulo propio para que las variables del sector no se mezclen con la ficha del
                inmueble. Todavía no se persisten campos nuevos hasta confirmar la matriz definitiva.
            </p>
        </div>
    </div>
    <nav class="mt-6 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Subsecciones de sector">
        <?php foreach ($sectorTabs as $key => [$number, $label]): ?>
            <button type="button" class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-left text-sm font-semibold"
                @click="activeSector = '<?= e($key) ?>'"
                :class="activeSector === '<?= e($key) ?>' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800'">
                <span class="block text-xs opacity-80"><?= e($number) ?></span>
                <?= e($label) ?>
            </button>
        <?php endforeach; ?>
    </nav>
    <?php foreach ($sectorTabs as $key => [$number, $label, $description]): ?>
        <div class="mt-6 rounded-xl border border-slate-200 p-5" x-show="activeSector === '<?= e($key) ?>'">
            <p class="eyebrow"><?= e($number) ?></p>
            <h3 class="mt-2 text-xl font-semibold"><?= e($label) ?></h3>
            <p class="mt-2 text-sm leading-6 text-slate-600"><?= e($description) ?></p>
            <div class="mt-5 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm leading-6 text-slate-600">
                Pendiente de definir campos, ayudas y listas desplegables con base en la pantalla equivalente de
                InversKC. Este espacio queda listo para construir el numeral 2 sin invadir Bien sujeto.
            </div>
        </div>
    <?php endforeach; ?>
</section>
