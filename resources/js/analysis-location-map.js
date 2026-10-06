import {downloadReport,escapeReport as esc} from './analysis-download.js';
export function coordinate(row) {
    const texts=[row?.latitude,row?.longitude].map(v=>String(v??'').trim().replace(',','.')),values=texts.map(Number);
    return texts.every(Boolean) && values.every(Number.isFinite) && Math.abs(values[0])<=90 && Math.abs(values[1])<=180?values:null;
}
export function mapGeometry(rows,subject={}) {
    const subjectPoint=coordinate(subject),points=rows.flatMap((r,i)=>{const c=coordinate(r);return c?[{n:i+1,c,row:r}]:[];});
    if(subjectPoint)points.push({n:'S',c:subjectPoint,row:subject});
    if(!points.length)return [];
    const lat=points.reduce((s,p)=>s+p.c[0],0)/points.length,cos=Math.cos(lat*Math.PI/180);
    const xs=points.map(p=>p.c[1]*cos),ys=points.map(p=>p.c[0]),minX=Math.min(...xs),maxX=Math.max(...xs),minY=Math.min(...ys),maxY=Math.max(...ys);
    const scale=Math.min(640/Math.max(maxX-minX,0.001),380/Math.max(maxY-minY,0.001));
    return points.map((p,i)=>({...p,x:400+(xs[i]-(minX+maxX)/2)*scale,y:260-(ys[i]-(minY+maxY)/2)*scale,
        distance:subjectPoint?distanceKm(p.c,subjectPoint):null}));
}
export function distanceKm(a,b){const rad=Math.PI/180,dlat=(b[0]-a[0])*rad,dlng=(b[1]-a[1])*rad,v=Math.sin(dlat/2)**2+Math.cos(a[0]*rad)*Math.cos(b[0]*rad)*Math.sin(dlng/2)**2;return 6371*2*Math.atan2(Math.sqrt(v),Math.sqrt(Math.max(0,1-v)));}
export function mapSvg(points) {
    const grid=Array.from({length:7},(_,i)=>'<path d="M'+(80+i*100)+' 70 V450 M80 '+(70+i*60)+' H720" stroke="#dce4ed"/>').join('');
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 540" role="img"><rect width="800" height="540" fill="white"/><text x="30" y="30" font-family="Arial" font-size="20">Localización comparada · sujeto y muestras</text>'+grid+points.map(p=>'<g><title>'+esc('Muestra '+p.n+' · '+p.c.join(', ')+' · '+(p.row.location_verification || 'precisión pendiente'))+'</title><circle cx="'+p.x+'" cy="'+p.y+'" r="'+(p.n==='S'?14:10)+'" fill="'+(p.n==='S'?'#9f1239':'#0f766e')+'"/><text x="'+p.x+'" y="'+(p.y+4)+'" text-anchor="middle" font-family="Arial" font-size="11" fill="white">'+p.n+'</text></g>').join('')+'<text x="750" y="80" font-family="Arial">N ↑</text><text x="30" y="495" font-family="Arial" font-size="13">S: sujeto · números: orden en el grupo de trabajo · distancias geográficas, no de recorrido.</text><text x="30" y="520" font-family="Arial" font-size="12">Proyección local de coordenadas WGS84. Sin cartografía de calles. Revisar precisión y soporte.</text></svg>';
}
export function locationMapMethods(subject={}) {
    return {
        analysisSubjectLocation:subject,analysisMapOnlyPending:true,analysisMapEditingId:'',
        analysisMapPoints(){return mapGeometry(this.analysisActiveRows(),this.analysisSubjectLocation);},
        analysisMapReady(row){return coordinate(row) && ['exact','approximate'].includes(row.location_verification) && String(row.location_source??'').trim() && String(row.verification_detail??'').trim();},
        analysisMapCaptureRows(){return this.analysisActiveRows().filter(r=>!this.analysisMapOnlyPending || !this.analysisMapReady(r) || this.analysisMapEditingId===r.id);},
        analysisMapUrl(){const p=this.analysisMapPoints();if(!p.length)return '';const lats=p.map(v=>v.c[0]),lngs=p.map(v=>v.c[1]);return 'https://www.openstreetmap.org/export/embed.html?bbox='+encodeURIComponent([Math.min(...lngs)-0.005,Math.min(...lats)-0.005,Math.max(...lngs)+0.005,Math.max(...lats)+0.005].join(','))+'&layer=mapnik';},
        analysisMapRender(element){const parser=new DOMParser(),svg=parser.parseFromString(mapSvg(this.analysisMapPoints()),'image/svg+xml').documentElement;element.replaceChildren(document.importNode(svg,true));},
        analysisMapExport(){downloadReport(mapSvg(this.analysisMapPoints()),'localizacion-comparada.svg','image/svg+xml');},
        analysisMapReport(){const points=this.analysisMapPoints();const html='<!doctype html><html lang="es"><meta charset="utf-8"><title>Localización comparada</title><style>body{font:14px Arial}svg{max-width:100%}td,th{border:1px solid #aaa;padding:6px}table{border-collapse:collapse}</style><h1>Localización comparada</h1><p>'+esc(new Date().toISOString())+' · '+points.filter(p=>p.n!=='S').length+' muestras ubicadas de '+this.analysisActiveRows().length+'.</p>'+mapSvg(points)+'<table><tr><th>Referencia</th><th>Coordenadas</th><th>Distancia al sujeto (km)</th><th>Precisión</th><th>Fuente y soporte</th></tr>'+points.map(p=>'<tr>'+[p.n,p.c.join(', '),p.distance===null?'Sujeto pendiente':p.distance.toFixed(3),p.n==='S'?'Coordenadas del sujeto':p.row.location_verification || 'Pendiente',(p.row.location_source || '')+' · '+(p.row.verification_detail || '')].map(v=>'<td>'+esc(v)+'</td>').join('')+'</tr>').join('')+'</table><p>Los puntos sin soporte son referencias pendientes; no se presentan como ubicaciones confirmadas.</p></html>';downloadReport(html,'informe-localizacion-comparada.html');},
    };
}
