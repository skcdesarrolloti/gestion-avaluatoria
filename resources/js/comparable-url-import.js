import { fillRows } from './comparable-bulk-import.js';

const previews = new WeakMap();
const labels = { source_name: 'Fuente', source_url: 'Enlace', listing_code: 'Código', operation: 'Operación',
    property_type: 'Tipo', price_amount: 'Precio/canon COP', price_unit: 'Unidad de precio', area_m2: 'Área publicada m²',
    bedrooms: 'Alcobas', bathrooms: 'Baños', neighborhood: 'Barrio anunciado', address_hint: 'Dirección anunciada',
    listing_date: 'Fecha publicación', consulted_at: 'Consulta', comparability_notes: 'Pendientes' };

export function installComparableUrlImport() {
    document.addEventListener('input', event => {
        if (!event.target.matches?.('[data-listing-url]')) return;
        const panel = event.target.closest('[data-listing-reader]');
        event.target.removeAttribute('aria-invalid');
        previews.delete(panel);
        panel.querySelector('[data-listing-add]').disabled = true;
        panel.querySelector('[data-listing-preview]').textContent = '';
        panel.querySelector('[data-listing-message]').textContent = '';
    }, true);
    document.addEventListener('click', async event => {
        const button = event.target.closest?.('[data-listing-read], [data-listing-add], [data-listing-next]');
        if (!button) return;
        const panel = button.closest('[data-listing-reader]');
        const form = panel.closest('form');
        const message = panel.querySelector('[data-listing-message]');
        const add = panel.querySelector('[data-listing-add]');
        if (button.hasAttribute('data-listing-next')) {
            if (panel.getAttribute('aria-busy') === 'true') { event.preventDefault(); return; }
            previews.delete(panel);
            add.disabled = true;
            const input = panel.querySelector('[data-listing-url]');
            input.value = '';
            input.removeAttribute('aria-invalid');
            panel.querySelector('[data-listing-preview]').textContent = '';
            message.textContent = 'Captura limpia para otro inmueble. Las muestras agregadas siguen en la tabla.';
            return;
        }
        if (button.hasAttribute('data-listing-add')) {
            const result = previews.get(panel);
            if (!result) return;
            const counts = fillRows(form, [result.row], panel.dataset.defaultQuery ?? '');
            message.textContent = counts.count ? 'Muestra agregada a la tabla. Puedes pegar el siguiente enlace aquí. Revisa los campos y el estado de guardado abajo.'
                : counts.duplicates ? 'Este enlace ya está en la captura. Revisa la muestra existente.'
                    : counts.suspected ? 'Posible duplicado: no se agregó. Revisa las muestras indicadas en la tabla; la vista previa se conserva.' : 'No hay filas vacías: límite de 60 muestras.';
            if (counts.suspected) return;
            previews.delete(panel);
            add.disabled = true;
            if (counts.count) {
                panel.querySelector('[data-listing-url]').value = '';
                panel.querySelector('[data-listing-preview]').textContent = '';
                panel.querySelector('[data-listing-url]').focus();
            }
            return;
        }
        const input = panel.querySelector('[data-listing-url]');
        if (!input.reportValidity() || !input.value.trim()) { message.textContent = 'Pega el enlace de un aviso.'; return; }
        const requested = input.value.trim();
        previews.delete(panel);
        add.disabled = true;
        button.disabled = true;
        input.readOnly = true;
        panel.setAttribute('aria-busy', 'true');
        const preview = panel.querySelector('[data-listing-preview]');
        preview.textContent = '';
        message.textContent = 'Leyendo el aviso…';
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 25000);
        try {
            const body = new FormData();
            body.set('source_url', requested);
            const token = form.querySelector('input[name="_token"]');
            if (token) body.set('_token', token.value);
            const response = await fetch(panel.dataset.endpoint, { method: 'POST', body, signal: controller.signal,
                headers: { Accept: 'application/json', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content ?? token?.value ?? '' } });
            if (!response.headers.get('content-type')?.includes('application/json')) throw new Error('No se recibió una respuesta válida. Comprueba tu sesión e intenta de nuevo.');
            const result = await response.json();
            if (!response.ok || !result.ok) throw new Error(result.message || 'No fue posible leer el aviso.');
            if (!panel.isConnected || input.value.trim() !== requested) return;
            Object.entries(labels).forEach(([key, label]) => {
                if (result.row[key] === undefined) return;
                const line = document.createElement('p');
                let value = result.row[key];
                if (key === 'price_amount') value = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(Number(String(value).replace(',', '.')));
                if (key === 'price_unit') value = { precio_total: 'Precio total', canon_mensual: 'Canon mensual' }[value] ?? value;
                line.textContent = `${label}: ${value}`;
                preview.append(line);
            });
            previews.set(panel, result);
            message.textContent = `${result.title}. ${result.warning}`;
            add.disabled = false;
        } catch (error) {
            input.setAttribute('aria-invalid', 'true');
            message.textContent = error.name === 'AbortError' ? 'La lectura tardó demasiado. Reintenta o pega el texto del aviso abajo.' : error.message;
        } finally {
            clearTimeout(timeout);
            button.disabled = false;
            input.readOnly = false;
            panel.removeAttribute('aria-busy');
        }
    });
}
