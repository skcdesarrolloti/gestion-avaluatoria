# Plan de investigación · Mercado / Renta

Capítulo 8 → Insumos → 4. Plan de investigación. Configuración propia de cada
unidad, parte y método; no ejecuta regresión, depuración ni adopta valores.

- Factores sugeridos por tipo; sujeto desde sus datos propios del numeral 3.
- Cada factor conserva uso, tipo, definición, categorías y justificación.
- Filtro de contexto, investigar, candidato al modelo o pendiente son decisiones
  de planificación: no alteran ni descartan las muestras.
- Disponibilidad por portal en anuncios guardados. El catálogo observado por portal
  sigue en la pestaña 3; no se promete extracción completa de todos los portales.
- Inmuebles según agrupación confirmada por el analista. Diferencias entre fuentes,
  relecturas y formatos incompatibles requieren conciliación. Un dato complementario
  puede dar disponibilidad preliminar; no se convierte en valor adoptado.
- Vacío ≠ cero; intervalos ≠ edad exacta; piso alto ≠ número de piso.
- Área genérica PH no sustituye área privada construida. Régimen, tipo y operación
  discordantes/desconocidos quedan pendientes del contexto; no cuentan como listos.
- Cantidad conjunta de inmuebles con todos los factores candidatos legibles.
  Se muestran variación y pendientes del sujeto, definición y justificación.
- Referencia configurable: 10 inmuebles por factor candidato (máximo cuatro), no
  requisito normativo ni suficiencia estadística. Categorías con k clases prevén
  k−1 coeficientes; el conteo de coeficientes se conserva para revisión posterior.
  No introduce ponderaciones económicas del analista ni trata códigos como distancias.

Datos sólo consultados: no modifica anuncios, precios, áreas, fotos, selección,
coordenadas, Excel ni identidad. Coordenadas y depuración PH quedan en Análisis.
El precio comparable por m² y la calidad de la futura regresión requieren verificación
posterior: la disponibilidad de factores no garantiza un modelo válido.

Persistencia aditiva en methodology_workflow[component].research_plan, con fecha UTC
del servidor, propietario, CSRF y methodology_version. No requiere migración.
Autoguardado 800 ms y confirmación del servidor; versión obsoleta devuelve 409.
Actualizar consulta usa la navegación existente después de guardar la captura.

Límites: altura/acceso/servicio/niveles requieren un dato estructurado explícito en
la captura; no se deducen de texto ambiguo. Ascensores en la ficha del sujeto
pueden estar calificados por nivel; esa clase no se transforma en presencia sí/no.
La identidad no confirmada entre portales mantiene inmuebles potenciales separados.
Validación estadística, corroboración y control normativo corresponden a M4.
## 2026-10-04 · Cuadro por inmueble y portal
En Plan de investigación: selector de un inmueble vinculado y tabla con todos los
factores aplicables en filas (o sólo candidatos y área mediante checkbox). Columnas:
factor, sujeto desde su numeral 3, cada anuncio
por portal y validación entre fuentes. El sujeto es referencia, no participa en la
comparación de coincidencias entre anuncios. Área compatible y publicada siempre visibles,
con unidad m² y base original; no hay equivalencia automática total/privada/construida.
Verde sólo si dos o más anuncios tienen datos legibles iguales, sin pendientes de formato
ni relectura. Amarillo ante diferencia o revisión; gris para faltantes/una fuente.
No se comparan propiedades distintas ni se sustituyen datos. Dos avisos del mismo portal
conservan columnas separadas, código y enlace. El cuadro no genera datos desde la referencia.
El conteo conjunto exige área positiva compatible independientemente de su selección como
predictor; el área obligatoria no agrega un coeficiente si no se elige para el modelo.
El ratio de planificación sigue orientativo, no requisito normativo ni regresión.

## 2026-10-04 · Sujeto, catálogo completo y cuatro candidatos
Planta eléctrica y destinación/uso observado amplían el catálogo según tipo.
Se conservan campos explícitos opcionales de captura para estos factores y para
niveles, servicio, altura y acceso en el JSON existente; no se promete extracción
automática completa desde todos los portales. Datos ausentes permanecen pendientes.
Investigar no consume cupos: sólo Candidato al modelo cuenta para el máximo cuatro.
Cliente deshabilita un quinto candidato y servidor rechaza más de cuatro sin borrar
configuraciones anteriores. Meta = factores candidatos × referencia (por defecto 10).
El conteo conjunto considera inmuebles distintos, factores legibles y área compatible;
duplicados vinculados no aumentan la muestra. La clasificación queda en un bloque
cerrado para dar prioridad al resumen, sin retirar definiciones ni justificaciones.

