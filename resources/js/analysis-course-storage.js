import {courseSummary} from './analysis-course-math.js';
import {courseHistogram} from './analysis-course-plots.js';
export function courseSnapshot(state){
    const r=state.courseResult;if(!r)return '';
    const summary=Object.fromEntries(Object.entries(r.summary).filter(([k,v])=>v===null||typeof v==='number'||['estimators','modes'].includes(k)));
    const rows=r.rows.map(v=>Object.fromEntries(['id','label','y','reasons','area','offer','discount','final','areaNote','regime'].map(k=>[k,v[k]??(['reasons'].includes(k)?[]:null)])));
    return JSON.stringify({v:1,basis:state.courseBasis,confidence:Number(state.courseConfidence),step:state.courseStep,notes:state.courseNotes,conclusion:state.courseConclusion,
        result:{rows,summary,stamp:r.stamp,at:r.at,basis:r.basis,simulated:r.simulated,bootstrap:r.bootstrap,reconstructed:!!r.reconstructed}});
}
export function restoreCourseSnapshot(text){
    if(!text)return null;
    const saved=JSON.parse(text),r=saved.result,valid=r.rows.filter(v=>!v.reasons.length&&Number.isFinite(v.y));
    if(saved.v!==1||valid.length<2)throw new Error('Ejecución estadística guardada inválida.');
    const summary={...courseSummary(valid,r.summary.confidence),...r.summary};
    return {...saved,result:{...r,valid,pending:r.rows.filter(v=>!valid.includes(v)),summary,histogram:courseHistogram(summary.classes)}};
}
