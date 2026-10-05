# Entrega al responsable de la implementación

Insumos: tercera pestaña de consulta «Configuración por portal y tipo de inmueble».
Evidencia pública fechada 2026-10-04: 20 perfiles, especialmente FincaRaíz/Ciencuadras.
Combos no documentados siguen pendientes; no declara formularios privados ni APIs.
Ver PORTALES-CAMPOS-POR-TIPO.md y app/Services/portal-profiles. Sin esquema, extracción
nueva, cambios de identidad ni depuración. Próximo paso autorizado por separado:
comparación campo/fuente, discrepancias internas y confirmación manual de identidad.
Validado: PHP934/JS136/lint/build68,1KB gzip. Navegador: selección de portal/tipo,
perfil observado y pendiente, regreso a bandeja conserva 3 inmuebles/4 anuncios;
consulta estrecha sin desbordamiento ni errores de consola. Sin pruebas BD nuevas:
esta entrega sólo consulta un catálogo estático y no añade persistencia.

Insumos: cada panel de portal/inmobiliaria muestra búsqueda breve de la unidad
actual, botón copiar, filtros orientativos y alternativa Google con dominio.
Garajes incluyen sinónimos; lectura extensa existente se conserva debajo plegada.
No promete capacidades del buscador, no cambia extracción ni muestras. Contexto
heredado de ComparableSearchContext (tipo por unidad, renta/venta por recorrido).

Academia centralizada: eliminado el bloque repetido «Artículos completos para este
paso» de las etapas operativas. Mercado conserva íntegros 16–21 en Academia; PH36
en General. Insumos, Análisis y Entregable mantienen el acceso Academia. El aviso
de discrepancias en Análisis sigue vigente, sin repetir teoría en la captura.

Corrección de navegación: los cinco pasos permanecen visibles en Academia General
y Configuración. Sin componente actual, continúa el primer recorrido activo del
método consultado (o primer método activo), anunciado en pantalla. Sin métodos,
remite a Configuración. No cambia asignaciones ni muestras. PHP858/JS136/build68,1KB.

REQUISITO PERMANENTE: advertir contradicciones/incumplimientos de artículos en
Análisis, con artículo, evidencia, incidencia y acción pendiente. Aviso visible
actual; motor de detección integral pendiente. Leer CONTROL-NORMATIVO-ANALISIS.md
antes de ampliar análisis/conclusiones. No confundir academia con certificación.
Academia General centraliza artículos y lectura PH; Configuración ya no muestra
el bloque de artículos. Subtemas generales siempre disponibles, con o sin métodos.

C1 homogéneo con las otras academias: tarjetas de artículos 27–30, trece temas
íntegros en seis apartados y subpestañas dependientes. Tablas, ejemplos y PDF original
conservados. Sólo presentación de academia. PHP817/JS131/lint/build/66,4KB;
Chrome escritorio y móvil. Ver COSTO-C1-ACADEMIA.md.

Academia capítulo 8 agrupada por métodos activos, incluidos contrastes, sin repetir
teoría por unidad. Academia accesible desde Configuración; cada pestaña anuncia
unidades/alcances. Reutiliza guías existentes y C1 completo; lectura PH compartida
plegable y controles por unidad en apartado cerrado. Sin esquema ni datos nuevos.
PHP690, JS131, lint/build/66,4KB, navegador escritorio/móvil y lectura completa.

Configuración del 8 por pestañas de unidad: sólo se renderizan los formularios de
la unidad elegida; navegación conserva identidad y recuperación del borrador.
Conceptos dinámicos para organización, método y tratamiento. Ambas alternativas
de organización son visibles; separación NPH deshabilitada en PH/anexos.
Textos sugeridos por método/tratamiento para justificación y alcance, aplicables
sólo a campos vacíos mediante decisión explícita. No acreditan evidencia ni se
guardan automáticamente al consultar. Sin esquema ni nuevas reglas de cálculo.

