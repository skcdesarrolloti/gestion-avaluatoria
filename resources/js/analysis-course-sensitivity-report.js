import {courseDiagnostics} from './analysis-course-diagnostics.js';
import {courseSensitivity} from './analysis-course-sensitivity.js';
import {courseDispersionPlot} from './analysis-course-dispersion-plot.js';
import {escapeReport as esc} from './analysis-download.js';
export function courseSensitivityReport(r,fmt){
    const v=courseSensitivity(r,fmt),s=r.summary;if(!v)return '';
    const diagnostics='<h3>Qué significa cada indicador en esta ejecución</h3><table><tr><th>Indicador</th><th>Resultado</th><th>Qué mide</th><th>Cómo leer tu resultado</th></tr>'+courseDiagnostics(r,fmt).map(item=>'<tr>'+[item.label,item.value,item.definition,item.reading].map(value=>'<td>'+esc(value)+'</td>').join('')+'</tr>').join('')+'</table>';
    return courseDispersionPlot(r.valid,s,true)+'<p>Franja Q1–Q3: aproximadamente el 50 % central; estar fuera no indica atipicidad. Cruces rojas: fuera de los límites de 1,5 RIC. Morado: mediana; azul: media.</p><p>'+esc('Q1: '+fmt(s.q1)+' · Q2: '+fmt(s.median)+' · Q3: '+fmt(s.q3)+' · RIC: '+fmt(s.q3-s.q1)+' · Límite inferior: '+fmt(s.lower)+' · Límite superior: '+fmt(s.upper)+' COP/m²')+'</p><table><tr><th>Medida</th><th>De dónde sale</th><th>COP/m²</th><th>Diferencia COP/m²</th><th>Diferencia %</th></tr>'+v.comparison.map(row=>'<tr>'+[row.label,row.operation,fmt(row.value),fmt(row.difference),fmt(row.percent)].map(cell=>'<td>'+esc(cell)+'</td>').join('')+'</tr>').join('')+'</table><p>'+esc('Recorte: piso(0,20 × '+v.n+') = '+v.k+' por cola; '+v.retained+' en el cálculo. Diferencia % = 100 × (estimador − media) / |media|. Media cero: porcentaje no estimable.')+'</p>'+diagnostics;
}
