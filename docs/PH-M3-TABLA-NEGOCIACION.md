# PH: captura, negociación y Excel en M3

Ruta: capítulo 8, unidad o banco sin asignar, M3, Tabla de muestras.
PH abre columnas de área privada y componentes; NPH conserva terreno/construcción.
La matriz, importadores, fotos y autoguardado siguen compartidos. Seleccionar PH
no atribuye ese régimen a todas las muestras ni cambia la descripción del sujeto.

## Captura y negociación

- Municipio/fuente, tipo de dato (oferta/transacción/arriendo) y contexto urbano.
- Áreas construidas/libres privadas, parqueaderos y depósitos: presencia,
  cantidad, área, inclusión en precio, naturaleza y soporte. Vacío es pendiente.
- Descuento monetario en la misma unidad del precio, tipo otorgado/estimado,
  contacto, fecha y justificación. No se aplica un porcentaje estándar.
- Valor negociado = oferta menos descuento. % = descuento / oferta × 100.
  Oferta cero y descuento cero producen cero y 0%; no se divide por cero.
- Servidor valida descuento no negativo ni superior a oferta y recalcula el
  derivado. Los resultados enviados por el cliente nunca se persisten.
- Datos nuevos en capture_details existente; sin cambio de esquema ni migración.
  Clientes anteriores conservan campos omitidos por ID; CSRF/owner/version intactos.

Un resultado numérico no acredita negociación real ni suficiencia de su soporte.
M4 mantiene sus estadísticos previos: no adopta negociado como depurado.
La depuración monetaria de componentes sigue pendiente en M4. M3 ahora muestra
área del cociente, base, oferta/m², negociado/m², unidad monetaria y estado.
PH ordinaria usa área privada construida con fuente, sin sumar libre/anexos.
NPH/condominio usa área publicada con base y fuente expresas, como cociente
preliminar integral, sin reemplazar desagregación o análisis. Si régimen, área,
base o fuente faltan, no se calcula. No divide otra vez precio ya en valor/m²;
canon muestra COP/m²/mes. Sin descuento confirmado no hay negociado/m².
Estos cocientes no alimentan aún estadísticas ni valores adoptados de M4/M5.
Servidor y navegador recalculan; valores derivados enviados no se persisten.
Excel exporta fórmulas de área y cocientes y descarta sus valores al importar,
recalculándolos en el módulo a partir de campos originales editables.

Distinción explícita: art. 19.2.b exige depurar componentes del comparable,
incluidos comunes de uso exclusivo. Art. 36.2 trata el sujeto: común de uso
exclusivo implícito, sin liquidación independiente; matrícula separada exige
mercado/restricciones. Privado en misma matrícula no se presume común.
No inventa precio de garaje/depósito ni aplica porcentajes fijos por falta de ofertas.

## Portales y tabla

Cada fuente tiene instrucciones copiables con su lector y campos PH/NPH.
FincaRaíz/Metrocuadrado conservan búsqueda y enlace/texto; Ciencuadras,
Properati/Mercado Libre conservan resultados HTML para oficinas en venta.
Ctrl+A/C/V prepara candidatos; se incorporan con Agregar sugeridos y Guardado.
La guía no hace que un portal acepte un prompt ni inventa datos ausentes.

Conteos visibles por fuente de las filas de la unidad/banco actual, incluidos
cambios aún sin guardar. No se suman las colecciones de otras unidades.
Pendientes se resaltan en amarillo y se listan por muestra; filtro incluye éstos.
Controles operativos no son una certificación normativa.

Excel (.xlsx) exporta todas las filas de la colección, no sólo la página/filtro.
Tabla nativa con filtros, encabezados y primera columna inmovilizados, importes
numéricos, fórmulas de negociación y pendientes amarillos. Texto externo es
inlineStr, nunca fórmula. Generador OOXML del navegador, sin nuevas dependencias.
La descarga espera confirmar el autoguardado. El bloque Excel se muestra al inicio
de la tabla, antes de filtros y explicaciones; permite descargas repetidas completas.
El Excel prioriza fuente/enlace, ubicación, tipo/operación, oferta/unidad, área
publicada, parqueaderos/depósito, contacto y datos habituales del aviso. Después
presenta descuento y cocientes por m²; verificaciones, soportes y demás campos
siguen disponibles, con pendientes e ID al final. Orden propio independiente
del grupo seleccionado en pantalla, sin eliminar campos ni cambiar M4.
Importar Excel actualizado revisa el archivo exportado y presenta los cambios.
Guardar Excel actualizado revalida el archivo y versión en servidor y guarda
datos y fecha de importación en la misma transacción. No usa el autoguardado
para confirmar la carga. El nombre y fecha/hora de Colombia persisten por colección;
seleccionar, cancelar o fallar no cambia la fecha. Excel idéntico también permite
confirmar una nueva carga. Nombre de descarga incluye versión y momento UTC.
La hoja oculta conserva expediente, unidad/banco, versión, campos y opciones;
cada fila conserva ID. Reordenar no altera su identidad; omitir filas no elimina
muestras. Se rechazan IDs ajenos, repetidos, otras colecciones o versiones antiguas.
Campos calculados se recalculan en el módulo; fórmulas editables no se ejecutan.
No se incorporan nuevas muestras desde este Excel: usar Nueva muestra o lectores.
Archivo .xlsx hasta 5 MB; servidor requiere extensiones PHP ZIP y SimpleXML.
Migración aditiva 202610020002_comparable_excel_history agrega historial por
unidad/banco a appraisals. AUTO_MIGRATE o comando migrate aplica sin SQL manual.
PH/NPH omite columnas específicas vacías del otro régimen en Excel; si tienen
información previa, se conserva. No se borran muestras ni datos al cambiar vistas.

## Cobertura normativa

Control plegable fuera del formulario (no introducir otras filas en su tbody).
Contrasta arts. 16–21, 27–28, 36–37 y anexo técnico de estudio de mercado.
Identifica captura disponible y pendientes M4/M5. En particular faltan memoria
monetaria de componentes, COP/m² privado depurado, suficiencia/validación y adopción.
No aplicar homologación por factores: Factor M4 señala una variable de estudio.
No certifica la resolución completa ni altera los otros métodos/informes.

Bocagrande es admisible como dato de prueba por instrucción del usuario en el
expediente de oficina. No se cambió la ubicación física ni el avalúo Zona Franca.

Validación previa: 615 checks PHP, 120 JS, 142 BD desechable 3361; lint, build y
63,3 KB gzip. Navegador: edición, guardado y recarga de 500 millones menos
25 millones = 475 millones (5%), conteo FincaRaíz 1, móvil CSS 390 sin overflow.
Excel: lectura con openpyxl/ZIP/XML valida tabla, panes, fórmulas, caché y estilos.

Importación: 629 checks PHP, 124 JS, 142 BD desechable 3362 y seis HTTP de
sesión, CSRF, revisión sin escritura y aislamiento de unidad. Archivo del generador
reescrito con openpyxl conserva hoja oculta y se lee por HTTP. Navegador verifica
herramientas visibles y consola limpia. Carga UI completa no probada: extensión
Chrome no permite archivos locales; no se cambiaron sus permisos.
