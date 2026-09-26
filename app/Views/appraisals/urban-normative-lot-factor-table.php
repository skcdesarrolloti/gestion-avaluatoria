<div class="overflow-x-auto rounded-xl border border-amber-200 bg-white">
    <table class="min-w-full divide-y divide-amber-100 text-sm">
        <thead class="bg-amber-50 text-left text-xs uppercase text-amber-800">
            <tr><th class="px-3 py-2">Factor de lote</th><th class="px-3 py-2">Dato</th><th class="px-3 py-2">Cómo se usa</th></tr>
        </thead>
        <tbody class="divide-y divide-amber-100">
            <tr><td class="px-3 py-2 font-semibold">Área de propiedad</td><td class="px-3 py-2" x-text="land || 'Pendiente'"></td><td class="px-3 py-2">Base física traída del numeral 3.2 o MIDAS.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Área neta normativa</td><td class="px-3 py-2" x-text="fmt(netArea()) || 'Pendiente'"></td><td class="px-3 py-2">Área útil después de retiros, afectaciones o cesiones.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Índice de ocupación</td><td class="px-3 py-2" x-text="occ || 'Pendiente'"></td><td class="px-3 py-2">Huella permitida. Si MIDAS no lo da, puede salir de área libre.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Altura máxima</td><td class="px-3 py-2" x-text="floors || 'Pendiente'"></td><td class="px-3 py-2">Pisos o altura usados para estimar índice cuando aplique.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Índice de construcción</td><td class="px-3 py-2" x-text="fmt(buildIndex()) || 'Pendiente'"></td><td class="px-3 py-2">Dato normativo o estimación IO x pisos.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Área máxima construible</td><td class="px-3 py-2" x-text="fmt(maxBuild()) || 'Pendiente'"></td><td class="px-3 py-2">Área neta x índice de construcción.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Factor de área vendible</td><td class="px-3 py-2" x-text="sellFactor || 'Pendiente'"></td><td class="px-3 py-2">Parámetro preliminar para un futuro residual.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Área vendible de referencia</td><td class="px-3 py-2" x-text="fmt(sellableArea()) || 'Pendiente'"></td><td class="px-3 py-2">Área de venta orientativa si se adopta el escenario.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Potencial adicional</td><td class="px-3 py-2 font-semibold text-teal-800" x-text="fmt(potential()) || 'Pendiente'"></td><td class="px-3 py-2">Máximo construible menos construcción actual.</td></tr>
        </tbody>
    </table>
</div>
