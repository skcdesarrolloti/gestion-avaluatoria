# Renta · Comprender el inmueble y su arriendo

**Historia de la versión del 12/11/2026.** Desde el 13/11, la pantalla vigente es
[Ficha del arriendo](RENTA-FICHA-ARRIENDO.md): descripción/tipologías y ocupación
por unidad en capítulo 3, contratos compartidos en Renta. Esta versión conceptual
y sus respuestas se conservan como antecedentes; el flujo siguiente documenta
la implementación anterior, no la pantalla actual.

Primera etapa aprobada por el usuario tras revisar una propuesta de pantalla.
Solo preparación conceptual para inmuebles urbanos, común a capitalización directa
y flujo de caja descontado. No añade cálculos, tasas o porcentajes de deducción.

## Flujo

Capítulo 8 → recorrido Renta → Insumos → 1. Comprender el inmueble y su arriendo.

- A. Inmueble y alcance: identificación, usos, áreas con sus fuentes, construcción,
  atributos y enlaces a PH, obsolescencias y fotografías. Consulta del encargo.
  Precisa qué parte se estudia, correspondencia con el arriendo y disponibilidad.
- B. Ocupación y contrato: ocupación y sus diferencias, espacios arrendados,
  soporte, vigencia, renovación, terminación y condiciones especiales. Consulta
  canon, periodicidad, administración e IVA del encargo y antecedentes de PH.
- C. Gastos y antecedentes: responsables, reparto, recuperaciones y soportes;
  disponibilidad y períodos de la historia de ocupación, cobros y gastos.
- D. Preparación reunida: resumen vivo y pendientes para ambos métodos. Conserva
  la distinción entre antecedentes, verificación documental y decisiones posteriores.

Preguntas respondidas aparecen plegadas con su respuesta; se pueden revisar.
Faltantes y opciones pendientes se abren para completar. Las ayudas mantienen
explicación, ejemplo, soportes y enlaces normativos. Notas condicionales se
conservan al cambiar de respuesta o tema. Los cuatro temas de Academia siguen
disponibles y sus conexiones con Insumos se actualizaron.

## Origen y alcance de los datos

IncomeSubjectContext y IncomeSubjectDetails consultan el sujeto autorizado,
la unidad del recorrido y PH. No guardan una copia de los datos del capítulo 3.
Los faltantes de ese capítulo se completan mediante enlaces a su formulario de
origen; las condiciones específicas de Renta se diligencian en esta primera etapa.

La ocupación global solo se reutiliza cuando existe una única unidad principal
completa, sin otras unidades/anexos ni separación por partes. En los demás casos
se consulta como contexto y se solicita la situación propia del recorrido.
«Arrendado» del capítulo 3 se presenta como «alcance por precisar»; no se supone
ocupación total. «Mixta» y «En venta» no se convierten en vacancia o arriendo parcial.
Una respuesta propia de Renta conserva prioridad visible y cualquier diferencia
con el origen se advierte; no hay sustitución automática ni conciliación silenciosa.

Uso observado no prueba uso aprobado. Área privada/construida no equivale a área
arrendable. Cuota de administración no determina quién soporta el gasto. El dato
económico global no se atribuye automáticamente a cada unidad. Calificaciones y
obsolescencias no se convierten en tasas o descuentos.

## Persistencia y seguridad

Sin DDL ni migración nueva. Se amplía el objeto existente
`methodology_workflow[recorrido].income_concept`, con listas permitidas y notas
opcionales hasta 1000 caracteres. Se conservan los campos previos y el guardado
parcial. Cambiar de método o desactivar/reactivar un contraste conserva los datos.

Misma ruta POST protegida por sesión, actividad, propietario, recorrido Renta,
CSRF y versión optimista. Error por campo 422; conflicto 409 sin sobrescritura.
Autoguardado de 800 ms, guardado manual, reintento, recuperación y aviso de red
existentes. Sin localStorage; el borrador sin conexión requiere conservar la página.
Los temas permanecen montados y se conserva la carga diferida de las muestras.
JS adicional dentro del bundle de Renta cargado bajo demanda; no añade librerías.

## Validación

- PHP: 1650 verificaciones; JavaScript: 283 verificaciones.
- BD: 250 verificaciones, solo ga_test_app/ga_test_auth en MariaDB local desechable
  con GA_TEST_PORT=3383; incluye migración repetida y conservación de datos.
- HTTP real local: 14 verificaciones de login, CSRF, acceso ajeno, inactivo,
  conflicto, validación, fallback sin JS y recuperación de los campos nuevos.
- Navegador con PHP y BD reales: referencia viva sin duplicar ocupación,
  autoguardado, recarga, conflicto entre pestañas con borrador conservado,
  desconexión/reintento, cuatro temas en móvil 390 y escritorio, sin errores JS.
- Lint PHP, build y check:size: 79,9 KB gzip de recursos iniciales, límite 80 KB.

Comandos: `php tests/run.php`, `npm test`, `npm run build`, `npm run check:size`,
`GA_TEST_PORT=3383 php tests/database.php` en instancia nueva y
`GA_TEST_HTTP=true GA_TEST_PORT=3383 php tests/income-concept-http.php` con servidor
local de prueba. Fixtures y capturas de navegador en tests/.runtime, fuera de Git.
Los avisos previos de fixtures sobre sesión/sample_index no proceden de esta ampliación.

Publicación: commit/push 50eaf67 a origin/main. Tras la actualización, app.js e
income-inputs.js públicos respondieron HTTP200 con SHA-256 idéntico al local.
La apertura nueva del expediente redirigió a login, por lo que no se acredita
revisión visual autenticada del hosting. Se conservaron respuestas y muestras
reales; no se solicitó acceso al hosting ni se modificó InversKC.
