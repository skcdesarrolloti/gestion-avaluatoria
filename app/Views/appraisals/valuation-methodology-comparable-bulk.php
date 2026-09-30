<?php
$bulkQuery = trim((string) ($baseQuery ?? ''));
?>
<section class="mt-5 rounded-xl border border-teal-100 bg-teal-50 p-4" data-comparable-bulk-panel
    data-default-query="<?= e($bulkQuery) ?>">
    <div class="grid gap-4 lg:grid-cols-[1fr_320px]">
        <div>
            <p class="eyebrow text-teal-800">Captura rápida</p>
            <h4 class="mt-2 text-lg font-semibold text-teal-950">Pega avisos o filas y el sistema llena la tabla</h4>
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
