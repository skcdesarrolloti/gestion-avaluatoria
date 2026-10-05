import test from 'node:test';
import assert from 'node:assert/strict';
import { captureSelectedDetails } from '../resources/js/capture-selected-details.js';

const cards=()=>[1,2,3].map(id=>({row:{source_url:'https://source.test/oficina-'+id},tone:id===1?'registered':'new'}));
test('two steps save intake before reading only inserted properties and checkpoint each detail',async()=>{
 const events=[],items=cards(); let fills=0;
 const io={fill(_form,rows,_query,_unused,options){fills++;events.push('fill'+fills);if(fills===1)options.onInserted(rows[1]);return {count:1,duplicates:1,overflow:1};},
  async save(){events.push('saved');return true;},
  async read(picked,_endpoint,_progress,complete){events.push('read');assert.deepEqual(picked.map(i=>i.row.source_url),[items[1].row.source_url]);await complete(picked[0]);return {completed:1,failed:0};}};
 const message=await captureSelectedDetails({},items,'','/leer',()=>{},false,io);
 assert.deepEqual(events,['fill1','saved','read','fill2','saved']);assert.match(message,/1 fichas leídas/);
});
test('unacknowledged intake never opens a property link',async()=>{
 let reads=0;
 const io={fill(_form,rows,_query,_unused,options){options.onInserted(rows[0]);return {count:1,duplicates:0,overflow:0};},save:async()=>false,read:async()=>{reads++;}};
 assert.match(await captureSelectedDetails({},cards(),'','/leer',()=>{},false,io),/No se inició/);
 assert.equal(reads,0);
});
test('a failed detail checkpoint stops investigation while keeping the acknowledged intake',async()=>{
 let saves=0;
 const io={fill(_form,rows,_query,_unused,options){options.onInserted?.(rows[0]);return {count:1,duplicates:0,overflow:0};},save:async()=>++saves===1,
 read:async(items,_endpoint,_progress,complete)=>{await complete(items[0]);throw new Error('Must not continue');}};
 assert.match(await captureSelectedDetails({},cards(),'','/leer',()=>{},false,io),/avisos iniciales están guardados/);assert.equal(saves,2);
});
