# Capítulo 5 — Normatividad urbana: academia base

Investigación de referencia al 24 de septiembre de 2026. Este documento separa la fuente normativa de las reglas que luego debe aplicar la aplicación. No reemplaza el concepto oficial de uso del suelo ni una licencia urbanística.

## Hallazgo principal

Cartagena conserva como base del POT vigente el Decreto 0977 de 2001, pero la lectura práctica de un predio no debe depender solo de ese decreto. Para un avalúo se debe consultar una cadena de fuentes: POT vigente, cartografía oficial, MIDAS, cuadros de reglamentación, instrumentos o resoluciones especiales, determinantes ambientales y, cuando exista, concepto de uso del suelo expedido por Planeación Distrital.

El nuevo POT de Cartagena está en trámite en 2026. Según publicaciones oficiales, fue avalado por el Consejo de Gobierno en julio de 2026, radicado ante autoridades ambientales el 28 de julio de 2026 e inició concertación ambiental en agosto de 2026. Mientras no sea adoptado mediante el trámite legal y aprobación correspondiente, debe tratarse como proyecto o insumo de contexto, no como norma vigente para concluir uso del suelo.

## Jerarquía práctica para el módulo

1. Norma nacional y determinantes de superior jerarquía.
   - Ley 388 de 1997, especialmente determinantes de ordenamiento territorial.
   - Decreto 1077 de 2015 y modificaciones relacionadas con ordenamiento territorial, licencias y conceptos.
   - Decreto 1232 de 2020, que modifica reglas de planeación del ordenamiento territorial.
   - Determinantes ambientales de CARDIQUE/EPA, entre ellas la Resolución 0944 de 2020 cuando aplique.

2. Norma distrital vigente.
   - Decreto 0977 de 2001, POT vigente de Cartagena.
   - Cartografía oficial del POT vigente, especialmente planos de clasificación, usos y tratamientos.
   - Cuadros de reglamentación del Decreto 0977.

3. Instrumentos o actos especiales por localización.
   - Resolución 043 de 1994 para Centro Histórico, área de influencia y periferia histórica, cuando el predio caiga en esos sectores.
   - PEMP u otros instrumentos patrimoniales cuando estén adoptados o sean soporte oficial aplicable.
   - Planes parciales, macroproyectos, actos de movilidad, riesgo, espacio público, licencias o resoluciones específicas si recaen sobre el área.

4. Evidencia oficial predial.
   - MIDAS como evidencia cartográfica y de ubicación, indicando fecha de consulta y capas usadas.
   - Certificado o concepto de uso del suelo expedido por Planeación Distrital para la referencia catastral o predio.
   - Respuesta oficial SIGOB/Planeación, si existe.

5. Fuentes auxiliares para el avalúo.
   - Documento del solicitante, certificado de tradición, impuesto predial, ficha catastral, visita técnica, fotografías, plano o mapa de localización.
   - Si hay contradicción entre fuentes, el sistema debe conservar todas y obligar al analista a dejar salvedad.

## Reglas de academia que debe enseñar la pantalla

### POT vigente y estado del nuevo POT

El sistema debe explicar que el POT vigente es el Decreto 0977 de 2001, salvo que el usuario documente un acto posterior aplicable. El proyecto de nuevo POT 2026 se puede registrar como contexto, pero no debe reemplazar la norma vigente hasta que exista acto adoptado.

Campo sugerido: `estado_norma_pot` con opciones:
- POT vigente consultado.
- POT vigente + acto posterior aplicable.
- Nuevo POT en trámite consultado solo como contexto.
- Pendiente de validación oficial.

### MIDAS no reemplaza el concepto de Planeación

MIDAS debe guardarse como fuente cartográfica: capa, fecha de consulta, captura o PDF, uso identificado y observación. Si MIDAS no arroja resultado o arroja capas contradictorias, el entregable debe decir que la consulta requiere validación ante Planeación.

