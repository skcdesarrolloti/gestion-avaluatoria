# Dos calificaciones en 3.4

3.4 reúne dos subpestañas: Calificación valuatoria actual y Factores para
investigación · módulo 8. Se conservan íntegros el catálogo anterior, sus datos,
pesos, escala 1–5 y fórmula del índice/ajuste. `#factores` sigue abriendo la segunda
subpestaña; no hay otra pestaña principal de factores.

La investigación incorpora las características observables del catálogo anterior
por tipo. Vista, acabados, altura, frente y piso se vinculan al factor actual cuando
existe, sin convertir automáticamente clases descriptivas en medidas o códigos.
Los demás se presentan plegados como diferenciales para investigar. Evidencia
fotográfica, impacto valuatorio y otro diferencial son soporte/interpretación,
no predictores creados automáticamente. Los nuevos factores descriptivos conservan
clases sin jerarquías inventadas; su definición puede editarse en el catálogo.

| Tipo | Entradas del catálogo anterior | Factores para investigar | Complementarios de 3.4 |
|---|---:|---:|---:|
| Oficina | 24 | 32 | 19 |
| Apartamento | 21 | 36 | 16 |
| Casa | 21 | 43 | 16 |
| Lote | 21 | 24 | 18 |
| Local | 21 | 25 | 16 |
| Bodega | 24 | 32 | 20 |
| Consultorio | 30 | 39 | 25 |
| Edificio | 50 | 56 | 47 |
| Finca | 42 | 61 | 37 |
| Hotel | 19 | 37 | 16 |
| Garaje | 14 | 20 | 11 |
| Depósito | 8 | 12 | 5 |

Conteos del catálogo predeterminado, sin personalización, bases de área ni filtro
de destinación. Depósito sólo tenía Base común en la calificación anterior. La
integración no aprueba nuevas listas especializadas pendientes de revisión.

El dato para investigar se confirma con soporte. La tarjeta muestra la observación,
calificación, peso y notas previos cuando existen. No se copian puntuaciones,
porcentajes ni pesos a los coeficientes de regresión. Guardar una clase no modifica
`special_attributes_json`; se conserva en `subject_factors_json`, con versión
optimista, autorización por propietario y CSRF existentes.

En módulo 8 se muestran todos los factores por defecto. Se eliminó el bloqueo de
cuatro candidatos en servidor y navegador. Elegir candidatos prepara investigación;
no estima regresión ni prueba idoneidad estadística. Se conservan fuentes, muestras,
calificaciones manuales con soporte y comparación entre anuncios vinculados.

La revisión encontró dos errores de visualización de la calificación anterior:
las claves numéricas de opciones no coincidían estrictamente con cadenas guardadas,
y el índice JS se leía antes de inicializar las selecciones Alpine. Se corrigió
la recuperación de selección y el momento de lectura, sin cambiar la fórmula.

Validación: 1387 PHP, 154 JS, 210 BD en instancia nueva 3389, lint/build/tamaño
71,2 KB. Navegador local: guardar/recargar 4 y peso 3, ajuste +5% e índice 80%,
guardar clase investigada sin alterar esa valoración, navegación y vista estrecha.
