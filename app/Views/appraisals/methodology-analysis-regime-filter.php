<div class="mb-3 rounded-xl border p-3">
    <p class="text-sm" x-text="'Régimen del sujeto: '+(analysisSubjectRegime==='si'?'PH':analysisSubjectRegime==='no'?'No PH':'sin definir')"></p>
    <label class="label">Muestras para trabajar<select class="input" x-model="analysisScope"><option value="subject">Priorizar el régimen del sujeto, incluidos indicios publicados</option><option value="all">Ver todas · ampliar o revisar pendientes</option></select></label>
    <p class="mt-2 text-sm" x-text="analysisActiveRows().length+' de '+analysisRows.length+' muestras en el grupo · '+analysisMatches().length+' compatibles o con indicios del régimen del sujeto'"></p>
    <p class="text-sm text-amber-900" x-show="!analysisSubjectRegime">Define el régimen del sujeto para priorizar muestras. Se muestran todas.</p>
    <p class="text-sm text-amber-900" x-show="analysisSubjectRegime && !analysisMatches().length">No hay muestras identificadas ni con indicios del régimen del sujeto. Se muestran todas para revisar o ampliar la búsqueda.</p>
    <p class="mt-1 text-xs">Área privada publicada sugiere PH; no lo acredita. El ascensor no determina el régimen. Las otras muestras se conservan al cambiar el filtro. Ley 675, arts. 3–4: indicio publicitario, soporte jurídico pendiente.</p>
</div>
