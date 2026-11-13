# Renta: flujos, alternativas de tasa y costos sustentados

Interfaz ampliada: [historial, costos y corrección del origen](RENTA-HISTORIAL-COSTOS-TABLAS.md).
Ahora se puede editar contrato/inmueble/fecha desde Renta y sincronizar el contexto
tras guardado confirmado. Las convenciones financieras siguientes se conservan.

13/11/2026. Implementación solicitada por el responsable, sobre las cinco etapas
existentes. Capitalización directa y flujo de caja descontado comparten insumos;
no adoptan un valor de avalúo ni modifican investigaciones automáticamente.

## Uso

1. En capítulo 8, abrir el recorrido de Renta de la unidad.
2. Etapa 3, Ingresos y gastos: consultar contrato en origen o documentar canon
   representativo de mercado; completar calendario, pérdidas, costos y eventos.
3. Etapa 4: agregar alternativas, calcular/comparar y elegir una tasa por función
   con justificación. Se puede calcular un método y ver pendientes del otro.
4. Etapa 5: resumen de ambos métodos, gráfico anual, tabla mensual, soportes y
   descarga JSON de las entradas, convenciones y resultados del último cálculo.

Los campos de trabajo autoguardan en 800 ms y tienen Guardar borrador ahora.
Calcular es una acción explícita. La edición no recalcula el snapshot: se señala
como anterior hasta repetir el cálculo. Fecha de valoración consulta value_date
del encargo; solo cuando falta admite fecha provisional del escenario. El contrato
se consulta por ID, sin copiar canon/PH, y sus anexos no se agregan otra vez.

## Convenciones implementadas

- COP nominales, flujo del activo antes de financiación e impuesto de renta.
- Canon vigente mensual; contratos trimestrales/anuales se convierten por 3/12.
  Otra periodicidad queda pendiente. Canon excluye administración e IVA.
- NOI mensual = potencial − vacancia − pérdidas de cobro − gastos del propietario.
  Vacancia se aplica sobre potencial; pérdidas sobre potencial menos vacancia.
- Gastos: COP mensuales/anuales o porcentaje de potencial/efectivo. Anuales se
  distribuyen en doce meses. Solo se deduce propietario; cero necesita explicación.
  No se cargan porcentajes universales por tipo de inmueble.
- Directa = ingreso anual representativo bruto o neto / tasa compatible. No utiliza
  eventos extraordinarios del calendario explícito. No calcula con ingreso no positivo.
- FCD: desde el día siguiente a la fecha del avalúo, hasta cada cierre mensual.
  El primer mes se prorratea por días del mes. N meses significa N cierres, por lo
  que un primer cierre parcial no equivale a un mes completo. Descuento EA, días/365.
- Incrementos anuales desde fecha explícita y aniversarios (29 de febrero se ajusta
  al último día válido). Costos crecen cada doce meses proyectados, según su propia
  tasa. Canon de mercado para recolocación se expresa a fecha de avalúo y sigue el
  mismo calendario de incremento indicado. No se presume ni descarga IPC.
- Contrato: sin renta antes del inicio; después del vencimiento, parar o recolocar
  desde fecha sustentada. Fechas vacías quedan pendientes. Vacancia adicional debe
  excluir la desocupación ya representada por fechas/eventos.
- Evento mensual: canon sustituto (0 permite gracia), gasto extraordinario y CAPEX.
  Canon sustituto se prorratea en primer mes; los importes extraordinarios completos
  se pagan al cierre. Solo una fila consolidada por mes; fuera del horizonte se rechaza.
- Flujo = NOI − CAPEX. Salida observada: NOI mensual estabilizado al cierre, sin
  eventos, ×12×(1+g), dividido por tasa terminal neta. Perpetuidad de flujo libre:
  (NOI mensual estabilizado×12 − reposición anual)×(1+g)/(k−g).
