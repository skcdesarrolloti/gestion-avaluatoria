import {distribution,correlation} from './analysis-distribution.js';
export function regressionDiagnostics(result) {
    const rows=result.matrix.complete,y=rows.map(r=>r.y),summary=distribution(y),residualSummary=distribution(result.residuals),p=result.k+1;
    const cases=rows.map((row,i)=>{
        const e=result.residuals[i],h=result.leverage[i],mse=result.rmse**2,denom=mse*(1-h);
        const student=denom>0?e/Math.sqrt(denom):null,cook=denom>0?e*e*h/(p*mse*(1-h)**2):null;
        const flags=[];if(row.y<summary.lower||row.y>summary.upper)flags.push('Precio fuera de 1,5 RIC');
        if(student!==null&&Math.abs(student)>2)flags.push('Residuo studentizado > 2');
        if(h>2*p/result.n)flags.push('Apalancamiento alto');if(cook!==null&&cook>4/result.n)flags.push('Influencia de Cook');
        return {...row,fitted:result.fitted[i],residual:e,student,leverage:h,cook,flags};
    });
    return {summary,residualSummary,cases,flagged:cases.filter(c=>c.flags.length),
        correlations:result.matrix.cols.map((c,j)=>({label:c.label,r:correlation(rows.map(r=>r.x[j]),y)})),
        mape:y.every(v=>v!==0)?100*result.residuals.reduce((s,v,i)=>s+Math.abs(v/y[i]),0)/y.length:null,
        thresholds:{cook:4/result.n,leverage:2*p/result.n},
        caveat:'Señales exploratorias para revisar, no pruebas concluyentes ni motivos automáticos de exclusión.'};
}
export function regressionEquation(result) {
    if(!result)return '';const fmt=v=>v.toLocaleString('es-CO',{maximumFractionDigits:4});
    return 'Valor estimado por m² = '+fmt(result.coefficients[0])+result.coefficients.slice(1).map((v,i)=>
        (v<0?' − ':' + ')+fmt(Math.abs(v))+' × '+result.matrix.cols[i].label).join('');
}
