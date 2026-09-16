const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

export function isFetchableUrl(href, currentHref = window.location.href) {
    let url;
    let current;
    try {
        url = new URL(href, currentHref);
        current = new URL(currentHref);
    } catch {
        return false;
    }
    if (!['http:', 'https:'].includes(url.protocol) || url.origin !== current.origin) return false;
    return !(url.pathname === current.pathname && url.search === current.search && url.hash);
}

export function shouldHandleLink(link, event, currentHref = window.location.href) {
    if (event.defaultPrevented || event.button !== 0) return false;
    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
    if (link.target || link.hasAttribute('download') || link.closest?.('[data-no-fetch]')) return false;
    return isFetchableUrl(link.href, currentHref);
}

function setBusy(active, text = 'Cargando...') {
    const loader = document.getElementById('app-loader');
    document.documentElement.dataset.fetchBusy = active ? 'true' : 'false';
    document.body?.setAttribute('aria-busy', active ? 'true' : 'false');
    if (!loader) return;
    loader.hidden = !active;
    const label = loader.querySelector('[data-loader-text]');
    if (label) label.textContent = text;
}

function updateHeadFrom(nextDocument) {
    document.title = nextDocument.title || document.title;
    const nextToken = nextDocument.querySelector('meta[name="csrf-token"]')?.content;
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token && nextToken !== undefined) token.content = nextToken;
}

function activateNewBody(body, focusMain = true) {
    document.body.replaceWith(body);
    window.Alpine?.initTree(document.body);
    setBusy(false);
    if (!focusMain) return;
    const main = document.getElementById('contenido');
    main?.setAttribute('tabindex', '-1');
    main?.focus({ preventScroll: true });
}

async function renderFetchedPage(response, fallbackUrl, focusMain) {
    const type = response.headers.get('content-type') ?? '';
    if (!type.includes('text/html')) {
        window.location.assign(response.url || fallbackUrl);
        return false;
    }
    const html = await response.text();
    const nextDocument = new DOMParser().parseFromString(html, 'text/html');
    if (!nextDocument.body) throw new Error('Respuesta HTML inválida.');
    updateHeadFrom(nextDocument);
    activateNewBody(nextDocument.body, focusMain);
    return true;
}

export function redirectedUrl(responseUrl, fallbackUrl, body = null, currentHref = window.location.href) {
    const url = new URL(responseUrl || fallbackUrl, currentHref);
    if (url.hash || !(body instanceof FormData)) return url.toString();
    const returnTo = body.get('return_to');
    if (typeof returnTo !== 'string' || returnTo === '') return url.toString();
    const target = appRouteUrl(returnTo, url, currentHref);
    if (target.origin === url.origin && target.pathname === url.pathname && target.search === url.search && target.hash) {
        url.hash = target.hash;
    }
    return url.toString();
}

function appRouteUrl(value, targetUrl, currentHref) {
    const route = value.trim();
    if (/^[a-z][a-z\d+.-]*:/i.test(route) || route.startsWith('/') || route.startsWith('#')) {
        return new URL(route, currentHref);
    }
    const firstSegment = route.split(/[/?#]/, 1)[0];
    const marker = `/${firstSegment}/`;
    const index = targetUrl.pathname.indexOf(marker);
    if (firstSegment && index >= 0) {
        return new URL(targetUrl.pathname.slice(0, index + 1) + route, targetUrl.origin);
    }
    return new URL(route, currentHref);
}

async function visit(url, { method = 'GET', body = null, replace = false, text } = {}) {
    setBusy(true, text);
    try {
        const headers = { Accept: 'text/html', 'X-Requested-With': 'fetch' };
        if (method !== 'GET') headers['X-CSRF-Token'] = csrfToken();
        const response = await fetch(url, { method, body, headers, credentials: 'same-origin' });
        const nextUrl = redirectedUrl(response.url, url, body);
        if (method !== 'GET') history.replaceState({}, '', nextUrl);
        const rendered = await renderFetchedPage(response, url, method === 'GET');
        if (!rendered) return;
        if (method === 'GET') {
            history[replace ? 'replaceState' : 'pushState']({}, '', nextUrl);
            scrollToTarget(new URL(nextUrl).hash);
        } else {
            scrollToTarget(new URL(nextUrl).hash);
        }
    } catch {
        setBusy(false);
        window.location.assign(url);
    }
}

function scrollToTarget(hash) {
    if (!hash) {
        window.scrollTo({ top: 0, behavior: 'auto' });
        return;
    }
    document.getElementById(decodeURIComponent(hash.slice(1)))?.scrollIntoView();
}

function formBody(form, submitter) {
    try {
        return new FormData(form, submitter);
    } catch {
        return new FormData(form);
    }
}

export function installFetchNavigation() {
    document.addEventListener('click', event => {
        const link = event.target.closest?.('a[href]');
        if (!link || !shouldHandleLink(link, event)) return;
        event.preventDefault();
        visit(link.href, { text: 'Cargando página...' });
    });

    document.addEventListener('submit', event => {
        if (event.defaultPrevented) return;
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.closest('[data-no-fetch]')) return;
        if ((form.target || '').trim() !== '') return;
        const method = (form.method || 'GET').toUpperCase();
        if (!['GET', 'POST'].includes(method)) return;
        event.preventDefault();
        if (method === 'GET') {
            const url = new URL(form.action);
            url.search = new URLSearchParams(new FormData(form)).toString();
            visit(url.toString(), { text: 'Buscando...' });
            return;
        }
        visit(form.action, { method, body: formBody(form, event.submitter), text: 'Procesando...' });
    });

    document.addEventListener('ga:loader', event => {
        setBusy(Boolean(event.detail?.active), event.detail?.text || 'Procesando...');
    });

    window.addEventListener('popstate', () => visit(window.location.href, {
        replace: true,
        text: 'Cargando página...',
    }));
}
