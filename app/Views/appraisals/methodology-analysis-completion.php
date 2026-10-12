<section class="mb-4 rounded-xl border p-3" aria-label="Estado del grupo preparado">
    <p x-show="analysisHasSimulated()" class="mb-2 rounded bg-purple-50 p-2 font-semibold text-purple-900">Ejercicio numérico con datos simulados. Los originales se conservan; estos complementos no son datos verificados.</p>
    <p class="my-2" aria-live="polite" x-text="analysisComplete()+' con atributos completos · '+(analysisActiveRows().length-analysisComplete())+' con atributos pendientes'"></p>
    <button type="button" class="btn-primary my-2" @click="analysisModule='statistics';courseStep=0">Continuar a 2. Entender la muestra</button>
    <div x-show="analysisComplete()<analysisActiveRows().length">
    <div class="flex flex-wrap gap-2 text-sm"><span class="rounded bg-emerald-50 p-2 text-emerald-900">Verde: dato recogido</span><span class="rounded bg-amber-50 p-2 text-amber-900">Amarillo: falta completar</span><span class="rounded bg-blue-50 p-2 text-blue-900">Azul: dato manual con soporte</span></div>
    <p class="my-2 text-sm">Completa las celdas amarillas con el dato y su fuente.</p>
    <label class="flex min-h-11 items-center gap-2"><input type="checkbox" x-model="analysisOnlyMissing">Mostrar solo inmuebles con datos pendientes</label>
    </div>
    <button x-show="analysisManualVersion>0||analysisComplete()<analysisActiveRows().length" type="button" class="btn-secondary my-2" @click="analysisRecordManual(); $dispatch('input')">Actualizar resultado con los datos completados</button>

</section>
