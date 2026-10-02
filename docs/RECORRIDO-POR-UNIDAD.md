# Recorrido por unidad — 02/10/2026

Nombres de las pestañas: se conserva el nombre personalizado guardado en el campo
«Nombre del componente» del capítulo 1. Si sigue vacío o genérico (Unidad N / Anexo N),
el capítulo 8 lo identifica con el tipo registrado y su número, por ejemplo
«Cerramiento · Anexo 1». El tipo general solo se usa para una única unidad principal;
nunca se hereda a anexos ni a múltiples unidades sin clasificación propia.
Esta presentación se comparte con el encabezado y el recorrido del componente;
no renombra filas, cambia identificadores ni requiere migración. El menú general
conserva integración, texto del numeral y acceso a las herramientas del expediente.
Validación de este ajuste: 543 comprobaciones PHP, 113 JS, lint, build y tamaño.

Cada unidad o anexo tiene una pestaña principal. Solo se renderiza su ficha a todo
el ancho; su identidad, orden y definición se conservan desde capítulos 1 y 3.
El método guardado prevalece sobre el parámetro de un enlace antiguo. Consultar
academia no guarda ni cambia la decisión. La integración sigue mostrando todos.

Dentro de la unidad: academia y revisión, método y alcance, insumos y comparables,
análisis y entregable. Se conservan M1–M5/C1–C5/R1–R5/Re1–Re5 como referencias
locales a esa unidad. Ya no se muestran filas paralelas para cambiar de método.
Matriz técnica, banco sin asignar y texto del capítulo siguen accesibles.

Academia: 10 apartados en fila, contador y controles anterior/siguiente. Recupera
las tarjetas con resumen, resaltados y artículo completo desplegable, junto con
Qué comprende, Insumos mínimos, Desarrollo técnico y Cierre en informe. Cada método
usa su guía existente. Las características del sujeto quedan plegables.

Lecturas añadidas: 1–14, 35, 37–42 y 57–61 (36 ya estaba). Se conservan 15–34.
Fuente: reproducción del Diario Oficial publicada por Camacol, con página de origen.
Fórmulas de 37 y 38 verificadas visualmente. El encabezado de ámbito de aplicación
repite artículo 1 en la reproducción; se advierte esa inconsistencia al consultar 2.
Los casos especiales no se declaran automáticamente aplicables. Otros procedimientos
específicos, como plusvalía y rural, conservan la referencia al documento fuente;
esta entrega no incorpora su cálculo ni afirma validación normativa automática.

Mercado usa la operación del expediente; Renta prepara consulta de arriendo para
la unidad seleccionada. Texto copiable y enlaces/filtros disponibles en portales;
no se promete que un portal ejecute un prompt ni lectura masiva de toda tipología.
Se preservan lectores, Ctrl+A/C/V, conteos, coincidencias, carga de no repetidos,
tabla única, mapas, fotos, guardado y asignación por componente.
La búsqueda por barrio soportada valida ahora también el componente en servidor.

Costo/Residual: insumos académicos pertinentes. Captura técnica y motores numéricos
pendientes, indicados en pantalla. Renta: captura habilitada, cálculo pendiente.
No se altera esquema, no se borran datos y no se requieren nuevas migraciones.
Si no estaba aplicada la versión anterior, sigue siendo necesaria la migración
202610020001_unit_method_structure.php (AUTO_MIGRATE o consola habitual).

Validación: 540 comprobaciones PHP, 113 JS, 97 BD y 52 HTTP en bases desechables
locales ga_test_app/ga_test_auth (puerto 3350); lint, build y 58.1 KB gzip.
Navegador: navegación fetch entre unidades/métodos, textos y artículos abribles,
consulta de arriendos y vista estrecha sin desbordamiento global (ancho CSS efectivo
562 px). No se verificó despliegue ni datos de producción. La primera captura móvil
agotó el tiempo; la segunda captura de viewport confirmó el contenido visible.
