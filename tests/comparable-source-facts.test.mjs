import test from 'node:test';
import assert from 'node:assert/strict';
import { publishedDetails } from '../resources/js/comparable-published-details.js';
import { sourceFacts, comparisonRows } from '../resources/js/comparable-source-facts.js';
import { sourceUpdate } from '../resources/js/comparable-source-update.js';
import { duplicateEvidence } from '../resources/js/comparable-duplicates.js';
test('collects unknown portal attributes and full description without statistical grades', () => {
    const row=publishedDetails('Baños: 2\nAscensor: Sí\nVista: Exterior paisajística\nJacuzzi privado: Sí\n'+ 'Descripción extensa '.repeat(150));
    assert.equal(row.bathrooms,'2'); assert.equal(row.elevator,'Sí');
    assert.equal(sourceFacts(row.published_attributes)['Jacuzzi privado'],'Sí');
    assert.ok(row.published_text.length>1600); assert.equal(row.latitude,undefined);
});
test('later source reading supplements attributes and records contradictory bathrooms without replacing them', () => {
    const before=publishedDetails('Baños: 2\nJacuzzi privado: No');
    const changes=sourceUpdate(before,publishedDetails('Baños: 3\nJacuzzi privado: Sí\nRampa de acceso: Sí'));
    assert.equal(changes.bathrooms,undefined);
    assert.equal(sourceFacts(changes.published_attributes)['Jacuzzi privado'],'No');
    assert.equal(sourceFacts(changes.published_attributes)['Rampa de acceso'],'Sí');
    assert.match(changes.source_updates,/Jacuzzi privado/); assert.equal(changes.intake_state,'review');
});
test('source matrix uses subject only as reference, flags differences and retains missing values', () => {
    const rows=comparisonRows({rows:[{bathrooms:'2',elevator:'Sí'},{bathrooms:'3',elevator:'Sí'}]},
        {catalog:{bathrooms:{label:'Baños',sample:'bathrooms'},elevator:{label:'Ascensor',sample:'elevator'}},subjects:{bathrooms:'4'}});
    assert.equal(rows[3].subject,'4'); assert.equal(rows[3].state,'different');
    assert.equal(rows[4].state,'same'); assert.equal(rows[0].state,'pending');
});
test('different portal prices still flag matching contact and area without confirming identity', () => {
    const a={source_url:'https://example.com/a',operation:'Venta',property_type:'Oficina',area_m2:'80',price_amount:'100',contact_phone:'3001234567'};
    const b={...a,source_url:'https://example.org/b',price_amount:'110'};
    assert.equal(duplicateEvidence(a,b).exact,false);
    assert.equal(duplicateEvidence(a,{...b,property_type:'Apartamento'}),null);
});
