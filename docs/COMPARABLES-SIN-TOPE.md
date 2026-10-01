# Comparables sin tope de 60 · 01/10/2026

Se elimina el tope de cantidad en captura, lectura de lotes, validación JSON y
repositorio. No se reemplaza por otra cuota de muestras. Las filas se crean al
necesitarlas; se conservan paginación de diez, IDs, fotos, autorización y versión
optimista. Se mantiene el límite técnico de 2 MB por petición JSON: un exceso
rechaza el guardado completo con error, nunca recorta silenciosamente la matriz.

La migración `202610010001_expand_comparable_sample_index.php` amplía el índice
TINYINT a INT UNSIGNED sin borrar datos. Es reintentable y conserva índices.
Aplicar `php bin/console.php migrate` o `AUTO_MIGRATE=true` al actualizar el hosting,
junto con los assets compilados. No editar migraciones anteriores ni reimportar.

Mercado Libre: las capturas entregadas muestran tres avisos leídos, uno sugerido
y dos posibles coincidencias. «Muestra 12 de la matriz» es una referencia a la fila
existente, no doce resultados. El resumen ahora muestra el total de esta página
y explica la numeración. No se cambian ni se borran las muestras de producción.

Coordenadas: art. 17.a exige georreferenciación aproximada; ante limitaciones de la
fuente, registrar la mayor precisión disponible y su procedencia. En la matriz:
Campos a revisar → Ubicación y mapa, latitud/longitud, precisión y notas con fuente.
No atribuir el centro del barrio como ubicación exacta. Los campos ya existen;
se completan conservando la muestra y sus fotos. Negociación y áreas diferenciadas
siguen pendientes según REVISION-RESOLUCION-941.md; esta entrega no los implementa.

Validación: 373 comprobaciones PHP, 100 pruebas JS; build y tamaño 56,3 KB gzip.
MariaDB desechable local, `GA_TEST_PORT=33327`, solo ga_test_app/ga_test_auth:
28 verificaciones, incluyendo 300 filas pasando por el lector JSON, recarga,
conflicto, propietario, fotos estables, coordenadas y migraciones reintentadas.
Prueba DOM de lotes: 84 nuevas sobre una existente, IDs únicos y fotos vinculadas.
Navegador local: nueva muestra 61, edición, contador, selección y grupo de ubicación.
No se ha actualizado el hosting ni modificado la matriz real.
