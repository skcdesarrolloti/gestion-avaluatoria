// Alpine gets one deep watcher for input changes, instead of scanning every source per cell.
export function cacheAnalysisReads(state) {
    const methods=['analysisMatches','analysisActiveRows','analysisVisibleRows','analysisColumns','analysisComplete',
        'analysisDisplayRows','analysisHasSimulated','analysisFactorsPending','analysisCombinations',
        'regressionColumns','regressionCategories','regressionMatrix','regressionStamp','courseStamp','analysisMapPoints','analysisMapCaptureRows'];
    const caches=new Map();
    for(const name of methods){const method=state[name];state[name]=function(...args){
        if(!this.analysisReactiveCache)return method.apply(this,args);
        const stamp=JSON.stringify([this.analysisDataRevision,this.analysisView,this.analysisModule,this.analysisAppliedScope,
            this.analysisRegimeApplied,this.analysisSubjectRegime,this.analysisSelected,this.analysisApplied,
            this.analysisReview,this.analysisOnlyMissing,this.analysisEditingId,this.analysisMapOnlyPending,this.analysisMapEditingId,
            this.regressionBasis,this.regressionCodes,this.analysisDiscounts,this.analysisSubjectLocation,this.courseBasis,this.courseConfidence]);
        const entry=caches.get(name);if(entry?.stamp===stamp)return entry.value;
        const value=method.apply(this,args);caches.set(name,{stamp,value});return value;
    };}
    return state;
}