- Debe explicitarse remanente capitalizado o sin remanente. No se capitaliza salida
  perpetua si se declara que el ingreso cesa después del contrato. k>g, ingreso
  terminal positivo y reposición anual explícita para perpetuidad de flujo libre.
  Gastos de venta se restan una vez a la salida; salida y flujos se descuentan.

## Alternativas de tasa

Mercado: ingreso anual/precio, misma base bruta/neta, alcance y evidencia de ambos.
No se unen automáticamente las colecciones de ventas y arriendos. Cada pareja o
referencia se documenta como candidata; no se promedia ni selecciona automáticamente.

Descuento: referencia+prima; CAPM del activo (referencia+beta desapalancado×prima);
costo ponderado antes de impuesto de renta (Ke×(1−D/V)+Kd×D/V, sin escudo fiscal);
o tasa documentada. CAPM/costo ponderado no se aceptan como tasa directa o terminal.
Perpetuidad k−g solo para salida sobre flujo después de reposiciones. No se igualan
capitalización, descuento y salida. Fuente, fecha, base y razón de elección requeridas.

Academia resume Resolución IGAC 0941/2026 arts.23–26 y anexo2.2; enlace oficial
https://www.igac.gov.co/node/53595. Los ejemplos de cursos y anexo no son parámetros
obligatorios. Se conservan los cuatro temas anteriores y sus fuentes nacionales e
internacionales separadas. CV7,5% no determina tasas ni elimina muestras para ajustarlo.

## Persistencia y controles

Sin DDL ni configuración nueva. `methodology_workflow[component].income_valuation`
contiene projection/costs/events/rates/adoption y calculation. Un formulario guarda
atómicamente las cinco secciones; JSON evita max_input_vars en colecciones largas.
Fallback nativo exige marcador final, conserva borradores y escapa mensajes/datos.
Límites técnicos: 600 cierres/eventos, 40 costos, 40 alternativas, payload1,5MB.

Transacción, propietario, token renta:arriendo, componente activo no contenedor,
CSRF y CAS methodology_version. Huella canónica del contexto evita cálculos o
guardados con contrato/inmueble/fecha cambiados. Un cambio en origen requiere
guardar allí y volver a abrir el escenario. No mezcla ni repara silenciosamente.

Snapshot incluye entradas, contexto, resultados, fecha UTC, autor y motor renta-1.
No es historial de todas las ejecuciones: cada cálculo explícito reemplaza el último;
las ediciones posteriores conservan ese snapshot anterior hasta recalcular.
Exportación verifica propietario y marca vigencia. Los formularios anteriores y
el cambio Mercado↔Renta conservan la nueva sección y sus cálculos. No escribe en
comparables, lease_register, capítulo3 ni constancias de exclusión.

## Verificación y límites

PHP unitario: fórmula directa, suma independiente de PV, meses parciales, aniversario
bisiesto, gracia, recolocación, CAPEX, Gordon, k=g, bruto/neto, gastos de terceros,
fecha en origen, CAPM/ponderación y entradas inválidas. Persistencia/HTTP: borrador,
snapshot vigente/anterior, CAS409, contexto409, separación por método, conservación,
CSRF419, propietario404, inactivo401, fallback, escape y exportación. JS: edición de
alternativas, no cálculo con borrador pendiente, conservación ante error de red.

Comandos: php tests/run.php; npm test; npm run build; npm run check:size; lint PHP.
php tests/database.php solo contra ga_test_app/ga_test_auth desechables con
GA_TEST_PORT3388. HTTP: GA_TEST_HTTP=true, GA_TEST_PORT3387 y servidor local8090
exclusivamente de pruebas; php tests/income-valuation-http.php.

No importa automáticamente porcentajes/índices financieros ni convierte tasas reales,
monedas, tasas nominales periódicas o WACC después de impuestos. No reconcilia pagos
históricos, administra deuda, aplica impuestos, estima costos desde una biblioteca ni
emite certificado de cumplimiento o informe definitivo. La proyección es un escenario
explícito con crecimiento anual constante y excepciones mensuales, no un optimizador.
Verificar evidencia, normas específicas aplicables y horizonte sigue a cargo del analista.
