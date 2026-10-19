import {amount} from './comparable-negotiation.js';
import {sourceFacts} from './comparable-source-facts.js';
export function courseInlineMethods(){return {
    courseInlineDrafts:{},
    courseInlineEdit(id){const current=this.courseInlineDraft(id),draft=this.courseInlineDrafts[id]??=current;
        if(draft&&current)for(const f of current.factors)if(!draft.factors.some(v=>v.key===f.key))draft.factors.push(f);return draft;},
    courseInlineFields(draft){const keys=this.analysisVerificationColumns().map(f=>f.key);return draft.factors.filter(f=>keys.includes(f.key));},
    courseInlineDraft(id){const row=this.analysisRows.find(r=>r.id===id),property=this.analysisVisibleRows().find(r=>r.key===id);
        if(!row||!property)return null;
        return {offer:row.price_amount,area:row.area_m2,percent:String(this.analysisPercents[id]??''),source:row.negotiation_source||'',
            factors:this.analysisVerificationColumns().map(f=>({key:f.key,label:f.label,value:String(property?.values[f.key]??'')})),error:''};},
    async courseInlineApply(id,draft){
        draft.error='';const row=this.analysisRows.find(r=>r.id===id),offer=amount(draft.offer),area=amount(draft.area),pct=draft.percent===''?null:Number(String(draft.percent).replace(',','.'));
        if(!row||!this.courseInlineDraft(id)){draft.error='Este inmueble ya no está en el grupo actual. Se conserva el resultado anterior.';return false;}
        if(!(offer>0&&area>0)||pct!==null&&(!Number.isFinite(pct)||pct<0||pct>100)){draft.error='Precio y área deben ser positivos; descuento entre 0 y 100 %, o vacío si está pendiente.';return false;}
        const source=String(draft.source||'').trim();if(!source||source.length>1600){draft.error='Indica fuente, fecha y motivo de la corrección (máximo 1600 caracteres).';return false;}
        const original=this.courseInlineDraft(id),bag=sourceFacts(row.analysis_manual_factors),changes=[];
        for(const f of this.courseInlineFields(draft)){const prev=original.factors.find(v=>v.key===f.key);if(!prev||String(f.value).length>500){draft.error='Atributo inválido o demasiado largo.';return false;}
            if(f.value!==prev.value){bag[f.key]={label:prev.label,value:String(f.value),source};changes.push(prev.label+': '+prev.value+' → '+f.value);}}
        if(offer!==amount(original.offer))changes.push('Oferta COP: '+original.offer+' → '+offer);
        if(area!==amount(original.area))changes.push('Área m²: '+original.area+' → '+area);
        if(String(draft.percent)!==original.percent)changes.push('Descuento %: '+original.percent+' → '+draft.percent);
        const audit=changes.length?[row.source_updates||'',new Date().toISOString()+' · Corrección: '+changes.join('; ')+' · '+source].filter(Boolean).join('\n'):row.source_updates||'';
        if(audit.length>1600){draft.error='El registro de correcciones alcanzó su límite. No se aplicaron cambios; conserva el soporte antes de continuar.';return false;}
        row.price_amount=String(offer);row.area_m2=String(area);row.negotiation_source=source;row.source_updates=audit;row.analysis_manual_factors=JSON.stringify(bag);
        this.analysisChange(id,pct===null?'':String(pct));this.analysisManualVersion++;this.analysisDataRevision++;
        delete this.courseInlineDrafts[id];await this.coursePersist();return true;
    }
};}
