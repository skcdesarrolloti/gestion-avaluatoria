<?php
$bulkQuery = trim((string) ($baseQuery ?? ''));
?>
<section class="mt-5 rounded-xl border border-teal-100 bg-teal-50 p-4" data-comparable-bulk-panel
    data-default-query="<?= e($bulkQuery) ?>">
    <details class="mb-4 rounded-lg border border-teal-200 bg-white p-4">
        <summary class="min-h-11 cursor-pointer font-semibold text-teal-950">Guía: del aviso a la muestra y su soporte</summary>
        <ol class="mt-3 space-y-2 text-sm leading-6 text-slate-700">
            <li><strong>1. Buscar:</strong> elige una fuente y sigue su indicación de filtros: operación, ciudad, barrio, tipo de inmueble y alcobas. Captura y revisa sus avisos antes de pasar a la siguiente. La frase del sistema resume la búsqueda; no es una instrucción de IA para el portal.</li>
            <li><strong>2. Abrir el aviso:</strong> en FincaRaíz, copia el enlace del inmueble y usa «Leer aviso» arriba. En otros portales, copia enlace y texto (precio, áreas y características) y pégalos juntos abajo.</li>
            <li><strong>3. Cargar:</strong> pulsa «Cargar en filas vacías». Se prellenan los datos que el lector reconozca; lo no reconocido se revisa en la ficha. Para varios avisos, separa cada uno con una línea vacía.</li>
            <li><strong>3. Matriz de datos:</strong> recorre Captura básica, Ubicación, Atributos y Revisión. Son grupos de la misma muestra. No completes con suposiciones datos que el aviso no informa.</li>
            <li><strong>5. Conservar soporte:</strong> guarda una captura o PDF del aviso donde se vean precio, ubicación, áreas y características, junto con URL y fecha. Una foto del inmueble sola no documenta el precio anunciado. Esta captura de datos todavía no adjunta imágenes ni PDF a la muestra.</li>
            <li><strong>6. Preparar M4:</strong> verifica vigencia y duplicados, documenta la selección y los descartes. Los atributos describen la muestra; no se aplican coeficientes de homologación desde esta pantalla.</li>
        </ol>
    </details>
    <div class="grid gap-4 lg:grid-cols-[1fr_320px]">
        <div>
            <p class="eyebrow text-teal-800">Captura rápida</p>
            <h4 class="mt-2 text-lg font-semibold text-teal-950">Alternativa: pegar texto de avisos o filas</h4>
            <p class="mt-2 text-sm leading-6 text-teal-950">
                No empieces moviendo la barra horizontal. Copia texto de un aviso, varios enlaces o filas desde Excel/Sheets;
                luego pulsa cargar y revisa las filas creadas en la tabla madre.
            </p>
            <label class="mt-4 block text-sm font-semibold text-teal-950" for="bulk-comparable-text">Texto, enlaces o filas copiadas</label>
            <textarea id="bulk-comparable-text" class="input min-h-36 bg-white text-sm leading-6" data-comparable-bulk-input @input.stop @change.stop
                placeholder="Pega aquí uno o varios avisos. Ejemplo: enlace, precio, área, edificio, barrio, teléfono u observaciones. También puedes pegar filas copiadas de Excel o Google Sheets."></textarea>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                <button type="button" class="btn-primary min-h-11" data-comparable-bulk-apply>Cargar en filas vacías</button>
                <span class="text-sm font-semibold text-teal-800" role="status" data-comparable-bulk-message></span>
            </div>
        </div>
        <div class="rounded-lg bg-white p-4 text-sm leading-6 text-slate-700">
            <p class="font-bold text-slate-900">Qué intenta reconocer</p>
            <ul class="mt-2 space-y-1">
                <li>Enlace del aviso y nombre del portal.</li>
                <li>Precio o canon publicado.</li>
                <li>Área en m2, teléfono y proyecto si aparece.</li>
                <li>Operación venta/arriendo cuando el texto la menciona.</li>
            </ul>
            <p class="mt-3 text-xs font-semibold uppercase text-slate-500">Después</p>
            <p class="mt-1">Separa los avisos con una línea vacía. Para Excel/Sheets incluye encabezados: Fuente, Enlace, Precio, Área, Teléfono, Barrio. Un enlace solo no descarga los datos del aviso.</p>
            <p class="mt-2">Revisa los valores extraídos y completa los pendientes. Los enlaces repetidos se omiten; un mismo inmueble publicado en portales diferentes requiere revisión manual.</p>
        </div>
    </div>
</section>
