# Diagnósticos del modelo de M4

2026-10-06. Ruta: M4 → Modelo de regresión → Gráficos y datos atípicos.
La ecuación también se muestra al calcular. El descuento integra la respuesta
unitaria; no se incorpora como predictor. Antigüedad ordinal no equivale a años.

## Alcance

- Histogramas de respuesta/residuos, caja y bigotes, observado/estimado,
  residuos/estimados, Q-Q normal de residuos y dispersión de cada factor con y.
- Media, mediana, desviación muestral, CV, asimetría y exceso de curtosis
  corregidos como SKEW/KURT de Excel (normal de referencia: exceso cero).
- Comparación media, mediana, media recortada total 40% y geométrica; MAPE
  descriptivo de cada estimador y del ajuste. No mide desempeño fuera de muestra.
- Cuartiles inclusivos interpolados. Señales exploratorias: y fuera de
  Q1−1,5 RIC / Q3+1,5 RIC; |studentizado interno| >2; h>2p/n; Cook>4/n.
  p=k+1 incluye intercepto. Son criterios de revisión, no pruebas de exclusión.
- h se obtiene desde R de la QR estandarizada, resolviendo Rᵀu=z_i;
  h_i=||u||². Studentizado e_i/[s√(1−h_i)]; Cook=e_i²h_i/[p s²(1−h_i)²].
- Cada inmueble conserva su identificación y botón Revisar inmueble que abre
  Resultado depurado, muestra todas las filas y enfoca su fila; no cambia
  selección, datos, muestras ni historial. Resultados desactualizados bloquean
  esa revisión y exportación hasta recalcular.
- El informe HTML incorpora ecuación, gráficos y diagnósticos por observación.
  Avisos de simulación existentes se conservan. No hay valor adoptado automático.

## Ejercicios aportados por el usuario

Lectura de los dos libros originales sin modificarlos. EJ - S2, Hoja1: 12
observaciones, área/valor unitario, coeficientes calculados 161,0736173393 y
−1,3251121076, R²=0,8951038258. El texto G17 contiene otra ecuación y no se
reutiliza como resultado. Hoja2: 24 observaciones, R² recalculado=0,2346131000;
se ven dos bandas de precio para áreas similares, sin etiqueta que explique
su origen. Su observación 12 también cambia de 68 a 71 frente a Hoja1.

EJ_CURSO_IGAC compara media/mediana/recortada/geométrica mediante MAPE; incluye
intervalo de confianza de la media, que no es un límite para excluir muestras.
No se encontró fórmula KURT ni regla explícita de eliminación en esos libros.
La curtosis y las señales de influencia se agregan como diagnósticos nuevos.

El modelo de la captura del usuario tiene n30, R²≈0,2387 y ajustado≈0,1509;
los VIF≈1–2,14 no señalan colinealidad severa. No se atribuye ese ajuste a
atípicos sin examinar su matriz. Se conservan los mínimos acordados de tres
factores y diez muestras completas por factor: excluir de treinta requeriría
completar o incorporar otras muestras comparables. No se alteraron sus datos.

## Fuentes

- NIST: https://www.itl.nist.gov/div898/handbook/eda/section3/eda35b.htm
- NIST: https://www.itl.nist.gov/div898/handbook/eda/section3/eda35h.htm
- Excel KURT: https://support.microsoft.com/en-us/excel/functions/kurt-function
- Fórmulas diagnósticas Minitab (nuestros umbrales son heurísticos explícitos):
  https://support.minitab.com/en-us/minitab/help-and-how-to/statistical-modeling/regression/how-to/fit-regression-model/methods-and-formulas/diagnostic-measures/

## Verificación y límites

220 pruebas JS, 1551 PHP, lint de vistas, build y tamaño inicial 78,4 KB gzip.
Pruebas reproducen S2 Hoja1, KURT publicado por Excel, leverage analítico y Cook
contra ajuste con observación retirada. Escapado y conservación de matriz.
QA local con 34 observaciones ficticias: nueve SVG válidos, navegación directa
al inmueble, escritorio y viewport estrecho sin desborde global ni errores.
Las tablas conservan desplazamiento horizontal. No hay DDL/persistencia nueva.

No se implementan todavía ANOVA, significancia t/F, intervalos de coeficientes,
validación fuera de muestra ni aplicación al sujeto. Los resultados están en
memoria y se conservan mediante el informe existente. Actualización del hosting
pendiente a cargo del usuario; los diagnósticos requieren recalcular allí.
