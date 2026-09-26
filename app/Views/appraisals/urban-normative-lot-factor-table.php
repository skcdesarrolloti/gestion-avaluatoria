<div class="overflow-x-auto rounded-xl border border-amber-200 bg-white">
    <table class="min-w-full divide-y divide-amber-100 text-sm">
        <thead class="bg-amber-50 text-left text-xs uppercase text-amber-800">
            <tr><th class="px-3 py-2">Factor de lote</th><th class="px-3 py-2">Dato</th><th class="px-3 py-2">Cómo se usa</th></tr>
        </thead>
        <tbody class="divide-y divide-amber-100">
            <tr><td class="px-3 py-2 font-semibold">Área de propiedad</td><td class="px-3 py-2" x-text="land || 'Pendiente'"></td><td class="px-3 py-2">Base física traída del numeral 3.2 o MIDAS.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Frente y fondo</td><td class="px-3 py-2" x-text="(front || 'Pendiente') + ' / ' + (fmt(depthValue()) || 'Pendiente')"></td><td class="px-3 py-2">Datos básicos para formar la geometría del lote.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Huella ocupable</td><td class="px-3 py-2" x-text="fmt(geometryFootprint()) || 'Pendiente'"></td><td class="px-3 py-2">(Frente - laterales) x (fondo - frontal - posterior).</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Área neta normativa</td><td class="px-3 py-2" x-text="fmt(netArea()) || 'Pendiente'"></td><td class="px-3 py-2">Área base después de afectaciones generales, si existen.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Índice de ocupación</td><td class="px-3 py-2" x-text="fmt(occupancyRatio()) || 'Pendiente'"></td><td class="px-3 py-2">Huella ocupable dividida entre área de terreno, salvo índice expreso adoptado.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Altura máxima</td><td class="px-3 py-2" x-text="floors || 'Pendiente'"></td><td class="px-3 py-2">Pisos o altura usados para estimar índice cuando aplique.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Índice de construcción</td><td class="px-3 py-2" x-text="fmt(buildIndex()) || 'Pendiente'"></td><td class="px-3 py-2">Dato normativo o estimación IO x pisos.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Área máxima construible</td><td class="px-3 py-2" x-text="fmt(maxBuild()) || 'Pendiente'"></td><td class="px-3 py-2">Área neta x índice de construcción.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Factor de área vendible</td><td class="px-3 py-2" x-text="sellFactor || 'Pendiente'"></td><td class="px-3 py-2">Dato manual; exige soporte de cabida, diseño, producto o residual.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Área vendible de referencia</td><td class="px-3 py-2" x-text="fmt(sellableArea()) || 'Pendiente'"></td><td class="px-3 py-2">Solo se calcula si hay área directa o factor vendible soportado.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Potencial adicional</td><td class="px-3 py-2 font-semibold text-teal-800" x-text="fmt(potential()) || 'Pendiente'"></td><td class="px-3 py-2">Máximo construible menos construcción actual.</td></tr>
        </tbody>
    </table>
</div>
