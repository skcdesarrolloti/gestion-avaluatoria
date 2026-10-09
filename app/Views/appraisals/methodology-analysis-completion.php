<section class="mb-4 rounded-xl border p-3" aria-label="Estado del grupo preparado">
    <p x-show="analysisHasSimulated()" class="mb-2 rounded bg-purple-50 p-2 font-semibold text-purple-900">Ejercicio numérico con datos simulados. Los originales se conservan; estos complementos no son datos verificados.</p>
    <h3 class="font-semibold">Estado del grupo preparado</h3>
    <p class="my-2" aria-live="polite" x-text="analysisActiveRows().length+' inmuebles conservados · '+analysisComplete()+' con datos completos · '+(analysisActiveRows().length-analysisComplete())+' con datos pendientes'"></p>
    <p class="my-2 text-sm" x-show="analysisComplete()===analysisActiveRows().length">No faltan campos en los factores aplicados. No necesitas completar ni actualizar de nuevo sólo para continuar. Conserva la identificación de datos simulados y revisa su sustento antes de usar resultados en el avalúo.</p>
    <button type="button" class="btn-primary my-2" @click="analysisModule='statistics';courseStep=0">Continuar a 2. Entender la muestra</button>
    <div x-show="analysisComplete()<analysisActiveRows().length">
    <div class="flex flex-wrap gap-2 text-sm"><span class="rounded bg-emerald-50 p-2 text-emerald-900">Verde: dato recogido</span><span class="rounded bg-amber-50 p-2 text-amber-900">Amarillo: falta completar</span><span class="rounded bg-blue-50 p-2 text-blue-900">Azul: dato manual con soporte</span></div>
    <p class="my-2 text-sm">Completa las celdas amarillas en la tabla, con el dato y su fuente. Los originales se conservan. Vacío no equivale a cero. El borrador se autoguarda; después registra otro resultado para conservar esta etapa en el historial.</p>
    <label class="flex min-h-11 items-center gap-2"><input type="checkbox" x-model="analysisOnlyMissing">Mostrar solo inmuebles con datos pendientes</label>
    <button type="button" class="btn-primary my-2" @click="analysisRecordManual(); $dispatch('input')">Actualizar resultado con los datos completados</button>
    </div>

</section>
