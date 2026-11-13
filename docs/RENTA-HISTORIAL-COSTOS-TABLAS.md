# Renta: historial mensual, costos y edición del origen

13/11/2026. Ampliación autorizada por el responsable. Conserva cinco etapas,
Academia, contratos compartidos, muestras/exclusiones y ambos métodos.

## Recorrido

Capítulo 8 → recorrido Renta de la unidad → 3. Ingresos y gastos:

- Historial y contrato: ficha compartida con etapa 1, modificaciones fechadas,
  tabla de doce meses por año, detalle de pagos/gastos/soporte y gráfico del año.
- Proyección futura: canon contractual y referencia de mercado diferenciados,
  calendario futuro y eventos. La tabla proyectada permanece en resultados,
  después del cálculo explícito de etapa 4; no convierte historia en proyección.
- Costos con soporte: tabla de rubros, responsable, importe/porcentaje adoptado,
  base, crecimiento y fuente. Referencia externa o porcentaje del historial
  revisado, con botón explícito para adoptar. Agregar rubros conserva los previos.

Consultar/corregir fecha, nombre, tipo, áreas y ocupación permite editar los
originales del encargo/capítulo 3 sin salir de Renta. No crea otra ficha del
inmueble. La tipología se conserva; cambiar tipo requiere revisar su adecuación.
Contrato, canon e historial se editan aquí en su registro compartido por unidades.

## Significado de la historia

Canon reconstruido, facturación y recaudo son distintos. La reconstrucción parte
del canon inicial y cambios documentados con fecha, porcentaje o nuevo canon
mensual, y soporte. Regla anual, calendario de modificaciones o canon constante
son elecciones explícitas. No descarga ni supone IPC; un aniversario sin índice
documentado deja pendiente la serie hasta documentarlo o fijar un nuevo importe.
Canon inicial trimestral/anual se divide entre 3/12; otras frecuencias pendientes.

Meses parciales y cambios se ponderan por días calendario del mes, con corte de
inicio, final y avalúo; es una reconstrucción orientativa, no una factura. La serie
no reemplaza canon vigente ni pagos registrados. Meses posteriores al avalúo se
identifican aparte. Se puede consultar historia desde cualquier año documentado.

Ocupación, vacancia, gracia o días de ocupación parcial necesitan evidencia.
Un impago o mes sin datos no prueba desocupación. Vacíos no equivalen a cero.
Total del período = canon facturado + PH facturada + IVA facturado; total recibido
= canon + PH recibida por propietario + IVA recibido. PH pagada es otro dato.
El IVA solo se estima cuando constan canon facturado y tarifa del mes; no se
deduce su aplicabilidad por tipo. La estimación se distingue del importe transcrito.
Los costos del propietario son netos de reembolsos y separados del IVA/PH recibidos.
Canon recibido menos siete rubros completos produce un saldo, no rentabilidad.

## Porcentajes y Academia

Rubros: predial, seguros, mantenimiento/reparaciones menores, administración/PH,
servicios, imprevistos/otros operativos y comisión inmobiliaria. No se atribuyen
porcentajes universales a la norma. Sugerencia histórica = suma del gasto / suma
del canon recibido × 100, mismos meses revisados, con soporte y cerrados al corte.
Muestra meses, numerador, denominador, tipo y fuentes. No promedia porcentajes
mensuales ni adopta automáticamente la cifra como gasto anual estabilizado.
Un único contrato de referencia evita sumar nuevamente anexos incluidos en canon.

También admite una cotización/estudio externo con tipo, ciudad, fecha, alcance,
porcentaje y base. La referencia se conserva separada del valor adoptado, que
solo cambia al pulsar su botón; importe real y porcentual son alternativas.

Ayudas emergentes accesibles por teclado/clic, cerrables con Escape o botón,
resumen artículos 23, 24, 25 y 26 pertinentes junto al campo. Son síntesis de
aplicación, no citas literales ni certificación de cumplimiento. Fuente oficial:
https://www.igac.gov.co/node/53595 (Resolución IGAC 0941/2026 y anexo).
Los cuatro temas y ayudas anteriores de Academia se conservan.

## Persistencia y seguridad

Sin DDL ni migración nueva. Extiende JSON existente de lease_register:
history_rule, rent_adjustments por ID y monthly_history por YYYY-MM. Combinación
parcial conserva años, campos y vínculos omitidos por formularios anteriores.
JSON del borrador evita max_input_vars; vínculos se envían por lease_units.
El servidor solo admite campos del contrato, no metadatos internos del registro.
Reconstrucciones/totales son vista previa derivada; no se guardan como recibos.

Costos agregan category/reference_percent/reference_basis/reference_source en
income_valuation. No cambia el motor financiero ni la fórmula de ambos métodos.
Origen usa sus columnas existentes, transacción, propietario, CSRF, recorrido
Renta activo, CAS appraisals.version y occupation_version. Historias conservan
lease_version y valoración methodology_version/huella del contexto. Respuesta
confirmada actualiza el contexto de Renta y marca cálculos anteriores; no fusiona
ediciones ajenas ni borra resultados. Sin localStorage ni credenciales nuevas.

## Validación

- PHP: 1765; JS: 306; lint de 926 PHP; build/check:size dentro de 81920 bytes gzip.
- Base desechable ga_test_app/ga_test_auth, MariaDB local3389: 322 comprobaciones
  de migraciones, conservación, CAS y autorización; no usa bases de producción.
- HTTP: 39 comprobaciones con GA_TEST_HTTP=true, GA_TEST_PORT=3389,
  GA_TEST_HTTP_PORT=8093, php tests/lease-ledger-http.php. Incluye CSRF, ajeno,
  inactivo, versiones, JSON válido después de renderizar, recarga y referencias.
- UI local con datos ficticios: guardar pago/recargar, cambiar año por fetch,
  incremento documentado, edición del área en origen, adopción explícita de costo,
  recálculo de ambos métodos y ayudas/Escape. Escritorio y ancho CSS390 sin
  desbordamiento de página; tablas se desplazan internamente. Consola sin errores.
- Suites PHP mantienen avisos preexistentes de fixtures incompletos; pantallas
  HTTP verificadas no generan avisos. Desconexión/reintento y conflicto cubiertos
  por pruebas existentes del autoguardado y nuevas comprobaciones CAS/HTTP.

Los porcentajes no forman una biblioteca empírica por ciudad/tipo ni sustituyen
presupuestos. No estima ocupación desconocida, valida documentos automáticamente,
modifica muestras ni rellena contratos reales con datos de prueba.

Publicación: commits48289d7/9bd0085 enviados a origin/main. Tres assets públicos
HTTP200 y SHA-256 coincidente con build local; carga inicial81913bytes gzip.
El corte parcial del mes se señala y no entra en el resumen de vacancia de meses
cerrados. La sesión expiró después del despliegue; comprobación autenticada pendiente.
