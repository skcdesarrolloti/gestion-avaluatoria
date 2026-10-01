<div class="comparable-tools mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3" x-cloak>
    <p class="text-sm font-semibold" aria-live="polite">
        <span x-text="total"></span> registradas · <span x-text="pending"></span> con datos básicos pendientes ·
        <span x-text="duplicates"></span> con enlace repetido
    </p>
    <p class="mt-1 text-xs text-slate-600">Control operativo de captura. No certifica cumplimiento NTS ni suficiencia de la muestra. Los datos sin publicar quedan pendientes de verificación.</p>
    <p class="mt-2 text-sm">Completa las muestras existentes sin borrarlas. En «Campos a revisar → Ubicación y mapa» registra latitud, longitud, precisión y fuente en las notas. Art. 17: georreferenciación aproximada; si la fuente limita la ubicación, indica la mayor precisión disponible. No inventes coordenadas.</p>
    <p class="mb-2 text-xs">PH del inmueble avaluado: <strong><?= e(['si' => 'Sí', 'no' => 'No', 'no_aplica' => 'No aplica'][$record['regimen_ph'] ?? ''] ?? 'Por verificar') ?></strong>. Clasifica las muestras por separado.</p>
        <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4" @input.stop @change.stop>
        <label class="label">Propiedad horizontal
            <select class="input" :value="phFilter" @change="phFilter = $event.target.value; page = 1; render()"><option value="all">Todos los regímenes</option><option value="si">Sí, PH</option><option value="no">No PH</option><option value="por_verificar">Por verificar</option></select>
            <span class="mt-1 block text-xs font-normal">Separa PH de amenidades. Completa el régimen con soporte; no se deduce del precio de administración.</span>
        </label>
        <label class="label">Vista
            <select class="input" :value="mode" @change="mode = $event.target.value; render()" aria-describedby="comparable-view-help"><option value="cards">Fichas sin desplazamiento lateral</option><option value="table">Tabla comparativa</option></select>
            <span id="comparable-view-help" class="mt-1 block text-xs font-normal text-slate-600">Fichas para diligenciar; tabla para comparar varias muestras.</span>
        </label>
        <label class="label">Campos a revisar
            <select class="input" :value="group" @change="group = $event.target.value; render()" aria-describedby="comparable-group-help"><option value="capture">1. Captura básica</option><option value="location">2. Ubicación y mapa</option><option value="attributes">3. Atributos del inmueble</option><option value="review">4. Revisión y selección</option><option value="all">Todos los campos</option></select>
            <span id="comparable-group-help" class="mt-1 block text-xs font-normal text-slate-600" x-text="groupHelp"></span>
        </label>
        <label class="label">Mostrar
            <select class="input" :value="filter" @change="filter = $event.target.value; page = 1; render()" aria-describedby="comparable-filter-help"><option value="all">Todas las muestras</option><option value="pending">Con datos pendientes</option><option value="duplicates">Enlaces repetidos</option></select>
            <span id="comparable-filter-help" class="mt-1 block text-xs font-normal text-slate-600">Filtra las fichas registradas; no borra muestras ni cambia su selección técnica.</span>
        </label>
        <label class="label">Buscar en la captura
            <input class="input" placeholder="Fuente, barrio, edificio o código" :value="search" @input="search = $event.target.value; page = 1; render()" aria-describedby="comparable-search-help">
            <span id="comparable-search-help" class="mt-1 block text-xs font-normal text-slate-600">Busca entre tus muestras. No busca inmuebles en los portales.</span>
        </label>
    </div>
    <div class="mt-3 flex flex-wrap items-center gap-2">
        <button type="button" class="btn-secondary" @click="add()" >Nueva muestra</button>
        <button type="button" class="btn-secondary" @click="page--; render()" :disabled="page <= 1">Anterior</button>
        <span class="text-sm" aria-live="polite">Página <span x-text="page"></span> de <span x-text="pages"></span> · hasta 10 inmuebles</span>
        <button type="button" class="btn-secondary" @click="page++; render()" :disabled="page >= pages">Siguiente</button>
        <button type="submit" class="btn-primary">Guardar ahora</button>
        <span class="text-xs" data-autosave-status aria-live="polite">Autoguardado activo</span>
    </div>
    <?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-comparable-removal.php'; ?>
    <p class="mt-2 rounded-lg bg-teal-50 p-3 text-sm" x-show="total === 0 && shown === 0">La matriz está vacía: no quedan muestras. Pulsa «Seguir capturando» para buscar avisos o «Nueva muestra» para diligenciar una manualmente.</p>
    <p class="mt-2 text-sm" x-show="shown === 0 && total > 0">No hay muestras que coincidan con este filtro.</p>
    <div x-show="mode === 'table' && shown > 0" class="mt-3">
        <p class="text-xs text-slate-600">Desplaza la tabla aquí, sin bajar al final. También puedes usar las flechas del teclado.</p>
        <div class="comparable-scroll" x-ref="topScroll" tabindex="0" role="region" aria-label="Desplazamiento horizontal de comparables"
            @scroll="$refs.grid.scrollLeft = $el.scrollLeft"><div x-ref="track" style="height:1px"></div></div>
    </div>
</div>
