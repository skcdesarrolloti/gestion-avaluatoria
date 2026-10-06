import {sourceFacts} from './comparable-source-facts.js';
import {modelArea,modelFactorCount,positiveModelArea} from './analysis-model-area.js';
import {amount} from './comparable-negotiation.js';
import {portalTable} from './comparable-portal-table.js';
export function statistics(values) {
    values=values.filter(Number.isFinite).sort((a,b)=>a-b);
    const n=values.length, mean=n?values.reduce((a,b)=>a+b,0)/n:null;
    const median=n?(values[Math.floor(n/2)]+values[Math.floor((n-1)/2)])/2:null;
    const sd=n>1?Math.sqrt(values.reduce((s,v)=>s+(v-mean)**2,0)/(n-1)):null;
    return {n,mean,median,sd,cv:sd!==null && mean!==0?sd/Math.abs(mean)*100:null};
}
export function workingRows(state) {
    const matches=state.analysisMatches();
    if(!state.analysisRegimeApplied || state.analysisAppliedScope!=='subject' || !matches.length)return state.analysisRows;
    const ids=new Set([...matches.map(r=>r.id),...state.analysisReview.filter(v=>v.restored_at).map(v=>v.id)]);
    return state.analysisRows.filter(r=>ids.has(r.id));
}
export function snapshot(state,action,rows,changed='') {
    if(state.analysisStatistics.length>=80)throw new Error('history limit');
    const hasFactors=action==='factors' || (action!=='initial' && state.analysisStatistics.some(v=>v.action==='factors'));
    const factors=hasFactors?[...state.analysisApplied]:[];
    const table=portalTable(rows,true),columns=factors.map(key=>table.columns.find(c=>c.key===key) || {key}),area=modelArea(columns), missing=v=>v===undefined || v===null || /^(?:\s*|no publicado|pendiente|por confirmar)$/i.test(String(v).trim());
    const eligible=hasFactors?rows.filter((r,i)=>amount(r.area_m2)>0 && positiveModelArea(table.rows[i].values[area.key]) && factors.every(k=>!missing(table.rows[i].values[k]))):rows;
    const complete=hasFactors?eligible.length:null;
    const simulated=eligible.some(r=>Object.values(sourceFacts(r.analysis_manual_factors)).some(e=>/^EJEMPLO SIMULADO\b/i.test(e.source || '')));
    const offer=eligible.map(r=>{const p=amount(r.price_amount),a=amount(r.area_m2);return p!==null && p>0 && a>0?(r.price_unit==='valor_m2'?p:p/a):null;});
    const adjusted=eligible.map(r=>{const result=state.analysisResult(r.id);return r.price_unit==='valor_m2'?result.value:result.perM2;});
    state.analysisStatistics.push({at:new Date().toISOString(),action,changed,scope:state.analysisAppliedScope,regime:state.analysisSubjectRegime,count:rows.length,factors,complete,...(simulated?{simulated:true}:{}),...(hasFactors?{model_area:area.key,factor_count:modelFactorCount(columns)}:{}),offer:statistics(offer),adjusted:statistics(adjusted)});
}
export function ensureHistory(state) {
    if(state.analysisStatistics.length)return;
    snapshot(state,'initial',state.analysisRows);
    if(state.analysisRegimeApplied)snapshot(state,'filter',workingRows(state));
}
export function requireHistorySpace(state) {
    const needed=state.analysisStatistics.length?1:state.analysisRegimeApplied?3:2;
    if(state.analysisStatistics.length+needed>80)throw new Error('history limit');
}
