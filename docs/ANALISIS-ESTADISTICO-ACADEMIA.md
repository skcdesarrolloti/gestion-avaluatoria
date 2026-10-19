# Apartado 2 · Análisis estadístico y academia trazable

Entrega incremental del 09/10/2026. Amplía el recorrido existente de siete pasos;
no cambia muestras, fórmulas, criterios de exclusión ni estructura de persistencia.

## Organización

Actualización 15/10/2026: un selector reemplaza las seis pestañas de estadística,
conservando sus pasos, orden, navegación anterior/siguiente y paso guardado.
«Revisar o corregir datos» abre la misma tabla de preparación dentro del paso;
Revisar inmueble permanece en Estadística. Precio, área, descuento y atributos se
corrigen en la fila con evidencia. El cálculo previo sigue visible, marcado como
desactualizado, hasta actualizarlo. El mapa y sus soportes se conservan.

Tendencia central añade un acordeón de operaciones: suma/n, posiciones de la
mediana, suma y cantidad del tramo acotado, suma de logaritmos y media geométrica.
Para cada centro se muestran los valores individuales ordenados, su origen,
error absoluto, error porcentual, suma de porcentajes y división entre n. La
acotada sigue comparándose con todos los valores, no solo con su tramo central.
No se cambia ningún estimador, criterio de selección o convención de cuartiles.
Dispersión muestra inmuebles mínimo/máximo, resta del rango, suma de cuadrados,
divisor n−1, raíz de varianza y cociente del CV. Estos cuadros se montan al abrirlos
y leen la ejecución guardada, sin mezclarla con correcciones posteriores.

Referencia de presentación: lectura de Hoja1 del archivo del solicitante
«Libro de Ejemplo de Regresion Lineal.xlsx», C3:C36; centros C37:C40; errores D:K;
promedios H37:K37 y rango C43. No se modifica ni importa el libro a un expediente.
La comprobación independiente de sus 34 valores reproduce los cuatro MAPE y el
rango de la lectura inicial. Se conserva academia/fuentes plegables junto al cálculo.

- Un acordeón jurídico/técnico compartido: marco nacional, NTS, IVS y límites.
- Un acordeón de academia del paso seleccionado, disponible antes de calcular.
- Cada paso: ejemplo didáctico, significado, revisión y fundamento con alcance.
- Los datos y controles existentes continúan debajo; precisión conserva su academia
  detallada y el cálculo opcional de remuestreo en un acordeón independiente.
- La memoria HTML incluye una nota de fuentes y límites mediante texto escapado.
  No es una decisión guardada de aplicabilidad ni una declaración de conformidad.

## Cotejo de fuentes

