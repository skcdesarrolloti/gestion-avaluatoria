import {leaseSeries,ledgerNumber} from './lease-ledger.js';
const valid=s=>/^\d{4}-\d{2}-\d{2}$/.test(s || '') && +s.slice(0,4)>=1900 && +s.slice(0,4)<=2199
    && Number.isFinite(Date.parse(s+'T00:00:00Z')) && new Date(s+'T00:00:00Z').toISOString().slice(0,10)===s;
const round=n=>n===null?null:Math.round((n+Number.EPSILON)*100)/100;
const populated=r=>Object.values(r).some(v=>v!=='' && v!==null && v!==undefined);
export function referenceLease(lease,rates,until,admin=false) {
    const rule=admin?lease.history_admin_rule:lease.history_rule,start=admin?(lease.history_admin_date || lease.start_date):lease.start_date;
    const manual=Object.fromEntries(Object.entries((admin?lease.admin_adjustments:lease.rent_adjustments) || {}).filter(([,r])=>populated(r)));
    const out={...lease,start_date:start,reference_round:true,rent_adjustments:manual};
    if(admin) Object.assign(out,{initial_rent_amount:lease.history_admin_amount,initial_rent_period:'mensual',end_date:'',monthly_history:{},history_rule:rule});
    if(rule==='constant') out.history_rule='schedule';
    if(admin && ledgerNumber(lease.history_admin_amount)===0 && !Object.keys(manual).length) {out.history_rule='constant';return out;}
    if(rule!=='ipc' || !valid(start)) return out;
    const points=admin?0:ledgerNumber(lease.history_ipc_points),calendar=admin?'january':lease.history_calendar;
    let first=calendar==='january'?(+start.slice(0,4)+1)+'-01-01':calendar==='custom'?lease.first_adjustment_date:'';
    if(calendar==='anniversary') {
        const y=+start.slice(0,4)+1,m=+start.slice(5,7),d=Math.min(+start.slice(8),new Date(Date.UTC(y,m,0)).getUTCDate());
        first=`${y}-${String(m).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    }
    out.history_rule='annual';out.first_adjustment_date=first;
    if(points===null || !valid(first) || first<=start) {out.initial_rent_amount='';return out;}
    for(let year=+first.slice(0,4);year<=+until.slice(0,4);year++) {
        const m=+first.slice(5,7),day=Math.min(+first.slice(8),new Date(Date.UTC(year,m,0)).getUTCDate());
        const date=`${year}-${String(m).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
        if(Object.values(manual).some(r=>r.date===date) || rates[year-1]===undefined) continue;
        const rate=Number(rates[year-1])+points;
        if(!Number.isFinite(rate) || rate<=-100) continue;
        out.rent_adjustments={...out.rent_adjustments,['ipc'+year]:{date,rate:String(rate),source:`DANE IPC diciembre ${year-1}: ${rates[year-1]} %${admin?'':`; + ${points} puntos`}`}};
    }
    return out;
}
export function referenceSeries(lease,today,rates={},cutoff='') {
    const empty=message=>({rows:[],years:[],message,issues:[],sources:[]});
    if(!valid(lease.start_date) || !valid(today) || lease.start_date>today) return empty('Indica la fecha inicial para generar el historial hasta el mes actual.');
    if(ledgerNumber(lease.initial_rent_amount)===null || !lease.initial_rent_period) return empty('Indica el canon inicial y su periodicidad. La tabla se genera automáticamente.');
    const months=(+today.slice(0,4)- +lease.start_date.slice(0,4))*12 + +today.slice(5,7)- +lease.start_date.slice(5,7)+1;
    if(months>1200) return empty('La referencia admite hasta 100 años por contrato.');
    const vacancies=Object.values(lease.vacancy_periods || {}).filter(populated),issues=[];
    const invalidVacancy=vacancies.some(v=>!valid(v.start) || !valid(v.end) || v.end<v.start);
    if(invalidVacancy) issues.push('Completa inicio y final de las vacancias: no se aplica una fila incompleta.');
    const intervals=vacancies.filter(v=>valid(v.start) && valid(v.end) && v.end>=v.start);
    const rent=referenceLease(lease,rates,today),admin=referenceLease(lease,rates,today,true),rows=[];
    const sources=Object.values(rent.rent_adjustments).filter(r=>r.source?.startsWith('DANE') && r.date<=today);
    for(let year=+lease.start_date.slice(0,4);year<=+today.slice(0,4);year++) {
        const rentRows=leaseSeries(rent,year,'',intervals),adminRows=leaseSeries(admin,year);
        for(let m=0;m<12;m++) {
            const r=rentRows[m],key=r.month,actual=lease.monthly_history?.[key] || {};
            if(key<lease.start_date.slice(0,7) || key>today.slice(0,7)) continue;
            const fee=ledgerNumber(actual.admin_billed)??adminRows[m].contract;
            const rate=ledgerNumber(actual.vat_rate)??ledgerNumber(lease.history_vat_rate)??(['no','no_aplica'].includes(lease.rent_charges_vat)?0:null);
            const vat=rate!==null && r.reference!==null?round(r.reference*rate/100):null;
            const total=[r.reference,fee,vat].some(n=>n===null)?null:round(r.reference+fee+vat);
            const state=!r.contractDays?'Fuera de vigencia':r.vacantDays?`${r.vacantDays} días de vacancia informada`:
                actual.occupancy==='occupied'?'Ocupación documentada':actual.occupancy==='partial'?'Ocupación parcial: revisar detalle':'Continuidad supuesta';
            rows.push({...r,admin:fee,adminManual:ledgerNumber(actual.admin_billed)!==null,vat,total,state,
                afterCutoff:valid(cutoff) && key>cutoff.slice(0,7),current:key===today.slice(0,7),
                note:[r.changes,adminRows[m].changes?'PH: '+adminRows[m].changes:''].filter(Boolean).join(' · ')});
        }
    }
    if(rows.some(r=>r.contractDays && r.reference===null)) issues.push('Hay cánones pendientes: revisa la regla, las excepciones y la cobertura del IPC. No se prolonga un IPC faltante.');
    if(rows.some(r=>r.admin===null)) issues.push('Administración pendiente: basta una cuota inicial y su fecha; corrige únicamente las excepciones.');
    if(rows.some(r=>r.contractDays && r.vat===null)) issues.push('Indica la tarifa de IVA aplicable o 0 si no aplica para obtener el total.');
    return {rows,years:[...new Set(rows.map(r=>r.month.slice(0,4)))],issues,sources,
        message:`${rows.length} meses generados. ${intervals.length?'Vacancias informadas por fechas; los intervalos superpuestos se cuentan una sola vez.':'Sin intervalos de vacancia informados; la continuidad es un supuesto de referencia.'}`};
}
