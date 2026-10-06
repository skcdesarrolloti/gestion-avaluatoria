# Recoger, consolidar y completar

Solicitud aprobada el 6 de octubre de 2026: minimizar trabajo repetido y densidad de Insumos.

1. Recoger por fuente: pegar resultados y «Subir sin repetidos de este portal». Conserva los datos del pegado y espera confirmación del servidor. No abre fichas individuales durante la incorporación. Las coincidencias de esta etapa se revisan dentro de la fuente.
2. Consolidación de las muestras: revisar coincidencias internas y entre fuentes, vincular únicamente identidades confirmadas y conservar sus anuncios como respaldo. Elegir una ficha principal y decidir qué inmuebles conservar. No borra registros ni cambia su selección histórica para Análisis.
3. Completar inmuebles únicos: leer una ficha principal por grupo confirmado, guardar cada complemento y presentar una fila por inmueble. No investiga automáticamente los anuncios de respaldo. Sin información no equivale a cero; solo aparecen columnas con datos recogidos.

`research_primary` se conserva en `capture_details`, sin migración. Vincular o separar invalida la confirmación de captura del grupo para revisarla; conserva precios, áreas, evidencias y selecciones analíticas. La lectura espera el guardado previo; un fallo al guardar el complemento detiene la ejecución.

## Lectura de las fuentes

El catálogo contiene cinco portales y once inmobiliarias. Se mantienen sus lectores de pegado. FincaRaíz conserva su lector de ficha técnica. La lectura general admite JSON-LD identificado, el formato público VisualInmueble con URL exacta y campos visibles dentro de una ficha inequívoca. Conserva características, descripción y anunciante explícito, sin interpretar metadatos técnicos como atributos. Área seguida de unidad y número se reconoce también en el pegado.

La cobertura de rutas se verificó con fixtures de las 16 fuentes. Esto no garantiza acceso automático a todos sus sitios vivos: errores HTTP, bloqueos, páginas que requieren JavaScript o formatos no identificables quedan pendientes para pegar la ficha. No se evaden restricciones ni se mezclan anuncios relacionados. La comprobación del HTML público de Asesorar produjo 2 baños, 92 m² y 36 atributos conservados; no se incorporó a ningún avalúo.

## Validación

- 1481 verificaciones PHP y 180 pruebas JavaScript.
- MariaDB nueva en puerto 33342, exclusivamente `ga_test_app` / `ga_test_auth`: 213 verificaciones, incluida recuperación de principal y respaldo.
- Compilación y límite de tamaño: 77,0 KB gzip, límite 80 KB.
- Revisión visual local en escritorio y móvil, con datos ficticios: 56 anuncios, 20 grupos, 36 respaldos; tabla de 20 filas. Sin errores de consola observados.
- Sin modificación de datos de producción. Publicación del código en `main`; el hosting necesita actualizarse para mostrar el cambio.

## Aclaración y reinicio para probar una captura nueva

La lista de todos los anuncios deja de ser la acción principal: las alertas muestran la pareja propuesta, precio, área, enlace y motivos concretos de coincidencia. «Reunir: confirmé que es el mismo» agrupa únicamente por decisión explícita. El menú para buscar otra coincidencia queda plegado. Mismo edificio, sin medidas u otra evidencia suficiente, ya no produce por sí solo una alerta.

La vista compacta muestra parejas pendientes y grupos vinculados pendientes. «Conservar N sin alertas de repetidos» confirma en bloque esos grupos, sin seleccionar para Análisis. El checkbox permite revisar todos los inmuebles. Los conteos distinguen anuncios incorporados, grupos después de reunir y confirmados para investigar; no afirman que una muestra histórica provenga de una prueba nueva.

«Empezar de cero · muestras de esta unidad» está visible en el recorrido principal. Ofrece respaldo Excel, reinicio explícito de toda la colección actual y restauración durante la página abierta. Utiliza el mecanismo existente de retiro y guardado confirmado; no cambia el sujeto ni otras unidades. Tras el reinicio confirmado, fuentes, atributos y decisiones se vacían y los contadores de portal y consolidación son cero. Las fotos conservan la identidad anterior, no se asignan a capturas nuevas. No se ejecutó el reinicio sobre producción: lo confirma el usuario desde ese control.

Validación adicional: 1482 verificaciones PHP, 182 pruebas JS, 77,1 KB gzip. Pruebas de parejas con motivos, confirmación en bloque limitada a anuncios sin alertas y reinicio con cero conteos/restauración. UI local con datos ficticios: 4 anuncios → 3 grupos; reinicio → cero; restauración → 4 anuncios. Sin cambios nuevos de persistencia ni esquema.

2026-10-06: corregido Abrir búsqueda en FincaRaíz para oficina venta Bocagrande, Cartagena. Ruta pública /venta/oficinas/bocagrande/cartagena verificada en navegador: barrio seleccionado, encabezado Bocagrande y 37 resultados. Se conserva el enlace de ciudad para barrios sin ruta comprobada. PHP 1483, JS 182, lint y build/tamaño 77,1 KB. No modifica muestras ni guardado. Requiere actualizar hosting para reflejar el botón.
