# Ciencuadras: validación de captura

Revisión del 30/09/2026. La navegación normal permite buscar oficinas en venta
con `https://www.ciencuadras.com/venta/oficina?v=Bocagrande`: primera página con
20 avisos de 29 resultados. La ciudad y barrio deben comprobarse en cada tarjeta;
el parámetro `v` es búsqueda textual, no un identificador geográfico.

La solicitud HTTP directa devolvió 403 «Acceso denegado». No se implementó ni
se anuncia lector automático por servidor. Sigue disponible el pegado de enlaces
y datos, o un bloque tabulado con encabezados fuente, enlace, precio, area, barrio
y operacion. Pegar enlaces solos no descarga los datos ni las fotografías.

Se corrigió la unidad al pegar una operación explícita: un aviso de arriendo o
venta con operación Venta y precio de compra queda en precio total, no canon.
PH y datos ausentes siguen por verificar. Se mantienen las comprobaciones de
duplicados y el límite de 60; no se modifica automáticamente la matriz existente.

Validación: navegación real, prueba de importación tabulada con URL de doble
operación, suite PHP/JS, lint, build y presupuesto de assets. Sin migraciones.
