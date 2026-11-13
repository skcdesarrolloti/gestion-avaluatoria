export function incomePreparation() {
    return {
        section:'subject', values:{}, issues:{}, sourceOccupancy:'', fieldGroups:{},
        init() {
            Object.assign(this,JSON.parse(this.$el.dataset.incomePreparation));
            this.revealErrors();
        },
        get occupancy() { return this.values.occupancy || this.sourceOccupancy; },
        get scope() { return this.values.scope; },
        get support() { return this.values.support; },
        get occupancyConflict() {
            const saved=this.values.occupancy, source=this.sourceOccupancy;
            if(!saved || !source || saved==='pending' || saved===source) return false;
            return !(source==='rented_unspecified' && ['rented','partial','rented_unspecified'].includes(saved));
        },
        hasLease() { return !['owner','vacant','works'].includes(this.occupancy); },
        value(name) { return name==='occupancy'?this.occupancy:this.values[name] || ''; },
        label(name,options) { return options[this.value(name)] || 'Por completar o verificar'; },
        showQuestion(name) { return name!=='support' || this.hasLease() || Boolean(this.values.support); },
        openSection(section) {
            if(!['subject','lease','evidence','summary'].includes(section)) return;
            this.section=section;
            const url=new URL(location.href);
            url.searchParams.set('renta_tema',section);
            window.history?.replaceState(window.history.state,'',url.href);
        },
        revealErrors() {
            const first=Object.keys(this.issues)[0]?.match(/^income_concept\[([^\]]+)\]$/)?.[1];
            if(first && this.fieldGroups[first]) this.section=this.fieldGroups[first];
        },
        validation(errors) { this.issues=errors || {}; this.revealErrors(); },
    };
}
