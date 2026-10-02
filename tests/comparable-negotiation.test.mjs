import {test} from 'node:test';
import assert from 'node:assert/strict';
import { negotiation, capturePending, portalCounts } from '../resources/js/comparable-negotiation.js';
import {tableWorkbook, columnName} from '../resources/js/xlsx-table.js';
test('negociación conserva desconocido, cero explícito y rechaza importes fuera de rango', () => {
    assert.equal(negotiation({price_amount:'$ 500.000.000',negotiation_discount:'25000000'}).value,'475000000.00');
    assert.equal(negotiation({price_amount:'500'}).value,'');
    assert.equal(negotiation({price_amount:'500',negotiation_discount:'0'}).value,'500.00');
    assert.ok(negotiation({price_amount:'500',negotiation_discount:'501'}).error);
    assert.ok(negotiation({price_amount:'500',negotiation_discount:'-1'}).error);
});
test('PH exige áreas y derechos de componentes presentes, no de ausencias confirmadas', () => {
    const row = {ph_regime:'si',ph_parking_presence:'si',ph_deposit_presence:'no'};
    const pending = capturePending(row,true).map(([key]) => key);
    assert.ok(pending.includes('ph_parking_area_m2'));
    assert.ok(!pending.includes('ph_deposit_nature'));
    assert.ok(!capturePending({...row,ph_special:'condominio'},true).some(([key]) => key === 'private_built_m2'));
});
test('conteos por fuente normalizan acentos, URL y fuentes desconocidas', () => {
    const counts = portalCounts([{source_name:'FincaRaíz'},{source_url:'https://www.metrocuadrado.com/1'}, {source_name:'Vendedor'}]);
    assert.equal(counts.find(item => item.label === 'FincaRaíz').count,1);
    assert.equal(counts.find(item => item.label === 'Metrocuadrado').count,1);
    assert.equal(counts.find(item => item.label === 'Otras fuentes').count,1);
});
test('Excel es ZIP OOXML con tabla, filtros, panes y fórmulas seguras separadas del texto fuente', () => {
    const bytes = tableWorkbook([{key:'source_name',label:'Fuente'}, {key:'price_amount',label:'Oferta',numeric:true},
        {key:'negotiation_discount',label:'Descuento',numeric:true},{key:'negotiated_amount',label:'Negociado',numeric:true}],
        [{source_name:{value:'=HYPERLINK("https://externo")'}, price_amount:{value:500},negotiation_discount:{value:25},negotiated_amount:{value:475}}]);
    assert.equal(new DataView(bytes.buffer).getUint32(0,true),0x04034b50);
    const contents = new TextDecoder().decode(bytes);
    assert.match(contents,/t="inlineStr"><is><t xml:space="preserve">=HYPERLINK/);
    assert.match(contents,/<autoFilter ref="A1:D2"/);
    assert.match(contents,/state="frozen"/);
    assert.match(contents,/<f>IF\(AND\(ISNUMBER\(B2\)/);
    assert.equal(columnName(26),'AA'); assert.equal(columnName(78),'CA');
});
