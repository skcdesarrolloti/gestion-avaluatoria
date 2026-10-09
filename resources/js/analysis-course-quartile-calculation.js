import {quantile} from './analysis-distribution.js';
export function courseQuartileCalculation(rows){
    const ordered=[...rows].sort((a,b)=>a.y-b.y).map((r,i)=>({...r,position:i+1})),n=ordered.length;
    if(!n)return {ordered,cuts:[]};
    const values=ordered.map(r=>r.y),cuts=[.25,.5,.75].map((p,i)=>{
        const index=(n-1)*p,lo=Math.floor(index),hi=Math.ceil(index),fraction=index-lo;
        return {label:'Q'+(i+1),p,n,position:index+1,lower:ordered[lo],upper:ordered[hi],fraction,value:quantile(values,p)};
    });
    return {ordered,cuts};
}
export function courseQuartileOperation(c,fmt){
    return c.label+' = '+fmt(c.lower.y)+' + '+fmt(c.fraction)+' × ('+fmt(c.upper.y)+' − '+fmt(c.lower.y)+') = '+fmt(c.value)+' COP/m²';
}
export function courseQuartileLimitOperations(s,fmt){
    const ric=s.q3-s.q1;
    return ['RIC = '+fmt(s.q3)+' − '+fmt(s.q1)+' = '+fmt(ric)+' COP/m²',
        'Límite inferior = '+fmt(s.q1)+' − 1,5 × '+fmt(ric)+' = '+fmt(s.lower)+' COP/m²',
        'Límite superior = '+fmt(s.q3)+' + 1,5 × '+fmt(ric)+' = '+fmt(s.upper)+' COP/m²'];
}
