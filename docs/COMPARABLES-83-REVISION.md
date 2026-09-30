# Revisión de captura de comparables — 30/09/2026

## Corrección de la búsqueda concatenada

La vista académica de 8.1 reutilizaba `$guide` al recorrer los métodos. Los includes
PHP comparten ámbito, por lo que 8.3 recibía el último método en lugar de los datos
del sujeto. Se renombró la variable local a `$methodGuide`. Buscar y Capturar vuelven
a recibir la consulta generada con operación, tipología, barrio, localidad y ciudad.
No se inventan datos del expediente ni se alteran las reglas de composición.
La regresión renderiza academia y enlaces en el mismo ámbito y comprueba que se
conservan la consulta y los criterios en el enlace del portal.

## Aclaración del responsable y corrección de guías

El objetivo del responsable es automatizar la captura de avisos y conservar evidencia
para un informe explicado y sustentado, incluyendo posible uso judicial. 8.4 debe
desarrollar el análisis estadístico conforme a los criterios aprobados de la 941;
los grupos de atributos de 8.3 no constituyen coeficientes de homologación.
Ampliar la muestra no garantiza por sí solo reducir su dispersión: se requiere
comparabilidad, verificación y depuración documentada.

Se corrigió un fallo observado en producción: `render()` consultaba `$el`, que al
invocarse desde un selector podía ser ese selector en lugar del formulario. Ahora
conserva la referencia al formulario inicial y actualiza el valor antes de renderizar.
Se agregaron ayudas a cada desplegable y una guía del aviso a su soporte.
Comprobado en navegador el cambio inmediato a Atributos y la edición de sus campos.

La captura automática completa sigue pendiente de prueba con un aviso real del
usuario: evaluar lectura por URL y, si la fuente no lo permite, capturador voluntario
en navegador. Debe prellenar datos, conservar original, URL, fecha y soporte visual,
separar extracción de verificación humana, mantener selección y descarte trazables
y alimentar el anexo desde la evidencia guardada. No se presenta esa integración
como implementada ni se sustituyen soportes por fotos aisladas del inmueble.

## Resultado

La captura de 8.3 pasa de mostrar 60 filas por 47 columnas a fichas paginadas
de cinco en cinco. Conserva los controles y nombres enviados al guardado existente;
ocultar páginas o grupos no elimina ni deshabilita sus datos.

- Grupos: captura básica, ubicación, atributos y revisión; opción de todos los campos.
- Buscador y filtros de datos pendientes y enlaces repetidos; conteos operativos.
- Vista tabular opcional con altura acotada, cabecera y primera columna fijas,
  desplazamiento superior sincronizado y herramientas persistentes en escritorio.
- Fichas de una columna en móvil, con labels visibles.
- Consulta conjunta de los cinco portales existentes mediante Google. Es búsqueda
  en un índice externo, no integración ni descarga automática de anuncios.
- Pegado por lotes con encabezados de Excel/Sheets y omisión de enlaces repetidos
  tras retirar parámetros de seguimiento. No identifica el mismo inmueble entre portales.
- El importador evita interpretar precios o identificadores de URL como teléfonos,
  y no asigna números ambiguos a área/precio sin encabezados.
- El texto pegado se conserva. El mensaje informa registros cargados, repetidos y
  excedentes del límite existente de 60; la confirmación de BD sigue en el autoguardado.

No se modificaron esquema, cálculos, permisos, repositorio ni endpoints de guardado.
Para actualizar, publicar las vistas y assets compilados junto al código habitual.
No requiere configuración nueva ni migración. No se publicó al hosting.

## Revisión normativa y pendientes del entregable

No se dispone de los PDFs NTS en el almacenamiento local: contiene solo `.gitkeep`.
No se certifica cumplimiento ni se atribuye a NTS un mínimo universal de 30–40
muestras. La meta existente de 15 por factor y 60 en total está codificada en
`AppraisalComparableSampleDesignGuide`; requiere contrastar su fundamento con la
edición aplicable y los criterios del responsable. Los conteos nuevos son ayudas
de captura y no validan la suficiencia estadística ni la comparabilidad.

