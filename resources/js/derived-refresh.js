const channelName = 'gestion-avaluatoria-updates';
let channel = null;

function broadcast(detail) {
    window.dispatchEvent?.(new CustomEvent('ga:record-saved', { detail }));
    try {
        channel ??= typeof window.BroadcastChannel !== 'undefined' ? new window.BroadcastChannel(channelName) : null;
        channel?.postMessage(detail);
    } catch {}
}

function handle(detail) {
    if (!detail?.topic) return;
    document.querySelectorAll('[data-refresh-on-save-topic]').forEach(node => {
        if (node.dataset.refreshOnSaveTopic !== detail.topic) return;
        if (node.dataset.refreshing === '1') return;
        node.dataset.refreshing = '1';
        node.querySelector('[data-refresh-message]')?.classList.remove('hidden');
        setTimeout(() => window.location.reload(), 250);
    });
}

export function publishDerivedChange(form, result) {
    const topic = form?.dataset?.autosaveTopic;
    if (!topic) return;
    broadcast({ topic, version: result?.version ?? null, savedAt: result?.saved_at ?? null });
}

export function installDerivedRefresh() {
    window.addEventListener('ga:record-saved', event => handle(event.detail));
    try {
        channel ??= typeof window.BroadcastChannel !== 'undefined' ? new window.BroadcastChannel(channelName) : null;
        if (channel) channel.onmessage = event => handle(event.data);
    } catch {}
}
