// Loaded only when the analyst applies a sample filter or restores a sample.
export async function applyFilter(state) {
    const scope=state.analysisScope, regime=state.analysisSubjectRegime, matched=[], retired=[], at=new Date().toISOString();
    for(let i=0;i<state.analysisRows.length;i++) {
        const row=state.analysisRows[i], effective=state.analysisEffectiveRegime(row);
        if(regime && effective===regime) matched.push(row.id);
        else retired.push({id:row.id,reason:effective?'other':'unknown',at,restored_at:''});
        state.analysisProcessed=i+1;
        if(i%10===0) await new Promise(resolve=>typeof requestAnimationFrame==='function'?requestAnimationFrame(resolve):setTimeout(resolve,0));
    }
    if(scope==='subject' && matched.length) {
        const previous=new Map(state.analysisReview.map(v=>[v.id,v]));
        retired.forEach(v=>{if(!previous.has(v.id))previous.set(v.id,v);});
        state.analysisReview=[...previous.values()];
    }
    state.analysisAppliedScope=scope;state.analysisRegimeApplied=true;state.analysisFilterDone=true;
}
export function restoreSample(state,id,checked) {
    const review=state.analysisReview.find(v=>v.id===id);
    if(review && state.analysisRows.some(r=>r.id===id)) review.restored_at=checked?new Date().toISOString():'';
}
