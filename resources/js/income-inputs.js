export function incomeInputs() {
    let pending=null, controller, root;
    return {
        incomeTab:'concept', incomePortal:'', incomeLoaded:false, incomeLoading:false, incomeError:'',
        incomePortals:[], incomeSources:[], incomePortalBusy:false, incomePortalError:'',
        get incomePortalIndex() { return this.incomePortals.indexOf(this.canonicalPortal(this.incomePortal)); },
        get incomeNextPortal() { return this.incomePortalIndex<0 ? '' : this.incomePortals[this.incomePortalIndex+1] || ''; },
        init() {
            root=this.$el;
            Object.assign(this,JSON.parse(root.dataset.incomeState));
            this.incomeLoaded=root.dataset.incomeLoaded==='1';
            this.$watch?.('incomePortal',value=>{
                const label=this.canonicalPortal(value);
                if(label && label!==value) { this.incomePortal=label; return; }
                this.rememberStep();
            });
            const target={'#renta-concepto':'concept','#renta-busqueda':'search','#captura-83':'capture'}[location.hash];
            if(target) this.openTab(target);
        },
        destroy() { controller?.abort(); },
        canonicalPortal(value) {
            const key=text=>String(text).normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim().replace(/ inmuebles$/,'');
            return this.incomeSources.find(source=>key(source)===key(value)) || '';
        },
        rememberStep() {
            const url=new URL(location.href);
            url.searchParams.set('renta_paso',this.incomeTab);
            if(this.incomePortal) url.searchParams.set('renta_portal',this.incomePortal);
            window.history?.replaceState(window.history.state,'',url.href);
        },
        async choosePortal(portal, view='capture') {
            portal=this.canonicalPortal(portal);
            if(this.incomePortalBusy || !this.incomeSources.includes(portal)) return false;
            this.incomePortalBusy=true; this.incomePortalError='';
            try {
                if(window.gaFlushAutosaves && !await window.gaFlushAutosaves()) {
                    this.incomePortalError='Hay cambios pendientes de guardar. Reintenta el guardado antes de cambiar de portal.';
                    return false;
                }
                this.incomePortal=portal;
                await this.openTab(view==='search'?'search':'capture');
                if(view!=='search' && !this.incomeLoaded) return false;
                window.dispatchEvent(new CustomEvent('income-source-view',{detail:view}));
                await this.$nextTick?.();
                const target=root.querySelector(view==='review'?'#capture-review':view==='search'?'#renta-busqueda':'#capture-sources');
                target?.scrollIntoView?.({block:'start',behavior:'smooth'});
                return true;
            } finally { this.incomePortalBusy=false; }
        },
        async nextPortal() {
            return this.incomeNextPortal ? this.choosePortal(this.incomeNextPortal) : false;
        },
        async openTab(tab) {
            if(!['concept','search','capture'].includes(tab)) return;
            this.incomeTab=tab;
            this.rememberStep();
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
