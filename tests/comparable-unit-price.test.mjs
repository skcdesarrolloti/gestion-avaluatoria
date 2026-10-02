import {test} from 'node:test';
import assert from 'node:assert/strict';
import {unitPrice} from '../resources/js/comparable-unit-price.js';
import {unitFormula} from '../resources/js/xlsx-unit-formula.js';
import {columnName} from '../resources/js/xlsx-table.js';
const row={ph_regime:'si',private_built_m2:'100',private_free_m2:'20',area_m2:'124',areas_source:'Cuadro verificado',
    operation:'Venta',price_unit:'precio_total',price_amount:'500000000',negotiation_discount:'25000000'};
test('PH cociente de área privada construida no suma anexos ni confunde depuración con negociación',()=>{
    assert.equal(unitPrice(row).negotiated_per_m2,'4750000.00');
    assert.equal(unitPrice({...row,private_built_m2:'12.345'}).unit_area_m2,'12.3450');
    assert.match(unitPrice(row).unit_price_status,/no es valor depurado/);
    for (const patch of [{areas_source:''},{ph_regime:'por_verificar'},{private_built_m2:'0'},{negotiation_discount:''}])
        assert.equal(unitPrice({...row,...patch}).negotiated_per_m2,'');
});
test('Excel unitarios referencian área editable y resultado negociado, sin congelar cociente al editar',()=>{
    const columns=['ph_regime','ph_special','areas_source','private_built_m2','area_m2','area_basis','unit_area_m2','price_unit','operation','price_amount','negotiated_amount'].map(key=>({key}));
    const area=unitFormula(columns,2,'unit_area_m2',columnName);
    assert.match(area,/ISNUMBER\(D2\)/); assert.match(area,/C2=""/);
    const result=unitFormula(columns,2,'negotiated_per_m2',columnName);
    assert.match(result,/K2\/G2/); assert.match(result,/H2="valor_m2",K2/);
    assert.equal(unitFormula([],2,'unit_area_m2',columnName),'""');
});
test('Unitarios, renta y NPH mantienen dimensión y base sin doble división',()=>{
    assert.equal(unitPrice({...row,price_unit:'valor_m2',price_amount:'5000000',negotiation_discount:'250000'}).negotiated_per_m2,'4750000.00');
    assert.equal(unitPrice({...row,operation:'Arriendo',price_unit:'canon_mensual'}).unit_price_currency,'COP/m²/mes');
    assert.equal(unitPrice({...row,price_unit:'canon_mensual'}).offer_per_m2,'');
    assert.equal(unitPrice({...row,ph_regime:'no',area_basis:'Área construida'}).unit_area_m2,'124.0000');
    assert.equal(unitPrice({...row,ph_regime:'no'}).unit_area_m2,'');
});
