# Academia NIIF - Valor razonable y jerarquia de datos

## Proposito del documento

Este documento sirve como ficha academica para la biblioteca de Normas NIIF y para
el capitulo metodologico del avaluo cuando el encargo tenga finalidad contable,
financiera, corporativa, de auditoria, deterioro, revelacion o medicion de valor
razonable. Su uso no reemplaza la norma completa ni el juicio del valuador; orienta
la forma en que el analista debe explicar la base de medicion y la calidad de los
datos utilizados.

En lenguaje NIIF se recomienda hablar de **activo**, no de bien, cuando el encargo
se formula para estados financieros o reportes contables. El informe valuatorio
puede mantener la descripcion inmobiliaria del inmueble, pero al conectar con NIIF
debe precisar que la medicion recae sobre el activo, el derecho o la unidad de
cuenta definida por el encargo.

## Marco NIIF aplicable

NIIF 13 / IFRS 13 define el valor razonable, establece un marco para medirlo y exige
revelaciones sobre esas mediciones cuando otra NIIF requiere o permite medir a valor
razonable. No decide por si sola cuando un activo se mide a valor razonable; esa
decision proviene de la norma contable que aplique al caso, por ejemplo NIC 16,
NIC 36, NIC 40, NIIF 5, NIIF 16 u otra norma relacionada.

La definicion operativa de valor razonable se resume asi: precio que seria recibido
por vender un activo, o pagado por transferir un pasivo, en una transaccion ordenada
entre participantes de mercado, en la fecha de medicion. Por tanto, la medicion se
lee desde la perspectiva de participantes de mercado y bajo condiciones actuales,
no desde la intencion particular de la entidad de conservar, liquidar o usar el
activo de una forma aislada del mercado.

En activos inmobiliarios, esta lectura obliga a explicar:

- cual es el activo o derecho medido;
- cual es la fecha de medicion;
- cual es el mercado principal o mas ventajoso observable para ese activo;
- que uso, restricciones, estado fisico, localizacion y condiciones juridicas
  tendrian en cuenta participantes de mercado;
- que informacion fue observable y que informacion dependio de supuestos del
  analista.

## Jerarquia del valor razonable

La jerarquia NIIF no ordena los metodos de avaluo. Ordena la calidad y observabilidad
de los datos usados en la medicion. El aplicativo debe permitir que el analista
seleccione el nivel trabajado y justificarlo con la evidencia disponible.

### Nivel 1 - Datos observables para activos identicos

Corresponde a precios cotizados o transados en mercados activos para activos
identicos, disponibles en la fecha de medicion. Es el nivel de mayor prioridad
porque no requiere ajustes significativos.

En inmuebles suele ser excepcional. Un activo inmobiliario normalmente tiene
caracteristicas propias de localizacion, area, estado, uso, regimen juridico,
restricciones urbanisticas y condiciones fisicas que impiden tratarlo como identico
a otro activo cotizado en un mercado activo.

**Uso sugerido en el entregable:** solo seleccionar Nivel 1 cuando exista evidencia
directa, actual y verificable de precio para el mismo activo o para un activo
realmente identico en un mercado activo, sin ajustes relevantes.

### Nivel 2 - Datos observables para activos similares

Corresponde a datos observables, directa o indirectamente, distintos de los precios
de Nivel 1. En avaluos inmobiliarios suele ser la referencia mas comun cuando existen
ofertas, transacciones, canones, tasas o indicadores de mercado verificables para
activos similares, y el valuador puede homologar diferencias de ubicacion, area,
estado, uso, vetustez, regimen juridico, ingresos, riesgos o condiciones de mercado.

El Nivel 2 no significa que todos los comparables sean perfectos. Significa que la
base principal de la medicion proviene de evidencia observable de mercado y que los
ajustes son explicables, trazables y consistentes.

