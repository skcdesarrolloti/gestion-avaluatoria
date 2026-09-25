export function installHelpTooltips(root = document) {
    if (!root?.querySelectorAll) return;
    const enhance = scope => {
        const nodes = [scope?.matches?.('.help-dot') ? scope : null,
            ...(scope?.querySelectorAll?.('.help-dot') || [])].filter(Boolean);
        nodes.forEach(dot => {
            const text = dot.dataset.tooltip || dot.getAttribute('title') || '';
            if (text === '') return;
            dot.dataset.tooltip = text;
            dot.setAttribute('tabindex', '0');
            dot.setAttribute('role', 'button');
            dot.setAttribute('aria-label', 'Ayuda: ' + text);
            dot.removeAttribute('title');
        });
    };
    enhance(root);
    if (root !== document || typeof MutationObserver === 'undefined' || !document.body) return;
    new MutationObserver(records => records.forEach(record => record.addedNodes.forEach(enhance)))
        .observe(document.body, { childList: true, subtree: true });
}
