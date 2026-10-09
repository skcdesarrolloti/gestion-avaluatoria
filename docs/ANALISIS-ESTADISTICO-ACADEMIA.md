# Apartado 2 · Análisis estadístico y academia trazable

Entrega incremental del 09/10/2026. Amplía el recorrido existente de siete pasos;
no cambia muestras, fórmulas, criterios de exclusión ni estructura de persistencia.

## Organización

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
