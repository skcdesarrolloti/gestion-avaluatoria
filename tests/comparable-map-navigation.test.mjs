import test from 'node:test';
import assert from 'node:assert/strict';
import { comparableMapNavigation } from '../resources/js/comparable-map-navigation.js';

const state = save => Object.assign(comparableMapNavigation(save), {
    page:1, pages:3, photoOpen:true, photoBusy:false, photoRetry:false,
    render() { this.mapIndex = this.page - 1; },
    async openPhotos(index) { this.opened = index; this.photoOpen = true; },
});
test('next sample waits for confirmed save and opens only the new sample photos', async () => {
    let resolve; const s = state(() => new Promise(r => resolve = r));
    const moving = s.moveMap(1, true);
    assert.equal(s.page, 1); assert.equal(s.mapBusy, true);
    await s.moveMap(1, true); assert.equal(s.page, 1);
    resolve(true); await moving;
    assert.equal(s.page, 2); assert.equal(s.opened, 1); assert.equal(s.mapBusy, false);
});
test('failed save, upload in progress or pending photo cannot advance or lose current panel', async () => {
    const s = state(async () => false);
    await s.moveMap(1, true);
    assert.equal(s.page, 1); assert.equal(s.photoOpen, true); assert.match(s.mapMessage, /No se pudo/);
    for (const key of ['photoBusy', 'photoRetry']) {
        const blocked = state(async () => { throw new Error('must not save'); }); blocked[key] = true;
        await blocked.moveMap(1, true); assert.equal(blocked.page, 1); assert.equal(blocked.photoOpen, true);
    }
});
test('navigation respects boundaries and ordinary next closes previous photo panel', async () => {
    const s = state(async () => true);
    await s.moveMap(-1, true); assert.equal(s.page, 1);
    await s.moveMap(1); assert.equal(s.page, 2); assert.equal(s.photoOpen, false);
    s.page = 3; await s.moveMap(1, true); assert.equal(s.page, 3);
});
