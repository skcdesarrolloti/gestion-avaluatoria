# Plan de investigación · Mercado / Renta

Capítulo 8 → Insumos → 4. Plan de investigación. Configuración propia de cada
unidad, parte y método; no ejecuta regresión, depuración ni adopta valores.

- Factores sugeridos por tipo; sujeto desde sus datos propios del numeral 3.
- Cada factor conserva uso, tipo, definición, categorías y justificación.
- Filtro de contexto, investigar, candidato al modelo o pendiente son decisiones
  de planificación: no alteran ni descartan las muestras.
- Disponibilidad por portal en anuncios guardados. El catálogo observado por portal
  sigue en la pestaña 3; no se promete extracción completa de todos los portales.
- Inmuebles según agrupación confirmada por el analista. Diferencias entre fuentes,
  relecturas y formatos incompatibles requieren conciliación. Un dato complementario
  puede dar disponibilidad preliminar; no se convierte en valor adoptado.
- Vacío ≠ cero; intervalos ≠ edad exacta; piso alto ≠ número de piso.
- Área genérica PH no sustituye área privada construida. Régimen, tipo y operación
  discordantes/desconocidos quedan pendientes del contexto; no cuentan como listos.
- Cantidad conjunta de inmuebles con todos los factores candidatos legibles.
  Se muestran variación y pendientes del sujeto, definición y justificación.
- Referencia configurable: 10 inmuebles por factor candidato (máximo cuatro), no
  requisito normativo ni suficiencia estadística. Categorías con k clases prevén
  k−1 coeficientes; el conteo de coeficientes se conserva para revisión posterior.
  No introduce ponderaciones económicas del analista ni trata códigos como distancias.

Datos sólo consultados: no modifica anuncios, precios, áreas, fotos, selección,
coordenadas, Excel ni identidad. Coordenadas y depuración PH quedan en Análisis.
El precio comparable por m² y la calidad de la futura regresión requieren verificación
posterior: la disponibilidad de factores no garantiza un modelo válido.

Persistencia aditiva en methodology_workflow[component].research_plan, con fecha UTC
del servidor, propietario, CSRF y methodology_version. No requiere migración.
Autoguardado 800 ms y confirmación del servidor; versión obsoleta devuelve 409.
Actualizar consulta usa la navegación existente después de guardar la captura.

Límites: altura/acceso/servicio/niveles requieren un dato estructurado explícito en
la captura; no se deducen de texto ambiguo. Ascensores en la ficha del sujeto
pueden estar calificados por nivel; esa clase no se transforma en presencia sí/no.
La identidad no confirmada entre portales mantiene inmuebles potenciales separados.
Validación estadística, corroboración y control normativo corresponden a M4.
## 2026-10-04 · Cuadro por inmueble y portal
En Plan de investigación: selector de un inmueble vinculado y tabla con todos los
factores aplicables en filas (o sólo candidatos y área mediante checkbox). Columnas:
factor, sujeto desde su numeral 3, cada anuncio
por portal y validación entre fuentes. El sujeto es referencia, no participa en la
comparación de coincidencias entre anuncios. Área compatible y publicada siempre visibles,
con unidad m² y base original; no hay equivalencia automática total/privada/construida.
Verde sólo si dos o más anuncios tienen datos legibles iguales, sin pendientes de formato
ni relectura. Amarillo ante diferencia o revisión; gris para faltantes/una fuente.
No se comparan propiedades distintas ni se sustituyen datos. Dos avisos del mismo portal
conservan columnas separadas, código y enlace. El cuadro no genera datos desde la referencia.
El conteo conjunto exige área positiva compatible independientemente de su selección como
predictor; el área obligatoria no agrega un coeficiente si no se elige para el modelo.
El ratio de planificación sigue orientativo, no requisito normativo ni regresión.

## 2026-10-04 · Sujeto, catálogo completo y cuatro candidatos
Planta eléctrica y destinación/uso observado amplían el catálogo según tipo.
Se conservan campos explícitos opcionales de captura para estos factores y para
niveles, servicio, altura y acceso en el JSON existente; no se promete extracción
automática completa desde todos los portales. Datos ausentes permanecen pendientes.
Investigar no consume cupos: sólo Candidato al modelo cuenta para el máximo cuatro.
Cliente deshabilita un quinto candidato y servidor rechaza más de cuatro sin borrar
configuraciones anteriores. Meta = factores candidatos × referencia (por defecto 10).
El conteo conjunto considera inmuebles distintos, factores legibles y área compatible;
duplicados vinculados no aumentan la muestra. La clasificación queda en un bloque
cerrado para dar prioridad al resumen, sin retirar definiciones ni justificaciones.

## 2026-10-04 · Dato original y código de investigación
Catálogos iniciales por 12 tipos (incluye oficina); no garantizan publicación de todos
los factores. Tipo ordinal añadido: niveles por líneas en orden ascendente, códigos
0, 1, 2... compartidos por sujeto y anuncios. Numérica conserva medida, binaria No=0
Sí=1, nominal conserva clases sin imponer jerarquía. Orden no demuestra distancias
iguales ni efecto en el precio. Regresión y correlación permanecen en Análisis futuro.
Cada celda separa dato original de código. Planta: No=0, Parcial=1, Total=2; Sí sin
cobertura queda pendiente. La ficha del sujeto permite Total sin alterar Sí anterior.
Planes guardados conservan su tipo y categorías: el analista debe seleccionar Ordinal
y definir sus niveles para cambiar una clasificación anterior. No se migra en silencio.
Checkbox muestra sólo candidatos y área (inicial si hay candidatos), o todos los factores.
Máximo cuatro candidatos; investigación ilimitada dentro del catálogo. Sin DDL.
Pruebas: 1005 PHP, 144 JS, 176 BD en instancia desechable 3367, build 70,2 KB gzip.
Navegador local: oficina, dato Parcial sujeto=1, portales No=0 Parcial=1 Total=2,
guardado confirmado y recarga. Vista estrecha con scroll contenido, sin errores.
