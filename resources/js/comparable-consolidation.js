import { flushModuleForm } from './module-autosave.js';
import { completeListingDetails } from './comparable-detail-enrichment.js';
import { sourceUpdate } from './comparable-source-update.js';

export const primaryListing = group => group.rows.find(row => row.research_primary==='si') || group.rows[0];
export const resolvedGroup = group => group.rows.every(r=>r.capture_confirmation==='confirmed') || group.rows.every(r=>r.capture_confirmation==='excluded');

export function consolidatedResearch(getEntries,getForm,getGroups) {
    return {
        consolidationRows:[], consolidationPending:0, consolidationDuplicates:0,
        researchBusy:false, researchMessage:'', researchResults:[],
        intakePrimary(group,id) {
            if (!group.rows.some(row=>row.id===id)) return;
            group.rows.forEach(row=>this.intakeWrite(row.index,'research_primary',row.id===id?'si':'no'));
            this.intakeChanged();
        },
        async finishConsolidation() {
            if (this.consolidationPending || this.researchBusy) return;
            if (await flushModuleForm(getForm())) this.intakeNavigate('research');
        },
        async investigateConsolidated() {
            if (this.researchBusy || this.consolidationPending) return;
            this.researchBusy=true;
            try {
                if (!await flushModuleForm(getForm())) { this.researchMessage='Guardado pendiente. No se inició la lectura.'; return; }
                const groups=getGroups(getEntries().filter(e=>e.used).map(e=>({...e.data,index:e.index})));
                if (groups.some(g=>!resolvedGroup(g))) { this.researchMessage='Termina de consolidar las muestras antes de abrir fichas.'; return; }
                const picked=groups.filter(g=>g.rows.every(r=>r.capture_confirmation==='confirmed')).map(g=>({row:primaryListing(g)}));
                this.researchResults=picked;
                if (!picked.length) { this.researchMessage='No hay inmuebles consolidados para completar.'; return; }
                const result=await completeListingDetails(picked,getForm().dataset.detailEndpoint,
                    (n,total)=>{this.researchMessage=`Completando inmueble ${n} de ${total}…`;},
                    async item=>{
                        const current=getEntries().find(e=>e.data.id===item.row.id);
                        if (!current) throw new Error('La muestra cambió durante la lectura.');
                        const changes=sourceUpdate(current.data,item.row);
                        Object.entries(changes).filter(([key])=>key!=='intake_state').forEach(([key,value])=>this.intakeWrite(current.index,key,value));
                        this.intakeChanged();
                        if (!await flushModuleForm(getForm())) throw new Error('Guardado pendiente.');
                    });
                this.researchMessage=`${result.completed} fichas leídas; ${result.failed} pendientes. Guardado confirmado. Se leyó una ficha por inmueble consolidado.`;
            } catch { this.researchMessage='Lectura detenida. Conserva esta página y revisa el estado de guardado antes de continuar.'; }
            finally { this.researchBusy=false; this.rebuildIntake(); }
        },
    };
}
