# Dispersión visual

La nube en Entender la muestra → Dispersión conserva cada observación válida.
La altura es el valor unitario; el eje horizontal es la posición en el listado,
no una variable explicativa ni una serie temporal. No se crea una regresión.

Se utiliza el criterio exploratorio existente de 1,5 RIC; únicamente valores
estrictamente fuera aparecen rojos, con cruz y texto. Los límites se muestran
con líneas, su valor numérico y fórmula plegable. La media se mantiene como
referencia de la dispersión y no se sustituye por el centro de menor MAPE.

Fuente técnica: NIST, Box Plot,
https://www.itl.nist.gov/div898/handbook/eda/section3/boxplot.htm.
No se presenta como una regla legal de exclusión o precios admisibles. El
criterio puede producir señales que necesitan investigación de comparabilidad.

Unidades, descripción accesible, etiquetas escapadas y lista de observaciones
señaladas permiten revisión sin depender únicamente del color. En móviles,
la nube admite desplazamiento horizontal para conservar legibilidad.
Se incorpora el gráfico y límites a la memoria descargable existente.

Validación: PHP1561, JS232, lint789, build/check:size78,5KB. Pruebas verifican
cantidad/conservación de puntos, señales, límites inclusivos, series constantes
y escape. QA local con34 ficticios y extremos4,2/16millones: dos señales,
academia, escritorio y móvil375px sin desbordamiento global ni errores de
consola. Exportación verificada. Sin modificación de BD ni ejecución sobre
muestras reales del hosting. No se alteran fórmulas ni se excluyen inmuebles.

La barra de cuartiles incorpora ahora un punto por inmueble en su valor exacto.
Valores próximos o repetidos se apilan verticalmente sin desplazar el precio.
Cada punto tiene identificación y valor accesibles; rojo con cruz conserva las
señales de los límites RIC existentes. Los cortes coincidentes no forman nuevos
grupos. Misma representación en memoria exportada. PHP1561/JS234/lint790;
build/check:size78,5KB. QA local:34 puntos/dos señales, escritorio y móvil sin
desbordamiento global ni errores. Pruebas de apilamiento, conservación, serie
constante y escape. Sin cambios de persistencia, fórmulas o selección.
