<template x-if="isFactor(key)"><div>
    <p class="text-xs" x-text="`Comparable: ${assessmentCode(comparisonGroup.id,key)}`"></p>
    <p class="text-xs" x-show="evidence.subjectCaptureKeys?.includes(key)">El dato del sujeto se actualiza en 3.4 → Factores para investigación.</p>
    <details class="mt-2"><summary class="min-h-11 cursor-pointer text-xs font-semibold">Calificar con soporte</summary><div class="space-y-3">
    <template x-for="target in assessmentTargets(key)" :key="target"><div class="rounded border p-2">
        <p class="text-xs font-semibold" x-text="target==='subject'?'Sujeto · referencia':'Comparable · inmueble vinculado'"></p>
        <label class="mt-2 block text-xs font-semibold">Calificación / medida
            <template x-if="plan.factors[key].kind==='numeric'"><input class="input mt-1" type="text" inputmode="decimal" :value="assessment(target,key).value" @input="setAssessment(target,key,'value',$event.target.value)" maxlength="120" placeholder="Cantidad comprobada; vacío si desconocida" :aria-label="`Calificación de ${factorLabel(key)} · ${target==='subject'?'sujeto':'comparable'}`"></template>
            <template x-if="plan.factors[key].kind!=='numeric'"><select class="input mt-1" :value="assessment(target,key).value" @change="setAssessment(target,key,'value',$event.target.value)" :aria-label="`Calificación de ${factorLabel(key)} · ${target==='subject'?'sujeto':'comparable'}`">
                <option value="">Selecciona según evidencia</option><template x-for="option in gradeOptions(key)" :key="option.value"><option :value="option.value" :selected="assessment(target,key).value===option.value" x-text="option.caption"></option></template>
            </select></template>
        </label>
        <p class="mt-1 text-xs" x-text="assessmentCode(target,key)"></p>
        <details><summary class="min-h-11 cursor-pointer text-xs">Fuente y soporte de la calificación</summary>
            <label class="block text-xs font-semibold">Soporte<textarea class="input" rows="2" maxlength="600" :value="assessment(target,key).support" @input="setAssessment(target,key,'support',$event.target.value)" placeholder="Fuente, fecha, contacto o evidencia que respalda esta calificación" :aria-label="`Soporte de ${factorLabel(key)} · ${target==='subject'?'sujeto':'comparable'}`"></textarea></label>
        </details>
        <p class="text-xs" x-text="assessmentStatus(target,key)"></p>
    </div></template>
</div></details></div></template>
<span x-show="!isFactor(key)" class="text-xs">Base en m² para COP/m²; fuera de los factores candidatos.</span>
