import { leaseSeries,ledgerMoney } from './lease-ledger.js';
export function leaseLedgerForm() {
    let last='',rows=[];
    return {
        historyYear:new Date().getFullYear(),cutoff:'',historyView:'rent',ledgerDirty:false,
        money:ledgerMoney,
        get ledgerRows() {
            const signature=JSON.stringify([this.values,this.historyYear,this.cutoff]);
            if(signature!==last) {last=signature;rows=leaseSeries(this.values,this.historyYear,this.cutoff);}
            return rows;
        },
        get ledgerMax() { return Math.max(1,...this.ledgerRows.flatMap(r=>[r.contract || 0,r.rentReceived || 0])); },
        get ledgerSummary() {
            const rows=this.ledgerRows.filter(r=>r.closed),known=rows.filter(r=>r.occupied!==null);
            const days=known.reduce((s,r)=>s+r.days,0),vacant=known.reduce((s,r)=>s+r.days-r.occupied,0);
            return `${known.length}/${rows.length} meses cerrados al corte con ocupación documentada. ${days?((vacant/days)*100).toFixed(2)+' % de días vacantes sobre esos '+days+' días':'Vacancia pendiente por falta de evidencia'}.`;
        },
        addAdjustment() {
            if(Object.keys(this.values.rent_adjustments).length>=600) return;
            const id=Array.from(crypto.getRandomValues(new Uint8Array(8)),b=>b.toString(16).padStart(2,'0')).join('');
            this.values.rent_adjustments[id]={date:'',rate:'',amount:'',source:''};
            this.$nextTick(()=>this.$el.dispatchEvent(new Event('input',{bubbles:true})));
        },
        clearAdjustment(id) {
            this.values.rent_adjustments[id]={date:'',rate:'',amount:'',source:''};
            this.$nextTick(()=>this.$el.dispatchEvent(new Event('input',{bubbles:true})));
        },
        saved(result) {
            this.ledgerDirty=false;
            if(result.income_context) window.dispatchEvent(new CustomEvent('ga:income-context',{detail:result.income_context}));
        },
    };
}
