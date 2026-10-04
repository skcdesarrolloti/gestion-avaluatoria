# Factores del sujeto · capítulo 3

Captura por unidad en «Factores del sujeto», con búsqueda por nombre y pestañas
de unidades. Usa el catálogo compartido de los 12 tipos de inmueble y las escalas
guardadas del propietario; los anexos usan su propio tipo, no el de la oficina.
Las áreas permanecen en Superficie y la destinación es un filtro, no un factor.

Cada atributo guarda valor o clase, soporte y clasificación vigente. Cantidades
y edades conservan su medida; binarios y ordinales muestran su código compartido.
Vista conserva cuatro clases nominales sin puntaje ni jerarquía. Un dato desconocido
queda pendiente: nunca se convierte automáticamente en cero o «No».

Los datos existentes se muestran para comprobarlos; «Usar dato existente» copia
sólo valores compatibles y exige completar su soporte. Guardar campos nuevos vacíos
no sustituye esos datos. Las clasificaciones anteriores se conservan y requieren
confirmación explícita al adoptar otra escala. No hay recodificación automática.

El capítulo 8 consulta esta referencia; una captura del sujeto no puede sustituirse
por otra calificación manual desde Insumos. Cambiar valor o soporte cambia la huella
del factor. Si el recorrido tiene una escala anterior, debe revisarse/adoptarse allí.

Migración aditiva `202610040002_subject_factor_capture.php`: JSON y versión en
`appraisal_units`, sin rellenar registros existentes. Guardado CSRF, propietario,
unidad activa y versión comparada atómicamente; error 409 ante cambios concurrentes.
Esto prepara observaciones para Análisis: no implementa regresión, coeficientes,
correlaciones ni selección estadística de factores.

Verificación: 1073 comprobaciones PHP, 153 JS y 191 de persistencia en bases
de prueba nuevas, puerto 3377; navegador local con datos ficticios, guardar/recargar
Vista y Ascensor y consulta en capítulo 8. Lint, compilación y límite gzip verificados.
