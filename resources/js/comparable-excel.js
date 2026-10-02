import { flushModuleAutosaves } from './module-autosave.js';
import { excelPlan, applyExcelPlan, verifyExcelPlan } from './comparable-excel-plan.js';

export function comparableExcel() {
    let form, entries, changes = [], version, pendingFile;
    const request=async endpoint=>{
        const body=new FormData(); body.set('excel',pendingFile);body.set('_token',form.querySelector('[name=_token]').value);
        body.set('component_scope',form.querySelector('[name=component_scope]').value);
        const response=await fetch(endpoint,{method:'POST',body,headers:{Accept:'application/json'}});
        if (!response.headers.get('content-type')?.includes('application/json')) throw new Error('No se pudo leer Excel. Comprueba la sesión y el tamaño del archivo.');
        const result=await response.json();if (!response.ok || !result.ok) throw new Error(result.message || 'No se pudo guardar el Excel.');
        return result;
    };
    return {
        excelBusy:false, excelMessage:'', excelChanges:[], exportBusy:false, excelReady:false, excelUpdated:'',
        initExcel(element, collection) { form=element; entries=collection; this.excelUpdated=form.dataset.excelUpdated; },
        async prepareExcel(event) {
            const file=event.target.files?.[0]; if (!file) return;
            this.excelBusy=true; this.excelReady=false;this.excelMessage='Leyendo y revisando Excel…';this.excelChanges=[];changes=[];pendingFile=null;
            try {
                if (file.size > 5000000 || !/\.xlsx$/i.test(file.name)) throw new Error('Selecciona un archivo .xlsx de hasta 5 MB.');
                if (!await flushModuleAutosaves()) throw new Error('Confirma el guardado de la tabla antes de importar.');
                pendingFile=file;
                const result=await request(form.dataset.excelPreviewEndpoint);
                this.refresh(); version=result.version;
                if (Number(form.querySelector('[name=version]').value) !== version) throw new Error('La tabla cambió. Vuelve a revisar el Excel.');
                changes=excelPlan(entries,result.rows);
                this.excelChanges=changes.map(change=>({sample:change.sample,label:change.label,before:change.before || '(vacío)',after:change.value || '(vacío)'}));
                this.excelReady=true;
                this.excelMessage=changes.length ? `${changes.length} campos cambiados. Revisa y pulsa Guardar Excel actualizado.` : 'El Excel coincide con la tabla. Pulsa Guardar Excel actualizado para registrar esta carga.';
            } catch (error) { this.excelMessage=error.message; }
            finally { this.excelBusy=false; event.target.value=''; }
        },
        async applyExcel() {
            this.excelBusy=true;
            try {
                if (!pendingFile || !await flushModuleAutosaves()) throw new Error('Confirma el guardado antes de cargar el Excel.');
                if (Number(form.querySelector('[name=version]').value) !== version) throw new Error('La tabla cambió durante la revisión. Vuelve a revisar el Excel.');
                verifyExcelPlan(changes);
                const result=await request(form.dataset.excelSaveEndpoint);
                form.querySelector('[name=version]').value=String(result.version);
                this.excelUpdated=result.excel_updated;this.excelReady=false;
                try {applyExcelPlan(changes);this.refresh();}
                catch {throw new Error('El Excel quedó guardado en el servidor. Hay ediciones nuevas en la pantalla; consérvalas y recarga para confrontarlas.');}
                this.excelChanges=[]; changes=[];
                pendingFile=null;this.excelMessage='Excel actualizado y guardado. Ya puedes descargar la nueva versión.';
            } catch (error) { this.excelMessage=error.message; }
            finally { this.excelBusy=false; }
        },
        cancelExcel() {changes=[];pendingFile=null;this.excelReady=false;this.excelChanges=[];this.excelMessage='Revisión cancelada; la tabla conserva sus datos.';},
    };
}
