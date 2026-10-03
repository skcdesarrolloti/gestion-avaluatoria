# Plan de valoración y consulta por paso

Entrada inicial del capítulo 8: `stage=plan`. Cuatro pasos visibles: organizar,
definir métodos/alcances, desarrollar cada componente, consolidar. M1–M5,
C1–C5, R1–R5 y Re1–Re5 siguen como recorridos interiores, con unidad/parte visible.
Las rutas antiguas permanecen; no se borran lectores, matriz, Excel o fotografías.

## Casa: una unidad, dos partes de valoración

En el plan seleccionar «Terreno y construcción por separado», esperar autoguardado
confirmado y usar «Actualizar plan». Se habilita exclusivamente para unidad
principal con NPH confirmado. PH y anexos mantienen su alcance jurídico propio.
Después asignar explícitamente métodos a terreno y construcción en paso 2.
No se asignan Mercado o Costo por defecto ni por falta de muestras.
Los datos del capítulo 3 siguen en la ficha original; los enlaces conservan su ID.
Terreno busca lotes y superficie propia; no exige estado de la construcción.
Construcción consulta sus datos construidos y no completa su área con suelo.

La organización se guarda en `methodology_workflow[unitId].plan_parts`, valores
`whole`/`land_building`; no hay cambio de esquema ni migración. Las partes tienen
claves estables `unitId:terreno` y `unitId:construccion`, con métodos, alcances,
muestras y memorias independientes. No se crean filas en `appraisal_units`.
Se mantiene autorización por expediente, validación de claves activas, CSRF y
versión optimista. El formulario de organización sólo envía plan_parts; no puede
combinarlo con edición del método. Los formularios comparten versión de guardado.

La unidad original pasa a agrupadora, excluida de los recorridos activos, escritura
de análisis/matriz, importación Excel y consolidación. Métodos, conclusiones y
muestras previos siguen intactos. Las muestras pueden reasignarse expresamente
desde banco, conservando ID, datos y fotos, sin copiarse por la desagregación.
Volver al alcance propio conserva memorias de las partes; reactivarlas recupera
sus claves y datos. Un cambio posterior de régimen/tipo incompatible suspende
la desagregación hasta revisión, sin reactivar el valor anterior de la casa.

## Resolución 941 por etapa

MethodologyStepArticles organiza textos completos ya disponibles; lector plegable
antes del contenido del paso. Todos los bloques normativos inician cerrados.
Plan: 5,11,14,15,27 y 36 si PH. Selección: 15,16,22,27,28,31 y 36 si PH.
Mercado: M1 15/16/19, M3 16–19, M4 19–21, M5 14/21.
Costo: C1 27–30, C3 28/29, C4 29/30, C5 14/27.
Renta: 22–26, con 14 en resultado. Residual: 31–34, con 14 en resultado.
Consolidación/informe: 14/15/27 y 36 si PH. Es una selección pertinente, no una
declaración de cumplimiento ni una lista exhaustiva de reglas de cada encargo.
Se mantienen fuentes oficiales/transcripciones y anexo costo 2.3 íntegro de C1.

## Límites y verificación

La consolidación reúne recorridos activos y alcances; no suma valores monetarios
ni desarrolla cálculos de Costo/Renta/Residual pendientes. Se conserva M4 existente,
sin introducir homologación, coeficientes arbitrarios o nuevas fórmulas.
InversKC y los expedientes reales no fueron modificados durante las pruebas.
PHP678, JS131, BD163 en instancia nueva desechable localhost3366. Lint/build y
CSS+JS66,4KB gzip. Pruebas: claves, PH, áreas separadas, fuentes, preservación,
concurrencia y autorización; guardar/recargar organización y métodos distintos.
Chrome local: casa ficticia, plan→terreno Mercado→construcción Costo→artículo30→
consolidación; enlaces a misma ficha del capítulo 3 y academia sin unidades.
Móvil CSS390/documento367/plan327, sin desbordamiento global. Capturas guardadas
de navegación y plan con ambos métodos. Consulta general C1 conserva sólo teoría,
sin revisión o siguiente paso de una unidad, incluso con casa desagregada.
Push no prueba publicación en hosting. Desplegar archivos PHP y CSS actualizado.
