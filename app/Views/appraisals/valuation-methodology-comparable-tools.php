<div class="comparable-tools mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3" x-cloak>
    <p class="text-sm font-semibold" aria-live="polite">
        <span x-text="total"></span> registradas · <span x-text="pending"></span> con datos básicos pendientes ·
        <span x-text="duplicates"></span> con enlace repetido
    </p>
    <p class="mt-2 text-sm"><span x-text="capturePendingCount"></span> muestras con negociación, componentes o soportes por confirmar. Las celdas pendientes se resaltan en amarillo.</p>
    <?php require __DIR__ . '/methodology-portal-counts.php'; ?>
    <p id="negotiation-help" class="mt-2 rounded-lg bg-teal-50 p-3 text-sm">Valor negociado = precio ofertado − descuento de negociación. El descuento se registra en la misma unidad del precio, con tipo (otorgado o estimado) y soporte. Vacío = pendiente; 0 = sin descuento confirmado. Este resultado todavía requiere la depuración por componentes en M4; no es el valor adoptado del avalúo.</p>
    <p class="mt-1 text-xs text-slate-600">Control operativo de captura. No certifica cumplimiento NTS ni suficiencia de la muestra. Los datos sin publicar quedan pendientes de verificación.</p>
    <p class="mt-2 text-sm">Completa las muestras existentes sin borrarlas. En «Mapas, coordenadas y fotos» registra latitud, longitud, precisión, fuente y soporte de cada muestra. Art. 17: georreferenciación aproximada; si la fuente limita la ubicación, indica la mayor precisión disponible. No inventes coordenadas.</p>
    <p class="mb-2 text-xs">PH del inmueble avaluado: <strong><?= e(['si' => 'Sí', 'no' => 'No', 'no_aplica' => 'No aplica'][$record['regimen_ph'] ?? ''] ?? 'Por verificar') ?></strong>. Clasifica las muestras por separado.</p>
        <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4" @input.stop @change.stop>
        <label class="label">Propiedad horizontal
            <select :disabled="mapBusy || photoBusy || photoRetry" class="input" :value="phFilter" @change="phFilter = $event.target.value; page = 1; render()"><option value="all">Todos los regímenes</option><option value="si">Sí, PH</option><option value="no">No PH</option><option value="por_verificar">Por verificar</option></select>
            <span class="mt-1 block text-xs font-normal">Separa PH de amenidades. Completa el régimen con soporte; no se deduce del precio de administración.</span>
        </label>
        <label class="label" x-show="searchTab !== 'mapa'">Vista
            <select :disabled="mapBusy || photoBusy || photoRetry" class="input" :value="mode" @change="mode = $event.target.value; render()" aria-describedby="comparable-view-help"><option value="cards">Fichas sin desplazamiento lateral</option><option value="table">Tabla comparativa</option></select>
            <span id="comparable-view-help" class="mt-1 block text-xs font-normal text-slate-600">Fichas para diligenciar; tabla para comparar varias muestras.</span>
        </label>
        <label class="label" x-show="searchTab !== 'mapa'">Campos a revisar
            <select :disabled="mapBusy || photoBusy || photoRetry" class="input" :value="group" @change="group = $event.target.value; render()" aria-describedby="comparable-group-help"><option value="capture">1. Captura básica</option><option value="ph">2. PH · área privada, parqueaderos y depósitos</option><option value="composition">3. Áreas y componentes · NPH / condominio</option><option value="attributes">4. Atributos del inmueble</option><option value="review">5. Revisión y selección</option><option value="all">Todos los campos</option></select>
            <span id="comparable-group-help" class="mt-1 block text-xs font-normal text-slate-600" x-text="groupHelp"></span>
        </label>
        <label class="label">Mostrar
            <select :disabled="mapBusy || photoBusy || photoRetry" class="input" :value="filter" @change="filter = $event.target.value; page = 1; render()" aria-describedby="comparable-filter-help"><option value="all">Todas las muestras</option><option value="pending">Con datos pendientes</option><option value="duplicates">Enlaces repetidos</option></select>
            <span id="comparable-filter-help" class="mt-1 block text-xs font-normal text-slate-600">Filtra las fichas registradas; no borra muestras ni cambia su selección técnica.</span>
        </label>
        <label class="label">Buscar en la captura
            <input :disabled="mapBusy || photoBusy || photoRetry" class="input" placeholder="Fuente, barrio, edificio o código" :value="search" @input="search = $event.target.value; page = 1; render()" aria-describedby="comparable-search-help">
            <span id="comparable-search-help" class="mt-1 block text-xs font-normal text-slate-600">Busca entre tus muestras. No busca inmuebles en los portales.</span>
        </label>
    </div>
    <div class="mt-3 flex flex-wrap items-center gap-2">
        <button type="button" class="btn-secondary" @click="add()" x-show="searchTab !== 'mapa'">Nueva muestra</button>
        <button type="button" class="btn-secondary" @click="searchTab === 'mapa' ? moveMap(-1) : (page--, render())" :disabled="page <= 1 || mapBusy || photoBusy || photoRetry">Anterior</button>
        <span class="text-sm" aria-live="polite" x-text="searchTab === 'mapa' ? ('Ficha ' + (shown ? page : 0) + ' de ' + shown + ' · una muestra a la vez') : ('Página ' + page + ' de ' + pages + ' · hasta 10 inmuebles')"></span>
        <button type="button" class="btn-secondary" @click="searchTab === 'mapa' ? moveMap(1) : (page++, render())" :disabled="page >= pages || mapBusy || photoBusy || photoRetry">Siguiente</button>
        <button type="button" class="btn-primary" x-show="searchTab === 'mapa' && mapIndex !== null" @click="openPhotos(mapIndex)" :disabled="mapBusy || photoBusy || photoRetry">Fotos de esta muestra</button>
        <button type="submit" class="btn-primary">Guardar ahora</button>
        <span class="text-xs" data-autosave-status aria-live="polite">Autoguardado activo</span>
    </div>
    <p class="mt-2 text-xs text-slate-600">La tabla es editable y autoguarda. Excel exporta todas las filas de esta unidad/banco, después de confirmar el guardado. Amarillo = por confirmar. Puedes importar el archivo actualizado y revisar los cambios antes de aplicarlos.</p>
    <p x-show="searchTab === 'mapa'" role="status" class="mt-2 text-sm" x-text="mapMessage"></p>
    <div x-show="searchTab !== 'mapa'"><?php require BASE_PATH . '/app/Views/appraisals/valuation-methodology-comparable-removal.php'; ?></div>
    <p class="mt-2 rounded-lg bg-teal-50 p-3 text-sm" x-show="total === 0 && shown === 0">La matriz está vacía: no quedan muestras. Pulsa «Seguir capturando» para buscar avisos o «Nueva muestra» para diligenciar una manualmente.</p>
    <p class="mt-2 text-sm" x-show="shown === 0 && total > 0">No hay muestras que coincidan con este filtro.</p>
    <div x-show="searchTab !== 'mapa' && mode === 'table' && shown > 0" class="mt-3">
        <p class="text-xs text-slate-600">Desplaza la tabla aquí, sin bajar al final. También puedes usar las flechas del teclado.</p>
        <div class="comparable-scroll" x-ref="topScroll" tabindex="0" role="region" aria-label="Desplazamiento horizontal de comparables"
            @scroll="$refs.grid.scrollLeft = $el.scrollLeft"><div x-ref="track" style="height:1px"></div></div>
    </div>
</div>
