# Flujo mensual preliminar de Renta — 13/11/2026

La tabla de ingresos ya puede consultarse en Renta → 3. Ingresos y gastos →
Proyección futura → Mostrar / actualizar flujo mensual, sin exigir costos ni tasas
ficticios para obtener un resultado final. Se conserva el historial independiente,
los dos métodos, sus validaciones y las ayudas de Academia.

## Entradas y alcance

- Se mantiene la fecha del avalúo del encargo y sus controles de versión/contexto.
- Canon de contrato y canon de mercado conservan su comportamiento. La opción
  nueva «Canon de un escenario de prueba» permite ejercicios identificados sin
  presentar una referencia histórica como contrato confirmado o renta de mercado.
- Canon inicial del escenario es el vigente a la fecha del avalúo, sin IVA ni PH.
  El incremento y su primera fecha son supuestos explícitos; no pronostica IPC.
- El calendario usa el día siguiente al avalúo, cierres mensuales y prorrateo diario
  del primer mes si corresponde. Incremento anual desde la fecha seleccionada.
- Vencimiento/recolocación siguen aplicándose a la fuente contrato. Un escenario
  de prueba sin contrato simula continuidad; debe explicarlo en su soporte.
- Vacancia y pérdida sin soporte quedan pendientes; cero debe ser explícito.
  Costos/neto quedan pendientes mientras no se complete su revisión. PH desconocida
  nunca se transforma en cero. Reposiciones cero significan ausencia de eventos
  programados, no una afirmación de que el activo no los necesite.

## Dos métodos

La tarjeta directa muestra canon de partida × 12 e ingreso neto representativo
solo cuando se revisan costos. No es valor del inmueble ni suma de los 60 meses.
La tarjeta FCD muestra los ingresos brutos acumulados sin descontar; el cuadro
mensual distingue potencial, efectivo, costos, reposiciones y flujo neto.
Las tasas, la coherencia de su base, la salida y demás requisitos del cálculo final
siguen en etapa4; esta consulta no los omite ni adopta valores automáticamente.

IncomeCashFlow::calendar es compartido por vista preliminar y cálculo final, para
evitar dos cronogramas distintos. No se modificaron fórmulas de descuento/salida.

## Persistencia y conservación

Operación `preview` en el endpoint existente, con propietario, CSRF, alcance
`renta:arriendo`, versión optimista y hash del contexto. Guarda `flow_preview`
(entradas, contexto, resultado, actor, fecha y huella) en el JSON existente.
No reemplaza `calculation`. Cambios posteriores mantienen la tabla anterior y la
marcan para actualizar. El botón espera autoguardados y sustituye los dos paneles
de resultados por HTML del servidor, sin recargar la página completa.
No cambia historial, muestras, exclusiones, otras unidades ni métodos.
No requiere DDL, migración o configuración adicional.

## Ejercicio autorizado

Canon reconstruido de referencia 2.941.590,06 COP al 30/09/2026; 60 meses desde
octubre2026 hasta septiembre2031; 7,10 % cada enero desde 01/01/2027, equivalente
a IPC futuro hipotético5,10 % +2puntos. Es supuesto del usuario, no IPC oficial.
Administración queda pendiente. La prueba de continuidad sin vacancia/pérdida
simulada no representa tasas observadas. No se seleccionan tasas de valoración.

Esperado: enero2027=3.150.442,95; enero2031=4.145.047,42 COP/mes. Suma bruta
del horizonte214.232.360,48 COP. Referencia anual directa35.299.080,72 COP.
Internamente se conserva precisión; los importes visibles se redondean a2decimales.

## Verificación

- `php tests/run.php`:1791; `npm test`:314; lintPHP:939 archivos.
- `npm run build`, `npm run check:size`, `git diff --check`:correctos.
- BD nueva desechable en3391: `GA_TEST_PORT=3391 php tests/database.php`:330.
- HTTP local8095/BD3391: `GA_TEST_HTTP=true GA_TEST_HTTP_PORT=8095
  GA_TEST_PORT=3391 php tests/income-preview-http.php`:46.
- Casos: sesenta meses, incremento en enero, primer mes parcial, eventos,
  igualdad preliminar/FCD, pérdida desconocida, PH pendiente, conservación de
  cálculo/exclusiones, contexto, CSRF, propietario ajeno, conflicto y recarga.
- UI local: cambio7,1→7,2→7,1, actualización SPA, confirmación y recarga con60filas.
  Escritorio comprobado visualmente. Móvil CSS390: página367 y tabla desplazable
 262px con contenido871px; captura móvil no disponible por espera agotada del
  navegador, override restaurado. Evidencia escritorio en tests/.runtime/.
- Antes de la publicación no se modificó el expediente real. La carga del ejemplo
  autorizado y la comprobación de recursos publicados se realizan después del push.
  Si el despliegue vence la sesión, se requiere login del usuario; no acceso al hosting.
