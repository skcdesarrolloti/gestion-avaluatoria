<?php
$obsolescenceBlocks = [
    ['OBS-FIS', 'Obsolescencia física', 'Deterioro por edad, uso, estado de conservación, patologías, instalaciones y mantenimiento.'],
    ['OBS-FUN', 'Obsolescencia funcional', 'Limitaciones de distribución, dimensiones, flexibilidad, adecuación al uso, especialización o tecnología.'],
    ['OBS-EXT', 'Obsolescencia externa / económica', 'Efectos de mercado, entorno, accesibilidad, regulación, externalidades o pérdida de vocación.'],
];
$obsolescenceFactors = [
    'Física' => ['Estructura', 'Cubiertas, fachadas y cerramientos', 'Instalaciones y redes', 'Acabados', 'Conservación y mantenimiento'],
    'Funcional' => ['Distribución', 'Dimensiones', 'Flexibilidad', 'Adecuación al uso', 'Especialización', 'Componente tecnológico'],
    'Externa / económica' => ['Mercado o demanda', 'Entorno', 'Accesibilidad', 'Ambiental', 'Urbanístico / regulatorio', 'Vocación del sector'],
];
$scoreRows = [['0', 'No evidenciada'], ['1', 'Baja'], ['2', 'Media'], ['3', 'Alta'], ['N/A', 'No aplica']];
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">3.6 Obsolescencias</p>
            <h2 class="mt-2 text-2xl font-semibold">Diagnóstico técnico de obsolescencias</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Espacio reservado para diagnosticar obsolescencia física, funcional y externa/económica. El índice IEO será un indicador de evidencia; no será un porcentaje automático de depreciación ni de descuento.
            </p>
        </div>
        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Estructura inicial</span>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-3">
        <?php foreach ($obsolescenceBlocks as [$code, $title, $description]): ?>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-bold text-teal-800"><?= e($code) ?></p>
                <h3 class="mt-1 font-semibold text-slate-900"><?= e($title) ?></h3>
                <p class="mt-2 text-sm leading-6 text-slate-600"><?= e($description) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
        <strong>Regla metodológica:</strong> una calificación media o alta debe tener evidencia mediante fotografía, documento, dato de mercado, comparación, norma, cálculo o comentario técnico. El IEO solo orienta el diagnóstico; la cuantificación económica se analiza aparte.
    </div>
    <div class="mt-5 grid gap-5 xl:grid-cols-[1fr_.55fr]">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <h3 class="font-semibold text-slate-900">Matriz base para estructurar</h3>
            <div class="mt-3 overflow-x-auto">
                <table class="w-full min-w-[44rem] text-left text-sm">
                    <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Tipo</th><th class="py-2">Factores a revisar</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($obsolescenceFactors as $type => $factors): ?>
                            <tr><td class="py-2 pr-3 font-semibold text-slate-800"><?= e($type) ?></td><td class="py-2 text-slate-700"><?= e(implode(', ', $factors)) ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <h3 class="font-semibold text-slate-900">Escala IEO</h3>
            <div class="mt-3 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Puntaje</th><th class="py-2">Lectura</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($scoreRows as [$score, $label]): ?>
                            <tr><td class="py-2 pr-3 font-bold text-slate-800"><?= e($score) ?></td><td class="py-2 text-slate-700"><?= e($label) ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
