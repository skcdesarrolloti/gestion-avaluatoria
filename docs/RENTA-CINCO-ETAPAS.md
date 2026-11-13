# Renta: recorrido de cinco etapas

13/11/2026. Reorganización autorizada de navegación y ayudas, tomando como base
el recorrido de trabajo de Mercado. No agrega cálculos ni reglas de valoración.

## Recorrido

1. **Inmueble y arriendo:** consulta capítulo 3 y mantiene la ficha compartida
   del contrato, pagos, vigencia, incrementos y soportes. Antecedentes conservados.
2. **Investigación de arriendos:** reúne Preparar la búsqueda y Recoger/revisar
   muestras. Conserva portales, lectores, conteos, selección, matriz y Excel.
3. **Ingresos y gastos:** guía de preparación de ingresos, ocupación, cobros,
   gastos y fuentes. Las condiciones del contrato se corrigen en la etapa 1.
4. **Valoración por los dos métodos:** presenta capitalización directa y flujo
   de caja descontado con sus insumos por sustentar; no hay ejecución numérica.
5. **Revisión y sustentación:** orienta la futura memoria, diferencias, soportes
   y limitaciones. No adopta resultados ni certifica datos al visitar la etapa.

Cada etapa ofrece Academia contextual a partir de los cuatro temas y fuentes
existentes. La lectura completa sigue disponible. Se eliminan la segunda barra
de Academia/Insumos/Análisis/Entregable y el siguiente paso del recorrido anterior
dentro del espacio de Renta. Mercado conserva sus siete etapas.

En Configuración, ambos métodos usan **Continuar con el estudio**. El enlace a
Academia consulta el método elegido en pantalla y ya no conserva el rótulo del
método previo. Los recorridos de contraste siguen disponibles.

## Conservación y compatibilidad

- Una ficha de arriendo y una matriz de muestras montadas por visita. Cambiar
  etapa usa visibilidad, sin reemplazar el formulario ni su pegado pendiente.
- La captura mantiene carga diferida y reintento; las otras etapas no cargan
  muestras, barrios ni escalas para captura desde la base.
- Cambiar etapa espera autoguardados. Si fallan, permanece en la anterior y
  muestra el motivo; los campos continúan en pantalla para reintentar.
- `renta_paso` y portal se conservan en la URL. `concept/search/capture` siguen
  vigentes; se agregan `evidence/valuation/review` como navegación, sin guardado BD.
- Las rutas Renta `stage=4/5` abren Valoración/Revisión; al navegar se normalizan
  al espacio común `stage=3`. Enlaces anteriores y fallback HTML permanecen.
- No cambia el esquema, endpoints, permisos, versiones ni aislamiento por unidad,
  método y operación. CV ≤ 7,5 % sigue como criterio de análisis de la muestra;
  vacancia, capitalización y descuento no reciben porcentajes predeterminados.

## Validación

Lint PHP de archivos afectados; `php tests/run.php` (1700), `npm test` (293),
`npm run build` y `npm run check:size` (81.920/81.920 bytes gzip iniciales).

Con `GA_TEST_HTTP=true` y `GA_TEST_PORT=3384`, exclusivamente las bases locales
desechables `ga_test_app/ga_test_auth`: `php tests/lease-http.php` (18),
`php tests/comparable-study-http.php` (14) y `php tests/income-concept-http.php` (14).
El test de aislamiento ahora acepta el puerto desechable explícito, como las
otras suites; sigue rechazando 3306 y fijando ga_test_app. No se requirió DDL.

Navegador contra PHP/BD locales: cinco etapas, ayudas por etapa, contrato guardado
y recuperado al recargar, desconexión y reintento sin navegar ni perder borrador,
pegado conservado al salir y volver a investigación, matriz única, enlaces 4/5,
Academia al cambiar Mercado/Renta y 390 px sin desbordamiento ni errores JS.
La navegación no modifica contratos, respuestas ni muestras reales en producción.
