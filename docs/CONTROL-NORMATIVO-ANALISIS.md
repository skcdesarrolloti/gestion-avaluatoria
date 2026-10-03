# Requisito permanente del capítulo 8

Solicitud expresa del usuario, 2026-10-03: si el análisis contradice un artículo
aplicable o no cumple un requisito, debe advertirlo. No olvidar al desarrollar
Mercado, Costo, Renta, Residual, integración ni entregable.

## Implementado ahora

Academia → General centraliza los artículos 5, 11, 14, 15 y la consulta contextual
27, 36 y 37, con lectura completa y utilidad. La revisión común de normas se
presenta allí, sin repetirla en cada método. Configuración enlaza esta academia.
PH conserva la distinción del sujeto (36) y sus comparables (19.2).
Aviso permanente en Análisis de todos los métodos: artículo, requisito,
discrepancia/incumplimiento/falta de soporte, incidencia y acción pendiente.
Esto es orientación; no detecta automáticamente todo el cumplimiento normativo.

## Pendiente obligatorio al desarrollar el análisis

- Vincular cada control computable con artículo/inciso, alcance, unidad y método.
- Emitir advertencias con el dato o decisión que activa el control y evidencia.
- Distinguir incumplimiento detectado, contradicción, falta de soporte, no aplica
  y revisión profesional pendiente. Un campo lleno no acredita cumplimiento.
- No inferir incumplimiento sólo por una palabra en la memoria, ni certificar
  cumplimiento universal por ausencia de alertas. Las reglas no computables
  requieren revisión técnica documentada.
- Mantener advertencias en la memoria y entregable, con estado de resolución y
  justificación verificable; actualizar cuando cambien datos/soportes.
- No corregir valores, homologar, excluir muestras ni cambiar el método de forma
  silenciosa. Una advertencia no inventa evidencia ni reemplaza al avaluador.
- Probar contradicción conocida, requisito sin dato, no aplicabilidad sustentada,
  corrección posterior y conservación de advertencias de otros recorridos.

No se implementa este motor en la reorganización de Academia. No hay cambios de
esquema, datos, muestras o fórmulas. Las etapas operativas continúan con sus
controles parciales existentes.

Validación de esta entrega: PHP827, JS131, lint/build y 66,4KB gzip. Chrome local
configuración sin bloque de artículos; entrada a General, subtemas elección/PH,
lector completo art.36 abre/cierra; regreso a Mercado; aviso de Análisis visible.
Móvil CSS367/scroll367 sin desbordamiento. Sin cambios de persistencia.
