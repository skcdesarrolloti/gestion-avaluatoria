# Mercado Libre: búsqueda y captura por lote

Se corrige el dominio de búsqueda: los avisos usan `inmueble.mercadolibre.com.co`
y los resultados `listado.mercadolibre.com.co`. La búsqueda anterior restringía
Google a `inmuebles.mercadolibre.com.co`, sin resultados para el expediente.

Para oficina, venta, Cartagena y Bocagrande se usa la ruta verificada:
https://listado.mercadolibre.com.co/inmuebles/oficinas/venta/bolivar/cartagena-de-indias/bocagrande/
Otras combinaciones mantienen Google sobre `mercadolibre.com.co`, con comprobación
manual de filtros. No se inventan rutas para barrios no comprobados.

La pestaña explica: abrir resultados del portal, copiar página completa con
Ctrl+A/Ctrl+C, pegar y agregar sugeridos sin coincidencias. Pegar prepara; no guarda
ni incorpora automáticamente. Conserva el flujo común y la revisión en la matriz.

El lector inicial acepta tarjetas `poly-card` con operación explícita Oficina en
venta, URL HTTPS de inmueble MCO, ciudad del expediente, precio en pesos y una sola
área publicada. Un título que menciona venta y arriendo usa la operación explícita
de la tarjeta. Omite arriendos, otras ciudades/tipos y datos ambiguos/incompletos.
Conserva barrio, URL sin seguimiento, precio, área y su clase publicada en notas.
PH queda por verificar. No importa fotos ni supone área privada a partir de cubierta.
HTML externo se lee separado e inerte; nunca se muestra ni ejecuta.

Validación del 01/10/2026: navegación real mostró 14 oficinas en el barrio, tres
en venta tras aplicar Operación. Copia real preparada en interfaz local: tres
avisos; incorporar pasa de 12 filas ficticias a 15 y los deja ya registrados.
Interfaz revisada en escritorio y en contenedor de 390 px, con pegado reconocido.
No se agregaron avisos a la matriz de producción. El límite existente sigue en 60.
Pruebas: 99 JS, 370 PHP, lint de archivos PHP afectados, build y 55,8 KB gzip/80 KB.
Sin cambios de esquema o persistencia; no corresponde ejecutar migraciones.
Publicar código y assets juntos; subir Git no despliega el hosting.
