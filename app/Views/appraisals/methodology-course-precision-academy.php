<details class="rounded-xl border bg-slate-50 p-4">
    <summary class="min-h-11 cursor-pointer font-semibold">Academia con ejemplos · entender la precisión y el remuestreo desde cero</summary>
    <div class="mt-3 space-y-3">
        <p>Abre un tema a la vez. Los números del ejemplo son didácticos: no sustituyen tus muestras ni se incorporan al avalúo.</p>
        <details class="rounded-lg border bg-white p-3">
            <summary class="min-h-11 cursor-pointer font-semibold">1. Qué permanece igual cuando usamos los mismos datos</summary>
            <p class="mt-3">Imagina tres inmuebles con valores de 8, 10 y 12 millones COP/m². La media (promedio aritmético) es (8 + 10 + 12) / 3 = 10 millones COP/m². Si vuelves a calcular usando cada valor una vez, siempre obtienes 10.</p>
            <p class="mt-3">La mediana (valor central después de ordenar) también es 10. La desviación estándar muestral (medida de cuánto se separan los valores de la media) es 2 millones COP/m². El CV (coeficiente de variación: dispersión expresada como porcentaje de la media) es 100 × 2 / 10 = 20 %.</p>
            <p class="mt-3">El remuestreo no modifica esa media ni ese CV originales. Produce cálculos adicionales para estudiar su variabilidad.</p>
        </details>
        <details class="rounded-lg border bg-white p-3">
            <summary class="min-h-11 cursor-pointer font-semibold">2. Bootstrap (remuestreo con reemplazo): la bolsa de tres fichas</summary>
            <p class="mt-3">Escribe 8, 10 y 12 en tres fichas y mételas en una bolsa. Saca una ficha, anota su número y devuélvela a la bolsa antes de sacar la siguiente. Devolverla es el reemplazo: permite sacar el mismo número varias veces.</p>
            <p class="mt-3">Haz tres extracciones para formar una repetición. Cada ficha tiene la misma posibilidad de salir. La repetición conserva tres valores, pero algunos originales pueden repetirse y otros no aparecer.</p>
            <div class="mt-3 overflow-auto" role="region" aria-label="Ejemplo de tres repeticiones de remuestreo" tabindex="0">
                <table class="min-w-full text-left"><caption class="mb-2 text-left">Valores en millones COP/m² · ejemplos posibles, no resultado de tu ejecución</caption>
                    <thead><tr><th class="p-2">Conjunto</th><th class="p-2">Valores usados</th><th class="p-2">Media (promedio)</th></tr></thead>
                    <tbody><tr><td class="p-2">Original</td><td class="p-2">8, 10, 12</td><td class="p-2">10</td></tr>
                    <tr><td class="p-2">Repetición A</td><td class="p-2">8, 8, 12</td><td class="p-2">9,33</td></tr>
                    <tr><td class="p-2">Repetición B</td><td class="p-2">10, 12, 12</td><td class="p-2">11,33</td></tr>
                    <tr><td class="p-2">Repetición C</td><td class="p-2">8, 10, 12</td><td class="p-2">10</td></tr></tbody>
                </table>
            </div>
            <p class="mt-3">Ahora se ve qué cambia: la frecuencia (cantidad de veces que aparece cada valor). Cambiar sólo el orden de 8, 10 y 12 no cambiaría la media. Repetir unos y omitir otros sí puede cambiarla.</p>
        </details>
        <details class="rounded-lg border bg-white p-3">
            <summary class="min-h-11 cursor-pointer font-semibold">3. Qué hace exactamente el botón de 10.000 remuestreos</summary>
            <p class="mt-3">El programa toma los n valores unitarios disponibles en el análisis actualizado. La letra n significa cantidad de valores utilizables: si tienes 34, cada repetición contiene 34 extracciones con reemplazo.</p>
            <ol class="mt-3 list-decimal pl-6 space-y-2">
                <li>Escoge al azar n valores de esa misma lista, permitiendo repeticiones.</li>
                <li>Calcula la media, la mediana y el CV de esa repetición.</li>
                <li>Repite el procedimiento 10.000 veces y ordena los resultados de cada medida por separado.</li>
                <li>Presenta un límite inferior y otro superior para cada medida, según la confianza elegida.</li>
            </ol>
            <p class="mt-3">Con 34 valores se hacen 340.000 extracciones en total, agrupadas en 10.000 repeticiones de 34. Sigues teniendo los mismos 34 valores de mercado; esas extracciones no son nuevos inmuebles ni nuevas evidencias.</p>
            <p class="mt-3">La semilla (número de inicio que permite repetir la secuencia aleatoria) es 2026. Con los mismos datos, orden y confianza se recupera el mismo cálculo. Mulberry32 (generador de números seudoaleatorios) es el mecanismo usado; no es un criterio de valoración.</p>
            <p class="mt-3">El botón realiza un cálculo opcional en la ficha abierta. No cambia las muestras, los descuentos ni la media original. Para conservar esta ejecución, descarga su memoria. Al actualizar el análisis se reinicia el resultado del remuestreo.</p>
        </details>
        <details class="rounded-lg border bg-white p-3">
            <summary class="min-h-11 cursor-pointer font-semibold">4. Cómo leer los límites del remuestreo</summary>
            <p class="mt-3">Con confianza del 95 %, el programa busca el percentil 2,5 y el percentil 97,5 (posiciones que dejan aproximadamente el 2,5 % de resultados por debajo y por encima). Utiliza interpolación (cálculo entre posiciones vecinas cuando la posición no es entera).</p>
            <p class="mt-3">Ejemplo hipotético: si los límites para la media fueran 9,4 y 10,6 millones COP/m², indicarían el tramo central del 95 % de las medias remuestreadas. No serían los precios mínimo y máximo de los inmuebles ni el intervalo de valor de tu oficina.</p>
            <p class="mt-3">Cada fila tiene su propia unidad: media y mediana en COP/m²; CV en porcentaje. El intervalo de la mediana no es el de la media, y el intervalo del CV no reemplaza el CV original.</p>
            <p class="mt-3">Los límites se interpretan como una aproximación a la incertidumbre del estimador (medida calculada para conocer el grupo), bajo supuestos de muestreo adecuados. Que el procedimiento produzca cifras no demuestra que la muestra represente el mercado.</p>
        </details>
        <details class="rounded-lg border bg-white p-3">
            <summary class="min-h-11 cursor-pointer font-semibold">5. La tabla superior: precisión de la media, explicada por partes</summary>
            <p class="mt-3">La tabla superior usa otro procedimiento: el IC (intervalo de confianza) con t de Student (distribución de referencia para calcular el margen de la media). No necesita ejecutar el remuestreo.</p>
            <ul class="mt-3 list-disc pl-6 space-y-2">
                <li><strong>n:</strong> cantidad de valores disponibles. <strong>s:</strong> desviación estándar muestral, en COP/m².</li>
                <li><strong>gl (grados de libertad):</strong> n − 1; con 34 valores son 33.</li>
                <li><strong>Error estándar (variabilidad estimada de la media):</strong> s / √n. No mide el error del avalúo.</li>
                <li><strong>t:</strong> multiplicador que depende de la confianza y de los grados de libertad.</li>
                <li><strong>Semiancho (margen a cada lado de la media):</strong> t × error estándar.</li>
                <li><strong>Inferior y superior:</strong> media menos ese margen y media más ese margen.</li>
                <li><strong>Semiancho relativo:</strong> margen dividido por el valor absoluto de la media, multiplicado por 100. Es distinto del CV.</li>
            </ul>
            <p class="mt-3">Ejemplo redondeado: una media de 10 millones con un semiancho de 0,8 millones produce un intervalo de 9,2 a 10,8 millones COP/m² y un semiancho relativo del 8 %. Ese 8 % no equivale al CV ni representa automáticamente el error de valorar un inmueble.</p>
            <p class="mt-3">Confianza del 95 % significa que, si repitiéramos el muestreo y este procedimiento bajo sus supuestos, aproximadamente el 95 % de los intervalos incluiría la media poblacional (promedio del mercado definido). No significa que el 95 % de los inmuebles tenga su precio dentro de este intervalo.</p>
        </details>
        <details class="rounded-lg border bg-white p-3">
            <summary class="min-h-11 cursor-pointer font-semibold">6. Para qué sirve y qué no puede resolver</summary>
            <p class="mt-3">El remuestreo ayuda a explorar cuánto varían la media, la mediana y el CV al repetir u omitir valores en composiciones posibles de la muestra. Un intervalo amplio invita a revisar la incertidumbre; uno estrecho exige igualmente comprobar la calidad de los datos.</p>
            <p class="mt-3">No corrige sesgo (desviación sistemática por cómo se eligieron los datos), mezcla de mercados, áreas mal definidas, duplicados ni dependencia (observaciones que comparten información y no actúan como muestras independientes). El remuestreo actual trata cada valor disponible como una observación independiente; no agrupa por edificio o proyecto.</p>
            <p class="mt-3">No reduce el CV original, no justifica eliminar inmuebles ni convierte 10.000 repeticiones en 10.000 comparables. Tampoco valida por sí solo el límite normativo que corresponda al expediente.</p>
            <p class="mt-3">Si una repetición tiene media cero, su CV no es estimable porque exigiría dividir entre cero; ese resultado se omite sólo de la serie de CV. La media y la mediana de esa repetición siguen calculándose.</p>
        </details>
        <details class="rounded-lg border bg-white p-3">
            <summary class="min-h-11 cursor-pointer font-semibold">7. Cómo avanzamos en nuestro avalúo</summary>
            <p class="mt-3">Primero verificamos el objetivo, la base de precio y área, los inmuebles comparables y sus fuentes. Después comprendemos la media y el CV originales. Luego interpretamos la precisión de la media; el remuestreo puede usarse como complemento si sus supuestos son razonables.</p>
            <p class="mt-3">Al construir la regresión (modelo que relaciona el precio con atributos del inmueble), revisaremos sus errores y su validación por separado. Los intervalos de esta pantalla corresponden a medidas del grupo, no a una predicción individual de ese modelo.</p>
            <p class="mt-3 text-sm">Lectura vinculada: Sesión 1 del curso, precisión de la media, páginas 36–46. Este ejemplo de la bolsa explica el complemento de remuestreo implementado; no introduce una exigencia normativa adicional.</p>
        </details>
    </div>
</details>
