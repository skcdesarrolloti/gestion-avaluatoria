# Entrega al responsable de la implementación

## Punto de partida

Proyecto: `C:\Workspace\desarrollo-skc\gestion-avaluatoria`.
Origen de consulta: `C:\Workspace\desarrollo-skc\inverskc`.
La base nueva no necesita cargar ningún archivo del proyecto original.
Contiene acceso y un ciclo real de crear, editar, autoguardar y recuperar fichas privadas.
Incluye biblioteca inicial de Normas Técnicas Sectoriales con categorías A, B y 1 a 13.
Incluye menú base de Marco Jurídico Nacional con bibliografía B1 a B13 para cargar
leyes, decretos, resoluciones y documentos derogados cuando el responsable los entregue.
Las leyes extensas se modelan como documento fuente más artículos o fragmentos
pertinentes, no como texto completo indiscriminado. Las IVS quedan en menú separado.
Las NIIF quedan en otro menú independiente para consultas de medición contable.
Incluye catálogo de Tipologías Constructivas IGAC con imágenes, agrupado por categoría.
Solo implementa datos iniciales, no fórmulas, aprobación ni generación de informes.

## Referencias encontradas en InversKC

El controlador `controllers/ValuatoriaController.php` supera 1 MB y varias vistas
también son extensas. Consultarlos por función para extraer responsabilidades pequeñas:

| Módulo futuro | Referencia de vistas en `views/valuatoria/` |
| --- | --- |
| Avalúos urbanos | `avaluos_urbanos.php` |
| Posesión | `avaluo_posesion.php`, `avaluo_posesion_v1.php` |
| Sujeto y comparables | `definicion_sujeto_comparables.php`, variante `v2` |
| Sector y entorno | `caracterizacion_sector_entorno.php` |
| Identificación jurídica | `identificacion_caracteristicas_juridicas.php` |
| Propiedad horizontal | `caracteristicas_agrupacion_ph.php` |
| Valoración cualitativa | `valoracion_cualitativa.php` |
| Multicriterio | `modelos_multicriterio_critic.php` |
| Informe técnico | `informe_tecnico_avaluo.php` |

Las referencias del login son `control-servicios-inmobiliarios/src/Core/Auth.php`
y `dashboard-marketing/app/Auth.php`. Se usa el contrato de credenciales, no sus sesiones
ni dependencias WordPress. La skill de diseño disponible no tenía su script `search.py`;
se aplicó su guía de accesibilidad, rendimiento e interacción directamente.

## Entregar al programador por cada módulo

1. Campos, tipos, unidades, catálogos, ejemplos y obligatoriedad al finalizar.
2. Reglas y fórmulas confirmadas, redondeos y casos de cálculo esperados.
3. Etapas del flujo, responsables, permisos y aprobación de documentos.
4. Secciones que autoguardan y qué acción constituye envío definitivo.
5. Documentos permitidos, límites, almacenamiento privado y descarga autorizada.
6. Criterios de aceptación y datos de prueba anonimizados.

## Secuencia sugerida

Configurar y comprobar acceso → definir expediente y permisos → migraciones y servicios
por módulo → formularios pequeños con autoguardado → pruebas con casos aprobados →
informes y revisión → importación histórica independiente si se solicita.

No hay aún datos migrados, motor de cálculos, PDF/DOCX, carga de anexos, auditoría de
negocio ni colaboración entre funcionarios. Agregarlos cuando el jefe entregue la
implementación. Si se generan documentos de SuCasa, aplicar la skill de branding correspondiente.

## Contrato del autoguardado

POST `/avaluos/{id}/borrador`, cookie de sesión, token CSRF y JSON con `version`,
`titulo`, `tipo`, `direccion`, `municipio`, `observaciones`. Se aceptan borradores vacíos.
Respuesta 200 confirma versión y fecha. 401 sesión, 419 CSRF, 422 validación,
404 ficha inexistente/ajena, 409 conflicto de versión. El frontend conserva valores
en memoria ante error y bloquea sobrescritura ante conflicto; hay que copiar los cambios
antes de recargar. La pérdida de conexión no equivale a haber guardado.

La base no promete recuperar texto no enviado tras cerrar el navegador. Recupera
de BD lo confirmado. No se autoguardan credenciales ni acciones definitivas.

## Pruebas aisladas

`php tests/run.php` comprueba validación y autenticación con SQLite en memoria.
`npm test` comprueba estados y concurrencia del cliente sin instalar navegador.
`php tests/database.php` comprueba MySQL/MariaDB en bases desechables locales;
requiere `GA_TEST_PORT`, opcional `GA_TEST_USER`/`GA_TEST_PASSWORD`. Nunca usa `.env`
para elegir una base a borrar: no borra bases ni tablas y rechaza fixtures existentes.

Repetir revisión visual en navegador cuando cambien vistas. No declarar validado el
login real hasta tener conexión a funcionarios y una cuenta de prueba autorizada.

## Normas Técnicas Sectoriales

La migración `202609150003_create_valuation_standards.php` crea el catálogo y siembra
las 22 normas entregadas. Los archivos PDF se copian al almacenamiento privado con:

```powershell
php bin/console.php standards:import "C:\Users\skcge\OneDrive\Escritorio\Nueva Ley Valuatoria\Normas Sectoriales"
```

Las categorías sin normas asignadas quedan visibles como pendientes para conservar la
estructura oficial de inscripción.

## Marco jurídico e IVS

El menú jurídico nacional queda listo para sembrar leyes, decretos, resoluciones,
actos y artículos aplicables por categoría. El menú de Normas Internacionales de
Valuación registra la estructura IVS por familia y su relación con categorías RAA.
Los PDFs fuente del marco jurídico se importan desde la pantalla del módulo y se guardan
en `storage/marco-juridico-nacional/`; Git conserva la carpeta base pero ignora los PDFs.
Los PDFs de IVS se cargan desde cada tarjeta internacional y se guardan en
`storage/normas-internacionales-valuacion/`.
Los PDFs de NIIF se cargan desde cada tarjeta NIIF y se guardan en `storage/normas-niif/`.
El menú también enumera los campos del expediente y separa soporte normativo directo,
derivación metodológica y control operativo interno.
