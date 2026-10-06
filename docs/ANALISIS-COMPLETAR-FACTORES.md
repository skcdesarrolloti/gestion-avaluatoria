# Completar datos de factores en Análisis

En M4 > 4. Resultado depurado, la tabla distingue datos recogidos (verde),
faltantes (amarillo) y complementos manuales con soporte (azul). El texto
acompaña al color. El analista puede filtrar las filas pendientes y digitar
los factores faltantes con fuente, fecha y responsable. Los atributos originales
se conservan; no se reemplazan por el complemento ni se inventan ceros.

Los complementos se guardan en `analysis_manual_factors`, dentro de
capture_details existente, por ID del anuncio principal. JSON validado en
servidor: hasta 160 claves, 48.000 caracteres; etiquetas 180, valores 500 y
soportes 1.600 caracteres. Un borrador sin soporte se conserva, pero no cuenta
como dato completo. No requiere migración. Las pantallas anteriores que omiten
el campo mantienen la información por ID.

La cobertura, las filas completas y las sugerencias se actualizan al completar
el dato y soporte. Actualizar resultado con los datos completados agrega una
etapa al historial; nunca recalcula las cifras de las etapas anteriores.

Se muestran hasta tres combinaciones de área obligatoria más dos candidatos,
ordenadas por filas conjuntamente completas con oferta positiva y áreas válidas.
Cada candidato cumple compatibilidad, cobertura mínima 50 % y variación.
Área privada sustituye el predictor área publicada y no se cuenta dos veces.
La base monetaria oferta/área publicada permanece igual. Para 3 factores se
requieren 30 filas completas. El botón Completar inmuebles de esta combinación aplica esa elección explícita,
registra la etapa y lleva a las filas pendientes, enfocando el primer campo visible.
Ver anuncio permite consultar la ficha; el dato y soporte se completan ahí mismo.
La fila en edición permanece visible hasta actualizar, para poder terminar el soporte.
Si no hay pendientes se muestran los inmuebles de la combinación. No ejecuta regresión. Cumplir cantidad no
acredita codificación, correlación, colinealidad, comparabilidad o descuentos.

Verificación: 1545 PHP, 208 JS, 221 BD local desechable en puerto 3397;
lint, build y 78,3 KB gzip inicial. Navegador local con datos ficticios: celda
amarilla editable, dato sin soporte pendiente, cero explícito con soporte azul,
conteo 2→3, autoguardado confirmado y pantalla estrecha sin desborde ni errores.

Acceso directo validado (2026-10-06): 1545 PHP, 210 JS, lint, build y
78,3 KB inicial; navegador local confirma filtro, foco, edición de soporte sin
perder la fila y ancho estrecho. Persistencia permanece igual.


## Ejercicio autorizado con 34 muestras

El 06/10/2026 el usuario autorizó completar los faltantes para un ejemplo.
Se conserva published_attributes original; analysis_manual_factors identifica
cada complemento con fuente que comienza EJEMPLO SIMULADO. La tabla muestra
estos valores en morado con texto no verificado y aviso de ejercicio. Las nuevas
etapas preservan simulated=true en el historial; anteriores no se reescriben.
La marca no modifica fórmulas, descuentos, selecciones ni componentes.

Se cachean las proyecciones del portal y perfiles de factores por sus valores
fuente, invalidando datos manuales, soporte y composición del grupo. Evita
releer todas las fichas por cada celda. 1547 PHP/212 JS/222 BD local3398;
compilación/lint y 78,3 KB gzip. QA ficticio34: 29→34 con cinco complementos,
aviso de simulación, historial identificado y sin errores de consola.
