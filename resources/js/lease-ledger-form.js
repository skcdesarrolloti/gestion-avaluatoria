import { leaseSeries,ledgerMoney } from './lease-ledger.js';
import { referenceSeries,referenceLease } from './lease-reference.js';
export function leaseLedgerForm() {
    let last='',rows=[],referenceKey='',reference=null;
    return {
        historyYear:new Date().getFullYear(),cutoff:'',historyView:'rent',ledgerDirty:false,
        today:'',ipc:{rates:{}},referenceYear:'',
        money:ledgerMoney,
        get referenceHistory() {
            const key=JSON.stringify([this.values,this.today,this.ipc,this.cutoff]);
            if(key!==referenceKey) {referenceKey=key;reference=referenceSeries(this.values,this.today,this.ipc.rates,this.cutoff);}
            return reference;
        },
        get referenceRows() {return this.referenceHistory.rows.filter(r=>!this.referenceYear || r.month.startsWith(this.referenceYear));},
        get referenceMax() {return Math.max(1,...this.referenceRows.map(r=>r.reference || 0));},
        get ledgerRows() {
            const signature=JSON.stringify([this.values,this.historyYear,this.cutoff]);
            if(signature!==last) {last=signature;rows=leaseSeries(referenceLease(this.values,this.ipc.rates,this.historyYear+'-12-31'),this.historyYear,this.cutoff);}
            return rows;
        },
        get ledgerMax() { return Math.max(1,...this.ledgerRows.flatMap(r=>[r.contract || 0,r.rentReceived || 0])); },
        get ledgerSummary() {
            const rows=this.ledgerRows.filter(r=>r.closed),known=rows.filter(r=>r.occupied!==null);
            const days=known.reduce((s,r)=>s+r.days,0),vacant=known.reduce((s,r)=>s+r.days-r.occupied,0);
            return `${known.length}/${rows.length} meses cerrados al corte con ocupación documentada. ${days?((vacant/days)*100).toFixed(2)+' % de días vacantes sobre esos '+days+' días':'Vacancia pendiente por falta de evidencia'}.`;
        },
        addAdjustment(group='rent_adjustments') {
            if(Object.keys(this.values[group]).length>=600) return;
            const id=Array.from(crypto.getRandomValues(new Uint8Array(8)),b=>b.toString(16).padStart(2,'0')).join('');
            this.values[group][id]=group==='vacancy_periods'?{start:'',end:'',source:''}:{date:'',rate:'',amount:'',source:''};
            this.$nextTick(()=>this.$el.dispatchEvent(new Event('input',{bubbles:true})));
        },
        clearAdjustment(id,group='rent_adjustments') {
            this.values[group][id]=group==='vacancy_periods'?{start:'',end:'',source:''}:{date:'',rate:'',amount:'',source:''};
            this.$nextTick(()=>this.$el.dispatchEvent(new Event('input',{bubbles:true})));
        },
        saved(result) {
            this.ledgerDirty=false;
            if(result.income_context) window.dispatchEvent(new CustomEvent('ga:income-context',{detail:result.income_context}));
        },
    };
}
