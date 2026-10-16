// Read-only workings from the saved execution, never from newer unsaved inputs.
export function courseCalculation(result, label = '') {
    if (!result?.valid?.length) return null;
    const ordered = result.valid.map((r, i) => ({...r, index:i})).sort((a,b)=>a.y-b.y||a.index-b.index);
    const estimator = result.summary.estimators.find(e=>e.label===label)
        || result.summary.estimators.filter(e=>Number.isFinite(e.mape)).sort((a,b)=>a.mape-b.mape)[0]
        || result.summary.estimators[0];
    const n = ordered.length, trim = Math.floor(n*.2), middle = ordered.slice(trim,n-trim);
    const rows = ordered.map((r,i)=>{
        const absolute = Number.isFinite(estimator?.value)?Math.abs(r.y-estimator.value):null;
        return {...r,position:i+1,absolute,percent:absolute!==null&&r.y!==0?100*absolute/Math.abs(r.y):null};
    });
    const sumPercent = rows.every(r=>r.percent!==null)?rows.reduce((sum,r)=>sum+r.percent,0):null;
    const meanSum = ordered.reduce((sum,r)=>sum+r.y,0), trimmedSum = middle.reduce((sum,r)=>sum+r.y,0);
    const deviations = ordered.reduce((sum,r)=>sum+(r.y-result.summary.mean)**2,0);
    return {rows,estimator,n,sumPercent,mape:sumPercent===null?null:sumPercent/n,meanSum,trim,trimmedSum,
        trimmedValue:result.summary.estimators.find(e=>e.label==='Media acotada (40% total)')?.value??null,
        geometricValue:result.summary.estimators.find(e=>e.label==='Media geométrica')?.value??null,
        trimmedCount:middle.length,trimmedFirst:trim+1,trimmedLast:n-trim,
        medianLow:ordered[Math.floor((n-1)/2)],medianHigh:ordered[Math.ceil((n-1)/2)],
        medianLowPosition:Math.floor((n-1)/2)+1,medianHighPosition:Math.ceil((n-1)/2)+1,
        logSum:ordered.every(r=>r.y>0)?ordered.reduce((sum,r)=>sum+Math.log(r.y),0):null,
        min:ordered[0],max:ordered[n-1],deviations};
}
