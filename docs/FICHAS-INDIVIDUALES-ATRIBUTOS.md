# Completar los anuncios desde su ficha individual

Después de pegar un listado, el lector recorre cada enlace en orden antes de
ofrecer su incorporación. Muestra progreso, atributos publicados y estado por
aviso. La búsqueda automática por barrio utiliza el mismo complemento. No guarda
ni confirma muestras hasta que el analista pulse Agregar/Complementar; conserva
la confirmación de guardado del servidor.

FincaRaíz: JSON-LD más ficha técnica e instalaciones de __NEXT_DATA__, únicamente
si ID y enlace corresponden al anuncio. Captura piso, administración, áreas con
sus etiquetas, antigüedad textual y todas las instalaciones publicadas. Un
intervalo de edad no se convierte en una edad exacta. Área privada no equivale a
privada construida. Un atributo de «Exterior» no se atribuye automáticamente a
la unidad privada o a PH. Estrato publicado en oficina queda como dato original,
no como factor del catálogo de oficinas.

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
