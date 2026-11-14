const dayMs=86400000;
export function ledgerNumber(value) {
    if(value===undefined || value===null || String(value).trim()==='') return null;
    let s=String(value).trim().replace(/[$ ]/g,'');
    if(/^\d{1,3}(\.\d{3})+(,\d{1,2})?$/.test(s)) s=s.replace(/\./g,'');
    s=s.replace(',','.');return /^-?\d+(\.\d+)?$/.test(s) && Number.isFinite(+s)?+s:null;
}
const stamp=s=>{const n=Date.parse(s+'T00:00:00Z');return /^\d{4}-\d{2}-\d{2}$/.test(s || '') && Number.isFinite(n) && new Date(n).toISOString().slice(0,10)===s?n:null;};
const sumKnown=values=>values.every(v=>v!==null)?values.reduce((a,b)=>a+b,0):null;
const rounded=n=>n===null?null:Math.round((n+Number.EPSILON)*100)/100;
export const ledgerMoney=n=>n===null || n===undefined?'Pendiente':new Intl.NumberFormat('es-CO',{maximumFractionDigits:2}).format(n);
export function leaseSeries(lease,year,cutoff='',vacancies=[]) {
    const start=stamp(lease.start_date),end=stamp(lease.end_date),cut=stamp(cutoff);
    const factor={mensual:1,trimestral:3,anual:12}[lease.initial_rent_period];
    const initial=ledgerNumber(lease.initial_rent_amount),rule=lease.history_rule;
    const adjustments=Object.values(lease.rent_adjustments || {}).filter(r=>Object.values(r).some(Boolean));
    const invalid=adjustments.some(r=>stamp(r.date)===null || !r.source?.trim() || (ledgerNumber(r.rate)===null)===(ledgerNumber(r.amount)===null));
    const dates=adjustments.map(r=>r.date),duplicate=new Set(dates).size!==dates.length;
    const changes=Object.fromEntries(adjustments.filter(r=>stamp(r.date)!==null).map(r=>[r.date,r]));
    const first=stamp(lease.first_adjustment_date),expected=new Set();
    if(rule==='annual' && first!==null) for(let y=new Date(first).getUTCFullYear();y<=year;y++) {
        const d=new Date(first),m=d.getUTCMonth(),day=Math.min(d.getUTCDate(),new Date(Date.UTC(y,m+1,0)).getUTCDate());
        expected.add(new Date(Date.UTC(y,m,day)).toISOString().slice(0,10));
    }
    let current=start!==null && initial!==null && factor && ['annual','schedule','constant'].includes(rule) && !invalid && !duplicate
        && !(rule==='annual' && first===null)?initial/factor:null;
    if(rule==='constant' && adjustments.length) current=null;
    const rows=[];
    // Advance from inception; the bounded range matches the accepted date interval.
    const begin=start===null?Date.UTC(year,0,1):Math.min(start,Date.UTC(year,0,1));
    const stop=Date.UTC(year+1,0,1),monthly={};
    for(let t=begin;t<stop;t+=dayMs) {
        const d=new Date(t),date=d.toISOString().slice(0,10),month=date.slice(0,7);
        if(t>=start && expected.has(date) && !changes[date]) current=null;
        const change=changes[date];
        if(change && !invalid && !duplicate && rule!=='constant' && start!==null && t>=start) {
            const fixed=ledgerNumber(change.amount),rate=ledgerNumber(change.rate);
            current=fixed!==null?fixed:current===null?null:current*(1+rate/100);
            if(lease.reference_round) current=rounded(current);
        }
        if(d.getUTCFullYear()!==year) continue;
        const bucket=monthly[month] ??= {total:0,reference:0,known:true,days:0,vacant:0};
        if(start===null || t<start || (end!==null && t>end) || (cut!==null && t>cut)) continue;
        const vacant=vacancies.some(v=>date>=v.start && date<=v.end) || lease.monthly_history?.[month]?.occupancy==='vacant';
        bucket.days++;if(vacant) bucket.vacant++;
        if(current===null || !Number.isFinite(current)) bucket.known=false;
        else {
            const daily=current/new Date(Date.UTC(year,d.getUTCMonth()+1,0)).getUTCDate();
            bucket.total+=daily;if(!vacant) bucket.reference+=daily;
        }
    }
    for(let m=1;m<=12;m++) {
        const key=year+'-'+String(m).padStart(2,'0'),row=lease.monthly_history?.[key] || {},b=monthly[key];
        const rent=ledgerNumber(row.rent_billed),vat=ledgerNumber(row.vat_billed),rate=ledgerNumber(row.vat_rate);
        const vatBill=vat??(rent!==null && rate!==null?rounded(rent*rate/100):null);
        const received=ledgerNumber(row.rent_paid),costs=['tax','insurance','maintenance','administration','utilities','contingencies','commission'].map(k=>ledgerNumber(row['cost_'+k]));
        const ownerCost=sumKnown(costs),days=new Date(Date.UTC(year,m,0)).getUTCDate();
        const occupied=['occupied','grace'].includes(row.occupancy)?days:row.occupancy==='vacant'?0:row.occupancy==='partial'?ledgerNumber(row.occupied_days):null;
        rows.push({month:key,contract:rounded(b?.days && b.known?b.total:null),contractDays:b?.days || 0,
            reference:rounded(b?.days && b.known?b.reference:null),vacantDays:b?.vacant || 0,
            billed:rent,vat:vatBill,vatEstimated:vat===null && vatBill!==null,
            totalBilled:rounded(sumKnown([rent,ledgerNumber(row.admin_billed),vatBill])),
            totalReceived:rounded(sumKnown([received,ledgerNumber(row.admin_received),ledgerNumber(row.vat_paid)])),
            rentReceived:received,ownerCost:rounded(ownerCost),net:rounded(received!==null && ownerCost!==null?received-ownerCost:null),occupied,days,
            changes:adjustments.filter(a=>a.date?.startsWith(key)).map(a=>a.date+': '+(a.amount?ledgerMoney(ledgerNumber(a.amount))+' COP':a.rate+' %')).join('; '),
            future:cut!==null && Date.UTC(year,m-1,1)>cut,closed:cut===null || Date.UTC(year,m,0)<=cut,
            review:row.review || '',occupancy:row.occupancy || 'unknown'});
    }
    return rows;
}

export function historySuggestions(lease,cutoff,catalog,type='') {
    const out={},cut=stamp(cutoff);
    for(const [key,meta] of Object.entries(catalog)) {
        let base=0,cost=0;const months=[],sources=[];
        for(const [month,row] of Object.entries(lease?.monthly_history || {})) {
            const last=stamp(month+'-01')===null?null:Date.UTC(+month.slice(0,4),+month.slice(5,7),0);
            const amount=ledgerNumber(row['cost_'+key]),rent=ledgerNumber(row.rent_paid);
            if(cut===null || last===null || last>cut || row.review!=='verified' || !row.support?.trim() || amount===null || rent===null) continue;
            base+=rent;cost+=amount;months.push(month);sources.push(row.support);
        }
        if(base>0) out[key]={rate:cost/base*100,basis:'effective',months:months.sort(),cost,base,
            source:`Historial revisado de ${lease.reference || 'contrato'} (${type || 'tipo por verificar'}). ${months.length} meses: ${months.sort().join(', ')}. ${meta.label}: ${cost} COP / canon recibido ${base} COP × 100. Soportes: ${[...new Set(sources)].join('; ')}`.slice(0,2000)};
    }
    return out;
}
