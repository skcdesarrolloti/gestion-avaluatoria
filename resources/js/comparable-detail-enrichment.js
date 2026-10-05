import { sourceUpdate } from './comparable-source-update.js';

// One request per listing, in order. Failure keeps the card, never invents facts.
export async function completeListingDetails(items, endpoint, onProgress = () => {}) {
    let completed = 0, failed = 0, stop = '';
    for (const [index, item] of items.entries()) {
        onProgress(index + 1, items.length);
        item.detailState = 'Leyendo ficha individual…';
        try {
            if (stop) throw new Error(stop);
            const body = new FormData(); body.set('source_url', item.row.source_url);
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
            body.set('_token', token);
            const controller = new AbortController(), timer = setTimeout(() => controller.abort(), 25000);
            let response, result;
            try {
                response = await fetch(endpoint, { method:'POST', body, credentials:'same-origin', signal:controller.signal,
                    headers:{ Accept:'application/json','X-CSRF-Token':token } });
                if ([401,419,429].includes(response.status)) stop = response.status===429 ? 'Límite de lectura alcanzado. Completa la ficha por copiar y pegar o reintenta después.' : 'Sesión vencida: inicia sesión para continuar.';
                if (!response.headers?.get?.('content-type')?.includes('application/json')) throw new Error(stop || 'No se recibió una ficha válida. Abre su enlace y copia el texto.');
                result = await response.json();
            } finally { clearTimeout(timer); }
            if (!response.ok || !result.ok || !result.row) throw new Error(stop || result.message || 'No se pudo leer la ficha.');
            const changes = sourceUpdate(item.row, result.row);
            item.row = { ...item.row, ...changes };
            item.detailState = 'Ficha individual leída · ' + (result.read_scope || 'datos publicados por verificar');
            completed++;
        } catch (error) {
            const reason = error.name==='AbortError' ? 'La ficha tardó demasiado. Abre el enlace y copia su texto.' : error.message;
            item.detailState = 'Ficha pendiente · ' + reason;
            item.row.comparability_notes = `${item.row.comparability_notes || ''}\nLectura de ficha pendiente: ${reason}`.trim().slice(-1600);
            failed++;
        }
    }
    return { completed, failed };
}
