# Mamonal y fotos del numeral 2.2 — 2026-10-01

La migración `202610010007_add_mamonal_sector.php` incorpora Mamonal al catálogo
de Cartagena si no existe. No altera registros existentes ni asigna localidad,
UCG o delimitación sin verificar. El buscador existente lo encuentra con `Mam`.

El control compartido de fotos presenta «Eliminar foto» arriba de cada imagen,
con altura mínima de 44 px. El retorno acepta `sector#banco-02` y muestra el
mensaje de eliminación en el sector. Se conservan autorización por expediente
y CSRF. Ninguna foto se elimina durante la actualización.

Se corrigió también una fecha inválida en las fuentes del banco sectorial:
el año del CNPV sigue en su nombre, pero no se usa como fecha de revisión.
La revisión desconocida queda nula, evitando el rechazo de MySQL estricto.

## Aplicación

Actualizar código y assets; ejecutar `php bin/console.php migrate`, o mantener
`AUTO_MIGRATE=true` para aplicar la migración al primer acceso autenticado.
Conservar `.env`, base de datos y `storage`. No requiere configuración nueva.
Esperar confirmación de autoguardado antes de actualizar una pantalla abierta.

## Verificación

- Lint PHP, 446 verificaciones PHP, 109 pruebas JS, build y tamaño 57,6 KB gzip.
- 77 pruebas de base en `ga_test_app`/`ga_test_auth`, puerto desechable 33333.
- Comprobación adicional de fuentes sectoriales en esa misma base: revisión
  DANE nula y carga del banco sin error. Añadida al test de base para futuras corridas.
- HTTP con analista de prueba: selección por `Mam`, carga persistida y visible
  al titular, CSRF, eliminación persistida, mensaje y retorno a 2.2.
- Navegador: sugerencia Mamonal, botón encima de imagen y vista estrecha sin
  desbordamiento; botón de 44 px. El viewport estrecho efectivo fue de 585 px
  debido a la escala del navegador, no se afirma una prueba a 390 px efectivos.

Las pruebas usaron datos desechables locales. No se verificó el despliegue en hosting.
