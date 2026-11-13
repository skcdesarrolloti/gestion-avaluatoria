# Navegación y recuperación del guardado

## Incidente observado

El enlace «8 · Metodología valuatoria» parecía no responder desde capítulo 3.
La navegación esperaba el autoguardado, pero este devolvía sesión de seguridad
vencida. El aviso quedaba junto al formulario, fuera de la posición visible.
Tras iniciar sesión en otra pestaña se pudo abrir el capítulo 8 en producción.
No se modificaron datos reales ni se confirmó el guardado del borrador anterior.

## Comportamiento corregido

- Al navegar se indica que se están guardando los cambios. Si el guardado falla,
  la página conserva sus campos y enfoca un aviso con el destino y la causa.
- El aviso permite reintentar el guardado y continuar. Cuando falta la sesión,
  ofrece iniciar sesión en otra pestaña, manteniendo abierta la del borrador.
- Un 419 intenta obtener un token desde la página autenticada y repite el mismo
  POST una sola vez. Solo actualiza tokens; conserva campos y versión del envío.
- Una redirección a login no se acepta como renovación ni como guardado exitoso.
  El servidor conserva autorización por registro, CSRF y control de versión 409.
- El selector IGAC se descarga al entrar en una página que lo utiliza, antes
  de montar Alpine. El recurso tiene versión derivada de su contenido.

No hay migraciones, reglas de valoración, cálculos ni configuración nueva.
El borrador sigue en memoria: cerrar o recargar sin confirmar puede perderlo.
La recuperación no resuelve conflictos 409 ni reemplaza datos de otra pestaña.

## Validación

- Lint PHP: 787 archivos correctos; no se modifica código PHP en esta entrega.
- `php tests/run.php`: 1.680 verificaciones correctas.
- `npm test`: 291 pruebas correctas, incluyendo renovación única, login vencido,
  conservación de borrador/versión, conflicto, aviso y carga diferida de IGAC.
- `npm run build` y `npm run check:size`: 81.913 bytes gzip iniciales de 81.920.
- `GA_TEST_HTTP=true`, `GA_TEST_PORT=3384`, `php tests/lease-http.php`: 18 pruebas,
  exclusivamente con `ga_test_app` y `ga_test_auth` locales desechables.
- Navegador local contra PHP y esas bases: 419 seguido de renovación y guardado;
  sesión caducada, aviso visible y borrador intacto; login en pestaña separada,
  reintento y entrada al capítulo 8; recarga confirma persistencia. Escritorio
  y móvil de 390 px sin desbordamiento ni errores JavaScript.

La comprobación pública de recuperación de un borrador no se fuerza con datos
del usuario; esa secuencia se valida con los registros locales desechables.
