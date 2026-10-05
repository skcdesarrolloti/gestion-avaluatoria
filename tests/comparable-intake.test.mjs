import test from 'node:test';
import assert from 'node:assert/strict';
import { intakeGroups, comparableIntake } from '../resources/js/comparable-intake.js';
import { publishedDetails } from '../resources/js/comparable-published-details.js';
import { sourceUpdate } from '../resources/js/comparable-source-update.js';
test('portal review shows one advertisement and confirmation is separate from analysis selection', () => {
    const entries=[{used:true,index:0,data:{id:'a',source_name:'FincaRaíz',capture_confirmation:''},controls:[{name:'comparables[0][capture_confirmation]',value:''}]},
        {used:true,index:1,data:{id:'b',source_name:'Ciencuadras',capture_confirmation:'confirmed'},controls:[]}];
    const state=comparableIntake(()=>entries,()=>({dispatchEvent(){}}));
    state.rebuildIntake();
    assert.equal(state.intakeCards.length,1); assert.equal(state.intakeCards[0].rows[0].id,'a');
    state.intakePortal='Ciencuadras'; state.rebuildIntake(); assert.equal(state.intakeCards[0].rows[0].id,'b');
    state.intakeView='confirmed'; state.rebuildIntake(); assert.equal(state.intakeCards[0].rows[0].id,'b');
    state.intakeConfirm({rows:[{index:0}]},'confirmed'); assert.equal(entries[0].controls[0].value,'confirmed');
    assert.equal(entries[0].data.intake_state,undefined);
});
test('later reading of the same URL fills blanks and flags changed price without overwriting it', () => {
    const changes=sourceUpdate({price_amount:'$ 100.000.000',area_m2:'80',intake_state:'selected'},
        {price_amount:'110000000',area_m2:'80',parking_spaces:'2',location_verification:'exact'});
    assert.equal(changes.price_amount,undefined); assert.equal(changes.parking_spaces,'2');
    assert.match(changes.source_updates,/110000000/); assert.equal(changes.intake_state,'review');
    assert.equal(changes.location_verification,undefined);
});
test('confirmed property preserves contradictory source prices without creating independent properties', () => {
    const rows=[{id:'a',property_group:'a',price_amount:'100',area_m2:'80',intake_state:'selected'},
        {id:'b',property_group:'a',price_amount:'110',area_m2:'82',intake_state:'selected'}];
    const groups=intakeGroups(rows);
    assert.equal(groups.length,1); assert.equal(groups[0].rows.length,2);
    assert.deepEqual(groups[0].conflicts,['price_amount','area_m2']);
    assert.deepEqual(groups[0].rows.map(r=>r.price_amount),['100','110']);
});
test('building coincidence proposes review without merging distinct offices', () => {
    const groups=intakeGroups([{id:'a',project_name:'Torre Uno',neighborhood:'Centro',property_type:'Oficina',operation:'Venta'},
        {id:'b',project_name:'Torre Uno',neighborhood:'Centro',property_type:'Oficina',operation:'Venta'}]);
    assert.equal(groups.length,2); assert.equal(groups[0].candidates[0].key,'b');
});
test('previously used observations show their existing selection while new imports remain for review', () => {
    const groups=intakeGroups([{id:'a',status:'usada'},{id:'b',status:'por_verificar'}]);
    assert.equal(groups[0].state,'selected_pending'); assert.equal(groups[1].state,'review');
});
test('captures explicit private area and annexes but never invents rights or exact coordinates', () => {
    const row=publishedDetails('Área privada construida: 85,5 m². 2 parqueaderos y 1 depósito. Administración: $ 600.000. Celular: 3001234567');
    assert.equal(row.private_built_m2,'85,5'); assert.equal(row.parking_spaces,'2');
    assert.equal(row.ph_deposit_count,'1'); assert.equal(row.admin_fee,'600.000');
    assert.equal(row.contact_phone,'3001234567'); assert.equal(row.location_verification,undefined);
    assert.equal(row.ph_parking_nature,undefined); assert.equal(row.ph_parking_in_price,undefined);
    assert.equal(publishedDetails('Parqueadero y depósito disponibles').ph_deposit_count,undefined);
    assert.equal(publishedDetails('Área privada: 85 m²').private_built_m2,undefined);
});