Capítulo 8 simplificado: Configuración y una única barra Academia/Insumos/Análisis/
Entregable por recorrido. Selección y alcance se editan en Configuración; las rutas
anteriores siguen funcionando. Matriz y sugerencias plegadas allí, banco en Insumos,
texto y revisión de resultados en Entregable. C1 teórico continúa sin unidad.
El analista puede activar métodos adicionales del mismo alcance (PH incluido).
Claves estables `:metodo:renta`, etc., JSON existente, guardado/versionado atómico,
insumos separados y memoria conservada al desactivar. No sumar estimaciones alternativas.
Sin nuevas fórmulas ni migración. Detalle en PLAN-VALORACION-CAP8.md.

Capítulo 8 inicia en Plan de valoración: organizar → métodos/alcances → recorrido
por parte → consolidación. Casa NPH permite terreno y construcción vinculados
a una sola ficha, por decisión explícita. JSON existente, sin migración, claves
estables y muestras anteriores conservadas; unidad completa no se suma con partes.
Artículos completos plegables por etapa y método antes de su contenido.
Ver PLAN-VALORACION-CAP8.md para recorrido, preservación, controles y límites.
PHP678, JS131, BD163 (instancia nueva3366), lint/build/66,4KB y navegador local.
Consolidación monetaria de costo sigue pendiente; no se adoptan valores automáticos.

C1 consultado desde Mercado muestra sólo academia, sin revisión ni recorrido
operativo de esa unidad. Pestañas filtradas a unidades con Costo guardado.
La revisión reaparece para cada componente efectivamente asignado a Costo.
Sin escritura de métodos/datos. PHP660, JS131, lint/build/66,4KB y navegador
escritorio/móvil. Ver COSTO-C1-ACADEMIA.md. Push no confirma hosting.

C1 costo: temas cerrados al entrar y consulta completa plegable de artículos
27–30 más anexo 2.3, páginas oficiales 22–39. PDF local original con 18 páginas,
lector, descarga y enlace al documento íntegro. Desplegar también
`public/assets/normativa/igac-941-anexo-costo.pdf`.
Validación: PHP657, JS131, lint/build/66,4KB; HTTP PDF200, Chrome escritorio y
móvil. Sin esquema/datos ni cambios C2–C5. Ver COSTO-C1-ACADEMIA.md.

M3 PH: tabla diferenciada, descuento y negociado calculado, instrucciones por
portal, conteos por fuente y descarga XLSX con pendientes. Datos JSON sin migración.
Ver PH-M3-TABLA-NEGOCIACION.md: controles normativos y pendientes reales de M4.
Conserva lectores, matriz, versiones y fotos; no modifica datos de Zona Franca.

M1 y M2 PH: vínculo explícito de anexo a principal activa, naturaleza jurídica y
composición del área en M2. Los campos viven en market_evidence_json existente;
edición parcial conserva los soportes del numeral 3 y la versión compartida impide
sobrescrituras. No hay migración. Ver PH-VINCULO-M1-M2.md para recorrido y controles.
M1 diferencia unidades independientes, partes privadas integradas y comunes de uso
exclusivo. Confronte contradicciones sin borrar áreas, matrícula o coeficientes.
Validación: PHP 606, JS 116, BD desechable 3360: 139, HTTP local: 7, lint, build y
59,0 KB gzip; navegador, autoguardado/recarga y móvil CSS 390 px. Git para hosting.

Consulta y tabla PH: instrucciones específicas de búsqueda/captura y grupo propio
con presencia de parqueaderos/depósitos, cantidad, inclusión en precio, naturaleza
jurídica y soporte. Misma matriz, lectores y fotos conservados; datos nuevos en
capture_details existente, sin migración. Ver CAPTURA-COMPARABLES-CONTINUIDAD.md.
No interpreta anexos iguales como excepción al art. 19.2.b; no calcula depuración
monetaria ni modifica estadísticos existentes de M4. Pruebas: PHP 598, JS 116,
BD desechable 3358: 130, lint, build, 59,0 KB gzip; escritorio y CSS 390 px.

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

