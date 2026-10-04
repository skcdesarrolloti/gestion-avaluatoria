# Investigación de fichas por portal y tipo

Fecha de revisión: 2026-10-04, Colombia. Primera etapa solicitada: documentar
publicación antes de ampliar extracción y comparación entre anuncios.

## Alcance y cobertura

Insumos incorpora «3. Configuración por portal y tipo de inmueble», consulta
independiente de Buscar y de Inmuebles recogidos. Selecciona portal/tipo, muestra
datos básicos, elementos descriptivos, hallazgos y enlace a una ficha fuente.
Una ficha documenta campos observados, no un esquema universal ni campos obligatorios.
Los formularios privados de publicación, feeds de inmobiliarias y APIs no se han
verificado. La página pública de publicación de Ciencuadras muestra perfiles/planes,
no suficiente evidencia del formulario de alta. No se registraron cuentas.

FincaRaíz: oficina, apartamento, casa, lote, local, bodega, consultorio y edificio.
Ciencuadras: oficina, apartamento, casa, lote, local, bodega, finca, edificio,
parqueadero y uso hotelero bajo categoría Edificio. Metrocuadrado: oficina.
Properati: local. Otros cruces, Mercado Libre y depósito independiente: pendientes.
La búsqueda de consultorio en Ciencuadras produjo evidencia parcial y una ficha
inaccesible: no se publica como configuración documentada.

Todos los enlaces y diferencias por tipo están en los archivos pequeños
`app/Services/portal-profiles/{fincaraiz,ciencuadras,otros}.php`, consumidos por
ComparablePortalProfiles. Las opciones pendientes no heredan otro perfil.
Venta no certifica campos de arriendo. La celda publicada en Ciencuadras se
presenta como remate/oferta aunque su URL contiene venta: modalidad pendiente.

## Hallazgos y consecuencias

1. FincaRaíz distribuye datos entre encabezado, Detalles, Descripción y anunciante;
   Ciencuadras entre Datos generales, Detalles, Características, Zonas comunes y
   Gastos. La casilla sola pierde información; el texto puede contradecirla.
2. Oficina FincaRaíz 194156871: área 105 y piso 18 en ficha; 105,5 y piso 19 en
   descripción. Apartamento Ciencuadras 3346478: un parqueadero en casilla y dos
   garajes en texto. Una sola fuente ya necesita alertas internas.
3. Lote Ciencuadras 3244209: áreas privada/construida 86, terreno 171 en texto.
   Finca 3430833: construcción 2.750 en ficha y 235 en texto. Conservar ambas
   versiones; no corregir automáticamente ni usar área genérica como terreno.
4. Área privada anunciada no acredita área privada construida. Terraza de uso
   exclusivo, terreno y construcción no son cantidades intercambiables.
5. Antigüedad por intervalo en FincaRaíz/Metrocuadrado no equivale a edad numérica
   de Ciencuadras o año de construcción de Properati. Mantener unidad/origen.
6. Código de anuncio, código inmobiliaria e ID de URL pueden diferir dentro de
   una fuente. Guardarlos separados; no son identificadores físicos universales.
7. Ambientes de oficina/bodega no son alcobas. Baños de un apartamento del edificio
   no necesariamente son baños de todo el edificio. Servicio puede explicar conteos.
8. Dirección del anunciante, barrios cercanos o ubicaciones asociadas no prueban
   dirección del sujeto. Ninguna coordenada del portal se verifica por importación.
9. Parqueadero individual sí existe publicado; también existen establecimientos
   completos en esa categoría. «Asignado»/«independiente» no prueba matrícula.
   Depósito como atributo de apartamento no acredita oferta independiente.
10. Uso hotelero puede aparecer como Edificio. La operación debe distinguir inmueble,
    negocio, remate, venta y renta; una mención de canon no cambia venta por arriendo.

## Siguiente etapa propuesta; todavía no implementada

Guardar evidencia por campo: etiqueta y valor originales, valor normalizado cuando
sea inequívoco, unidad/base, sección, portal, código, URL y fecha. Separar dato
publicado de dato confirmado. No publicado, cero explícito y no aplica son estados
distintos. Atributos complementarios no deben sobrescribir atributos contradictorios.

Comparar primero dentro de cada aviso y después entre candidatos. Precio distinto
no demuestra inmuebles distintos; igualdad de precio/área/barrio tampoco identidad.
Combinar edificio/dirección anunciada, características, anunciante/código interno y
evidencia visual con explicaciones legibles, sin un umbral de identidad inventado.
El analista confirma o rechaza la coincidencia y registra soporte.

Dos pestañas por inmueble: Datos básicos y Elementos descriptivos. Estados por campo:
coincidente (verde, no equivale a verificado), diferencia (alerta), complementario
(nuevo dato con fuente), pendiente (sin evidencia). Tras confirmación, un inmueble
con varios anuncios originales; una observación analítica futura, sin borrar ads
ni escoger/promediar precios automáticamente. La selección del dato adoptado y
las exclusiones requieren trazabilidad en Análisis.

## Captura actual y preservación

Esta entrega añade referencia, no extracción nueva, agrupación automática ni reglas
estadísticas. Lectores de resultados Ciencuadras/Properati/Mercado Libre tienen
cobertura limitada de oficinas/venta; no se anuncian como lectores de todos los
tipos. El texto genérico y lectores existentes deben probarse contra cada nueva
etiqueta/estructura antes de ampliar cobertura. Se conservan consulta extensa,
búsquedas breves por portal/unidad, pegado, muestras, fotos, Excel y vinculación.
No cambia esquema ni escribe en configuración del expediente.

Validación: PHP lint/tests, JavaScript, build/tamaño; navegador escritorio/móvil,
selección documentada frente a pendiente, retorno a búsqueda/bandeja y datos intactos.

## Ampliación 2026-10-04 y consulta breve
Se agregan 15 referencias: Properati (oficina, apartamento, casa, lote),
Metrocuadrado (apartamento, casa, lote, local, bodega) y Mercado Libre
(oficina, apartamento parcial, casa, local, edificio, finca). Se mantienen las anteriores.
Para oficina los cinco portales cuentan con referencia. No se anuncian todos los
cruces portal/tipo como completos; los no documentados conservan estado pendiente.
Cada referencia enlaza su propia ficha; no son comparables adoptados del expediente.

Fuentes nuevas: contenido público indexado de páginas originales, con fechas de
rastreo variables. El acceso directo devolvió 403 en varias fichas de Properati y
Mercado Libre; no se sortearon bloqueos. Por eso su estado indica versión indexada
y necesidad de confirmar vigencia, no inspección vigente del formulario privado.
El apartamento Mercado Libre sólo tiene evidencia parcial del texto y atributos.
No se extrapola la ficha de lote industrial de Properati a categoría bodega.

Presentación: dos selectores, cobertura por tipo y resumen básico; características,
contradicciones y fuente plegadas. El paso 3 sirve para consultar campos posibles;
el paso 4 cuenta información realmente disponible en las muestras del expediente.
Sin lectores nuevos, cambios de base, deduplicación automática ni adopción de valores.