Como contraste separado de NTS, se revisó el artículo 17 de la Resolución IGAC
0941 de 2026, en la reproducción del Diario Oficial publicada por Camacol:
[documento fuente](https://camacol.co/sites/default/files/descargables/IGAC-Resolucion-2026-N0000941_20260731_Diario_Oficial-N053573_20260801.pdf).
Registra ubicación, valores, áreas, fuente y fecha. Su aplicación al encargo debe
revisarse conforme al ámbito de la resolución; no sustituye la norma sectorial.

Antes de presentar un anexo definitivo se requiere cerrar:

1. Norma/edición/cláusula aplicable, evidencia y ubicación en el informe.
2. Soportes por muestra, confirmación de vigencia, diferenciación de ofertas y
   transacciones, áreas pertinentes, comparabilidad y motivos de descarte.
3. Incorporación de las muestras al entregable: el generador actual conserva un
   texto preparatorio en 8.3 y no construye el anexo desde los comparables.
4. Control de concurrencia del guardado: el repositorio actual reemplaza la colección
   sin versionado optimista. No se ha resuelto ni probado un HTTP 409 de comparables.
5. Transporte de lotes grandes: revisar `max_input_vars` en hosting, dado que el
   formulario existente envía todos los campos, incluso los ocultos por paginación.

## Lectura por enlace y búsquedas por portal (30/09/2026)

- Presentación de una fuente a la vez: FincaRaíz inicial; se reemplaza la fila de
  pestañas simultáneas por «Cambiar de fuente», cerrado por defecto, con selector
  de portales/inmobiliarias. Elegir cierra el selector y solo muestra esa fuente.
  Conserva estado de captura y tabla; cambiar fuente no dispara autoguardado.
  Verificado cambio FincaRaíz/Metrocuadrado y regreso, escritorio y móvil 390 px;
  352 controles PHP, 67 pruebas JS, build y tamaño 50,3 KB gzip. Sin cambios de BD.

- Barrio de búsqueda desde catálogo: se precarga por `neighborhood_id` del sujeto,
  con sugerencias de barrios activos de su `city_id`. Tipo, operación y ciudad se
  muestran desde los datos del expediente, sin edición en esta búsqueda.
  Escribir invalida la selección y limpia resultados; elegir una sugerencia usa el
  nombre guardado y habilita buscar. No acepta texto libre para consultar.
- El POST recibe `neighborhood_id`, valida que esté activo y pertenezca a la ciudad
  del sujeto y resuelve su nombre en BD antes de construir la URL. URL del enlace
  externo generada en servidor con el mismo adaptador. Si falta ciudad/barrio válido,
  se indica completar el catálogo/expediente. La grafía del catálogo no garantiza
  que el portal use la misma ruta: sigue comprobándose el canónico de resultados.
- Sin esquema nuevo ni cambios en el barrio del sujeto. Pruebas con SQLite en
  memoria: rechazos de IDs de otra ciudad, inactivos y nombres libres. 352 controles
  PHP, 67 pruebas JS; navegador con catálogo de prueba: precarga, «Boca» → sugerencia,
  selección y enlace, escritorio/móvil. No se consultó la BD de producción ni se
  ejecutaron pruebas MySQL de persistencia. Build 50,3 KB gzip.
- Publicar también Kernel, controlador y repositorio GeoMaster junto con vista y JS;
  el contrato de búsqueda cambia de texto a ID y exige actualizar ambos lados juntos.

- Búsqueda por barrio en FincaRaíz para oficinas en venta en Cartagena: el barrio
  inicia desde el expediente y puede cambiarse sin modificar el sujeto. Consulta
  una página pública de resultados por petición, permite marcar varios avisos y
  agregarlos juntos a la tabla como por verificar. Página siguiente solo si existe
  enlace publicado; máximo 10 páginas. Los demás casos conservan lectura individual.
- Se verifica URL canónica del listado; no se aceptan redirecciones o un listado
  genérico que no confirme la zona. Mismas restricciones de conexión del lector.
  Endpoint POST `comparables/buscar-zona` con CSRF, sesión y propietario; comprueba
  tipo, negocio y ciudad del expediente. Máximo 30 consultas por 15 minutos/usuario.
- Se usa el resumen JSON-LD de cada aviso, no la ficha completa. Conserva URL, código,
  consulta, precio COP, área MTK y datos publicados. `query_used` guarda la URL de
  búsqueda con barrio y página. La ubicación del resumen puede ser inexacta y el
  área puede estar redondeada: revisar ficha y evidencia antes del análisis.
- La pestaña principal queda con barrio, buscar, resultados seleccionables y
  paginación. Captura por enlace y pegado de texto pasan a un desplegable secundario.
  Barrio y selección temporal no se guardan hasta incorporar; no modifica otras fuentes.
- Comprobación en vivo de Bocagrande: primera página 21 avisos legibles y segunda
  17 al momento de la prueba. No equivale a 38 inmuebles únicos ni comparables válidos.
  Validación: 347 verificaciones PHP, 64 pruebas JS, lint 489 PHP y build 50,3 KB gzip.
  Navegador: incorporación conjunta, contador acumulativo, siguiente página y fin
  de resultados, interfaz escritorio/móvil 390 px. No permite paginar con selección
  pendiente; incorporar o quitar selección primero.
  Sin migraciones ni cambios de persistencia; se mantienen los pendientes documentados.

- «Buscar otro inmueble» en FincaRaíz limpia enlace, vista previa, errores y estado
  de incorporación y abre la búsqueda de la pestaña. No modifica filas. Durante
  una lectura pendiente no permite reiniciar para evitar una respuesta tardía.
- Antes de incorporar por URL o texto, se contrasta con las filas del expediente,
  incluidas las de otras fuentes y las incorporadas en el mismo lote. URL idéntica
  normalizada se omite. Dirección numérica coincidente + área, o área + precio +
  sector/edificio coincidentes, generan confirmación de posible duplicado con
  números de muestra y motivos. Cancelar no incorpora; confirmar inmueble distinto
  incorpora dejando la decisión en observaciones. No compara fotos ni asegura
  identidad; los datos incompletos o distintos pueden impedir detectar repetidos.
  Es una ayuda en la captura del navegador, no una restricción única en BD ni
  validación de identidad al editar manualmente o enviar directamente al servidor.
- Validación: 341 verificaciones PHP, 64 pruebas JS (7 casos de coincidencias,
  cancelación y registro de decisión), lint 486 PHP, build y 49,6 KB gzip.
  No cambia esquema ni persistencia; publicar vista y assets compilados juntos.

- Flujo revisado con el usuario: pestaña propia para cada portal/inmobiliaria,
  agrupadas en Portales e Inmobiliarias. Cada pestaña reúne consulta del expediente,
  enlace externo, filtros disponibles y captura. FincaRaíz tiene lector por URL;
  las demás fuentes tienen enlace + texto, sin simular lectores automáticos.
- Todas las fuentes incorporan filas en la misma tabla. Contador y accesos entre
  captura/revisión. Tras incorporar un aviso por URL, el campo se limpia y recibe
  foco; ya no se desplaza automáticamente a la tabla. Texto no incorporado se
  conserva al alternar pestañas durante la sesión de página, no al recargar.
- Ayuda explica cómo abrir un aviso individual, distinguirlo de resultados y
  qué hacer si el portal muestra una sola oferta. No amplía criterios automáticamente.
- Verificación del flujo: lectura HTTP real, incorporación de FincaRaíz y texto
  desde otra pestaña (12 a 14 filas sin reemplazos), conservación del texto entre
  pestañas, agrupación de inmobiliarias, escritorio y móvil 390 px. Lint 486 PHP,
  341 verificaciones PHP, 57 pruebas JS, build y tamaño 48,8 KB gzip. Sin cambios
  de BD ni pruebas nuevas de persistencia. Publicar vistas y ambos assets juntos.

- Ajuste de navegación solicitado: se elimina «Buscar en todos los portales».
  Portales e inmobiliarias se muestran en listas numeradas independientes, con
  instrucciones de captura. Las inmobiliarias indican que sus filtros se aplican
  manualmente en su sitio; no se presentan como integraciones automáticas.
  Verificado el cambio en escritorio y móvil de 390 px, apertura de ayudas,
  lint de 485 PHP, 341 verificaciones PHP, 57 pruebas JS y build de 48,8 KB gzip.
  Sin cambios de esquema ni persistencia; no requiere migración.

- Nuevo bloque «Leer un aviso por enlace · FincaRaíz»: pegar enlace individual,
  leer, revisar vista previa e incorporar como `por_verificar`. La incorporación
  usa filas vacías, omite enlaces repetidos y muestra la página de la nueva ficha.
- Servicio PHP lee JSON-LD público. Probado con el aviso 194234202: COP 3.300.000.000,
  340 m² publicados, 5 alcobas, 6 baños, dirección, fecha, barrio y código. Es una
  prueba de extracción, no una selección como comparable de la oficina del expediente.
- Precio solo si la moneda publicada es COP; área solo en MTK. La clase de área,
  vigencia, ubicación, duplicados entre portales y criterio técnico se verifican.
  Los datos ausentes no se completan por suposición. No adjunta fotos ni PDF.
- POST protegido `comparables/leer-aviso`: sesión, CSRF y propiedad del expediente
  antes de leer. No escribe comparables. Allowlist HTTPS FincaRaíz, DNS público
  fijado a la conexión, sin redirecciones, TLS verificado, límite 2 MB y 20 s.
  Límite por usuario: 60 lecturas/15 minutos. Requiere PHP cURL y salida HTTPS.
- FincaRaíz oficinas venta Cartagena/Castillogrande y casas venta Castillogrande
  tienen enlace nativo verificado. Metrocuadrado y Ciencuadras: oficinas venta
  Cartagena; el barrio queda explícitamente pendiente en esos enlaces. Resto de
  combinaciones conserva Google identificado como tal, sin inventar filtros.
- La consulta usa tipología exacta; oficina ya no agrega consultorio ni edificio.
  No agrega localidad si ya hay barrio. No se modifican fórmulas ni criterios 8.4.
- Otros portales todavía requieren enlace + texto en la captura manual.
- Validación de esta actualización: 341 verificaciones PHP, 57 pruebas JS, lint
  de 485 PHP, build y tamaño 48,8 KB gzip. Lectura HTTP real y prellenado probados
  en vista local sin persistencia; no se ejecutaron migraciones ni pruebas de BD.
  Los pendientes de concurrencia, transporte y anexos mencionados arriba siguen vigentes.
- Navegador: vista previa, prellenado, salto a la ficha, rechazo de duplicado y de
  portal no admitido; móvil 390 px y escritorio sin desbordamiento del documento.
- Publicar PHP, vistas, rutas y assets compilados juntos. El push al repositorio
  no sustituye el despliegue manual del hosting.

## Validación previa de fichas y pegado de texto

- Lint PHP: 475 archivos correctos.
- `php tests/run.php`: 320 verificaciones correctas.
- `npm test`: 57 pruebas correctas, incluyendo números ambiguos, encabezados,
  URLs malformadas, identificación de enlaces y conservación de borradores parciales.
- `npm run build` y `npm run check:size`: 47,5 KB gzip, inferior a 80 KB.
- Navegador con vista PHP real y datos ficticios fuera del proyecto: cinco filas por
  página, cambio de grupos/vista, nueva muestra, filtro de búsqueda, edición y
  pegado de un lote con un enlace repetido y uno nuevo; valores de precio/área correctos.
- Revisión visual a ancho de escritorio y 390 px, sin desbordamiento del documento.
- La vista de prueba no guarda. No se probaron BD, recarga persistida ni login real;
  no se conectaron bases reales ni se ejecutaron migraciones.
