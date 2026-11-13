import {refreshCsrf,isLoginRedirect} from './session-refresh.js';

export async function postAutosave(form,body,signal) {
    const send=()=>fetch(form.dataset.autosaveEndpoint,{method:'POST',body,signal,credentials:'same-origin',
        headers:{Accept:'application/json','X-CSRF-Token':document.querySelector('meta[name="csrf-token"]')?.content ?? ''}});
    let response=await send();
    if(response.status===419 && await refreshCsrf(signal)) {
        body.set('_token',document.querySelector('meta[name="csrf-token"]').content);
        response=await send();
    }
    if(response.status===401 || response.status===419 || (response.url && isLoginRedirect(response.url))) {
        form.dataset.autosaveLogin='1';
        throw new Error('La sesión venció. Inicia sesión en otra pestaña y reintenta. No recargues: conserva aquí tus cambios.');
    }
    delete form.dataset.autosaveLogin;
    return response;
}
