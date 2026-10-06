import {portalTable} from './comparable-portal-table.js';
import {amount} from './comparable-negotiation.js';
import {factorProfile,factorSuggestion} from './analysis-factor-suggestions.js';

const missing=value=>value===undefined || value===null || /^(?:\s*|no publicado|pendiente|por confirmar)$/i.test(String(value).trim());
export function analysisCoverage(rows) {
    const table=portalTable(rows), fixed=new Set(['listing_code','price_amount','area_m2','contact_name','property_type','neighborhood',
        ...['tipo de anunciante','descripcion','description','nombre','name','url','direccion','address','telefono','pricecurrency','moneda','valor de compra','codigo','identificador'].map(k=>'published:'+k)]);
    return table.columns.filter(c=>!fixed.has(c.key)).map(c=>factorProfile({...c,count:table.rows.filter(r=>!missing(r.values[c.key])).length},table,rows))
        .filter(c=>c.count>0).sort((a,b)=>b.count-a.count || a.label.localeCompare(b.label,'es'));
}
export function discountedAnalysis(row,percent) {
    const price=amount(row.price_amount), area=amount(row.area_m2), text=String(percent??'').trim(), pct=Number(text.replace(',','.'));
    if (text==='' || price===null || !Number.isFinite(pct) || pct<0 || pct>100) return {discount:'',value:null,perM2:null};
    const discount=(price*pct/100).toFixed(4), value=price-Number(discount);
    return {discount,value,perM2:area>0 && row.price_unit!=='valor_m2' ? value/area : null};
}
export function marketAnalysisTable(rows,subjectRegime='') {
    rows=rows.map(r=>({...r,ph_regime:r.ph_regime || 'por_verificar',ph_regime_source:r.ph_regime_source || ''}));
    const table=portalTable(rows), factors=analysisCoverage(rows);
    let saved={};try {saved=JSON.parse(rows.find(r=>r.analysis_factor_selection)?.analysis_factor_selection || '{}');} catch {}
    return {
        analysisRows:rows, analysisTable:table, analysisSubjectRegime:subjectRegime, analysisScope:saved.scope ?? 'subject', analysisThreshold:saved.threshold ?? 50,
        get analysisFactors(){return analysisCoverage(this.analysisActiveRows());},
        analysisSelected:(saved.selected ?? factors.map(f=>f.key)).filter(key=>factors.some(f=>f.key===key && f.compatible)),
        analysisDiscounts:Object.fromEntries(rows.map(r=>[r.id,String(r.negotiation_discount??'')])),
        analysisPercents:Object.fromEntries(rows.map(r=>{const offer=amount(r.price_amount), discount=amount(r.negotiation_discount);
            return [r.id,offer>0 && discount!==null ? Number((discount/offer*100).toFixed(4)) : ''];})),
        analysisColumns(){return this.analysisFactors.filter(f=>f.compatible && this.analysisSelected.includes(f.key));},
        analysisEffectiveRegime(row){return ['si','no'].includes(row.ph_regime) ? row.ph_regime : (row.regime_hint?.regime || '');},
        analysisMatches(){return this.analysisRows.filter(r=>this.analysisEffectiveRegime(r)===this.analysisSubjectRegime && this.analysisSubjectRegime);},
        analysisActiveRows(){return this.analysisScope==='subject' && this.analysisMatches().length ? this.analysisMatches() : this.analysisRows;},
        analysisVisibleRows(){const ids=new Set(this.analysisActiveRows().map(r=>r.id));return this.analysisTable.rows.map((r,i)=>({...r,analysisIndex:i})).filter(r=>ids.has(r.key));},
        analysisSuggestion(factor){return factorSuggestion(factor,this.analysisActiveRows().length,this.analysisThreshold);},
        analysisApplySuggestion(){this.analysisSelected=this.analysisFactors.filter(f=>this.analysisSuggestion(f)==='Sugerido por cobertura y variación').map(f=>f.key);},
        analysisSelection(){return JSON.stringify({selected:this.analysisSelected,threshold:Number(this.analysisThreshold),scope:this.analysisScope});},
        analysisRegime(row){return !['si','no'].includes(row.ph_regime) ? (row.regime_hint?.reason || 'Régimen sin verificar') : (row.ph_regime==='si'?'PH':'No PH')+(row.ph_regime_source.trim()?' · soporte registrado':' · falta soporte');},
        analysisChange(id,value){this.analysisPercents[id]=value;const pct=Number(value);if(value==='' || Number.isFinite(pct) && pct>=0 && pct<=100) this.analysisDiscounts[id]=discountedAnalysis(this.analysisRows.find(r=>r.id===id),value).discount;},
        analysisResult(id){const row=this.analysisRows.find(r=>r.id===id), offer=amount(row.price_amount), discount=amount(this.analysisDiscounts[id]), area=amount(row.area_m2);
            if(this.analysisPercents[id]!=='' && (Number(this.analysisPercents[id])<0 || Number(this.analysisPercents[id])>100)) return {value:null,perM2:null};
            const value=offer!==null && discount!==null && discount<=offer ? offer-discount : null;
            return {value,perM2:value!==null && area>0 && row.price_unit!=='valor_m2' ? value/area : null};},
        analysisMoney(value){return value===null ? 'Pendiente' : new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:2}).format(value);},
        analysisOffer(id){return amount(this.analysisRows.find(r=>r.id===id).price_amount);},
        analysisAreaNote(id){const row=this.analysisRows.find(r=>r.id===id);return row.ph_regime==='si' && row.ph_special!=='condominio'
            && (amount(row.private_built_m2)!==amount(row.area_m2) || !String(row.areas_source??'').trim())
            ? 'PH · art. 19.2: base privada construida sin confirmar o sin soporte. Revisar área y componentes antes de adoptar el cociente.' : '';},
    };
}
