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

## Objeto visible y tabla documental — 1 de octubre de 2026

1.5 muestra el texto del generador usado por el entregable. La respuesta de
 autoguardado refresca esta vista después de confirmar persistencia. Se redacta
con base de valor, tipo de inmueble, localización y finalidad; si faltan base o
finalidad, se indica pendiente sin asumir valor de mercado.

1.11 incorpora tabla Ítem/Descripción/Documento aportado o estado. Las siete
primeras filas siguen la referencia del usuario; conserva los demás tipos del
catálogo anterior. Cada fila admite 1000 caracteres, sin adjunto obligatorio.
No se copian los datos particulares de la escritura del ejemplo a expedientes.
Se mantienen observaciones y marcas anteriores; estas se identifican como
antecedentes, sin convertir casillas vacías en «No suministrado». La tabla se
presenta también en el entregable y sus valores pasan al texto consolidado.

Aplicar `202610010006_assignment_document_table.php` por el migrador o
AUTO_MIGRATE. Agrega una columna TEXT nullable; no borra ni reescribe datos.
Clientes anteriores que omiten la tabla no borran sus valores. El despliegue
actualiza código/assets, conservando la BD, .env y storage del hosting.
Antes de actualizar, esperar confirmación de autoguardado; los cambios aún
pendientes en memoria del navegador no equivalen a información persistida.

Validación: 443 verificaciones PHP, 109 pruebas JS, lint/build/check:size;
75 pruebas MySQL en ga_test_app/auth, puerto local desechable 33332, incluyendo
migración repetida, conservación y tabla recuperada por titular. HTTP con dos
sesiones confirma vista previa idéntica al entregable, persistencia de tabla,
observación histórica y cliente anterior. Escritorio inspeccionado; DOM estrecho
585 px de viewport y 562 px de contenido sin desbordamiento. Sin cambios de producción.
