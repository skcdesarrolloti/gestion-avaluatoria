# Crear y editar factores

Capítulo 8 → Configuración → Catálogo de factores. Crear nuevo factor aparece
una sola vez; cada atributo tiene un editor plegable. Se pueden editar nombre,
definición, clasificación, niveles, alcance y asignaciones por tipo de inmueble.
Las definiciones son del propietario y se reutilizan en sus expedientes.

Presencia: 0 No / 1 Sí. Jerarquía: primera línea = 0, siguientes = 1, 2…;
el usuario ordena niveles de menor a mayor. Clases sin orden no reciben puestos.
Medidas numéricas conservan unidad y tipo; para otra medida se crea otro factor.
Desconocido queda pendiente. Área y destinación siguen como base y filtro.

Acabados terminados: 0 Básico/económico, 1 Medio, 2 Bueno, 3 Alto,
4 Superior/lujo; obra gris corresponde a ejecución. Oficina tiene 13 atributos,
sin estrato, acceso, aire acondicionado ni ruta accesible. PH y parqueo son grupos
separados; anteriores permanecen sólo cuando hay datos o planes que los usan.

Migración aditiva 202610040003_user_research_factors.php crea tabla por propietario
y clave estable. La aplica AUTO_MIGRATE o `php bin/console.php migrate`.
CSRF, acceso al expediente y versión optimista protegen el guardado. Datos y
selecciones se conservan ante errores de validación. No se borran factores ni
capturas al retirar asignaciones; la compatibilidad histórica sigue disponible.

Kernel carga definiciones para los módulos de catálogo, metodología y sujeto.
Capítulo 3 y el plan de investigación comparten catálogo. Las definiciones editadas
prevalecen sobre escalas anteriores del propietario. Las capturas conservan clase,
soporte y huella de definición; un cambio de medida, definición, alcance o niveles
deja pendiente la captura hasta verificarla expresamente. Planes previos conservan
su definición; adoptan la vigente mediante el mecanismo existente de Insumos.
Nuevo factor sin evidencia de portal se señala como investigación manual.

No se implementa correlación, regresión ni coeficientes de ajuste en esta entrega.
Validación: 1106 PHP, 153 JS, 209 persistencia en base desechable puerto 3383;
lint, compilación, tamaño 71,3 KB. Navegador local: crear, editar, validar niveles
repetidos sin perder texto, comprobar dos tipos y guardar/recargar dato del sujeto.
