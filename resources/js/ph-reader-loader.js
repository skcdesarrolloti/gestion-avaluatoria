export function phReaderUrl(baseUrl, revision) {
    const url = new URL('ph-pdf-reader.js', baseUrl);
    url.searchParams.set('v', revision);
    return url.toString();
}

export async function loadPhPdfReader(baseUrl, revision, importer = url => import(url)) {
    const reader = await importer(phReaderUrl(baseUrl, revision));
    if (typeof reader.preparePhPdfText !== 'function') {
        throw new Error('El lector PDF publicado está desactualizado. Recarga la página; si persiste, revisa la actualización de los archivos del lector.');
    }
    return reader.preparePhPdfText;
}
