# Entrega al responsable de la implementación

Composición simplificada (vigente): cada ficha permite definir unidad, método de
valoración y referencia IGAC. El selector Mercado/Costo/Renta/Residual se guarda
ahora en el numeral 1, sobre el mismo methodology_workflow del capítulo 8.
Sustituye el recuadro y enlace a M2 descritos en la entrega anterior. Se mantienen
principales/anexos separados, tratamiento y cobertura; notas existentes conservadas.
Guardado atómico con versión de metodología: un cambio concurrente devuelve 409
sin guardar parcialmente; editar otros campos no sobrescribe el método de otra pestaña.
Validación: PHP 555, JS 114, BD local desechable puerto 3356: 119; lint, build y
58,2 KB gzip. Navegador: seleccionar Mercado, autoguardar y recargar; persiste junto
al Costo independiente del anexo. Revisión visual escritorio y ancho reducido sin
desbordamiento. Sin migración nueva; hosting pendiente de actualizar por el usuario.

Retiro puntual solicitado: sin bloque «Volver al capítulo 8» en composición del
numeral 1 ni botón «Ver capítulo 1 · Composición» en el 8. Se mantienen selección
del método, datos y navegación general. Fieldsets con x-cloak evitan mostrar
unidades inactivas antes de inicializar Alpine. No se borró Unidad 2: la captura
no permite determinar la cantidad registrada y la pestaña del hosting no estaba
disponible. La exclusión por cantidad guardada sigue vigente.
Verificación: lint, PHP, JavaScript, build y límite de tamaño. Sin migración.

Composición: principales primero y bloque separado de anexos; contador distingue
ambos. Capítulos 3.2, 3.3 y 3.4 muestran el nombre registrado, no solo «Anexo N».
Cada ficha del numeral 1 muestra «Cómo se va a valorar», método guardado y enlace
al M2 del mismo ID de componente para elegir/revisar el método existente. No se
agrega otro selector ni almacenamiento de métodos en el capítulo 1. Se conserva
el tratamiento integrado/separado y la estructura como conceptos distintos.
Prueba local de 1 principal + 2 anexos: Oficina principal, Depósito y Garaje,
guardados repetidamente con IDs y nombres estables, sin duplicados. Navegador:
separación, enlace a M2 de Depósito, guardar Mercado y verlo al regresar al numeral 1.
PHP 555, JS 113, BD desechable puerto 3355: 114; lint, build y tamaño 58,1 KB.
Escritorio visual y viewport estrecho sin desbordamiento (562 px). Sin migración;
no se inspeccionaron los registros del hosting ni se confirmó duplicación allí.

Composición sin anexos: al indicar 0, la ficha del anexo queda oculta y sus campos
deshabilitados; guardado manual y automático descartan definiciones fuera de las
cantidades activas. Capítulo 8 también filtra colecciones anteriores por cantidad.
Los datos históricos no se borran: reactivar recupera identidad y contenido.
Validación: PHP 555, JS 113, BD desechable ga_test_app/ga_test_auth puerto 3354: 113,
lint, build y tamaño 58,1 KB. Navegador local: 1→0, autoguardado, recarga y capítulos
3.3/8 muestran solo Unidad 1. No se verificó el hosting. Sin migraciones nuevas.

Numeral 1: [Oficinas en el buscador IGAC](OFICINAS-IGAC.md). Lista conjunta de
Comerciales y Edificios, categoría original y selección conservada. Sin migraciones.

Corrección vigente: [responsable asignado y vigencia RAA](PERITO-ASIGNADO-VIGENCIA.md).
El capítulo 1 conserva visible y al guardar el perito ya asignado aunque deje de
ser elegible para nuevas asignaciones. Sin cambios de certificados ni migraciones.

Actualización vigente: [recorrido por unidad y conservación](RECORRIDO-POR-UNIDAD.md).
Una unidad visible, método registrado, academia íntegra e insumos propios.

Academia por unidad: cuatro subpestañas visibles en A del capítulo 8 y lectura contextual
para PH, NPH, terreno y mejoras. Capítulo 1 guarda «Estructura del método» por unidad
y anexo; capítulo 8 la consulta. Migración aditiva `202610020001_unit_method_structure.php`
mediante `php bin/console.php migrate` o `AUTO_MIGRATE=true` al acceder autenticado.
Los registros anteriores quedan por definir; no se asigna un método automáticamente.
Validación: PHP, JavaScript, compilación, tamaño y BD local desechable; persistencia,
propietario ajeno, formulario anterior y migración repetida. No desarrolla otros métodos.