## 2026-10-02 · Descripción y cobertura de todos los anexos

M1 muestra resumen de todas las unidades activas con nombre, clasificación,
descripción propia de 3.1, estado y conteo de controles para Mercado, incluso
cuando la academia seleccionada es otra unidad. Cada fila abre su numeral 3 o
sus controles. Checklist pasa a nueve controles: añade nombre, tipo y descripción
propios. Nombre genérico, descripción vacía o clasificación pendiente no son OK;
nombre de garaje/depósito con clasificación contradictoria muestra Diferencia.
No se modifica automáticamente identidad, clasificación o contenido técnico.
OK registra presencia del texto, no valida que una base IGAC describa la visita.

3.1 mantiene el campo notes y su autoguardado existente; aclara descripción física
por unidad y orienta ubicación interna, acceso y condiciones observadas. Enlaces
de descripción conservan unidad y evitan desplazamiento al soporte jurídico.
Sin columnas, migraciones ni modificaciones al método de costo o a comparables.

Revisión de sólo lectura en hosting: oficina, Deposito y Garaje con notes vacío
en 3.1; en 3.3 tipos oficina, deposito y parqueo respectivamente. No se rellenaron
campos ni se guardaron cambios del expediente. Depósito tiene referencia IGAC,
pero eso no completa la descripción física editable.

Validación: 585 checks PHP, 116 JS, 612 archivos PHP sin errores, build y 58,8 KB
gzip. UI aislada con datos ficticios: tres filas, depósito sin descripción pendiente,
enlace abre controles del garaje, sin errores de consola; CSS 390 px sin
desbordamiento de página. El push publica código en Git; usuario actualiza hosting.

## 2026-10-02 · Tipo del anexo en 3.1

La lista de tipo de inmueble principal no incluía depósito y sugería heredar el
tipo general para los anexos. 3.1 muestra ahora la clasificación construction_type
ya guardada en 3.3 para cada anexo, con enlace a su panel Datos básicos para editarla.
Depósito se presenta como Depósito / cuarto útil y parqueo como Garaje / parqueadero
/ celda de parqueo. Si está vacío, pide diligenciar en 3.3. No duplica el editor ni
asigna tipos por nombre; la unidad principal conserva la lista property_type.
El formulario mantiene el property_type existente del anexo en un campo oculto
para que guardar su descripción no lo borre. Sin cambios de persistencia o esquema.

Verificación: 589 checks PHP, 116 JS, 614 PHP sin errores, build y 58,8 KB gzip.
Interfaz local con datos ficticios: depósito y garaje consultan tipo correcto,
enlace conserva la unidad y Datos básicos; CSS 390 px sin desbordamiento y consola
sin errores. Cambios subidos a Git para actualización habitual del hosting.

## 2026-10-02 · Semáforo y significado del conteo

Resumen y detalle muestran controles completos/aplicables, datos faltantes en rojo,
diferencias en amarillo y controles completos en verde (cero usa tono neutro).
Tipo registrado se muestra en verde independientemente de descripción pendiente;
mensaje de descripción identifica sólo campos realmente faltantes. No se marca
todo completo por haber llenado uno de los campos de un control compuesto.
Lectura actual en hosting: oficina sigue sin descripción propia y 0 controles
completos; clasificación sí registrada. No se escribieron datos del expediente.
La detección de nombre de anexo conserva separadores para confrontar «Depósito 8»
o «Celda de parqueo 12» con el tipo, incluyendo acentos y números.
Sin cambios de BD. Validación: 592 checks PHP, 116 JS, 615 PHP sin errores, build,
58,8 KB gzip. Vista aislada ficticia prueba verde/rojo/amarillo, CSS 390 px sin
desbordamiento y consola sin errores. Publicación Git para actualización del usuario.

