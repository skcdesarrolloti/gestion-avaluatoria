import test from 'node:test';
import assert from 'node:assert/strict';
import { incomePreparation } from '../resources/js/income-preparation.js';

test('Renta consulta ocupación sin copiarla y conserva diferencias entre fuentes',()=>{
    const state=incomePreparation(); state.values={occupancy:'',scope:'part'};state.sourceOccupancy='rented_unspecified';
    assert.equal(state.occupancy,'rented_unspecified');assert.equal(state.values.occupancy,'');
    state.values.occupancy='partial';assert.equal(state.occupancyConflict,false);
    state.values.occupancy='owner';assert.equal(state.occupancyConflict,true);assert.equal(state.hasLease(),false);
    assert.equal(state.showQuestion('support'),false);
    state.values.support='contract';assert.equal(state.showQuestion('support'),true);
    state.values.occupancy='pending';assert.equal(state.hasLease(),true);
});

test('Renta abre el tema del error sin descartar respuestas ocultas',()=>{
    const state=incomePreparation();state.values={lease_term:'Cláusula vigente',occupancy:'vacant'};
    state.fieldGroups={lease_term:'lease'};state.validation({'income_concept[lease_term]':'Revisa la extensión'});
    assert.equal(state.section,'lease');assert.equal(state.values.lease_term,'Cláusula vigente');
    assert.equal(state.label('cost_ph',{owner:'Propietario'}),'Por completar o verificar');
});