Capítulo 8 por componente: [flujo Mercado y conservación](CAPITULO-8-COMPONENTES.md).
Aplicar migración `202610010008_methodology_workflow.php`. Muestras previas en
banco sin asignar; M1–M5 y asignación segregada. Costo se desarrolla después.

Objeto 1.5 visible y tabla documental 1.11: aplicar migración
`202610010006_assignment_document_table.php`; conserva datos anteriores.
Ver [expediente](EXPEDIENTE-SOLICITANTE.md).

[Expediente: solicitante, fecha y foto 1.4](EXPEDIENTE-SOLICITANTE.md): campos de
contacto, uso previsto de 1400 caracteres, explicación en 1.3.5 y carga de imagen
en 1.4. Aplicar migración `202610010005_assignment_contact_and_value_date_notes.php`.

Guías de los cuatro métodos: lectura completa de arts. 16–34, requisitos y
pendientes por método y revisión transversal de la Resolución 941. Ver
[revisión normativa](REVISION-RESOLUCION-941.md). Sin nuevas fórmulas ejecutables,
persistencia ni migraciones; requiere actualizar el código del hosting.

[Acceso del analista desde Perito responsable](ACCESO-ANALISTA.md): cuenta local
delegada, clave temporal con cambio inicial, nuevos avalúos visibles al titular y
perito responsable fijado. Aplicar migración `202610010004_create_analyst_access.php`.
WordPress sigue solo lectura. No hay cuentas nuevas en producción por esta entrega.

Fotos en Mapas: una ficha por vez y «Continuar con la siguiente muestra», con
confirmación de guardado y bloqueo ante foto pendiente. Galería de altura limitada.
Sin migración nueva; ver pruebas y limitación de subida en [Mapas](COMPARABLES-MAPAS-AREAS.md).

Comparables: [Mapas, evidencia y áreas PH/no PH en 8.3](COMPARABLES-MAPAS-AREAS.md).
Aplicar migración `202610010003_comparable_capture_details.php`. Conserva muestras;
análisis, desagregación monetaria y estadísticos siguen pendientes para 8.4.

Perito: [antecedentes y anexo judicial CGP](PERITO-JUDICIAL-CGP.md). Nueva pestaña
en Maestros, declaraciones por expediente Judicial, exportación TXT por apartado
y registro explícito de presentación que alimenta historial y conserva copia.
Aplicar migración `202610010002_create_judicial_expert_records.php`.

Comparables: [sin tope de 60 y conservación de datos](COMPARABLES-SIN-TOPE.md).
Aplicar migración nueva que amplía sample_index; no borrar ni reimportar muestras.

Resolución 941: [revisión completa y brechas de comparables](REVISION-RESOLUCION-941.md).
Lectura plegable de arts. 16–21; pendientes de negociación, áreas por régimen,
evidencia y memoria de cálculo. La captura no certifica cumplimiento normativo.

Mercado Libre: [búsqueda directa y captura por lote](COMPARABLES-MERCADOLIBRE.md),
con filtros verificados para oficinas en venta en Bocagrande y guía 1–2–3.

Properati: [captura por lote y red Proppit](COMPARABLES-PROPERATI.md), con guía
1–2–3 y selección sin coincidencias compartida con Ciencuadras.

Ciencuadras: [validación y límites de captura](COMPARABLES-CIENCUADRAS.md).

Metrocuadrado: [captura por barrio](COMPARABLES-METROCUADRADO.md), lectura inicial
de oficinas en venta en Cartagena, selección por lote y coincidencias compartidas.

Actualización: [matriz, PH y fotos de comparables](COMPARABLES-MATRIZ-FOTOS.md).
Incluye migración nueva, fotos privadas por muestra, envío compacto y control de
versión de la matriz; reemplaza los pendientes anteriores de transporte/concurrencia.

Actualización del 30/09/2026: [captura y revisión de comparables 8.3](COMPARABLES-83-REVISION.md).
Fichas paginadas, pendientes, búsqueda por fuente y pegado por lotes. Incluye límites
de la revisión NTS y pendientes del anexo y de concurrencia del guardado existente.

