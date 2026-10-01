# Revisión de Resolución IGAC 941 de 2026 y comparables

Revisión documental y de código: 01/10/2026. Base examinada: 4583dde.
Conclusión: la captura es una base operativa; **no está completo el desarrollo del
método de mercado ni puede certificarse cumplimiento por contar muestras guardadas**.
Este documento distingue obligaciones, funciones existentes y desarrollo pendiente.
No sustituye la verificación del expediente concreto ni la responsabilidad del avaluador.

## Fuentes y alcance

- [Ficha oficial IGAC, vigente](https://www.igac.gov.co/node/53595): resolución firmada
  del 31/07/2026, 117 páginas (acto y anexo), y anexo separado de 62 páginas.
- [Anexo oficial](https://www.igac.gov.co/sites/default/files/transparencia/normograma/Anexo_Final_.pdf).
- [Reproducción del Diario Oficial publicada por Camacol](https://camacol.co/sites/default/files/descargables/IGAC-Resolucion-2026-N0000941_20260731_Diario_Oficial-N053573_20260801.pdf):
  el encabezado interior dice edición **53.575, 03/08/2026**, pp. 52–80. El nombre del
  archivo contiene otra fecha/número: no se usó el nombre como fecha de vigencia.
  Se utilizó para extraer texto del acto escaneado; los arts. 16–21 se cotejaron
  visualmente con las pp. 17–22 del original IGAC.
- Se leyeron los arts. 1–61 y el anexo, incluidos glosario, procedimientos y ejemplos.
  Las fórmulas no se implementan en esta entrega. Los ejemplos del anexo no son
  parámetros de mercado, tasas ni precios predeterminados para el aplicativo.
- Arts. 1–2 y 7: comprobar el ámbito y marco del encargo; no afirmar obligatoriedad
  indistinta para cualquier finalidad. Art. 59: conservar el régimen transitorio.
  Art. 60: vigencia desde publicación, no desde expedición; deroga 620/2008,
  art. 13 e incisos 1–3 del art. 14 de 898/2014 y disposiciones contrarias.

## Contraste prioritario del módulo de comparables

| Requisito | Evidencia en código | Brecha / criterio de aceptación pendiente |
| --- | --- | --- |
| Arts. 16–17: datos recientes, corroborados, similares/comparables | Captura por portal, estado, notas y motivo de rechazo | Registrar corroboración, responsable, fecha y decisión motivada. Un aviso importado sigue por verificar. |
| Art. 17 y anexo 2.1: localización, fuente, fecha y contacto | `neighborhood`, `address_hint`, `project_name`, URL, contacto, fechas, coordenadas y precisión | Municipio del comparable y fuente de localización explícitos; no dar por acreditado el barrio solo por el filtro del portal. Salvedad si la fuente limita precisión. |
| Art. 17: pedido, negociación y transacción | Solo `price_amount`, unidad y operación | Separar pedido, porcentaje justificado, valor negociado y valor transado; identificar tipo de dato. No aplicar descuento estándar ni confundir oferta con transacción. |
| Arts. 17–19 y anexo: áreas diferenciadas | Un único `area_m2`, PH, número de parqueaderos y notas | Área privada construida/libre, terreno, construcción, anexos/cultivos cuando apliquen; unidad y procedencia. No dividir por área cubierta del portal como si fuera privada. |
| Arts. 17, 19: depuración PH | `ph_regime`, `parking_relation`, atributos | Valores de garajes, depósitos y otras unidades, derechos/restricciones, soporte y descuentos sobre valor negociado. Memoria para obtener valor integral por m² privado. |
| Arts. 18–19: NPH | Clasificación PH/NPH | Desagregar terreno/construcción/anexos/cultivos y áreas; justificar relación terreno/construcción para análisis integral complementario. |
| Arts. 14.13, 17 y anexo 2.1: evidencia | Fotos privadas en BD, pie de imagen y URL por muestra | Se pueden pegar capturas, pero no hay clasificación de evidencia ni control de contenido/completitud. Conservar captura con URL, pedido, áreas, ubicación y fecha; foto decorativa no basta. |
| Art. 17: tabla de comparación y memoria | Matriz de captura, notas y exclusiones | Tabla trazable de datos originales, clasificación, depuración, operaciones, resultados y razones; exportación legible dentro del informe. |
| Arts. 20–21: suficiencia, estadística y adopción | Guías y metas internas 15/60; no motor estadístico en estos servicios | Justificar suficiencia; estadísticos según pertinencia. CV urbano ≤7,50% / rural ≤10% para adoptar media. Alternativa sustentada, atípicos y condiciones de mercado. No exclusiones automáticas para forzar CV. |
| Art. 20 §2: modelos avanzados | No modelo valuatorio implementado en captura | Si se incorpora: manual auditable, supuestos, limitaciones, calidad/sesgo, métricas verificables y validación crítica profesional. No presentar predicción sola como avalúo. |
| Anexo 2.1: no homologación mediante factores | `analysis_factor` clasifica una variable; no multiplica valores | Mantenerlo descriptivo. No crear multiplicadores por piso, vista, área o amenidades para igualar ofertas. Distinguir procedimientos expresos de arts. 17.i, 35 y anexo 3.1/6.2. |
| Art. 36: liquidación PH | Expediente PH y composición del sujeto | Distinguir matrícula independiente de bien común de uso exclusivo: este último implícito en valor integral, no liquidarlo separado. Áreas privadas construidas/libres con valor diferenciado; licencias y reglamento concordantes. |
| Arts. 19.2.c, 36 §1 y 37: casos especiales | Etiqueta PH/NPH | Condominios y NPH físicamente asimilable a copropiedad necesitan ruta técnica específica, áreas estimadas y costos de adecuación sustentados. No mezclar automáticamente por parecido. |

La detección de URL repetida y coincidencia precio/área/sector es ayuda de captura,
no prueba jurídica de identidad ni criterio normativo suficiente de comparabilidad.
La depuración entre portales debe conservar el vínculo a las fuentes y la decisión,
sin borrar evidencia útil ni descartar inmuebles distintos por compartir precio/área.

## Recorrido de toda la resolución y pendientes conexos

Los estados siguientes son de cobertura funcional, no un dictamen sobre todos los
expedientes. La revisión de implementación se concentró en comparables, guías,
selección del método, persistencia y soportes; los demás módulos requieren pruebas
de aceptación propias antes de declararlos completos.

| Artículos | Exigencia / incidencia | Cobertura y siguiente verificación |
| --- | --- | --- |
| 1–4 | Objeto, ámbito, anexo integral y definiciones | Biblioteca disponible; falta decisión de aplicabilidad por encargo y versión normativa. |
| 5 | Objetividad, fuentes, transparencia, integridad, independencia, RAA | Hay expediente y soportes; faltan cierre transversal, declaraciones y control de suficiencia que permita reproducir el valor. |
| 6–9 | Solicitud, marco, documentos, contacto y plazo; pacto distinto permitido | Verificar completitud y fecha de recepción, encargo escrito, particularidades retroactivas/parciales/plusvalía y plazo aplicable. No imponer 30 días sin considerar parágrafo. |
| 10 | Visita e inspección; excepción escrita del solicitante e información suficiente | Falta puerta de control documentada para impedir concluir si no hay acceso, autorización o información suficiente según el caso. |
| 11–12 | Identificación física, jurídica, normativa, áreas, construcciones, cultivos y restricciones | Existen módulos del sujeto/PH/entorno/norma; falta cotejo transversal y trazabilidad de inconsistencias y áreas adoptadas. |
| 13 | Etapas, visita por RAA, fuentes, selección justificada y garantía de calidad | La recomendación del método depende hoy de tipología/negocio; no debe tratarse como selección definitiva ni prueba de viabilidad. |
| 14–15 | Informe mínimo, memoria, anexos, métodos y fuentes expresas | Guías disponibles; falta informe integral reproducible con memoria, anexos, firmas, RAA y control final. |
| 16–21 | Mercado | Brechas detalladas arriba; lectura literal de estos seis artículos incorporada. |
| 22–26 | Renta, capitalización directa y FCD | Guía académica; faltan contratos/mercado, coherencia bruto-neto, tasas soportadas, flujos, valor terminal y memoria. Art. 24.e aplica topes a vivienda urbana. |
| 27–30 | Costo, reposición/reproducción, vida útil y depreciación continua | Hay módulo de conservación y catálogo; no equivale a presupuesto y liquidación completa. Validar Ross-Heideck, VUP sustentada y excepción BIC sin depreciación por edad. |
| 31–34 | Residual estático/dinámico y terreno en bruto | Guía; falta factibilidad real, mayor y mejor uso, ventas, costos/cargas, utilidad, tiempos y tasa sustentada. Evitar doble adición de construcción; cumplir condiciones del art. 34. |
| 35 | Dotacionales | Ruta excepcional solo si faltan datos suficientes o ingresos imputables; factor de relación por uso no es permiso general de homologación. |
| 36–37 | PH y NPH asimilable | Brechas descritas arriba; contrastar análisis de referentes con liquidación del sujeto para evitar duplicidad de unidades/comunes/terreno. |
| 38 | Canon | Investigación y tasa o mercado de arriendos; no seleccionar automáticamente capitalización solo porque se busca canon. |
| 39–42 | VIS, BIC, expansión sin plan parcial y maquinaria | Rutas específicas por encargo pendientes de cierre: VIS terreno+construcción; BIC con restricciones; expansión uso agropecuario/forestal; maquinaria RAA pertinente y sin duplicar equipos incluidos. |
| 43–45 | Compensaciones, cesiones y derecho de superficie | No quedan resueltos con captura genérica. Exigen soportes, fechas/tasas, derechos y áreas del encargo, procedimiento y memoria propios. |
| 46–48 | Rural agropecuario, renta del terreno y cultivos | Requiere AHT/suelos, agua, infraestructura, producción, costos, rendimientos, ciclos, fuentes y separación de componentes. No usar oficinas urbanas como plantilla suficiente. |
| 49 | Suelo de protección | No castigos porcentuales por la sola restricción; determinar aprovechamiento permitido, características y método conforme a cada caso. |
| 50–56 | Plusvalía | P1/P2, zonas homogéneas, acciones urbanísticas, fechas y distribución sobre área legal; módulo específico y pruebas de cálculo pendientes. |
| 57 | Revisión e impugnación | Expediente completo y trazable; términos/competencia según régimen. No equivale al botón de revisar muestras. |
| 58 | Entrega de información al OIC por el solicitante | Falta salida según formato/procedimiento oficial aplicable; no enviar automáticamente información desde captura. |
| 59–61 | Transición, vigencia, derogaciones y publicación | Consulta disponible; registrar régimen por fecha de inicio y encargo. Corregida fecha de vigencia del catálogo a publicación 03/08/2026. |

## Anexo revisado

- Glosario (pp. 6–16): comparable no exige identidad absoluta; área privada no es
  cualquier área publicada; valor integral cambia según régimen.
- 2.1 (pp. 17–20): tres esquemas mínimos rural NPH, urbano NPH y urbano/suburbano PH;
  salvedades explícitas ante información limitada y exclusión de homologación.
- 2.2–2.4 (pp. 20–40): FCD, vidas útiles, VUP, estados, depreciación y residual.
  VUP no automática, desde el umbral previsto y con estados admisibles y sustento;
  revisar las excepciones BIC. Las tablas orientadoras no reemplazan investigación.
- 3 (pp. 40–45): dotacionales y maquinaria con procedimientos específicos.
- 4 (pp. 45–55): estructuras de costos y renta agropecuaria; cifras didácticas no reales.
- 5–6 (pp. 55–59): plusvalía, compensación posterior y forma del lote.
- 7 (pp. 59–62): bibliografía. Las referencias no se convierten por sí solas en reglas
  automáticas ni justifican copiar valores de los ejemplos al expediente.

## Actualización posterior: captura 8.3

Se incorporaron áreas por régimen y fuente/salvedades; coordenadas y evidencia
se diligencian en «4. Mapas y evidencia» sobre la misma muestra. Ver
[alcance y pruebas](COMPARABLES-MAPAS-AREAS.md). Se conserva el área publicada.
Continúan pendientes negociación, desagregación monetaria, clasificación avanzada
de soportes, memoria de cálculo y decisiones trazables en 8.4. Esta ampliación
no cierra por sí sola todas las brechas normativas.

## Entrega inicial y prioridad propuesta

1. Se incorpora flecha de lectura completa para arts. 16–21 en 8.1 y en Buscar 8.3,
   con fuente y página. Cerrada inicialmente, scroll interno y teclado.
2. Se aclaran homologación, carácter optativo de estadísticos, CV para media y
   metas internas; se agrega panel plegable de pendientes y ayuda para evidencia.
3. Siguiente implementación: ampliar datos por régimen y negociación con migraciones,
   conservar originales y clasificar soportes; luego decisiones trazables de depuración.
4. Después: memoria de cálculo y adopción del valor, control de calidad e informe.
   No se implementaron fórmulas, aprobación, cambios de datos ni migraciones en esta entrega.

Archivos de contraste: `AppraisalComparableInput`, `AppraisalComparableRepository`,
`comparable-review.js`, `comparable-candidate-review.js`, `comparable-photos.js`,
`AppraisalMethodologyChapterReport`, `AppraisalMethodologyResolution941Guide`,
`AppraisalComparableSampleDesignGuide` y vistas de matriz/búsqueda/academia.

Validación: lint PHP correcto; `php tests/run.php` 373 verificaciones;
`npm test` 99 pruebas; `npm run build` y `npm run check:size` correctos,
55,8 KB gzip. Prueba local de vista real: apertura/cierre con Enter del art. 16,
lectura del art. 17 y enlace al PDF. Revisión visual de escritorio y comprobación
DOM en pantalla estrecha: una columna, sin desbordamiento horizontal y texto con
scroll interno. La captura de pantalla con viewport móvil falló en la herramienta;
la comprobación responsive fue DOM, no imagen. Sin cambios de persistencia/esquema,
no se ejecutaron pruebas de bases de datos. Pendiente actualización del hosting.

## Ampliación a los cuatro métodos — 1 de octubre de 2026

Se amplía la revisión anterior de los 61 artículos y el anexo con lectura íntegra
plegable de los artículos 22–34: renta, costo y residual. Los textos se cotejaron
visualmente con las páginas 23–34 del PDF firmado del IGAC; se conservan parágrafos
 y ecuaciones, adaptando su presentación a texto. Fuente institucional:
https://www.igac.gov.co/node/53595 y anexo técnico oficial Anexo_Final_.pdf.

Cada método tiene su propia revisión plegable de requisitos, insumos disponibles
 y trabajo pendiente. El panel común conserva ámbito, preparación, informe,
casos especiales y cierre normativo. Ningún panel acredita cumplimiento automático.

Precisiones incorporadas:
- Renta: coherencia bruta/neta, alcance del límite de vivienda urbana, dos tasas
  distintas en FCD, valor continuo y gastos mínimos.
- Costo: CT, D y VT separados; reposición/reproducción, presupuesto localizado,
  alternativas justificadas ante falta de documentación, vida remanente y VUP.
  El anexo 2.3.2 admite evaluar VUP desde 90% de vida de referencia, con condiciones
  de conservación 2,5–4,5 y exclusión de BIC. La excepción del art. 30 excluye edad,
  pero conserva el análisis del estado. Las ecuaciones se transcriben para consulta;
  no se implementa un calculador ni descuentos automáticos por obsolescencia.
- Residual: factibilidad, estático/dinámico, utilidad y trazabilidad; resultado y
  excepción del art. 33, sin sumar nuevamente construcción; condición de uso de VTB.
- Mercado: captura 8.3 y análisis 8.4, corroboración, negociación y memoria pendientes.

Validación: PHP 437 verificaciones; JavaScript 106 pruebas; lint, build y tamaño
correctos (57,2 KB gzip). Vista real local: navegación por los cuatro métodos,
apertura/cierre de artículo 30 y requisitos de costo; revisión visual de escritorio.
La captura móvil agotó el tiempo de la herramienta; DOM en ventana estrecha reportó
585 px de viewport y 562 px de contenido, sin desbordamiento horizontal. No se
presenta esto como una captura móvil validada. Sin cambios de BD ni migraciones,
no aplica la prueba de persistencia. Pendiente actualizar el hosting.