Campos sugeridos:
- Fuente MIDAS consultada: sí/no.
- Fecha de consulta.
- Capa o plano: clasificación del suelo, uso del suelo, tratamiento, riesgo, protección, patrimonio.
- Resultado observado.
- Captura o soporte.
- Nivel de confianza: preliminar, soportado, requiere concepto oficial.

### Concepto de uso del suelo

El concepto expedido por Planeación informa usos permitidos y normas urbanísticas aplicables al predio, pero no otorga por sí mismo licencia, derecho adquirido ni autorización para intervenir. El sistema debe guardar su radicado, fecha, referencia catastral, actividad consultada, conclusión y salvedades.

Campos sugeridos:
- Radicado del concepto.
- Fecha del concepto.
- Referencia catastral usada.
- Actividad solicitada o uso pretendido.
- Norma citada por Planeación.
- Conclusión: permitido, compatible, restringido, prohibido, requiere concepto/licencia, no disponible.
- Texto literal relevante.
- Soporte PDF.

### Cuadro de reglamentación de usos

El módulo debe permitir seleccionar el tipo de actividad o zona normativa y luego registrar el cuadro aplicable. Ejemplo visto en conceptos de Planeación: Cuadro No. 7 de actividad mixta en suelo urbano y suelo de expansión para Mixto 2 o Mixto 5. El sistema debe conservar los grupos: uso principal, compatible, complementario, restringido y prohibido.

Campos sugeridos:
- Cuadro normativo aplicable.
- Tipo de suelo o actividad: Residencial A/B, Mixto 2, Mixto 5, Comercial, Institucional, Industrial, Portuario, Turístico, zona verde/protección, otro.
- Uso principal.
- Uso compatible.
- Uso complementario.
- Uso restringido.
- Uso prohibido.
- Actividad del inmueble valorado.
- Cruce actividad vs cuadro.
- Conclusión urbanística.

### Uso restringido

Cuando una actividad aparece como restringida o condicionada, el sistema no debe concluir automáticamente que está permitida. Debe pedir soporte de Planeación o dejar salvedad. La academia debe explicar que el uso restringido requiere valoración frente al área o tratamiento y puede necesitar concepto previo de Planeación.

Campo sugerido: `requiere_concepto_planeacion` sí/no/pendiente.

### Centro Histórico, periferia histórica y patrimonio

Si el predio cae en Centro Histórico, área de influencia o periferia histórica, debe activarse una ruta especial de academia. La Resolución 043 de 1994 reglamenta el Centro Histórico de Cartagena, áreas de influencia y periferia histórica, incluyendo usos, intervenciones y listados prediales. El Decreto 0977 también incorpora reglamentación urbana asociada a estos sectores.

Campos sugeridos:
- ¿Está en área patrimonial o influencia? sí/no/pendiente.
- Instrumento aplicable: Resolución 043 de 1994, POT, PEMP, otro.
- Categoría o listado predial.
- Usos permitidos/prohibidos.
- Segunda opción de uso si aplica.
- Restricción patrimonial para el avalúo.

### Determinantes ambientales

El capítulo 5 debe consultar si el predio está afectado por determinantes ambientales o de riesgo. Esto puede venir de CARDIQUE, EPA, POT, cartografía o concepto oficial. Si aplica, debe alimentar tanto capítulo 5 como condiciones restrictivas.

Campos sugeridos:
- Autoridad ambiental: CARDIQUE, EPA Cartagena, otra.
- Determinante ambiental identificada.
- Riesgo, ronda, protección, humedal, manglar, zona costera, inundación, remoción, otro.
- Norma/acto soporte.
- Incidencia en uso, edificabilidad o comercialización.

## Estructura propuesta para la pantalla del capítulo 5

1. Identificación normativa del predio.
   - Referencia catastral, dirección, barrio, coordenadas, fuente de localización.

2. Fuentes consultadas.
   - POT/Decreto, MIDAS, Planeación, certificados, cuadros, resoluciones, POT en trámite si se revisó.

3. Clasificación y localización urbanística.
   - Clase de suelo, área de actividad, zona normativa, tratamiento, condición especial.

