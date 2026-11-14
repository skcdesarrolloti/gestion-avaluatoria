import { postAutosave } from './autosave-session.js';
import { incomeCostTable } from './income-cost-table.js';

export function incomeValuation() {
    return Object.defineProperties({
        state:{projection:{},costs:[],events:[],rates:[],adoption:{}},issues:{},busy:false,stale:false,previewStale:false,message:'',
        init() { Object.assign(this,JSON.parse(this.$el.dataset.valuationState)); },
        validation(errors) {
            this.issues=errors;
            if(Object.keys(errors).length) this.$nextTick(()=>this.$refs.valuationForm.querySelectorAll('[aria-invalid="true"]').forEach(input=>{
                for(let parent=input.parentElement;parent && parent!==this.$refs.valuationForm;parent=parent.parentElement) if(parent.tagName==='DETAILS') parent.open=true;
            }));
        },
        changed() { this.stale=true;this.previewStale=true;this.$nextTick(()=>this.$refs.valuationForm.dispatchEvent(new Event('input',{bubbles:true}))); },
        addRow(group) {
            const limits={costs:40,events:600,rates:40};
            if(this.state[group].length>=limits[group]) {this.message='Se alcanzó el límite de filas; consolida las referencias.';return;}
            const row={};if(group==='rates') row.id=Array.from(crypto.getRandomValues(new Uint8Array(8)),b=>b.toString(16).padStart(2,'0')).join('');
            this.state[group].push(row);this.changed();
        },
        removeRow(group,index) {
            if(group==='rates') for(const field of ['direct_id','discount_id','exit_id']) if(this.state.adoption[field]===this.state.rates[index].id) this.state.adoption[field]='';
            this.state[group].splice(index,1);this.changed();
        },
        rateFieldVisible(field,method) {
            const specific={income:['market'],price:['market'],reference:['build','capm'],premium:['build','capm'],beta:['capm'],equity:['wacc'],debt:['wacc'],debt_weight:['wacc'],rate:['manual']};
            return !specific[field] || specific[field].includes(method);
        },
        async calculate(operation='calculate') {
            if(this.busy) return;
            this.busy=true;this.message='Guardando los supuestos antes de calcular…';
            try {
                if(window.gaFlushAutosaves && !await window.gaFlushAutosaves()) throw new Error('Hay cambios sin confirmar. Reintenta Guardar borrador antes de calcular.');
                const form=this.$refs.valuationForm,body=new FormData(form);
                body.set('payload',JSON.stringify(this.state));body.set('operation',operation);
                const controller=new AbortController(),timer=setTimeout(()=>controller.abort(),30000);
                let response,result;
                try {response=await postAutosave(form,body,controller.signal);result=await response.json();}
                finally {clearTimeout(timer);}
                this.validation(result.errors || {});
                if(!response.ok || !result.ok || !Number.isInteger(result.version)) throw new Error(result.message || 'No se confirmó el cálculo.');
                form.closest('[data-methodology-version-group]')?.querySelectorAll('[data-workflow-version] input[name="version"]').forEach(input=>{input.value=String(result.version);});
                this.message='Datos guardados. Actualizando la tabla…';
                await this.refreshResults();
                if(operation==='preview') this.previewStale=false;else this.stale=false;
                this.message=operation==='preview'?'Flujo mensual preliminar guardado. Los costos pendientes conservan su estado.':'Resultados y pendientes actualizados.';
            } catch(error) {this.message=error.name==='AbortError'?'No se recibió confirmación. Revisa el resultado guardado antes de repetir.':error.message || 'No se pudo completar el cálculo.';}
            finally {this.busy=false;}
        },
        async refreshResults() {
            const controller=new AbortController(),timer=setTimeout(()=>controller.abort(),20000);
            try {
                const response=await fetch(location.href,{credentials:'same-origin',cache:'no-store',signal:controller.signal,headers:{Accept:'text/html'}});
                if(!response.ok || new URL(response.url || location.href).pathname!==location.pathname) throw new Error('El cálculo se guardó; vuelve a ingresar para consultar el resultado.');
                const page=new DOMParser().parseFromString(await response.text(),'text/html');
                for(const [selector,ref] of [['[data-income-valuation-results]','valuationResults'],['[data-income-flow-preview]','previewResults']]) {
                    const fresh=page.querySelector(selector),old=this.$refs[ref];
                    if(!fresh || !old) throw new Error('Los datos se guardaron, pero no se pudo actualizar la tabla.');
                    window.Alpine.mutateDom(()=>{window.Alpine.destroyTree(old);old.replaceWith(fresh);window.Alpine.initTree(fresh);});
                }
            } finally {clearTimeout(timer);}
        },
    },Object.getOwnPropertyDescriptors(incomeCostTable()));
}
