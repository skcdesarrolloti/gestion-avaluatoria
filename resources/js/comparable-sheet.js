import { negotiation, capturePending, portalCounts, amount } from './comparable-negotiation.js';
import { tableWorkbook } from './xlsx-table.js';
import {unitPrice, unitFields, unitNumeric} from './comparable-unit-price.js';
import {orderExcelColumns} from './comparable-excel-columns.js';

const negotiationFields = [...unitFields, 'negotiation_discount', 'negotiated_amount', 'negotiation_percent', 'negotiation_kind', 'negotiation_source'];
export function updateCapture(entries, form, state) {
    for (const entry of entries) {
        const result = negotiation(entry.data);
        Object.assign(entry.data,unitPrice(entry.data));
        for (const key of unitFields) {
            const control=entry.controls.find(input=>input.name.endsWith(`[${key}]`));
            if (control) {control.value=entry.data[key];control.dataset.ready=unitNumeric.includes(key) && entry.data[key]!=='';}
        }
        entry.data.negotiated_amount = result.value;
        const offer = amount(entry.data.price_amount);
        entry.data.negotiation_percent = result.value === '' ? '' : (offer > 0 ? amount(entry.data.negotiation_discount) / offer * 100 : 0).toFixed(4);
        const calculated = entry.controls.find(input => input.name.endsWith('[negotiated_amount]'));
        if (calculated) { calculated.value = result.value; calculated.dataset.ready = result.value !== ''; }
        const percent = entry.controls.find(input => input.name.endsWith('[negotiation_percent]'));
        if (percent) { percent.value = entry.data.negotiation_percent; percent.dataset.ready = result.value !== ''; }
        const discount = entry.controls.find(input => input.name.endsWith('[negotiation_discount]'));
        discount?.setCustomValidity(result.error);
        entry.capturePending = entry.used ? capturePending(entry.data, form.dataset.phSubject === 'si') : [];
        const missing = new Set(entry.capturePending.map(([key]) => key));
        for (const input of entry.controls) input.closest('td')?.classList.toggle('capture-pending', missing.has(input.name.match(/\[([^\]]+)\]$/)?.[1]));
        entry.summary.textContent += entry.used ? ` · ${entry.capturePending.length} pendientes de corroboración` : '';
    }
    state.portalSummary = portalCounts(entries.filter(entry => entry.used).map(entry => entry.data));
    state.capturePendingCount = entries.filter(entry => entry.used && entry.capturePending.length).length;
}
export function arrangeSheet(entries, form, keys) {
    const headers = [...form.querySelectorAll('thead th')];
    const first = entries[0];
    if (!first) return;
    const columns = [...first.tr.cells].map((cell, index) => ({header: headers[index], index,
        key: cell.querySelector('[name]')?.name.match(/\[([^\]]+)\]$/)?.[1]}));
    const score = column => column.index === 0 ? -1 : keys.includes(column.key) ? keys.indexOf(column.key) : keys.length + column.index;
    columns.sort((a,b) => score(a) - score(b));
    for (const column of columns) form.querySelector('thead tr').append(column.header);
    for (const entry of entries) {
        const cells = [...entry.tr.cells];
        for (const column of columns) entry.tr.append(cells[column.index]);
    }
}
export function exportCapture(entries, form) {
    const used = entries.filter(entry => entry.used);
    if (!used.length) return;
    const first = entries[0], headers = [...form.querySelectorAll('thead th')];
    const columns = [{key:'id', label:'ID de muestra (conservar)',numeric:false}, {key:'capture_pending', label:'Pendientes por confirmar', numeric:false}];
    for (const [index, cell] of [...first.tr.cells].entries()) {
        const input = cell.querySelector('[name]'), key = input?.name.match(/\[([^\]]+)\]$/)?.[1];
        if (!key || ['id','component_key'].includes(key)) continue;
        const scope = cell.dataset.captureScope;
        const phExport = form.dataset.phSubject === 'si';
        if ((phExport && scope === 'nph' || !phExport && scope === 'ph') &&
            !used.some(entry => String(entry.data[key] ?? '').trim())) continue;
        const label = cell.querySelector('.comparable-field-label')?.textContent || headers[index].textContent.trim();
        const uniqueLabel = columns.some(column => column.label === label) ? `${label} (${columns.length})` : label;
        columns.push({key, label:uniqueLabel, options:input.tagName === 'SELECT' ? Object.fromEntries([...input.options].map(option=>[option.value,option.textContent.trim()])) : undefined,
            numeric: input.type === 'number' || [...unitNumeric,'price_amount','admin_fee','negotiated_amount','negotiation_percent','area_m2'].includes(key)});
    }
    const rows = used.map(entry => {
        const row = {id:{value:entry.data.id},capture_pending:{value: entry.capturePending.map(([,label]) => label).join('; '), pending:entry.capturePending.length > 0}};
        const missing = new Set(entry.capturePending.map(([key]) => key));
        for (const column of columns.slice(2)) {
            const input = entry.controls.find(control => control.name.endsWith(`[${column.key}]`));
            let value = entry.data[column.key] ?? '';
            if (input?.tagName === 'SELECT' && value) value = input.options[input.selectedIndex].textContent;
            if (column.numeric && value !== '') value = input?.type === 'number' ? Number(String(value).replace(',','.')) : amount(value) ?? value;
            row[column.key] = {value, pending:missing.has(column.key)};
        }
        return row;
    });
    const bytes = tableWorkbook(orderExcelColumns(columns), rows, form.dataset.phSubject === 'si' ? 'Comparables PH' : 'Comparables NPH', {
        appraisal:form.dataset.appraisalId,scope:form.querySelector('[name=component_scope]').value,version:Number(form.querySelector('[name=version]').value),exported_at:new Date().toISOString()});
    const url = URL.createObjectURL(new Blob([bytes], {type:'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'}));
    const link = document.createElement('a'); link.href = url; link.download = `comparables-${form.dataset.phSubject === 'si' ? 'PH' : 'NPH'}-v${form.querySelector('[name=version]').value}-${new Date().toISOString().replaceAll(':','-').slice(0,19)}.xlsx`;
    link.click(); setTimeout(() => URL.revokeObjectURL(url), 1000);
}
export { negotiationFields };
