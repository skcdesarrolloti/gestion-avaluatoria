# Gestión avaluatoria — instrucciones permanentes

## Objetivo y alcance

Proyecto independiente de InversKC para gestión avaluatoria de SuCasa. Esta entrega
es una **base técnica**. El jefe del solicitante proporcionará la implementación,
reglas, fórmulas, etapas y criterios de aceptación de cada módulo. No inventar reglas
de negocio ni trasladar el controlador monolítico. No modificar InversKC al trabajar aquí.

## Arquitectura obligatoria

- PHP 8.2 o superior, MVC; Alpine.js 3 para interacción y Tailwind CSS 4 compilado.
- `public/` es la única raíz web. Configuración, código y almacenamiento quedan fuera.
- Rutas en `routes/`; controladores coordinan HTTP; servicios contienen reglas;
  modelos/repositorios hacen SQL; vistas presentan datos escapados.
- Separar por módulo: avalúos, comparables, jurídico, entorno, PH, valoración,
  documentos e informes cuando se soliciten. Compartir componentes pequeños.
- Objetivo: 80–180 líneas por archivo propio; máximo 220 líneas o 16 KB.
  Dividir antes de superar el límite. No cuentan lockfiles ni archivos generados.
- Máximo inicial CSS + JS: 80 KB gzip en conjunto. `npm run check:size` lo verifica.
- Sin frameworks de frontend adicionales, jQuery, Bootstrap, CDN en producción,
  fuentes remotas, bundles completos de iconos, documentos de ejemplo pesados ni base64.
- Cargar bibliotecas de PDF, mapas y gráficos solo en el módulo que las necesite.
- Secretos en `.env` o entorno; nunca en código, documentación, logs ni Git.

## Login compartido SuCasa

- Leer `wp_jet_cct_funcionarios` en la conexión `auth`, con credenciales de solo lectura.
- Usuario: `user_others_apss`; contraseña: `pass_others_apss` (nombres reales revisados).
  Las columnas y tabla son configurables, sin autodetección silenciosa.
- Exigir `activo = Si`; roles opcionales en `AUTH_ALLOWED_ROLES`.
- `_ID` identifica el registro propietario; `id_empleado` se conserva por separado.
- Verificar hashes con `password_verify`. Texto plano solo mediante opción explícita
  `AUTH_ALLOW_LEGACY_PASSWORDS=true` para compatibilidad con sistemas existentes.
- No crear usuarios demo, copiar claves, modificar funcionarios ni agregar accesos mágicos.
- Sesión propia, regeneración al login, expiración, cookies HttpOnly/SameSite y HTTPS
  en producción. Revalidar actividad y rol en peticiones protegidas.
- CSRF en TODA mutación, logout por POST, límite de intentos por IP y autorización por
  registro en servidor. No basta ocultar botones. Base inicial: cada usuario ve sus fichas.
- Antes de habilitar colaboración o acceso de jefes, implementar permisos explícitos
  y pruebas de autorización conforme a lo que solicite el responsable.

## Base de datos y migraciones: requisito obligatorio

- MySQL/MariaDB, InnoDB, utf8mb4, PDO preparado y conexión `app` exclusiva.
- **Toda tabla, columna, índice o cambio de esquema se entrega como migración
  ejecutable en `database/migrations/`. Nunca pedir al usuario pegar SQL manualmente.**
- Nombres ordenados `YYYYMMDDHHMM_descripcion.php`; archivos pequeños y un propósito.
- `php bin/console.php install --create-database` prepara una base si hay permiso CREATE.
- `php bin/console.php migrate` aplica pendientes. `AUTO_MIGRATE=true` también aplica
  cambios pendientes en el primer acceso autenticado tras actualizar el programa.
- Registrar versión, checksum y fecha. No editar/eliminar migraciones ya aplicadas.
  Para corregirlas agregar una nueva. Mantener bloqueo de concurrencia entre procesos.
- Migraciones reintentables: DDL MySQL no permite suponer rollback transaccional.
  Usar guardas (`IF NOT EXISTS`, `Schema::addColumn`) y comprobar índices antes de crearlos.
- No ejecutar DDL en controladores, vistas, modelos de negocio ni formularios.
- No borrar datos ni columnas automáticamente. Cambios destructivos requieren estrategia
  de respaldo, migración de datos y validación acordada con el responsable.
- Importación histórica futura: comando separado, simulación, conteos y trazabilidad;
  nunca copiar toda la base de InversKC o modificar la base compartida de WordPress.
- Si el hosting no concede CREATE DATABASE, el administrador crea **solo la base vacía**
  desde el panel; el instalador hace todo el esquema sin SQL manual.

## Formularios y experiencia de uso

- Español, responsive, teclado, foco visible, contraste legible y controles de 44 px.
- Todo input textual y textarea lleva label visible y placeholder útil. Un placeholder
  nunca sustituye al label. Select lleva opción inicial orientativa; fechas, archivos,
  checkbox y radio usan ayuda visible cuando no admiten placeholder nativo.
- Identificar unidades, límites, formatos y campos requeridos; errores junto al campo
  con `aria-invalid` y `aria-describedby`, conservar valores tras errores.
- Autoguardar formularios de trabajo como borrador, con debounce de 800 ms, persistencia
  en BD y recuperación al abrir la ficha. Nunca autoguardar claves, login o acciones
  irreversibles. Separar guardar borrador de enviar/aprobar/publicar.
- Mostrar pendiente, guardando, guardado, sin conexión, error y conflicto con texto.
  Confirmar guardado solo tras respuesta del servidor. Botón Guardar ahora/reintento.
- Versionado optimista y HTTP 409 ante edición simultánea; no sobrescribir ni fusionar
  silenciosamente. Avisar antes de abandonar cambios pendientes; no depender de unload
  para guardar. Una pestaña cerrada sin confirmar puede perder cambios sin conexión.
- No guardar datos sensibles en localStorage. La base conserva cambios no enviados solo
  en memoria; una futura recuperación offline requiere diseño explícito.
- Estados vacíos, botones bloqueados durante envío, paginación y carga progresiva.
- Navegación interna, paginación, formularios y cargas de archivos funcionan con
  `fetch` estilo SPA, sin recarga completa visible. Toda acción muestra loader o
  estado de progreso hasta recibir respuesta. Mantener rutas server-rendered como
  fallback y usar recarga solo para descargas, PDF en nueva pestaña o enlaces externos.
- No usar `x-html` con datos externos. Escapar con `e()` y validar en servidor.

## Entrega y verificación

- Leer `README.md`, `docs/DATABASE.md` y `docs/HANDOFF.md` antes de ampliar módulos.
- Ejecutar lint PHP, `php tests/run.php`, `npm test`, `npm run build`, `npm run check:size`.
- Para persistencia y migraciones ejecutar `php tests/database.php` únicamente contra
  las bases locales desechables `ga_test_app` y `ga_test_auth`, con `GA_TEST_PORT` definido.
- Probar login, rechazo de inactivos, acceso ajeno, CSRF, autoguardado, recarga, error
  de red, conflicto, migración repetida y conservación de datos al agregar columnas.
- Verificar interfaz móvil/escritorio cuando cambien las vistas; no introducir pruebas
  triviales que solo repitan implementación.
- Documentar cambios, comandos, configuración nueva, validación ejecutada y limitaciones.
