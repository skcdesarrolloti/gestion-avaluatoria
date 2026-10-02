import test from 'node:test';
import assert from 'node:assert/strict';
import { compositionVisible, locationPoints } from '../resources/js/comparable-location.js';
test('PH y NPH exponen sus áreas sin deducir régimen ni confundir condominio', () => {
    assert.equal(compositionVisible('land_m2', {ph_regime:'no'}), true);
    assert.equal(compositionVisible('private_built_m2', {ph_regime:'no'}), false);
    assert.equal(compositionVisible('private_built_m2', {ph_regime:'si'}), true);
    assert.equal(compositionVisible('land_m2', {ph_regime:'por_verificar'}), false);
    assert.equal(compositionVisible('land_m2', {ph_regime:'si', ph_special:'condominio'}), true);
    assert.equal(compositionVisible('private_built_m2', {ph_regime:'si', ph_special:'condominio'}), false);
    assert.equal(compositionVisible('ph_parking_nature', {ph_regime:'no'}), false);
    assert.equal(compositionVisible('ph_deposit_count', {ph_regime:'si'}), true);
    assert.equal(compositionVisible('ph_components_source', {ph_regime:'por_verificar'}), false);
});
test('mapa conserva número de fila, admite cero y excluye vacíos, inválidos o descartados', () => {
    const p = locationPoints([{latitude:'',longitude:''},{latitude:'0',longitude:'0'},
        {latitude:'10,4',longitude:'-75,5'}, {latitude:'91',longitude:'1'},
        {latitude:'10',longitude:'-75',status:'descartada'}]);
    assert.deepEqual(p.map(v=>v.n),[2,3]);
    assert.equal(p[1].lat,10.4);
    assert.ok(p.every(v=>Number.isFinite(v.x) && Number.isFinite(v.y)));
});
test('mapa incluye el sujeto sin convertirlo en muestra ni perder puntos coincidentes', () => {
    const p = locationPoints([{latitude:'10',longitude:'-75'},{latitude:'10',longitude:'-75'}], {latitude:'10',longitude:'-75'});
    assert.deepEqual(p.map(v=>v.n),[1,2,'S']);
    assert.ok(p.every(v=>v.x===300 && v.y===180));
});
