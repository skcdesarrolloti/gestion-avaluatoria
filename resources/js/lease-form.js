export function leaseForm() {
    return {
        values:{},issues:{},occupancy:'',areaAvailable:[],antecedentAvailable:false,
        get pendingItems() {
            const v=this.values,items=[];
            if(!this.occupancy || this.occupancy==='pending') items.push('Precisar la ocupación de esta unidad en el capítulo 3.');
            if(!this.areaAvailable.includes(v.analysis_area_reference) || v.analysis_area_scope!=='same') items.push('Revisar el área documentada y su correspondencia con los espacios del canon.');
            if(['rented','partial','rented_unspecified'].includes(this.occupancy) && v.status==='none') items.push('Contrastar la ocupación arrendada del capítulo 3 con la ausencia de contrato vigente informada aquí.');
            if(!['vacant','owner','works'].includes(this.occupancy) && v.status!=='none') {
                if(!v.scope) items.push('Identificar los espacios comprendidos en el arriendo.');
                if(v.economic_source==='assignment'?!this.antecedentAvailable:(!v.rent_amount || !v.rent_period)) items.push('Documentar el canon vigente y su periodicidad en su origen.');
                if(!v.start_date || !v.end_date) items.push('Documentar inicio y terminación, o explicar su condición pendiente.');
                if(!v.adjustment_rule) items.push('Documentar la regla de incremento o su no aplicación.');
                if(!v.initial_rent_amount || !v.initial_rent_period) items.push('Para la futura serie: documentar canon inicial y periodicidad.');
            }
            if(!v.support) items.push('Identificar los soportes revisados y su fecha.');
            if(Object.values(v.monthly_history || {}).some(row=>Object.values(row).some(value=>value!=='') && row.review!=='verified')) items.push('Hay meses registrados pendientes de validación manual.');
            return items.length?items:['Datos básicos registrados; falta la revisión técnica de su coherencia y soportes.'];
        },
        init() {
            Object.assign(this,JSON.parse(this.$el.dataset.leaseForm));
        },
        validation(errors) {
            this.issues=errors || {};
            if(this.id) {
                const url=new URL(location.href);url.searchParams.set('lease',this.id);
                history.replaceState(history.state,'',url.href);
            }
            const field=Object.keys(this.issues)[0];
            if(field) this.$nextTick(()=>{
                const control=this.$el.querySelector(`[name="${CSS.escape(field)}"]`);
                for(let parent=control?.parentElement;parent && parent!==this.$el;parent=parent.parentElement)
                    if(parent.tagName==='DETAILS') parent.open=true;
            });
        },
    };
}
