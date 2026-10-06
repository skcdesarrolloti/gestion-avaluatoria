import {sourceFacts} from './comparable-source-facts.js';
import {portalTable} from './comparable-portal-table.js';
import {modelArea,modelFactorCount,positiveModelArea} from './analysis-model-area.js';
import {amount} from './comparable-negotiation.js';

export const missingFactor=value=>value===undefined || value===null || /^(?:\s*|no publicado|pendiente|por confirmar)$/i.test(String(value).trim());
const complete=(property,columns)=>positiveModelArea(property.values[modelArea(columns).key]) && positiveModelArea(property.values.area_m2) && columns.every(f=>!missingFactor(property.values[f.key]));
export function factorCombinations(properties,candidates,area) {
    const others=candidates.filter(f=>f.key!==area.key), options=[];
    for(let i=0;i<others.length;i++)for(let j=i+1;j<others.length;j++) {
        const columns=[area,others[i],others[j]];
        if(modelFactorCount(columns)!==3)continue;
        const count=properties.filter(r=>complete(r,columns) && amount(r.values.price_amount)>0).length;
        options.push({keys:columns.filter(f=>f.key!=='area_m2').map(f=>f.key),labels:columns.map(f=>f.label).join(' + '),count,required:30,meets:count>=30});
    }
    return options.sort((a,b)=>b.count-a.count || a.labels.localeCompare(b.labels,'es')).slice(0,3);
}
export function manualMethods() {
    return {
        analysisOnlyMissing:false, analysisManualVersion:0, analysisCombinationCache:null,
        analysisManualEntry(id,key){return sourceFacts(this.analysisRows.find(r=>r.id===id)?.analysis_manual_factors)[key] || {};},
        analysisOriginal(property,key){return portalTable([this.analysisRows[property.analysisIndex]]).rows[0].values[key];},
        analysisCanEdit(property,key){return missingFactor(this.analysisOriginal(property,key));},
        analysisEdit(property,factor,field,value){
            if(!this.analysisCanEdit(property,factor.key))return;
            const row=this.analysisRows[property.analysisIndex], bag=sourceFacts(row.analysis_manual_factors);
            bag[factor.key]={...bag[factor.key],label:factor.label,[field]:value};
            row.analysis_manual_factors=JSON.stringify(bag);this.analysisManualVersion++;
        },
        analysisCellState(property,factor){
            const value=property.values[factor.key];
            if(missingFactor(value) || factor.key===modelArea(this.analysisColumns()).key && !positiveModelArea(value))return 'missing';
            return this.analysisManualEntry(property.key,factor.key).source?.trim()?'manual':'original';
        },
        analysisRowComplete(property){return complete(property,this.analysisColumns());},
        analysisDisplayRows(){return this.analysisVisibleRows().filter(r=>!this.analysisOnlyMissing || !this.analysisRowComplete(r));},
        analysisCombinations(){
            const stamp=JSON.stringify([this.analysisManualVersion,this.analysisActiveRows().map(r=>r.id),this.analysisFactors,this.analysisModelArea().key]);
            if(this.analysisCombinationCache?.stamp===stamp)return this.analysisCombinationCache.options;
            const options=factorCombinations(this.analysisVisibleRows(),this.analysisFactors.filter(f=>this.analysisEligible(f)),this.analysisModelArea());
            this.analysisCombinationCache={stamp,options};return options;
        },
        analysisChooseCombination(option){this.analysisSelected=[...option.keys];this.analysisView='clean';},
        analysisRecordManual(){this.analysisUpdate();},
    };
}
