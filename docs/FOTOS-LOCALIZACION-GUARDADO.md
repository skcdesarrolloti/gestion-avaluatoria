# Guardado de fotos de localización · 23/11/2026

El botón del apartado 1.4 utilizaba `this.$el.dataset.uploadUrl`. Alpine evalúa
el clic con `$el` referido al botón, cuyo dataset no tiene esa dirección.
La reproducción local devolvía «Página no encontrada» y conservaba una vista
previa que nunca se incorporaba a la galería persistida.

Ahora la dirección se captura desde `$root` al inicializar el componente.
La carga espera el autoguardado del expediente, renueva un token CSRF vencido
una sola vez y conserva la selección ante error de red o sesión vencida.
Solo confirma éxito si el servidor devuelve una imagen nueva en la galería
de este apartado. El contador distingue imágenes guardadas de vistas previas.
Los enlaces de salida se bloquean mientras haya una selección pendiente;
se puede guardarla o quitar únicamente esa selección. Al cerrar/recargar se
solicita la advertencia nativa si quedan archivos pendientes.

El módulo se carga bajo demanda con versión de contenido, tanto al entrar
directamente como mediante navegación SPA. No hay migraciones, configuración
nueva ni alteración de la clasificación de imágenes ya existentes. Se conserva
el almacenamiento de archivo y copia binaria en la base de datos, con los
controles existentes de propietario, expediente y CSRF.

## Verificación

- Reproducido el fallo con imagen pegada antes de la corrección.
- Después: carga confirmada, imagen visible tras recarga y tras visitar el
  capítulo 3 y regresar al capítulo 1; ausencia de errores de consola.
- Aviso al intentar navegar con otra imagen pendiente; quitar esa selección
  conserva la imagen anterior. Vista estrecha sin desbordamiento del documento.
- `php tests/run.php`: 1791 comprobaciones; advertencias preexistentes por
  fixtures incompletos de sesión, versión y muestras.
- `npm test`: 319 pruebas. Incluye contexto real del botón sin URL, error de
  red, sesión vencida, reintento CSRF, confirmación de galería y carga diferida.
- `GA_TEST_PORT=3392 php tests/database.php`: 330 comprobaciones en una instancia
  nueva y desechable, exclusivamente `ga_test_app` y `ga_test_auth`. Incluye
  autorización, conflicto, migración repetida, conservación y respaldo de foto.
- Lint PHP completo y adicional del nuevo archivo HTTP; `npm run build` y
  `npm run check:size` correctos (79,8 KB gzip iniciales).
- `tests/location-photos-http.php` se incluye en `tests/http.php`: carga multipart,
  rechazo CSRF, contenido binario, recarga y aislamiento por expediente.
  Ejecución focalizada local en 8088, omitiendo los includes de PH/metodología:
  19 comprobaciones correctas, también login, conflicto, logout y sesión vencida.
- La suite HTTP general se detiene en una aserción ajena a este cambio:
  `tests/methodology-http.php:6`, expectativa de texto sobre unidades vacías.
  Ese módulo y su prueba no se modificaron.

## Recuperación

En el expediente revisado no se encontraron imágenes clasificadas como
`location:chapter-one`; sí una foto de portada conservada en el capítulo 3
y servida desde respaldo interno. No se reclasificó ni duplicó esa imagen.
La corrección no puede reconstruir cargas que nunca llegaron al servidor:
se necesitan los originales para volver a incorporarlas al apartado 1.4.
