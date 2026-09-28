<div class="overflow-x-auto rounded-xl border border-amber-200 bg-white">
    <table class="min-w-full divide-y divide-amber-100 text-sm">
        <thead class="bg-amber-50 text-left text-xs uppercase text-amber-800">
            <tr><th class="px-3 py-2">Factor de lote</th><th class="px-3 py-2">Dato</th><th class="px-3 py-2">Cómo se usa</th></tr>
        </thead>
        <tbody class="divide-y divide-amber-100">
            <tr><td class="px-3 py-2 font-semibold">Área de propiedad</td><td class="px-3 py-2" x-text="land || 'Pendiente'"></td><td class="px-3 py-2">Base física traída del numeral 3.2 o MIDAS.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Frente y fondo</td><td class="px-3 py-2" x-text="(front || 'Pendiente') + ' / ' + (fmt(depthValue()) || 'Pendiente')"></td><td class="px-3 py-2">Datos básicos para formar la geometría del lote.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Área neta normativa</td><td class="px-3 py-2" x-text="net || 'Pendiente'"></td><td class="px-3 py-2">Dato descriptivo si la norma, afectación o soporte lo informa.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Índice de ocupación</td><td class="px-3 py-2" x-text="occ || 'Pendiente'"></td><td class="px-3 py-2">Se conserva como parámetro normativo; la huella se revisa en módulo 8.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Altura máxima</td><td class="px-3 py-2" x-text="floors || 'Pendiente'"></td><td class="px-3 py-2">Pisos o altura permitida, condicionada o pendiente de concepto.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Índice de construcción</td><td class="px-3 py-2" x-text="ci || 'Pendiente'"></td><td class="px-3 py-2">Dato normativo; no se convierte aquí en área construible.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Retiros y aislamientos</td><td class="px-3 py-2" x-text="[frontSetback, rearSetback, leftSetback, rightSetback].filter(Boolean).join(' / ') || 'Pendiente'"></td><td class="px-3 py-2">Condiciones que pueden limitar la factibilidad y se verifican en cabida.</td></tr>
            <tr><td class="px-3 py-2 font-semibold">Afectaciones</td><td class="px-3 py-2" x-text="affect || 'Pendiente'"></td><td class="px-3 py-2">Porcentaje o condición general que debe quedar sustentada.</td></tr>
        </tbody>
    </table>
</div>
