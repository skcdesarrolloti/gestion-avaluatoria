export function quantile(sorted,q) {
    const i=(sorted.length-1)*q,a=Math.floor(i);return sorted[a]+(sorted[Math.ceil(i)]-sorted[a])*(i-a);
}
export function distribution(values) {
    const a=[...values].sort((x,y)=>x-y),n=a.length,mean=a.reduce((s,v)=>s+v,0)/n;
    const ss=a.reduce((s,v)=>s+(v-mean)**2,0),sd=n>1?Math.sqrt(ss/(n-1)):null;
    const q1=quantile(a,.25),q3=quantile(a,.75),iqr=q3-q1;
    const skew=n>2&&sd>0?n/((n-1)*(n-2))*a.reduce((s,v)=>s+((v-mean)/sd)**3,0):null;
    const kurt=n>3&&sd>0?n*(n+1)/((n-1)*(n-2)*(n-3))*a.reduce((s,v)=>s+((v-mean)/sd)**4,0)-3*(n-1)**2/((n-2)*(n-3)):null;
    // Excel TRIMMEAN: remove an even total count, half from each tail (40% total).
    const trim=Math.floor(n*.4/2),central=a.slice(trim,n-trim);
    const estimators=[['Media',mean],['Mediana',quantile(a,.5)],['Media acotada (40% total)',central.reduce((s,v)=>s+v,0)/central.length],
        ['Media geométrica',a.every(v=>v>0)?Math.exp(a.reduce((s,v)=>s+Math.log(v),0)/n):null]];
    return {n,mean,median:quantile(a,.5),sd,cv:mean!==0&&sd!==null?100*sd/Math.abs(mean):null,skew,kurt,q1,q3,lower:q1-1.5*iqr,upper:q3+1.5*iqr,
        estimators:estimators.map(([label,value])=>({label,value,mape:value!==null&&a.every(v=>v!==0)?100*a.reduce((s,v)=>s+Math.abs((v-value)/v),0)/n:null}))};
}
export function correlation(a,b){const n=a.length,ma=a.reduce((s,v)=>s+v,0)/n,mb=b.reduce((s,v)=>s+v,0)/n;
    const aa=a.reduce((s,v)=>s+(v-ma)**2,0),bb=b.reduce((s,v)=>s+(v-mb)**2,0);
    return aa>0&&bb>0?a.reduce((s,v,i)=>s+(v-ma)*(b[i]-mb),0)/Math.sqrt(aa*bb):null;
}