4. Uso del suelo y actividad.
   - Uso actual, uso pretendido, actividad económica o funcional, cuadro aplicable.

5. Reglamentación del cuadro.
   - Principal, compatible, complementario, restringido, prohibido.

6. Edificabilidad y restricciones.
   - Altura, índices, ocupación, construcción, aislamientos, parqueaderos, cesiones, protección, riesgo.

7. Soportes visuales.
   - Captura MIDAS, plano POT, imagen de localización normativa, certificado o concepto PDF.

8. Conclusión urbanística para el avalúo.
   - Uso permitido o no, efecto sobre mayor y mejor uso, efecto en mercado/valor, salvedades.

9. Ampliaciones del entregable.
   - Mantener el bloque de numerales adicionales al final como en los capítulos anteriores.


## Documento aportado: cuadros de uso del Decreto 0977 de 2001

Archivo revisado: `C:\Users\skcge\Downloads\pdf_descargas_pot2001_decreto_0977_2001_cuadro_uso_250519_140954.pdf`. Tiene 14 páginas y contiene los cuadros de reglamentación de actividades del Decreto 0977 de 2001. No debe copiarse como archivo pesado dentro del repositorio; en la aplicación debe cargarse como documento normativo administrable, con metadatos y extractos referenciables.

Tratamiento recomendado, igual a la biblioteca NIIF/IVS/NTS:

- Registrar documento fuente: título, tipo, entidad, fecha normativa, fecha de carga, vigencia, versión, enlace o archivo, observación y responsable de carga.
- Extraer solo los cuadros, artículos o fragmentos aplicables a la ficha de un avalúo.
- Referenciar el documento cuando el analista seleccione una actividad, cuadro o uso.
- Mantener historial: si se carga una resolución posterior, no borrar el cuadro anterior; marcarlo como reemplazado, complementado o vigente con salvedad.
- Permitir adjuntar soporte PDF y capturas MIDAS, pero el entregable debe citar solo el fragmento usado.

Cuadros identificados en el PDF:

| Página | Cuadro | Contenido principal | Uso para el módulo |
| --- | --- | --- | --- |
| 1 | Cuadro No. 1 | Actividad residencial en suelo urbano y de expansión: Residencial tipo A, B, C y D. | Cruce de uso residencial, compatibilidades, restricciones, áreas, alturas, aislamientos y estacionamientos. |
| 2-4 | Cuadro No. 2 | Actividad institucional: Institucional 1, 2, 3 y 4. | Clasificar equipamientos, servicios, cobertura, impacto y requisitos urbanísticos. |
| 5-7 | Cuadro No. 3 | Actividad comercial: Comercial 1, 2, 3 y 4. | Clasificar locales, oficinas, comercio, impacto y relación con actividades compatibles o prohibidas. |
| 8-9 | Cuadro No. 4 | Actividad industrial: Industrial 1, 2 y 3. | Validar industrias, bodegas, actividades productivas y compatibilidad con suelo urbano. |
| 10 | Cuadro No. 5 | Actividad turística Bocagrande y La Boquilla. | Aplicar en predios turísticos o zonas expresamente reguladas. |
| 11 | Cuadro No. 6 | Actividad portuaria: Portuario 1, 2, 3 y 4. | Evaluar predios con vocación portuaria, marítima, fluvial o logística portuaria. |
| 12 | Cuadro No. 7 | Actividad mixta: Mixto 1, 2, 3, 4 y 5. | Fundamental para Cartagena; aquí se cruza el uso principal, compatible, complementario, restringido y prohibido. |
| 13 | Cuadro No. 8 | Áreas de actividad en suelo rural suburbano, desarrollo turístico Zona Norte y Barú. | Usar en predios rurales suburbanos turísticos o residenciales campestres. |
| 14 | Cuadro No. 9 | Áreas de actividad en suelo rural: parcelaciones y actividad productora agroindustrial. | Usar en suelo rural, parcelaciones y actividades agroindustriales. |

