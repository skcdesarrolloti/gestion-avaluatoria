import {amount} from './comparable-negotiation.js';
import {courseSummary,courseBootstrap,orderCourseEstimators,preferredCourseEstimators,courseSelectionConclusion} from './analysis-course-math.js';
import {courseHistogram} from './analysis-course-plots.js';
import {mountDiagnosticSvg} from './analysis-diagnostic-plots.js';
import {courseReport} from './analysis-course-report.js';
import {downloadReport} from './analysis-download.js';
import {courseInterpretations} from './analysis-course-interpretation.js';
export const courseSteps=['Base del cálculo','Bloques y distribución','Tendencia central','Dispersión','Sensibilidad y consideraciones','Precisión de la media','Conclusión y memoria'];
export function courseMethods(){return {
    courseStep:0,courseSteps,courseBasis:'adjusted',courseConfidence:'.95',courseResult:null,courseBusy:false,courseError:'',courseBootstrapBusy:false,courseProgress:0,courseNotes:{},courseConclusion:'',analysisReviewReturn:null,
    courseRows(){return this.analysisVisibleRows().map(r=>{const original=this.analysisRows[r.analysisIndex],area=amount(original.area_m2),offer=amount(original.price_amount),adjusted=this.analysisResult(r.key),discount=amount(this.analysisDiscounts[r.key]),reasons=[];
        if(!(area>0))reasons.push('Área publicada no positiva');if(!(offer>0))reasons.push('Oferta no positiva');if(original.price_unit==='valor_m2')reasons.push('Precio publicado unitario: requiere revisar base');
        if(this.courseBasis==='adjusted'&&(!Number.isFinite(adjusted.perM2)||discount===null))reasons.push('Descuento o valor final pendiente');
        const y=this.courseBasis==='adjusted'?adjusted.perM2:area>0&&offer>0?offer/area:null;
        return {id:r.key,label:'Muestra '+(r.analysisIndex+1),y,reasons,area,offer,discount,final:adjusted.value,areaNote:this.analysisAreaNote(r.key),regime:this.analysisRegime(original)};
    });},
    courseStamp(){return JSON.stringify([this.analysisActiveRows(),this.analysisDiscounts,this.courseBasis,this.courseConfidence]);},
    courseCurrent(){return !!this.courseResult&&this.courseResult.stamp===this.courseStamp();},
    courseNumber(v){return Number.isFinite(v)?v.toLocaleString('es-CO',{maximumFractionDigits:4}):'No estimable';},
    courseInterpretation(){return this.courseResult?courseInterpretations(this.courseResult)[this.courseStep]:null;},
    courseSortedEstimators(){return orderCourseEstimators(this.courseResult?.summary.estimators||[]);},
    courseEstimatorSelected(e){return preferredCourseEstimators(this.courseResult?.summary.estimators||[]).some(v=>v.label===e.label);},
    courseSelectionConclusion(){return courseSelectionConclusion(this.courseResult?.summary.estimators||[],v=>this.courseNumber(v));},
    coursePlot(el,svg){mountDiagnosticSvg(el,svg);},
    async courseCalculate(){if(this.courseBusy||this.courseBootstrapBusy)return;this.courseBusy=true;this.courseError='';
        try{await new Promise(resolve=>setTimeout(resolve,0));if(!this.analysisRegimeApplied)throw new Error('Aplica primero la depuración de muestras.');const rows=this.courseRows(),valid=rows.filter(r=>!r.reasons.length&&Number.isFinite(r.y));if(valid.length<2)throw new Error('Completa al menos dos valores unitarios para el análisis descriptivo.');
            const s=courseSummary(valid,Number(this.courseConfidence));this.courseResult={rows,valid,pending:rows.filter(r=>!valid.includes(r)),summary:s,histogram:courseHistogram(s.classes),stamp:this.courseStamp(),at:new Date().toISOString(),basis:this.courseBasis,simulated:this.analysisHasSimulated(),bootstrap:null};
        }catch(e){this.courseError=e.message;}finally{this.courseBusy=false;}},
    async courseResample(){if(!this.courseCurrent()||this.courseBootstrapBusy)return;this.courseBootstrapBusy=true;this.courseProgress=0;const result=this.courseResult;
        try{const b=await courseBootstrap(result.valid.map(r=>r.y),result.summary.confidence,n=>this.courseProgress=n);if(this.courseResult===result)result.bootstrap=b;}catch{this.courseError='No se pudo completar el remuestreo.';}finally{this.courseBootstrapBusy=false;}},
    analysisReviewOrigin(){const p=this.analysisReviewReturn;return !p?'':p.module==='statistics'?(p.step===6?'Memoria y sustentación':'Entender la muestra → '+courseSteps[p.step]):(p.tab==='diagnostics'?'Revisar el modelo':'Construir el modelo');},
    analysisReviewLabel(){const i=this.analysisRows.findIndex(r=>r.id===this.analysisEditingId);return i<0?'Inmueble en revisión':'Muestra '+(i+1);},
    async courseReview(id){if(this.analysisModule!=='samples')this.analysisReviewReturn={module:this.analysisModule,step:this.courseStep,tab:this.regressionTab,scrollY:window.scrollY,scrollX:window.scrollX};this.analysisModule='samples';this.analysisView='result';this.analysisOnlyMissing=false;this.analysisEditingId=id;await this.$nextTick();const row=document.getElementById('analysis-row-'+id);row?.scrollIntoView({block:'center'});row?.focus({preventScroll:true});},
    async analysisReturnFromReview(){const p=this.analysisReviewReturn;if(!p)return;this.analysisModule=p.module;this.courseStep=p.step;this.regressionTab=p.tab;this.analysisReviewReturn=null;await this.$nextTick();window.scrollTo({top:p.scrollY,left:p.scrollX,behavior:'instant'});const form=this.$root;const target=form?.querySelector(p.module==='statistics'?'[aria-label="Interpretación de los datos de este paso"]':'[aria-label="Etapas de M4 Análisis de mercado"] button');target?.focus({preventScroll:true});},
    courseExport(){if(this.courseCurrent()){const support=this.$root?.querySelector('[data-course-normative-support]')?.value||'';downloadReport(courseReport(this.courseResult,this.courseNotes,this.courseConclusion,support),'analisis-estadistico-ejercicio.html');}},
};}
