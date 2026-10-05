import { comparisonRows } from './comparable-source-facts.js';
export function intakeTable(groups, config) {
    const columns=new Map();
    const rows=groups.map(group => {
        const facts=comparisonRows(group,config);
        const values={};
        facts.forEach(fact => {
            const key=fact.sample || fact.raw || fact.label;
            columns.set(key,{key,label:fact.label,subject:fact.subject});
            values[key]={state:fact.state,value:group.rows.map((ad,i) => fact.values[i] ? `${ad.source_name || 'Fuente'}: ${fact.values[i]}` : '').filter(Boolean).join(' / ') || 'Pendiente'};
        });
        return {...group,values,confirmation:group.rows.every(r => r.capture_confirmation==='confirmed') ? 'Confirmado' : group.rows.every(r => r.capture_confirmation==='excluded') ? 'No participa' : 'Por confirmar'};
    });
    return {columns:[...columns.values()],rows};
}
