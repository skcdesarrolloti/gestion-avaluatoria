import { fillRows } from './comparable-bulk-import.js';
import { flushModuleForm } from './module-autosave.js';
import { completeListingDetails } from './comparable-detail-enrichment.js';
import { comparableUrlKey } from './comparable-review.js';

// First acknowledge the selected intake. Only then read its accepted listings.
export async function captureSelectedDetails(form, items, query, endpoint, progress, includeRegistered = false,
    io = { fill:fillRows, save:flushModuleForm, read:completeListingDetails }) {
    const accepted = new Set();
    const explicit = includeRegistered ? items.filter(item=>item.tone==='registered') : [];
    const counts = io.fill(form,items.map(item=>item.row),query,undefined,
        {deferDuplicateReview:true,onInserted:row=>accepted.add(comparableUrlKey(row.source_url))});
    const summary=`${counts.count} avisos nuevos; ${counts.enriched || 0} existentes complementados; ${counts.duplicates} enlaces ya registrados sin duplicar; ${counts.overflow} sin cargar.`;
    progress(`${summary} Paso 1: guardando los avisos incorporados…`);
    if (!await io.save(form)) return `${summary} Guardado pendiente. No se inició la investigación de fichas. Pulsa Guardar matriz y conserva esta página.`;
    for (const item of explicit) accepted.add(comparableUrlKey(item.row.source_url));
    const unique = new Map();
    for (const item of items) if (accepted.has(comparableUrlKey(item.row.source_url))) unique.set(comparableUrlKey(item.row.source_url),item);
    const picked=[...unique.values()];
    if (!endpoint || !picked.length) return `${summary} Guardado confirmado. No hay fichas nuevas para investigar.`;
    picked.forEach(item=>{item.detailState='Ficha pendiente de investigación';});
    try {
        const details = await io.read(picked,endpoint,
            (number,total)=>progress(`${summary} Paso 1 guardado. Paso 2: investigando ficha ${number} de ${total}…`),
            async item=>{
                io.fill(form,[item.row],query,undefined,{deferDuplicateReview:true});
                if (!await io.save(form)) throw new Error('Guardado pendiente del complemento.');
            });
        return `${summary} ${details.completed} fichas leídas; ${details.failed} pendientes de completar. Guardado confirmado de los avisos y sus complementos.`;
    } catch {
        return `${summary} Los avisos iniciales están guardados. Se interrumpió el complemento: revisa el aviso de guardado y pulsa Guardar matriz antes de salir.`;
    }
}
