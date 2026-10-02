<div class="mt-3 rounded-lg border border-slate-200 bg-white p-3">
    <p class="text-sm font-semibold">Muestras incorporadas por fuente · unidad / banco actual</p>
    <div class="mt-2 flex flex-wrap gap-2">
        <template x-for="sourceCount in portalSummary" :key="sourceCount.label">
            <span class="rounded-full bg-teal-50 px-3 py-2 text-sm" x-text="sourceCount.label + ': ' + sourceCount.count"></span>
        </template>
    </div>
    <p class="mt-1 text-xs text-slate-600">Cuenta filas incorporadas en esta colección, incluidas las pendientes de guardar. No cuenta resultados sólo leídos ni suma las muestras de otras unidades.</p>
</div>
