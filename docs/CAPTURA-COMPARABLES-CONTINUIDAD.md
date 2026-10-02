# Flujo que debe conservarse en C

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
