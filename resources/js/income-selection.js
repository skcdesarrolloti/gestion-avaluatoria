import { postAutosave } from './autosave-session.js';

export function incomeSelection() {
    return {
        sample:'',category:'',reason:'',support:'',drafts:{},excluded:{},issues:{},
        busy:false,applied:false,message:'',page:0,total:0,
        init() { Object.assign(this,JSON.parse(this.$el.dataset.selectionState)); },
        async choose(id) {
            if(this.busy || this.applied) return;
            this.$refs.sample.value=this.sample;
            this.busy=true;
            try {
                if(this.sample && window.gaFlushAutosaves && !await window.gaFlushAutosaves()) {
                    this.message='Confirma el borrador pendiente antes de cambiar de muestra.';return;
                }
            if(this.sample) this.drafts[this.sample]={category:this.category,reason:this.reason,support:this.support};
            this.sample=id;
            Object.assign(this,{category:'',reason:'',support:''},this.drafts[id] || {});
            this.issues={};
            this.$nextTick(()=>{
                this.$refs.sample.dispatchEvent(new Event('change',{bubbles:true}));
                this.$refs.reason.focus();
            });
            } catch(error) {this.message=error.message || 'No se pudo guardar el borrador. Reintenta.';}
            finally {this.busy=false;}
        },
        async decide(operation) {
            if(this.busy || this.applied) return;
            this.busy=true;this.message='Guardando la justificación…';this.issues={};
            try {
                if(window.gaFlushAutosaves && !await window.gaFlushAutosaves()) throw new Error('Confirma el borrador pendiente antes de aplicar la decisión.');
                const form=this.$refs.form,body=new FormData(form);body.set('operation',operation);
                const controller=new AbortController(),timer=setTimeout(()=>controller.abort(),20000);
                let response,result;
                try {response=await postAutosave(form,body,controller.signal);result=await response.json();}
                finally {clearTimeout(timer);}
                this.issues=result.errors || {};
                if(!response.ok || !result.ok) throw new Error(result.message || 'No se confirmó la decisión.');
                this.applied=true;
                this.$el.closest('[data-methodology-version-group]')?.querySelectorAll('[data-workflow-version] input[name="version"]')
                    .forEach(input=>{input.value=String(result.version);});
                this.message='Decisión guardada. Actualizando estadísticas…';
                await this.refresh();
            } catch(error) { this.message=error.name==='AbortError'?'No se recibió confirmación. Actualiza la vista antes de repetir la decisión.':(error.message || 'No se pudo guardar. Conserva esta pantalla y reintenta.'); }
            finally {this.busy=false;}
        },
        async refresh() {
            const controller=new AbortController(),timer=setTimeout(()=>controller.abort(),20000);
            try {
                const response=await fetch(location.href,{credentials:'same-origin',cache:'no-store',signal:controller.signal,headers:{Accept:'text/html'}});
                if(!response.ok || new URL(response.url || location.href).pathname!==location.pathname) throw new Error('Vuelve a ingresar y actualiza la vista.');
                const page=new DOMParser().parseFromString(await response.text(),'text/html');
                const fresh=page.querySelector('[data-income-selection]'),old=this.$refs.form.closest('[data-income-selection]');
                if(!fresh) throw new Error('No se pudo actualizar el análisis. La decisión guardada se conserva.');
                window.Alpine.mutateDom(()=>{window.Alpine.destroyTree(old);old.replaceWith(fresh);window.Alpine.initTree(fresh);});
            } catch(error) {this.message=error.message || 'No se pudo actualizar la vista. Reintenta.';}
            finally {clearTimeout(timer);}
        },
    };
}
