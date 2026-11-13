import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { execFileSync } from 'node:child_process';
import { filterIgacOptions, igacUnitSelector } from '../resources/js/igac-unit-selector.js';

const php = process.platform === 'win32' ? 'C:/xampp/php/php.exe' : 'php';
const rules = JSON.parse(execFileSync(php, ['-r',
    "require 'bootstrap.php'; echo json_encode(App\\Support\\IgacTypologyFilter::rules());"], { encoding: 'utf8' }));
const catalog = JSON.parse(readFileSync('resources/data/tipologias_igac_catalogo.json', 'utf8'))
    .filter(item => item.categoria === 'ANEXOS').map(item => ({ value: item.denominacion, label: item.denominacion,
        description: item.descripcion, specifications: item.especificaciones }));

test('garage and parking bay aliases find actual related references, with accents', () => {
    const expected = filterIgacOptions(catalog, { type: 'parqueo', rules });
    assert.ok(expected.length > 0 && expected.length < catalog.length);
    for (const query of ['garaje', 'garage', 'parqueadero', 'celda de parqueo', 'estacionamiento']) {
        assert.deepEqual(filterIgacOptions(catalog, { type: 'parqueo', query, rules }), expected);
    }
    assert.ok(expected.some(item => item.value === 'Anexos.Sótano_Sencillo'));
    assert.ok(!expected.some(item => item.value.includes('Estacion_Sistema_Transporte')));
    const deposits = filterIgacOptions(catalog, { type: 'deposito', query: 'cuarto útil', rules });
    assert.deepEqual(deposits.map(item => item.value), ['Anexos.Depósitos_1']);
});

test('search never erases saved selection or substitutes a typology on empty results', () => {
    const selected = 'Anexos.Depósitos_1';
    const options = filterIgacOptions(catalog, { type: 'parqueo', query: 'inexistente', selected, rules });
    assert.deepEqual(options.map(item => item.value), [selected]);
    assert.deepEqual(filterIgacOptions(catalog, { type: 'parqueo', query: 'inexistente', rules }), []);
    assert.equal(filterIgacOptions(catalog, { type: 'parqueo', showAll: true, rules }).length, catalog.length);
    const selector = Object.assign(igacUnitSelector({ unitKind: 'annex', constructionType: 'parqueo',
        igacCategory: 'ANEXOS', igacHint: selected }), { typologies: { ANEXOS: catalog }, igacFilterRules: rules,
        constructionIgacCategories: { parqueo: 'ANEXOS' } });
    selector.syncIgacFromConstruction();
    assert.equal(selector.igacHint, selected);
});

test('every property type can consult the complete catalog and retain a cross-category choice', () => {
    const definitions = JSON.parse(execFileSync(php, ['-r',
        "require 'bootstrap.php'; echo json_encode([App\\Support\\IgacTypologySelection::families(), (new App\\Models\\IgacTypologyRepository())->optionsByCategory()]);"], { encoding: 'utf8' }));
    const [igacFamilies, typologies] = definitions;
    const total = Object.values(typologies).reduce((count, options) => count + options.length, 0);
    for (const constructionType of [...Object.keys(igacFamilies), 'lote', 'parqueo', 'deposito', 'otro']) {
        const selector = Object.assign(igacUnitSelector({constructionType, igacCategory:'COMERCIALES',
            igacHint:'9026547_ED.Servicios_Tipo_1'}), {typologies, igacFamilies, igacFilterRules:rules});
        selector.igacShowAll = true;
        assert.equal(selector.igacOptions.length, total, constructionType);
        selector.igacShowAll = false;
        selector.igacQuery = 'sin coincidencia';
        assert.deepEqual(selector.igacOptions.map(option => option.value), ['9026547_ED.Servicios_Tipo_1']);
        selector.selectIgacCategory();
        assert.equal(selector.igacCategory, 'EDIFICIOS');
        assert.equal(selector.igacHint, '9026547_ED.Servicios_Tipo_1');
    }
});

test('changing office to apartment or lot does not carry the previous commercial category into suggestions', () => {
    const [igacFamilies,typologies] = JSON.parse(execFileSync(php, ['-r',
        "require 'bootstrap.php'; echo json_encode([App\\Support\\IgacTypologySelection::families(), (new App\\Models\\IgacTypologyRepository())->optionsByCategory()]);"], {encoding:'utf8'}));
    const previous='2026532_Comercial.Intermedio_3';
    const selector=Object.assign(igacUnitSelector({constructionType:'oficina',igacCategory:'COMERCIALES',igacHint:previous}),
        {igacFamilies,typologies,igacFilterRules:rules,constructionIgacCategories:{apartamento:'RESIDENCIALES'}});
    assert.ok(selector.igacMatches.some(item=>item.category==='COMERCIALES'));
    selector.constructionType='apartamento'; selector.igacQuery='lote'; selector.igacShowAll=true;
    selector.syncIgacFromConstruction();
    assert.equal(selector.igacQuery,''); assert.equal(selector.igacShowAll,false);
    assert.ok(selector.igacMatches.some(item=>item.category==='RESIDENCIALES'));
    assert.ok(selector.igacMatches.every(item=>['RESIDENCIALES','EDIFICIOS'].includes(item.category)));
    assert.deepEqual(selector.igacOptions.filter(item=>item.category==='COMERCIALES').map(item=>item.value),[previous]);
    assert.equal(selector.igacOutsideType,true);
    assert.match(selector.igacOptions.find(item=>item.value===previous).label,/Anterior/);
    selector.constructionType='lote'; selector.syncIgacFromConstruction();
    assert.equal(selector.igacMatches.length,0);
    assert.equal(selector.igacOptions.length,1); assert.equal(selector.igacHint,previous);
    selector.igacHint=''; selector.syncIgacFromConstruction();
    assert.deepEqual(selector.igacOptions,[]);
    selector.igacShowAll=true; assert.ok(selector.igacMatches.length>0);
    selector.constructionType='parqueadero'; selector.syncIgacFromConstruction();
    assert.ok(selector.igacMatches.length>0);
    assert.ok(selector.igacMatches.every(item=>item.category==='ANEXOS' && !item.value.includes('Depósitos')));
    selector.constructionType=''; selector.defaultConstructionType='apartamento'; selector.syncIgacFromConstruction();
    assert.ok(selector.igacMatches.some(item=>item.category==='RESIDENCIALES'));
});
