# Revisión de captura de comparables — 30/09/2026

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

## Validación

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
