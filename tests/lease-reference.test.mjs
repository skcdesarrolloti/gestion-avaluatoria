import test from 'node:test';
import assert from 'node:assert/strict';
import {referenceSeries} from '../resources/js/lease-reference.js';
const rates={2021:5.62,2022:13.12,2023:9.28,2024:5.2,2025:5.1};
const base=()=>({start_date:'2022-10-01',initial_rent_amount:'2000000',initial_rent_period:'mensual',history_rule:'ipc',history_calendar:'january',history_ipc_points:'2',
    history_admin_amount:'300000',history_admin_rule:'ipc',history_vat_rate:'19',monthly_history:{},rent_adjustments:{},admin_adjustments:{},vacancy_periods:{}});
const get=(l,date='2026-11-13')=>referenceSeries(l,date,rates,'2026-09-30');
test('referencia: ejemplo octubre 2022, IPC+2, todos los meses hasta hoy, sin convertirla en pagos',()=>{
    const l=base(),before=JSON.stringify(l),r=get(l);
    assert.equal(r.rows.length,50);assert.deepEqual(r.years,['2022','2023','2024','2025','2026']);
    for(const [month,value] of Object.entries({'2022-10':2000000,'2023-01':2302400,'2024-01':2562110.72,'2025-01':2746582.69,'2026-01':2941590.06}))
        assert.equal(r.rows.find(r=>r.month===month).reference,value);
    const last=r.rows.at(-1);assert.equal(last.admin,410033.93);assert.equal(last.total,3910526.1);assert.equal(last.afterCutoff,true);
    assert.equal(last.rentReceived,null);assert.equal(last.state,'Continuidad supuesta');assert.equal(JSON.stringify(l),before);
});
test('referencia: cuota inicial PH desde fecha conocida y excepción reemplaza IPC sin duplicarlo',()=>{
    const l=base();l.history_admin_date='2024-06-01';l.admin_adjustments.a={date:'2025-01-01',amount:'320000',source:'Acta PH'};
    l.rent_adjustments.a={date:'2023-01-01',rate:'10',source:'Otrosí'};
    const r=get(l).rows;assert.equal(r[3].reference,2200000);assert.equal(r[3].admin,null);
    assert.equal(r.find(r=>r.month==='2024-06').admin,300000);assert.equal(r.find(r=>r.month==='2025-01').admin,320000);
    assert.equal(r.at(-1).admin,336320);
});
test('referencia: aniversario, fecha personalizada y año sin IPC no reutilizan otro índice',()=>{
    const l=base();l.history_calendar='anniversary';let r=get(l).rows;
    assert.equal(r.find(r=>r.month==='2023-09').reference,2000000);assert.equal(r.find(r=>r.month==='2023-10').reference,2302400);
    l.history_calendar='custom';l.first_adjustment_date='2023-02-01';r=get(l).rows;
    assert.equal(r.find(r=>r.month==='2023-01').reference,2000000);assert.equal(r.find(r=>r.month==='2023-02').reference,2302400);
    l.history_calendar='january';assert.equal(get(l,'2027-02-20').rows.at(-1).reference,null);
    assert.match(get(l,'2027-02-20').issues.join(' '),/cobertura del IPC/);
});
test('referencia: vacancias superpuestas, mes bisiesto y PH que continúa; no infiere vacancia por falta de pagos',()=>{
    const l={...base(),start_date:'2024-02-01',initial_rent_amount:'2900',history_rule:'constant',history_admin_amount:'290',history_admin_rule:'constant',history_vat_rate:'0'};
    l.vacancy_periods={a:{start:'2024-02-01',end:'2024-02-10'},b:{start:'2024-02-05',end:'2024-02-15'}};
    const r=get(l,'2024-02-29').rows[0];assert.equal(r.vacantDays,15);assert.equal(r.reference,1400);assert.equal(r.admin,290);assert.equal(r.total,1690);
    l.vacancy_periods={};l.monthly_history={'2024-02':{rent_paid:'0'}};assert.equal(get(l,'2024-02-29').rows[0].reference,2900);
    l.monthly_history['2024-02'].occupancy='vacant';assert.equal(get(l,'2024-02-29').rows[0].reference,0);
});
test('referencia: importes manuales previos preservados, inicial parcial y fin del contrato',()=>{
    const l={...base(),start_date:'2024-02-15',end_date:'2024-02-29',initial_rent_amount:'2900',history_rule:'constant',history_vat_rate:''};
    l.monthly_history={'2024-02':{admin_billed:'123',vat_rate:'0',rent_paid:'789'}};
    const r=get(l,'2024-03-20').rows;assert.equal(r[0].reference,1500);assert.equal(r[0].admin,123);assert.equal(r[0].vat,0);assert.equal(r[0].adminManual,true);
    assert.equal(r[1].reference,null);assert.equal(r[1].state,'Fuera de vigencia');assert.equal(l.monthly_history['2024-02'].rent_paid,'789');
});
test('referencia: borradores incompletos, rango excesivo e IVA pendiente no generan ceros ficticios',()=>{
    assert.equal(get({...base(),start_date:'2022-02-30'}).rows.length,0);
    assert.equal(get({...base(),start_date:'1900-01-01'}).rows.length,0);
    assert.equal(get({...base(),history_ipc_points:''}).rows[0].reference,null);
    assert.equal(get({...base(),history_vat_rate:''}).rows[0].total,null);
    const l=base();l.vacancy_periods.a={start:'2023-01-01',end:''};assert.match(get(l).issues[0],/Completa inicio/);
});
