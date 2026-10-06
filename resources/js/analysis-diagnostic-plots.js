import {escapeReport as esc} from './analysis-download.js';
const fmt=v=>Number(v).toLocaleString('es-CO',{maximumFractionDigits:2});
const tick=v=>Math.abs(v)>=10000?Number(v).toExponential(1):fmt(v);
const frame=(title,body)=>'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 680 400" role="img" aria-label="'+esc(title)+'"><title>'+esc(title)+'</title><rect width="680" height="400" fill="white"/><g font-family="Arial" font-size="12" fill="#1e293b"><text x="65" y="25" font-size="16">'+esc(title)+'</text>'+body+'</g></svg>';
function scatter(title,points,xlabel,ylabel,{zero=false,diagonal=false}={}) {
    const xs=points.map(p=>p.x),ys=points.map(p=>p.y);let minX=Math.min(...xs),maxX=Math.max(...xs),minY=Math.min(...ys),maxY=Math.max(...ys);
    if(zero){minY=Math.min(0,minY);maxY=Math.max(0,maxY);}if(diagonal){minX=minY=Math.min(minX,minY);maxX=maxY=Math.max(maxX,maxY);}
    const dx=maxX-minX||1,dy=maxY-minY||1;minX-=dx*.05;maxX+=dx*.05;minY-=dy*.08;maxY+=dy*.08;
    const sx=v=>65+(v-minX)/(maxX-minX)*555,sy=v=>330-(v-minY)/(maxY-minY)*280;
    let body='<path d="M65 50 V330 H620" fill="none" stroke="#64748b"/>';
    for(let i=0;i<=4;i++){const x=minX+(maxX-minX)*i/4,y=minY+(maxY-minY)*i/4;
        body+='<path d="M65 '+sy(y)+' H620" stroke="#e2e8f0"/><text x="60" y="'+(sy(y)+4)+'" text-anchor="end">'+tick(y)+'</text><text x="'+sx(x)+'" y="350" text-anchor="middle">'+tick(x)+'</text>';}
    if(zero)body+='<path d="M65 '+sy(0)+' H620" stroke="#b45309" stroke-dasharray="5 4"/>';
    if(diagonal)body+='<path d="M'+sx(minX)+' '+sy(minX)+' L'+sx(maxX)+' '+sy(maxX)+'" stroke="#64748b" stroke-dasharray="5 4"/>';
    body+=points.map(p=>'<circle cx="'+sx(p.x)+'" cy="'+sy(p.y)+'" r="5" fill="'+(p.flag?'#be123c':'#0f766e')+'"><title>'+esc(p.label+' · x='+fmt(p.x)+' · y='+fmt(p.y))+'</title></circle>'+(p.flag?'<text x="'+(sx(p.x)+6)+'" y="'+(sy(p.y)-5)+'">'+esc(p.label.replace('Muestra ',''))+'</text>':'')).join('');
    return frame(title,body+'<text x="340" y="385" text-anchor="middle">'+esc(xlabel)+'</text><text x="18" y="185" transform="rotate(-90 18 185)" text-anchor="middle">'+esc(ylabel)+'</text>');
}
function histogram(title,values) {
    const bins=Math.max(1,Math.ceil(Math.log2(values.length)+1)),min=Math.min(...values),max=Math.max(...values),width=(max-min||1)/bins,counts=Array(bins).fill(0);
    values.forEach(v=>counts[Math.min(bins-1,Math.floor((v-min)/width))]++);const top=Math.max(...counts);
    const bars=counts.map((n,i)=>'<rect x="'+(65+i*555/bins)+'" y="'+(330-n/top*280)+'" width="'+(555/bins-2)+'" height="'+(n/top*280)+'" fill="#0f766e"><title>'+fmt(min+i*width)+' a '+fmt(min+(i+1)*width)+' · '+n+' observaciones</title></rect><text x="'+(65+(i+.5)*555/bins)+'" y="'+(322-n/top*280)+'" text-anchor="middle">'+n+'</text>').join('');
    return frame(title,bars+'<path d="M65 50 V330 H620" fill="none" stroke="#64748b"/><text x="65" y="352">'+fmt(min)+'</text><text x="620" y="352" text-anchor="end">'+fmt(max)+'</text><text x="340" y="385" text-anchor="middle">COP/m² · frecuencia por intervalo</text>');
}
function boxplot(cases,s) {
    const values=cases.map(c=>c.y),min=Math.min(...values),max=Math.max(...values),span=max-min||1,sx=v=>65+(v-min)/span*555;
    const inner=values.filter(v=>v>=s.lower&&v<=s.upper),lo=Math.min(...inner),hi=Math.max(...inner);
    let body='<path d="M'+sx(lo)+' 180 H'+sx(hi)+' M'+sx(lo)+' 155 V205 M'+sx(hi)+' 155 V205" stroke="#475569"/><rect x="'+sx(s.q1)+'" y="135" width="'+(sx(s.q3)-sx(s.q1))+'" height="90" fill="#ccfbf1" stroke="#0f766e"/><path d="M'+sx(s.median)+' 135 V225" stroke="#0f766e" stroke-width="3"/>';
    body+=cases.filter(c=>c.y<s.lower||c.y>s.upper).map(c=>'<circle cx="'+sx(c.y)+'" cy="180" r="6" fill="#be123c"><title>'+esc(c.label+' · '+fmt(c.y))+'</title></circle><text x="'+sx(c.y)+'" y="120" text-anchor="middle">'+esc(c.label.replace('Muestra ',''))+'</text>').join('');
    for(let i=0;i<=4;i++){const v=min+span*i/4;body+='<text x="'+sx(v)+'" y="300" text-anchor="middle">'+tick(v)+'</text>';}
    return frame('Caja y bigotes del valor por m²',body+'<text x="340" y="355" text-anchor="middle">COP/m² · caja: Q1–Q3 · línea: mediana</text><text x="340" y="380" text-anchor="middle">Bigotes: extremos dentro de 1,5 RIC · puntos: candidatos</text>');
}
// Inverse standard normal by bisection of the A&S normal CDF approximation.
export function normalQuantile(p){let lo=-8,hi=8;for(let i=0;i<50;i++){const x=(lo+hi)/2,t=1/(1+.2316419*Math.abs(x));
    const tail=Math.exp(-x*x/2)/Math.sqrt(2*Math.PI)*t*(.319381530+t*(-.356563782+t*(1.781477937+t*(-1.821255978+t*1.330274429))));
    const cdf=x>=0?1-tail:tail;if(cdf<p)lo=x;else hi=x;}return (lo+hi)/2;}
