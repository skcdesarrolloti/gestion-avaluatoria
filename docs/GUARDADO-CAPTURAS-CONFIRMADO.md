# Guardado confirmado de capturas por portal

Al agregar un lote, el lector espera el guardado de la matriz antes de informar
«Guardado confirmado en la base de datos». Durante el envío mantiene bloqueada
la incorporación. El envío usa el formulario compartido, su JSON completo, CSRF
y versión optimista; no introduce otro endpoint ni reemplaza datos de otras unidades.

La pantalla de búsqueda incluye Guardar matriz y estado de guardado accesible.
Una respuesta sin versión no se acepta como confirmación. Sin conexión, los datos
siguen pendientes en memoria y se reintentan al recuperar conexión; HTTP 409
conserva el conflicto sin sobrescribir. Guardar matriz permite reintentar también
un error de conexión cuando el navegador todavía se declara conectado.

Pegar solo prepara la vista previa: hay que pulsar Agregar. No cerrar ni recargar
mientras haya cambios pendientes. No se almacena información sensible localmente.
La recuperación de una vista previa sin incorporar sigue fuera del alcance.

Validación 2026-10-05: PHP 1420, JS 170, BD desechable 212 (puerto 3396), lint PHP,
build y tamaño 75,5 KB gzip. En avalúo ficticio local: seis avisos anteriores
conservados, un aviso nuevo guardado y recuperado tras recarga con precio, área,
baños, parqueaderos, ascensor y atributos originales. Servidor de prueba detenido:
Guardar matriz mostró pendiente; reiniciado, el reintento confirmó persistencia.
Escritorio y vista estrecha sin desbordamiento. Prueba visual en outputs del chat.

No se inspeccionó la base de producción: no se ha determinado si los avisos que
el usuario dejó de ver se borraron o estaban filtrados/no incorporados. Esta
entrega no elimina muestras ni modifica el esquema. Pendientes y confirmados
siguen siendo estados distintos de captura y análisis.
