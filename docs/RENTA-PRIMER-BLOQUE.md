# Renta: primer bloque conceptual

12/11/2026 · Insumos de Renta se organiza en tres pestañas horizontales:
1. Comprender el inmueble; 2. Preparar la búsqueda; 3. Recoger muestras.
Solo se muestra un panel con JavaScript; en móvil la barra se desplaza horizontalmente.
Los paneles se mantienen montados: cambiar pestaña no reconstruye formularios,
no borra borradores de pegado ni duplica la matriz. Teclado con flechas, Inicio y Fin;
enlaces server-rendered `renta_paso` y contenido disponible como fallback sin JS.
La preparación consulta los filtros existentes del expediente y permite elegir
portal/inmobiliaria para reutilizar su texto e instrucciones de búsqueda. El botón
Continuar lleva a la captura de esa fuente. La selección de fuente es de navegación,
no un parámetro persistido del avalúo. Las correcciones del sujeto se hacen en su
capítulo; no se agregan rangos, cálculos, campos de negocio ni migraciones.
Ayuda plegable conecta con los soportes del tema 3 de Academia. La captura previa,
conteos, lectores, consolidación, investigación, mapas y autoguardado se conservan.
Validación: lint, PHP1598, JS268, build/check:size78,9KB; fixture PHP local con
contexto Arriendo/Oficina/Cartagena/Chambacú, escritorio1440 y móvil390 sin
desbordamiento, controles44px y sin errores JS. Se comprobó selección conservada,
borrador de pegado intacto, una matriz/un formulario conceptual y fuente elegida
al continuar. POST de la fixture simulado; no acredita persistencia nueva ni hosting.
Capturas: output/pdf/renta-pestanas-escritorio.png y renta-pestanas-movil.png.

Flujo enfocado (12/11/2026): al seleccionar Renta en Configuración se ocultan las
entradas académicas de los otros métodos y la recomendación académica general.
Botón directo «1. Abrir Academia de Renta y sus normas», seguido de Insumos.
Dentro de Academia Renta se muestra solo su pestaña; fundamentos comunes quedan
como consulta opcional. Contrastes, métodos guardados y sus datos se conservan.
El acceso espera el autoguardado mediante la navegación fetch existente; bloqueado
si cambió la organización y falta actualizar el plan. No cambia la persistencia.
Validación: PHP1598/JS268/lint/build/check:size78,9KB; vista PHP real con Mercado y
Costo activos como contrastes. Cambio Costo→Renta oculta sus academias en vivo;
acceso directo muestra cuatro temas y solo Renta. Escritorio1440/móvil390 sin
desbordamiento ni errores JS. Transporte de guardado simulado en esta fixture UI;
no se hizo otra prueba BD al no cambiar escritura. Capturas renta-flujo-simple.jpg
y renta-academia-solo-renta.jpg en output/pdf. Hosting no actualizado: recargar el
navegador no instala código que todavía no se ha desplegado.

Academia ampliada (12/11/2026): cuatro temas plegables sobre encargo, contratos y
derechos, información/verificación y documentación de decisiones. Cada tema incluye
explicación, ejemplo, insumos por reunir y conexión con las tres preguntas iniciales.
Soportes internos por tema: IGAC, IVS y NTS/GTS con edición, apartado, síntesis del
fragmento pertinente, utilidad y enlace al documento existente. No son transcripciones
ni requisitos automáticos. Se conservan el resumen PDF y los artículos IGAC previos.
Sin fórmulas, nuevos formularios ni cambios de persistencia.
Validación de esta ampliación: PHP1597/JS268, lint de los archivos nuevos/modificados,
build y check:size correctos (78,9KB gzip). Revisión de vistas PHP en fixture local:
cuatro temas plegados, apertura con Enter y soportes por familia; escritorio1440 y
móvil390 sin desbordamiento ni errores de consola. Capturas
`output/pdf/renta-academia-soportes.jpg` y `renta-academia-soportes-movil.jpg`.
Sin prueba BD adicional: contenido de consulta, sin nueva escritura o esquema.

Identificación del recorrido (12/11/2026): distintivo visible «Recorrido activo:
Renta» y etiquetas Academia/Insumos/Análisis/Entregable con sufijo «· Renta».
Academia abre directamente el método; General conserva su identificación común
y permite volver a Academia de Renta. Encabezado específico «Academia · Modelo
de Renta». Solo presentación/navegación; sin cambios de datos o persistencia.
Validado con vistas PHP reales y fixture de presentación, escritorio 1440 y móvil
390 sin desbordamiento, PHP1594/JS268/lint/build/tamaño78,9KB. Sin despliegue.

Implementación local del 12 de noviembre de 2026. No incorpora cálculos ni tasas.

## Ubicación y experiencia

Capítulo 8 > recorrido con Renta guardada > Insumos >
«1. Comprender el inmueble y su arriendo». Se conserva debajo la captura existente
de muestras, portales, matriz y herramientas; no se modifican sus lectores.

