import {escapeReport as esc} from './analysis-download.js';
export const outsideCourseLimits=(r,s)=>r.y<s.lower||r.y>s.upper;
export function courseDispersionPlot(rows,s){
    const values=rows.map(r=>r.y),low=Math.min(...values,s.lower),high=Math.max(...values,s.upper),span=high-low||Math.max(Math.abs(low)*.1,1),min=low-span*.1,max=high+span*.1;
    const x=i=>90+(rows.length===1?270:i/(rows.length-1)*540),y=v=>355-(v-min)/(max-min)*280;
    const millions=v=>(v/1e6).toLocaleString('es-CO',{maximumFractionDigits:3});
    let body='<path d="M90 75 V355 H630" fill="none" stroke="#64748b"/>';
    for(let i=0;i<=4;i++){const v=min+(max-min)*i/4,py=y(v);body+='<path d="M90 '+py+' H630" stroke="#e2e8f0"/><text x="80" y="'+(py+4)+'" text-anchor="end">'+millions(v)+'</text>';}
    let lineIndex=0;
    for(const [label,value,color] of [['Límite inferior',s.lower,'#b45309'],['Límite superior',s.upper,'#b45309'],['Media',s.mean,'#1d4ed8']]){
        body+='<path d="M90 '+y(value)+' H630" stroke="'+color+'" stroke-dasharray="6 4"/><text x="638" y="'+(y(value)+4+(high===low?(lineIndex++-1)*16:0))+'" fill="'+color+'">'+label+'</text>';}
    body+=rows.map((r,i)=>{const px=x(i),py=y(r.y),outside=outsideCourseLimits(r,s),label=r.label+' · '+r.y.toLocaleString('es-CO')+' COP/m² · '+(outside?'Fuera de límites exploratorios':'Dentro de límites exploratorios');
        return '<g role="img" aria-label="'+esc(label)+'"><title>'+esc(label)+'</title><circle cx="'+px+'" cy="'+py+'" r="5" fill="'+(outside?'#be123c':'#0f766e')+'"/>'+(outside?'<path d="M'+(px-7)+' '+(py-7)+' L'+(px+7)+' '+(py+7)+' M'+(px-7)+' '+(py+7)+' L'+(px+7)+' '+(py-7)+'" stroke="#be123c" stroke-width="2"/><text x="'+px+'" y="'+(py-13)+'" text-anchor="middle" fill="#be123c">'+esc(r.label)+'</text>':'')+'</g>';}).join('');
    for(const i of [...new Set([0,Math.floor((rows.length-1)/2),rows.length-1])])body+='<text x="'+x(i)+'" y="378" text-anchor="middle">'+(i+1)+'</text>';
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 760 450" role="img" aria-label="Nube de valores unitarios y límites exploratorios"><title>Nube de valores unitarios y límites exploratorios</title><rect width="760" height="450" fill="white"/><g font-family="Arial" font-size="12" fill="#1e293b"><text x="90" y="28" font-size="17">Dispersión de los valores por m²</text>'+body+'<text x="360" y="408" text-anchor="middle">Posición del inmueble en la muestra · orden de listado</text><text x="20" y="220" transform="rotate(-90 20 220)" text-anchor="middle">Valor unitario · millones COP/m²</text><text x="90" y="435">Verde: dentro de límites · Rojo y cruz: fuera de límites · Azul: media</text></g></svg>';
}
