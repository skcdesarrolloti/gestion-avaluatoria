// Householder QR on standardized predictors; never form X'X for the calculation.
export function linearRegression(x,y) {
    const n=x.length,k=x[0]?.length || 0,p=k+1;
    if(n<=p || y.length!==n || x.some(r=>r.length!==k || r.some(v=>!Number.isFinite(v))) || y.some(v=>!Number.isFinite(v))) throw new Error('Matriz numérica inválida o insuficiente.');
    const means=Array.from({length:k},(_,j)=>x.reduce((s,r)=>s+r[j],0)/n);
    const scales=means.map((m,j)=>Math.sqrt(x.reduce((s,r)=>s+(r[j]-m)**2,0)/n));
    if(scales.some(s=>s===0))throw new Error('Hay un factor sin variación.');
    const a=x.map(r=>[1,...r.map((v,j)=>(v-means[j])/scales[j])]),b=[...y];
    for(let j=0;j<p;j++) {
        const v=a.slice(j).map(r=>r[j]),norm=Math.hypot(...v);
        if(norm<1e-9)throw new Error('Factores dependientes: la matriz no tiene rango completo.');
        v[0]+=v[0]>=0?norm:-norm;const vv=v.reduce((s,t)=>s+t*t,0);
        for(let c=j;c<p;c++){const d=2*v.reduce((s,t,i)=>s+t*a[j+i][c],0)/vv;v.forEach((t,i)=>a[j+i][c]-=d*t);}
        const d=2*v.reduce((s,t,i)=>s+t*b[j+i],0)/vv;v.forEach((t,i)=>b[j+i]-=d*t);
    }
    const z=Array(p).fill(0);
    for(let j=p-1;j>=0;j--)z[j]=(b[j]-z.reduce((s,t,c)=>s+(c>j?a[j][c]*t:0),0))/a[j][j];
    const coefficients=[z[0]-means.reduce((s,m,j)=>s+m*z[j+1]/scales[j],0),...scales.map((s,j)=>z[j+1]/s)];
    const fitted=x.map(r=>coefficients[0]+r.reduce((s,v,j)=>s+v*coefficients[j+1],0)),residuals=y.map((v,i)=>v-fitted[i]);
    const sse=residuals.reduce((s,v)=>s+v*v,0),mean=y.reduce((s,v)=>s+v,0)/n,sst=y.reduce((s,v)=>s+(v-mean)**2,0);
    if(sst===0)throw new Error('El valor por m² no tiene variación.');
    const r2=1-sse/sst;
    const vif=x[0].map((_,j)=>{
        const target=j+1;
        // Diagonal of (R'R)^-1 uses row of R^-1, not column.
        let diagonal=0;
        for(let c=0;c<p;c++){
            const inv=Array(p).fill(0);
            for(let i=p-1;i>=0;i--)inv[i]=((i===c?1:0)-inv.reduce((s,t,h)=>s+(h>i?a[i][h]*t:0),0))/a[i][i];
            diagonal+=inv[target]**2;
        }
        return diagonal*n;
    });
    // h_i = ||R^-T z_i||², using the same standardized QR factors.
    const leverage=x.map(row=>{
        const z=[1,...row.map((v,j)=>(v-means[j])/scales[j])],u=Array(p).fill(0);
        for(let j=0;j<p;j++)u[j]=(z[j]-u.reduce((s,v,i)=>s+(i<j?a[i][j]*v:0),0))/a[j][j];
        return Math.min(1,Math.max(0,u.reduce((s,v)=>s+v*v,0)));
    });
    return {n,k,coefficients,fitted,residuals,r2,adjustedR2:1-(1-r2)*(n-1)/(n-p),rmse:Math.sqrt(sse/(n-p)),vif,leverage};
}
