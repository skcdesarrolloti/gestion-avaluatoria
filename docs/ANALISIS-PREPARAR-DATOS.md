# M4 · Preparar los datos

Primera entrega incremental, 2026-10-09. Aprovecha las muestras y herramientas
existentes. Los restantes pasos del plan académico se ampliarán progresivamente.

## Menú actual

Actualización 19/10/2026. Se conservan las siete etapas principales de M4.
«Preparar la muestra» reúne tres entradas: Configuración del estudio, Verificación
de las muestras y Ubicación y mapa. Configuración del estudio reúne base del
análisis descriptivo, atributos habilitados y, en acordeón, método/alcance existentes.
El acceso superior de Configuración abre este panel en recorridos de Mercado
con muestras; el plan general sigue disponible para organizar unidades y métodos.
La captura conserva su enlace plegable, lectores, fuentes y todos los anuncios.

Los atributos seleccionados son las columnas editables de la verificación,
incluso con poca cobertura o sin variación. Precio, área y descuento se mantienen.
Desmarcar conserva los originales y complementos; habilitar no acredita verificación.
La selección del modelo conserva las reglas existentes de compatibilidad, cobertura
y variación: Aplicar selección al modelo registra esa preparación en el historial.
La codificación técnica se confirma en Construir el modelo, sin otro selector de
atributos. Combinaciones existentes y fundamento quedan plegados en la academia.

«Editar datos aquí» utiliza los mismos atributos en preparación y sensibilidad,
sin salir del cuadro. El borrador de campos deshabilitados no se aplica como
corrección; sus datos guardados se conservan. Los campos vuelven al habilitarlos.
Los conteos de pendientes de verificación consideran los atributos habilitados,
no solo los elegibles para modelar; completo no significa verificado.
Entender la muestra reutiliza la tabla mediante un acordeón y consulta la base del
estudio. Su intervalo conserva el selector de confianza en el paso correspondiente.
No se recalcula por navegar, cambiar atributos o base: la ejecución anterior,
fecha y notas siguen disponibles; los cambios relevantes muestran obsolescencia.

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

La consulta del encargo sigue siendo de lectura. La base del ejercicio se guarda
como `basis` validada (offer/adjusted) dentro de `analysis_factor_selection`, en el
JSON existente. Se recupera incluso sin haber calculado una primera ejecución;
los clientes que omiten el campo conservan la selección previa. No hay DDL.
Método/alcance usan su formulario y versión existentes, fuera del formulario de
comparables. Método y memoria del mismo componente comparten la versión confirmada
de su endpoint; comparables y evidencia PH mantienen versiones independientes.
Los conflictos externos siguen sujetos a 409. No se agregan reglas de exclusión.

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