La [ficha oficial IGAC](https://www.igac.gov.co/node/53595), consultada el
09/10/2026, identifica la Resolución 941 de 2026 como vigente. La descarga mediante
la herramienta web no respondió; el contenido prioritario se cotejó visualmente
con el PDF local aportado de 117 páginas, usando sus páginas 21–22 y 73/76.
No se toma el título de una diapositiva como sustituto del acto.

Fuente local: `Nueva Ley Valuatoria/R 0941 - 2026 SE FIJAN LOS MÉTODOS Y LAS
CONDICIONES DE ELABORACIÓN Y PRESENTACIÓN DE AVALÚOS.pdf`.

| Paso | Referencia nacional y alcance identificado |
| --- | --- |
| Preparación | Arts. 16–19; PH19.2.a–b, excepciones19.2.c/NPH con ruta propia. |
| Bloques | Art.20.c, herramienta optativa; comparabilidad del19. Sturges no es mandato. |
| Centros | Art.20.a y21; no adopción automática de mediana. §1 menciona valor unitario de terreno. |
| Dispersión | Art.20.b y21: límites de CV para adoptar media, más revisión de mercado. |
| Sensibilidad | Art.20.c–d y21§1 según alcance. Umbrales1,5/3,5 del ejercicio no ordenan retiros. |
| Precisión | Art.20 y§1; t/bootstrap/10000 no se presentan como exigencias textuales. |
| Memoria | Art.21§1–2 según alcance y anexo2.1; trazabilidad y razones. |

Las referencias a los artículos reutilizan los enlaces originales de
Resolution941Reading. No se duplican transcripciones completas en el apartado2.
Arts.1–2/7/59 y los antecedentes del encargo requieren revisión para determinar
aplicabilidad y transición; este cambio no decide ese punto automáticamente.

Anexo2.1: páginas impresas17–20 (PDF integrado73–76). El cierre de la página20
distingue una depuración/clasificación/comparación/análisis rigurosos de un proceso
automatizado de homogeneización. No se interpreta como prohibición de toda
operación expresamente prevista en el acto ni se añade un tratamiento por factores.

### NTS examinadas, sin certificación de edición vigente

Fuentes locales en `Nueva Ley Valuatoria/Normas Sectoriales/`:

- `02 NTS S03 Contenido Inf Valuacion.pdf`: fecha editorial10/09/2009;
  PDF11,7.1.8 y7.1.10: datos/análisis/métodos/argumentos y normas/excepciones.
- `09 NTS M01 Metodologias Val.pdf`: fecha editorial12/02/2016;
  PDF30,sección8: informe/revelaciones; PDF32 en adelante,anexoB informativo:
  investigación/modelación/supuestos. No convertir recomendaciones en mandato.
- AnexoA de M01,PDF31: tratamiento por factores; advertencia de contraste con
  anexo2.1 de la Resolución. No incorporación automática.
- NTS I01: numerales pertinentes pendientes de revisión específica. No se
  incluye una referencia numérica que aún no se haya cotejado.

La búsqueda pública no permitió confirmar la última edición NTS. En pantalla se
expresa edición examinada y verificación pendiente; no conformidad universal.

### IVS de estudio y límite de la traducción

Fuente aportada: `Curso Residual y Renta/Curso Resudual/
INS_2025_ESPANOL_Version-Preliminar.pdf`,166páginas, portada con aplicación
31/01/2025. La advertencia de la traducción identifica la inglesa como oficial.

- IVS104,30.1–30.3 y50.1–50.3: PDF55–56, calidad/selección/documentación de datos.
- IVS106,20.1–20.3 y30.1–30.6: PDF62–64, documentación y presentación.
- Son referencias de estudio mediante traducción preliminar. Cotejo de numerales
  con original oficial y edición del encargo pendiente antes de declarar conformidad.
- Art.20§2 remite a IVS vigente para las herramientas de ese parágrafo; esta
  referencia no hace obligatorio indistintamente todo estándar internacional.

El [IVSC, consulta2024](https://www.ivsc.org/wp-content/uploads/2024/07/Agenda-Consultation-2024-v.11.pdf)
identifica IVS104/105 de la edición aplicable desde2025; el proyecto público2028
es un borrador de consulta, no una sustitución automática de esa numeración.

## Conservación y límites funcionales

Los ejemplos8/10/12 son texto didáctico: no agregan ni simulan nuevas muestras.
Los cocientes existentes siguen sobre área publicada, con avisos de base pendiente.
No se ejecutó contra datos reales del hosting. No DDL, migraciones ni campos
editables nuevos. Notas/conclusión del ejercicio sólo en ficha abierta y descarga;
se mantiene la advertencia existente sobre falta de autoguardado de esas notas.
No añade detección completa de incumplimientos ni decisiones de aplicabilidad.

## Verificación

`php tests/run.php`:1555verificaciones. `npm test`:228pruebas; se agrega una
prueba de memoria que conserva límites de fuentes, escapa texto y no altera datos.
Lint PHP, `npm run build`, `npm run check:size`:78,4KB gzip inicial.
No se ejecuta tests/database.php: no cambian persistencia ni migraciones.
La verificación de interfaz usa un servidor local con tres inmuebles ficticios.
Academias disponibles antes de calcular en los siete pasos; apertura de fuentes
NTS/IVS y de fundamento del CV con enlaces originales. Escritorio y móvil375px
útiles sin desbordamiento horizontal, consola sin errores. No se confirma
despliegue al hosting mediante la publicación al repositorio.

## Sensibilidad y concepto · 19/10/2026

El paso 5 conserva el grupo y la ejecución guardada. Reutiliza el gráfico de
Dispersión con franja Q1–Q3, mediana punteada, media y límites de 1,5 RIC.
Se muestran cuartiles/límites y tres centros con sus operaciones y diferencias
absolutas con signo y relativas respecto de la media; media cero deja el porcentaje
no estimable. La recortada conserva el criterio existente: piso(0,20n) por cola.
El acordeón de comprobación muestra todos los valores ordenados y cuáles entran
en ese promedio; se monta al abrir. No crea filtros ni exclusiones.

El concepto descriptivo automático cuantifica diferencias, separa estar fuera de
Q1–Q3 de cruzar los límites del RIC y conserva las señales robustas. No clasifica
sensibilidad mediante umbrales inventados ni certifica el CV. Recuerda si hay datos
simulados. Se deriva de los datos de la ejecución, se reproduce al restaurarla y
se incluye en la memoria; las consideraciones del analista conservan su guardado
existente. No agrega campos almacenados ni modifica esquemas o versiones.
MAD, asimetría y curtosis se conservan como diagnósticos complementarios plegables;
academia contextual y edición por inmueble siguen disponibles en el mismo paso.
Los diagnósticos del paso 5 incluyen un cuadro de explicaciones por indicador:
resultado, definición y lectura calculada de la ejecución (asimetría, exceso de
curtosis, MAD, Q1/Q3 y z robusto). Distingue MAD cero de ausencia de señales y
no infiere errores o exclusiones del signo de una medida. Conserva ejemplo y
fórmulas existentes; en móvil apila cada indicador. Las explicaciones también se
incluyen en la memoria descargable y usan los datos ya guardados, sin persistencia
nueva ni recálculo del estudio.
Vista previa al revisar: el paso 5 compara ejecución guardada y cambios actuales
(n, media, s, CV, Q1/Q3), con valor unitario por fila y CV dentro del editor.
Combina borradores numéricos de varias filas sin escribirlos en la ejecución;
los cambios sin aplicar se identifican. Cualquier fila incompleta bloquea el CV
de revisión, sin excluirla. Se compara con 7,5 % como referente solicitado, sin
certificación automática o nuevos filtros. Aplicar corrección conserva evidencia
y autoguardado existente; Actualizar análisis incorpora las correcciones aplicadas
a la ejecución y su memoria. La vista previa no agrega almacenamiento.