export function diagnosticPlots(result) {
    const d=result.diagnostics,cases=d.cases,point=(c,x,y)=>({x,y,label:c.label,flag:c.flags.length>0});
    const plots=[{title:'Distribución del valor por m²',svg:histogram('Distribución del valor por m²',cases.map(c=>c.y))},
        {title:'Caja y bigotes del valor por m²',svg:boxplot(cases,d.summary)},
        {title:'Observado frente a estimado',svg:scatter('Observado frente a estimado',cases.map(c=>point(c,c.fitted,c.y)),'Estimado · COP/m²','Observado · COP/m²',{diagonal:true})},
        {title:'Residuos frente a estimados',svg:scatter('Residuos frente a estimados',cases.map(c=>point(c,c.fitted,c.residual)),'Estimado · COP/m²','Residuo · COP/m²',{zero:true})},
        {title:'Distribución de residuos',svg:histogram('Distribución de residuos',cases.map(c=>c.residual))}];
    const sorted=[...cases].sort((a,b)=>a.residual-b.residual);
    plots.push({title:'Q-Q normal de residuos',svg:scatter('Q-Q normal de residuos',sorted.map((c,i)=>point(c,normalQuantile((i+.5)/cases.length),c.residual)),'Cuantil normal teórico','Residuo ordenado · COP/m²')});
    result.matrix.cols.forEach((f,j)=>plots.push({title:f.label+' frente a valor por m²',svg:scatter(f.label+' frente a valor por m²',cases.map(c=>point(c,c.x[j],c.y)),f.label,'Observado · COP/m²')}));
    return plots;
}
export function mountDiagnosticSvg(element,svg){const node=new DOMParser().parseFromString(svg,'image/svg+xml').documentElement;element.replaceChildren(document.importNode(node,true));}