Actualización PH del 21/09/2026: [lectura documental](PH-LECTURA.md). El numeral 3.5
incorpora OCR local por página, referencias al documento, prellenado conservador y
versionado optimista. Aplicar la migración nueva y publicar los assets OCR locales.
Los campos no acreditados, las tablas ambiguas y el criterio valuatorio quedan al
analista; no se implementó interpretación jurídica mediante un modelo externo.

## Punto de partida

Proyecto: `C:\Workspace\desarrollo-skc\gestion-avaluatoria`.
Origen de consulta: `C:\Workspace\desarrollo-skc\inverskc`.
La base nueva no necesita cargar ningún archivo del proyecto original.
Contiene acceso y un ciclo real de crear, editar, autoguardar y recuperar fichas privadas.
Incluye biblioteca inicial de Normas Técnicas Sectoriales con categorías A, B y 1 a 13.
Incluye menú base de Marco Jurídico Nacional con bibliografía B1 a B13 para cargar
leyes, decretos, resoluciones y documentos derogados cuando el responsable los entregue.
Las leyes extensas se modelan como documento fuente más artículos o fragmentos
pertinentes, no como texto completo indiscriminado. Las IVS quedan en menú separado.
Las NIIF quedan en otro menú independiente para consultas de medición contable.
La Normatividad Urbana queda modelada como biblioteca y módulo del capítulo 5, iniciada con los cuadros de usos del Decreto 0977 de 2001, y como ficha por avalúo para MIDAS por predial, opción Uso del suelo, concepto, usos, determinantes y soportes.
Incluye catálogo de Tipologías Constructivas IGAC con imágenes, agrupado por categoría.
Incluye Biblioteca MIDAS como menú propio junto a IGAC, para alojar descargas comunes
de capas, circulares urbanísticas y soportes reutilizables sin mezclarlas con Maestros
ni repetirlas por avalúo. La carga permite hasta 20 archivos por lote, omite duplicados
y muestra los límites PHP reales para ajustar lotes pesados al hosting.
Incluye en Maestros una biblioteca documental maestra para subir soportes normativos
faltantes, clasificarlos por destino lógico y relacionarlos con módulos sin duplicar
el PDF. Sirve como entrada administrativa previa a integrar una norma a una biblioteca
especializada o a citarla desde una academia de módulo.
Incluye glosario valuatorio inicial basado en NTS M 01 y formulario para alimentar nuevos factores o conceptos académicos.
Solo implementa datos iniciales, no fórmulas, aprobación ni generación de informes.

## Referencias encontradas en InversKC

El controlador `controllers/ValuatoriaController.php` supera 1 MB y varias vistas
también son extensas. Consultarlos por función para extraer responsabilidades pequeñas:

| Módulo futuro | Referencia de vistas en `views/valuatoria/` |
| --- | --- |
| Avalúos urbanos | `avaluos_urbanos.php` |
| Posesión | `avaluo_posesion.php`, `avaluo_posesion_v1.php` |
| Sujeto y comparables | `definicion_sujeto_comparables.php`, variante `v2` |
| Sector y entorno | `caracterizacion_sector_entorno.php` |
| Identificación jurídica | `identificacion_caracteristicas_juridicas.php` |
| Propiedad horizontal | `caracteristicas_agrupacion_ph.php` |
| Valoración cualitativa | `valoracion_cualitativa.php` |
| Multicriterio | `modelos_multicriterio_critic.php` |
| Informe técnico | `informe_tecnico_avaluo.php` |

Las referencias del login son `control-servicios-inmobiliarios/src/Core/Auth.php`
y `dashboard-marketing/app/Auth.php`. Se usa el contrato de credenciales, no sus sesiones
ni dependencias WordPress. La skill de diseño disponible no tenía su script `search.py`;
se aplicó su guía de accesibilidad, rendimiento e interacción directamente.

## Entregar al programador por cada módulo

1. Campos, tipos, unidades, catálogos, ejemplos y obligatoriedad al finalizar.
2. Reglas y fórmulas confirmadas, redondeos y casos de cálculo esperados.
3. Etapas del flujo, responsables, permisos y aprobación de documentos.
4. Secciones que autoguardan y qué acción constituye envío definitivo.
5. Documentos permitidos, límites, almacenamiento privado y descarga autorizada.
6. Criterios de aceptación y datos de prueba anonimizados.

## Secuencia sugerida

Configurar y comprobar acceso → definir expediente y permisos → migraciones y servicios
por módulo → formularios pequeños con autoguardado → pruebas con casos aprobados →
informes y revisión → importación histórica independiente si se solicita.

