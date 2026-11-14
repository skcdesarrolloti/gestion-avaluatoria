# Historial de referencia automático de Renta

13/11/2026. Sustituye la captura mensual como recorrido principal, por solicitud
del responsable. Acceso: Renta → 3. Ingresos y gastos → Historial y contrato.
La misma ficha está disponible en etapa 1 y sirve a directa/FCD.

## Uso y límites

Inicio, canon inicial, periodicidad y regla generan todos los meses hasta el mes
actual, con filtro de año y gráfico. IPC + puntos es la opción inicial para
fichas sin regla. Enero es el calendario predeterminado solicitado; aniversario
y primera fecha personalizada siguen disponibles. Las reglas y modificaciones
ya guardadas se conservan. El inicio se edita una sola vez en la ficha compartida.

Canon siguiente = canon anterior × (1 + (IPC diciembre anterior + puntos)/100).
Se redondea cada cambio a centavos. No descarga tasas en cada visita. Catálogo:
DANE, anexo diciembre 2025, hoja 1, A10:D19, variación anual nacional 2016–2025,
actualizado por DANE el 08/01/2026. Fuente:
https://www.dane.gov.co/files/operaciones/IPC/dic2025/anex-IPC-dic2025.xlsx

Faltantes de IPC dejan pendiente el canon desde ese reajuste. La excepción con
fecha, índice/importe y fuente permite completar una referencia fuera de cobertura.
No se repite el último índice ni se atribuye a la Resolución 941 la regla IPC+2.

La cuota PH inicial puede corresponder a otra fecha conocida; no se extrapola
hacia atrás ni se confunde con la cuota actual del encargo. Su referencia aumenta
por IPC cada enero, o permanece constante si se elige. Una excepción sustituye
el reajuste de la misma fecha, o modifica el importe desde otra fecha. Un importe
PH mensual ya digitado prevalece únicamente en ese mes y se identifica en la tabla.
La tarifa IVA requiere dato expreso o no aplicación documentada; la base es canon,
separada de PH. La tabla no implica tratamiento fiscal verificado ni facturación.

Vacancias se registran como intervalos inclusivos. Días superpuestos cuentan una
sola vez. Descuentan canon de referencia por días calendario y conservan PH. Una
vacancia mensual previa también se conserva; impagos no se interpretan como
vacancia. Sin intervalos se muestra continuidad supuesta, no ocupación probada.
Una ocupación parcial sin fechas requiere revisar su detalle, sin inventar cuáles
días estuvieron libres. Fuera de vigencia no se afirma ocupación/vacancia.

Mes actual completo como referencia; inicio, final y cambios parciales se ponderan
por días calendario. Meses posteriores a la fecha del avalúo se identifican. Máximo
1200 meses por ficha. No reconstruye pagos ni reemplaza canon vigente, proyección,
costos adoptados, tasas, resultados o evidencia de rentabilidad del activo.

## Conservación y persistencia

monthly_history y rent_adjustments permanecen íntegros; la tabla manual anterior
está plegada bajo «Pagos y comprobantes ya registrados · consulta opcional».
No es un requisito para generar la referencia. Los porcentajes históricos de costos
siguen usando únicamente gastos y pagos revisados, nunca importes reconstruidos.

Campos nuevos en lease_register JSON: history_ipc_points, history_calendar,
history_admin_amount/date/rule, history_vat_rate, admin_adjustments por ID y
vacancy_periods por ID. Sin cambio de esquema. Parches conservan campos omitidos,
contratos, anexos y registros de otros años. Referencias se calculan en memoria;
no se guardan como pagos. Propietario, CSRF, CAS y autoguardado existentes intactos.

## Validación

- PHP: 1770 comprobaciones; JS: 312; lint de 931 archivos PHP.
- BD desechable ga_test_app/ga_test_auth en 127.0.0.1:3390: 324 comprobaciones.
- HTTP local8094: 39 comprobaciones, incluidas CSRF, propietario, CAS y recarga.
- Build y check:size correctos. Código separado, sin ampliar la carga inicial.
- Navegador: ejemplo octubre 2022 / 2 millones / IPC+2 genera 50 meses a noviembre
  2026; autoguardado/recarga, vacancia febrero2024, PH excepcional enero2025,
  tabla sin inputs por mes y ancho móvil390 sin desbordar la página.
- Ejemplo enero2026: canon 2.941.590,06 COP. PH inicial de prueba300.000 genera
  410.033,93 COP sin excepciones. Las cifras son exclusivamente de prueba local.
- Conserva ambos métodos, Academia, investigación de arriendos y exclusiones.

Comandos: php tests/run.php; npm test; npm run build; npm run check:size;
php -l por archivo; GA_TEST_PORT=3390 php tests/database.php;
GA_TEST_PORT=3390 GA_TEST_HTTP=true GA_TEST_HTTP_PORT=8094 php tests/lease-ledger-http.php.
La suite PHP muestra avisos anteriores en fixtures parciales; HTTP no muestra
avisos PHP y la consola del navegador no presenta errores.
