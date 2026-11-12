export function incomeInputs() {
    let pending=null, controller, root;
    return {
        incomeTab:'concept', incomePortal:'', incomeLoaded:false, incomeLoading:false, incomeError:'',
        init() {
            root=this.$el;
            Object.assign(this,JSON.parse(root.dataset.incomeState));
            this.incomeLoaded=root.dataset.incomeLoaded==='1';
            const target={'#renta-concepto':'concept','#renta-busqueda':'search','#captura-83':'capture'}[location.hash];
            if(target) this.openTab(target);
        },
        destroy() { controller?.abort(); },
        async openTab(tab) {
            if(!['concept','search','capture'].includes(tab)) return;
            this.incomeTab=tab;
            if(tab!=='capture') return;
            if(!this.incomeLoaded) {
                if(!pending) pending=this.loadCapture().finally(()=>{pending=null;});
                await pending;
            }
            if(this.incomeLoaded) window.dispatchEvent(new CustomEvent('income-portal',{detail:this.incomePortal}));
        },
        async loadCapture() {
            this.incomeLoading=true; this.incomeError='';
            let timer;
            try {
                if(window.gaFlushAutosaves && !await window.gaFlushAutosaves()) throw new Error('save');
                const url=new URL(root.dataset.incomeCaptureUrl,location.href);
                if(url.origin!==location.origin) throw new Error('origin');
                controller=new AbortController(); timer=setTimeout(()=>controller.abort(),20000);
                const response=await fetch(url.href,{credentials:'same-origin',cache:'no-store',signal:controller.signal,
                    headers:{Accept:'text/html','X-Requested-With':'fetch'}});
                if(!response.ok || !response.headers.get('Content-Type')?.includes('text/html')) throw new Error('response');
                const finalUrl=new URL(response.url || url.href);
                if(finalUrl.origin!==url.origin || finalUrl.pathname!==url.pathname) throw new Error('session');
                const page=new DOMParser().parseFromString(await response.text(),'text/html');
                const content=page.querySelector('[data-income-capture-content]');
                if(!content?.querySelector('#tabla-madre-83')) throw new Error('version');
                if(!root.isConnected) return;
                const host=root.querySelector('[data-income-capture-content]');
                window.Alpine.mutateDom(()=>{
                    host.replaceChildren(...content.childNodes);
                    window.Alpine.initTree(host);
                });
                this.incomeLoaded=true;
            } catch(error) {
                this.incomeError=error.message==='save'
                    ? 'Confirma el guardado pendiente antes de abrir las muestras.'
                    : 'No se pudieron cargar las muestras. Reintenta; si tu sesión venció, vuelve a ingresar. Tus respuestas permanecen aquí.';
            } finally { clearTimeout(timer); this.incomeLoading=false; }
        },
    };
}
