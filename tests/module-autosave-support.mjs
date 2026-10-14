import { installModuleAutosave } from '../resources/js/module-autosave.js';
class HTMLFormElement {
    constructor() {
        this.id = 'expediente-form';
        this.dataset = { autosaveEndpoint: '/avaluos/abc/expediente/autoguardar' };
        this.version = { name: 'version', value: '7' };
        this.status = { textContent: '', dataset: {} };
        this.dossier = { value: 'Pendiente de asignar' };
        this.banner = { textContent: 'Pendiente de asignar' };
        this.cardClasses = new Set(['border-amber-200', 'bg-amber-50', 'text-amber-950']);
        this.card = { classList: {
            add: (...names) => names.forEach(name => this.cardClasses.add(name)),
            remove: (...names) => names.forEach(name => this.cardClasses.delete(name)),
            contains: name => this.cardClasses.has(name),
        } };
        this.dossierState = { textContent: 'Número de expediente' };
        this.dossierHelpClasses = new Set(['text-amber-900']);
        this.dossierHelp = { classList: {
            add: (...names) => names.forEach(name => this.dossierHelpClasses.add(name)),
            remove: (...names) => names.forEach(name => this.dossierHelpClasses.delete(name)),
            contains: name => this.dossierHelpClasses.has(name),
        } };
        this.createPanel = { hidden: false };
        this.attrs = new Set();
        this.ownerDocument = {
            querySelectorAll: selector => ({
                '[data-expediente-number-output]': [this.dossier, this.banner],
                '[data-dossier-card]': [this.card],
                '[data-dossier-state]': [this.dossierState],
                '[data-dossier-help]': [this.dossierHelp],
                '[data-create-dossier-panel]': [this.createPanel],
            }[selector] || []),
        };
    }
    matches(selector) { return selector === '[data-module-autosave]'; }
    hasAttribute(name) { return this.attrs.has(name); }
    removeAttribute(name) { this.attrs.delete(name); }
    querySelector(selector) { return selector === 'input[name="version"]' ? this.version : null; }
    querySelectorAll(selector) { return selector === '[data-autosave-status]' ? [this.status] : []; }
}

class HTMLInputElement {
    constructor(form) { this.form = form; this.type = 'text'; this.name = 'titulo'; this.value = 'Casa'; }
}

class HTMLSelectElement {
    constructor(form) { this.form = form; this.name = 'appraiser_id'; this.attrs = new Set(); }
    hasAttribute(name) { return this.attrs.has(name); }
}
class HTMLTextAreaElement {}

class CreateDossierButton {
    constructor(form) { this.form = form; }
    closest(selector) { return selector === '[data-create-dossier]' ? this : null; }
    hasAttribute() { return false; }
}

function setup() {
    const originals = {
        HTMLFormElement: globalThis.HTMLFormElement,
        HTMLInputElement: globalThis.HTMLInputElement,
        HTMLSelectElement: globalThis.HTMLSelectElement,
        HTMLTextAreaElement: globalThis.HTMLTextAreaElement,
        CSS: globalThis.CSS,
        document: globalThis.document,
        window: globalThis.window,
        FormData: globalThis.FormData,
        setTimeout: globalThis.setTimeout,
        clearTimeout: globalThis.clearTimeout,
        fetch: globalThis.fetch,
        CustomEvent: globalThis.CustomEvent,
    };
    const listeners = {};
    globalThis.HTMLFormElement = HTMLFormElement;
    globalThis.HTMLInputElement = HTMLInputElement;
    globalThis.HTMLSelectElement = HTMLSelectElement;
    globalThis.HTMLTextAreaElement = HTMLTextAreaElement;
    globalThis.CSS = { escape: value => value };
    globalThis.document = {
        querySelector: selector => selector === 'meta[name="csrf-token"]' ? { content: 'csrf-token' } : null,
        querySelectorAll: () => [],
        addEventListener: (type, handler) => { listeners[type] = handler; },
    };
    const dispatched = [];
    globalThis.window = { addEventListener: () => {}, dispatchEvent: event => dispatched.push(event) };
    globalThis.CustomEvent = class { constructor(type, params = {}) { this.type = type; this.detail = params.detail || {}; } };
    globalThis.FormData = class {
        constructor(form) {
            this.values = new Map([['_token', 'old'], ['version', form.version.value]]);
        }
        delete(key) { this.values.delete(key); }
        get(key) { return this.values.get(key); }
        set(key, value) { this.values.set(key, value); }
    };
    let timerCallback = null, timerDelay = null;
    globalThis.setTimeout = (callback, delay) => { timerCallback = callback; timerDelay = delay; return 1; };
    globalThis.clearTimeout = () => {};
    installModuleAutosave();
    return {
        listeners,
        runTimer: () => timerCallback(),
        timerDelay: () => timerDelay,
        dispatched,
        cleanup: () => Object.entries(originals).forEach(([key, value]) => { globalThis[key] = value; }),
    };
}
export { HTMLFormElement, HTMLInputElement, HTMLSelectElement, CreateDossierButton, setup };
