# Capítulo 3 y ficha compartida de arriendo

13/11/2026. Descripción y tipologías se consultan en el capítulo 3; Renta reúne
los contratos documentados para capitalización directa y flujo de caja descontado.
No añade fórmulas de valoración, proyecciones, tasas ni porcentajes de vacancia.

## Un solo origen para cada dato

- Capítulo 3: unidad, tipo, tipología IGAC, descripción, áreas y atributos. La
  ocupación por unidad añade situación, fecha y fuente; el antecedente general
  se conserva y no se distribuye automáticamente entre unidades o anexos.
- Renta → Insumos → 1. Ficha del arriendo: identificación y estado del contrato,
  unidades incluidas y alcance, canon/periodicidad, administración/IVA, cobros
  documentados diferentes del canon, fechas, plazo, prórrogas, incrementos,
  conceptos incluidos, gracia, responsables, soporte y pendientes.
- El mismo contrato puede relacionar oficina y garaje u otras unidades; todos
  los recorridos consultan ese registro. Agregar otro contrato crea otra ficha.
- El canon anterior del capítulo 1 conserva sus columnas originales. Se corrige
  en «Antecedente económico del encargo» en Renta y se consulta en el capítulo 1.
  Solo un contrato puede referenciarlo. No se copia ni se atribuye a cada anexo.
  El usuario también puede registrar un canon propio para un contrato distinto.
- Las respuestas conceptuales anteriores quedan visibles como antecedentes por
  recorrido, sin interpretar automáticamente texto libre como fechas o importes.
  El endpoint anterior y la recuperación de sus borradores siguen disponibles.

Los cuatro temas y soportes de Academia siguen accesibles. Muestras, portales,
investigaciones por método/operación y reglas posteriores de análisis no cambian.
El criterio CV ≤ 7,5 % permanece para la etapa de análisis; no es un descuento
ni una estimación de vacancia.

## Tipologías

Un selector común sirve a todos los tipos de inmueble: muestra familias de
consulta relacionadas, búsqueda y acceso explícito al catálogo completo.
Oficinas incluyen Comerciales y Edificios; consultorios también Institucionales.
La selección anterior permanece visible al buscar o cambiar el tipo.
Servidor y navegador resuelven la categoría de la referencia elegida.
No se inventan tipologías ni se modifican descripciones oficiales del catálogo.
La imagen y especificación orientan la revisión; la referencia no se adopta sola.
Capítulo 1 consulta el mismo catálogo y conserva las mismas unidades.

Corrección del filtro (13/11): cambiar el tipo ya no agrega toda la categoría
de la referencia anterior. Apartamento consulta Residenciales + Edificios;
Lote no propone tipologías del suelo desde este catálogo constructivo.
La selección previa fuera del filtro queda como única excepción rotulada
«Anterior · revisar tipo», con aviso; no se borra ni se sustituye automáticamente.
El conteo muestra coincidencias reales, excluyendo la excepción conservada.
Cambiar el tipo limpia la búsqueda y desactiva la consulta del catálogo completo.
«Usar tipo general» consulta el tipo del expediente sin copiarlo a la unidad.
Parqueadero usa el mismo filtro de garaje/parqueo. Buscar texto no cambia el tipo.

Validación de la corrección: PHP1680, JS285, HTTP18, lint/build/check:size
81.912 bytes gzip (límite 81.920). Navegador con PHP/BD locales: Oficina →
Apartamento → Lote, conservación y borrado explícito de selección, recarga,
tipo general, catálogo completo y móvil390; sin errores JS. No altera esquema
ni guardado: se conservan las mismas columnas y validaciones de servidor.

## Persistencia y migraciones

- `202611130001_shared_lease_register.php`: `appraisals.lease_register` JSON
  almacenado en LONGTEXT y `lease_version`. Clave de contrato estable y unidades
  por ID; conserva relaciones históricas con unidades desactivadas.
- `202611130002_unit_occupation.php`: `appraisal_units.occupation_details` y
  `occupation_version`. Ninguna respuesta previa se borra ni migra por inferencia.
- Edición de descripción/tipología usa ahora CAS sobre `appraisals.version`;
  una pestaña vieja recibe 409. Los contratos y la ocupación tienen versiones
  propias. El antecedente económico comparte versión con el capítulo 1.
- Migraciones reintentables mediante Schema::addColumn, aplicadas por `migrate`
  o AUTO_MIGRATE en acceso autenticado. No requieren SQL manual.
- Propietario, sesión activa y CSRF en servidor. El contrato exige recorrido
  Renta activo y unidad actual entre sus relaciones. Guardado parcial conserva
  campos omitidos. Fechas válidas y fin no anterior al inicio; dinero decimal
  no negativo, sin convertir vacíos en cero ni calcular duración del contrato.
- Autoguardado de 800 ms, recuperación al abrir, 422 por campo, 409 sin sobrescribir,
  reintento manual y aviso de desconexión. Sin localStorage; no cerrar una página
  con borradores sin conexión. Navegación fetch y fallback HTML conservados.

No hay eliminación de contratos ni agregación de ingresos. Informes y cálculos
existentes no se conectan todavía al registro nuevo: esta entrega prepara datos.

## Validación

Lint PHP, `php tests/run.php`, `npm test`, `npm run build`, `npm run check:size`.
Resultado: 1677 comprobaciones PHP, 284 JavaScript y 79,9 KB gzip iniciales.
BD: `GA_TEST_PORT=3384 php tests/database.php`, solo ga_test_app y ga_test_auth
en instancia MariaDB desechable: 266 comprobaciones, incluida migración repetida.
HTTP: `GA_TEST_HTTP=true GA_TEST_PORT=3384 php tests/lease-http.php` contra PHP
local 8088 y las mismas bases: 18 comprobaciones; login, CSRF, inactivo, propietario, 422/409,
fallback, contrato compartido y conservación del canon. El endpoint conceptual
anterior conserva sus 14 comprobaciones en `tests/income-concept-http.php`.
Navegador local con PHP y BD reales: autoguardado, recarga, conflicto entre
pestañas, desconexión/reintento, tipología cruzada, catálogo completo,
ocupación por unidad y móvil 390 px sin desbordamiento ni errores JavaScript.
Alta de otro contrato con enlace estable tras navegar/recargar; corrección del
canon original desde Renta visible en capítulo 1, sin segundo campo editable.
Fixtures/capturas quedan en tests/.runtime, fuera de Git.

## Publicación

Commit/push `b7a7554` a origin/main. Tras el despliegue, app.js,
income-inputs.js y app.css públicos respondieron HTTP 200 con SHA-256 idéntico
al local. La pestaña autenticada mostraba la versión anterior; al recargar
después de actualizar, el aplicativo pidió iniciar sesión. Por ello no se
acredita revisión visual autenticada de la versión nueva ni ejecución de sus
migraciones en producción. El flujo completo y las migraciones se validaron
en local; AUTO_MIGRATE las aplica en el siguiente acceso autenticado si está
habilitado. No se modificaron respuestas ni muestras reales ni se pidió acceso
al hosting. Los servicios locales desechables de esta prueba se detuvieron.
