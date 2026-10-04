<section class="mb-5 min-w-0 rounded-xl border border-slate-200 bg-white p-4" @input.stop @change.stop>
    <h3 class="font-semibold">Comparar factores por portal</h3>
    <p class="mt-2 text-sm">Un inmueble a la vez; sus anuncios permanecen separados por fuente. Sólo se comparan juntos después de vincular su identidad en «Inmuebles recogidos».</p>
    <label class="mt-3 block text-sm font-semibold">Inmueble de la investigación
        <select class="input mt-2" x-model="comparisonId"><option value="">Selecciona un inmueble</option>
            <template x-for="group in evidence.groups" :key="group.id"><option :value="group.id" x-text="`${group.title} · ${group.id.slice(0,8)} · ${group.ads.length} anuncios`"></option></template>
        </select>
    </label>
    <p class="mt-3 text-sm">Verde: coinciden las fuentes. Amarillo: diferencia o revisión pendiente. Gris: una sola fuente, información incompleta o no publicada. Coincidencia no significa verificación.</p>
    <p class="mt-2 text-sm"><strong>Área en m² obligatoria para preparar COP/m²:</strong> conserva la base publicada y el área compatible con el alcance. En PH un área total no se convierte automáticamente en privada construida. Sin área positiva compatible, queda pendiente.</p>
    <p class="mt-2 text-xs text-slate-600">Se muestran los factores candidatos o por investigar del plan; mientras no los elijas, aparecen los más frecuentes. El área permanece visible siempre.</p>
    <template x-if="comparisonGroup">
        <div class="mt-4">
            <p class="mb-2 text-sm text-amber-800" x-show="comparisonGroup.contextPending">Tipo, operación o régimen por verificar; los colores sólo comparan datos publicados.</p>
            <div class="max-w-full overflow-x-auto rounded-lg border" tabindex="0" role="region" aria-label="Cuadro de factores por portal; desplazamiento horizontal">
                <table class="w-full text-left text-sm">
                    <caption class="sr-only">Factores publicados del mismo inmueble por anuncio y portal</caption>
                    <thead class="bg-slate-100"><tr>
                        <th scope="col" class="min-w-44 p-3">Portal / anuncio</th>
                        <th scope="col" class="min-w-40 p-3">Área publicada · m²<br><span class="font-normal" x-text="publishedAreaLabel"></span></th>
                        <template x-for="key in comparisonKeys" :key="key"><th scope="col" class="min-w-36 p-3">
                            <span x-text="`${factorLabel(key)} · ${catalog[key].unit}`"></span>
                            <span class="mt-1 block font-normal" x-text="comparisonLabel(key)"></span>
                        </th></template>
                    </tr></thead>
                    <tbody><template x-for="(ad,index) in comparisonGroup.ads" :key="ad.id || index"><tr class="border-t">
                        <th scope="row" class="p-3 font-semibold"><span x-text="ad.portal"></span>
                            <span class="block font-normal" x-text="ad.code || 'Código pendiente'"></span>
                            <a x-show="/^https?:\/\//i.test(ad.url || '')" :href="/^https?:\/\//i.test(ad.url || '') ? ad.url : '#'" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center text-blue-700 underline">Consultar fuente</a>
                        </th>
                        <td class="p-3" :class="publishedAreaClass(ad)"><span x-text="ad.publishedArea || 'No publicado'"></span><span class="block text-xs" x-text="ad.areaBasis || 'Base por confirmar'"></span></td>
                        <template x-for="key in comparisonKeys" :key="key"><td class="p-3" :class="comparisonClass(key,ad)"><span x-text="comparisonValue(key,ad)"></span></td></template>
                    </tr></template></tbody>
                </table>
            </div>
            <p class="mt-2 text-sm" x-show="comparisonGroup.ads.length < 2">Este inmueble tiene una sola fuente vinculada. No hay coincidencia entre portales que confirmar todavía.</p>
        </div>
    </template>
    <p class="mt-3 text-sm" x-show="!evidence.groups.length">No hay inmuebles disponibles para esta investigación. Recoge anuncios y revisa su selección primero.</p>
    <p class="mt-3 text-xs text-slate-600">Cuadro de consulta: no promedia, corrige ni adopta valores. Los datos distintos se conservan para la revisión del analista.</p>
</section>
