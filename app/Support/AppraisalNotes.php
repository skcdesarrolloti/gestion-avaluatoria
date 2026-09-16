<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalNotes
{
    public static function all(): array
    {
        return [
            'tipo' => [
                'comercial' => self::note('Avalúo orientado al precio probable de mercado.', 'Compraventa, patrimonio, soporte judicial o garantía.', 'Base principal: valor de mercado; soporte NTS/IGAC.'),
                'posesion' => self::note('Valora una tenencia material sin dominio pleno consolidado.', 'Procesos de pertenencia, ocupaciones o controversias.', 'Exige advertir riesgo jurídico y no concluir como dominio.'),
                'mejoras' => self::note('Valora construcciones, adecuaciones o inversiones sobre un predio.', 'Cuando la mejora es separable del derecho sobre el suelo.', 'Debe separar terreno, construcción y soporte jurídico.'),
                'remate' => self::note('Soporta una actuación de venta forzada o subasta.', 'Procesos ejecutivos o liquidaciones con finalidad judicial.', 'Debe reforzar trazabilidad, fecha y supuestos.'),
                'negociacion' => self::note('Sirve como insumo para pactar precio o canon.', 'Acuerdos privados, ofertas o renegociaciones.', 'No equivale por sí solo a una orden judicial o contable.'),
                'conciliacion' => self::note('Apoya una solución acordada entre partes.', 'Audiencias, arreglos directos o controversias patrimoniales.', 'Debe explicar alcance y límites del acuerdo.'),
                'zona_franca' => self::note('Avalúa bienes bajo régimen o entorno especial.', 'Inmuebles o activos ubicados en zona franca.', 'Requiere revisar norma especial, uso y restricciones.'),
                'catastral' => self::note('Se relaciona con información o finalidad catastral.', 'Referencias administrativas y consistencia predial.', 'No debe confundirse con valor comercial ordinario.'),
                'seguro' => self::note('Busca base para cobertura, reposición o indemnización.', 'Pólizas, siniestros o administración de riesgo.', 'Normalmente exige costo de reposición y exclusiones claras.'),
                'hipotecario' => self::note('Soporta garantía crediticia sobre el bien o derecho.', 'Crédito, garantía real o análisis de riesgo.', 'Debe diferenciar valor comercial, realizable y restricciones.'),
                'interno' => self::note('Ordena análisis interno sin destinatario externo principal.', 'Control patrimonial, decisiones internas o escenarios.', 'Debe marcarse como uso interno y no informe final.'),
            ],
            'tipo_derecho' => [
                'dominio_pleno' => self::note('Derecho completo de propiedad sobre el bien.', 'Cuando el certificado y títulos respaldan propiedad plena.', 'Base civil e inmobiliaria; verificar gravámenes y limitaciones.'),
                'nuda_propiedad' => self::note('Propiedad separada del goce o usufructo.', 'Cuando el uso económico pertenece temporalmente a otro.', 'Se valora el derecho limitado, no el inmueble completo.'),
                'usufructo' => self::note('Derecho de uso y disfrute sobre bien ajeno.', 'Cuando existe usufructuario vigente.', 'Separar duración, rentas, cargas y extinción del derecho.'),
                'posesion' => self::note('Tenencia material con ánimo de señor y dueño.', 'Cuando no hay dominio pleno inscrito o hay controversia.', 'Requiere advertir riesgo, realizabilidad y prueba disponible.'),
                'mejoras' => self::note('Derecho económico sobre obras o inversiones.', 'Cuando la construcción pertenece a quien no domina el suelo.', 'Valorar solo lo reconocible y soportado.'),
                'derecho_fiduciario' => self::note('Derecho derivado de una fiducia o patrimonio autónomo.', 'Proyectos, encargos fiduciarios o derechos económicos.', 'Leer contrato fiduciario antes de concluir valor.'),
                'arrendatario' => self::note('Posición económica de quien ocupa por contrato.', 'Arrendamientos, derechos de uso o ventajas contractuales.', 'Conecta con NIIF 16 si el encargo es contable.'),
                'otro' => self::note('Derecho no encajado en categorías base.', 'Casos especiales o figuras contractuales particulares.', 'Debe documentarse con fuente jurídica específica.'),
            ],
            'tipo_negocio' => [
                'venta' => self::note('El análisis se orienta a precio de transferencia o compraventa.', 'Cuando el encargo busca estimar cuánto podría pagarse por el bien.', 'Usar evidencia de mercado comparable y no mezclar cánones de renta como precio directo.'),
                'arriendo' => self::note('El análisis se orienta a canon o ingreso periódico.', 'Cuando el encargo busca estimar renta, alquiler o explotación económica.', 'Requiere consistencia entre renta, vacancia, gastos y capitalización si se deriva valor.'),
            ],
            'destinacion' => [
                'residencial' => self::note('Uso principal habitacional.', 'Casas, apartamentos, vivienda campestre o unidades residenciales.', 'Afecta comparables, tipología IGAC y lectura de mercado.'),
                'comercial' => self::note('Uso orientado a venta de bienes o prestación de servicios.', 'Locales, oficinas comerciales, hospedaje o espacios de atención al público.', 'Revisar uso permitido, renta de mercado y condiciones de localización.'),
                'institucional' => self::note('Uso asociado a entidades, servicios colectivos o dotacionales.', 'Educación, salud, culto, servicios públicos o sedes institucionales.', 'Verificar norma urbana y restricciones de uso.'),
                'industrial' => self::note('Uso productivo, almacenamiento o transformación.', 'Bodegas, plantas, talleres o predios industriales.', 'Separar suelo, construcciones, infraestructura y posibles equipos.'),
                'dotacional' => self::note('Uso destinado a equipamientos o servicios de soporte urbano.', 'Equipamientos colectivos, recreación, servicios o infraestructura social.', 'Exige revisar POT, permisos y régimen específico.'),
                'lote_suelo' => self::note('Predomina el suelo sobre la construcción.', 'Lotes vacantes, suelo de expansión o predios con mejoras menores.', 'Define si aplica mercado de suelo, residual o reposición de anexos.'),
                'mixto' => self::note('Concurren dos o más usos relevantes.', 'Predios con vivienda y comercio, lote con anexos, o usos combinados.', 'Debe separar componentes para no promediar mercados incompatibles.'),
            ],
            'tipo_inmueble' => [
                'casa' => self::note('Unidad residencial normalmente asociada a lote y construcción.', 'Viviendas unifamiliares, bifamiliares o casas campestres.', 'Puede requerir separar terreno, construcción y anexos.'),
                'apartamento' => self::note('Unidad residencial en edificio o conjunto sometido usualmente a PH.', 'Apartamentos, apartaestudios o unidades habitacionales privadas.', 'Revisar área privada, coeficiente, parqueaderos y depósitos.'),
                'lote' => self::note('Predio donde el suelo es el componente principal.', 'Lotes urbanos, rurales, comerciales, industriales o institucionales.', 'Verificar norma de uso, edificabilidad, servicios y mejoras existentes.'),
                'local' => self::note('Unidad destinada a comercio o atención al público.', 'Locales en calle, centros comerciales o propiedad horizontal.', 'El frente, flujo, visibilidad y renta son variables críticas.'),
                'oficina' => self::note('Unidad para actividades administrativas o profesionales.', 'Oficinas privadas, corporativas o consultorios administrativos.', 'Revisar edificio, parqueaderos, administración y mercado de renta.'),
                'bodega' => self::note('Inmueble para almacenamiento, logística o actividad industrial liviana.', 'Bodegas urbanas, parques industriales o centros logísticos.', 'Altura, accesos, patios, pisos y redes pesan en la comparación.'),
                'consultorio' => self::note('Unidad profesional con uso especializado.', 'Consultorios médicos, odontológicos o servicios profesionales.', 'Verificar habilitación, uso permitido y características del edificio.'),
                'edificio' => self::note('Construcción con varias unidades o áreas funcionales.', 'Edificios residenciales, comerciales, servicios o mixtos.', 'Puede requerir valoración por componentes, rentas o potencial.'),
                'finca' => self::note('Predio rural con suelo, construcciones y posibles mejoras productivas.', 'Fincas recreativas, agropecuarias o rurales mixtas.', 'Separar tierra, construcciones, cultivos, aguas y anexos.'),
                'hotel' => self::note('Inmueble asociado a hospedaje y operación económica.', 'Hoteles, hostales, alojamientos rurales o turísticos.', 'Diferenciar inmueble de negocio en marcha cuando aplique.'),
                'parqueadero' => self::note('Unidad o área destinada a estacionamiento.', 'Parqueaderos privados, comunales de uso exclusivo o explotación comercial.', 'Revisar independencia jurídica, PH y evidencia comparable.'),
            ],
            'subtipo_funcional' => [
                'lote_urbano' => self::note('Suelo ubicado dentro de perímetro urbano o con tratamiento urbano.', 'Predios urbanos vacantes o con mejoras no predominantes.', 'Revisar POT, norma urbanística, servicios y edificabilidad.'),
                'lote_rural' => self::note('Suelo ubicado en suelo rural.', 'Predios rurales, agropecuarios, recreativos o suburbanos según norma.', 'Revisar uso del suelo, acceso, aguas, productividad y restricciones.'),
                'lote_comercial' => self::note('Suelo cuyo mayor potencial se relaciona con comercio o servicios.', 'Corredores, esquinas, zonas de actividad económica o lotes para local.', 'La norma de uso y la exposición comercial son determinantes.'),
                'lote_industrial' => self::note('Suelo apto o destinado a uso industrial/logístico.', 'Parques industriales, zonas logísticas o bodegaje futuro.', 'Revisar cargas urbanísticas, accesos pesados, redes y restricciones.'),
                'lote_institucional' => self::note('Suelo con vocación o destino institucional/dotacional.', 'Equipamientos, sedes, salud, educación o servicios colectivos.', 'La comparabilidad puede ser limitada; documentar norma aplicable.'),
            ],
            'finalidad' => [
                'judicial' => self::note('Dictamen con finalidad probatoria.', 'Juzgados, procesos ejecutivos, pertenencia o controversias.', 'Prima trazabilidad, replicabilidad y soporte documental.'),
                'extrajudicial' => self::note('Informe para uso fuera de proceso judicial.', 'Negociaciones, consultas o acuerdos privados.', 'Dejar claro destinatario y uso permitido.'),
                'interna' => self::note('Análisis para gestión de la entidad solicitante.', 'Control, planeación, auditoría interna o inventario.', 'No se presenta como dictamen para terceros.'),
                'patrimonial' => self::note('Soporta lectura del patrimonio o activos propios.', 'Inventarios, sucesiones, sociedades o decisiones familiares.', 'Separar valor de mercado de valores contables.'),
                'negociacion' => self::note('Orienta una decisión de compra, venta o arriendo.', 'Mesas de negociación y análisis de oportunidad.', 'La fecha y condiciones de mercado son críticas.'),
                'decision_junta' => self::note('Insumo para órgano directivo o societario.', 'Junta, comité, asamblea o aprobación interna.', 'Debe ser claro, comparable y trazable.'),
                'garantia' => self::note('Soporta respaldo económico de una obligación.', 'Crédito, cupo, fiducia o garantía real.', 'Revisar exigencias del acreedor y realizabilidad.'),
                'remate' => self::note('Apoya precio base o referencia de subasta.', 'Procesos de ejecución, liquidación o venta forzada.', 'Debe evidenciar restricciones y condición de venta.'),
            ],
            'base_valor' => [
                'mercado' => self::note('Precio probable en mercado abierto entre partes informadas.', 'Compraventa, garantía, conciliación o patrimonio.', 'Respaldo NTS/IGAC e IVS cuando aplique.'),
                'razonable' => self::note('Medición contable basada en participantes de mercado.', 'Estados financieros, auditoría o revelación.', 'Soporte principal NIIF 13.'),
                'depreciable' => self::note('Base para costo, vida útil, deterioro o reposición.', 'Activos construidos, maquinaria o seguros.', 'Conecta con NIC 16, NIC 36 y método de costo.'),
                'renta' => self::note('Valor derivado de ingresos o cánones esperados.', 'Arriendos, inversión y explotación económica.', 'No mezclar rentas del negocio con renta del inmueble.'),
                'catastral' => self::note('Valor usado para fines catastrales o administrativos.', 'Catastro, impuestos o consistencia oficial.', 'No reemplaza automáticamente el valor comercial.'),
                'residual' => self::note('Valor estimado a partir de potencial de desarrollo.', 'Suelo, proyectos, renovación o expansión.', 'Debe soportar usos, costos, tiempos y norma urbana.'),
            ],
            'aplica_niif' => [
                'no' => self::note('El encargo no se formula bajo medición contable.', 'Avalúos comerciales, judiciales o internos no financieros.', 'Usar NTS, marco jurídico e IVS según alcance.'),
                'si' => self::note('El encargo requiere lenguaje y soporte contable NIIF.', 'Estados financieros, auditoría, deterioro o valor razonable.', 'Revisar NIIF 13, NIC 16, NIC 36, NIC 38, NIC 40 o NIIF 16.'),
            ],
            'regimen_ph' => [
                'no_aplica' => self::note('La pregunta no corresponde a la tipología elegida.', 'Bienes no inmobiliarios o activos sin PH.', 'No forzar área privada ni coeficientes.'),
                'si' => self::note('El inmueble está sometido a propiedad horizontal.', 'Apartamentos, locales, oficinas, parqueaderos o unidades PH.', 'Ley 675: diferenciar bien privado, comunes y coeficiente.'),
                'no' => self::note('No existe sometimiento a propiedad horizontal.', 'Casas, lotes o inmuebles independientes.', 'Separar terreno y construcción cuando corresponda.'),
            ],
            'estructura_metodo' => [
                'area_privada' => self::note('La unidad de valoración es el área privada.', 'Unidades sometidas a PH.', 'Evita duplicar bienes comunes no individualizados.'),
                'lote_construccion' => self::note('Se analiza terreno y construcción por separado.', 'Inmuebles no PH con edificación.', 'Útil para costo, depreciación y explicación del valor.'),
                'solo_terreno' => self::note('La unidad de valoración es únicamente el suelo.', 'Lotes, suelo bruto o predios sin construcción relevante.', 'No cargar construcción inexistente o no reconocible.'),
                'por_definir' => self::note('La estructura depende de datos pendientes.', 'Cuando faltan PH, tipología, títulos o inspección.', 'Debe resolverse antes de cerrar el informe.'),
            ],
        ];
    }

    private static function note(string $what, string $when, string $basis): array
    {
        return ['what' => $what, 'when' => $when, 'basis' => $basis];
    }
}