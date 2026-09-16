<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalPropertyNotes
{
    public static function all(): array
    {
        return [
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
                'casa_unifamiliar' => self::note('Vivienda independiente para un grupo familiar.', 'Casas en lote propio o unidad no sometida a división por apartamentos.', 'Permite separar terreno, construcción, patios y anexos cuando existan.'),
                'casa_bifamiliar' => self::note('Construcción residencial con dos unidades funcionales.', 'Casas divididas o proyectadas para dos hogares.', 'Conviene describir accesos, independencia y posible renta por unidad.'),
                'casa_campestre' => self::note('Vivienda rural o suburbana con componente recreativo.', 'Predios campestres donde pesan lote, entorno y construcción.', 'No mezclar mercado urbano con mercado rural o recreativo sin ajuste.'),
                'apartamento' => self::note('Unidad residencial privada dentro de edificio o conjunto.', 'Apartamentos sometidos usualmente a propiedad horizontal.', 'Revisar área privada, parqueaderos, depósitos y bienes comunes.'),
                'apartaestudio' => self::note('Unidad habitacional compacta.', 'Viviendas de menor área con un ambiente principal.', 'Comparar con unidades de escala y programa similar.'),
                'lote_urbano' => self::note('Suelo ubicado dentro de perímetro urbano o con tratamiento urbano.', 'Predios urbanos vacantes o con mejoras no predominantes.', 'Revisar POT, norma urbanística, servicios y edificabilidad.'),
                'lote_rural' => self::note('Suelo ubicado en suelo rural.', 'Predios rurales, agropecuarios, recreativos o suburbanos según norma.', 'Revisar uso del suelo, acceso, aguas, productividad y restricciones.'),
                'lote_comercial' => self::note('Suelo cuyo mayor potencial se relaciona con comercio o servicios.', 'Corredores, esquinas, zonas de actividad económica o lotes para local.', 'La norma de uso y la exposición comercial son determinantes.'),
                'lote_industrial' => self::note('Suelo apto o destinado a uso industrial/logístico.', 'Parques industriales, zonas logísticas o bodegaje futuro.', 'Revisar cargas urbanísticas, accesos pesados, redes y restricciones.'),
                'lote_institucional' => self::note('Suelo con vocación o destino institucional/dotacional.', 'Equipamientos, sedes, salud, educación o servicios colectivos.', 'La comparabilidad puede ser limitada; documentar norma aplicable.'),
                'local_calle' => self::note('Local con frente o acceso directo desde vía o corredor.', 'Comercio de calle, vitrinas, servicios o atención al público.', 'Frente, flujo peatonal, visibilidad y accesos afectan la comparación.'),
                'local_centro_comercial' => self::note('Local integrado a centro comercial o copropiedad.', 'Locales en malls, pasajes, galerías o centros empresariales.', 'Revisar ubicación interna, administración, tráfico y restricciones del reglamento.'),
                'oficina_privada' => self::note('Unidad para actividad administrativa o profesional.', 'Oficinas independientes o consultorios administrativos.', 'Comparar edificio, parqueaderos, administración, piso y servicios.'),
                'oficina_corporativa' => self::note('Espacio de oficina de mayor escala o especificación.', 'Plantas corporativas, oficinas empresariales o áreas integradas.', 'Puede requerir análisis por renta, adecuaciones y eficiencia del área.'),
                'bodega_urbana' => self::note('Espacio para almacenamiento o logística dentro de zona urbana.', 'Bodegas pequeñas o medianas con acceso vehicular.', 'Altura, pisos, muelles, patios y maniobra deben describirse.'),
                'bodega_industrial' => self::note('Inmueble productivo o logístico especializado.', 'Bodegas en corredor industrial, parque logístico o zona fabril.', 'Separar suelo, nave, oficinas, patios, redes y adecuaciones.'),
                'consultorio_medico' => self::note('Unidad profesional de salud o servicios especializados.', 'Consultorios médicos, odontológicos o similares.', 'Verificar uso permitido, habilitación, accesibilidad y condiciones del edificio.'),
                'edificio_residencial' => self::note('Edificación con varias unidades de vivienda.', 'Edificios de apartamentos, apartaestudios o renta residencial.', 'Puede valorarse por componentes, renta o potencial según encargo.'),
                'edificio_comercial' => self::note('Edificación orientada a comercio, servicios u oficinas.', 'Edificios de locales, oficinas, servicios o uso institucional.', 'Describir ocupación, rentas, áreas comunes y flexibilidad funcional.'),
                'edificio_mixto' => self::note('Edificación con dos o más usos relevantes.', 'Comercio en primer piso con vivienda, oficinas o servicios superiores.', 'Separar componentes para no mezclar mercados con comportamientos distintos.'),
                'finca_recreativa' => self::note('Predio rural con uso de descanso o recreación.', 'Fincas campestres, parcelaciones o casas de recreo.', 'El entorno, accesos, paisaje, aguas y construcciones influyen mucho.'),
                'finca_agropecuaria' => self::note('Predio rural con vocación productiva.', 'Fincas agrícolas, pecuarias o mixtas.', 'Diferenciar tierra, mejoras, cultivos, infraestructura y productividad.'),
                'hotel_urbano' => self::note('Inmueble urbano destinado a hospedaje.', 'Hoteles, hostales o alojamientos en zona urbana.', 'Distinguir inmueble, dotación y negocio en marcha cuando aplique.'),
                'hospedaje_rural' => self::note('Alojamiento turístico o rural.', 'Ecohoteles, glamping, hostales rurales o fincas hoteleras.', 'Revisar licencias, accesos, entorno, servicios y operación.'),
                'parqueadero_independiente' => self::note('Unidad o área destinada a estacionamiento.', 'Garajes privados, parqueaderos comerciales o unidades PH.', 'Revisar independencia jurídica, uso exclusivo y evidencia comparable.'),
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
        return ['what' => $what, 'when' => $when, 'basis' => $basis, 'report' => ''];
    }
}
