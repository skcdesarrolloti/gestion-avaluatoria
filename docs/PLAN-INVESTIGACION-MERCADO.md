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
- Referencia configurable: 10 inmuebles por coeficiente, no requisito normativo
  ni suficiencia estadística. Categorías con k clases prevén k−1 coeficientes.
  No introduce ponderaciones económicas del analista ni trata códigos como distancias.

Datos sólo consultados: no modifica anuncios, precios, áreas, fotos, selección,
coordenadas, Excel ni identidad. Coordenadas y depuración PH quedan en Análisis.
El precio comparable por m² y la calidad de la futura regresión requieren verificación
posterior: la disponibilidad de factores no garantiza un modelo válido.

Persistencia aditiva en methodology_workflow[component].research_plan, con fecha UTC
del servidor, propietario, CSRF y methodology_version. No requiere migración.
Autoguardado 800 ms y confirmación del servidor; versión obsoleta devuelve 409.
Actualizar consulta usa la navegación existente después de guardar la captura.

Límites: altura/acceso/servicio/niveles sin campo estructurado en las muestras siguen
pendientes, aunque estén en su descripción original. Ascensores en la ficha del sujeto
pueden estar calificados por nivel; esa clase no se transforma en presencia sí/no.
La identidad no confirmada entre portales mantiene inmuebles potenciales separados.
Validación estadística, corroboración y control normativo corresponden a M4.