No hay aún importación histórica, motor de cálculos completo ni auditoría de
negocio completa. La colaboración habilitada se limita al acceso delegado descrito arriba. Agregar otros permisos cuando el jefe entregue la
implementación. Si se generan documentos de SuCasa, aplicar la skill de branding correspondiente.

## Contrato del autoguardado

POST `/avaluos/{id}/borrador`, cookie de sesión, token CSRF y JSON con `version`,
`titulo`, `tipo`, `direccion`, `municipio`, `observaciones`, `tipo_derecho`,
`tipo_negocio`, `destinacion`, `tipo_inmueble`, `subtipo_funcional`, `finalidad`,
`base_valor`, `aplica_niif`, `regimen_ph` y `estructura_metodo`.
Se aceptan borradores vacíos.
Respuesta 200 confirma versión y fecha. 401 sesión, 419 CSRF, 422 validación,
404 ficha inexistente/ajena, 409 conflicto de versión. El frontend conserva valores
en memoria ante error y bloquea sobrescritura ante conflicto; hay que copiar los cambios
antes de recargar. La pérdida de conexión no equivale a haber guardado.

La base no promete recuperar texto no enviado tras cerrar el navegador. Recupera
de BD lo confirmado. No se autoguardan credenciales ni acciones definitivas.

El expediente conserva la capa didáctica heredada del avance anterior, pero cada
campo se clasifica como soporte normativo directo, derivación técnica/metodológica
u organización operativa interna. Las opciones no deben presentarse como mandato
legal literal si solo son ayudas de clasificación para el analista.

## Requisito para el entregable del avalúo

Las ayudas contextuales de los campos, visibles como `?`, también son insumo del
informe final. Cuando una opción seleccionada requiera explicación técnica, el
generador del entregable debe usar esa ayuda como base narrativa y ajustarla al
valor elegido. Ejemplo: si `Centralidad` queda en `Alta`, el informe puede explicar
que la centralidad mide la inserción urbana, cercanía a equipamientos, servicios y
nodos de actividad, siempre que aplique al caso. La fuente de estas ayudas debe ser
el catálogo del módulo, no una redacción duplicada en la plantilla del documento.


## Normatividad urbana

El capítulo 5 debe consultar una biblioteca liviana, no cargar el módulo con todo el
POT. La implementación registra documentos fuente, cuadros, categorías de uso, reglas
y parámetros en tablas reutilizables. Cada avalúo conserva una ficha propia con la
fuente adoptada, resultado de MIDAS, concepto de Planeación si existe, clasificación
del suelo, área de actividad, tratamiento, uso actual y uso pretendido, cruce frente
al cuadro de usos, restricciones, conclusión del analista y soportes.

La primera semilla corresponde al PDF de cuadros de reglamentación de usos del Decreto
0977 de 2001 entregado por el usuario. Una migración correctiva vuelve a sembrar esos
cuadros si el hosting marcó aplicada la semilla anterior sin dejar datos. La consulta
MIDAS del capítulo 5 lee primero Predios, actualiza el numeral 3, usa la referencia del
botón Uso Suelo y, cuando reconoce una categoría como Residencial D, Mixto 2 o
Institucional 3, aplica el cuadro POT disponible al numeral 5. Queda listo para sumar
resoluciones o actos de Planeación como nuevos documentos de academia urbana sin
duplicar campos del avalúo.

## Pruebas aisladas

`php tests/run.php` comprueba validación y autenticación con SQLite en memoria.
`npm test` comprueba estados y concurrencia del cliente sin instalar navegador.
`php tests/database.php` comprueba MySQL/MariaDB en bases desechables locales;
requiere `GA_TEST_PORT`, opcional `GA_TEST_USER`/`GA_TEST_PASSWORD`. Nunca usa `.env`
para elegir una base a borrar: no borra bases ni tablas y rechaza fixtures existentes.

Repetir revisión visual en navegador cuando cambien vistas. No declarar validado el
login real hasta tener conexión a funcionarios y una cuenta de prueba autorizada.

## Normas Técnicas Sectoriales

La migración `202609150003_create_valuation_standards.php` crea el catálogo y siembra
las 22 normas entregadas. Los archivos PDF se copian al almacenamiento privado con:

```powershell
php bin/console.php standards:import "C:\Users\skcge\OneDrive\Escritorio\Nueva Ley Valuatoria\Normas Sectoriales"
```

