# Factores del sujeto · capítulo 3

Captura por unidad en «Factores del sujeto», con búsqueda por nombre y pestañas
de unidades. Usa el catálogo compartido de los 12 tipos de inmueble y las escalas
guardadas del propietario; los anexos usan su propio tipo, no el de la oficina.
Las áreas permanecen en Superficie y la destinación es un filtro, no un factor.

Cada atributo guarda valor o clase, soporte y clasificación vigente. Cantidades
y edades conservan su medida; binarios y ordinales muestran su código compartido.
Vista usa la jerarquía aprobada: 0 Sin vista, 1 Interior, 2 Exterior: calles y avenidas,
3 Exterior: paisajística. Estos códigos no son incrementos de precio. Un dato desconocido
queda pendiente: nunca se convierte automáticamente en cero o «No».

Los datos existentes se muestran para comprobarlos; «Usar dato existente» copia
sólo valores compatibles y exige completar su soporte. Guardar campos nuevos vacíos
no sustituye esos datos. Las clasificaciones anteriores se conservan y requieren
confirmación explícita al adoptar otra escala. No hay recodificación automática.

El capítulo 8 consulta esta referencia; una captura del sujeto no puede sustituirse
por otra calificación manual desde Insumos. Cambiar valor o soporte cambia la huella
del factor. Si el recorrido tiene una escala anterior, debe revisarse/adoptarse allí.

Migración aditiva `202610040002_subject_factor_capture.php`: JSON y versión en
`appraisal_units`, sin rellenar registros existentes. Guardado CSRF, propietario,
unidad activa y versión comparada atómicamente; error 409 ante cambios concurrentes.
Esto prepara observaciones para Análisis: no implementa regresión, coeficientes,
correlaciones ni selección estadística de factores.

Verificación: 1073 comprobaciones PHP, 153 JS y 191 de persistencia en bases
de prueba nuevas, puerto 3377; navegador local con datos ficticios, guardar/recargar
Vista y Ascensor y consulta en capítulo 8. Lint, compilación y límite gzip verificados.

Apartamento: unidad privada, celdas de parqueo y copropiedad PH en apartados
plegables. Piscina/gimnasio, ascensor que sirve a la unidad, vigilancia y planta
comunes tienen claves ph_* explícitas; publicaciones ambiguas no se reinterpretan.
Alcoba de servicio es binaria; acabados terminados único factor ordinal. Piso
y niveles internos permanecen distintos. Los factores retirados sólo se muestran
si ya estaban capturados/en un plan anterior, con alcance por revisar. No cambia
casa ni los demás tipos. En comparables se conserva fuente y se verifica el
alcance común mediante calificación con soporte; no hay inferencia automática.
Integración al modelo pendiente de Análisis. 1083 PHP, 153 JS, 194 BD3379.

Casa aprobada: 19 datos privados, 3 de parqueo y 5 comunes PH disponibles si
aplican. Agrega depósitos, jacuzzi propio y claves propias house_* para piscina,
gimnasio, vigilancia, planta y acceso. No transforma registros ambiguos previos.
Acabados/servicio unificados; aire y ruta accesible retirados del catálogo nuevo.
Vista pasa a ordinal 0–3 por instrucción explícita del usuario; la escala anterior
permanece y requiere restablecimiento en catálogo, adopción en Insumos y confirmación
del sujeto. Una escala inválida o anterior sin confirmar no presenta código aplicado.
No modifica coeficientes ni implementa Análisis. 1096 PHP, 153 JS, 196 BD3380.

Vigente 2026-10-05: captura integrada en 3.4 > Factores para investigación · módulo8. Calificación anterior conservada en otra subpestaña. Catálogo incorpora observables anteriores por tipo y ya no limita candidatos a cuatro. Ver CALIFICACIONES-INTEGRADAS-3-4.md.
# Reutilización de características (2026-10-05)

SubjectFactorSource vincula observaciones existentes de la misma unidad en 3.2,
3.3 y 3.4 con la referencia del módulo 8. Cuando la medida o clase es compatible,
la tarjeta muestra Dato vinculado, su origen y Editar en el apartado original;
no presenta otro input ni exige volver a escribir el soporte existente. Abrir
la subpestaña de investigación espera autoguardados y actualiza por fetch.

Se reutilizan variables funcionales, edad, niveles, frente/fondo de lote o finca
y clases observadas de los diferenciales. Las escalas anteriores no se convierten
en pesos, coeficientes ni medidas: piso alto no produce un número de piso,
frente de lote no produce frente comercial, depósito presente no produce cantidad,
Exterior o Panorámica genéricos no acreditan calle o paisaje. Sólo equivalencias
explícitas se resuelven, por ejemplo Calle y Mar en la Vista aprobada.

Las capturas de investigación ya diligenciadas se conservan y tienen prioridad;
un borrador vacío permite reutilizar la fuente. Factores personalizados requieren
verificación de la equivalencia. No hay migración ni copias a JSON que queden
desactualizadas: el valor vinculado se lee del registro original. Las firmas por
factor incluyen origen y soporte para avisar de cambios en investigación.

Validación: 1410 verificaciones PHP, 155 pruebas JS, 210 verificaciones BD en
instancia desechable 3390, lint/build/tamaño 71,3 KB. Navegador local: captura en
3.3, autoguardado, lectura vinculada, cambio de 2 a 3 y actualización por fetch;
presentación en escritorio y pantalla estrecha. Sin cambios en fórmulas valuatorias.

Presentación de origen (2026-10-05): los datos reutilizados aparecen en input readonly, sin name de captura, sombreado gris y aviso de origen/no digitado en esta sección. Si el campo original existe pero está vacío, se identifica apartado y atributo con 'no digitado en su origen'. No se modifica persistencia ni fórmulas. 1411 PHP, 155 JS, lint/build/tamaño71,3KB; navegador verifica campo no editable y aviso de vacío.
