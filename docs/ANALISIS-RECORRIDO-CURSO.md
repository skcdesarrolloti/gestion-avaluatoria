# Recorrido estadístico integrado en M4

2026-10-06. M4 → 3. Análisis estadístico paso a paso → 4. Modelo de regresión.
Las muestras/depuración y el mapa conservan sus entradas. Academia del modelo
queda como explicación desplegable dentro de Preparar y calcular, sin pestaña
separada. Regresión conserva cálculo/diagnósticos y sus guardas anteriores.

## Fuente y secuencia

Se leyó completo el PDF de 51 páginas «Sesión 1 · Estadística en valuación
(Res. IGAC 0941 2026)», título interior «Curso Aspectos Técnicos Estadísticos
para la aplicación del Método de Mercado en Avalúos Inmobiliarios», sesión
1 de 3, 29/09/2026. Fórmulas de MAPE y tablas de páginas16/33/34/49/50 revisadas
visualmente. Original sin cambios. Sesiones2/3 todavía no aportadas; no se
atribuye al profesor una metodología de regresión ni marcas de clase ausente.

Siete pasos con explicación, fórmula, resultado e interpretación juntos:

1. Preparar la muestra: grupo aplicado, base explícita, oferta, descuento,
   final, área y estado individual. Datos pendientes permanecen visibles.
2. Bloques: histograma, tabla de frecuencias, acumuladas, marcas, miembros
   identificados y media/CV descriptivos de cada bloque. Complemento al curso.
3. Centro: media, mediana, moda exacta/clase modal, recortada20% por cola,
   geométrica y MAPE de cada centro respecto de la misma muestra.
4. Dispersión: rango, varianza/s muestrales n−1, CV y desviaciones por fila.
5. Sensibilidad: centros contrastados, SKEW/KURT corregidos, RIC, MAD y
   distancia robusta, con consideraciones y revisión directa del inmueble.
6. Precisión: intervalo t90/95/99% de la media; bootstrap opcional con progreso.
7. Conclusión: texto del analista y memoria descargable siguiendo ese orden.

## Mecánica

- El descriptivo sólo exige valores unitarios disponibles (al menos2 para s/IC).
  No exige completos los predictores ni aplica el mínimo30 de regresión.
- Base ajustada requiere descuento registrado, incluso0, y valor final válido.
  Base oferta es exploratoria; no sustituye negociación documentada.
- Se conserva el denominador publicado actual del ejercicio y sus avisos PH.
  No se inventan área privada, valores de garajes ni depósitos. Antes de un
  avalúo formal falta confrontar área y depurar componentes en la sección PH.
- Clase: k=ceil(log₂n+1), amplitud=rango/k; extremos [a,b), último cerrado.
  Constante: un bloque. No reemplaza datos individuales por marcas para los
  otros cálculos, no elimina observaciones ni presume submercados por precio.
- Recortada: floor(0,20n) por cola. No cambia el grupo ni la regresión.
- RIC y z robusto=.6744897501960817(x−mediana)/MAD señalan revisión (1,5RIC y
  |z|>3,5). MAD0 produce diagnóstico no estimable, no división por cero.
- t mediante beta incompleta regularizada y búsqueda de su cuantil, no z.
  IC de media, no de un precio individual ni error del avalúo. Independencia
  y representatividad siguen pendientes de evaluación por el analista.
- Bootstrap:10000 remuestreos con reemplazo, tamaño n, Mulberry32/semilla2026,
  percentiles interpolados de media/mediana/CV. Pausa cada250 para actualizar
  progreso. CV no calculable en remuestras de media0 se omite de su intervalo.
  No aumenta n ni genera muestras inmobiliarias.
- Regresión muestra por inmueble el factor/código o valor final pendiente.
  Treinta es mínimo; todas las filas numéricas completas se utilizan.
- Cambios de datos, base o confianza invalidan la descarga hasta actualizar.
  Resultados son ejecuciones explícitas en memoria, no cálculos por pulsación.

## Conservación y límites

No cambia datos originales, selección ni historial anterior. No escribe a BD
ni inventa descuentos/parqueos. Notas/conclusión nuevas se mantienen en la
ficha abierta y se conservan en HTML descargado; la interfaz advierte que aún
no están autoguardadas. Sus eventos no anuncian un guardado de notas inexistente.
La memoria incluye fórmulas, todos los valores/pendientes, clases/miembros,
gráfico, consideraciones, IC y bootstrap si se ejecutó. Hay identificación
exploratoria y simulación. Todavía no genera valor adoptado ni inserta en PDF.

## Verificación

225 JS,1551 PHP, lint, build/tamaño78,4KB; sin nuevas dependencias ni DDL.
Pruebas reproducen muestraA del curso, s² e IC95%[2,7198;2,9486], cuantil t
df1, semilla repetible, clases sin pérdida/duplicación, extremos/serie constante,
descriptivo34 frente a regresión30 y motivos individuales. Escapado de memoria.
QA local ficticio34: histograma7clases y acumulada34, ICt y10000 remuestreos,
notas y revisión directa, pasos/breadcrumb, SVG válido, sin errores de consola.
La revisión distingue dato faltante de categoría sin código: esta última lleva
al campo de codificación. QA confirmó el foco y regresión con n=34, sin tope de
30. El informe de regresión conserva el total del grupo y los motivos pendientes.
Escritorio visual; viewport estrecho DOM585px/doc562 sin desborde global (la
captura estrecha no fue devuelta por el navegador). Exportación verificada por
contenido en pruebas; el control de navegador no devolvió ruta de descarga.
Hosting pendiente de actualización por el usuario. Recalcular allí para usar
las muestras reales del ejercicio; QA local no sustituye sus resultados.

Interpretaciones por paso (2026-10-06): cuadro destacado con resultado observado,
significado y acción siguiente, derivados de la ejecución actual. Incluye conteos,
clases modales, centros y diferencia media/mediana, menor MAPE descriptivo, CV frente
a ambos referentes del curso sin inferir ámbito, señales por inmueble, dirección
de asimetría/curtosis, IC y contraste bootstrap cuando existe. No convierte estas
señales en exclusión, normalidad, valor adoptado ni aprobación de regresión. Las
siete lecturas también se incluyen en la memoria. Corregida unidad del descuento
a porcentaje y su fórmula explicativa; no cambió el cálculo ni los datos.
227 JS / 1551 PHP / lint / build 78,4 KB. QA local comprobó los siete pasos sin
errores de consola y lectura de dispersión en escritorio. Notas sin persistencia
nueva; hosting pendiente.
