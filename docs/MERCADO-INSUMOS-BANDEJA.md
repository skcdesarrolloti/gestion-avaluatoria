# Mercado: recogida por inmueble y fuentes

M3 abre Buscar inmuebles / Inmuebles recogidos. La bandeja predeterminada muestra
tarjetas paginadas por inmueble confirmado, precios/áreas originales por anuncio,
pendientes y diferencias. Tabla completa, Excel y fotos se conservan como respaldo.
No depura precios, no promedia fuentes ni adopta automáticamente una muestra.

Cada anuncio mantiene su ID, código, enlace, campos y fotos. `property_group` en
capture_details vincula explícitamente anuncios de la misma colección. Las coincidencias
por edificio/barrio o contacto/área son candidatas; nunca una identidad confirmada.
Vincular/separar es reversible y vuelve a Por revisar. Unidades y métodos conservan
colecciones independientes. Cambiar unidad conserva la etapa 3/4/5.

`intake_state`: review, selected, selected_pending, not_selected. Selección no equivale
a estado usada ni aprobación. Análisis recibe sólo grupos con todos sus anuncios
seleccionados; muestras usadas anteriores sin metadata conservan compatibilidad.
Anuncios vinculados quedan fuera de estadísticos existentes hasta desarrollar su
depuración/adopción; no se contabilizan como observaciones independientes.

Los lectores amplían captura de hechos rotulados en descripción y datos disponibles:
área privada CONSTRUIDA, libre, construcción/terreno, cantidades de anexos, administración,
contacto y atributos. Área privada sin especificación no se presume construida.
FincaRaíz lee descripción y additionalProperty estructurados; Metrocuadrado descripción
disponible; pegados Ciencuadras/Properati/MercadoLibre conservan texto por tarjeta.
No equivale a un scraper universal: listados no contienen todos los detalles y sus
formatos pueden cambiar. No inventa inclusión en precio, matrícula ni coordenada exacta.
Fragmentos de texto limitados a 1600 caracteres; consultar enlace para texto completo.

Recoger nuevos y complementar existentes incorpora posibles coincidencias sin declararlas
distintas. Se mantiene Agregar sugeridos sin coincidencias y la selección individual.
Mismo enlace llena vacíos; cambios de precio/área/cantidades se anotan en source_updates,
sin sustituir importes originales. latest_source_excerpt permite contrastar última lectura.
Nueva información devuelve a revisión. Guardado conserva versiones, CSRF y autorización.

Ubicación publicada sólo referencia. M3 no ofrece mapa como verificación. M4 confirma
manualmente coordenadas, precisión exact/approximate, fuente y responsable/fecha/soporte.
El mapa Google se muestra únicamente con confirmación y soporte guardados. No se calculan
distancias ni se verifican direcciones automáticamente. Referencias anteriores se conservan
en published_location al confirmar manualmente. No desarrolla exclusión de datos atípicos.

Sin cambio de esquema; metadata aditiva en JSON existente. Pruebas: PHP835, JS136,
BD168 más cinco nuevas de persistencia, lint y build; 68 KB gzip dentro de 80 KB.
Chrome local con datos ficticios: vincular precios100/110 y áreas80/82, seleccionar un grupo,
recepción sólo del grupo, punto aproximado guardado/recargado, captura85,5m²/2garajes/1depósito.


Recorrido de investigación reorganizado (2026-10-05): tres pasos principales: Buscar por portal → Revisar por portal → Confirmados y factores. La revisión abre un anuncio por vez y muestra botones por fuente con conteos arriba, incluidos los portales sin anuncios. El portal elegido se comparte con la búsqueda; alias FincaRaiz/FincaRaíz y Mercado Libre/Inmuebles se reconocen. Confirmados conserva todas las filas de factores y sólo las fuentes confirmadas, sin seleccionar automáticamente para Análisis. Guía de campos de portales y planificación se mantienen como herramientas de apoyo, sin numeración de pasos; el plan agrupa disponibilidad, viabilidad y clasificación en desplegables. Instrucciones extensas, inmobiliarias y respaldo Excel siguen disponibles plegados. No borra datos, modifica escalas, calcula regresión ni cambia persistencia. Validación: 1414 PHP, 162 JS, lint, build/tamaño72,7KB; navegador local con datos ficticios, fuente vacía, confirmados, apoyo y disposición estrecha sin desborde documentado. Screenshot local: Recorrido-investigacion-por-portal.png.
