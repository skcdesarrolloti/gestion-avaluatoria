# Datos del solicitante y soporte visual — 1 de octubre de 2026

En Solicitud y cliente se agregan correo, celular y municipio del solicitante.
Este municipio es independiente del municipio del inmueble. Fecha de solicitud
se mueve desde Tipo y fechas, conservando la misma columna y sus valores.
Uso previsto admite 1400 caracteres en captura, validación y lectura urbana.
El numeral 1.3.5 conserva fecha y agrega explicación de hasta 1400 caracteres.
Los datos nuevos se incluyen en la memoria descriptiva.

El numeral 1.4 antes solo tenía una referencia textual. Ahora admite archivo o
captura pegada, nombre, vista previa, confirmación y galería persistida. Usa el
servicio de fotos existente, archivo privado y respaldo en BD. La imagen se
incluye en la sección 1.4 del entregable. El cliente espera el guardado pendiente
antes de subir, conserva la selección ante error y no recarga el formulario.
Una respuesta no confirmada requiere reintento; comprobar la galería antes de
repetir una carga cuya respuesta se perdió. Formatos JPG/PNG/WebP, hasta 12 MB;
los límites upload_max_filesize/post_max_size del hosting también aplican.

## Actualización

Publicar código y assets compilados. Aplicar
`202610010005_assignment_contact_and_value_date_notes.php` mediante
`php bin/console.php migrate` o AUTO_MIGRATE. Agrega cuatro columnas; no modifica
migraciones anteriores, cuentas, fotos o avalúos existentes. Una pestaña anterior
que omita los campos nuevos conserva sus valores al guardar.

## Verificación

- Lint PHP, 437 verificaciones PHP, 109 pruebas JS, build y límite de tamaño:
  57,5 KB gzip. Pruebas JS de orden de guardado, conflicto y pérdida de conexión.
- MySQL local desechable, GA_TEST_PORT=33331, exclusivamente ga_test_app y
  ga_test_auth: 73 verificaciones, migración repetida, conservación, Unicode,
  versiones, informe, campos omitidos, fotos y acceso delegado.
- HTTP local: login analista, guardado/recarga en dos sesiones, CSRF, 409,
  subida multipart real, lectura de bytes por titular y analista, rechazo de
  foto atribuida a otro expediente y presencia en entregable.
- Interfaz real como analista: nuevos campos, descripción de fecha y galería
  recuperada; escritorio inspeccionado visualmente. DOM estrecho: viewport
  efectivo 585 px, contenido 562 px, sin desbordamiento. No captura móvil.
- El selector de archivo automatizado de Chrome fue bloqueado por el permiso
  de archivos de la extensión. La subida se verificó por HTTP, no por ese selector.

No se accedió ni se modificó la base de producción. La actualización del hosting
y la migración deben estar aplicadas para que el analista vea los cambios.
