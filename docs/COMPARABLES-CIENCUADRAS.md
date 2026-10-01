# Ciencuadras: validación de captura

Revisión del 30/09/2026. La navegación normal permite buscar oficinas en venta
con `https://www.ciencuadras.com/venta/oficina?v=Bocagrande`: primera página con
20 avisos de 29 resultados. La ciudad y barrio deben comprobarse en cada tarjeta;
el parámetro `v` es búsqueda textual, no un identificador geográfico.

La solicitud HTTP directa devolvió 403 «Acceso denegado». No se implementó ni
se anuncia lector automático por servidor. Para oficinas en venta se habilitó
copiar una página de resultados completa: aplicar barrio con Enter, hacer clic
en el título y Ctrl+A/Ctrl+C; pegar con Ctrl+V en el campo del aplicativo.
El pegado HTML se procesa en una plantilla inerte, sin insertar markup ni cargar
imágenes externas. Solo toma tarjetas con precio de compra, área, oficina, ciudad
del expediente y enlace HTTPS individual de Ciencuadras. Máximo 2 MB/60 tarjetas.
Los destacados de otras ciudades se descartan. El barrio publicado se conserva
para revisión, sin sustituirlo por el del sujeto.

Pegar prepara una vista previa sin guardar. Seleccionar todos permite desmarcar
individualmente; solo Agregar incorpora. Los enlaces ya registrados se omiten.
Las coincidencias posibles se señalan y pueden incorporarse como por verificar,
dejando nota de revisión pendiente, sin declarar que sean inmuebles distintos.
No se incluye todavía un nuevo filtro de duplicados de toda la matriz.

También admite un bloque tabulado con encabezados fuente, enlace, precio, area,
barrio y operacion. Pegar enlaces solos muestra ayuda y no crea muestras vacías.
Cada pegado sustituye la vista previa, no las filas ya incorporadas. Las fotos
se adjuntan desde la matriz. Otros tipos/operaciones conservan captura manual.

Se corrigió la unidad al pegar una operación explícita: un aviso de arriendo o
venta con operación Venta y precio de compra queda en precio total, no canon.
PH y datos ausentes siguen por verificar. Se mantienen las comprobaciones de
duplicados y el límite de 60; no se modifica automáticamente la matriz existente.

Validación: copia real desde navegador con 20 tarjetas; pegado en vista local,
selección de 20/desmarcado de una/incorporación de 19, contador 12→31 y bloqueo
posterior de enlaces incorporados. Pruebas de URLs, ciudad, operación, decimales,
ausencia de guardado al pegar/seleccionar y revisión diferida; suite PHP/JS, lint,
build y presupuesto de assets. Verificación visual escritorio/móvil. Sin migraciones;
no se modificó la matriz de producción ni se probó persistencia nueva en esta entrega.
