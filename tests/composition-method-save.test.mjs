import {test} from 'node:test';
import assert from 'node:assert/strict';
import {parseHTML} from 'linkedom';
import {acknowledgeCompositionMethods} from '../resources/js/composition-method-save.js';

test('acknowledgement preserves a newer in-flight selection and updates only saved baseline', () => {
    const {document} = parseHTML('<form><input name="composition_method_version" value="1"><input data-original-method="annex-1" value=""><select><option value="renta" selected>Renta</option></select></form>');
    const form = document.querySelector('form');
    acknowledgeCompositionMethods(form, {composition_method_version:2, composition_methods:{'annex-1':'costo'}});
    assert.equal(form.querySelector('[data-original-method]').value, 'costo');
    assert.equal(form.querySelector('select').value, 'renta');
    assert.equal(form.querySelector('[name="composition_method_version"]').value, '2');
});
