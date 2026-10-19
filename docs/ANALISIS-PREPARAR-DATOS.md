# M4 · Preparar los datos

Primera entrega incremental, 2026-10-09. Aprovecha las muestras y herramientas
existentes. Los restantes pasos del plan académico se ampliarán progresivamente.

## Menú actual

Actualización 19/10/2026. Se conservan las siete etapas principales de M4.
«Preparar la muestra» reúne Configuración del estudio, el acceso directo Traer
muestras de portales, Verificación de las muestras y Ubicación y mapa. Configuración del estudio reúne base del
análisis descriptivo, atributos habilitados y, en acordeón, método/alcance existentes.
Durante captura y análisis de Mercado activo se muestra únicamente su recorrido,
sin otra barra superior de Configuración/Análisis. La misma navegación de preparación
aparece en ambas rutas. Traer muestras de portales abre los lectores existentes
sin acordeón intermedio. El plan general se conserva mediante Organizar unidades y
métodos; las entradas sin método o sin muestras mantienen su orientación inicial.
En Memoria y sustentación se mantiene el enlace al entregable. El catálogo define
nombres/escalas reutilizables; la selección de atributos del estudio sigue siendo
única en Preparar la muestra.

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


### Menú dependiente del paso 1

Las cuatro opciones de Preparar la muestra se despliegan desde su propio botón,
identificado con flecha, sin una barra de opciones debajo de todo el recorrido.
La misma plantilla sirve a M3 (enlaces) y M4 (estado Alpine del formulario actual).
Abrir el desplegable no cambia el paso ni los datos: se navega al elegir una opción.
Se cierra al elegir, pulsar Escape o hacer clic fuera. Escape devuelve el foco al
botón; Tab recorre las opciones nativas. aria-expanded/aria-controls identifican
la relación. En móvil el menú conserva ancho/alto acotados y se lleva a la vista
al abrir, sin recargar ni recalcular el análisis. El estado abierto no se persiste.
