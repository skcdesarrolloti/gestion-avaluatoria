import { flushModuleAutosaves } from './module-autosave.js';
import { excelPlan, applyExcelPlan } from './comparable-excel-plan.js';

export function comparableExcel() {
    let form, entries, changes = [], version;
    return {
        excelBusy:false, excelMessage:'', excelChanges:[], exportBusy:false,
        initExcel(element, collection) { form=element; entries=collection; },
        async prepareExcel(event) {
            const file=event.target.files?.[0]; if (!file) return;
            this.excelBusy=true; this.excelMessage='Leyendo y revisando Excel…'; this.excelChanges=[]; changes=[];
            try {
                if (file.size > 5000000 || !/\.xlsx$/i.test(file.name)) throw new Error('Selecciona un archivo .xlsx de hasta 5 MB.');
                if (!await flushModuleAutosaves()) throw new Error('Confirma el guardado de la tabla antes de importar.');
                const body=new FormData(); body.set('excel',file); body.set('_token',form.querySelector('[name=_token]').value);
                body.set('component_scope',form.querySelector('[name=component_scope]').value);
                const response=await fetch(form.dataset.excelPreviewEndpoint,{method:'POST',body,headers:{Accept:'application/json'}});
                if (!response.headers.get('content-type')?.includes('application/json')) throw new Error('No se pudo leer Excel. Comprueba la sesión y el tamaño del archivo.');
                const result=await response.json(); if (!response.ok || !result.ok) throw new Error(result.message || 'No se pudo revisar el Excel.');
                this.refresh(); version=result.version;
                if (Number(form.querySelector('[name=version]').value) !== version) throw new Error('La tabla cambió. Vuelve a revisar el Excel.');
                changes=excelPlan(entries,result.rows);
                this.excelChanges=changes.map(change=>({sample:change.sample,label:change.label,before:change.before || '(vacío)',after:change.value || '(vacío)'}));
                this.excelMessage=changes.length ? `${changes.length} campos cambiados. Revisa y pulsa Aplicar cambios para guardar.` : 'El Excel coincide con la tabla; no hay cambios que aplicar.';
            } catch (error) { this.excelMessage=error.message; }
            finally { this.excelBusy=false; event.target.value=''; }
        },
        async applyExcel() {
            this.excelBusy=true;
            try {
                if (Number(form.querySelector('[name=version]').value) !== version) throw new Error('La tabla cambió durante la revisión. Vuelve a revisar el Excel.');
                applyExcelPlan(changes); this.refresh();
                form.dispatchEvent(new Event('input',{bubbles:true}));
                this.excelChanges=[]; changes=[];
                this.excelMessage=await flushModuleAutosaves() ? 'Cambios importados y guardados.' : 'Cambios aplicados en la tabla; guardado pendiente. Usa Guardar ahora/reintentar.';
            } catch (error) { this.excelMessage=error.message; }
            finally { this.excelBusy=false; }
        },
        cancelExcel() {changes=[];this.excelChanges=[];this.excelMessage='Revisión cancelada; la tabla conserva sus datos.';},
    };
}
