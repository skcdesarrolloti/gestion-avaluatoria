import {amount} from './comparable-negotiation.js';
import {distribution} from './analysis-distribution.js';

export function courseLiveMethods(){return {
    courseLivePreviewRow(id){
        const row=this.analysisRows.find(r=>r.id===id);if(!row)return {id,y:null,reasons:['Muestra fuera del grupo actual']};
        const draft=this.courseInlineDrafts[id],base=draft?.previewOriginal;
        const value=(key,current)=>draft&&base&&String(draft[key])!==String(base[key])?draft[key]:current;
        const offer=amount(value('offer',row.price_amount)),area=amount(value('area',row.area_m2)),text=value('percent',this.analysisPercents[id]??'');
        const percent=String(text).trim()===''?null:Number(String(text).replace(',','.')),reasons=[];
        if(!(offer>0))reasons.push('Oferta no positiva');if(!(area>0))reasons.push('Área no positiva');
        if(row.price_unit==='valor_m2')reasons.push('Precio publicado unitario: revisar base');
        if(percent!==null&&(!Number.isFinite(percent)||percent<0||percent>100))reasons.push('Descuento fuera de 0–100 %');
        if(this.courseBasis==='adjusted'&&percent===null)reasons.push('Descuento pendiente');
        const changed=!!(draft&&base&&['offer','area','percent'].some(key=>String(draft[key])!==String(base[key])));
        const adjusted=changed?(offer-Number((offer*percent/100).toFixed(4)))/area:this.analysisResult(id).perM2;
        const y=reasons.length?null:this.courseBasis==='adjusted'?adjusted:offer/area;
        if(!reasons.length&&!Number.isFinite(y))reasons.push('Valor unitario no estimable');
        return {id,offer,area,percent,reasons,draft:changed,y:reasons.length?null:y};
    },
    courseLivePreview(){
        const rows=this.analysisVisibleRows().map(r=>({...this.courseLivePreviewRow(r.key),label:'Muestra '+(r.analysisIndex+1)}));
        const pending=rows.filter(r=>r.reasons.length),summary=rows.length>=2&&!pending.length?distribution(rows.map(r=>r.y)):null;
        const saved=this.courseResult?.summary;
        return {rows,pending,summary,n:rows.length,drafts:rows.filter(r=>r.draft).length,basis:this.courseBasis,
            comparison:[['Valores disponibles',this.courseResult?.valid.length,summary?rows.length:null,''],['Media',saved?.mean,summary?.mean,'COP/m²'],['Desviación estándar',saved?.sd,summary?.sd,'COP/m²'],['CV',saved?.cv,summary?.cv,'%'],['Q1',saved?.q1,summary?.q1,'COP/m²'],['Q3',saved?.q3,summary?.q3,'COP/m²']].map(([label,before,after,unit])=>({label,before,after,unit,difference:Number.isFinite(before)&&Number.isFinite(after)?after-before:null})),
            status:!summary||!Number.isFinite(summary.cv)?'CV no estimable: completa los datos de todo el grupo.':summary.cv<=7.5?'CV ≤ 7,5 %: dentro del referente comparado.':'CV > 7,5 %: supera el referente comparado.'};
    },
};}
