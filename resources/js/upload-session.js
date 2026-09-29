export const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

export function syncToken(body) {
    const token = csrfToken();
    if (token && body instanceof FormData) body.set('_token', token);
    return body;
}

export async function refreshSecurityToken() {
    const response = await fetch(window.location.href, {
        headers: { Accept: 'text/html', 'X-Requested-With': 'upload-keepalive' },
        credentials: 'same-origin',
    });
    const html = await response.text();
    const next = new DOMParser().parseFromString(html, 'text/html');
    const fresh = next.querySelector('meta[name="csrf-token"]')?.content;
    const current = document.querySelector('meta[name="csrf-token"]');
    if (fresh && current) current.setAttribute('content', fresh);
    return Boolean(fresh);
}

export function keepSessionAlive() {
    const start = window.setInterval || globalThis.setInterval;
    const stop = window.clearInterval || globalThis.clearInterval;
    const id = start(() => { refreshSecurityToken().catch(() => {}); }, 60000);
    return () => stop(id);
}
