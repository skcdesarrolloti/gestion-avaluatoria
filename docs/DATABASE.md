# Crear y evolucionar la base sin SQL manual

Contrastes por método: `additional_methods` activa claves `:metodo:<método>` dentro
del mismo `methodology_workflow`. Se guardan junto al método en una actualización
optimista; desactivar conserva sus datos. Muestras vinculadas por clave de recorrido,
sin copiar las originales. No requiere migración ni nuevas unidades inmobiliarias.

Plan capítulo 8: `plan_parts` y métodos de partes se guardan en JSON existente
`methodology_workflow`, con versión compartida; no hay migración ni nuevas filas
de unidades. Claves de muestras/Excel por parte son independientes y estables.
Ver PLAN-VALORACION-CAP8.md para preservación y controles de organización.

Excel M3: `202610020002_comparable_excel_history.php` agrega historial de última
importación por unidad/banco. Se guardan fecha UTC, nombre de archivo y versión
en la misma transacción que las muestras. UI presenta hora de Colombia.
Aplicar mediante AUTO_MIGRATE o `php bin/console.php migrate`; no cambia muestras.

Academia por unidad: cuatro subpestañas visibles en A del capítulo 8 y lectura contextual
para PH, NPH, terreno y mejoras. Capítulo 1 guarda «Estructura del método» por unidad
y anexo; capítulo 8 la consulta. Migración aditiva `202610020001_unit_method_structure.php`
mediante `php bin/console.php migrate` o `AUTO_MIGRATE=true` al acceder autenticado.
Los registros anteriores quedan por definir; no se asigna un método automáticamente.
Validación: PHP, JavaScript, compilación, tamaño y BD local desechable; persistencia,
propietario ajeno, formulario anterior y migración repetida. No desarrolla otros métodos.

Capítulo 8: `202610010008_methodology_workflow.php` agrega documento de selección
y versión optimista, sin modificar filas ni fotos existentes. Ver
[flujo por componentes](CAPITULO-8-COMPONENTES.md).

Tabla 1.11: `202610010006_assignment_document_table.php` agrega el detalle
documental sin reemplazar marcas ni observaciones anteriores.

Expediente: `202610010005_assignment_contact_and_value_date_notes.php` agrega
correo, celular, municipio del solicitante y explicación de fecha de valor.
Conserva datos y fotos existentes; ver [detalle](EXPEDIENTE-SOLICITANTE.md).

Accesos: `202610010004_create_analyst_access.php` crea analyst_accounts en la base
app y agrega analyst_account_id a appraisals. No modifica funcionarios/WordPress
ni propietarios existentes. Ver [acceso delegado](ACCESO-ANALISTA.md).

Captura 8.3: `202610010003_comparable_capture_details.php` agrega `capture_details`
a appraisal_comparables para áreas por régimen y trazabilidad de evidencia.
No reemplaza filas ni fotos. Ver [Mapas y áreas](COMPARABLES-MAPAS-AREAS.md).

Perito judicial: `202610010002_create_judicial_expert_records.php` crea perfiles
privados por propietario/perito y anexos versionados por expediente, con copia
de cada presentación. Ver [módulo CGP](PERITO-JUDICIAL-CGP.md). No altera muestras.

Comparables sin tope: `202610010001_expand_comparable_sample_index.php` amplía
sample_index de TINYINT a INT UNSIGNED, conservando filas, índices y fotos.
Ver [actualización](COMPARABLES-SIN-TOPE.md). No requiere reimportar muestras.

Comparables: `202609300003_comparable_ph_and_photos.php` agrega régimen PH por
muestra, versión optimista de la colección y fotos privadas en BD vinculadas por
ID estable. Ver [matriz y fotos](COMPARABLES-MATRIZ-FOTOS.md) para despliegue y límites.

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
| `master_documents` | Fichas documentales subidas desde Maestros, con destino lógico, utilidad, módulos relacionados, PDF privado y respaldo interno |
| `midas_documents` | Biblioteca global de descargas comunes de MIDAS, con grupo de capa, utilidad, archivo privado y respaldo interno |
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
`master_documents` no reemplaza esas bibliotecas: permite alojar documentos faltantes,
históricos o de soporte académico y registrar desde qué módulos pueden citarse. El
PDF se guarda una vez y la relación funcional se conserva en metadatos JSON.
`midas_documents` separa los insumos cartográficos comunes de MIDAS de los soportes
particulares del avalúo. Los documentos generales se cargan una vez en el menú MIDAS:
circulares urbanísticas, división política, POT, servicios, movilidad, equipamientos
y riesgos. El numeral 2 conserva solo la evidencia específica de barrio, predio o consulta.

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

## 6. Evidencia de Mercado por unidad (202610020002)

La migración `202610020002_unit_market_evidence.php` añade a `appraisal_units`
`market_evidence_json` (LONGTEXT nullable) y `market_evidence_version` (entero,
predeterminado 0). Guarda identidad/vínculo, naturaleza, matrícula, usos y su
contraste, coeficiente y alcance con sus soportes desde 3.1. No almacena estados
del checklist: éstos se calculan al consultar capítulo 8.

Cada autoguardado valida propietario, expediente y unidad activa; incrementa la
versión mediante actualización condicionada. Una versión antigua devuelve 409
para evitar sobrescribir otro guardado. Desactivar/reactivar conserva la evidencia.
Área privada y fuente usan columnas existentes; no requieren otra migración.
Publicar con el migrador habitual (`php bin/console.php migrate` o AUTO_MIGRATE).


## C2 · alcance del costo (2026-10-02)
Sin migración. Guarda cost_scope anidado en methodology_workflow[component].
Edición parcial conserva campos previos; sólo permite alcance cuando método efectivo
es costo. Usa methodology_version y propietario existentes (409 si obsoleto).
C1 consulta datos de appraisal_units y alcance; estados se calculan, no se almacenan.
No importa catálogos de InversKC ni crea tablas presupuestales en esta entrega.

## Plan de investigación (2026-10-04)
JSON aditivo methodology_workflow[component].research_plan: target_ratio, factors
(decision/kind/definition/categories/reason) y updated_at UTC del servidor.
Sin migración; guardado con owner_id y methodology_version, 409 ante conflicto.
No modifica appraisal_comparables ni la agrupación de anuncios.

