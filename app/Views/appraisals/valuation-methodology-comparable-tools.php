<div class="comparable-tools mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3" x-cloak>
    <p class="text-sm font-semibold" aria-live="polite">
        <span x-text="total"></span> registradas · <span x-text="pending"></span> con datos básicos pendientes ·
        <span x-text="duplicates"></span> con enlace repetido
    </p>
    <p class="mt-1 text-xs text-slate-600">Control operativo de captura. No certifica cumplimiento NTS ni suficiencia de la muestra. Los datos sin publicar quedan pendientes de verificación.</p>
    <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4" @input.stop @change.stop>
        <label class="label">Vista
            <select class="input" x-model="mode" @change="render()"><option value="cards">Fichas sin desplazamiento lateral</option><option value="table">Tabla comparativa</option></select>
        </label>
        <label class="label">Campos a revisar
            <select class="input" x-model="group" @change="render()"><option value="capture">1. Captura básica</option><option value="location">2. Ubicación y mapa</option><option value="attributes">3. Atributos del inmueble</option><option value="review">4. Revisión y selección</option><option value="all">Todos los campos</option></select>
        </label>
        <label class="label">Mostrar
            <select class="input" x-model="filter" @change="page = 1; render()"><option value="all">Todas las muestras</option><option value="pending">Con datos pendientes</option><option value="duplicates">Enlaces repetidos</option></select>
        </label>
        <label class="label">Buscar en la captura
            <input class="input" placeholder="Fuente, barrio, edificio o código" x-model="search" @input="page = 1; render()">
        </label>
    </div>
    <div class="mt-3 flex flex-wrap items-center gap-2">
        <button type="button" class="btn-secondary" @click="add()" :disabled="total >= 60">Nueva muestra</button>
        <button type="button" class="btn-secondary" @click="page--; render()" :disabled="page <= 1">Anterior</button>
        <span class="text-sm" aria-live="polite">Página <span x-text="page"></span> de <span x-text="pages"></span> · hasta 5 fichas</span>
        <button type="button" class="btn-secondary" @click="page++; render()" :disabled="page >= pages">Siguiente</button>
        <button type="submit" class="btn-primary">Guardar ahora</button>
        <span class="text-xs" data-autosave-status aria-live="polite">Autoguardado activo</span>
    </div>
    <p class="mt-2 text-sm" x-show="shown === 0">No hay muestras que coincidan con este filtro.</p>
    <div x-show="mode === 'table'" class="mt-3">
        <p class="text-xs text-slate-600">Desplaza la tabla aquí, sin bajar al final. También puedes usar las flechas del teclado.</p>
        <div class="comparable-scroll" x-ref="topScroll" tabindex="0" role="region" aria-label="Desplazamiento horizontal de comparables"
            @scroll="$refs.grid.scrollLeft = $el.scrollLeft"><div x-ref="track" style="height:1px"></div></div>
    </div>
</div>
