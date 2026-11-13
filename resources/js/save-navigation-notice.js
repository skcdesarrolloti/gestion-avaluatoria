export function showSaveBlocked(link) {
    document.querySelector('[data-save-blocked]')?.remove();
    const status=document.querySelector('[data-autosave-tone="error"], [data-autosave-tone="offline"], [data-autosave-tone="pending"]');
    const form=status?.closest('form'), notice=document.createElement('section');
    notice.dataset.saveBlocked=''; notice.setAttribute('role','alert');notice.setAttribute('tabindex','-1');
    notice.className='mb-5 rounded-xl border bg-amber-50 p-5 text-amber-900';
    const title=document.createElement('p'), reason=document.createElement('p'), retry=document.createElement('button');
    title.textContent='Hay cambios sin guardar. No se abrió «'+link.textContent.trim()+'».';
    title.className='font-semibold';
    reason.textContent=status?.textContent || 'Revisa el guardado. Conserva esta página abierta.';
    reason.className='my-3 text-sm';
    retry.type='button';retry.className='btn-secondary';retry.textContent='Reintentar guardado y continuar';
    retry.addEventListener('click',()=>link.click());notice.append(title,reason,retry);
    const logout=document.querySelector('form[action$="/logout"]');
    if (form?.dataset.autosaveLogin==='1' && logout) {
        const login=document.createElement('a');login.href=new URL('login',logout.action).href;
        login.target='_blank';login.rel='noopener';login.className='btn-secondary ml-2';
        login.textContent='Iniciar sesión en otra pestaña';notice.append(login);
    }
    (document.getElementById('contenido') || document.body).prepend(notice);
    notice.focus({preventScroll:true});notice.scrollIntoView({block:'start'});
}