## 2026-10-04 · Dato original y código de investigación
Catálogos iniciales por 12 tipos (incluye oficina); no garantizan publicación de todos
los factores. Tipo ordinal añadido: niveles por líneas en orden ascendente, códigos
0, 1, 2... compartidos por sujeto y anuncios. Numérica conserva medida, binaria No=0
Sí=1, nominal conserva clases sin imponer jerarquía. Orden no demuestra distancias
iguales ni efecto en el precio. Regresión y correlación permanecen en Análisis futuro.
Cada celda separa dato original de código. Planta: No=0, Parcial=1, Total=2; Sí sin
cobertura queda pendiente. La ficha del sujeto permite Total sin alterar Sí anterior.
Planes guardados conservan su tipo y categorías: el analista debe seleccionar Ordinal
y definir sus niveles para cambiar una clasificación anterior. No se migra en silencio.
Checkbox muestra sólo candidatos y área (inicial si hay candidatos), o todos los factores.
Máximo cuatro candidatos; investigación ilimitada dentro del catálogo. Sin DDL.
Pruebas: 1005 PHP, 144 JS, 176 BD en instancia desechable 3367, build 70,2 KB gzip.
Navegador local: oficina, dato Parcial sujeto=1, portales No=0 Parcial=1 Total=2,
guardado confirmado y recarga. Vista estrecha con scroll contenido, sin errores.

## 2026-10-04 · Filtros y obtención manual
Destinación residencial/comercial/industrial es filtro, fuera del cuadro de atributos,
calificaciones, coeficientes y meta. Servidor rechaza su uso como model/investigate;
configuración anterior incompatible se conserva y pide corregir, sin borrado automático.
Filtros documentan contexto: no descartan anuncios automáticamente. Cada atributo guarda
collection=portal/manual/mixed para planificar su obtención. Ausencia en portales no
excluye un atributo: investigación manual comparable por comparable con dato, fuente,
fecha y soporte en captura existente. Elegir manual no inventa ni verifica valores.
No añade factores personalizados fuera del catálogo ni un lector automático nuevo.
JSON aditivo, sin DDL. Vista sólo candidatos conserva consulta completa. Pruebas:
1008 PHP, 145 JS, build 70,3 KB gzip. Navegador local con guardado y recarga de Vista
manual y Destinación filtro; pantalla estrecha sin desbordamiento ni consola con errores.

## 2026-10-04 · Catálogo permanente de factores
Configuración separa Métodos y alcances / Catálogo de factores. Consulta central
por los doce tipos del catálogo existente, sin densificar los formularios de métodos.
Muestra atributos, definición, escala y referencias de fichas públicas de cada portal
y tipo. Referenciado significa mención documentada, no dato garantizado en cada anuncio
ni extracción automática. Sin evidencia específica, propone investigación manual.
Destinación sigue como filtro, fuera del catálogo de atributos.
En Insumos se decide uso, obtención y motivo; definición/tipo/categorías son de consulta.
El servidor rechaza cambios de clasificación desde el expediente. Los planes anteriores
conservan su clasificación; no se sustituyen escalas ni recodifican datos en silencio.
Vista mantiene Interior/Exterior/Panorámica/Esquinera/Sin vista relevante como clases
nominales; no inventa una jerarquía ordinal. Planta No/Parcial/Total mantiene 0/1/2.
El catálogo es código común de la aplicación, sin editor administrativo ni DDL nuevo.
Una futura revisión general de escalas requiere versionado y tratamiento de históricos.
Pruebas: 1014 PHP, 145 JS, 176 BD desechable nueva (3369), lint/build, 70,3KB gzip.
Navegador local: métodos separados, oficina/apartamento/depósito, guardado confirmado
y recarga; pantalla estrecha con desplazamiento limitado a la tabla, sin errores de consola.

