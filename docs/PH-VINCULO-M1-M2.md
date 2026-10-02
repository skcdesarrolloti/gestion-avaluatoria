# Vínculo PH y composición en M1 y M2

M2 Mercado muestra «PH · vínculo y composición» cuando el expediente está marcado
PH. Cada anexo selecciona una principal activa del mismo expediente, su naturaleza,
relación con el área privada registrada (incluida, excluida, no privada o pendiente),
fuente y explicación. No se infiere el vínculo por nombre, cantidad o matrícula.
La principal muestra el resumen de anexos y enlaces a sus respectivos M2.

Naturaleza y documento de identificación usan los mismos campos del numeral 3,
sin duplicar su almacenamiento. El vínculo y composición están en market_evidence_json
existente. M2 escribe únicamente su parche, preservando matrícula, usos, coeficiente
y demás soportes. Una pantalla anterior del numeral 3 conserva los nuevos campos
omitidos. Versión optimista compartida: 409 ante conflicto, propiedad y unidad activa
validadas en servidor. CSRF obligatorio. No se agrega esquema ni migración.

## Controles de M1

- Unidad independiente: área privada, matrícula y coeficiente propios con soporte;
  confronta matrícula repetida y tratamiento integrado del anexo independiente.
- Parte privada integrada: mantiene su área diferenciada; consulta matrícula de la
  principal por vínculo explícito y contrasta matrícula propia si fue registrada.
  No exige ni suma coeficiente independiente; confronta valor separado en M2.
- Común de uso exclusivo: área privada y coeficiente propios no aplican cuando
  identidad y documento están registrados; el vínculo registral se consulta de la
  principal. Si hay área capturada como privada, muestra diferencia y enlace a 3.2.
  No borra la superficie ni convierte la naturaleza por el nombre del anexo.
- Nuevo control PH de vínculo/composición: fuente obligatoria para considerarlo
  completo, principal activa y relación coherente con naturaleza. La principal
  señala vínculos faltantes o pendientes de sus anexos.

M1 mantiene estados verde, rojo, amarillo y no aplica. Enlaces de datos físicos y
jurídicos llevan al numeral 3; vínculos y tratamiento llevan a M2. Al guardar,
esperar confirmación y volver a «Actualizar verificación M1». No se certifica la
autenticidad del documento ni se interpreta un campo completo como aprobación.

## Límites y comprobación

No modifica áreas del capítulo 1/3, muestras, métodos o tratamientos automáticamente.
No suma áreas, reparte precios, depura comparables ni adopta valor. M4/M5 quedan
pendientes. Análisis del comparable (art. 19.2.b) y liquidación del sujeto (art. 36)
continúan distinguidos.

606 checks PHP, 116 JS, 139 checks BD local desechable (3360), 7 HTTP locales,
lint, build y 59,0 KB gzip. Prueba de navegador con datos ficticios: autoguardar
garaje común, recargar, vínculo persiste y M1 presenta 8 de 8 controles completos
y 2 no aplican. Escritorio y CSS 390 px sin desbordamiento ni errores de consola.
Sin lectura o escritura directa en la base de producción ni asignación de la
naturaleza jurídica del garaje o depósito reales del expediente.
