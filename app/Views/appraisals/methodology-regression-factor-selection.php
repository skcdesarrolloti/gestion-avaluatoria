<fieldset :disabled="analysisBusy">
    <legend class="font-semibold">Atributos para verificar y analizar</legend>
    <p class="my-2 text-sm">Oferta, área, descuento y sus soportes siempre están disponibles. Puedes completar atributos aunque todavía tengan pocos datos o no varíen.</p>
    <div class="grid gap-2 sm:grid-cols-2"><template x-for="factor in analysisFactors" :key="factor.key"><label class="flex min-h-11 items-center gap-2"><input type="checkbox" :checked="analysisSelected.includes(factor.key)" @change="analysisSelected=$event.target.checked ? [...analysisSelected,factor.key] : analysisSelected.filter(k=>k!==factor.key);regressionConfirmed=false" :disabled="!factor.compatible"><span><span class="block" x-text="factor.label+' · '+factor.count+'/'+analysisActiveRows().length+' con dato'"></span><span class="block text-xs text-slate-600" x-show="!factor.compatible" x-text="analysisSuggestion(factor)"></span></span></label></template></div>
    <p x-show="!analysisFactors.length" class="text-sm">Aún no hay atributos capturados. Puedes agregar sus datos desde la captura existente.</p>
    <details class="mt-3 rounded-lg border p-3">
        <summary class="min-h-11 cursor-pointer font-semibold">Academia · selección y requisitos para regresión</summary>
        <p class="mt-2 text-sm">Habilitar un atributo permite revisarlo; no confirma su soporte ni su aptitud para regresión. La regla existente exige compatibilidad, al menos 50 % con dato y variación. En oficinas, Habitaciones y Estrato no participan. Área privada reemplaza área publicada como factor, sin doble conteo.</p>
        <p class="mt-2 text-sm" x-text="'Regresión: mínimo 3 factores y 30 muestras completas; 10 por factor. Selección aplicada: '+analysisSampleRule().factors+' factores, '+analysisSampleRule().complete+' filas completas.'"></p>
        <p class="mt-2 text-sm">En Construir el modelo se comprueban codificación, unidades y pertinencia de los factores.</p>
        <?php require __DIR__.'/methodology-regression-combinations.php'; ?>
    </details>
    <p class="my-2 text-sm text-amber-900" role="status" x-show="analysisFactorsPending()">Selección modificada: pendiente de aplicar al modelo. El resultado anterior se conserva en el historial.</p>
    <button type="button" class="btn-secondary mt-3" :disabled="!analysisRegimeApplied || analysisScope!==analysisAppliedScope" @click="analysisUpdate();if(!analysisError)regressionConfirmed=false;$dispatch('input')">Aplicar selección al modelo</button>
    <p class="mt-2 text-sm" x-show="!analysisRegimeApplied || analysisScope!==analysisAppliedScope">Aplica primero el grupo en Verificación de las muestras → Revisar composición del grupo.</p>
    <p class="text-red-700" role="alert" x-show="analysisError" x-text="analysisError"></p>
</fieldset>
