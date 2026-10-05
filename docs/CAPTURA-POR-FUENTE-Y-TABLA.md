# Captura por portal e inmobiliaria · 2026-10-05

Recorrido: Buscar por portal → Revisar por portal → Confirmados y factores →
Tabla de inmuebles. Cada fuente tiene botón para abrirla en otra pestaña,
instrucción Ctrl+A/C en el sitio y Ctrl+V aquí, conteos y tarjetas visibles.
Verde significa sin coincidencias detectadas, amarillo requiere revisar y gris
ya incorporado. El lote sugerido conserva la selección sin coincidencias existente.
No confirma identidad ni comparabilidad; la confirmación para investigación
sigue separada de la selección posterior para Análisis.

Las once inmobiliarias del catálogo de Cartagena conservan sus enlaces y tienen
lector de contenido copiado, además de los cinco portales. Se comprobaron los
catálogos públicos de [Araújo & Segovia](https://www.araujoysegovia.com/),
[Asesorar](https://asesorarinmobiliaria.com/),
[SuCasa](https://sucasainmobiliaria.com.co/) e
[Inverfin](https://inmobiliariainverfin.com/). Esto no constituye un ranking de
participación ni una certificación de funcionamiento de todos los sitios.

El lector general busca enlaces del dominio de la fuente y sus subdominios HTTP/S,
delimitados por tarjetas con precio y área explícitos. Si un contenedor mezcla
enlaces de distintos avisos, no reúne sus datos como un inmueble. Conserva el
texto completo dentro de los límites existentes y las etiquetas originales.
No inventa valores de los iconos sin etiqueta, derechos, PH ni coordenadas.
Si no reconoce el listado, admite enlace y texto de cada ficha separados por una
línea vacía, o captura manual. Pegar no escribe en la matriz. No ejecuta HTML
copiado ni consulta el sitio en segundo plano.

Ciencuadras, Properati y Mercado Libre conservan sus lectores específicos para
oficinas en venta. FincaRaíz y Metrocuadrado mantienen su búsqueda automática
por barrio y lector individual en una sección opcional. Los enlaces generales
abren el portal; la alternativa de Google permanece dentro de los filtros.

Cada tarjeta presenta los campos reconocidos, atributos originales y texto leído.
La nueva tabla usa una fila por grupo confirmado explícitamente como inmueble,
incluye la referencia del sujeto y todos los factores del catálogo y etiquetas
publicadas. Mantiene los valores de cada fuente, incluso contradictorios, sin
promediarlos ni reemplazarlos. Los posibles duplicados siguen separados hasta
vincularlos manualmente. No limita factores ni ejecuta correlación o regresión.

Sin migración ni borrado de muestras: reutiliza capture_details, autoguardado y
CAS. La tabla usa roles semánticos div para no alterar el tbody de la matriz
que utilizan los lectores e importadores existentes.

Validación: 1415 PHP, 165 JavaScript, 212 BD desechable en puerto 3393, lint,
build y 74,3 KB gzip. Navegador: pegado ficticio en Asesorar sin incorporación,
tarjeta con atributos, tabla de dos inmuebles/cuatro anuncios, referencia del
sujeto, cambio de fuente y comprobación de ancho móvil sin desbordar documento.
No se importaron anuncios reales ni se modificaron datos de producción.