Las categorías sin normas asignadas quedan visibles como pendientes para conservar la
estructura oficial de inscripción.

## Marco jurídico e IVS

El menú jurídico nacional queda listo para sembrar leyes, decretos, resoluciones,
actos y artículos aplicables por categoría. El menú de Normas Internacionales de
Valuación registra la estructura IVS por familia y su relación con categorías RAA.
Los PDFs fuente del marco jurídico se importan desde la pantalla del módulo y se guardan
en `storage/marco-juridico-nacional/`; Git conserva la carpeta base pero ignora los PDFs.
Los PDFs de IVS se cargan desde cada tarjeta internacional y se guardan en
`storage/normas-internacionales-valuacion/`.
Los PDFs de NIIF se cargan desde cada tarjeta NIIF y se guardan en `storage/normas-niif/`.
Los documentos maestros se cargan desde Maestros y se guardan en
`storage/documentos-maestros/`, con respaldo interno y metadatos de destino, vigencia,
utilidad, temas y módulos que los citan.
Los documentos MIDAS comunes se cargan desde el menú MIDAS y se guardan en
`storage/biblioteca-midas/`, con respaldo interno, grupo de capa y utilidad práctica.
Los soportes MIDAS del numeral 2 quedan reservados para evidencias específicas del caso.
El menú también enumera los campos del expediente y separa soporte normativo directo,
derivación metodológica y control operativo interno.


## 2026-10-02 · Acceso visible a captura de portales

M3 abre en Portales y pegado. El capítulo 8 ofrece un acceso directo a M3,
conservando el componente activo y el banco anterior. La captura mantiene los
lectores por portal, URL individual, pegado por fuente y control de repetidos.
Se agregan accesos a ficha manual, matriz y mapas, y consulta del artículo 17.
Los datos incompletos siguen siendo borradores; el cálculo corresponde a M4.
No se modifican importadores, persistencia, migraciones ni datos existentes.
Cultivos deja de figurar en el grupo habitual de áreas; permanece disponible en
Todos los campos para conservar información anterior. Esto no implementa todavía
una clasificación automática urbano/rural ni los campos normativos pendientes.
Validación: PHP 471, JS 113, lint PHP, build y tamaño 58.1 KB gzip.
Navegador local con datos sintéticos: entrada directa a portales, apertura del
lector de enlace/texto y ficha manual. No se probó descarga real de los portales
ni guardado en hosting en esta revisión. No se cambió el esquema de BD.

## 2026-10-02 · Ayuda PH en Mercado

Acordeón compartido en M3 (portales y matriz), M4 y M5: captura original,
pendientes de investigación, depuración de comparables según 19.2.b,
liquidación según naturaleza jurídica (36.2), áreas privadas diferenciadas,
condominios y prevención de doble conteo. Artículo 19 completo y enlace al
artículo 36 del Diario Oficial; artículo 17 ya disponible en captura.
La ayuda no pasa al informe ni calcula descuentos; advierte el alcance actual
de los estadísticos. Sin modificaciones a importadores, guardado, esquema o datos.
Validación: 471 comprobaciones PHP, 113 pruebas JS, lint, build y 58.1 KB gzip.
Revisión local del acordeón en navegador de escritorio y visibilidad en ancho
móvil. No se ejecutó prueba de BD: cambio exclusivo de ayuda en vistas.

## 2026-10-02 · Recorrido A–E y fuentes visibles

Menú: A Inmuebles, B Matriz y método, C Insumos y comparables (etapa 3),
D Análisis de las muestras (etapa 4), E Integración y texto del numeral 8.
Los enlaces conservan componente y método. Sin componente, D pide elegirlo;
el siguiente paso de B dirige a selección si falta método, o a los insumos si existe.
Portales e inmobiliarias del catálogo aparecen como botones en C. Mantiene
buscadores, filtros soportados, lectores por fuente, pegado y duplicados.
No se agregaron integraciones nuevas a inmobiliarias: las que abren el sitio
siguen requiriendo filtros manuales, tal como se indica en su panel.
Verificado: PHP 471, JS 113, lint de vistas, build y 58.1 KB gzip; navegador local
con cambio de FincaRaíz a Araújo & Segovia y recorrido visible A–E; acceso C
visible en ancho móvil. Sin cambios de base de datos ni pruebas de portales en vivo.


