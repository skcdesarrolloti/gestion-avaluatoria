import {amount} from './comparable-negotiation.js';
import {courseSummary,courseBootstrap} from './analysis-course-math.js';
import {courseHistogram} from './analysis-course-plots.js';
import {mountDiagnosticSvg} from './analysis-diagnostic-plots.js';
import {courseReport} from './analysis-course-report.js';
import {downloadReport} from './analysis-download.js';
export const courseSteps=['Preparar la muestra','Bloques y distribución','Tendencia central','Dispersión','Sensibilidad y consideraciones','Precisión de la media','Conclusión y memoria'];
export function courseMethods(){return {
    courseStep:0,courseSteps,courseBasis:'adjusted',courseConfidence:'.95',courseResult:null,courseBusy:false,courseError:'',courseBootstrapBusy:false,courseProgress:0,courseNotes:{},courseConclusion:'',
    courseRows(){return this.analysisVisibleRows().map(r=>{const original=this.analysisRows[r.analysisIndex],area=amount(original.area_m2),offer=amount(original.price_amount),adjusted=this.analysisResult(r.key),discount=amount(this.analysisDiscounts[r.key]),reasons=[];
        if(!(area>0))reasons.push('Área publicada no positiva');if(!(offer>0))reasons.push('Oferta no positiva');if(original.price_unit==='valor_m2')reasons.push('Precio publicado unitario: requiere revisar base');
        if(this.courseBasis==='adjusted'&&(!Number.isFinite(adjusted.perM2)||discount===null))reasons.push('Descuento o valor final pendiente');
        const y=this.courseBasis==='adjusted'?adjusted.perM2:area>0&&offer>0?offer/area:null;
        return {id:r.key,label:'Muestra '+(r.analysisIndex+1),y,reasons,area,offer,discount,final:adjusted.value,areaNote:this.analysisAreaNote(r.key),regime:this.analysisRegime(original)};
    });},
    courseStamp(){return JSON.stringify([this.analysisActiveRows(),this.analysisDiscounts,this.courseBasis,this.courseConfidence]);},
    courseCurrent(){return !!this.courseResult&&this.courseResult.stamp===this.courseStamp();},
    courseNumber(v){return Number.isFinite(v)?v.toLocaleString('es-CO',{maximumFractionDigits:4}):'No estimable';},
    coursePlot(el,svg){mountDiagnosticSvg(el,svg);},
    async courseCalculate(){if(this.courseBusy||this.courseBootstrapBusy)return;this.courseBusy=true;this.courseError='';
        try{await new Promise(resolve=>setTimeout(resolve,0));if(!this.analysisRegimeApplied)throw new Error('Aplica primero la depuración de muestras.');const rows=this.courseRows(),valid=rows.filter(r=>!r.reasons.length&&Number.isFinite(r.y));if(valid.length<2)throw new Error('Completa al menos dos valores unitarios para el análisis descriptivo.');
            const s=courseSummary(valid,Number(this.courseConfidence));this.courseResult={rows,valid,pending:rows.filter(r=>!valid.includes(r)),summary:s,histogram:courseHistogram(s.classes),stamp:this.courseStamp(),at:new Date().toISOString(),basis:this.courseBasis,simulated:this.analysisHasSimulated(),bootstrap:null};
        }catch(e){this.courseError=e.message;}finally{this.courseBusy=false;}},
    async courseResample(){if(!this.courseCurrent()||this.courseBootstrapBusy)return;this.courseBootstrapBusy=true;this.courseProgress=0;const result=this.courseResult;
        try{const b=await courseBootstrap(result.valid.map(r=>r.y),result.summary.confidence,n=>this.courseProgress=n);if(this.courseResult===result)result.bootstrap=b;}catch{this.courseError='No se pudo completar el remuestreo.';}finally{this.courseBootstrapBusy=false;}},
    async courseReview(id){this.analysisModule='samples';this.analysisView='result';this.analysisOnlyMissing=false;this.analysisEditingId=id;await this.$nextTick();const row=document.getElementById('analysis-row-'+id);row?.scrollIntoView({block:'center'});row?.focus({preventScroll:true});},
    courseExport(){if(this.courseCurrent())downloadReport(courseReport(this.courseResult,this.courseNotes,this.courseConclusion),'analisis-estadistico-ejercicio.html');},
};}
