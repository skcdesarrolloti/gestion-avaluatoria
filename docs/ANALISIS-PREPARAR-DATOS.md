# M4 · Preparar los datos

Primera entrega incremental, 2026-10-09. Aprovecha las muestras y herramientas
existentes. Los restantes pasos del plan académico se ampliarán progresivamente.

## Menú actual

1. Preparar los datos.
   - 1.1 Objetivo y unidad de análisis: consulta del encargo y explicación inicial.
   - 1.2 Muestras y depuración: herramientas existentes de información recogida,
     régimen, factores y resultado, con anuncios originales e historial.
   - 1.3 Coordenadas y mapa comparativo: verificación existente de localización.
2. Análisis estadístico paso a paso: recorrido existente de siete pasos.
3. Modelo de regresión: academia, preparación y diagnósticos existentes.

El primer panel se abre al entrar a M4. Cambiar de panel no cambia la selección
guardada, aplica filtros ni actualiza cálculos. La navegación fija se limita a
pantallas desde el breakpoint sm; en móvil se desplaza con el contenido.

## Qué consulta 1.1

Componente actual; finalidad, base de valor, derecho, negocio y fecha del encargo.
El tipo de inmueble procede de ComparableSearchContext, respetando la unidad
actual y las reglas existentes de herencia. Un anexo no toma el tipo global.
Los campos ausentes se presentan como pendientes, sin imponer valores nuevos.
Puede consultarse incluso sin muestras; en ese caso no se monta un formulario
vacío de comparables.

Explica una observación por inmueble agrupado, conservando todos sus anuncios;
oferta frente a precio de cierre; descuento sustentado; denominador de área y
tratamiento de anexos; variable respuesta Y y atributos X como decisiones futuras.
No selecciona una franja de precios para conseguir un CV objetivo.

## Academia y límites

Guía basada en el recorrido de estudio: Sesión 1, preparación/distribución;
Sesión 2, robustez/depuración; Sesión 3, modelación/validación. No incorpora una
nueva regla normativa ni certifica cumplimiento. Academia y el aviso normativo
existente permanecen disponibles. El desarrollo posterior debe vincular controles
con artículo, evidencia y alcance según CONTROL-NORMATIVO-ANALISIS.md.

Este panel es de consulta: no añade persistencia, columnas, fórmulas o decisiones
de exclusión. Los controles existentes conservan su autoguardado y versión.
Pendiente: documentar y persistir la definición expresa de Y/unidad/base de área
cuando se acuerden los campos y criterios; ampliar cada preparación paso a paso.

## Verificación

PHP: 1555 verificaciones, incluyendo consulta sin muestras, tipo del anexo,
escape de etiquetas y campos pendientes. JavaScript: 227 pruebas. Lint PHP,
compilación y control de tamaño: 78,4 KB gzip CSS + JS inicial.
Sin cambios de persistencia: no se ejecuta tests/database.php ni se usa BD real.
Vista local con datos sintéticos: objetivo, matriz, mapa, estadística y regresión;
móvil de 375 px útiles sin desbordamiento horizontal ni errores de consola.
No desplegado al hosting en esta entrega.

Comandos: `php tests/run.php`, `npm test`, `npm run build`, `npm run check:size`;
lint con `php -l` sobre los archivos PHP de app, routes, database, bin y tests.