## 2026-10-02 · Lectura completa en Matriz y método

Las tarjetas de Matriz y método reutilizan el acordeón de la academia para
los artículos 16–34 de Mercado, Renta, Costo y Residual. Se conservan resumen,
resaltados, lectura por teclado, altura limitada y enlace a la fuente.
La selección incorpora el artículo 15; la ayuda de PH incorpora el 36 completo,
con sus cuatro numerales y dos parágrafos. Estos dos textos proceden del Diario
Oficial reproducido por Camacol (páginas PDF 5 y 9), identificado en el enlace.
La revisión transversal por rangos de artículos conserva su resumen existente.
Sin cambios en importadores, comparables, fotos, guardado ni base de datos.
Verificación: 491 comprobaciones PHP, 113 pruebas JS, lint PHP, build y
58.1 KB gzip. Apertura del art. 17 comprobada en navegador local de escritorio;
región visible y ancho contenido comprobados en vista móvil. La captura móvil
del navegador agotó su tiempo; la comprobación móvil se limitó al DOM.
No se ejecutaron pruebas de BD: cambio de contenido y vistas, sin persistencia.


## 2026-10-02 · Recuperación del tipo para búsquedas de comparables

Corregido el reemplazo del tipo del expediente por un property_type vacío al
seleccionar componente. ComparableSearchContext conserva el tipo explícito de la
unidad; solo la única unidad principal puede heredar el tipo del capítulo 1.
Los anexos y las unidades ambiguas no heredan esa clasificación. Normaliza claves
y etiquetas del catálogo. Una tipología desconocida ya no envía «tipología
pendiente» a Google; presenta búsqueda general y aviso para completar el tipo.
Metrocuadrado incorpora ruta de oficinas en venta en Bocagrande verificada en
navegador. Mercado Libre mostró 3 resultados y Ciencuadras 29 tras escribir el
barrio y pulsar Enter; estos conteos son observaciones, no inventario garantizado.
Ciencuadras requiere confirmar el barrio en su portal; se aclara en la ayuda.
No cambia lectores, pegado por lote, duplicados, filas guardadas ni esquema.
Validación: 500 comprobaciones PHP, 113 JS, lint PHP, build y 58.1 KB gzip.
Pruebas de regresión: principal sin tipo, anexo sin tipo, varias unidades,
clasificación explícita diferente y recuperación de enlaces directos.
No se ejecutó prueba de base de datos porque no cambió persistencia ni esquema.


## 2026-10-02 · C: búsqueda y tabla separadas

Dos subpestañas: Buscar inmuebles y Tabla de muestras. Mapas dentro de muestras;
criterios y academia se conservan plegables. Pegado de FincaRaíz/Metrocuadrado
abierto, lectores por lote elegidos según unidad; conteos y selección conservados.
Ver CAPTURA-COMPARABLES-CONTINUIDAD.md, también referenciado desde AGENTS.md.
Validado con 505 comprobaciones PHP, 113 JS, lint, build y tamaño 58.1 KB gzip.
Navegador local: lector visible, cambio entre vistas y texto pendiente conservado.
Sin cambios de persistencia ni prueba de BD; no valida datos del hosting.


## 2026-10-02 · Paso 1: orientación de Mercado junto a la unidad

En A, unidades sin método o con Mercado abren orientación en su propia ficha.
Distingue inmueble sujeto (36) y comparables (19.2); incluye lectura completa de
16–21 y 36, comprobaciones, límites y advertencia de no seleccionar automáticamente.
Mantiene el enlace a selección existente con identidad de unidad. No modifica
selección, otros métodos, importadores, capturas ni datos. Es un primer paso de
revisión con el usuario, no una revisión exhaustiva de todos los casos especiales.
Fuente contrastada: Diario Oficial reproducido por Camacol, artículo 36, página 9.
Validación: 506 comprobaciones PHP, 113 JS, lint de archivos cambiados, build,
58.1 KB gzip; apertura en la misma ficha y lectura visible en ancho móvil.


## 2026-10-02 · Capítulo 1: búsqueda IGAC de garaje y depósito

