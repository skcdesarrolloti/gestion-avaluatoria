import test from 'node:test';
import assert from 'node:assert/strict';
import { fincaraizAreaSearch } from '../resources/js/fincaraiz-area-search.js';
const items=[{id:'1',name:'Bocagrande',search_url:'https://example.test/bocagrande'},{id:'2',name:'Chambacú',search_url:'https://example.test/chambacu'}];
function component(){const c=fincaraizAreaSearch(); c.$el={dataset:{neighborhoods:JSON.stringify(items),neighborhoodId:'1'},closest:()=>({})}; c.init(); return c;}
test('initial neighborhood is selected by stored ID, and URL comes from server mapping',()=>{const c=component(); assert.equal(c.neighborhood,'Bocagrande'); assert.equal(c.searchUrl,items[0].search_url);});
test('editing clears identity and old results until a catalog option is selected',()=>{const c=component(); c.results=[{}]; c.selected=['x']; c.neighborhood='chambacu'; c.editNeighborhood(); assert.equal(c.neighborhoodId,''); assert.equal(c.searchUrl,''); assert.equal(c.results.length,0); assert.equal(c.suggestions[0].name,'Chambacú'); c.choose(c.suggestions[0]); assert.equal(c.neighborhood,'Chambacú'); assert.equal(c.neighborhoodId,'2');});
test('typing even an exact name cannot send a free-text search',async()=>{const c=component(); c.editNeighborhood(); await c.search(); assert.match(c.message,/Selecciona un barrio/); assert.equal(c.busy,false);});
