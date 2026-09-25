# Crear y evolucionar la base sin SQL manual

PH: la migración `202609210001_add_ph_profile_version.php` agrega `version` a
`appraisal_ph_profiles`. Cada guardado incrementa la versión; edición y análisis
rechazan versiones obsoletas con HTTP 409. El original y texto por páginas siguen
en `appraisal_ph_documents`; no se agregan tablas para la lectura OCR del navegador.

## 1. Preparar las conexiones

Copiar `.env.example` a `.env`. Se necesitan dos conexiones:

| Variables | Destino | Permisos |
| --- | --- | --- |
| `DB_*` | Nueva base `gestion_avaluatoria` | SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX en esa base |
| `AUTH_DB_*` | Base existente de SuCasa | Solo SELECT sobre funcionarios |

Configura host, puerto, nombre, usuario y contraseña para cada conexión.
El usuario y permisos se crean en el panel del hosting o con el administrador de BD.
El programa no puede concederse permisos ni descubrir credenciales. Para crear la
base desde el comando, la cuenta `DB_USERNAME` también debe poder crear esa base.
Nunca uses la base WordPress como `DB_DATABASE` ni la misma cuenta para las dos conexiones.

La autenticación consulta `wp_jet_cct_funcionarios` y estas columnas:
`_ID`, `id_empleado`, `nombre`, `rol`, `activo`, `user_others_apss`, `pass_others_apss`.
`AUTH_TABLE`, `AUTH_USER_COLUMN` y `AUTH_PASSWORD_COLUMN` permiten diferencias de nombre.
No se crea esta tabla ni se alteran sus datos. Para verificar: `php bin/console.php auth:check`.

## 2. Crear la base y tablas automáticamente

```powershell
php bin/console.php install --create-database
```

El comando crea la base indicada con utf8mb4 y luego ejecuta las migraciones.
Si el hosting no permite crear bases por conexión, crea **únicamente la base vacía**
en su panel, asigna el usuario y ejecuta:

```powershell
php bin/console.php install
```

No debes crear tablas ni pegar sentencias SQL. El programa hace esa parte.

## 3. Esquema inicial

| Tabla | Uso |
| --- | --- |
| `schema_migrations` | Versión, checksum y fecha de cada migración aplicada |
| `appraisals` | Ficha borrador, propietario, título, tipo, dirección, municipio, observaciones, versión y fechas |
| `valuation_standard_categories` | Grupos A, B y categorías valuatorias 1 a 13 |
| `valuation_standards` | Catálogo de normas técnicas y metadatos del PDF privado |
| `valuation_legal_categories` | Grupos jurídicos nacionales A, B y categorías valuatorias 1 a 13 |
| `valuation_legal_documents` | Catálogo fuente de leyes, decretos y resoluciones vigentes o derogadas |
| `valuation_legal_articles` | Artículos o fragmentos jurídicos pertinentes por categoría |
| `valuation_international_groups` | Familias de normas internacionales IVS |
| `valuation_international_standards` | Catálogo IVS separado del marco jurídico colombiano y metadatos del PDF privado |
| `valuation_ifrs_groups` | Familias NIIF/NIC aplicables a medición contable |
| `valuation_ifrs_standards` | Catálogo NIIF/NIC y metadatos del PDF privado |
| `valuation_field_considerations` | Clasificación de campos del expediente como normativos, derivados u operativos |
| `valuation_glossary_terms` | Glosario académico de conceptos y factores valuatorios con fuente y carga manual |
| `appraisal_sector_profile_sections` | Borradores avanzados por sección sectorial dentro de cada avalúo |
| `urban_norm_documents` | Biblioteca fuente de normatividad urbana para capítulo 5, con metadatos del documento y PDF privado |
| `urban_norm_tables` | Cuadros normativos del documento urbano, como los cuadros de usos del Decreto 0977 de 2001 |
| `urban_norm_use_categories` | Categorías de uso urbanístico por cuadro, grupo y orden de consulta |
| `urban_norm_use_rules` | Reglas de uso principal, compatible, complementario, restringido y prohibido por categoría |
| `urban_norm_parameters` | Parámetros urbanísticos por categoría; la semilla correctiva carga los parámetros residenciales visibles del Cuadro No. 1 para potencial constructivo |
| `appraisal_urban_norm_profiles` | Ficha del capítulo 5 por avalúo, con consulta MIDAS, concepto, clasificación, uso y conclusión |
| `appraisal_urban_norm_references` | Soportes y extractos urbanos asociados al avalúo y a la biblioteca normativa |

