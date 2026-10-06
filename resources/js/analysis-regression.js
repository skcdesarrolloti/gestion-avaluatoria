import {amount} from './comparable-negotiation.js';
import {modelArea} from './analysis-model-area.js';
import {linearRegression} from './analysis-regression-math.js';
import {downloadReport,escapeReport as esc} from './analysis-download.js';
import {regressionDiagnostics,regressionEquation} from './analysis-regression-diagnostics.js';
import {diagnosticPlots,mountDiagnosticSvg} from './analysis-diagnostic-plots.js';
import {diagnosticReport} from './analysis-diagnostic-report.js';
const numeric=v=>{const s=String(v??'').trim().replace(/\s*m[²2]$/i,'');return /^\d+(?:[.,]\d+)?$/.test(s)?Number(s.replace(',','.')):null;};
export function regressionMethods() {
    return {
        regressionTab:'academy',regressionBasis:'offer',regressionCodes:{},regressionConfirmed:false,regressionResult:null,regressionError:'',regressionBusy:false,
        regressionColumns(){const cols=this.analysisFactors.filter(f=>this.analysisApplied.includes(f.key));const area=modelArea(cols);return area.key==='area_m2'?[area,...cols]:cols;},
        regressionCategories(){return this.regressionColumns().flatMap(f=>[...new Set(this.analysisVisibleRows().map(r=>String(r.values[f.key]??'')))].filter(v=>numeric(v)===null && v && !/^(no publicado|pendiente)$/i.test(v)).map(v=>({key:JSON.stringify([f.key,v]),factor:f.label,value:v})));},
        regressionMatrix(){const cols=this.regressionColumns();const rows=this.analysisVisibleRows().map(r=>{
            const x=cols.map(f=>{const v=r.values[f.key],num=numeric(v);return num!==null?num:numeric(this.regressionCodes[JSON.stringify([f.key,String(v??'')])]);});
            const original=this.analysisRows[r.analysisIndex],area=amount(original.area_m2),offer=amount(original.price_amount);
            const y=this.regressionBasis==='adjusted'?this.analysisResult(r.key).perM2:area>0 && original.price_unit!=='valor_m2' && offer>0?offer/area:null;
            return {id:r.key,label:'Muestra '+(r.analysisIndex+1),x,y};
        });return {cols,rows,complete:rows.filter(r=>r.x.every(Number.isFinite) && Number.isFinite(r.y))};},
        regressionStamp(){return JSON.stringify([this.analysisApplied,this.analysisActiveRows(),this.analysisDiscounts,this.regressionBasis,this.regressionCodes]);},
        regressionCurrent(){return !!this.regressionResult && this.regressionResult.stamp===this.regressionStamp();},
        regressionEquation(){return regressionEquation(this.regressionResult);},
        regressionNumber(v){return Number.isFinite(v)?v.toLocaleString('es-CO',{maximumFractionDigits:4}):'No estimable';},
        regressionPlot(element,svg){mountDiagnosticSvg(element,svg);},
        async regressionReview(id){if(!this.regressionCurrent())return;this.analysisModule='samples';this.analysisView='result';this.analysisOnlyMissing=false;this.analysisEditingId=id;
            await this.$nextTick();const row=document.getElementById('analysis-row-'+id);row?.scrollIntoView({block:'center'});row?.focus({preventScroll:true});},
        async regressionRun(){if(this.regressionBusy)return;this.regressionBusy=true;this.regressionError='';this.regressionResult=null;
            try {await new Promise(resolve=>setTimeout(resolve,30));if(!this.regressionConfirmed)throw new Error('Confirma la codificación antes de calcular.');
                if(!this.analysisRegimeApplied || !this.analysisApplied.length || this.analysisFactorsPending())throw new Error('Aplica la depuración y actualiza los factores antes de calcular.');
                const m=this.regressionMatrix(),required=Math.max(30,m.cols.length*10);
                if(m.cols.length<3 || m.complete.length<required)throw new Error('Se requieren '+required+' filas numéricas completas para '+m.cols.length+' factores. Disponibles: '+m.complete.length+'.');
                this.regressionResult={...linearRegression(m.complete.map(r=>r.x),m.complete.map(r=>r.y)),matrix:m,stamp:this.regressionStamp(),at:new Date().toISOString(),simulated:this.analysisHasSimulated()};
                this.regressionResult.diagnostics=regressionDiagnostics(this.regressionResult);
                this.regressionResult.plots=diagnosticPlots(this.regressionResult);
                this.regressionTab='application';
            }catch(e){this.regressionError=e.message;}finally{this.regressionBusy=false;}},
        regressionExport(){if(!this.regressionCurrent())return;const r=this.regressionResult;
            const cells=v=>'<td>'+esc(v)+'</td>';
            const html='<!doctype html><html lang="es"><meta charset="utf-8"><title>Regresión del análisis</title><style>body{font:14px Arial}table{border-collapse:collapse}td,th{border:1px solid #aaa;padding:6px}</style><h1>Regresión lineal múltiple</h1><p>'+esc(r.at)+' · '+(r.simulated?'EJEMPLO CON DATOS SIMULADOS':'Datos del análisis')+'</p><p>Base: '+esc(this.regressionBasis==='offer'?'Oferta / área publicada':'Valor con descuento / área publicada')+'; n='+r.n+'; R²='+r.r2+'; R² ajustado='+r.adjustedR2+'; error residual='+r.rmse+'</p><table><tr><th>Variable</th><th>Coeficiente</th><th>VIF</th></tr>'+r.coefficients.map((v,i)=>'<tr>'+cells(i?r.matrix.cols[i-1].label:'Intercepto')+cells(v)+cells(i?r.vif[i-1]:'—')+'</tr>').join('')+'</table><h2>Codificación confirmada</h2><ul>'+this.regressionCategories().map(c=>'<li>'+esc(c.factor+' · '+c.value+' = '+this.regressionCodes[c.key])+'</li>').join('')+'</ul><h2>Matriz, predicciones y residuos</h2><table><tr><th>Muestra</th>'+r.matrix.cols.map(c=>'<th>'+esc(c.label)+'</th>').join('')+'<th>Y</th><th>Estimado</th><th>Residuo</th></tr>'+r.matrix.complete.map((v,i)=>'<tr>'+[v.label,...v.x,v.y,r.fitted[i],r.residuals[i]].map(cells).join('')+'</tr>').join('')+'</table><p>Resultado exploratorio. Revisar residuos, independencia, homocedasticidad, correlaciones, colinealidad y comparabilidad antes de adoptar un valor.</p></html>';
            downloadReport(html.replace('</html>',diagnosticReport(r)+'</html>'),'regresion-analisis.html');},
    };
}
