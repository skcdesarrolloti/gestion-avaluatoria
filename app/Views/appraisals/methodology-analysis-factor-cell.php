<p class="whitespace-pre-wrap" x-text="property.values[factor.key] ?? 'Dato faltante'"></p>
<p x-show="analysisView==='result'" class="mt-1 text-xs font-semibold" x-text="analysisCellState(property,factor)==='missing' ? 'Falta completar o verificar' : analysisCellState(property,factor)==='example' ? 'Ejemplo simulado · no verificado' : analysisCellState(property,factor)==='manual' ? 'Complementado por el analista' : 'Dato recogido'"></p>
<div x-show="analysisView==='result' && analysisCanEdit(property,factor.key)">
    <label class="mt-2 block text-xs" :for="'manual-value-'+property.key+'-'+factor.key" x-text="factor.label+' · dato manual'"></label>
    <input class="input w-52" :id="'manual-value-'+property.key+'-'+factor.key" maxlength="500" placeholder="Digita el dato verificado" :value="analysisManualEntry(property.key,factor.key).value ?? ''" @input="analysisEdit(property,factor,'value',$event.target.value)">
    <label class="mt-2 block text-xs" :for="'manual-source-'+property.key+'-'+factor.key">Fuente, fecha y responsable</label>
    <input class="input w-52" :id="'manual-source-'+property.key+'-'+factor.key" maxlength="1600" placeholder="Ej. ficha consultada, fecha y analista" :value="analysisManualEntry(property.key,factor.key).source ?? ''" @input="analysisEdit(property,factor,'source',$event.target.value)">
    <p class="mt-1 text-xs" x-show="analysisManualEntry(property.key,factor.key).value && !analysisManualEntry(property.key,factor.key).source">Registra el soporte para que este dato cuente como completo.</p>
</div>