Catálogo mínimo que debe quedar en base de datos cuando se construya el módulo:

- `documento_normativo`: ficha del documento fuente.
- `cuadro_normativo`: número de cuadro, nombre, suelo, actividad, página inicial/final, fuente.
- `categoria_uso`: código y nombre, por ejemplo Residencial A, Mixto 2, Comercial 3, Institucional 4.
- `regla_uso`: principal, compatible, complementario, restringido, prohibido.
- `parametro_urbanistico`: área libre, área mínima de lote, frente mínimo, altura, índice de construcción, aislamientos, estacionamientos, nivel de piso y condiciones especiales.
- `referencia_en_avaluo`: qué cuadro o regla se aplicó al predio, por quién, cuándo y con qué soporte.

Regla de seguridad técnica: el sistema puede sugerir el cruce entre actividad del inmueble y cuadro normativo, pero la conclusión final debe quedar confirmada por el analista. Si el resultado es restringido, condicionado, contradictorio o no disponible, debe exigir salvedad o soporte oficial de Planeación.

## Texto base para el entregable

Cuando haya fuente completa:

> De acuerdo con la consulta de normatividad urbana efectuada sobre el predio identificado con referencia catastral [referencia], ubicado en [dirección/barrio], y con fundamento en [fuentes], el inmueble se localiza en [clase/zona/tratamiento]. El cuadro normativo aplicable corresponde a [cuadro], en el cual la actividad [actividad] se clasifica como [principal/compatible/complementaria/restringida/prohibida]. Esta condición [permite/restringe/condiciona/no permite] el uso actual o pretendido, por lo cual para efectos valuatorios se adopta la siguiente conclusión: [conclusión].

Cuando falte fuente oficial:

> La consulta de normatividad urbana se realizó con los soportes disponibles al momento de la valuación. Dado que [MIDAS no arrojó información suficiente / no se aportó concepto oficial / existen capas o fuentes contradictorias], la conclusión urbanística se deja condicionada a la validación de la Secretaría de Planeación Distrital o autoridad competente. Esta salvedad se incorpora al análisis valuatorio para no asumir derechos urbanísticos no certificados.

## Fuentes oficiales iniciales

- Decreto 0977 de 2001, POT vigente de Cartagena: https://vuc.cartagena.gov.co/documentos/normatividad/DECRETO_0977_de_2001.pdf
- Documentos del POT vigente: https://seguimientopot.cartagena.gov.co/documentos-del-pot-vigente
- Actos administrativos de regulación del POT: https://seguimientopot.cartagena.gov.co/actos-administrativos-regulacion-del-pot
- Resoluciones POT / Planeación: https://seguimientopot.cartagena.gov.co/normativa/resoluciones
- Resolución 043 de 1994, Centro Histórico: https://seguimientopot.cartagena.gov.co/normativa/resolucion-043-1994-12118
- Resolución 0944 de 2020, determinantes ambientales CARDIQUE: https://seguimientopot.cartagena.gov.co/normativa/resolucion-0944-2020-11780
- Concepto de uso del suelo, trámite oficial: https://tramites.cartagena.gov.co/tramites-y-servicios/concepto-de-uso-del-suelo
- POT Cartagena, proceso nuevo POT 2026: https://pot.cartagena.gov.co/
- Nuevo POT radicado/concertación 2026: https://pot.cartagena.gov.co/noticias/nuevo-pot-cartagena-supera-verificacion-autoridades-ambientales-inicia-concertacion-549
- Ley 388 de 1997: https://www1.funcionpublica.gov.co/eva/gestornormativo/norma.php?i=339
- Decreto 1232 de 2020: https://www1.funcionpublica.gov.co/eva/gestornormativo/norma.php?i=142020

## Pendiente para programar

Cuando se reciban el POT, el cuadro de reglamentación de suelos o conceptos de Planeación, convertirlos en catálogos seleccionables y mantener el PDF/soporte asociado. La aplicación debe permitir actualizar o agregar actos normativos sin borrar capturas, conclusiones ni soportes anteriores.


