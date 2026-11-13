# Renta: preparación del arriendo e historial de pagos

Evolución posterior autorizada: [historial mensual y costos en tablas](RENTA-HISTORIAL-COSTOS-TABLAS.md)
incorpora reconstrucción documentada, totales y gráficos separados de proyección.
Lo siguiente describe la entrega inicial de captura, conservada como antecedente.

13/11/2026. Autorización: completar la primera etapa y preparar un historial
mensual de arriendo y administración. El usuario confirmó expresamente
«Dejar primero la captura, sin cálculos». No se generan cánones por IPC, series
proyectadas, totales, tasas ni resultados de los métodos.

## Origen y captura

- Inmueble y ocupación se consultan en capítulo 3. El resumen permite consultar
  también ubicación, características, referencia IGAC y contexto de PH.
- La ficha guarda una referencia al ID de unidad y al campo de área del capítulo
  3. No copia la magnitud ni supone área arrendable. Se documenta si el canon
  comprende otros espacios o solo una parte; un área sin soporte queda pendiente.
- Canon inicial y periodicidad son distintos del canon vigente. Fecha de inicio,
  primera fecha de incremento y referencia del índice preparan la futura serie.
  No se presume reajuste en enero ni se aplica el IPC sin revisar el contrato.
- Inicio/incrementos y condiciones especiales quedan plegados. Para ocupación
  propia, desocupación o adecuación, una ficha nueva empieza plegada; los
  antecedentes existentes permanecen accesibles. Estado «Sin contrato vigente»
  permite documentar antecedentes y gastos sin inventar fechas ni canon.
- Historial por año, doce meses: arriendo recibido imputado al período, cuota
  ordinaria cobrada, administración ordinaria pagada, expensas extraordinarias
  pagadas, pagador, validación manual, soporte y observaciones. PH queda plegada
  si no consta el régimen y no hay datos anteriores de administración.
- Un importe vacío no es cero. «Revisado con soportes» exige algún importe y
  soporte, y solo se refiere a los datos registrados. Editar un campo del mes
  vuelve la revisión a pendiente en la interfaz. Los pagos que cubran otros
  períodos y los reembolsos requieren distribución documentada, sin inferencias.
- Un contrato y su historial se comparten entre sus unidades vinculadas. No se
  repiten pagos por anexo ni se distribuye el canon automáticamente.

## Persistencia

Ampliación aditiva de `appraisals.lease_register` existente, sin cambio de esquema
ni migración. Campos escalares opcionales y `monthly_history`, objeto por YYYY-MM.
El servidor valida importes, campos, meses, fuentes y unidad del área. Combina
meses/campos enviados con el historial anterior; formularios que omiten el
historial lo conservan. Un mes totalmente vacío no crea pagos y un vaciado
explícito de todos sus campos retira solamente ese mes. Máximo técnico: 1200
meses por ficha; fechas del historial 1900–2199. No se usa localStorage.

Se mantiene propietario, CSRF, recorrido Renta activo y CAS con lease_version.
Una pestaña obsoleta devuelve409. El fallback conserva valores y errores de los
doce meses del año abierto. Navegar de año usa fetch y espera el autoguardado;
sin confirmación, conserva la pantalla y el borrador.

## Próxima fase estadística acordada

Conservar todos los recursos aplicables de Mercado, organizados en resumen y
detalle técnico: tendencia central, cuartiles, dispersión, asimetría, curtosis,
RIC/MAD, estimadores, intervalos, remuestreo, regresiones y diagnósticos. Adaptar
fuentes, áreas y conceptos a COP/m²/mes. Agregar Chauvenet con sus supuestos y
limitaciones; no está implementado actualmente en Mercado. No excluir muestras
automáticamente ni forzar CV≤7,5 %. El límite corresponde a adoptar la media
urbana, no a un descuento. Gráficos y memoria deben mantener trazabilidad.

El apartado plegable de etapa2 documenta este alcance, no ejecuta los análisis.
Las etapas de validación predictiva y aplicación al sujeto pendientes en Mercado
no se presentan como operativas en Renta. La investigación de arriendos será
insumo común de capitalización directa y flujo de caja descontado.

## Validación

- PHP1718 y JavaScript293; lint de PHP modificado, build y check:size≤81920 bytes.
- `GA_TEST_PORT=3383 php tests/database.php`:272 comprobaciones, únicamente
  ga_test_app/ga_test_auth en instancia MariaDB nueva desechable. Incluye
  migración repetida, conservación, otros años, formulario anterior y conflictos.
- `GA_TEST_HTTP=true GA_TEST_PORT=3383 php tests/lease-http.php`:26 comprobaciones
  (login, CSRF, propietario, inactivo, validación mensual, fallback, recarga,409).
  Suites comparable-study-http e income-concept-http:14+14.
- Navegador local con PHP/BD reales: cambiar año mediante fetch, autoguardado y
  recarga, revisión explícita y retorno a pendiente al editar, desconexión/reintento,
  conflicto entre pestañas y escritorio/móvil390 sin errores JS ni desbordamiento.
  Evidencia local ignorada: tests/.runtime/renta-history-{desktop,mobile}.png.
- La suite PHP emite advertencias de fixtures incompletos (sesión y campos de
  muestras); los1718 checks pasan. La navegación HTTP/UI probada no emitió esos
  avisos ni errores de JavaScript. No se afirma cumplimiento normativo automático.

Publicado con commit/push575d9b1 a origin/main. Después de esperar el despliegue,
app.js, income-inputs.js y app.css públicos respondieron200 y sus SHA-256
coincidieron con los locales. La apertura autenticada del expediente confirmó
que Oficina Principal sigue en Mercado; no se cambió su método ni sus datos
para verificar. La ficha nueva completa se verificó con PHP/BD locales; su
verificación visual en un recorrido Renta activo del hosting queda pendiente.
