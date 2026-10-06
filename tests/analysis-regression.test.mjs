import test from 'node:test';
import assert from 'node:assert/strict';
import {linearRegression} from '../resources/js/analysis-regression-math.js';
import {coordinate,distanceKm,mapGeometry,mapSvg} from '../resources/js/analysis-location-map.js';
import {marketAnalysisTable} from '../resources/js/market-analysis-table.js';
test('QR recovers three known coefficients and detects collinearity',()=>{
    const x=Array.from({length:40},(_,i)=>[40+i,i%3,i%7]);const y=x.map(r=>100+2*r[0]+3*r[1]-4*r[2]);
    const fit=linearRegression(x,y);[100,2,3,-4].forEach((v,i)=>assert.ok(Math.abs(fit.coefficients[i]-v)<1e-8));
    assert.ok(Math.abs(fit.r2-1)<1e-12);assert.ok(fit.vif.every(v=>v>=1-1e-10));
    assert.throws(()=>linearRegression(x.map(r=>[...r,r[0]*2]),y),/rango completo/);
});
test('regression requires confirmed codes, keeps missing discounts pending and invalidates changed inputs',async()=>{
    const keys=['published:area privada','bathrooms','published:antiguedad'];
    const rows=Array.from({length:34},(_,i)=>({id:String(i),property_type:'Oficina',ph_regime:'si',price_amount:String(400000000+i*5000000),area_m2:String(40+i),bathrooms:String(1+i%3),
        published_attributes:JSON.stringify({'Área Privada':String(40+i)+' m2','Antigüedad':i%2?'9 a 15 años':'1 a 8 años'}),
        analysis_factor_selection:JSON.stringify({selected:keys,applied:keys,threshold:50,scope:'subject',applied_scope:'subject',regime_applied:true,view:'result'})}));
    const state=marketAnalysisTable(rows,'si','oficina');state.analysisModule='regression';
    await state.regressionRun();assert.match(state.regressionError,/Confirma/);
    state.regressionCategories().forEach((c,i)=>state.regressionCodes[c.key]=String(i+1));state.regressionConfirmed=true;
    state.regressionBasis='adjusted';await state.regressionRun();assert.match(state.regressionError,/Disponibles: 0/);
    state.regressionBasis='offer';await state.regressionRun();assert.equal(state.regressionResult.n,34);assert.equal(state.regressionCurrent(),true);
    const reloaded=marketAnalysisTable(rows.map((r,i)=>({...r,...(i===0?{analysis_factor_selection:state.analysisSelection()}: {})})),'si','oficina');
    assert.deepEqual(reloaded.regressionCodes,state.regressionCodes);state.analysisRows[0].price_amount='800000000';assert.equal(state.regressionCurrent(),false);
});
test('geographic comparison preserves ratios, distances and escaped labels',()=>{
    assert.equal(coordinate({latitude:'',longitude:0}),null);assert.equal(coordinate({latitude:95,longitude:0}),null);
    assert.ok(Math.abs(distanceKm([0,0],[0,1])-111.195)<0.01);
    const points=mapGeometry([{latitude:0,longitude:1},{latitude:0,longitude:2}],{latitude:0,longitude:0});
    assert.equal(points[2].n,'S');assert.ok(Math.abs((points[1].x-points[2].x)/(points[0].x-points[2].x)-2)<1e-10);
    points[0].row.location_verification='<script>';assert.ok(!mapSvg(points).includes('<script>'));
});
