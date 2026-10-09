<section x-show="['preparation','samples','location'].includes(analysisModule)" class="mb-4 rounded-xl border border-teal-700 p-4 space-y-3" aria-label="Guía de revisión del menú 1">
    <h3 class="text-lg font-semibold">Qué debes hacer en el menú 1 · guía de revisión</h3>
    <p>Empieza por 1.1, recorre los cuatro pasos de 1.2 y termina en 1.3. Revisa cada inmueble contra su fuente. El objetivo es explicar por qué participa y qué dato usamos; todavía no buscamos que un porcentaje pase.</p>
    <p class="rounded-lg bg-teal-50 p-3" x-text="analysisModule==='preparation' ? 'Ahora: contrasta el encargo y la unidad de análisis que aparecen debajo. Después abre 1.2 Información recogida.' : analysisModule==='location' ? 'Ahora: comprueba punto, precisión, fuente y soporte de cada ubicación. Una referencia de sector no es un punto exacto.' : analysisView==='raw' ? 'Ahora: revisa anuncio, identidad, precio, área y componentes de cada inmueble. Desplaza la tabla horizontalmente para ver todas las columnas.' : analysisView==='regime' ? 'Ahora: revisa régimen y soporte. Elige el grupo de trabajo y pulsa Aplicar depuración de muestras cuando hayas revisado la decisión.' : analysisView==='clean' ? 'Ahora: elige factores pertinentes, verifica unidades y completa faltantes con evidencia. Después actualiza los factores mediante el botón existente.' : 'Ahora: examina Resultado depurado, pendientes y origen de cada dato. Una fila completa todavía puede ser incorrecta o no comparable.'"></p>
    <details class="rounded-lg border p-3">
        <summary class="min-h-11 cursor-pointer font-semibold">1.1 · Revisa el encargo antes de mirar los precios</summary>
        <div class="mt-3 space-y-3">
            <p><strong>Qué revisar:</strong> componente, fecha de valor, finalidad, base de valor, derecho, venta/arriendo y tipo. Compara lo mostrado con el encargo y los documentos del inmueble. No confundas fecha de valor con fecha de captura de una oferta.</p>
            <p><strong>Ejemplo:</strong> si estudias Oficina Principal en venta, un anuncio de arriendo mensual no tiene la misma unidad económica. Si el encargo pide otra fecha, una oferta actual requiere justificar su pertinencia temporal.</p>
            <p><strong>Dónde corregir:</strong> estos datos se consultan aquí; corrige el encargo o componente en su ficha correspondiente. No cambies una muestra para hacerla parecer compatible con un encargo mal registrado.</p>
            <button type="button" class="btn-secondary" @click="analysisModule='preparation'">Abrir 1.1 Objetivo y unidad</button>
        </div>
    </details>
    <details class="rounded-lg border p-3">
        <summary class="min-h-11 cursor-pointer font-semibold">1.2 · Paso 1: fuente, identidad, precio y área</summary>
        <div class="mt-3 space-y-3">
            <p><strong>Dónde:</strong> 1.2 Muestras y depuración → 1. Información recogida. Usa Ver anuncio y el acordeón Ubicación y anuncios originales. Revisa fuente, código, descripción, fecha disponible y evidencia conservada. Si el anuncio cambió o desapareció, identifica la evidencia y la limitación.</p>
            <p><strong>Identidad:</strong> dos portales pueden anunciar la misma oficina. Comprueba dirección, área, fotos y código; conserva los anuncios como fuentes del mismo inmueble. Revisa la agrupación en Insumos cuando corresponda. No cuentes anuncios duplicados como observaciones independientes.</p>
            <p><strong>Precio y área:</strong> verifica COP, precio total frente a precio por m², venta frente a renta y tipo de área. Distingue privada, construida y terreno. No asumas que el número publicado corresponde a la base requerida.</p>
            <p><strong>Ejemplo:</strong> 500 millones / 50 m² = 10 millones COP/m²; si los 50 m² no son el área pertinente, el cociente no queda sustentado por estar bien dividido. Precio y área capturados se corrigen en la muestra de Insumos; aquí no hay editor de esos dos campos.</p>
            <button type="button" class="btn-secondary" @click="analysisModule='samples';analysisView='raw'">Abrir Información recogida</button>
        </div>
    </details>
    <details class="rounded-lg border p-3">
        <summary class="min-h-11 cursor-pointer font-semibold">1.2 · Paso 2: comparabilidad, régimen y grupo de trabajo</summary>
        <div class="mt-3 space-y-3">
            <p><strong>Comparabilidad (pertinencia frente al sujeto):</strong> revisa uso, ubicación, fecha, área, estado, servicios y características relevantes del mercado. PH (propiedad horizontal) o no PH es una parte de esa revisión; compartir régimen no basta para ser comparable.</p>
            <p><strong>Dónde:</strong> en Información recogida o Resultado depurado puedes cambiar Régimen del inmueble y abrir Soporte del régimen para registrar documento o fuente, fecha y responsable. En 2. Depurar muestras elige Muestras para trabajar y pulsa Aplicar depuración de muestras. Ver todas permite revisar las que quedan fuera del grupo; no las elimina.</p>
            <p><strong>Qué sustentar:</strong> área privada anunciada es un indicio, no prueba jurídica del régimen. Confirma con evidencia pertinente. No excluyas por ser caro, barato o por hacer bajar el CV (coeficiente de variación). Deja los motivos y soportes en los campos existentes de la muestra; esta guía no añade un acta de selección.</p>
            <button type="button" class="btn-secondary" @click="analysisModule='samples';analysisView='regime'">Abrir Depurar muestras</button>
        </div>
    </details>
    <details class="rounded-lg border p-3">
        <summary class="min-h-11 cursor-pointer font-semibold">1.2 · Negociación y componentes: qué incluye el precio</summary>
        <div class="mt-3 space-y-3">
            <p><strong>Qué revisar:</strong> si la oferta incluye parqueaderos, depósitos u otros componentes; su tratamiento debe estar sustentado en la sección PH correspondiente. Registrar una cantidad no permite deducir automáticamente su valor. Descuento de negociación y depuración de componentes son operaciones distintas.</p>
            <p><strong>Dónde:</strong> columna Descuento · % en Información recogida o Resultado depurado. Registra el soporte del descuento en la muestra. Si no tienes evidencia, deja el pendiente explícito: vacío no equivale a cero y no uses el mismo porcentaje sólo para uniformar las ofertas.</p>
            <p><strong>Ejemplo:</strong> oferta 500 millones y descuento sustentado del 10 % → 450 millones. Dividido entre 50 m² → 9 millones COP/m² preliminares. Si ese precio incluye garaje, el descuento no separó su valor: todavía falta revisar componentes y área.</p>
        </div>
    </details>
    <details class="rounded-lg border p-3">
        <summary class="min-h-11 cursor-pointer font-semibold">1.2 · Paso 3: factores, unidades, faltantes y datos simulados</summary>
        <div class="mt-3 space-y-3">
            <p><strong>Dónde:</strong> 3. Depuración de factores por parte del analista, después de aplicar la depuración de muestras. Selecciona atributos que tengan sentido para este mercado; revisa definición, unidad, cobertura y fuente. Actualiza mediante el botón existente.</p>
            <p><strong>Qué completar:</strong> un número de baños vacío no significa cero; un rango de antigüedad no son años exactos. Diferencia publicado, completado por el analista con soporte y simulado. Los colores y estados del Resultado depurado ayudan a reconocer el origen; conserva la identificación de ejemplos.</p>
            <p><strong>No hagas:</strong> inventar un dato para llenar una fila ni elegir un atributo únicamente porque muchos anuncios lo tienen. La codificación para regresión se estudiará en el menú 3. Los mínimos de ese ejercicio no son una aprobación del avalúo ni un requisito de la estadística descriptiva.</p>
            <button type="button" class="btn-secondary" :disabled="!analysisRegimeApplied||analysisScope!==analysisAppliedScope" @click="analysisModule='samples';analysisView='clean'">Abrir Factores del analista</button>
            <p class="text-sm" x-show="!analysisRegimeApplied||analysisScope!==analysisAppliedScope">Primero aplica la depuración de muestras para habilitar esta pantalla.</p>
        </div>
    </details>
    <details class="rounded-lg border p-3">
        <summary class="min-h-11 cursor-pointer font-semibold">1.2 · Paso 4: resultado, pendientes y total recogido</summary>
        <div class="mt-3 space-y-3">
            <p><strong>Dónde:</strong> 4. Resultado depurado. Comprueba el total recogido, el grupo aplicado, las filas completas y las pendientes. El total se lee de la ficha: si agregas inmuebles, cambia; no es un número fijo en el programa.</p>
            <p><strong>Ejemplo didáctico:</strong> 71 recogidos, 50 en un grupo y 42 completos para ciertos factores serían tres conteos diferentes. No afirmamos que ésos sean tus resultados reales. La descriptiva puede usar valores unitarios disponibles aunque falten factores; la regresión exige sus variables completas.</p>
            <p><strong>Qué verificar:</strong> identifica las filas pendientes y vuelve a su fuente. Revisa oferta, descuento, valor con descuento y valor por m²; compara el cálculo con un ejemplo manual. No hay una regla automática que certifique todos los datos por estar completos.</p>
            <button type="button" class="btn-secondary" :disabled="!analysisRegimeApplied||analysisScope!==analysisAppliedScope" @click="analysisModule='samples';analysisShowResult()">Abrir Resultado depurado</button>
        </div>
    </details>
    <details class="rounded-lg border p-3">
        <summary class="min-h-11 cursor-pointer font-semibold">1.3 · Localización: punto, precisión y evidencia</summary>
        <div class="mt-3 space-y-3">
            <p><strong>Dónde:</strong> 1.3 Coordenadas y mapa comparativo. Comprueba ubicación del sujeto desde su ficha y revisa cada muestra. Registra latitud, longitud, precisión, fuente y responsable/fecha/soporte. Usa Mostrar solo ubicaciones pendientes para localizar faltantes.</p>
            <p><strong>Ejemplo:</strong> un portal ubica el anuncio en el centro del barrio. Regístralo como referencia aproximada si fue verificada; no lo conviertas en punto exacto del edificio. Una coordenada válida no acredita por sí sola la dirección ni la comparabilidad.</p>
            <p><strong>Resultado esperado:</strong> mapa interpretado con límites de precisión y evidencia por inmueble. Distancia en línea geográfica no equivale a recorrido ni demuestra por sí sola pertenencia al mismo mercado.</p>
            <button type="button" class="btn-secondary" @click="analysisModule='location';analysisSubjectLocation=<?= e($analysisSubjectLocation) ?>">Abrir Coordenadas y mapa</button>
        </div>
    </details>
    <details class="rounded-lg border p-3">
        <summary class="min-h-11 cursor-pointer font-semibold">Antes de pasar al menú 2 · cómo cerramos esta revisión</summary>
        <div class="mt-3 space-y-3">
            <p>Debes poder explicar: qué sujeto y fecha estudias; de dónde sale cada dato; cuáles anuncios corresponden al mismo inmueble; por qué el grupo es pertinente; qué área y componentes corresponden al precio; sustento del descuento; factores y unidades; ubicación y limitaciones.</p>
            <p>Los pendientes pueden seguir existiendo: identifica cuáles afectan cada cálculo y cómo limitan la conclusión. Espera Guardado confirmado del servidor, o usa Guardar análisis de muestras. Si aparece error o conflicto, resuélvelo antes de considerar conservados los cambios.</p>
            <p class="text-amber-900">Esta guía es orientativa y no registra casillas de aprobación ni certifica cierre técnico o cumplimiento normativo. Los soportes se guardan en los campos existentes. El manual y la memoria integral del menú 7 siguen pendientes.</p>
            <p class="text-sm">Consulta el texto y alcance de los arts. 19–21 y anexo 2.1 de la Resolución 0941 en Academia. La revisión de fuentes y comparabilidad precede al cálculo; esta guía no introduce nuevos criterios de exclusión.</p>
        </div>
    </details>
</section>
