# Gestión avaluatoria · SuCasa

Base independiente PHP MVC + Alpine.js + Tailwind para continuar la implementación
con el responsable del proyecto. No contiene todavía los cálculos ni los módulos
completos de InversKC. El proyecto original permanece intacto.

## Incluido

- Login con funcionarios de SuCasa, sesiones, CSRF y límite de intentos.
- Mis fichas, creación de borradores y formulario inicial con guardado en BD.
- Autoguardado, validación, propiedad por usuario y conflictos entre pestañas.
- Biblioteca inicial de Normas Técnicas Sectoriales, categorizada por A, B y 1 a 13.
- Menú de Marco Jurídico Nacional para leyes, decretos y resoluciones por categoría,
  guardando solo artículos o fragmentos pertinentes como material de consulta.
- Menú separado de Normas Internacionales de Valuación con estructura IVS.
- Instalador y migraciones automáticas de tablas y columnas.
- Assets locales compilados y estructura pequeña, sin dependencias PHP externas.

## Arranque

Requiere PHP 8.2+ con PDO MySQL y mbstring, MySQL 8+ o MariaDB 10.4+.
Node.js 20+ solo es necesario para reconstruir assets; no en el hosting.

1. Copia `.env.example` a `.env` y configura las dos conexiones. Usa la
   [guía de base de datos](docs/DATABASE.md); no se incluyen credenciales reales.
2. En la carpeta de este proyecto ejecuta:

   ```powershell
   php bin/console.php install --create-database
   php bin/console.php auth:check
   php -S 127.0.0.1:8088 -t public public/router.php
   ```

3. Abre [el acceso local](http://127.0.0.1:8088/login). Usa las credenciales de
   otras aplicaciones de SuCasa. Si allí las claves están en texto plano, configura
   `AUTH_ALLOW_LEGACY_PASSWORDS=true`; el valor predeterminado admite hashes PHP.

En Windows: `Copy-Item .env.example .env`. El instalador requiere conexión y permisos
válidos. Si la base ya existe, usa `php bin/console.php install` sin la opción CREATE.
No hay endpoint web de instalación ni cuenta demo habilitada.

## Desarrollo

```powershell
npm ci
npm run build
php tests/run.php
npm test
npm run check:size
```

En PowerShell usa `npm.cmd` si la política local bloquea `npm.ps1`.
Composer es opcional para este esqueleto; el autoload mínimo ya está en `bootstrap.php`.

Para importar los PDFs privados de normas técnicas después de migrar la base:

```powershell
php bin/console.php standards:import "C:\Users\skcge\OneDrive\Escritorio\Nueva Ley Valuatoria\Normas Sectoriales"
```

Los PDFs se copian a `storage/normas-tecnicas-sectoriales/`, carpeta excluida de Git.
También se puede definir `NTS_SOURCE_DIR` y ejecutar `php bin/console.php standards:import`.
En producción autenticada, la pantalla de Normas Técnicas Sectoriales permite seleccionar
varios PDFs a la vez e importarlos por nombre contra el catálogo existente.
Si el despliegue del hosting limpia `storage/`, configura `NTS_STORAGE_DIR` apuntando
a una carpeta privada persistente fuera del checkout y vuelve a importar los PDFs.

```text
app/Controllers/     Coordinación de solicitudes
app/Models/          Consultas y propiedad de registros
app/Services/        Autenticación, validación y reglas
app/Core/            HTTP, sesión, configuración y conexiones
app/Database/        Ejecutor y ayudas de migración
app/Views/           Vistas y parciales
database/migrations/ Cambios versionados del esquema
resources/           JS y CSS fuente
public/              Única raíz web; assets compilados incluidos
routes/              Tabla de rutas
storage/             Datos privados de ejecución
tests/               Pruebas aisladas
```

## Publicar y actualizar

- Si aparece **403 Forbidden** al entrar a `/public/`, consultar [acceso en hosting](docs/HOSTING.md).
- Configura el document root del dominio en `gestion-avaluatoria/public`.
  No publiques el directorio raíz ni sirvas `.env` como archivo.
- En Apache permite `.htaccess` y `mod_rewrite`; el `.htaccess` raíz deniega acceso
  directo a carpetas privadas. Configura el virtual host para permitir `public/`.
- Si temporalmente accedes como `dominio.com/public`, configura `APP_BASE_PATH=/public`
  hasta cambiar el document root; la aplicación también intenta detectarlo.
- En HTTPS usa `APP_ENV=production`, `SESSION_SECURE=true`. Para un subdirectorio
  servido mediante alias configura `APP_BASE_PATH=/avaluatoria`; deja vacío en raíz.
- Permite escribir en `storage/`, `storage/sessions/` y `storage/rate-limits/`; un
  único servidor comparte el límite de intentos por filesystem. Para varios servidores
  implementar un limitador centralizado.
- Sube assets compilados, código y migraciones. Excluye `node_modules/`, `tests/`,
  secretos de desarrollo y bases temporales. Configura `.env` en el servidor.
  Si el despliegue Git borra `.env`, crea `.gestion-avaluatoria.env` en la carpeta
  padre del proyecto; la app lo lee después de `.env` y no se versiona.
- Si el despliegue Git borra archivos privados de `storage/`, define `NTS_STORAGE_DIR`
  en una carpeta persistente no pública, por ejemplo una carpeta hermana del proyecto.
- Ejecuta `php bin/console.php migrate` durante el despliegue; con `AUTO_MIGRATE=true`
  el primer acceso autenticado también aplica pendientes. Migraciones costosas deben
  ejecutarse antes de abrir tráfico. La base inicial no borra tablas ni datos.
- Para diagnosticar el login en hosting, ejecuta `php bin/console.php auth:diagnose`.
  También puedes activar temporalmente `APP_DIAGNOSTICS=true` y abrir
  `/diagnostico/login`; vuelve a dejarlo en `false` al terminar.
- Para cambiar assets, recompila; se recomienda versionar sus URL al implementar
  un despliegue con caché/CDN. No configurar caché inmutable para nombres fijos actuales.

## Para continuar

Lee [AGENTS.md](AGENTS.md), [base de datos](docs/DATABASE.md) y
[entrega al responsable](docs/HANDOFF.md). Las decisiones visuales usan tipografía
del sistema, superficies claras, acento teal, labels visibles y formularios por secciones.

Los resultados y requisitos de las pruebas están en [validación](docs/VALIDATION.md).

Referencias: [instalación de Alpine](https://alpinejs.dev/essentials/installation) y
[compilación de Tailwind](https://tailwindcss.com/docs/installation/tailwind-cli).