## 2026-10-02 · Retorno de Excel en M3

Tabla editable prioritaria con autoguardado; Exportar a Excel e Importar Excel actualizado son opcionales. Importación por ID, con revisión antes de aplicar, versión de colección, aislamiento de expediente/unidad y filas omitidas conservadas. No se crean muestras desde Excel. Ver docs/PH-M3-TABLA-NEGOCIACION.md para pruebas y límites. PHP ZIP y SimpleXML requeridos en hosting. Sin migraciones ni cambios a datos reales o M4/M5.

## 2026-10-02 · Valores por m² en M3

Oferta y negociado por m² automáticos y preliminares, con área, base, unidad y
estado visibles. PH usa área privada construida con fuente; NPH/condominio
requiere identificar base publicada. No suma libres o anexos, no divide dos
veces valores ya unitarios, ni convierte descuento desconocido en cero.
Excel también recalcula área y cocientes con fórmulas. Art. 19.2.b y 36.2
se explican por separado; no estima componentes ni cambia análisis M4/M5.
Sin migración ni escritura de datos reales. Ver PH-M3-TABLA-NEGOCIACION.md.
Validación: 642 PHP, 127 JS, lint, build 65,9 KB gzip. Navegador: ejemplo
ficticio guardado/recargado 475 millones / 100 m² = 4.750.000 COP/m²;
consola limpia y CSS móvil 390 px sin desbordamiento. 142 checks BD desechable 3363.

## 2026-10-02 · Excel como flujo principal y fecha de carga

Bloque Descargar tabla completa en Excel / Importar Excel actualizado al comienzo
de M3 tabla. Descargas repetidas, nombres con versión y hora UTC. Confirmar
Guardar Excel actualizado usa endpoint protegido nuevo: revalida archivo/versión
y guarda datos y sello de importación en una transacción. La fecha de Colombia,
nombre y versión persisten por unidad/banco, incluso al recargar. Cargas sin
cambios pueden registrarse; selección o revisión no altera fecha. No crea filas.
Migración aditiva 202610020002_comparable_excel_history; AUTO_MIGRATE o migrate.
642 PHP, 129 JS, 152 BD desechable 3364 y ocho HTTP incluyendo CSRF, versión,
persistencia de fecha y conservación de otra colección. Build 66,1 KB gzip.
Prueba UI local muestra fecha y archivo guardados; carga mediante navegador sigue
limitada por permiso de archivos de extensión Chrome. No se modificaron permisos.

## 2026-10-02 · Columnas de Excel orientadas a captura desde portales

Descargar Excel en M3 coloca primero fuente/enlace, ubicación, tipo/operación,
oferta/unidad, área publicada, parqueaderos/depósito, contacto y datos del aviso.
Descuento y cocientes por m² siguen; verificaciones y soportes permanecen después,
con pendientes e ID al final. No elimina campos, oculta columnas ni modifica M4.
El orden de captura no depende del grupo seleccionado en la matriz del navegador.
Metadatos y fórmulas usan el mismo orden exportado; importación mantiene identidad.
Sin migración ni escritura de datos reales. 642 checks PHP y 131 JS; build y
check:size correctos. Prueba del exportador real verifica valores, ID, contexto
y referencias de negociación después de mover columnas. Descargar de nuevo tras
actualizar el hosting; archivos anteriores conservan compatibilidad de importación.

## 2026-10-02 · C1/C2 costo e inventario InversKC
Academia y revisión por unidad en C1; alcance directos/indirectos/remanente/retiro
con autoguardado C2. Ross–Heideck continuo como método nuevo; Fitto sólo antecedente.
Consultar C1 desde herramientas no reclasifica Mercado. C3–C5 todavía no calculan.
Ver COSTO-C1-C2-INVENTARIO.md para catálogos, relaciones, limitaciones y ruta siguiente.
JSON metodología existente, sin migración ni escritura de expedientes reales.
655 PHP, 131 JS, 156 BD desechable 3365; build/size 66,3 KB gzip.
Chrome local: alcance guardado y conservado al recargar, semáforo actualizado C1.

