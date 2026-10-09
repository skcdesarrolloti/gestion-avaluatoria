# Revisión de continuidad del capítulo 8 · 2026-10-09

Revisión del recorrido y sus mecanismos de conservación, a petición del usuario.
No modifica aplicación, filtros, fórmulas, muestras, datos o migraciones. No se
consultó ni escribió la ficha de producción. No constituye auditoría jurídica o
validación estadística integral de todos los métodos.

## Base existente que debe mantenerse

| Etapa | Trabajo existente y conservación |
| --- | --- |
| Configuración | Organización por componente/parte, métodos y alcances explícitos. Claves estables en methodology_workflow; contraste separado de integración. |
| Academia | Guías y lecturas por método, contexto del sujeto y artículos consultables. Consultar una guía no decide automáticamente un método. |
| Insumos | Lectores y pegado por portal, consolidación explícita de anuncios, ficha principal, lectura de detalles, tabla, fotos, Excel y soportes. |
| Análisis | Grupo aplicado, apartadas/reincorporadas, factores, descuentos, valores individuales, historial, mapa, descriptiva, regresión y diagnósticos existentes. |
| Entregable | Memoria y conclusión persistentes por componente; redacción consolidada del numeral 8; aviso cuando cambia la evidencia. |

La navegación interior de siete entradas es una organización de M4, no reemplazo
del capítulo 8 ni una autorización para rehacer captura o preparación anterior.
Motores pendientes de Costo/Renta/Residual no deben presentarse como terminados.
Regresión simple, inferencia, validación externa y aplicación al sujeto tampoco
se implementaron mediante los menús nuevos.

## Los 71 y los 34

La captura histórica aportada muestra 71 inmuebles recogidos y una ejecución con
34 muestras conservadas, 34 valores unitarios y cero pendientes para esa base.
No permite determinar por sí sola qué decisión produjo esa reducción.

- ComparableIntake distingue selección para análisis de consolidación.
- methodology-intake-analysis recibe grupos seleccionados o grupos completamente
  confirmados y elige la ficha principal de cada uno.
- marketAnalysisTable recupera scope/applied_scope/regime_applied, factores,
  apartadas/reincorporadas, historial y codificación guardados.
- workingRows obtiene el grupo aplicado; la vista raw de Información recogida
  puede mostrar todo el conjunto recibido sin reincorporarlo al cálculo.
- La selección no congela el número 34: el grupo se reconstruye con el criterio
  aplicado, régimen/datos actuales y reincorporaciones explícitas. Debe contrastarse
  con el historial real antes de declarar que las 34 actuales son las mismas.

La cabecera nueva utiliza analysisActiveRows, cuyo alcance cambia al consultar raw;
por ello puede rotular el total consultado como grupo actual. La guía de recepción
también cuenta notas de todo el conjunto recibido. Es una confusión de presentación
identificada, no evidencia de que se haya borrado o reaplicado la depuración.

No corresponde repetir la revisión general de los 71 ni pulsar nuevamente Aplicar
depuración como requisito para usar una preparación ya aplicada y guardada.

## Memorias y persistencia

La memoria analysis y conclusión de M4/M5 se conservan por componente en el flujo,
con versión optimista y huella de evidencia. Las muestras tienen una matriz común,
alcance por componente y metadata aditiva. El envío desde Análisis incluye los
anuncios originales y datos de las filas fuera del grupo; no basta renderizar sólo
la tabla visible para guardar.

Las notas/conclusión del ejercicio estadístico y las ejecuciones descargables son
otra capa: no sustituyen la memoria persistente del componente ni se integran
automáticamente al informe final. Deben conservarse ambos accesos.

## Comprobación concreta ejecutada

Escenario ficticio ejecutado directamente con las funciones existentes: 71 filas,
34 PH y 37 no PH, sujeto PH y preparación aplicada guardada. Navegar por los
menús mantiene 34 en workingRows y conserva selección/historial. Consultar raw
muestra 71, manteniendo workingRows en 34; volver a resultado muestra 34.
No se llamó a aplicar filtro, reincorporar ni guardar. No demuestra el criterio
real del expediente del usuario ni prueba persistencia de su producción.

## Trabajo siguiente, conservador

1. Contrastar el historial real del expediente: última etapa aplicada, criterio,
   fecha, cantidad, factores, disponibles y apartadas/reincorporadas.
2. Corregir sólo presentación/guía: priorizar grupo aplicado y distinguir banco
   recibido, grupo usado y valores completos. Historial visible sin recalcular.
3. Mantener fórmulas, agrupaciones, selección, capturas y memorias. Cualquier
   problema técnico encontrado debe describirse y probarse antes de cambiarlo.
4. Recomendar una actualización únicamente después de esa corrección y sus
   pruebas. En esta revisión no se ofrece una nueva versión operativa.

## Archivos principales contrastados

AppraisalValuationMethodologyController, valuation-methodology,
methodology-navigation, MethodologyWorkflow, MethodologyComparableScope,
MethodologyWorkflowRepository, AppraisalComparableRepository,
ComparableIntake, comparable-intake, comparable-consolidation,
methodology-consolidation, methodology-research-consolidated,
methodology-intake-analysis, market-analysis-table, analysis-filter-history,
analysis-statistical-history, analysis-course, methodology-analysis-statistics,
methodology-analysis-retired, methodology-market-analysis,
MethodologyWorkflowReport y valuation-methodology-deliverable-preview.

## Corrección posterior de presentación

Tras recibir las capturas del historial, se constata que la reducción a 34 aparece en Filtro de muestras, régimen del sujeto PH, del 06/10/2026. La captura actual muestra 34 completos/0 pendientes y aviso simulado; no se declara evidencia verificada. Se implementa count del grupo aplicado independiente de raw, antecedentes y ayuda plegados, sin recalcular o cambiar criterios. Las indicaciones de no actualizar anteriores corresponden a la revisión previa; la nueva versión se entrega tras pruebas.
