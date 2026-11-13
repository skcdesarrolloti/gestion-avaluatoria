# Renta: exclusiones con constancia

13/11/2026. El usuario autorizó cálculos exploratorios y exclusiones justificadas.
Acceso: capítulo 8, Renta, etapa 2, **Análisis y exclusiones**.

## Comportamiento

- Cociente del importe anunciado mensual / área publicada en COP/m²/mes.
  Administración, IVA, anexos y modalidades requieren verificación; no se restan
  importes desconocidos ni se inventan áreas. No es canon neto adoptado.
- Compara colección calculable actual y muestra conservada: cantidad, media,
  mediana, desviación muestral y CV sin redondeo previo.
- Señales por RIC (1,5), Z robusto (>3,5) y Chauvenet (n × cola normal bilateral
  <0,5). Chauvenet supone normalidad; ninguna señal elimina automáticamente.
- CV ≤7,5% es un umbral, no prueba suficiente de comparabilidad o adopción.
  Ayuda: artículo 21 de la Resolución IGAC 0941 de 2026 y publicación del NIST.
- Seleccionar muestra y completar razón, justificación y soporte. Escribir solo
  autoguarda borrador; **Excluir del análisis** aplica la decisión. Reincorporar
  exige también motivo y soporte y agrega un evento, sin borrar el anterior.
- Constancia: fecha UTC (pantalla en Colombia), responsable, motivo, soporte,
  IDs participantes y cifras antes/después. Guarda fuentes de cada decisión
  aunque cambie la matriz; descarga JSON. Muestra aviso si la matriz cambió.
- Conserva anuncios, fotos, contratos y ambos métodos de Renta. No adopta canon,
  tasa de capitalización, tasa de descuento ni valor final del inmueble.

## Persistencia y controles

Sin DDL: `methodology_workflow[component].income_selection`, aislado mediante
`MethodStudyState` al cambiar de método. `drafts`, `excluded`, `datasets` por
SHA-256 de filas normalizadas, `events` acumulativo, `last_sample`.
Cada dataset conserva importe, área, cociente, URL, ID y condición exploratoria.
No se recortan eventos ni se escriben cambios a captura.

POST `metodologia-valuatoria/renta/seleccion`: CSRF, propietario, recorrido
`renta:arriendo`, transacción con bloqueo del expediente y CAS de metodología
y comparables. El servidor calcula cifras, autor y fecha; no recibe historial
del cliente. Matriz o versión obsoleta responde 409.
GET `metodologia-valuatoria/renta/constancia`: descarga autorizada por expediente.

Inactivos/no seleccionados/excluidos en captura, operación no confirmada, URL de
venta, importe no mensual o precio/área ausentes no forman cociente. Reincorporar
no subsana esos pendientes. Colecciones de 0/1 no indican CV=0.

## Validación

Lint PHP; `php tests/run.php`; `npm test`; `npm run build`; `npm run check:size`.
BD local desechable con `GA_TEST_PORT=3385`: `php tests/database.php`.
Servidor de fixtures en8088, `GA_TEST_HTTP=true`, mismo puerto BD:
`php tests/income-selection-http.php`. Cubre permisos, CSRF, inactividad, 422,
409, persistencia, retorno desde Mercado, conservación y exportación.
Navegador local: exclusión/reincorporación sin recarga completa, historial,
autoguardado, recarga y formulario adaptable a390 píxeles CSS.
Pruebas JS cubren guardado pendiente y sustitución del panel completo.
La suite compartida cubre desconexión, reintento y conflictos.

No se excluyen anuncios del usuario durante la publicación. Estadísticas
completas, regresiones y valoración final continúan fuera de esta entrega.
