# Captura por barrio de Metrocuadrado — 30/09/2026

En Capturar, Cambiar de fuente → Metrocuadrado, el panel usa tipo, operación,
ciudad, PH y barrio del expediente. Para oficinas en venta en Cartagena permite
consultar el barrio del catálogo, seleccionar sugeridos y agregar a la misma matriz.
Las coincidencias incluyen toda la matriz, también las de FincaRaíz. Buscar o
seleccionar no guarda muestras; Agregar inicia el guardado versionado existente.

La lectura usa el HTML público de la búsqueda de Metrocuadrado, sin claves API,
cookies, navegador automatizado en servidor ni ejecución de scripts externos.
Extrae JSON de los segmentos `self.__next_f.push` e `initialResults`. Si cambia
el formato, falla explícitamente y conserva el pegado de texto como alternativa.
Confirma la URL canónica del barrio. URL construida en servidor para un host y
ruta restringidos; DNS público fijado, HTTPS validado, sin redirecciones/proxy,
18 segundos y máximo 4 MB. Ruta POST existente, autenticación/propiedad, CSRF y
límite de solicitudes conservados. El cliente solo envía portal, barrio de catálogo
y página; no puede enviar una URL arbitraria al lector.

Trae fuente, enlace, código, operación, tipo, precio de venta, área publicada,
administración, barrio, proyecto/contacto y baños/parqueaderos si están disponibles.
No usa `geopoints` como ubicación del inmueble: son puntos de interés cercanos.
No infiere PH de administración. No adjunta fotos; continúa el pegado por muestra.
Los valores son ofertas publicadas por verificar, no transacciones acreditadas.

Alcance inicial: hasta 50 avisos del bloque inicial de cada barrio; no implementa
paginación adicional de Metrocuadrado. Si el total del portal excede el bloque,
avisa que hay restantes para consultar en el portal. No promete que cada barrio
del catálogo tenga inventario o un nombre idéntico en Metrocuadrado. Otras ciudades,
tipologías y operaciones conservan búsqueda externa y pegado manual.

Validación: petición PHP real para Bocagrande recuperó 19 avisos válidos del bloque
de 20 anunciado. La cifra es de la prueba, no una constante del aplicativo. Pruebas
con datos sintéticos verifican parseo, límites, barrio canónico, destinos externos,
ciudad/tipo/operación, duplicados y ausencia de inferencias de PH/coordenadas.
368 comprobaciones PHP, 81 pruebas JS, lint de 498 PHP, build y 53,2 KB gzip.
Navegador local con respuesta pública capturada: 19 tarjetas, selección e incorporación,
conteos, bloqueo de duplicados y móvil 390 px; sin editar expedientes de producción.

Publicar PHP, vistas y assets juntos. No requiere migración ni configuración nueva.
