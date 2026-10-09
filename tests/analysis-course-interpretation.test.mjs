import test from 'node:test';
import assert from 'node:assert/strict';
import {courseSummary} from '../resources/js/analysis-course-math.js';
import {courseInterpretations} from '../resources/js/analysis-course-interpretation.js';
import {courseReport} from '../resources/js/analysis-course-report.js';
const result=values=>{const valid=values.map((y,i)=>({id:String(i),label:'Muestra '+(i+1),y,reasons:[]}));return {valid,rows:[...valid],pending:[],summary:courseSummary(valid),basis:'offer',simulated:true,histogram:'',at:'now'};};
test('interpretations distinguish dispersion references and preserve uncertainty',()=>{
    const low=result([100,101,99,102,98]),before=JSON.stringify(low),texts=courseInterpretations(low);
    assert.equal(texts.length,7);assert.match(texts[3].reading,/dentro de ambos/);assert.match(texts[5].meaning,/No se fijó un margen objetivo/);
    assert.match(texts[6].reading,/simulados/);assert.equal(JSON.stringify(low),before);
    const middle=result([88,100,112,100,100]);assert.match(courseInterpretations(middle)[3].reading,/supera el referente urbano/);
    const high=result([100,100,100,100,100,1000]),flags=courseInterpretations(high);
    assert.match(flags[3].reading,/supera ambos/);assert.match(flags[4].reading,/Muestra 6/);assert.match(flags[4].meaning,/no errores demostrados/);
});
test('pending, bootstrap and non-estimable shape remain explicit in UI and report',()=>{
    const r=result([100,100,100,100]);r.rows.push({id:'pending',label:'Muestra 5',reasons:['Descuento pendiente']});r.pending=[r.rows[4]];
    r.bootstrap={mean:[99,101],median:[100,100],cv:[0,0],repetitions:10000,seed:2026};
    const texts=courseInterpretations(r);assert.match(texts[0].reading,/4 de 5/);assert.match(texts[4].meaning,/no es estimable/);assert.match(texts[5].next,/99 a 101/);
    const html=courseReport(r);assert.equal((html.match(/<strong>Resultado observado:/g)||[]).length,7);assert.equal((html.match(/<strong>Qué hacer ahora:/g)||[]).length,7);
});
test('execution memory preserves source limits as escaped text without claiming appraisal conformity',()=>{
    const r=result([8,10,12]),before=JSON.stringify(r),support='Art. 21: adopción de la media. IVS preliminar: cotejo pendiente. <script>alterar()</script>';
    const html=courseReport(r,{},'',support);
    assert.ok(html.includes('Fuentes y alcance de esta ejecución'));
    assert.ok(html.includes('IVS preliminar: cotejo pendiente. &lt;script&gt;'));
    assert.ok(!html.includes('<script>'));assert.ok(html.includes('según ámbito aplicable'));
    assert.ok(html.includes('Conclusión del analista pendiente'));assert.equal(JSON.stringify(r),before);
});