## 2026-10-02 · Academia exclusiva C1 del costo
Trece temas visibles antes del checklist; vida de referencia/remanente/prolongada,
procedencia90%/EC2,5–4,5, tablas y ejemplos, Ross continuo, patrimonio y etapas.
100 años sigue como referencia de permanentes; sin vida/estado adoptados por defecto.
Sin Fitto en C1, ni cambios C2–C5 o importación de catálogos. Ver COSTO-C1-ACADEMIA.md.
Lint, PHP655, JS131, build y size66,3KB; UI local de consulta. Sin migración.

## 2026-10-03 · Bandeja de insumos Mercado
Tarjetas por inmueble confirmado, anuncios originales separados por fuente,
selección para Análisis, vinculación/separación reversible y comparación de diferencias.
Captura ampliada de descripción/atributos rotulados y lectura posterior del mismo
enlace completa vacíos sin sustituir precios; diferencias y último texto conservados.
Coordenadas sólo verificadas manualmente en M4 con precisión/fuente/soporte; mapa
después del guardado. No desarrolla depuración estadística ni adopción de valores.
Ver MERCADO-INSUMOS-BANDEJA.md. JSON aditivo, sin migraciones ni datos reales alterados.
PHP835, JS136, BD168 más cinco nuevas de persistencia, lint665PHP, build68KB gzip.
Chrome local: vincular y seleccionar dos anuncios, recepción como un inmueble,
captura85,5m²/2garajes/1depósito, relectura150→155 conserva150 y advierte diferencia,
punto aproximado confirmado y recargado; CSS390px sin desbordamiento lateral.

## 2026-10-04 · Plan de investigación
Cuarta pestaña de Insumos, por unidad/parte/método; datos del sujeto, factores y disponibilidad
por portal, diferencias y conteo conjunto. No ejecuta regresión ni altera anuncios.
Ver PLAN-INVESTIGACION-MERCADO.md. PHP949, JS139, MySQL176, build69KB gzip.
Prueba local: selección/definición/justificación, guardado y recuperación al recargar.


## 2026-10-04 · Referencias pendientes y lectura breve
15 perfiles públicos adicionales en Properati, Metrocuadrado y Mercado Libre;
fuentes indexadas rotuladas y apartamento Mercado Libre parcial. Oficina documentada
para los cinco portales. Otros cruces permanecen pendientes sin heredar datos.
La consulta muestra cobertura y resumen; detalle y fuente plegados. Sin cambios
de persistencia ni lectores. Ver PORTALES-CAMPOS-POR-TIPO.md para alcance y fuentes.
Validación de ampliación: 984 checks PHP, 139 JavaScript, lint sin errores,
build y 69 KB gzip. Navegador local: Properati Oficina, cambio a Mercado Libre,
detalle plegable y Depósito pendiente sin herencia; sin errores de consola.
Viewport móvil solicitado 390 px, observado 585 px CSS por zoom del navegador;
body 562 px sin desbordamiento. Sin cambio de base: no requiere migración.

## 2026-10-04 · Comparación visual por fuente
Cuadro por inmueble en el plan de investigación; anuncios en filas, factores elegidos
en columnas, áreas siempre visibles. Diferencias/relecturas amarillo, coincidencia
legible verde (no verificación), faltantes/una fuente gris. Sin adoptar ni modificar
anuncios. Área positiva compatible requerida para conteo conjunto; no agrega parámetro
por defecto. Metadatos de fuente en snapshot de lectura; sin esquema o persistencia nueva.
Validación: 985 PHP, 142 JS, build 69,7 KB gzip. Navegador local con ejemplo ficticio
3/3/2 baños amarillo y 80 m² misma base verde; móvil con scroll contenido y sin errores.
No se alteraron datos de producción. Ver PLAN-INVESTIGACION-MERCADO.md.

