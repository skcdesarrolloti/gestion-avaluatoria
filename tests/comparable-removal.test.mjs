import test from 'node:test';
import assert from 'node:assert/strict';
import { comparableRemoval } from '../resources/js/comparable-removal.js';
import { comparableIntake } from '../resources/js/comparable-intake.js';
import { hasComparableData } from '../resources/js/comparable-review.js';
import { portalCounts } from '../resources/js/comparable-negotiation.js';

function setup(flush = async () => true) {
    const entries = [0, 1].map(index => ({ index, used: true, opened: true, tr: { hidden: index === 1 },
        controls: Object.entries({ id: String(index + 1).repeat(32), source_url: `https://example.com/${index}`, operation: 'Venta', ph_regime: 'si' })
            .map(([key, value]) => ({ name: `comparables[${index}][${key}]`, value })) }));
    const component = comparableRemoval(flush);
    const events = [];
    component.initRemoval({ dispatchEvent: event => events.push(event.type) }, entries);
    return { component, entries, events };
}
test('removal waits for confirmation, clears only selected rows, rotates identity and restores original IDs', async () => {
    const { component, entries, events } = setup();
    component.selectRemovalPage(); component.requestRemoval();
    assert.equal(entries[0].controls[1].value, 'https://example.com/0');
    await component.confirmRemoval();
    assert.equal(entries[0].controls[1].value, '');
    assert.notEqual(entries[0].controls[0].value, '1'.repeat(32));
    assert.equal(entries[1].controls[1].value, 'https://example.com/1');
    assert.equal(component.undoCount, 1);
    assert.ok(events.includes('comparable-matrix-changed'));
    await component.undoRemoval();
    assert.equal(entries[0].controls[0].value, '1'.repeat(32));
    assert.equal(entries[0].controls[1].value, 'https://example.com/0');
});
test('reset includes hidden rows and never claims success when persistence fails', async () => {
    let calls = 0;
    const { component, entries } = setup(async () => ++calls === 1);
    component.requestRemoval(true); await component.confirmRemoval();
    assert.ok(entries.every(entry => entry.controls[1].value === ''));
    assert.match(component.removalMessage, /SIN confirmar guardado/);
    assert.equal(component.undoCount, 2);
});
test('conflict before deletion preserves every row', async () => {
    const { component, entries, events } = setup(async () => false);
    component.requestRemoval(true); await component.confirmRemoval();
    assert.equal(entries[0].controls[1].value, 'https://example.com/0');
    assert.equal(entries[1].controls[1].value, 'https://example.com/1');
    assert.equal(events.length, 0);
});

test('starting over clears sources, attributes and consolidation; all portal counts are zero',async()=>{
 const {component,entries}=setup();
 for (const entry of entries) for (const [key,value] of Object.entries({source_name:'FincaRaíz',published_attributes:'{"Baños":"2"}',
 property_group:'a'.repeat(32),research_primary:'si',capture_confirmation:'confirmed'})) entry.controls.push({name:`comparables[${entry.index}][${key}]`,value});
 component.requestRemoval(true);await component.confirmRemoval();
 const read=()=>entries.map(e=>{
 const data=Object.fromEntries(e.controls.map(c=>[c.name.match(/\[([^\]]+)\]$/)[1],c.value]));
 return {...e,data,used:hasComparableData(data)};
 });
 const intake=comparableIntake(read,()=>({dataset:{}}));intake.rebuildIntake();
 assert.equal(intake.intakeCount,0);assert.equal(intake.intakeConfirmedCount,0);assert.equal(intake.consolidationDuplicates,0);
 assert.equal(intake.intakePortalCount('FincaRaíz'),0);assert.ok(portalCounts(read().filter(e=>e.used).map(e=>e.data)).every(p=>p.count===0));
 assert.ok(read().every(e=>e.data.published_attributes==='' && e.data.property_group==='' && e.data.capture_confirmation===''));
 await component.undoRemoval();assert.equal(read().filter(e=>e.used).length,2);
});
