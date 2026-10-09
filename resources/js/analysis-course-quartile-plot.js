import {escapeReport as esc} from './analysis-download.js';
export function courseQuartileRanges(s){return [[s.min,s.q1],[s.q1,s.median],[s.median,s.q3],[s.q3,s.max]].map(([from,to],i)=>({label:'Tramo '+(i+1),from,to,percent:(i*25)+'–'+((i+1)*25)+' %'}));}
export function courseQuartileBar(s){
    const values=[s.min,s.q1,s.median,s.q3,s.max],labels=['Mínimo','Q1 · 25 %','Q2 · mediana','Q3 · 75 %','Máximo'],span=s.max-s.min;
    const x=v=>span?70+(v-s.min)/span*720:430,fmt=v=>v.toLocaleString('es-CO',{maximumFractionDigits:2});
    let body='<rect x="70" y="80" width="720" height="40" fill="#e2e8f0" stroke="#64748b"/>';
    const colors=['#e0f2fe','#99f6e4','#5eead4','#fef3c7'];
    body+=courseQuartileRanges(s).map((r,i)=>'<rect x="'+x(r.from)+'" y="80" width="'+(x(r.to)-x(r.from))+'" height="40" fill="'+colors[i]+'"><title>'+esc(r.label+' · '+fmt(r.from)+' a '+fmt(r.to)+' COP/m²')+'</title></rect>').join('');
    body+=values.map((v,i)=>{const col=70+i*180;return '<path d="M'+x(v)+' 75 V125 L'+col+' 170" fill="none" stroke="'+(i===2?'#1d4ed8':'#475569')+'" stroke-width="'+(i===2?3:1)+'"/><text x="'+col+'" y="190" text-anchor="middle">'+labels[i]+'</text><text x="'+col+'" y="215" text-anchor="middle" font-weight="bold">'+fmt(v)+'</text>';}).join('');
    body+='<path d="M'+x(s.q1)+' 60 V50 H'+x(s.q3)+' V60" fill="none" stroke="#0f766e" stroke-width="2"/><text x="430" y="35" text-anchor="middle">'+(span?'Tramo central Q1–Q3 · aproximadamente 50 % de las observaciones':'Serie constante · mínimo, cuartiles y máximo coinciden')+'</text>';
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 860 275" role="img" aria-label="Barra horizontal de cuartiles del valor unitario"><title>Barra horizontal de cuartiles del valor unitario</title><rect width="860" height="275" fill="white"/><g font-family="Arial" font-size="14" fill="#1e293b">'+body+'<text x="430" y="255" text-anchor="middle">Valores en COP/m² · la longitud representa distancia de precios, no cantidad de inmuebles</text></g></svg>';
}
