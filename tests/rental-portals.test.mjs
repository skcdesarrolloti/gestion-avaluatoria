import test from 'node:test';
import assert from 'node:assert/strict';
import { parseHTML } from 'linkedom';
import { copiedProperatiRows } from '../resources/js/properati-paste.js';
import { copiedCiencuadrasRows } from '../resources/js/ciencuadras-paste.js';
import { copiedMercadolibreRows } from '../resources/js/mercadolibre-paste.js';
import { readSourceRows } from '../resources/js/source-results-paste.js';
import { portalResultsPaste } from '../resources/js/portal-results-paste.js';

test('existing office readers select rental or sale from the same mixed page without substituting amounts', () => {
    const previous=globalThis.document; globalThis.document=parseHTML('<html/>').document;
    try {
        const cases=[
            [copiedProperatiRows, op=>`<article><a data-test="snippet__title" href="/detalle/${op==='Venta'?'12':'34'}-abcd">Oficina en ${op} en Bocagrande</a><span data-test="snippet__location">Bocagrande, Cartagena, Bolívar</span><span data-test="snippet__price">$ ${op==='Venta'?'400.000.000':'3.000.000'}</span><span data-test="area-value">37 m²</span></article>`],
            [copiedCiencuadrasRows, op=>`<a href="/inmueble/oficina-${op==='Venta'?'12':'34'}"><article><h3>Oficina en ${op}<span>cartagena, comuna 1, boca grande</span></h3><p>${op==='Venta'?'Valor de compra: $400.000.000':'Administración incluida: $3.000.000'}</p><span>37 m2</span></article></a>`],
            [copiedMercadolibreRows, op=>`<div class="poly-card"><span class="poly-component__headline">Oficina en ${op}</span><a class="poly-component__title" href="https://inmueble.mercadolibre.com.co/MCO-${op==='Venta'?'12':'34'}-oficina_JM">Oficina</a><span class="poly-component__location">Bocagrande, Cartagena De Indias, Bolívar</span><span class="poly-price__current">$ ${op==='Venta'?'400.000.000':'3.000.000'}</span><span class="poly-attributes_list__item">37 m² cubiertos</span></div>`],
        ];
        for (const [reader,card] of cases) {
            const html=card('Venta')+card('Arriendo');
            const rents=reader(html,'Cartagena','arriendo'),sales=reader(html,'Cartagena');
            assert.equal(rents.length,1); assert.equal(sales.length,1);
            assert.equal(rents[0].operation,'Arriendo'); assert.equal(rents[0].price_amount,'3.000.000');
            assert.equal(rents[0].price_unit,'canon_mensual');
            assert.equal(sales[0].operation,'Venta'); assert.equal(sales[0].price_amount,'400.000.000');
            assert.equal(sales[0].price_unit,'precio_total');
            assert.notEqual(rents[0].source_url,sales[0].source_url);
        }
        const [dual]=copiedCiencuadrasRows('<a href="/inmueble/oficina-99"><article><h3>Oficina en arriendo o ventacartagena, comuna 1, boca grande</h3><p>Valor de compra: $400.000.000</p><p>Arriendo con administración: $3.000.000</p><span>37 m2</span></article></a>','Cartagena','arriendo');
        assert.equal(dual.price_amount,'3.000.000'); assert.match(dual.comparability_notes,/verificar el desglose/);
    } finally {globalThis.document=previous;}
});

test('Metrocuadrado keeps missing clipboard area pending rather than using a contradictory image description', () => {
    const previous=globalThis.document; globalThis.document=parseHTML('<html/>').document;
    try {
        const html='<div class="property-card__container"><a href="/inmueble/arriendo-oficina-cartagena/532-M123"><img alt="Área 8 m2"><p>$779.500</p><h2>Oficina en Arriendo, Bocagrande, Cartagena de Indias</h2><pt-main-specs></pt-main-specs></a></div>';
        const [row]=readSourceRows(html,'','https://www.metrocuadrado.com/','Metrocuadrado');
        assert.equal(row.area_m2,''); assert.equal(row.operation,'Arriendo'); assert.match(row.comparability_notes,/Área no recibida/);
        assert.equal(readSourceRows(html.replace('Arriendo','Venta'),'','https://www.metrocuadrado.com/','Metrocuadrado').length,0);
    } finally {globalThis.document=previous;}
});

test('paste preview rejects other operations in either study and never relabels or adds them to the collection', async () => {
    const previous=globalThis.document;
    try {
        for (const operation of ['arriendo','venta']) {
            const form={querySelector:()=>({value:(operation==='arriendo'?'renta:':'mercado:')+operation}),querySelectorAll:()=>[]};
            globalThis.document={getElementById:()=>form};
            const ui=portalResultsPaste({label:'Prueba',validUrl:()=>true,readRows:()=>[
                {source_url:'https://example.test/rent-1',operation:'Arriendo'},
                {source_url:'https://example.test/sale-1',operation:'Venta'},
                {source_url:'https://example.test/unknown-1',operation:''},
            ]});
            ui.$el={dataset:{operation}}; ui.init();
            await ui.paste({preventDefault(){},clipboardData:{getData:type=>type==='text/html'?'<p>Copiado</p>':''}});
            assert.equal(ui.results.length,1); assert.equal(ui.results[0].row.operation.toLowerCase(),operation);
            assert.match(ui.message,/Se omitieron 2/); ui.destroy();
        }
    } finally {globalThis.document=previous;}
});
