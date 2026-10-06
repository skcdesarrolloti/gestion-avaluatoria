import {escapeReport as esc} from './analysis-download.js';
import {regressionEquation} from './analysis-regression-diagnostics.js';
const fmt=v=>Number.isFinite(v)?v.toLocaleString('es-CO',{maximumFractionDigits:4}):'No estimable';
export function diagnosticReport(r) {
    const d=r.diagnostics,cell=v=>'<td>'+esc(v)+'</td>';
    const shape=[['Valor por m²',d.summary],['Residuos',d.residualSummary]].map(([label,s])=>'<tr>'+[label,fmt(s.mean),fmt(s.median),fmt(s.sd),fmt(s.skew),fmt(s.kurt)].map(cell).join('')+'</tr>').join('');
    return '<style>svg{display:block;width:100%;max-width:900px;height:auto;break-inside:avoid}td,th{vertical-align:top}table{margin-bottom:16px}</style><h2>Ecuación calculada</h2><p>'+esc(regressionEquation(r))+'</p><p>Antigüedad codificada es una categoría ordinal, no años exactos. El descuento interviene en el valor final, no es un predictor.</p>'+
        '<h2>Distribución y errores</h2><table><tr><th>Serie</th><th>Media</th><th>Mediana</th><th>Desviación muestral</th><th>Asimetría ajustada</th><th>Exceso de curtosis ajustado</th></tr>'+shape+'</table><p>CV del valor: '+fmt(d.summary.cv)+'%; MAPE del ajuste: '+fmt(d.mape)+'%. Estadísticos del ajuste en la muestra, no validación predictiva.</p>'+
        '<h3>Comparación descriptiva del curso</h3><table><tr><th>Estimador del valor unitario</th><th>COP/m²</th><th>MAPE (%)</th></tr>'+d.summary.estimators.map(e=>'<tr>'+[e.label,fmt(e.value),fmt(e.mape)].map(cell).join('')+'</tr>').join('')+'</table>'+
        '<h2>Gráficos</h2><p>Rojo: alguna señal de revisión; verde: sin señales bajo estas reglas. Pasa el cursor para identificar muestras. La diagonal de observado/estimado representa coincidencia, no una recta ajustada.</p>'+r.plots.map(p=>p.svg).join('')+
        '<h2>Revisión por inmueble</h2><p>'+esc(d.caveat)+' RIC: [Q1 − 1,5×RIC; Q3 + 1,5×RIC]; |residuo studentizado interno| &gt; 2; h &gt; 2p/n; Cook &gt; 4/n, con p=k+1. No se eliminan observaciones.</p><table><tr><th>Muestra</th><th>Observado</th><th>Estimado</th><th>Residuo</th><th>Studentizado interno</th><th>h</th><th>Cook</th><th>Señales</th></tr>'+d.cases.map(c=>'<tr>'+[c.label,fmt(c.y),fmt(c.fitted),fmt(c.residual),fmt(c.student),fmt(c.leverage),fmt(c.cook),c.flags.join('; ')||'Sin señales'].map(cell).join('')+'</tr>').join('')+'</table>'+
        '<p>Asimetría y exceso de curtosis ajustados, convenciones SKEW/KURT de Excel. La normalidad relevante para inferencia se revisa en los errores; curtosis aislada no prueba normalidad.</p>';
}
