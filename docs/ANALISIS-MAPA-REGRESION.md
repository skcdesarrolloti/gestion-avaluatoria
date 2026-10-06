# Localización comparada y modelo de regresión

M4 tiene tres entradas: Muestras y depuración, Coordenadas y mapa comparativo,
Modelo de regresión. Conserva las cuatro etapas anteriores y sus historiales.

## Localización

Se trabaja sobre el grupo aplicado y la ficha principal del inmueble. Latitud,
longitud, precisión (exacta/aproximada), fuente y soporte usan campos existentes;
los anuncios secundarios conservan sus coordenadas anteriores. El sujeto se lee
del capítulo de información básica; no se inventan coordenadas ni se geocodifica
silenciosamente una dirección. Coordenadas sin precisión o soporte son pendientes.
La fila que está siendo editada no desaparece al terminar la última entrada.

El gráfico usa proyección local equirectangular, proporciones geográficas y norte;
no es un plano de calles o linderos. Distancia al sujeto: Haversine, radio6371km.
Permite descargar SVG y HTML con gráfico, coordenadas, distancias y evidencia.
La cartografía OSM del sector se consulta aparte y no tiene marcadores superpuestos.
Los archivos descargados pueden incorporarse al entregable, no se insertan
automáticamente en su PDF. Esperar confirmación del autoguardado antes de exportar.

## Regresión

Academia primero, basada en NIST, y luego preparación/aplicación. Factores aplicados,
área contada una vez, mínimo acordado3factores/30filas/10porfactor. Este criterio
del ejercicio no se presenta como teorema o garantía de suficiencia estadística.
Y elegible: oferta/área publicada o descuento/área publicada. No asume cero
descuento; la segunda base sólo usa descuentos existentes. No altera denominador.
Textos y rangos requieren código numérico explícito y confirmación del analista;
codificar edades por rangos supone relación lineal ordinal, no edades exactas.
No transforma categorías nominales automáticamente en números ni crea dummies.

Cálculo QR Householder con predictores estandarizados, coeficientes devueltos a
sus unidades; rechaza factores constantes o matriz sin rango. Entrega n, β,
R², R²ajustado, error residual, VIF y matriz/predicciones/residuos en HTML.
No declara validación completa, intervalos ni valor adoptado del sujeto. Falta
evaluar residuos, independencia, homocedasticidad y comparabilidad antes de adoptar.
La preparación (base/códigos/confirmación) persiste en analysis_factor_selection,
con validación servidor, CAS/CSRF existentes y sin DDL. La ejecución es en memoria;
su archivo descargable conserva fecha y codificación. Cambiar datos exige recalcular.
Las simulaciones siguen marcadas en pantalla e informe. No modifica originales.

Validación:1551PHP/215JS/223BD3399, lint/build78,3KB. Navegador local con34muestras
ficticias, confirmación de códigos, cálculo y vista de coordenadas; escritorio/móvil.
Sin actualización del hosting ni cambio en datos de producción.
