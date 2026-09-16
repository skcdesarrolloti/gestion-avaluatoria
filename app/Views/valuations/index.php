<section class="space-y-8">
    <div class="flex flex-wrap items-start justify-between gap-6">
        <div class="max-w-3xl">
            <p class="eyebrow">Módulo valuatorio</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Valuaciones</h1>
            <p class="mt-3 text-base leading-6 text-slate-600">
                Punto de entrada para construir el expediente valuatorio desde las fotos del inmueble,
                la preclasificación IGAC, la configuración, los métodos y los soportes normativos.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="btn-secondary" href="<?= e(url('#expediente-valuatorio')) ?>">Qué incluye el expediente</a>
            <form method="post" action="<?= e(url('avaluos')) ?>" x-data="{ busy: false }" @submit="busy = true">
                <?= csrf_field() ?>
                <button class="btn-primary" type="submit" :disabled="busy"
                    x-text="busy ? 'Creando...' : 'Crear ficha y abrir expediente'">Crear ficha y abrir expediente</button>
            </form>
        </div>
    </div>

    <div id="expediente-valuatorio" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Numeral 1</p>
                <h2 class="mt-2 text-2xl font-semibold">Expediente valuatorio</h2>
            </div>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">Inicial activo</span>
        </div>
        <p class="mt-4 max-w-3xl text-sm leading-6 text-slate-600">
            Aquí se define el encargo y su identificación formal: nombre del avalúo, cliente,
            solicitante, destinatario, finalidad, uso previsto, fechas, alcance, limitaciones,
            hipótesis y configuración técnica que orienta el informe.
        </p>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <article class="rounded-xl border border-slate-200 p-5">
                <h3 class="text-lg font-semibold">Consecutivo técnico</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Formato previsto: <strong>PP-NNN-AAAA-MET</strong>. El código del perito,
                    su consecutivo anual, el año y el método principal permiten identificar
                    quién responde por el avalúo.
                </p>
                <p class="mt-3 text-sm font-medium text-teal-800">Ejemplo: 01-001-2026-M</p>
            </article>
            <article class="rounded-xl border border-slate-200 p-5">
                <h3 class="text-lg font-semibold">Métodos por componente</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Un mismo expediente podrá tener terreno por mercado, construcción por
                    reposición, renta como contraste o residual cuando aplique.
                </p>
                <p class="mt-3 text-sm font-medium text-teal-800">M · RE · REP · R</p>
            </article>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-4">
        <?php
        $items = [
            ['title' => 'Fotos del inmueble', 'text' => 'La evidencia visual abre el análisis y ayuda a identificar qué se va a valorar.'],
            ['title' => 'Definiciones guiadas', 'text' => 'Cada lista desplegable mostrará qué es, cuándo aplica y su soporte normativo.'],
            ['title' => 'Preclasificación IGAC', 'text' => 'Las fotos se contrastarán con el catálogo IGAC y sus imágenes de referencia.'],
            ['title' => 'Numerales del informe', 'text' => 'El Bien sujeto tomará lo configurado sin duplicar datos del encargo.'],
        ];
        foreach ($items as $item): ?>
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-base font-semibold"><?= e($item['title']) ?></h3>
                <p class="mt-2 text-sm leading-6 text-slate-600"><?= e($item['text']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="flex flex-wrap gap-3">
        <form method="post" action="<?= e(url('avaluos')) ?>" x-data="{ busy: false }" @submit="busy = true">
            <?= csrf_field() ?>
            <button class="btn-primary" type="submit" :disabled="busy"
                x-text="busy ? 'Creando...' : 'Crear ficha y abrir expediente'">Crear ficha y abrir expediente</button>
        </form>
        <a class="btn-secondary" href="<?= e(url()) ?>">Ver mis avalúos</a>
        <a class="btn-secondary" href="<?= e(url('maestros')) ?>">Creación de Maestros</a>
        <a class="btn-secondary" href="<?= e(url('normas-tecnicas-sectoriales')) ?>">Consultar NTS</a>
        <a class="btn-secondary" href="<?= e(url('marco-juridico-valuatorio')) ?>">Consultar marco jurídico</a>
    </div>
</section>
