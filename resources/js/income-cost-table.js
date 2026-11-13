import { historySuggestions,ledgerMoney,ledgerNumber } from './lease-ledger.js';
export function incomeCostTable() {
    return {
        context:{leases:{}},costCatalog:{},historyLease:'',money:ledgerMoney,
        contextSaved(context) {
            if(context.record!==this.context.record || context.unit!==this.context.unit) return;
            this.context=context;this.stale=true;
            this.$refs.valuationForm.querySelector('[name="context_hash"]').value=context.hash;
            this.$el.closest('[data-income-inputs]')?.querySelectorAll('[data-income-origin-version] [name="version"]').forEach(i=>{i.value=String(context.origin_version);});
        },
        get costHistory() {
            const id=this.historyLease || this.state.projection.lease_id || (Object.keys(this.context.leases).length===1?Object.keys(this.context.leases)[0]:'');
            return historySuggestions(this.context.leases[id]?.ledger,this.context.value_date,this.costCatalog,this.context.type);
        },
        costSuggestion(row) {
            const rate=ledgerNumber(row.reference_percent);
            if(rate!==null && row.reference_source?.trim() && ['potential','effective'].includes(row.reference_basis)) return {rate,basis:row.reference_basis,source:row.reference_source,months:[]};
            return this.costHistory[row.category] || null;
        },
        applyCostSuggestion(row) {
            const s=this.costSuggestion(row);if(!s) return;
            row.amount=String(Math.round(s.rate*1000000)/1000000);row.basis=s.basis;row.source=s.source;
            this.changed();
        },
        addCatalogCosts() {
            for(const [category,meta] of Object.entries(this.costCatalog)) if(!this.state.costs.some(r=>r.category===category) && this.state.costs.length<40) this.state.costs.push({category,name:meta.label});
            this.changed();
        },
    };
}