**Uso sugerido en el entregable:** seleccionar Nivel 2 cuando el valor se sustente
principalmente en comparables, ofertas, transacciones o rentas observables de activos
similares, con homologaciones razonables y soportadas.

### Nivel 3 - Datos no observables o supuestos significativos

Corresponde a datos no observables para el activo. Se utiliza cuando no existe mercado
suficiente, cuando los datos disponibles son escasos o cuando la medicion depende de
modelos, supuestos internos, flujos proyectados, costos de reposicion, depreciaciones,
tasas, escenarios residuales o ajustes significativos que participantes de mercado
podrian considerar pero que no son directamente verificables en mercado.

El Nivel 3 no invalida la medicion; exige mayor revelacion. El informe debe explicar
las hipotesis relevantes, las fuentes disponibles, las limitaciones, la sensibilidad
del resultado y la razon por la cual el modelo adoptado representa la mejor evidencia
posible para la fecha de medicion.

**Uso sugerido en el entregable:** seleccionar Nivel 3 cuando el valor dependa de
supuestos significativos, modelos financieros, tecnica residual, costo con
depreciaciones relevantes, flujos proyectados o informacion no observable de manera
suficiente.

## Texto base para incorporar al entregable

Cuando el encargo tenga finalidad NIIF, el texto automatico debe adaptarse asi:

> Para efectos de la lectura financiera del encargo, el objeto de medicion se trata
> como activo, conforme al alcance indicado por el solicitante. La medicion se analiza
> bajo la base de valor razonable cuando esta sea requerida o permitida por la norma
> contable aplicable. El valor razonable se entiende como una medicion basada en
> participantes de mercado, a la fecha de medicion, considerando el activo, su estado,
> localizacion, uso, restricciones, mercado relevante y datos disponibles.
>
> La jerarquia NIIF se utiliza para revelar la calidad de los datos empleados. En este
> caso, el analista clasifica la medicion en el Nivel [1/2/3], porque [explicar fuente
> principal: precio de activo identico, comparables observables, o supuestos/modelos
> significativos]. Esta clasificacion no reemplaza la metodologia valuatoria; permite
> dejar trazabilidad sobre la observabilidad de los insumos, los ajustes aplicados y
> las limitaciones que deben ser consideradas por el usuario del informe.

## Reglas practicas para el aplicativo

- Si `aplica_niif` es "Si", el modulo debe mostrar una seleccion obligatoria de nivel
  NIIF: Nivel 1, Nivel 2 o Nivel 3.
- El campo debe llamarse "Nivel de jerarquia NIIF" y debe pedir justificacion breve.
- El texto teorico debe incorporarse al entregable solo cuando el encargo active NIIF
  o cuando la base de valor sea valor razonable, recuperable, deterioro, propiedad de
  inversion, derecho de uso o revelacion financiera.
- La recomendacion inicial puede sugerir Nivel 2 para mercado observable y Nivel 3 para
  supuestos/modelos significativos, pero la decision final queda al analista.
- El sistema debe evitar afirmar que un activo inmobiliario es Nivel 1 si no hay soporte
  directo de activo identico en mercado activo.
- Si la fuente principal es mercado comparable, el entregable debe hablar de "datos
  observables de activos similares" y de homologaciones.
- Si la fuente principal es residual, costo, renta proyectada o supuestos internos,
  el entregable debe explicar que existen insumos de Nivel 3 y revelar los supuestos
  significativos.

## Fuentes de referencia

- IFRS Foundation, NIIF 13 / IFRS 13 - Fair Value Measurement:
  https://www.ifrs.org/issued-standards/list-of-standards/ifrs-13-fair-value-measurement/
- IFRS Foundation, NIC 40 / IAS 40 - Investment Property:
  https://www.ifrs.org/issued-standards/list-of-standards/ias-40-investment-property/
- IFRS Foundation, proyecto Fair Value Measurement:
  https://www.ifrs.org/projects/completed-projects/2011/fair-value-measurement/