Alcance solicitado: oficina con garaje/celda de parqueo y depósito, referencias
constructivas IGAC y continuidad de la unidad en el capítulo 8. No se cambiaron
comparables, cálculos, métodos adoptados, normativa ni datos del expediente.
Garaje/parqueadero/celda de parqueo comparten búsqueda; Parqueo conserva su clave
persistida. Filtro por familia (PHP y Alpine) más consulta textual y opción de
ampliar a toda la categoría. Se conserva la referencia seleccionada al buscar.
Garaje ofrece 4 referencias relacionadas publicadas (2 sótanos y 2 pavimentos),
sin crear una tipología ficticia de celda. Depósito ofrece Anexos.Depósitos_1,
sin confundir cuarto útil con silos o depósitos de líquidos. Las estaciones que
excluyen estacionamiento en especificaciones no se proponen para garaje.
Las búsquedas de UI no activan autoguardado; elegir la referencia sigue el flujo
existente. Oficina conserva Comerciales + Edificios.
Ficha: unidad, vida útil en años y página real de la fuente, descripción,
especificaciones, imagen y acceso a biblioteca IGAC. El JSON cargado no contiene
costos de reposición ni fecha base: se informa expresamente, sin inventar importes
ni actualización. Queda pendiente incorporar una fuente de costos identificada,
con fecha, ubicación y alcance antes de ofrecer costos históricos o actualizados.
Capítulo 8: matriz abre la unidad solicitada; la tarjeta muestra el tratamiento
registrado en capítulo 1 y diferencia la confirmación pendiente del analista.
Verificación: 564 comprobaciones PHP, 116 JS, lint completo, build y 58,8 KB gzip.
Interfaz local: búsqueda garaje/celda, selección conservada ante consulta sin
coincidencias, catálogo ampliado (136 anexos), ficha visible en escritorio y
viewport CSS de 390 px sin desbordamiento. Sin cambios de esquema/persistencia;
no se requiere migración. No desplegado ni probado contra registros del hosting.

## 2026-10-02 · Mercado: confrontación automática con numeral 3

Capítulo 8 sustituye la recomendación genérica de revisar variables por una tabla
de ocho controles por unidad: identificación/naturaleza, área, uso, estado de obra,
conservación, matrícula, coeficiente y componentes/tratamiento. Lee datos guardados;
no persiste casillas manuales. Estados OK, Diligenciar, Diferencia y No aplica.
Los enlaces abren 3.1/3.2/3.3 en la unidad y panel pertinentes. El retorno conserva
la unidad y abre su academia. Actualizar vuelve a consultar; respuesta sin caché.

3.1 incorpora soporte de Mercado por unidad con autoguardado y versión optimista.
El vínculo con ficha general/PH debe declararse expresamente; los anexos no heredan
automáticamente matrícula, coeficiente ni uso de la oficina. 3.2 expone área privada
y fuente ya existentes en esquema. Se detectan diferencias de áreas, matrícula y
coeficiente vinculados, matrícula repetida entre unidades independientes, usos y
tratamientos. Resolver usos distintos exige compatibilidad explícita y explicación.
La conservación requiere estado adoptado y evidencia; texto generado no basta.
OK constata datos/soportes registrados y comparaciones, no certifica documentos ni
comparabilidad del precio. No modifica comparables, valores o método de costo.

Aplicar migración nueva 202610020002_unit_market_evidence.php al publicar. No se
editaron migraciones previas ni se accedió a bases reales. Validación: 579 checks
PHP, 116 JS, 129 de persistencia y 58 HTTP en MySQL desechable (puerto 3357);
610 archivos PHP sin errores de sintaxis, build y 58,8 KB gzip. UI: área pendiente
de oficina completada en 3.2, autoguardado, retorno y actualización a 8 OK;
identificación de garaje sólo completa sus propios controles. Viewport CSS 390 px
sin desbordamiento de página y consola sin errores. Pendiente publicar en hosting.

## 2026-10-02 · Verificación visible en M1

Corrección de ubicación: el cuadro había quedado dentro del apartado 2 (unidad),
aunque se indicó al usuario entrar al 1. Se mueve antes de la navegación académica
de Mercado, fuera de paneles x-show/x-cloak. Está visible en M1 con cualquier
apartado seleccionado, sin duplicarse ni cambiar controles o persistencia.
Regresión comprueba orden antes de pestañas y una sola instancia por unidad.
Validación: 580 checks PHP, 116 JS, 610 PHP sin errores, build y tamaño 58,8 KB.
Vista local aislada con datos ficticios: cuadro visible al abrir y al pasar a
Insumos mínimos; consola sin errores y ancho CSS 390 px sin desbordamiento.
No se certifica la actualización del hosting a partir del push a Git.