Tres desplegables sin selección automática: ocupación, partes a estudiar y soporte
disponible. Cada pregunta ofrece ayuda (?) mediante details/summary nativo, explicación,
ejemplo y referencias por edición/apartado a bibliotecas IGAC, IVS y NTS/GTS.
Son opciones operativas de captura, no categorías textuales impuestas por las normas.
Otra ocupación, partes identificadas e información contractual/verbal permiten notas
contextuales opcionales, máximo 1000 caracteres. Cambiar la opción conserva las notas;
no genera contratos, ingresos, evidencia verificada ni viabilidad del método.

Academia > Renta incluye «Antes de empezar con Renta», orientación en pantalla y
enlace al resumen completo de seis páginas, `assets/normativa/renta-orientacion-v1.pdf`.
El resumen propio se distribuye con el aplicativo; no requiere subirlo desde Chrome
ni acceder al archivo local del chat. No se creó una ficha adicional en Maestros.
Los PDF normativos originales siguen en sus bibliotecas privadas existentes.

## Persistencia y seguridad

`methodology_workflow[component_key].income_concept` utiliza el JSON y
`methodology_version` existentes. Sin DDL, migración o recodificación histórica.
Claves: occupancy, scope, support, occupancy_detail, scope_detail y support_detail.
Enumeraciones validadas en servidor; texto escapado; campos desconocidos rechazados.
El repositorio combina cambios parciales y conserva preguntas omitidas.

POST `/avaluos/{id}/metodologia-valuatoria/renta/concepto`, sesión/actividad,
CSRF, propietario, componente activo y método Renta guardado. Los contrastes tienen
clave independiente; desactivar/reactivar no elimina respuestas. HTTP 409 ante versión
obsoleta; no se reintenta automáticamente un conflicto ni se fusionan ediciones remotas.

Reutiliza autoguardado de 800 ms, confirmación del servidor, pendiente/guardando/
guardado/sin conexión/error/conflicto, reintento manual y aviso antes de abandonar.
Sin localStorage. Eventos de validación muestran errores junto al campo con ARIA.
Sin JS, el POST conserva respuestas y errores en flash por expediente/componente;
un conflicto conserva la versión antigua y el borrador, sin sobreescribirlo.
El formulario conceptual y el plan de investigación comparten únicamente la versión
confirmada de metodología; la versión de comparables permanece independiente.

## Validación

- Lint PHP del árbol: 813 archivos en la primera pasada; archivo HTTP añadido
  y vistas modificadas comprobados después. `git diff --check` sin errores.
- `php tests/run.php`: 1594 verificaciones correctas.
- `npm test`: 268 pruebas correctas, incluidos errores por campo y versiones
  compartidas entre concepto/plan sin alterar comparables.
- `npm run build` y `npm run check:size`: correctos, 78,9 KB gzip de CSS + JS.
- `GA_TEST_PORT=3377 php tests/database.php`: 236 verificaciones correctas;
  instancia MariaDB nueva en localhost, solo ga_test_app y ga_test_auth.
  Incluye persistencia/recarga, parcialidad, propietario, conflicto, conservación
  al reactivar contraste, migración repetida y conservación al agregar columnas.
- `GA_TEST_HTTP=true GA_TEST_PORT=3377 php tests/income-concept-http.php`:
  13 verificaciones contra servidor de fixtures localhost:8088; login real de
  prueba, CSRF, método, guardado/recarga, errores 422, fallback, conflicto 409,
  propietario ajeno, revalidación de actividad y PDF HTTP 200.
- Navegador IAB sobre aplicación PHP y BD reales desechables: selección y notas,
  confirmación de autoguardado, recarga, respuesta condicional, ayudas con Enter,
  referencias y navegación SPA a Academia. Escritorio 1440 y móvil 390;
  controles/ayudas de 44 px, ancho móvil sin desbordamiento y consola sin errores.
  Capturas en `output/pdf/renta-conceptual-*.jpg` y `renta-academia-movil.jpg`.
  Error de red/reintento y bloqueo de conflicto: pruebas JS del transporte existente.

La suite HTTP general anterior se detiene en su comprobación «sin unidades no crea
componentes virtuales», previa al bloque Renta y basada en una presentación anterior.
No se atribuye éxito a esa suite; se ejecutó la integración HTTP específica indicada.
Se corrigió una fixture de tests/run.php cuya fecha RAA de octubre ya había vencido:
usa fechas relativas para mantener activo al perito de prueba, sin cambiar producción.

## Publicación y continuidad

Actualizar código, assets compilados y el PDF propio junto con el repositorio.
No requiere migración nueva. No se ha desplegado al hosting ni modificado datos reales.
Revisar el primer bloque con el responsable antes de ampliar preguntas o matemática.
Mantener `RENTA-ORIENTACION-NORMATIVA.md` como memoria del estudio y los criterios
pendientes que no deben convertirse en parámetros o reglas automáticas.