## 2026-10-04 · Sujeto como referencia y máximo cuatro factores
Plan de investigación: factores en filas; sujeto propio del numeral 3 como primera
columna de datos, anuncios por portal y validación al final. Todos los factores del
tipo permanecen visibles. Planta eléctrica y destinación añadidas; ausencia no es cero.
Clasificación conservada en bloque cerrado. Hasta cuatro candidatos al modelo; investigar
no consume cupos. Meta orientativa 10 inmuebles distintos por factor, sin garantía de
suficiencia estadística ni regresión ejecutada. Área compatible obligatoria para el conteo.
Campos opcionales explícitos en capture_details_json, sin migración ni extracción global.
Validación: 988 PHP, 143 JS; build 69,8 KB gzip. UI local 3/3/2 amarillo, 80 m² verde,
sujeto separado y quinto candidato deshabilitado. Commit/push; sin acceso a Hostinger.

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
También pasan 176 verificaciones BD contra una instancia local desechable nueva (3368).

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

## Catálogo ampliado y clasificación semántica (2026-10-04)

Ver FACTORES-ESCALAS-INVESTIGACION.md: investigación primaria complementaria y
atributos para 12 tipos (oficina19, apartamento25, casa25, lote11, local21,
bodega19, consultorio19, edificio16, finca25, hotel21, parqueadero9, depósito7).
Sin obligación de investigarlos todos. Catálogo con búsqueda por nombre; configuración
de métodos, academia y captura permanecen separadas. No amplía lectores automáticos.
ResearchScalePolicy conserva cantidades y presencia; ordinales definidos para planta
y calidad de acabados terminados. Vista/acceso/servicio/relieve conservan clases, sin
inventar jerarquías de precio. Acceso vehicular, cargue y restricción separados.
Servidor rechaza clases→ordinal o niveles invertidos; escalas antiguas quedan guardadas
y señaladas, fuera de códigos/conteos listos hasta revisión/adopción explícita.
Huella histórica compatible evita invalidar calificaciones al ampliar el catálogo;
nuevas huellas por factor detectan cambios específicos. Sin DDL ni modificación de
Análisis. Pruebas: 1043 PHP, 151 JS, 181 BD nueva instancia desechable3372,
lint/build y 71,3KB gzip. Navegador: búsqueda de acceso en Bodega, advertencia Vista
anterior, pantalla estrecha contenida, Planta Parcial histórica conservada; atributo
nuevo Aire acondicionado Sí con soporte guarda/recarga sin tocar los anuncios.

Revisión Vista 2026-10-04: orientación nominal fija sin puntaje; panorámica,
paisajística y esquina separados. Nuevo panoramic_view en oficina/consultorio,
apartamento/casa/hotel/finca/edificio. Escalas mixtas antiguas, incluso nominales,
quedan advertidas y excluidas de preparación; originales intactos. Restauración
explícita del catálogo tiene endpoint y confirmación, luego adopción por recorrido.
1046 PHP, 152 JS, 181 BD3373, build/71,3KB. Navegador local confirma restauración,
recarga y separación de vista. No se implementa regresión ni despliega Hostinger.

Vista vigente: Sin vista / Interior / Exterior: calles y avenidas / Exterior:
paisajística, factor categórico único. Sustituye la propuesta de dimensiones
separadas. Paisaje/panorama sólo se conservan en planes previos; validateScope
mantiene compatibilidad por tipo, sin extenderla a bodega u otros tipos.
1047 PHP, 152 JS, 181 BD3374, lint/build/71,3KB; navegador local guardar/recargar.

