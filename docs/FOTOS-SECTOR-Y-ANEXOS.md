# Fotos sectoriales y reclasificación — 2026-10-01

## Fotos

El numeral 2.4 incluye «Foto del uso predominante», con nombre, pegar Ctrl+V,
selección de archivo y eliminación. Usa la etiqueta independiente
`sector:uso-predominante` y vuelve a `#banco-04`. Probado en navegador con
imagen pegada y recarga; no requiere migración ni altera fotos de otros numerales.

Los formularios de fotos del sector ahora envían automáticamente los archivos al
elegirlos o pegarlos. La selección anterior solo preparaba una vista previa y
requería pulsar Agregar fotos; el autoguardado del texto no enviaba esos archivos.
Las URLs siguen requiriendo Guardar / Reintentar carga. Se espera el guardado del
borrador antes de subir, se muestra progreso y se conserva la selección si falla
la red. Las fotos pendientes bloquean navegación interna y avisan antes de salir.

La carga usa el endpoint, almacenamiento y respaldo en BD existentes. Su respuesta
confirma la carga. El retorno reconstruye `#banco-02` antes de inicializar Alpine,
para mantener visible la sección correcta. La corrección conserva el retorno PH.

## Componentes

Renombrar una unidad como Anexo 2 no cambiaba `unit_kind`. Se muestra la
clasificación y una acción explícita «Cambiar a anexo o mejora» para unidades
principales. Guarda el formulario y reclasifica conservando ID, nombre, detalles
y vínculos de fotos. Ajusta cantidades e índices, incluyendo registros inactivos,
sin borrar filas. Limpia el tipo inmobiliario de la unidad; conserva tratamientos
específicos elegidos y, si era principal o vacío, aplica el valor predeterminado
del catálogo existente para anexos. El analista debe revisar ese tratamiento.

La operación verifica propietario y versión dentro de una transacción. El
formulario vuelve a Negocio y tipología mostrando el mismo editor de anexos.
No realiza conversiones automáticas basadas en nombres de componentes.

## Entrega y comprobación

Sin migración ni configuración nueva. Actualizar código y assets conservando
base, `.env` y `storage`. No se modificaron datos del hosting.

- Lint PHP; 446 verificaciones PHP; 112 pruebas JS; build; 58,1 KB gzip.
- Base completa: 83 verificaciones en ga_test_app/ga_test_auth, instancia
  desechable puerto 33334. Verificación posterior de 6 casos de reclasificación
  en las mismas bases desechables del puerto 33333, incluyendo vínculo de foto.
- Navegador con analista local: pegar imágenes en ambas figuras de 2.2,
  confirmación, recarga con ambas fotos; reclasificar Anexo 2 y ver ambos editores
  iguales. Escritorio y ancho efectivo de 585 px sin desbordamiento.
- La prueba local no demuestra el estado del hosting ni sus límites de subida;
  ante un fallo allí, el formulario presenta el error recibido para diagnosticarlo.