## 2026-10-04 · Jerarquías y calificaciones de investigación
El catálogo ya permite guardar clases nominales o niveles ordinales junto al factor;
primera línea=0, siguientes=1,2… según orden definido por el analista. Cantidades
y binarias conservan medida y No=0/Sí=1. No se impone orden económico a clases
nominales ni se inventa una jerarquía de Vista. Catálogo por propietario: se reutiliza
en sus avalúos y tipos que incluyan ese factor, sin modificar escalas de otro usuario.
Migración aditiva 202610040001_research_factor_scales.php, versión optimista por escala.
Los planes guardados conservan definición/categorías; botón explícito para adoptar
catálogo actual y volver a calificar. No recodificación silenciosa de históricos.
Área/terreno/construida son bases de cálculo y quedan fuera de candidatos/meta de
muestras; una antigua selección se muestra como base, conservando datos almacenados.
Insumos presenta escala y portales referenciados junto al factor; conteos reales por
portal continúan disponibles. Ausencia documental no prueba que el portal no publique
el dato. Investigación manual sigue disponible.
Cuadro por inmueble: calificaciones del sujeto y grupo vinculado, etiqueta/medida,
soporte y código derivado separados de valores publicados. Control plegable junto a
cada factor, primera columna fija durante desplazamiento. Se guarda en research_plan
assessments, sin nuevas columnas de comparables. Fuente cambiada, falta de soporte o
escala distinta deja la calificación pendiente y fuera del conteo conjunto; mantiene
las diferencias entre anuncios. No ejecuta correlación/regresión ni cambia Análisis.
Pruebas: 1020 PHP, 149 JS, 181 BD contra nueva instancia desechable 3371, lint/build
y tamaño 71,0KB gzip. Navegador local: editor Vista guarda/recarga; Planta Parcial
código1 guarda/recarga con soporte, fuentes No/Parcial/Total intactas; pantalla
estrecha sin desbordamiento de página (tabla con scroll propio), consola sin errores.

## 2026-10-04 · Vista: separar atributos antes de modelar
Orientación de la vista usa clases fijas Sin vista relevante / Interior / Exterior,
sin puntaje ni orden económico. Vista paisajística, amplitud panorámica y ubicación
esquinera son atributos independientes de presencia No=0/Sí=1; desconocido queda
pendiente. No se traduce una antigua etiqueta Esquinera a Paisajística, ni Panorámica
a Exterior. Se conserva el registro original de capítulos y anuncios.
Las escalas anteriores que mezclaban dimensiones, incluso nominales, se conservan
pero quedan advertidas y fuera de códigos/conteos listos. Restaurar el catálogo
requiere acción explícita, guardado confirmado y adopción en cada plan; las
calificaciones previas se revisan, no se recodifican automáticamente.
Para regresión futura, clases nominales requieren contrastes/indicadoras; usar
0,1,2… como una sola variable continua impondría intervalos iguales. Los códigos
binarios representan presencia, no primas de valor garantizadas. Esta etapa no
ajusta modelos. Referencia: https://www.statsmodels.org/stable/contrasts.html.
Evidencia pública de dimensiones distintas: FincaRaíz 193606578 describe una unidad
como vista exterior y esquinera; sus listados de casas con vista panorámica de
Bucaramanga describen panorama urbano. Panorama no acredita paisaje natural.
Pruebas: 1046 PHP, 152 JS, 181 BD nueva instancia desechable3373, build y 71,3KB gzip.

## 2026-10-04 · Simplificación acordada de Vista (vigente)
Por indicación del usuario, Vista vuelve a un único factor con cuatro clases fijas:
Sin vista / Interior / Exterior: calles y avenidas / Exterior: paisajística.
La selección describe la vista predominante desde el espacio principal. No asigna
una prima de precio ni intervalos numéricos iguales; el tratamiento del modelo
queda para Análisis. Esquinera sigue siendo posición física, fuera de Vista.
Paisaje y panorama dejan de proponerse como factores separados en catálogos y
planes nuevos. Si ya pertenecían a un plan guardado, permanecen visibles y
admisibles dentro de su tipo para conservar calificaciones y soportes. Sus claves
siguen reconocidas; no se borran registros. Escalas anteriores de Vista requieren
restauración y adopción explícita. Exterior genérico no se asigna automáticamente
a calles o paisaje. Falta de información no equivale a Sin vista.
Validación: 1047 PHP, 152 JS, 181 BD desechable3374, lint/build, 71,3KB gzip;
restauración del catálogo con confirmación y recarga en navegador local.
