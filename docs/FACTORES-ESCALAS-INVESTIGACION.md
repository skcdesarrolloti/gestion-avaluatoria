# Factores y clasificación para investigar · 2026-10-04

Configuración → Catálogo de factores conserva el selector por tipo y añade búsqueda
por nombre. Insumos → Plan de investigación muestra los mismos atributos; la
calificación del sujeto y del comparable se registra junto al factor, con soporte.
Sólo se prepara información. No hay regresión, correlación ni ajustes de precio.

## Qué se clasifica

| Dato | Registro | Interpretación |
| --- | --- | --- |
| Cantidad o medida | Baños, años, altura, frente, kW, carga admisible | Dato original; mayor número es más cantidad, no necesariamente mejor |
| Presencia | 0 = No; 1 = Sí | Siempre misma pauta; ausencia debe comprobarse |
| Planta eléctrica | 0 = No; 1 = Parcial; 2 = Total | Cobertura; Sí sin cobertura detallada queda pendiente |
| Acabados terminados | 0 Básico/económico; 1 Medio; 2 Bueno; 3 Alto; 4 Superior/lujo | Pauta cualitativa fija, justificar materiales y ejecución; no es un peso económico |
| Vista, acceso, servicio, relieve | Clases sin orden impuesto | No se codifican arbitrariamente como mejor/peor |
| Área | Base compatible del alcance | Precio / área = COP/m²; fuera de candidatos y calificaciones |
| Destinación | Filtro de contexto | No es atributo candidato |

Desconocido, no publicado o no investigado permanece pendiente, nunca cero.
Mayor pendiente, edad, restricción o humedad no significa mayor valor. El modelo
posterior debe estudiar dirección, forma y suficiencia; no se fija el signo esperado
del precio desde el código. Un ordinal no acredita intervalos iguales entre niveles.

Tipo de acceso conserva sus clases históricas. Acceso vehicular, posibilidad de
cargue y restricción se investigan como condiciones independientes. Vista conserva
las etiquetas anteriores; se separan ubicación esquinera y vista paisajística.
El campo anterior Acabados también conserva Obra gris: ejecución incompleta no
recibe un nivel de calidad de acabado terminado. Evitar elegir simultáneamente
dos representaciones redundantes de un mismo atributo; se revisará en Análisis.

## Complementos por tipo

| Tipo | Atributos complementarios destacados |
| --- | --- |
| Oficina / consultorio | Climatización, vigilancia, ruta accesible, paisaje, esquina, maniobra y cobertura del parqueo, calidad de acabados |
| Apartamento | Balcón, terraza, piscina, gimnasio, climatización, vigilancia, accesibilidad, paisaje, esquina y características del parqueo |
| Casa | Dotación residencial, acceso vehicular y restricciones |
| Local | Frente, vitrina, mezanine, altura, acceso de cargue, climatización y acabados |
| Bodega | Muelles, accesos, frente, fondo, potencia, carga admisible del piso, mezanine y dotación |
| Lote | Frente, fondo, relieve, pendiente, esquina, acceso y servicios operativos |
| Finca | Atributos del terreno, agua y energía verificadas, riego, terraza, paisaje y dotación de las construcciones |
| Edificio | Unidades interiores, muelles, acceso, climatización y parqueo |
| Hotel | Capacidad de huéspedes y dotación; no confundir unidades interiores con habitaciones |
| Parqueadero | Cobertura, bloqueo por otras celdas, restricciones, frente, fondo y altura |
| Depósito | Altura, humedad, seguridad y accesibilidad |

Es un catálogo de candidatos ampliado, no una afirmación de que estén todos los
atributos posibles ni de que todos sean pertinentes a cualquier inmueble del tipo.
No obliga a obtenerlos todos ni a incluirlos todos en un modelo. Permanecen máximo
cuatro candidatos y la referencia configurable de diez inmuebles por factor; esa
meta no es una obligación legal ni garantía de suficiencia estadística.

## Evidencia por portal

ResearchFactorReference conserva el cruce exacto tipo/portal documentado y agrega
patrones específicos y fuentes complementarias en ResearchFactorSources. Un enlace
demuestra publicación de un atributo en una ficha o descripción; no acredita un
campo obligatorio del formulario privado ni dato presente en cada anuncio. Los
atributos técnicos o no documentados muestran investigación manual. Esta entrega
no amplía los lectores automáticos ni crea valores ficticios de captura.

Fuentes primarias consultadas el 4 de octubre de 2026:

- [Ciencuadras: publicación, categorías del paso público](https://www.ciencuadras.com/publicacion-inmuebles/publicar). Sólo acredita los tipos, no los campos de pasos posteriores.
- [FincaRaíz: apartamentos con ascensor](https://www.fincaraiz.com.co/venta/apartamentos/con-ascensor). Descripciones de balcón, climatización y paisaje.
- [FincaRaíz: apartamentos usados en Bucaramanga](https://www.fincaraiz.com.co/venta/apartamentos/bucaramanga/santander/usados). Dotación común y parqueo.
- [FincaRaíz: bodegas](https://www.fincaraiz.com.co/venta/bodegas?addeletedid=11103858). Frente/fondo, accesos, muelles y mezanine; alturas y áreas de distinta base.
- [FincaRaíz: locales](https://www.fincaraiz.com.co/venta/locales). Vitrinas anunciadas.
- [FincaRaíz: lotes en Tolima](https://www.fincaraiz.com.co/venta/lotes/tolima). Esquina y relieve; red cercana no acredita servicio operativo.
- [FincaRaíz: fincas en Caldas](https://www.fincaraiz.com.co/venta/fincas/caldas). Riego y terraza en descripciones.
- [FincaRaíz: edificios con cochera](https://www.fincaraiz.com.co/venta/edificios/bogota/bogota-dc/con-cochera). Parqueo cubierto, vigilancia, balcón y paisaje.
- [Investigación original sobre variables indicadoras](https://arxiv.org/abs/1511.05728). Respalda la separación entre clases y codificación estadística posterior; no determina jerarquías de precio inmobiliario.

Properati bloqueó la apertura directa de una consulta adicional (403). No se
certifican nuevos formularios de ese portal a partir de esa respuesta; quedan sus
referencias públicas documentadas previamente. Las fichas/listados cambian y las
fuentes se conservan con el alcance realmente observado.

## Conservación y validación

Servidor rechaza convertir clases en ordinales o invertir niveles fijos. Una escala
guardada anterior se conserva y se marca por revisar si viola la pauta actual;
no produce códigos vigentes ni conteos de datos listos. El analista corrige el
catálogo y adopta expresamente la escala en el recorrido; conserva los soportes.
No hay recodificación silenciosa, borrado de muestras ni modificación de Análisis.

ResearchPlanEvidence conserva la huella histórica de los atributos originales para
que ampliar el catálogo no invalide calificaciones intactas. Las calificaciones
nuevas usan huellas por factor y fuente; cambiar ese dato exige volver a calificar,
sin invalidar automáticamente atributos distintos. Persistencia, propietario,
CSRF y versión existentes se mantienen. No cambia el esquema de base de datos.
