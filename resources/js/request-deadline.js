// Covers both the request and reading its response. A timeout never confirms a save.
export async function requestDeadline(operation, controller, milliseconds = 20000) {
    let timer;
    const deadline = new Promise((_, reject) => {
        timer = setTimeout(() => {
            const error = new Error('El servidor no confirmó el guardado en 20 segundos. Los cambios siguen pendientes en esta página; pulsa Guardar para reintentar.');
            error.name = 'TimeoutError';
            reject(error);
            controller.abort();
        }, milliseconds);
    });
    try { return await Promise.race([operation(), deadline]); }
    finally { clearTimeout(timer); }
}
