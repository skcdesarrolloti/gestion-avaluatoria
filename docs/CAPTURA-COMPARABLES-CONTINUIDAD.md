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

## Archivos y comprobaciones antes de reorganizar

Vistas: valuation-methodology-search.php, valuation-methodology-source-links.php,
valuation-methodology-portal-results-paste.php y valuation-methodology-comparable-table.php.
Mantener lectores resources/js/*-paste.js y pruebas de importación y duplicados.
Prueba de integración de vistas: tests/comparable-search-render.php.
Ejecutar php tests/run.php, npm test, build, check:size y revisión del navegador.
Una reorganización no autoriza borrar muestras ni sustituir importadores por formularios básicos.
