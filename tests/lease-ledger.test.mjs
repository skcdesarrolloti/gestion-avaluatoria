import test from 'node:test';
import assert from 'node:assert/strict';
import {leaseSeries,historySuggestions,ledgerNumber} from '../resources/js/lease-ledger.js';
import {incomeCostTable} from '../resources/js/income-cost-table.js';
const base=()=>({reference:'Oficina y garaje',start_date:'2022-01-01',end_date:'2026-12-31',initial_rent_amount:'1000',initial_rent_period:'mensual',
    history_rule:'annual',first_adjustment_date:'2023-01-01',rent_adjustments:{a:{date:'2023-01-01',rate:'10',amount:'',source:'IPC documentado de prueba'}},monthly_history:{}});
test('historia: reconstruye desde inicio, aumenta en aniversario y bloquea índice faltante sin inventar IPC',()=>{
    const s=base();assert.equal(leaseSeries(s,2022)[0].contract,1000);assert.equal(leaseSeries(s,2023)[11].contract,1100);
    assert.equal(leaseSeries(s,2024)[0].contract,null);assert.equal(s.initial_rent_amount,'1000');
    s.rent_adjustments.b={date:'2024-01-01',amount:'1300',source:'Otrosí'};assert.equal(leaseSeries(s,2024)[0].contract,1300);
});
test('historia: cambio a mitad de mes, gracia expresa, bisiesto y corte del avalúo',()=>{
    const s={...base(),history_rule:'schedule',start_date:'2024-02-15',rent_adjustments:{a:{date:'2024-02-20',amount:'0',source:'Gracia'}}};
    assert.equal(leaseSeries(s,2024,'2024-02-29')[1].contract,172.41);
    assert.equal(leaseSeries(s,2024,'2024-02-29')[2].contract,null);
    assert.equal(leaseSeries(s,2024,'2024-02-20')[1].closed,false);
    assert.equal(leaseSeries(s,2024,'2024-02-29')[1].closed,true);
    const a={...base(),first_adjustment_date:'2024-02-29',rent_adjustments:{a:{date:'2024-02-29',rate:'10',source:'Índice'},b:{date:'2025-02-28',rate:'10',source:'Índice'}}};
    assert.equal(leaseSeries(a,2025)[2].contract,1210);
});
test('historia: facturado y recibido separados; PH recibida no se confunde con PH pagada',()=>{
    const s=base();s.monthly_history['2022-01']={rent_billed:'1000',vat_rate:'19',admin_billed:'200',rent_paid:'800',admin_paid:'200'};
    let r=leaseSeries(s,2022)[0];assert.equal(r.totalBilled,1390);assert.equal(r.totalReceived,null);assert.equal(r.occupied,null);assert.equal(r.net,null);
    Object.assign(s.monthly_history['2022-01'],{admin_received:'100',vat_paid:'152',occupancy:'occupied'});
    r=leaseSeries(s,2022)[0];assert.equal(r.totalReceived,1052);assert.equal(r.occupied,31);assert.equal(r.rentReceived,800);
});
test('historia: fechas/importes inválidos y cambios incompletos no generan cifras',()=>{
    for(const edit of [{start_date:'2022-13-01'},{history_rule:''},{initial_rent_period:'otro'},{initial_rent_amount:''}]) assert.equal(leaseSeries({...base(),...edit},2022)[0].contract,null);
    const s=base();s.rent_adjustments.a.source='';assert.equal(leaseSeries(s,2023)[0].contract,null);
    assert.equal(ledgerNumber('3.500.000,50'),3500000.5);assert.equal(ledgerNumber(''),null);
});
test('costos: porcentaje ponderado, meses comunes revisados y anteriores al corte; no usa IVA ni PH',()=>{
    const s=base();s.monthly_history={'2022-01':{rent_paid:'1000',cost_insurance:'50',review:'verified',support:'Póliza'},
        '2022-02':{rent_paid:'3000',cost_insurance:'450',review:'verified',support:'Póliza'},
        '2022-03':{rent_paid:'1000',cost_insurance:'900',review:'pending',support:'Pendiente'},
        '2022-04':{rent_paid:'1000',cost_insurance:'900',review:'verified',support:'Posterior'}};
    const c={insurance:{label:'Seguros'}};const got=historySuggestions(s,'2022-03-31',c,'oficina').insurance;
    assert.equal(got.rate,12.5);assert.equal(got.months.length,2);assert.equal(got.base,4000);assert.match(got.source,/oficina/);
    assert.deepEqual(historySuggestions(s,'',c),{});
});
test('costos: referencia no se adopta sola y adopción preserva restantes rubros',()=>{
    const ui=incomeCostTable();ui.changed=()=>{};
    const row={amount:'100',basis:'monthly',reference_percent:'8',reference_basis:'effective',reference_source:'Cotización oficinas ciudad y fecha'};
    assert.equal(ui.costSuggestion(row).rate,8);assert.equal(row.amount,'100');ui.applyCostSuggestion(row);
    assert.equal(row.amount,'8');assert.equal(row.source,row.reference_source);assert.equal(row.basis,'effective');
});
