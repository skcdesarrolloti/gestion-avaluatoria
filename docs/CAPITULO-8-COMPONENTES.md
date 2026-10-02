# Capítulo 8: componentes y recorrido de Mercado

Actualización vigente: [recorrido por unidad y conservación](RECORRIDO-POR-UNIDAD.md).
Una unidad visible, método registrado, academia íntegra e insumos propios.

## Corrección de navegación del 02/10/2026

La entrada vuelve a mostrar inmuebles y anexos, la ruta derivada de los datos
del expediente y accesos por identidad a cada etapa. Métodos y M1–M5 quedan
visibles también en la entrada. Se reconectan la matriz metodológica anterior
y el texto consolidado del numeral 8, que habían quedado fuera de la vista.
Las muestras sin asignar tienen acceso directo con conteo. Consultar estos
enlaces no asigna muestras ni modifica decisiones guardadas.

No cambia el esquema, la persistencia ni la configuración. No requiere una
migración nueva. Publicar los archivos modificados mediante el despliegue habitual.
Validación local: lint PHP, 466 verificaciones PHP, 112 pruebas JS, build y
check:size (58.1 KB gzip). Prueba de render con inmueble, anexo integrado y
muestra anterior; vista inicial inspeccionada en navegador a tamaño escritorio
y móvil (390 px, 375 px de contenido sin desbordamiento global).
La revisión visual usa datos ficticios, no una sesión autenticada del hosting.
No se ejecutaron pruebas de BD en esta corrección de vistas ni se verificaron
los datos o el despliegue en producción.

Primero se asigna un método a cada componente. Consultar una pestaña no cambia
la elección guardada. Unidades y anexos conservan sus IDs y el orden de lectura
del capítulo 3. El capítulo 8 refleja exclusivamente las unidades activas de
appraisal_units definidas en capítulos 1 y 3: no crea terreno virtual ni principal
de respaldo. Si no hay unidades, muestra instrucciones para completarlas en origen.
La primera pestaña muestra clasificación, tratamiento, tipología y descripción
registrados, sin sustituirlos por sugerencias de métodos. Matriz y método no cambia.

## Recorrido y alcance

- Componentes y métodos: selección, tratamiento, justificación y cobertura.
- M1 Academia; M2 Selección del método; M3 Insumos; M4 Análisis; M5 Entregable.
- M3 conserva búsqueda, captura, matriz, fotografías, mapas y evidencia.
- M4 ofrece memoria del analista y estadísticos descriptivos de precios registrados,
  separados por operación, tipología, régimen, base de área y unidad de precio.
  Solo procesa filas activas marcadas Usada con datos definidos; con n=1 no
  estima desviación ni CV. No negocia, homologa ni descarta atípicos automáticamente.
- M5 conserva la conclusión del perito para el texto del capítulo general.
- Integración muestra resultados narrativos y cobertura, sin total automático.
- Costo, Renta y Residual conservan academia y asignación; operación posterior.

La revisión estadística no certifica suficiencia ni cumplimiento. El motor completo
de adopción, valores numéricos consolidados y demás métodos sigue pendiente.
La numeración del aplicativo es independiente de la numeración del informe.

## Actualización y conservación

Aplicar `php bin/console.php migrate` o AUTO_MIGRATE=true. La migración
202610010008_methodology_workflow agrega documento y versión a appraisals.
No borra muestras, fotos, tablas ni usuarios.

Muestras anteriores: Banco sin asignar. Marcar y asignar expresamente;
pueden devolverse al banco o reasignarse, sin duplicación y conservando IDs.
Las muestras vinculadas a terreno virtual o unidades que dejaron de estar activas
siguen visibles en Muestras sin asignar como asignación anterior para reasignación
explícita. Las selecciones anteriores permanecen almacenadas sin generar unidades.
Las nuevas capturas llevan component_key dentro de capture_details existente.
Guardar un componente preserva el resto de la colección. Se conserva versión
optimista global de muestras y se rechazan IDs de otros componentes.

La metodología tiene versión independiente y HTTP 409 ante edición simultánea.
La navegación fetch espera guardados pendientes. No se usa localStorage.
Cambios en muestras generan aviso de revisión de la redacción anterior.

## Verificación

- PHP: 469 verificaciones; JS: 112 pruebas; lint PHP y build correctos.
- MariaDB local desechable puerto 3319, ga_test_app/ga_test_auth: 93 verificaciones,
  migración repetida, conservación, aislamiento y conflictos.
- HTTP: 44 verificaciones, render de todas las etapas, CSRF, asignación y retorno
  al banco, recuperación de muestras de unidad inactiva y reflejo de nombres y
  descripción guardados en capítulo 3. Rechazo de componente virtual en servidor.
- CSS + JS: 58.1 KB gzip.
- Navegador local: terreno y dos anexos, selección y autoguardado antes de navegar,
  asignación de una muestra anterior, M4, redacción M5 e integración.
- Revisión visual de escritorio y pantalla estrecha; ancho CSS efectivo de 585 px
  sin desbordamiento global. La captura completa móvil falló en el navegador;
  no se declara verificación visual completa a 390 px.

Publicar código no equivale a verificar la actualización del hosting.

## Recorrido guiado (2026-10-02)

- A: inmuebles; B: matriz orientativa; C: elegir componente y recorrer M1–M5;
  D: integrar y revisar texto. Los métodos y sus etapas se muestran al trabajar
  un componente; las muestras anteriores son una herramienta de apoyo plegable.
- Botones de siguiente/anterior y ayudas «?» accesibles con teclado o toque.
  M2 conserva el enlace dinámico del formulario para seguir el método elegido.
- Enlaces a capítulo 1: config_tab=metodo y #unidades-capitulo-1; capítulo 3:
  section=tipologias y #unidades-capitulo-3. Apertura inicial desde parámetros del
  servidor y desplazamiento tras mostrar la pestaña. Retorno al 8 en ambos bloques.
- Sin cambios de esquema ni datos. No cambia las reglas de Matriz y método.
- Verificado: 471 pruebas PHP, 113 JS, 44 HTTP, lint y build; 58.1 KB gzip.
  Navegador: ida por fetch a ambos bloques visibles a 24 px del borde superior,
  vuelta al capítulo 8 conservando expediente. Se divide composición en parcial
  propio para respetar 16 KB por archivo.

La navegación fetch conserva el fragmento de enlaces GET al mismo destino, sin
trasladarlo a redirecciones de login. Prueba de regresión incluida.