Capítulo 3 → Factores del sujeto (2026-10-04): captura compartida con comparables
por unidad/tipo, soporte obligatorio para datos conocidos, desconocidos pendientes.
Capítulo 8 consulta la captura y evita otra calificación manual del mismo sujeto.
Migración aditiva 202610040002, CAS/CSRF/propietario. Sin regresión ni recodificar
escalas antiguas. 1073 PHP, 153 JS, 191 BD nueva instancia3377; navegador local
guardar/recargar Vista y Ascensor. Detalles: FACTORES-SUJETO-CAPITULO-3.md.

Apartamento/PH 2026-10-04: ApartmentResearchFactors define catálogo actual y
compatibilidad histórica. Capítulo 3 agrupa privado/parqueo/PH plegable; capítulo 8
identifica alcance en catálogo, comparación y preparación. ph_* no hereda valores
ambiguos antiguos. Servicio binario, acabados únicos, piso preservado. Sin DDL ni
regresión. 1083 PHP, 153 JS, 194 BD3379; navegador local captura PH.

Casa y Vista 2026-10-04: usuario aprobó casa y ordenó Vista 0 Sin vista / 1 Interior /
2 Exterior calles y avenidas / 3 Exterior paisajística. HouseResearchFactors separa
propios/comunes y conserva anteriores; 22 datos de casa/parqueo + 5 PH. Sin DDL.
Revisar/restablecer escalas Vista antiguas; datos anteriores no se recodifican.
1096 PHP, 153 JS, 196 BD3380, lint/build/71,3KB. Navegador ficticio: restablecer
Vista y confirmar dato anterior, captura casa. Siguiente tipo pendiente: oficina.

Vista sin ruido 2026-10-04: catálogo muestra descripción breve y jerarquía 0–3
una sola vez. Restablecer usa navegación fetch con respuesta HTML del servidor,
de modo que desaparecen aviso y botón tras el guardado, sin refresco adicional
manual. No modifica capturas ni escalas históricas de planes. Validación: 1096 PHP,
153 JS, 196 BD3381, lint/build/71,3KB; prueba local restablecer y vista estrecha.

Crear/editar catálogo 2026-10-04: user_research_factors por propietario y versión;
una definición para varios tipos, editor plegable, escala visible y captura en #3.
OfficeResearchFactors aplica 13 atributos aprobados, sin estrato/acceso/aire/ruta.
Acabados ordinal 0–4, obra gris separada. Capturas y planes antiguos conservados,
huellas de definición requieren confirmar datos afectados por edición. Registro de
controladores y contexto de investigación extraídos para cumplir tamaño máximo.
1106 PHP, 153 JS, 209 BD3383; UI local crear/editar/error/recarga/sujeto y móvil.
Ver CATALOGO-FACTORES-USUARIO.md. Sigue revisión por chat de Local, no aprobada aún.

Local comercial aprobado (2026-10-04): LocalResearchFactors define edad, altura libre, frente comercial, vitrina, mezanine, acabados, fuerza comercial, cargue/descargue y cantidad de celdas. Sin PH; parqueo agrupado con características en soporte. Fuerza comercial 0 Baja / 1 Media / 2 Alta exige evidencia de flujo potencial, visibilidad y actividad comercial con misma pauta sujeto/comparables. Capítulos 3 y 8 comparten catálogo; anteriores sólo se muestran si guardados o personalizados. Sin DDL ni regresión. Verificados 1109 PHP, 153 JS, 209 BD en instancia nueva3384; compilación/tamaño 71,3 KB. Próximo tipo para revisar por chat: Bodega. Cuadro por fuente existente en Insumos > 4 Plan de investigación; pendiente revisar visibilidad con el usuario al retomar muestras.

Bodega aprobada (2026-10-04): WarehouseResearchFactors comparte doce atributos en capítulos 3/8: edad, altura, frente, fondo, mezanine, acabados, acceso vehicular, cargue/descargue, muelles, potencia kW, carga admisible kg/m², parqueo agrupado. Áreas terreno/construida separadas como bases. Sin PH ni coeficientes. Conserva retirados guardados y personalización por usuario. 1112 PHP, 153 JS, 209 BD en instancia nueva3385; build/tamaño 71,3 KB y lint. Siguiente tipo pendiente de aprobación por chat: Lote.

