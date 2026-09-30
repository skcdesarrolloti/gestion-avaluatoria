# Matriz, PH y fotos de comparables — 30/09/2026

El flujo 8.3 queda en Buscar, Capturar, Matriz de datos y Mapas. Capturar muestra
solo la fuente activa y sus candidatos. La matriz inicia en tabla, con diez filas
por página, columnas por grupos, encabezados y primera columna fijos y barra de
desplazamiento superior. Conserva fichas como alternativa. La revisión pasa a los
grupos de la matriz y al desplegable de criterios; no se eliminan datos técnicos.

Cada fila tiene «Fotos»: confirma los autoguardados pendientes antes de consultar
el soporte y permite subir una imagen por acción, tantas veces como se requiera.
Admite JPG, PNG o WEBP hasta 5 MB; valida contenido/extensión en servidor, muestra
miniaturas privadas y conserva descripción, nombre original, fecha y SHA-256.
Una imagen idéntica en la misma muestra no se duplica. No descarga fotos del portal,
no admite PDF en este control y no genera todavía el anexo final del informe.

PH se registra por muestra: Sí, No o Por verificar. El sujeto se muestra solo como
referencia desde BD. No se infiere PH de administración, amenidades ni del sujeto.
Los resúmenes del portal llegan por verificar; el usuario los clasifica. El filtro
de captura actúa sobre los candidatos cargados de esa página, no es un filtro
remoto de FincaRaíz. Seleccionar todos respeta ese filtro. La matriz permite filtrar
las muestras ya incorporadas por régimen, sin descartarlas ni borrarlas.

El panel «FincaRaíz · Datos del expediente» muestra PH antes de buscar, junto a
tipo, operación y ciudad. Lee `regimen_ph` del expediente en un campo de solo
lectura: Sí, No, No aplica o Por definir en el expediente si falta el dato.
Verificado en escritorio y móvil; lint de la vista, 359 verificaciones PHP,
69 pruebas JS, build y control de tamaño correctos. No requiere migración nueva.

## Persistencia y despliegue

### Revisión de coincidencias antes de incorporar

FincaRaíz numera los avisos y resalta en amarillo las coincidencias con otros
resultados de la página o con muestras de la matriz. Muestra número, motivos,
precio, área, ubicación y enlace para comparar. Reutiliza las señales existentes;
una coincidencia no acredita identidad. Revisa de nuevo al pulsar Agregar y detiene
todo el lote si hay coincidencias seleccionadas sin resolver. Desmarcar uno de dos
avisos coincidentes permite conservar el otro; las coincidencias con la matriz
siguen requiriendo revisión. La casilla de inmueble distinto reemplaza el diálogo
en esta captura y conserva la anotación de revisión. Un enlace exacto no admite
esa excepción. Las demás formas de captura mantienen su confirmación anterior.

Validación: 359 verificaciones PHP, 72 pruebas JS, build y 51,7 KB gzip. Navegador
local con datos ficticios: bloqueo previo del lote, desmarcado e incorporación de
los restantes, coincidencia posterior con la matriz y vista móvil de 390 px.
No cambia persistencia ni requiere migración nueva; publicar vista y assets juntos.

- Aplicar `202609300003_comparable_ph_and_photos.php` con `php bin/console.php migrate`
  o `AUTO_MIGRATE=true`. Agrega `ph_regime`, `appraisals.comparables_version` y la
  tabla privada `appraisal_comparable_photos`. No elimina tablas ni columnas.
- IDs de muestras generados antes de capturar y conservados al guardar. Las fotos
  se vinculan por ID, avalúo y propietario; sin FK con borrado en cascada porque el
  guardado existente reemplaza filas de la colección. No se purgan fotos al guardar.
- Archivo binario en BD, fuera de la raíz web. Respaldo de BD incluye las fotos.
  Ajustar `upload_max_filesize` a al menos 5M y `post_max_size`/`max_allowed_packet`
  por encima de 6 MB para aprovechar todo el límite admitido.
- GET de listado/imagen y POST de carga autenticados y limitados al propietario
  del avalúo y de la muestra. POST pasa por CSRF del Kernel. Imágenes sin caché
  pública, MIME comprobado y `nosniff`.
- Autoguardado envía la matriz en un campo JSON para evitar truncamiento por
  `max_input_vars`. El marcador final rechaza envíos HTML incompletos. Versión
  optimista actualizada dentro de la transacción: HTTP 409 si cambió otra pestaña.
  Estos cambios resuelven los pendientes de transporte/concurrencia de la revisión
  anterior para esta matriz; no cambian versiones de otros módulos.
- Publicar PHP, migración y assets juntos. Una pestaña abierta con la versión
  anterior debe guardar antes del despliegue y recargarse después. Push no despliega
  automáticamente en hosting.

## Validación

- PHP: 359 verificaciones; JS: 69 pruebas; lint de 497 PHP; build y 51,3 KB gzip (límite 80 KB).
- MariaDB local desechable, `GA_TEST_PORT=33319`, exclusivamente `ga_test_app` y
  `ga_test_auth`: 24 verificaciones. Incluye migración repetida, 60 filas, conflicto,
  conservación de foto al reordenar, duplicado por huella y aislamiento de propietario,
  avalúo y muestra. Sin conexión a bases reales.
- Navegador con vistas reales, datos ficticios y base local: captura separada,
  selección PH/importación, tabla paginada, carga multipart real de PNG, guardado
  de PH y recuperación de foto tras recargar. Revisión escritorio y móvil 390 px.
- Login/inactivos y CSRF se mantienen en las pruebas existentes; la vista previa
  local de carga usa un usuario de prueba fijo y no valida el login de producción.
- La foto se conserva como soporte; no acredita por sí sola comparabilidad o NTS.
