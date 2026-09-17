# Validación de la base — 15 de septiembre de 2026

Pruebas ejecutadas con PHP 8.2.12, Node.js, Alpine 3.17.3, Tailwind 4.3.3
y MariaDB local temporal en un puerto separado. No se usaron datos reales.

| Verificación | Resultado |
| --- | --- |
| Sintaxis de todos los PHP del proyecto | Correcta |
| `php tests/run.php` | 18 verificaciones correctas |
| `npm test` | 6 pruebas de autoguardado correctas |
| `php tests/database.php` | 10 verificaciones con MariaDB correctas |
| `php tests/http.php` | 13 verificaciones HTTP correctas |
| `npm run build` | Compilación correcta |
| `npm run check:size` | CSS + JS: 24,5 KB gzip; fuentes propias dentro del límite |
| Navegador de escritorio | Login, creación, autoguardado y recuperación tras recarga comprobados |
| Navegador móvil, ancho 390 px | Login y formulario revisados visualmente |

Se verificaron: CSRF, rechazo de inactivos, contraseñas incorrectas, compatibilidad legacy
explícita, roles, expiración, límite de intentos, propiedad de fichas, UTF-8, escape XSS,
logout por POST, conflictos de edición y conservación de cambios tras fallos de red.
Las migraciones se repitieron sin duplicaciones y se comprobó conservación de datos al
agregar una columna, recuperación de creación parcial y detección de checksum alterado.

Los datos temporales, sesiones y dependencias instaladas quedan excluidos de Git.
El usuario ficticio existe solo en fixtures; no hay cuenta demo ni credenciales reales
en la aplicación distribuida. La limpieza local automática fue bloqueada por política.

## Reproducir

- Pruebas sin MySQL: `php tests/run.php` y `npm test`. PHP requiere `pdo_sqlite`.
- Pruebas de BD: instancia MySQL/MariaDB desechable en puerto distinto de 3306,
  `GA_TEST_PORT` definido y `php tests/database.php`. Crea exclusivamente `ga_test_app`
  y `ga_test_auth`; rechaza bases ya existentes y no borra nada.
- HTTP: iniciar la aplicación local en 8088 configurada con esas dos bases de fixtures,
  `SESSION_SECURE=false`, `GA_TEST_HTTP=true` y ejecutar `php tests/http.php` (extensión curl).
- No ejecutar las pruebas de integración contra una base de trabajo ni de producción.

## Pendiente de configuración real

El login compartido está implementado y probado contra el contrato de columnas de
SuCasa, pero la conexión real, permisos del hosting y una cuenta real deben verificarse
después de configurar `.env`. La base no incluye los módulos de negocio completos.

## Corrección de acceso 403 en hosting

Se reprodujo en Apache local el 403 de `/public/login` antes del cambio. La regla
`Require all granted` en `public/.htaccess` permite el directorio web y conserva el
bloqueo del directorio raíz. También se deniegan los archivos ocultos dentro de public.

Después del cambio: login y assets devuelven 200 tanto bajo `/public/` como con raíz
web directamente en public; `.env`, `.git/config`, `app/`, `storage/` y el `.htaccess`
público siguen devolviendo 403. Se verificaron enlaces, formulario y cookie con prefijo
`/public`. Pasaron nuevamente las pruebas PHP, JS, compilación y control de tamaño.
La corrección requiere actualizar el despliegue del hosting y configurar APP_BASE_PATH
según [HOSTING.md](HOSTING.md); no se modificó el servidor remoto desde esta sesión.

## Carga de documentos jurídicos

Se agregó carga privada de PDFs fuente para Marco Jurídico Nacional con diagnóstico
de ruta, permisos, archivos físicos y registros marcados sin archivo. También se
reutilizó la verificación de PDF para Normas Técnicas Sectoriales.

Verificación local ejecutada el 15 de septiembre de 2026:

- Sintaxis de todos los PHP del proyecto: correcta.
- `php tests/run.php`: 50 verificaciones correctas, incluida importación NIIF por tarjeta.
- `npm test`: 9 pruebas correctas.
- `npm run build`: compilación correcta.
- `npm run check:size`: CSS + JS 27,3 KB gzip.

`php tests/database.php` no se ejecutó en esta pasada porque `GA_TEST_PORT` no estaba
definido. En producción falta confirmar escritura en `storage/normas-tecnicas-sectoriales`,
`storage/marco-juridico-nacional`, `storage/normas-internacionales-valuacion` y
`storage/normas-niif`.

## Respaldo de PDFs jurídicos y sector avanzado

Verificación local ejecutada el 17 de septiembre de 2026:

- `php tests/run.php`: 76 verificaciones correctas.
- `npm test`: 11 pruebas correctas.
- `npm run build`: compilación correcta.
- `npm run check:size`: CSS + JS 29,0 KB gzip.

Se comprobó que Marco Jurídico conserva el PDF desde respaldo interno aun cuando el
archivo físico ya no existe, y que el diagnóstico solo marca faltante cuando tampoco
hay respaldo. También se comprobó que el banco sectorial conserva las secciones
avanzadas, cuenta como listas solo las secciones validadas y toma la versión real del
banco al generar la instantánea.
