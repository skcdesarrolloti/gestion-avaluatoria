# Perito responsable: antecedentes y anexo judicial

Implementado el 1 de octubre de 2026. Fuente consultada: [Ley 1564 de 2012,
compilación oficial de Cancillería](https://www.cancilleria.gov.co/normograma/compilacion/docs/ley_1564_2012.htm).
Revisión focalizada del artículo 226, artículo 50 y reglas relacionadas de
los artículos 227–233 y 235. No constituye una revisión de todo el CGP ni
certificación automática de admisibilidad o cumplimiento del dictamen.

## Uso

1. Maestros → Perito responsable → Requisitos judiciales · CGP → elegir perito.
   Completar localización, profesión, experiencia y referencias de soportes.
   Agregar publicaciones y designaciones/participaciones externas o anteriores.
   Cada campo se autoguarda a los 800 ms, con confirmación del servidor y versión.
2. En el expediente guardar finalidad **Judicial** y perito asignado. Abrir
   «Preparar anexo judicial y registrar presentación» y marcar «Dirigido a un
   juzgado». Completar proceso, participantes, métodos, documentos y declaraciones.
   Las publicaciones pertinentes se seleccionan por dictamen; no se presumen.
3. Guardar y actualizar la vista previa. Exportar el anexo o cada apartado en TXT
   UTF-8 para incorporarlo al informe. También hay acceso desde Entregable si la
   finalidad es Judicial. Los adjuntos se relacionan pero **no se empaquetan** en
   esta exportación: títulos, experiencia y documentos deben acompañar el dictamen.
4. Después de la presentación efectiva, indicar fecha y referencia de su constancia,
   confirmar revisión/firma/presentación y pulsar «Registrar presentación al juzgado».
   La acción no radica ni envía documentos. Añade el caso al historial y conserva
   una copia del anexo de solo lectura. Guardar/exportar borradores no añade casos.

## Criterios normativos

- Academia cerrada inicialmente con texto de los diez numerales del art. 226,
  explicación operativa y fuente oficial; reglas relacionadas separadas como resumen.
- 226.1–3: identidad y localización del perito, participantes, profesión e idoneidad;
  no sustituir todos estos datos por el número RAA.
- 226.4: publicaciones relacionadas con la materia en los diez años anteriores a
  la fecha del dictamen. Selección explícita y control de referencias modificadas.
- 226.5: casos de designación **o participación** de cuatro años, con despacho,
  partes, apoderados y materia. No restringir a casos firmados ni a la misma materia.
  Los antecedentes externos o una designación previa al envío se ingresan manualmente.
- 226.6: procesos por la misma parte o apoderado y objeto; declaración específica,
  sin aplicar el límite de cuatro años del numeral anterior.
- 226.7: causales pertinentes del art. 50; no equivale a pertenecer a la lista de
  auxiliares. No afirmar que el RAA comunica automáticamente listas a juzgados.
- 226.8 y 9: declaraciones separadas frente a peritajes anteriores y ejercicio
  habitual; las variaciones requieren justificación. Se permite declarar ausencia
  de peritajes anteriores, sin inventar que los métodos fueron iguales.
- 226.10: relacionar **y adjuntar** documentos. Juramento de independencia y
  convicción se entiende por la firma; una casilla no firma el dictamen.
- Sin respuestas negativas prellenadas. La ausencia de publicaciones/casos solo
  se expresa después de revisión explícita del historial por el perito.
- Art. 50: no permite confirmar presentación con causal declarada sin resolver.
  Arts. 227–231: el aplicativo no calcula plazos de actuaciones judiciales.
  Arts. 232, 233 y 235: valoración, colaboración y objetividad; llenar campos no
  sustituye el análisis profesional ni garantiza aceptación por el juez.

## Datos, seguridad y actualización

Nueva migración reintentable `202610010002_create_judicial_expert_records.php`:
`judicial_expert_profiles` y `appraisal_judicial_records`. No modifica ni borra
comparables, fotos, RAA o expedientes previos. Aplicar `php bin/console.php migrate`
o el mecanismo existente `AUTO_MIGRATE=true` tras actualizar el programa.

Antecedentes privados por propietario + perito, aunque el maestro RAA existente
sea común. No habilita colaboración entre funcionarios. Cada expediente se
autoriza por propietario. Rutas protegidas, CSRF y versiones optimistas/409.
El anexo se vincula al perito asignado y no transfiere declaraciones al cambiarlo.
No almacena estos datos en localStorage. La presentación guarda copia del texto;
actualizaciones del maestro no alteran esa copia. Un segundo envío no duplica
historial. Esta versión no permite enmendar una presentación registrada.

## Verificación

- PHP: pruebas de ventanas 10/4 años, declaraciones, referencias obsoletas,
  justificación, recuperación real de vistas y escape HTML; suite completa.
- MySQL desechable `ga_test_app`/`ga_test_auth`, puerto 33328: migración repetida,
  conservación de datos, aislamiento, conflictos, historial solo tras presentación,
  congelación de copia y no duplicación; 39 comprobaciones.
- HTTP local: login, TXT por campo, campo inexistente, finalidad no judicial,
  rechazo CSRF y edición obsoleta; sin tocar producción.
- Chrome local: nueva pestaña de Maestros, antecedente dinámico, autoguardado y
  recarga, anexo y presentación efectiva, aparición del caso en el maestro.
  Inspección escritorio y ancho CSS 390 px sin desbordamiento del formulario.
- `php tests/run.php`, `npm test`, lint PHP, `npm run build`, `npm run check:size`.
  CSS + JS: 56,3 KB gzip. Los controles existentes de autoguardado cubren errores
  de red y conservación de cambios ante conflictos.

Publicar el código en Git no actualiza el hosting; comprobar migración y recorrido
con los datos reales después de que el responsable actualice el programa.
