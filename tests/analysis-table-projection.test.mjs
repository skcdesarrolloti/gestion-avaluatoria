import test from 'node:test';
import assert from 'node:assert/strict';
import {tableProjection} from '../resources/js/analysis-table-projection.js';
test('projections reuse unchanged facts and invalidate data, support and membership',()=>{
 const rows=[{id:'a',area_m2:'40',published_attributes:'{"Estado":"Nuevo"}'}],project=tableProjection(true);
 const first=project(rows);assert.equal(project(rows),first);
 rows[0].area_m2='60';const next=project(rows);assert.notEqual(next,first);assert.equal(next.rows[0].values.area_m2,'60');
 rows[0].analysis_manual_factors=JSON.stringify({'published:antiguedad':{label:'Antigüedad',value:'12',source:'EJEMPLO SIMULADO: no verificado'}});
 assert.equal(project(rows).rows[0].values['published:antiguedad'],'12');assert.equal(project([]).rows.length,0);
});
