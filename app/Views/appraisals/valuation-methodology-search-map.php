<section class="space-y-4 rounded-xl border border-slate-200 bg-white p-4">
    <p class="eyebrow">8.3 · misma matriz, misma muestra</p>
    <h3 class="text-xl font-semibold">Mapas y evidencia</h3>
    <p>Completa aquí la ubicación y el soporte de cada inmueble. Las fichas de abajo son las mismas filas de la matriz; no debes importar ni escribir de nuevo las muestras.</p>
    <ol class="list-decimal space-y-2 pl-5 text-sm">
        <li>Registra latitud, longitud, precisión y fuente. Si el portal solo ubica el sector, conserva esa salvedad; no inventes un punto exacto.</li>
        <li>Pulsa «Fotos y soporte» en la muestra y pega una captura con URL, precio, áreas y ubicación. Escribe qué evidencia conservaste y su fecha; una foto decorativa no basta.</li>
        <li>Registra quién corroboró la información, cuándo y con qué resultado. Espera la confirmación de autoguardado. En 8.4 se decidirá su comparabilidad y tratamiento.</li>
    </ol>
    <p class="text-sm"><strong x-text="mapPoints.filter(p => p.n !== 'S').length"></strong> muestras activas con coordenadas válidas. «S» identifica al bien sujeto cuando tiene coordenadas. Este esquema se actualiza al editar; el estado de guardado indica si los cambios ya llegaron a la base.</p>
    <details class="rounded-lg border p-3" x-show="mapPoints.length">
        <summary class="min-h-11 cursor-pointer font-semibold">Ver distribución esquemática de las muestras</summary>
        <div class="relative my-3 h-72 rounded-lg border bg-slate-50" role="img" aria-label="Distribución esquemática de las muestras con coordenadas">
            <template x-for="point in mapPoints" :key="point.n"><span class="absolute rounded-full bg-teal-700 px-2 py-1 text-xs font-bold text-white" :style="'left:' + (point.x / 6) + '%;top:' + (point.y / 3.6) + '%;transform:translate(-50%,-50%)'" :title="point.label" x-text="point.n"></span></template>
        </div>
        <p class="text-xs">Esquema sin cartografía base ni escala de distancias. No representa calles o linderos, no acredita exactitud y no decide comparabilidad.</p>
        <ul class="mt-3 space-y-2"><template x-for="point in mapPoints" :key="point.n"><li><a class="btn-secondary" :href="point.url" target="_blank" rel="noopener" x-text="'Muestra ' + point.n + ' · ' + point.label + ' · ' + point.precision + ' · Abrir mapa'"></a></li></template></ul>
    </details>
    <p class="text-sm" x-show="!mapPoints.length">Aún no hay coordenadas válidas. Puedes completar las fichas de abajo sin borrar los demás datos.</p>
</section>
