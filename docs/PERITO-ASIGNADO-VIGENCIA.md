# Responsable asignado y vigencia del certificado RAA

El selector del capítulo 1 usaba exclusivamente `eligibleForAssignment()`. Un
responsable previamente asignado desaparecía si dejaba de cumplir el filtro de
actividad o fecha de vigencia, y el formulario podía enviar una selección vacía.

Ahora se agregan a las opciones los datos del responsable actual del expediente,
obtenido después de comprobar acceso al registro. Un aviso distingue inactividad,
fecha faltante, fecha vencida o ficha no encontrada. No se modifican los maestros,
fechas, certificados ni asignaciones de producción.

El guardado acepta conservar exclusivamente el ID ya asignado aunque no sea elegible
para una nueva asignación. Un valor vacío u omitido conserva el actual antes de
considerar el perito único elegible. El ID anterior se obtiene del servidor, nunca
de un campo oculto enviado por el cliente. Versionado y permisos del analista siguen
vigentes. Nuevas asignaciones y numeración conservan los filtros existentes.

Si el vínculo ya estaba vacío antes de actualizar, no se reconstruye a partir del
prefijo del consecutivo: el titular debe verificar el responsable. La corrección
no acredita vigencia ni repara automáticamente certificados incompletos.

Sin migraciones. Validación: lint, 543 comprobaciones PHP, 113 JS, 110 MySQL en bases
locales desechables ga_test_app/ga_test_auth puerto 3352, build y 58.1 KB gzip.
Pruebas cubren conservación al guardar, consecutivo, rechazo de nuevas asignaciones
no elegibles y vista de analista bloqueada. El primer ensayo tuvo un consecutivo
de prueba demasiado largo; se corrigió el fixture y se repitió toda la suite BD.
Interfaz verificada en escritorio; DOM estrecho de 562 px efectivos sin desborde.
Captura móvil no disponible por timeout. No se inspeccionó el RAA en el hosting.
