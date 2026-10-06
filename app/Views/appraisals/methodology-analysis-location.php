<template x-if="analysisModule==='location'"><section class="space-y-4">
    <h3 class="text-xl font-semibold">Localización comparada del sujeto y las muestras</h3>
    <p>Registra coordenadas WGS84, precisión y evidencia del anuncio. Una ubicación de sector sigue siendo aproximada. Las coordenadas del sujeto se consultan desde su ficha.</p>
    <p x-text="'Sujeto: '+(analysisSubjectLocation.latitude || 'latitud pendiente')+', '+(analysisSubjectLocation.longitude || 'longitud pendiente')"></p>
    <p role="status" x-text="analysisMapPoints().filter(p=>p.n!=='S').length+' de '+analysisActiveRows().length+' muestras con coordenadas · '+analysisActiveRows().filter(r=>analysisMapReady(r)).length+' con precisión y soporte registrados'"></p>
    <label class="flex min-h-11 items-center gap-2"><input type="checkbox" x-model="analysisMapOnlyPending">Mostrar solo ubicaciones pendientes</label>
    <div class="max-h-[60vh] overflow-auto rounded-xl border p-3">
        <template x-for="row in analysisMapCaptureRows()" :key="row.id"><article class="border-b p-3" @input="analysisMapEditingId=row.id" @change="analysisMapEditingId=row.id">
            <h4 class="font-semibold" x-text="'Muestra '+(analysisActiveRows().findIndex(r=>r.id===row.id)+1)+' · '+row.source_name"></h4>
            <a class="inline-flex min-h-11 items-center text-blue-700 underline" :href="/^https?:\/\//i.test(row.source_url)?row.source_url:'#'" target="_blank" rel="noopener">Consultar ubicación en el anuncio</a>
            <div class="grid gap-3 sm:grid-cols-2">
                <label class="label">Latitud<input class="input" placeholder="10.400000" x-model="row.latitude" @input="analysisEditingId=row.id"></label>
                <label class="label">Longitud<input class="input" placeholder="-75.550000" x-model="row.longitude" @input="analysisEditingId=row.id"></label>
                <label class="label">Precisión<select class="input" x-model="row.location_verification"><option value="">Por verificar</option><option value="exact">Punto exacto verificado</option><option value="approximate">Referencia aproximada verificada</option></select></label>
                <label class="label">Fuente<input class="input" placeholder="Portal, dirección confirmada o visita" x-model="row.location_source"></label>
            </div>
            <label class="label mt-3">Responsable, fecha y soporte<textarea class="input" rows="2" maxlength="1600" placeholder="Quién confirmó el punto, cuándo y con qué evidencia" x-model="row.verification_detail"></textarea></label>
        </article></template>
    </div>
    <button type="button" class="btn-secondary" @click="analysisMapOnlyPending=false">Ver todas las ubicaciones</button>
    <div x-show="analysisMapPoints().length" class="space-y-3">
        <div x-effect="if(analysisModule==='location')analysisMapRender($el)" class="rounded-xl border" aria-label="Mapa de coordenadas del sujeto y las muestras"></div>
        <p class="text-sm">Mapa de coordenadas con norte y proporciones geográficas. Sin calles; la precisión y el soporte se detallan en el informe. Las distancias son en línea geográfica, no por recorrido.</p>
        <details><summary class="min-h-11 cursor-pointer">Consultar cartografía del sector · OpenStreetMap</summary><iframe :src="analysisModule==='location'?analysisMapUrl():null" loading="lazy" title="Cartografía del sector OpenStreetMap" class="h-80 w-full border" referrerpolicy="no-referrer"></iframe><p class="text-xs">© colaboradores de OpenStreetMap. Esta vista de sector no contiene los marcadores del mapa comparativo.</p></details>
        <button type="button" class="btn-secondary" @click="analysisMapExport()">Descargar mapa SVG para el entregable</button>
        <button type="button" class="btn-secondary" @click="analysisMapReport()">Descargar mapa y cuadro de distancias</button>
    </div>
    <p class="text-sm">Espera Guardado confirmado antes de descargar la versión definitiva del informe.</p>
</section></template>
