export function isLoginRedirect(responseUrl,currentHref=window.location.href) {
    try {
        const url=new URL(responseUrl,currentHref);
        return url.origin===new URL(currentHref).origin && /\/login\/?$/i.test(url.pathname);
    } catch {return false;}
}

export async function refreshCsrf(signal) {
    const response=await fetch(window.location.href,{signal,credentials:'same-origin',
        headers:{Accept:'text/html','X-Requested-With':'fetch'}});
    if(!response.ok || isLoginRedirect(response.url)) return false;
    const next=new DOMParser().parseFromString(await response.text(),'text/html');
    const token=next.querySelector('meta[name="csrf-token"]')?.content;
    if(!token) return false;
    document.querySelector('meta[name="csrf-token"]').content=token;
    document.querySelectorAll('input[name="_token"]').forEach(input=>{input.value=token;});
    return true;
}
