import {escapeReport as esc} from './analysis-download.js';
import {outsideCourseLimits} from './analysis-course-dispersion-plot.js';
export function courseQuartileRanges(s){return [[s.min,s.q1],[s.q1,s.median],[s.median,s.q3],[s.q3,s.max]].map(([from,to],i)=>({label:'Tramo '+(i+1),from,to,percent:(i*25)+'–'+((i+1)*25)+' %'}));}
export function courseQuartileBar(s,rows=[]){
    const values=[s.min,s.q1,s.median,s.q3,s.max],labels=['Mínimo','Q1 · 25 %','Q2 · mediana','Q3 · 75 %','Máximo'],span=s.max-s.min;
    const x=v=>span?70+(v-s.min)/span*720:430,fmt=v=>v.toLocaleString('es-CO',{maximumFractionDigits:2});
    const stacks=new Map(),points=rows.map(r=>{const bucket=Math.round(x(r.y)/10),level=stacks.get(bucket)||0;stacks.set(bucket,level+1);return {r,x:x(r.y),y:145+level*11};});
    const bottom=Math.max(145,...points.map(p=>p.y)),height=bottom+150;
    let body='<rect x="70" y="80" width="720" height="40" fill="#e2e8f0" stroke="#64748b"/>';
    const colors=['#e0f2fe','#99f6e4','#5eead4','#fef3c7'];
    body+=courseQuartileRanges(s).map((r,i)=>'<rect x="'+x(r.from)+'" y="80" width="'+(x(r.to)-x(r.from))+'" height="40" fill="'+colors[i]+'"><title>'+esc(r.label+' · '+fmt(r.from)+' a '+fmt(r.to)+' COP/m²')+'</title></rect>').join('');
    body+=values.map((v,i)=>{const col=70+i*180;return '<path d="M'+x(v)+' 75 V'+(bottom+15)+' L'+col+' '+(bottom+40)+'" fill="none" stroke="'+(i===2?'#1d4ed8':'#475569')+'" stroke-width="'+(i===2?3:1)+'"/><text x="'+col+'" y="'+(bottom+60)+'" text-anchor="middle">'+labels[i]+'</text><text x="'+col+'" y="'+(bottom+85)+'" text-anchor="middle" font-weight="bold">'+fmt(v)+'</text>';}).join('');
    body+=points.map(p=>{const outside=outsideCourseLimits(p.r,s),color=outside?'#be123c':'#047857',title=esc(p.r.label+' · '+fmt(p.r.y)+' COP/m² · '+(outside?'fuera de límites exploratorios':'dentro de límites exploratorios'));return '<g tabindex="0" role="img" aria-label="'+title+'"><title>'+title+'</title><circle cx="'+p.x+'" cy="'+p.y+'" r="4" fill="'+color+'"/>'+(outside?'<path d="M'+(p.x-6)+' '+(p.y-6)+' l12 12 m-12 0 l12 -12" stroke="'+color+'" stroke-width="2"/>':'')+'</g>';}).join('');
    body+='<path d="M'+x(s.q1)+' 60 V50 H'+x(s.q3)+' V60" fill="none" stroke="#0f766e" stroke-width="2"/><text x="430" y="35" text-anchor="middle">'+(span?'Tramo central Q1–Q3 · aproximadamente 50 % de las observaciones':'Serie constante · mínimo, cuartiles y máximo coinciden')+'</text>';
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 860 '+height+'" role="img" aria-label="Barra horizontal de cuartiles del valor unitario"><title>Barra horizontal de cuartiles del valor unitario</title><rect width="860" height="'+height+'" fill="white"/><g font-family="Arial" font-size="14" fill="#1e293b">'+body+'<text x="430" y="'+(bottom+120)+'" text-anchor="middle">COP/m² · cada punto representa un inmueble · la altura solo separa puntos próximos</text></g></svg>';
}
