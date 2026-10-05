# Completar los anuncios desde su ficha individual

La investigación se realiza en dos pasos, tanto al pegar un listado como al
buscar automáticamente por barrio:

1. Detectar los avisos del listado, mostrar los datos publicados y revisar
   coincidencias. Agregar los sugeridos sin coincidencias guarda primero los
   avisos nuevos y espera la confirmación del servidor.
2. Abrir únicamente las fichas de los avisos efectivamente incorporados. Cada
   complemento se guarda y confirma antes de solicitar la siguiente ficha.

No se consultan automáticamente fichas descartadas, ya registradas o que no
cupieron en la matriz. Las posibles coincidencias requieren revisión, no prueban
identidad. La opción manual de complementar existentes conserva la posibilidad
de releer avisos registrados elegidos expresamente por el analista.

Si falla el primer guardado, no comienza la lectura individual. Si falla un
guardado posterior, se detiene el complemento; los avisos iniciales ya guardados
se conservan. Esperar el estado final antes de cambiar de pantalla. Este paso
prepara información y no selecciona muestras o factores para análisis.

FincaRaíz: JSON-LD más ficha técnica e instalaciones de __NEXT_DATA__, únicamente
si ID y enlace corresponden al anuncio. Captura piso, administración, áreas con
sus etiquetas, antigüedad textual y todas las instalaciones publicadas. Un
intervalo de edad no se convierte en una edad exacta. Área privada no equivale a
privada construida. Un atributo de «Exterior» no se atribuye automáticamente a
la unidad privada o a PH. Estrato publicado en oficina queda como dato original,
no como factor del catálogo de oficinas.

FincaRaíz también conserva el nombre y tipo del anunciante publicado en la ficha
identificada: contact_name y atributos originales Anunciante/Tipo de anunciante.
El campo owner del portal identifica al publicador; no demuestra propiedad legal
del inmueble. Teléfonos enmascarados no se guardan como números completos. El portal
sigue registrado por separado como fuente. Si no publica anunciante, queda pendiente.
Validación adicional: 1446 PHP, 175 JS, lint/build y 76,3 KB gzip; dos fichas
públicas conservan sus atributos y agregan los dos datos del anunciante.

Otros portales e inmobiliarias del catálogo: lectura de datos estructurados de
una única ficha identificada (descripción, atributos, instalaciones, medidas y
precio COP). Cuando la fuente no entrega una entidad inequívoca, bloquea acceso,
redirige o requiere ejecución de scripts, se conserva el listado y marca ficha
pendiente para completar desde el enlace con texto copiado. No se afirma que se
leyeron atributos visibles que ese lector no puede reconocer. El estado señala
el alcance de la lectura. No obtiene fotos, PDFs ni información no publicada.

Seguridad: catálogo de dominios existente, HTTPS, sin credenciales ni puertos,
DNS IPv4 público y conexión fijada a esa IP, TLS verificado, sin redirecciones,
timeout y máximo 2 MB. Conserva autorización, CSRF y límite de lectura 60/15 min;
429/sesión vencida detienen solicitudes restantes y señalan pendientes. No usa
navegación de recomendaciones ni consultas internas indiscriminadas de la página.

La misma URL completa vacíos, conserva texto adicional y registra contradicciones
sin sobrescribir precio, áreas, clases o decisiones del analista. Las muestras
anteriores no se eliminan ni duplican. Atributos siguen en capture_details con
los límites existentes de 80 pares y texto original de hasta 16.000 caracteres.

Validación 2026-10-05: 1437 PHP, 172 JS, 212 BD desechable (3397), lint, build y
76,2 KB gzip. Dos fichas públicas FincaRaíz 193907764/193978243: 39 y 32 etiquetas
respectivamente, más piso, administración, medidas y descripción publicada.
QA en avalúo ficticio local: siete avisos conservados, dos complementados sin
crear filas nuevas, guardado confirmado y recuperación en la tabla tras recarga.
Captura y tabla sin desbordamiento estrecho; consola sin errores. No escribe en
producción ni agrega esquema. Prueba visual: Fichas-individuales-atributos.png.

Validación del flujo en dos pasos: 1437 PHP, 175 JS y 212 BD desechable (3398);
build y 76,3 KB gzip. QA local: tres avisos pegados, dos existentes y uno nuevo;
únicamente se leyó la ficha nueva después del primer guardado. Sus atributos
se recuperaron tras recargar, junto con los siete anuncios previos. Vista
estrecha sin desbordamiento y consola sin errores. Prueba visual:
Investigacion-dos-pasos.png.
