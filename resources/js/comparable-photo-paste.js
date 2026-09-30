export function pastedPhoto(clipboard) {
    const files = [...(clipboard?.items || [])].filter(item => item.kind === 'file').map(item => item.getAsFile()).filter(Boolean);
    if (!files.length) throw new Error('Copia la imagen con «Copiar imagen», no su enlace, y vuelve aquí para pulsar Ctrl+V. También puedes pegar una captura.');
    if (files.length !== 1) throw new Error('Pega una sola foto a la vez para vincularla a esta muestra.');
    const file = files[0];
    const extension = { 'image/png': 'png', 'image/jpeg': 'jpg', 'image/webp': 'webp' }[file.type];
    if (!extension || !file.size || file.size > 5 * 1024 * 1024) throw new Error('La foto debe ser JPG, PNG o WEBP y pesar como máximo 5 MB.');
    return new File([file], `foto-pegada-${Date.now()}.${extension}`, { type: file.type });
}