Lote aprobado (2026-10-04): LandResearchFactors define frente, fondo, pendiente, exposición 0 Medianero / 1 Esquinero / 2 Tres frentes, acceso vehicular y servicios 0 Sin servicios / 1 Parciales / 2 Completos. Completo requiere agua, energía y alcantarillado operativos; detallar verificación en soporte. Misma captura capítulos 3/8 y recorrido terreno; área permanece base. Anteriores conservados, sin deducir agregados de datos parciales, ni regresión. 1116 PHP, 153 JS, 209 BD nueva3386, lint/build/tamaño71,3KB. Siguiente para revisar por chat: Consultorio; no aprobado aún.

Consultorio aprobado (2026-10-04): mismo catálogo de trece atributos y escalas de Oficina, más rampa de acceso 0 No / 1 Sí verificada en recorrido de entrada. Captura compartida #3/#8, personalización editable y datos retirados guardados conservados. Rampa no acredita accesibilidad completa. Sin DDL/regresión. 1119 PHP, 153 JS, 209 BD nueva3387, lint/build/tamaño71,3KB. Siguiente tipo para revisar por chat: Edificio, no aprobado aún.

Edificio aprobado por lo pronto (2026-10-04): nueve atributos compartidos #3/#8: edad, pisos, unidades interiores, acabados, ascensores operativos cantidad, celdas agrupadas, cobertura planta, vigilancia y rampa. Terreno y construida como bases separadas; uso como filtro. Edición disponible en catálogo. Conserva retirados guardados y personalización; no infiere cantidad de ascensores desde presencia. Sin DDL/regresión. 1122 PHP, 153 JS, 209 BD nueva3388, lint/build/tamaño71,3KB. Pendientes de revisión por chat: Finca, Hotel, Garaje, Depósito; cuadro de comparación entre fuentes pendiente de revisión visible con usuario.

3.4 integrado (2026-10-05): dos subpestañas, actual y preparación módulo8, sin pestaña principal separada; hashes anteriores compatibles. SubjectAttributeResearch revisa los doce tipos, vincula equivalentes y agrega observables anteriores plegados sin jerarquía inventada, con calificación/peso/notas previas visibles. ResearchPlanInput y navegador eliminan tope4; comparación muestra todos por defecto. Fórmula y catálogo valuatorios anteriores intactos; corregida selección rating/weight al recargar y lectura índice tras inicialización Alpine. 1387 PHP/154 JS/210 BD3389/build71,2KB, navegador local guardado/recarga y viewport estrecho. Ver CALIFICACIONES-INTEGRADAS-3-4.md. Sin regresión; Finca/Hotel/Garaje/Depósito especializados siguen pendientes de aprobación. Retomar muestras y cuadro por fuente con usuario.
Reutilización capítulo 3 (2026-10-05): tarjetas compatibles muestran Dato vinculado y edición en origen; SubjectFactorSource compartido con módulo8. Medidas funcionales/edad/niveles, frente-fondo de terreno y observaciones descriptivas previas se leen sin redigitación. Escalas ambiguas y personalizadas requieren precisión; no transforma ratings/pesos. Capturas existentes explícitas se conservan. Abrir investigación fuerza fetch después de confirmar autoguardados. 1410 PHP/155 JS/210 BD3390/build71,3KB y navegador local escritorio/estrecho, cambio origen2→3 reflejado. Sin DDL. Retomar muestras/cuadro por portal y tipos especializados pendientes con usuario.

Datos de origen sombreados (2026-10-05): input readonly gris para características vinculadas, aviso de origen/no digitado aquí; campos conocidos vacíos anuncian no digitado en origen. Sin DDL ni cambios de escritura. 1411 PHP/155 JS/lint/build71,3KB y QA navegador. Datos manuales anteriores conservados.
