const areaKeys = ['area','land','built','destination'];
export const researchFactor = key => !areaKeys.includes(key);
export function usableAssessment(item, factor, basis) {
    return Boolean(item?.value !== '' && item?.value !== undefined && item.support?.trim() && basis && (Array.isArray(basis)?basis.includes(item.basis):item.basis === basis) && item.scale_kind === factor.kind && item.scale_categories === factor.categories);
}
export const assessmentBasis = (group,key) => [group?.factorSignatures?.[key],group?.signature].filter(Boolean);
export const subjectBasis = (evidence,key) => [evidence.subjectFactorSignatures?.[key],evidence.subjectSignature].filter(Boolean);
export const assessmentMethods = {
    assessmentTargets(key) { return [this.evidence.subjectCaptureKeys?.includes(key)?null:'subject',this.comparisonGroup.id].filter(Boolean); },
    assessment(id,key) { return this.plan.assessments?.[id]?.[key] || {value:'',support:''}; },
    gradeOptions(key) { return String(this.plan.factors[key].categories || '').split('\n').filter(Boolean).map((label,i)=>({label,value:label,caption:this.scaleValid(key) && this.plan.factors[key].kind==='ordinal'?`${i} = ${label}`:label})); },
    setAssessment(id,key,field,value) {
        this.plan.assessments ??= {}; this.plan.assessments[id] ??= {};
        const item=this.plan.assessments[id][key] ??= {value:'',support:''};
        item[field]=value;
        if (field==='value' || !item.basis) item.basis=(id==='subject'?subjectBasis(this.evidence,key):assessmentBasis(this.evidence.groups.find(g=>g.id===id),key))[0] || '';
        if (field==='value' || !item.scale_kind) { item.scale_kind=this.plan.factors[key].kind; item.scale_categories=this.plan.factors[key].categories; }
    },
    assessmentCode(id,key) {
        const item=this.assessment(id,key), factor=this.plan.factors[key];
        return item.value && (item.scale_kind!==factor.kind || item.scale_categories!==factor.categories)?'Código: pendiente por cambio de escala':this.codeLabel(key,item.value);
    },
    assessmentStatus(id,key) {
        if (!this.scaleValid(key)) return 'Pendiente: clasificación anterior por revisar; conserva el dato y su soporte.';
        const item=this.assessment(id,key); if (!item.value) return 'Sin calificación manual';
        const basis=id==='subject'?subjectBasis(this.evidence,key):assessmentBasis(this.evidence.groups.find(g=>g.id===id),key);
        return usableAssessment(item,this.plan.factors[key],basis)?'Registrado por el analista · soporte indicado':'Pendiente: completa soporte o vuelve a calificar tras cambios de dato/escala';
    },
    adoptScale(key) {
        const factor=this.catalog[key]; Object.assign(this.plan.factors[key],{kind:factor.kind,categories:factor.categories,definition:factor.why});
    },
};
