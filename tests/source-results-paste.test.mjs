import test from 'node:test';
import assert from 'node:assert/strict';
import { parseHTML } from 'linkedom';
import { readSourceRows } from '../resources/js/source-results-paste.js';
import { intakeTable } from '../resources/js/comparable-intake-table.js';
import { intakeGroups } from '../resources/js/comparable-intake.js';
import { previewFacts } from '../resources/js/comparable-preview-facts.js';

test('agency reader separates cards, keeps source and unknown facts without executing HTML', () => {
    const previous=globalThis.document;
    globalThis.document=parseHTML('<html><body></body></html>').document;
    try {
        const html='<section><article><a href="/oficina-venta-123">Oficina en venta</a><p>$ 100.000.000 · 80 m² · 2 baños</p><p>Vista: Interior</p><p>Jacuzzi: Privado</p></article><article><a href="/oficina-venta-456">Oficina en venta</a><p>$ 200.000.000 · 90 m² · 3 baños</p></article><script>window.bad=true</script><a href="https://evil.example/oficina-789">$ 600.000.000 99 m²</a></section>';
        const rows=readSourceRows(html,'','https://agencia.example/','Agencia',true);
        assert.equal(rows.length,2); assert.equal(rows[0].source_type,'inmobiliaria');
        assert.equal(rows[0].source_name,'Agencia'); assert.equal(rows[0].bathrooms,'2');
        assert.match(rows[0].published_text,/Jacuzzi/); assert.equal(JSON.parse(rows[0].published_attributes).Jacuzzi,'Privado'); assert.equal(rows[0].view_quality,'Interior'); assert.doesNotMatch(rows[0].published_text,/200\.000/);
        assert.equal(rows[1].area_m2,'90');
        assert.ok(previewFacts(rows[0]).some(f => f.label==='Baños' && f.value==='2'));
    } finally { globalThis.document=previous; }
});
test('ambiguous mixed cards and foreign URLs never become a combined sample', () => {
    const previous=globalThis.document; globalThis.document=parseHTML('<html/>').document;
    try {
        assert.equal(readSourceRows('<div><a href="/oficina-1">Uno</a><a href="/oficina-2">Dos</a><p>$100.000.000 80 m²</p></div>','','https://agencia.example/','Agencia',true).length,0);
        assert.equal(readSourceRows('', 'https://otra.example/oficina-1\n$100.000.000 80 m²','https://agencia.example/','Agencia',true).length,0);
        const [row]=readSourceRows('', 'https://agencia.example/oficina-1\nOficina en venta\n$100.000.000 80 m²\nBaños: 0','https://agencia.example/','Agencia',true);
        assert.equal(row.bathrooms,'0');
        assert.equal(readSourceRows('','file://agencia.example/oficina-1\n$100.000.000 80 m²','https://agencia.example/','Agencia',true).length,0); assert.equal(row.capture_confirmation,undefined);
    } finally { globalThis.document=previous; }
});
test('property table retains contradictory sources, catalog gaps and arbitrary published attributes', () => {
    const data=[{id:'a',property_group:'a',source_name:'Portal',price_amount:'100',bathrooms:'2',capture_confirmation:'confirmed',published_attributes:'{"Jacuzzi":"Privado"}'},
        {id:'b',property_group:'a',source_name:'Agencia',price_amount:'110',bathrooms:'3'}, {id:'c',source_name:'Portal'}];
    const result=intakeTable(intakeGroups(data),{catalog:{baths:{label:'Baños',sample:'bathrooms'},view:{label:'Vista',sample:'view_quality'}}});
    assert.equal(result.rows.length,2); assert.equal(result.rows[0].values.price_amount.state,'different');
    assert.match(result.rows[0].values.price_amount.value,/Portal: 100 \/ Agencia: 110/);
    assert.equal(result.rows[0].values.view_quality.value,'Pendiente'); assert.equal(result.rows[0].confirmation,'Por confirmar');
    assert.ok(result.columns.some(c => c.label==='Publicado: Jacuzzi'));
    assert.deepEqual(data.map(row => row.price_amount),['100','110',undefined]);
});

test('FincaRaiz joined badges do not make area become bathrooms and outside description reaches table', () => {
    const previous=globalThis.document; globalThis.document=parseHTML('<html/>').document;
    try {
        const html='<section><div class="listingCard"><div><a href="/oficina-en-venta-en-manga-cartagena/193978243"><h2>Oficina en venta en Manga, Cartagena</h2></a><p>$ 1.400.000.000</p><div>2 Baños104 m²</div></div><p>El inmueble cuenta con 2 baños y 4 garajes. Servicios básicos de agua y electricidad. Vista panorámica. El edificio tiene acceso para discapacitados, acceso pavimentado y ascensor.</p></div><div class="listingCard"><a href="/oficina-en-venta-en-manga-cartagena/193978244"><h2>Oficina en venta en Manga, Cartagena</h2></a><p>$ 3.353.000.000</p><div>2 Baños395 m²</div><p>4 garajes.</p></div></section>';
        const rows=readSourceRows(html,'','https://www.fincaraiz.com.co/','FincaRaiz');
        assert.equal(rows.length,2); assert.deepEqual(rows.map(r=>r.bathrooms),['2','2']);
        assert.deepEqual(rows.map(r=>r.area_m2),['104','395']); assert.equal(rows[0].parking_spaces,'4');
        assert.equal(rows[0].elevator,'Sí'); assert.equal(rows[0].property_type,'Oficina'); assert.equal(rows[0].project_name,'');
        assert.match(rows[0].listing_title,/Manga/); assert.match(rows[0].published_attributes,/Acceso para discapacitados/);
        const table=intakeTable(intakeGroups(rows.map((row,i)=>({...row,id:String(i+1)}))),{catalog:{baths:{label:'Baños',sample:'bathrooms'}}});
        assert.equal(table.rows[0].values.bathrooms.value,'FincaRaiz: 2');
        assert.ok(table.columns.some(c=>c.label==='Publicado: Servicios descritos'));
        assert.equal(rows[1].elevator,undefined);
    } finally {globalThis.document=previous;}
});
