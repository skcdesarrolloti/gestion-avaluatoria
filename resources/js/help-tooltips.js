export function installHelpTooltips(root = document) {
    if (!root?.querySelectorAll) return;

    const closeAll = except => {
        (document.querySelectorAll?.('.help-dot.is-open') || []).forEach(dot => {
            if (dot !== except) dot.classList.remove('is-open');
        });
    };

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
            if (dot.dataset.helpEnhanced === '1') return;
            dot.dataset.helpEnhanced = '1';
            dot.addEventListener('click', event => {
                event.preventDefault();
                event.stopPropagation();
                const nextState = !dot.classList.contains('is-open');
                closeAll(dot);
                dot.classList.toggle('is-open', nextState);
            });
            dot.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    dot.classList.remove('is-open');
                    return;
                }
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    dot.click();
                }
            });
        });
    };

    enhance(root);
    if (root === document && document.body && document.documentElement.dataset.helpClickInstalled !== '1') {
        document.documentElement.dataset.helpClickInstalled = '1';
        document.addEventListener('click', event => {
            if (!event.target?.closest?.('.help-dot')) closeAll(null);
        });
    }
    if (root !== document || typeof MutationObserver === 'undefined' || !document.body) return;
    new MutationObserver(records => records.forEach(record => record.addedNodes.forEach(enhance)))
        .observe(document.body, { childList: true, subtree: true });
}
