# Investigaciones independientes de ventas y arriendos

## Regla de separación

Cada colección se identifica por expediente, propietario autorizado, unidad o
recorrido, método y operación. Compartir ubicación, áreas y características del
bien sujeto no comparte anuncios, importes, planes ni memorias del estudio.
Renta prepara arriendos. Mercado conserva la operación del encargo, con venta
como contexto anterior cuando falta la operación. Incluso dos métodos con la
misma operación mantienen colecciones distintas.

## Conservación de muestras anteriores

- No hay DDL, migración de esquema, borrado masivo ni reclasificación al desplegar.
- La lectura de registros antiguos usa su operación explícita: Venta se conserva
  en Mercado y Arriendo en Renta. Un recorrido de contraste con método explícito
  conserva ese método. No se deduce la operación del importe ni de la unidad.
- Sin operación reconocida, una muestra queda conservada y fuera de la colección
  activa. La pantalla informa del número de registros de otras investigaciones o
  pendientes de clasificación. El banco sin asignar conserva la revisión manual.
- Al guardar, el servidor identifica nuevas muestras mediante `research_method`
  y `research_operation` dentro de `capture_details`, junto al `component_key`
  existente. Estos campos internos no se adoptan del formulario ni del Excel.
- La combinación de matrices conserva íntegramente las filas de otras colecciones.
  Sus ID y fotografías mantienen la vinculación. La versión global existente de
  comparables sigue evitando sobrescrituras simultáneas.

## Controles del flujo

La matriz y el plan envían `study_scope`; una pantalla anterior o cuyo método
cambió se rechaza con409. No puede apropiarse del ID de otra investigación ni
cambiar una venta a arriendo desde una colección activa. La reasignación tampoco
puede trasladar ventas a Renta. Las autorizaciones y CSRF se conservan.

Excel exporta la investigación en sus metadatos y la envía al revisar y guardar.
Se rechazan archivos de otra investigación, incluso si la unidad y versión son
iguales; los archivos anteriores necesitan una exportación actualizada. Los campos
internos no son editables mediante columnas. El historial de importación usa una
clave separada por unidad/método/operación; el historial anterior no se elimina.

Al cambiar de método, plan, memoria, conclusión, huella y bloques particulares
quedan en `method_studies` dentro del JSON de metodología. Al volver se restaura
el estudio correspondiente. Un plan antiguo sin procedencia confirmada no se
presenta como plan de Renta; se conserva y, al reemplazarlo, queda en
`legacy_research_plan`. No se programan fórmulas ni nuevos cálculos.

Los conteos de Configuración y el enlace de continuación se calculan por
investigación: los arriendos no hacen aparecer ventas propias de Mercado.
La carga diferida del paso3 sigue conservando una sola matriz montada.

## Validación local

- PHP1625; JavaScript272; lint PHP modificado, build y check:size79,0KB gzip.
- Suite `GA_TEST_PORT=3378 php tests/database.php`:246 verificaciones; instancia
  MariaDB nueva y exclusivamente ga_test_app/ga_test_auth. Tras ajustes finales,
  prueba específica repetida:11 verificaciones, incluida foto original conservada.
- `GA_TEST_HTTP=true GA_TEST_PORT=3378 php tests/comparable-study-http.php`:14
  comprobaciones sobre servidor real local8088. Login, CSRF, propietario, cambio
  Mercado→Renta→Mercado, HTML sin datos ajenos, guardado, operación inválida,
  conflicto, Excel de otra investigación y reasignación incompatible.
- Navegador con PHP/BD reales de prueba: cambio de método, guardado confirmado,
  navegación fetch y carga diferida de Renta. Solo el anuncio de arriendo aparece
  en los controles; venta y operación desconocida permanecen en BD. Escritorio
  1440 y móvil390 sin desbordamiento. Capturas en output/pdf:
  renta-investigacion-separada-escritorio.png y renta-investigacion-separada-movil.png.

Publicar con commit y push; verificar assets públicos. Estas pruebas no atribuyen
una revisión autenticada del hosting ni alteran expedientes reales.
