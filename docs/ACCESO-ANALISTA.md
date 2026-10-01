# Acceso del analista desde Perito responsable

Petición del responsable: registrar acceso en el mismo apartado del perito, con
contraseña inicial igual al usuario, y mantener al titular como perito del avalúo.

## Uso

1. El titular entra con su cuenta habitual: Maestros → Perito responsable → Acceso del analista.
2. Registra nombre y usuario del analista (inicial y apellido), y selecciona su propia
   ficha RAA vigente como responsable. No debe reutilizar el usuario del titular.
3. El analista entra por el login habitual. La clave inicial es su usuario; antes de
   abrir cualquier expediente debe elegir una clave propia y volver a iniciar sesión.
4. Los avalúos nuevos del analista quedan bajo el espacio del titular, quien los ve
   y edita desde su cuenta. Volver a abrir/actualizar la ficha consulta el servidor;
   no hay sincronización visual en vivo. Se mantiene el control de versiones/409.

El analista solo accede a sus nuevas fichas; no a expedientes anteriores del titular,
otros analistas, administración o presentación judicial. No necesita aportar un RAA
propio para diligenciar. El responsable asignado queda seleccionado en el expediente
y protegido en servidor; no implica firma, presentación ni habilitación automática
de categorías. Las validaciones de RAA/categoría existentes siguen vigentes.

## Datos y permisos

- Migración `202610010004_create_analyst_access.php`: tabla privada `analyst_accounts`
  y columna nullable `appraisals.analyst_account_id`. No cambia propietarios previos.
- La conexión de funcionarios sigue solo lectura; no crea ni modifica WordPress.
- Login compartido tiene prioridad. No se permite crear nombres ya usados en
  funcionarios ni en los accesos locales. Claves con password_hash/password_verify.
- Sesión conserva el actor local (`analyst_id`, nombre), responsable y versión de
  acceso. `id` mantiene el espacio propietario por compatibilidad de repositorios.
  No tratar ese `id` como identidad del actor para una futura auditoría de ediciones.
- Política central de rutas verifica ficha + propietario + analista + responsable.
  Lista filtrada en servidor; prohibición de administración y anexo judicial.
- Actividad del titular se revalida. Revocar acceso/cambiar clave invalida sesiones.
  Desactivar no elimina avalúos ni fotos. No hay recuperación automática de clave ni
  asignación retroactiva de expedientes en esta entrega.
- Creación y clave son envíos explícitos con CSRF; nunca autoguardado de contraseñas.

## Despliegue y validación

Actualizar código/assets y ejecutar `php bin/console.php migrate`, o ingresar primero
con la cuenta compartida del titular si AUTO_MIGRATE está activo. Luego crear acceso.
No se han creado cuentas ni alterado permisos en producción.

398 verificaciones PHP, 106 pruebas JS; 64 verificaciones MySQL en instancia desechable
33330 (ga_test_app/ga_test_auth), incluyendo migración repetida y conservación.
Prueba HTTP con dos sesiones independientes: creación por titular, clave inicial
bloqueada para expedientes, cambio y nuevo ingreso, CSRF, alta de avalúo, guardado
de comparable por analista y lectura/listado por titular; rechazo de ficha anterior
y cambio de responsable. Revisión visual escritorio; móvil DOM sin desbordamiento.
Lint/build/check:size incluidos. Esta validación es local, no del hosting.

## Diagnóstico de alta duplicada

El formulario distingue usuario compartido de funcionarios, analista propio activo
 o desactivado y nombre no disponible. La lista muestra un contador y un estado
vacío explícito. No restablece contraseñas ni vincula cuentas compartidas mediante
el nombre de usuario. Validación: lint, 398 verificaciones PHP, 106 JS, build y
check:size 57,2 KB. No se verificó la cuenta sabuita en la base de producción:
la configuración local no dispone de conexión a ese servidor.
