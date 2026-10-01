# Properati y red Proppit

Actualización 30/09/2026. Captura por página copiada para oficinas en venta de la
ciudad del expediente, con la misma selección conservadora de Ciencuadras.

Proppit gestiona publicación en distintos portales; no se usa como API pública de
consulta de todo el mercado. La fuente guardada sigue siendo Properati y sus notas
identifican Red Proppit. El selector muestra «Properati · Red Proppit», una fuente
a la vez. La ayuda incluye Properati, Mitula, Punto Propiedad, Trovit, Nuroa y
Nestoria; esta entrega habilita el lector por lote de Properati, no de los seis.

Referencias revisadas: [Proppit](https://blog.proppit.com/que-es-proppit/),
[cobertura por país](https://info.proppit.com/es/support/d%C3%B3nde-se-publicar%C3%A1n-mis-propiedades)
y el enlace PUBLICAR de Properati que dirige a Proppit. La cobertura depende del
país y plan; no se supone que todos los portales tengan el mismo inventario.

## Uso

1. Abrir búsqueda en Properati. Ruta comprobada para oficinas en venta en Bocagrande:
   https://www.properati.com.co/s/bocagrande/oficina/venta.
   Otros barrios de Cartagena abren la ciudad y piden seleccionar/comprobar barrio.
2. Copiar la página completa con Ctrl+A/Ctrl+C y pegar en el aplicativo con Ctrl+V.
   El pegado prepara, no agrega. Repetir por página del portal.
3. Agregar sugeridos sin coincidencias y comprobar el estado de guardado. La
   selección opcional permite revisar los omitidos; el análisis sigue en la matriz.

Lector limitado a tarjetas publicadas con título de oficina en venta, ubicación
de la ciudad y precio/área inequívocos. Omite proyectos, otros tipos/ciudades,
precios «desde» o mensuales, áreas ausentes y enlaces ajenos. Nunca completa PH
desde el expediente ni interpreta amenidades como régimen. Fotos se adjuntan en
la matriz. Cada aviso queda por verificar; coincidencia no prueba identidad.

HTML del portapapeles se lee en template separado e inerte, nunca se inserta en la
interfaz. Enlaces HTTPS solo de properati.com.co/detalle; se quita seguimiento.
No se lee JSON interno ni se hacen peticiones servidor a Properati. Máximo 2 MB
por pegado y 60 muestras por matriz, conservando filas existentes y avisando overflow.
No cambia esquema, autorización, CSRF ni persistencia optimista/autoguardado.

## Validación

- Navegador real: 41 resultados de Bocagrande en dos páginas; primera página
  contiene 30 tarjetas, 27 con datos admitidos por el lector.
- Vista local: pegado no agrega; 26 sugeridos y una coincidencia interna; al agregar
  pasa de 12 filas ficticias a 38, conserva datos y bloquea una segunda incorporación.
- Regresión Ciencuadras: sigue preparando las 20 tarjetas de la copia real.
- Escritorio comprobado visualmente; tamaño móvil por DOM sin desbordamiento
  horizontal. La captura de pantalla móvil agotó tiempo del navegador.
- 90 pruebas JS, 370 verificaciones PHP, lint PHP, build y 54.9 KB gzip (límite 80).
  No hay migración ni nueva prueba de base de datos. No se importó a producción.

Publicar código y assets juntos en el hosting; subir a Git no actualiza el hosting.

## Respuesta visible al pegar

El cuadro conserva una vista abreviada del texto recibido (hasta 4.000 caracteres).
El resultado aparece encima y se desplaza a la vista tras pegar; distingue contenido
vacío, contenido no reconocido y avisos preparados. No cambia el lector ni agrega
muestras automáticamente. El HTML externo sigue separado de la interfaz.

Ante un reporte de tarjetas no reconocidas, repetir la copia directa desde Chrome
preparó 27 avisos, con 13 sugeridos frente a las 44 muestras de la matriz. No se
estableció la causa del primer pegado y no se incorporaron muestras en esa prueba.
Se verificó la nueva respuesta en escritorio y en un contenedor móvil de 390 px.
Validación: 90 pruebas JS, 370 verificaciones PHP, lint de la vista, build y 55,0 KB
gzip. Sin cambios de esquema o persistencia; no se ejecutó la suite de base de datos.
