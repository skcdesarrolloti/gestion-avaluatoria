# Flujo que debe conservarse en C

Actualización 2026-10-03: [bandeja por inmueble y anuncios](MERCADO-INSUMOS-BANDEJA.md).
Tarjetas como vista principal, tabla/Excel conservados; selección para Análisis,
vinculación explícita de fuentes y verificación manual de ubicación en M4.

Actualización vigente: [recorrido por unidad y conservación](RECORRIDO-POR-UNIDAD.md).
Una unidad visible, método registrado, academia íntegra e insumos propios.

C tiene dos entradas: Buscar inmuebles y Tabla de muestras. Mapas, coordenadas
y fotos son una vista de las mismas muestras dentro de la segunda entrada.
Los criterios y academia permanecen plegables; no desplazan el lector de portales.

## Funciones existentes que no deben perderse

- Fuentes e inmobiliarias visibles, enlaces y filtros según unidad seleccionada.
- FincaRaíz y Metrocuadrado: búsqueda de barrio, lector y captura de texto.
- Ciencuadras, Properati y Mercado Libre: pegado de resultados para oficinas en
  venta, Ctrl+A/C en el portal y Ctrl+V en el campo, con lectura automática.
- Conteos de avisos leídos, sugeridos, registrados y posibles coincidencias.
- Agregar sugeridos sin coincidencias; selección manual opcional y revisión.
- Una sola matriz compartida entre vistas. Cambiar pestañas no remonta el formulario.
- Autoguardado confirmado por servidor, versiones, fotos y muestras conservadas.

No afirmar que cada portal admite lectura de resultados para todas las tipologías.
El lector depende de la unidad seleccionada, no del tipo global del expediente.

## Captura PH por composición (2026-10-02)

La consulta preparada añade instrucciones PH cuando el contexto del sujeto confirma
ese régimen: área privada, sinónimos de parqueadero y depósito, cantidades,
inclusión en precio, naturaleza jurídica y soporte. Es una guía copiable; no un
filtro nuevo impuesto a los portales ni extracción automática de derechos jurídicos.
La igualdad de anexos no elimina la depuración prevista en el art. 19.2.b.

La misma matriz ofrece «PH · área privada, parqueaderos y depósitos» como grupo
propio y lo selecciona inicialmente en expedientes PH. Mantiene captura básica,
NPH/condominio, tabla/fichas, mapas, fotos y lectores. ComparablePhCapture define
campos estructurados persistidos en capture_details; no hay migración nueva.
Una pantalla anterior conserva los campos omitidos por ID. Vacío significa dato
no publicado o pendiente; no se transforma en ausencia ni se infiere matrícula.
Las áreas y naturaleza detalladas anteriores siguen disponibles en ph_units_detail.

Actualización M3: [negociación y Excel](PH-M3-TABLA-NEGOCIACION.md) agrega descuento
monetario y oferta menos descuento, con tipo y soporte, conteos e instrucciones
por portal. M4 conserva estadísticos descriptivos existentes: no calcula aún
valor depurado ni valores adoptados del sujeto. Eso requiere desarrollar la memoria
y sus soportes. El tratamiento de comunes de uso exclusivo
del sujeto (art. 36.2) sigue distinto del descuento del comparable (art. 19.2.b).

Validación: 598 checks PHP, 116 JavaScript y 130 checks BD local desechable en
puerto 3358, lint PHP, build y 59,0 KB gzip. Navegador con ejemplo ficticio en
escritorio y CSS 390 px sin desbordamiento ni errores de consola.

## Archivos y comprobaciones antes de reorganizar

M3 tabla editable prioritaria con autoguardado. Excel opcional: exportación espera
guardar; importación revisa diferencias por ID y exige archivo de misma colección
y versión antes de aplicar y guardar. No elimina filas omitidas ni crea muestras.
Ver PH-M3-TABLA-NEGOCIACION.md para límites y pruebas de retorno.

Vistas: valuation-methodology-search.php, valuation-methodology-source-links.php,
valuation-methodology-portal-results-paste.php y valuation-methodology-comparable-table.php.
Mantener lectores resources/js/*-paste.js y pruebas de importación y duplicados.
Prueba de integración de vistas: tests/comparable-search-render.php.
Ejecutar php tests/run.php, npm test, build, check:size y revisión del navegador.
Una reorganización no autoriza borrar muestras ni sustituir importadores por formularios básicos.

Vista: orientación, paisaje, panorama y esquina se investigan por separado;
la revisión queda documentada en PLAN-INVESTIGACION-MERCADO.md (2026-10-04).
No confundir restaurar catálogo con adoptar escala y revisar calificaciones del plan.

Vista vigente se simplifica a cuatro clases del usuario; véase la sección final
2026-10-04 de PLAN-INVESTIGACION-MERCADO.md. Conserva información anterior.

Referencia del sujeto: capítulo 3, pestaña Factores del sujeto, comparte catálogo
y clasificaciones con los comparables. Insumos consulta valor/soporte de esa captura;
no la sustituye con otra calificación manual. Escalas anteriores requieren revisión
explícita. No cambia lectura de portales ni integra regresión. Véase
FACTORES-SUJETO-CAPITULO-3.md.
# Captura ampliada · 5 de octubre de 2026

- Conservar muestras previas. `Agregar sugeridos` excluye anuncios ya registrados; completar la misma URL enriquece vacíos sin crear otra muestra.
- `published_attributes` conserva etiquetas originales (hasta 80 pares, 500 caracteres por valor); `published_text` conserva hasta 16.000 caracteres con aviso de abreviación. Ambos viajan en `capture_details`, sin cambio de esquema.
- El lector FincaRaíz conserva `additionalProperty`; Metrocuadrado conserva los campos del resultado reconocido. Pegados y fichas manuales usan extracción común. Un resumen no equivale a leer toda la ficha: completar los atributos pendientes desde el anuncio individual.
- Las tarjetas incluyen cuadro sujeto / anuncios / validación. Sólo comparar versiones del mismo grupo confirmado; las coincidencias entre otros grupos son propuestas para revisión. No fusionar automáticamente.
- Los cuadros de las tarjetas usan roles semánticos, no otro `tbody`: los lectores y crecimiento de filas siguen trabajando sobre la única tabla de captura.
- Todos los atributos siguen disponibles. El cuadro abre compacto y permite desplegar los factores sin datos. Correlación y elección final de predictores corresponden a Análisis.

## Revisión individual y conformación de investigación

- La bandeja revisa un anuncio por página, filtrado por portal. El cuadro de confirmados agrupa únicamente fuentes confirmadas y muestra todos los factores.
- capture_confirmation es independiente de intake_state: confirmar para investigación no selecciona para Análisis. No participa conserva el aviso; Volver a pendiente revierte la decisión.
- Conserva identidad, fuentes, fotos, importadores, captura ampliada y tabla de respaldo. No borra las muestras previas ni modifica sus selecciones históricas.
