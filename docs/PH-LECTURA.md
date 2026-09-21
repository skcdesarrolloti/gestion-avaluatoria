# Lectura de reglamentos PH

En 3.5 selecciona uno o varios PDF y pulsa **Leer soporte PH**. La pestaña debe
permanecer abierta hasta confirmar el guardado. PDF.js obtiene texto de cada página;
Tesseract en español e inglés lee las páginas sin una capa de texto suficiente.
Las dependencias se sirven desde `public/assets/` y se cargan solo al analizar PH.
No se envían imágenes a MiniMax ni se necesita configurar un proveedor para este flujo.
La compilación enlaza el lector y su worker PDF mediante versiones SHA-256 de su
contenido. Esto evita combinar la aplicación nueva con un lector anterior conservado
en caché. Publicar juntos `app.js`, `ph-pdf-reader.js` y `pdf.worker.mjs`.

Se procesan todas las páginas, sin el límite anterior de 6/300 páginas. Cada página
se libera después de leerla; se conserva en memoria únicamente su texto y metadatos.
El servidor valida nombre, tamaño, orden y número de páginas del resultado recibido.
Estos controles comprueban integridad del envío, no autenticidad del OCR del cliente.
Todo resultado es una sugerencia documental revisable.

La lectura identifica la copropiedad y propone extractos para identificación jurídica,
configuración, bienes comunes, administración, restricciones y soportes. Busca en todo
el texto, conserva referencias al archivo y página y limita los extractos por campo.
**Texto por páginas** permite consultar el texto completo recibido en una ruta privada.
La asociación usa reglas textuales, no un modelo de razonamiento jurídico: no interpreta
automáticamente excepciones, derogaciones internas ni la vigencia de cada cláusula.

Los campos vacíos reciben sugerencias; los ya diligenciados se conservan. Lo que no
se identifica queda pendiente, con ayuda visible. Una mención de piscina, póliza, acta,
mora o mantenimiento no acredita existencia actual, entrega del soporte ni incumplimiento.
No se marcan como verificados estos hallazgos. Fotos, visita, pagos vigentes, impactos
en valor y conclusiones finales requieren al analista y sus soportes correspondientes.
El coeficiente solo se propone si la unidad previamente diligenciada tiene una mención
expresa, única y reconocible junto a su coeficiente; tablas complejas quedan por revisar.

## Persistencia y conflictos

El archivo original y su texto se conservan por separado en el almacenamiento privado
y la tabla existente de documentos PH. Cada archivo recibe exclusivamente su propia
lectura, incluso al cargar varios PDF. No se guardan datos del reglamento en localStorage.
La migración `202609210001_add_ph_profile_version.php` agrega control de versión sin
alterar migraciones anteriores ni borrar datos. HTTP 409 protege tanto la edición como
la aplicación de un análisis si otra pestaña ha guardado cambios.
El botón Guardar funciona sin recarga con JS y mantiene un POST de respaldo sin JS.
Una lectura vacía o la eliminación de un soporte conserva los campos del analista.

## Actualización

1. Publicar código, migración y assets construidos con `npm run build`.
2. Ejecutar `php bin/console.php migrate` o usar el `AUTO_MIGRATE=true` existente.
3. Incluir `ph-pdf-reader.js`, `pdf.worker.mjs` y toda la carpeta `assets/tesseract/`.
   Apache tiene tipos MIME explícitos para `.mjs` y `.wasm`; el router PHP local también
   sirve los motores y paquetes de idioma.

## Límites visibles

- Escaneos borrosos, manuscritos y tablas pueden producir errores incluso con confianza
  OCR alta. Se avisa de páginas vacías, con menos de 40 caracteres o confianza inferior
  a 70. La cobertura indica páginas procesadas, no exactitud garantizada.
- Un fallo de lectura detiene la operación y permite reintentar; no se sube a escondidas
  una lectura truncada. El trabajo OCR no sobrevive al cierre de la pestaña.
- Se admiten hasta 300 MB por PDF y 20 MB para el JSON de texto. La lectura puede tardar
  varios minutos. Los límites PHP de POST/subida siguen aplicando; un PDF individual
  mayor a 12 MB usa el cargador por partes existente. Para lotes grandes, dividir cargas.
- ZIP/RAR, DOCX, TXT e imágenes conservan su ruta anterior. La cobertura completa por
  página se garantiza como recorrido solo para PDF seleccionados directamente con JS;
  extraer los PDF de los archivos comprimidos antes de subirlos. Sin JS, la lectura
  del servidor puede ser parcial y necesita las herramientas OCR instaladas.
- No se recalculan automáticamente sugerencias anteriores ya guardadas; el analista
  debe revisar y vaciar el campo que quiera volver a proponer desde otro soporte.

## Verificación de esta entrega

Se procesó localmente el PDF aportado de Chambacú: 244 páginas escaneadas. Se conservó
texto privado de las 244 y se reconoció el nombre del edificio desde su contenido.
No se incorporaron el PDF ni su texto a Git ni a bases de trabajo o producción.
Se comprobó OCR real en Chrome con una página del documento y sin proveedor externo.
La prueba de selección de archivos mediante la extensión de Chrome quedó bloqueada por
su permiso de acceso a archivos; se verificó el envío multipart por HTTP de fixtures.
