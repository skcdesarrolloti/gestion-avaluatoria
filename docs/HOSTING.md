# Acceso en hosting Apache / LiteSpeed

## Si aparece 403 Forbidden en /public/

El `.htaccess` principal bloquea el acceso a los archivos privados. `public/.htaccess`
debe contener `Require all granted` para permitir exclusivamente el directorio web.
Actualizar el repositorio incluye esta corrección; no eliminar la protección del raíz.

Desde la carpeta del proyecto en la terminal del hosting:

```bash
git pull --ff-only origin main
```

Si el panel tiene despliegue Git, ejecutar su acción de actualizar/desplegar para que
la versión nueva llegue a la carpeta que sirve el dominio. Conservar el `.env` local.

Algunos despliegues Git limpian la carpeta y eliminan archivos no versionados. Si eso
borra `.env`, crea un archivo persistente llamado `.gestion-avaluatoria.env` en la
carpeta padre del proyecto y coloca allí las mismas variables. El bootstrap lo lee
después de `.env`, queda fuera del repositorio y no se elimina al actualizar el checkout.
Los PDFs importados se guardan dentro de `storage/`. Si se borran al desplegar, revisa
que el proceso de actualización no limpie archivos privados existentes de esa carpeta
y vuelve a importarlos desde la pantalla de Normas Técnicas Sectoriales o con
`php bin/console.php standards:import`.
La pantalla de Normas Técnicas muestra un diagnóstico con la ruta real, escritura,
archivos físicos y registros marcados en base sin archivo. El modo normal esperado es
`storage/ del proyecto`.

Rutas internas esperadas:

```dotenv
NTS_STORAGE_DIR=storage/normas-tecnicas-sectoriales
LEGAL_STORAGE_DIR=storage/marco-juridico-nacional
```

Si se cargan las 22 normas en un solo intento, revisar `upload_max_filesize`,
`post_max_size` y `max_file_uploads`; algunos hostings aceptan solo 20 archivos por
petición o no permiten PDFs grandes sin subir el límite.
Si esas variables quedan vacías, la app usa las mismas rutas internas por defecto y
puede crear las carpetas automáticamente cuando PHP tenga permisos de escritura. Las
carpetas base quedan ancladas en Git con `.gitkeep`, pero los PDFs se ignoran.

La pantalla del Marco Jurídico acepta varios PDFs o un `.zip` con PDFs. El ZIP ayuda
a evitar seleccionar documentos uno por uno, pero sigue limitado por `post_max_size`.

## Elegir la ruta según la configuración del dominio

| Raíz configurada en el hosting | APP_BASE_PATH en .env | Dirección del login |
| --- | --- | --- |
| Carpeta `gestion-avaluatoria/public` (recomendado) | vacío | `/login` |
| Carpeta del proyecto completo, entrando por `/public/` | `/public` | `/public/login` |
| Proyecto en `/carpeta`, entrando por `/carpeta/public/` | `/carpeta/public` | `/carpeta/public/login` |

Para el acceso mostrado en `avaluos.sucasainmobiliaria.com.co/public/`:

```dotenv
APP_ENV=production
APP_BASE_PATH=/public
SESSION_SECURE=true
```

Luego abrir [el login](https://avaluos.sucasainmobiliaria.com.co/public/login).
Si el hosting permite apuntar la raíz directamente a `public/`, dejar `APP_BASE_PATH=`
y usar [el login del dominio](https://avaluos.sucasainmobiliaria.com.co/login).

El valor APP_BASE_PATH no lleva dominio, `/login` ni barra final. Controla enlaces,
recursos, rutas y cookie de sesión: debe coincidir con la ubicación que ve el navegador.

## Base creada manualmente

Configurar `.env` con las conexiones `DB_*` y `AUTH_DB_*` descritas en
[DATABASE.md](DATABASE.md). Ejecutar `php bin/console.php install` y
`php bin/console.php auth:check` desde la raíz del proyecto. No usar `--create-database`
cuando la base ya fue creada en el panel. El login visual no necesita conexión a BD;
la autenticación y el trabajo con fichas sí la necesitan.

## Si el error continúa

- 403: confirmar que `public/.htaccess` actualizado está en la carpeta desplegada y
  que Apache/LiteSpeed admite sus reglas; revisar registro de errores del hosting.
- 404 después de quitar el 403: comprobar APP_BASE_PATH y reescritura de URLs.
- 500: revisar el registro del servidor por directivas `.htaccess` no permitidas y
  confirmar PHP 8.2+, PDO MySQL y mbstring.
- Aviso de servicio no disponible: revisar conexiones y permisos de BD; `storage/`
  necesita escritura para el usuario PHP. El código de referencia identifica el error.

No cambiar permisos a 777 ni dar acceso público a `.env`, `.git`, `app/` o `storage/`.
La configuración de acceso sigue la [herencia de autorización de Apache](https://httpd.apache.org/docs/2.4/mod/mod_authz_core.html#authmerging).