`owner_id` guarda `_ID` del funcionario. No hay FK entre servidores/bases ni copia de
contraseñas. `id` es aleatorio (32 caracteres hexadecimales), pero siempre se comprueba
propiedad. Fechas en UTC; presentación en America/Bogota. Índice por propietario y fecha.
Las tablas de cálculos, documentos y flujos se agregarán según la implementación del jefe.
Los PDFs de normas no se guardan en Git; se importan a `storage/` con
`php bin/console.php standards:import <carpeta>`.
Para leyes extensas, la base conserva la ficha del documento fuente y el PDF privado,
pero solo almacena como consulta los artículos, incisos o extractos necesarios para
la categoría/finalidad del avalúo.
Las NIIF se guardan separadas de IVS y del marco jurídico nacional; se consultan
cuando el encargo tenga finalidad financiera, valor razonable, deterioro o revelación.

Normatividad Urbana queda separada en biblioteca y ficha del avalúo. La biblioteca
conserva el documento fuente y solo organiza cuadros, categorías, reglas y parámetros
consultables. La ficha por avalúo guarda lo que el analista adopta para el inmueble:
fuente, resultado MIDAS, concepto de Planeación, clasificación urbanística, uso,
restricciones, conclusión y soportes. Si el analista aún no selecciona documento,
cuadro o categoría, esas referencias se guardan como NULL para no forzar decisiones
ficticias ni romper llaves foráneas.

## 4. Nueva tabla o columna

Cada cambio llega con un archivo nuevo en `database/migrations/`. Por ejemplo:

```php
<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->addColumn('appraisals', 'referencia_externa', "VARCHAR(80) NOT NULL DEFAULT ''");
};
```

Guardar como `202609160001_add_referencia_externa.php`; adaptar la fecha y secuencia.
Este es un ejemplo para el desarrollador: el usuario final no tiene que ejecutarlo
manualmente en SQL. Las dos migraciones reales incluidas muestran creación de tabla
y adición de `observaciones`, incluida la recuperación si se interrumpe el proceso.

Al publicar la versión, `php bin/console.php migrate` aplica lo pendiente. Con
`AUTO_MIGRATE=true`, también se ejecuta al entrar autenticado a la aplicación.
Si no hay pendientes, solo lee el registro; no ejecuta CREATE/ALTER en cada formulario.

## 5. Garantías y límites

- Bloqueo MySQL `GET_LOCK` evita dos migradores concurrentes para la misma base.
- Checksum detecta modificaciones de migraciones aplicadas; nunca cambiar su contenido.
- DDL MySQL puede hacer commit implícito. Cada paso debe ser reintentable; no se promete
  revertir automáticamente cambios DDL parciales.
- Un error de migración detiene la petición antes de trabajar con un esquema incompleto.
  El usuario recibe un aviso genérico; el administrador ejecuta el comando para revisar.
- No hay rollback destructivo automático. Respalda antes de cambios importantes y entrega
  una migración correctiva nueva. Se conserva el historial versionado.
- Si en producción el usuario de ejecución no tiene DDL, usa `AUTO_MIGRATE=false` y
  ejecuta el migrador con credenciales de despliegue mediante variables de entorno.
- No se conectó esta entrega a las bases reales ni se copiaron secretos de otros proyectos.

