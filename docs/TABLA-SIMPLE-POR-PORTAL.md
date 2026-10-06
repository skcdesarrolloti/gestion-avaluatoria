# Tabla simple por portal · 2026-10-06

En Insumos, revisión y tabla muestran cada anuncio del portal seleccionado en una
fila. Las columnas nacen de datos recogidos, sin catálogo del sujeto, códigos de
regresión ni validación económica. Confirmados usa el mismo formato, filtrando
los anuncios confirmados del portal. No fusiona datos de publicaciones vinculadas.

Precio, área, baños, parqueaderos y anunciante se muestran una vez; las etiquetas
publicadas equivalentes tienen precedencia en la presentación, conservando todas
las capturas originales. Área construida y área privada mantienen sus etiquetas.
Solo aparecen columnas con al menos un valor; cero explícito se conserva y una
celda sin información dice No publicado. Los atributos adicionales conservan el
texto publicado. La revisión individual y los controles de participación quedan
plegados debajo, con enlaces, texto original, complemento y soportes disponibles.

No cambia persistencia, lectores, muestras ni selección para Análisis. Se mantiene
la matriz compartida y el guardado confirmado por servidor. No requiere migración.

Validación: 1446 PHP, 177 JavaScript, build y 76,7 KB gzip. Vista aislada con 19
muestras ficticias, cambio de portal, columna ausente, celdas sin dato, cero,
anuncios vinculados separados y sin errores de consola. Pantalla estrecha sin
ancho de página mayor al viewport; tabla con desplazamiento propio.
