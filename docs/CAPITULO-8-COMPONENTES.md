# Capítulo 8: componentes y recorrido de Mercado

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
la elección guardada. Unidades y anexos conservan sus IDs. El terreno separado
es opcional y nunca se asigna ni suma automáticamente.

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
Las nuevas capturas llevan component_key dentro de capture_details existente.
Guardar un componente preserva el resto de la colección. Se conserva versión
optimista global de muestras y se rechazan IDs de otros componentes.

La metodología tiene versión independiente y HTTP 409 ante edición simultánea.
La navegación fetch espera guardados pendientes. No se usa localStorage.
Cambios en muestras generan aviso de revisión de la redacción anterior.

## Verificación

- PHP: 456 verificaciones; JS: 112 pruebas; lint PHP y build correctos.
- MariaDB local desechable puerto 3319, ga_test_app/ga_test_auth: 93 verificaciones,
  migración repetida, conservación, aislamiento y conflictos.
- HTTP: 34 verificaciones, render de todas las etapas, CSRF, asignación y retorno
  al banco y recuperación de la identidad de muestras.
- CSS + JS: 58.1 KB gzip.
- Navegador local: terreno y dos anexos, selección y autoguardado antes de navegar,
  asignación de una muestra anterior, M4, redacción M5 e integración.
- Revisión visual de escritorio y pantalla estrecha; ancho CSS efectivo de 585 px
  sin desbordamiento global. La captura completa móvil falló en el navegador;
  no se declara verificación visual completa a 390 px.

Publicar código no equivale a verificar la actualización del hosting.
