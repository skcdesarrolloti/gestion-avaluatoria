<section class="space-y-8">
    <div class="flex flex-wrap items-start justify-between gap-6">
        <div class="max-w-3xl">
            <p class="eyebrow">Administración base</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Creación de Maestros</h1>
            <p class="mt-3 text-base leading-6 text-slate-600">
                Este espacio concentrará los catálogos que alimentan Valuaciones sin mezclarlos
                con el expediente. Primero peritos y ciudades; luego agregamos los demás maestros.
            </p>
        </div>
        <a class="btn-primary" href="<?= e(url('valuaciones')) ?>">Volver a Valuaciones</a>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <?php
        $masters = [
            [
                'title' => 'Peritos',
                'status' => 'Prioridad 1',
                'text' => 'Código interno, nombre, correo, RAA, categorías autorizadas, firma, estado y secuencia anual.',
            ],
            [
                'title' => 'Ciudades',
                'status' => 'Prioridad 2',
                'text' => 'Municipio, departamento, códigos oficiales y datos útiles para ubicar el expediente valuatorio.',
            ],
            [
                'title' => 'Catálogos futuros',
                'status' => 'Abierto',
                'text' => 'Entidades, finalidades, zonas, fuentes, anexos u otros maestros que el flujo vaya necesitando.',
            ],
        ];
        foreach ($masters as $master): ?>
            <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="text-lg font-semibold"><?= e($master['title']) ?></h2>
                    <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        <?= e($master['status']) ?>
                    </span>
                </div>
                <p class="mt-4 text-sm leading-6 text-slate-600"><?= e($master['text']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="eyebrow">Criterio de diseño</p>
        <h2 class="mt-2 text-2xl font-semibold">Los maestros no pertenecen al Capítulo 0</h2>
        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
            El Capítulo 0 usará estos datos para seleccionar responsables, ciudades y catálogos,
            pero no los creará allí. Así el expediente queda limpio y los datos base se mantienen
            una sola vez para todo el sistema.
        </p>
    </section>
</section>
