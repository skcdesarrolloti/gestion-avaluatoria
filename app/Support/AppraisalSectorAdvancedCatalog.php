<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalSectorAdvancedCatalog
{
    public static function sections(): array
    {
        return [
            '01' => ['Identificación y localización', [
                ['microsector', 'Microsector', 'text'], ['fuente_base_delimitacion', 'Fuente base de delimitación', 'text'],
                ['fuente_base_satelital', 'Fuente base satelital', 'text'], ['observacion_localizacion', 'Observación de localización', 'textarea'],
            ]],
            '02' => ['Soporte cartográfico', [
                ['mapa_delimitacion_url', 'Enlace delimitación o georreferencia', 'text'],
                ['imagen_satelital_url', 'Enlace satelital o fuente visual', 'text'],
                ['medicion_source', 'Fuente de área/perímetro', 'text'], ['cartografia_status', 'Estado cartográfico', 'select', 'status'],
            ]],
            '03' => ['Servicios públicos', [
                ['fuente_servicios', 'Fuente de validación', 'text'], ['acueducto', 'Acueducto', 'select', 'yesno'],
                ['alcantarillado', 'Alcantarillado', 'select', 'yesno'], ['energia', 'Energía', 'select', 'yesno'],
                ['gas', 'Gas', 'select', 'yesno'], ['internet_operadores', 'Operadores de internet', 'multiselect', 'internet'],
                ['aseo_prestadores', 'Prestador de aseo', 'multiselect', 'aseo'], ['aguas_lluvias_detalle', 'Lectura de drenaje pluvial', 'textarea'],
            ]],
            '04' => ['Uso predominante', [
                ['descripcion_general_sector', 'Descripción general del sector', 'textarea'],
                ['uso_predominante', 'Uso predominante', 'select', 'uses'], ['usos_complementarios', 'Usos complementarios', 'text'],
                ['actividad_economica_predominante', 'Actividad económica predominante', 'select', 'activity'],
            ]],
            '05' => ['Normatividad urbanística', [
                ['clasificacion_suelo', 'Clasificación del suelo', 'text'], ['norma_base', 'Norma base', 'text'],
                ['acto_complementario', 'Acto complementario', 'text'], ['fuente_normativa', 'Fuente principal', 'text'],
                ['midas_lectura_manual', 'Lectura MIDAS/POT', 'textarea'], ['soporte_normativo_sector', 'Soporte normativo sectorial', 'textarea'],
            ]],
            '06' => ['Vías y señalización vial', [
                ['via_principal', 'Vía principal referida', 'text'], ['corredor_actividad', 'Corredor comercial o de servicios', 'select', 'yespartial'],
                ['tipo_corredor_actividad', 'Tipo de corredor', 'select', 'corridor'], ['vias_detalle', 'Caracterización de vías y señalización', 'textarea'],
                ['comentario_vias_senalizacion', 'Redacción consolidada vial', 'textarea'],
            ]],
            '07' => ['Amoblamiento urbano', [
                ['amoblamiento_seleccionado', 'Amoblamiento observado', 'multiselect', 'furniture'],
                ['equipamientos_seleccionados', 'Equipamientos presentes', 'multiselect', 'facilities'],
                ['comentario_amoblamiento', 'Comentario integrado del componente', 'textarea'],
            ]],
            '08' => ['Estratificación socioeconómica', [
                ['fuente_estratificacion', 'Fuente principal', 'text'], ['estrato_predominante', 'Estrato predominante', 'select', 'stratum'],
                ['homogeneidad_estrato', 'Comportamiento del sector', 'select', 'homogeneity'],
                ['porcentajes_estrato', 'Porcentajes por estrato o transición', 'textarea'], ['comentario_estratificacion', 'Síntesis de estratificación', 'textarea'],
            ]],
            '09' => ['Legalidad de la construcción', [
                ['fuente_legalidad', 'Fuente principal', 'text'], ['estado_legalidad_sector', 'Enfoque del análisis', 'select', 'legality'],
                ['observacion_legalidad', 'Observaciones generales', 'textarea'], ['comentario_legalidad', 'Síntesis de legalidad', 'textarea'],
            ]],
            '10' => ['Topografía', [
                ['observacion_topografia', 'Observación adicional del analista', 'textarea'], ['comentario_topografia', 'Síntesis de topografía', 'textarea'],
            ]],
            '11' => ['Transporte', [
                ['servicio_transporte_predominante', 'Servicio predominante', 'select', 'transport'],
                ['tipos_transporte_identificados', 'Tipos identificados', 'multiselect', 'transport_types'],
                ['detalle_rutas_transporte', 'Rutas o sistema identificado', 'textarea'], ['detalle_paraderos_transporte', 'Paraderos identificados', 'textarea'],
                ['comentario_transporte', 'Síntesis de transporte', 'textarea'],
            ]],
            '12' => ['Edificaciones importantes', [
                ['categorias_edificaciones', 'Categorías presentes', 'multiselect', 'building_categories'],
                ['edificaciones_ancla', 'Edificaciones ancla o hitos principales', 'textarea'], ['comentario_edificaciones', 'Síntesis de edificaciones importantes', 'textarea'],
            ]],
            '13' => ['Externalidades', [
                ['externalidades_positivas', 'Externalidades positivas', 'multiselect', 'positive_externalities'],
                ['externalidades_negativas', 'Externalidades negativas', 'multiselect', 'negative_externalities'],
                ['observacion_externalidades', 'Registro puntual de externalidades', 'textarea'], ['comentario_externalidades', 'Síntesis de externalidades', 'textarea'],
            ]],
            '14' => ['Soporte gráfico', [
                ['anexos_normativos', 'Anexos normativos y enlaces oficiales', 'textarea'],
                ['soportes_fotograficos_plan', 'Registro fotográfico esperado por visita', 'textarea'],
                ['observacion_soporte_grafico', 'Observación sobre soporte gráfico', 'textarea'],
            ]],
            '15' => ['Conclusión sectorial', [
                ['aptitud_sector_avaluo', 'Aptitud del sector para avalúos', 'select', 'aptitude'],
                ['dinamica_sectorial', 'Dinámica sectorial predominante', 'text'], ['fortalezas_sector', 'Fortalezas relevantes', 'textarea'],
                ['condicionantes_sector', 'Condicionantes o alertas', 'textarea'], ['comentario_conclusion_sectorial', 'Conclusión sectorial integrada', 'textarea'],
            ]],
            '16' => ['Consideraciones generales', [
                ['literal_a_localizacion', 'A. Localización', 'textarea'], ['literal_b_vecindario', 'B. Vecindario', 'textarea'],
                ['literal_c_accesibilidad', 'C. Accesibilidad', 'textarea'], ['literal_d_actividad_constructora', 'D. Actividad constructora', 'textarea'],
                ['literal_e_amenazas', 'E. Amenazas o afectaciones', 'textarea'], ['literal_g_servicios', 'G. Disponibilidad de servicios', 'textarea'],
                ['literal_h_uso_suelo', 'H. Uso del suelo', 'textarea'],
            ]],
        ];
    }

    public static function sourceKeys(string $code): array
    {
        return [
            '01' => ['barrios_cartagena', 'geoportal_catastro', 'campo_analista'],
            '02' => ['geoportal_catastro', 'barrios_cartagena', 'imagenes_apoyo'],
            '03' => ['planeacion_cartagena', 'campo_analista'],
            '04' => ['campo_analista', 'investigacion_mercado'],
            '05' => ['midas_normatividad', 'pot_usos', 'planeacion_cartagena', 'nts_aplicables'],
            '06' => ['movilidad_infraestructura', 'transcaribe', 'campo_analista'],
            '07' => ['planeacion_cartagena', 'campo_analista'],
            '08' => ['planeacion_cartagena', 'campo_analista'],
            '09' => ['ipcc_pemp', 'planeacion_cartagena', 'campo_analista'],
            '10' => ['gestion_riesgo', 'epa_cartagena', 'cardique', 'dimar_cioh'],
            '11' => ['transcaribe', 'movilidad_infraestructura', 'campo_analista'],
            '12' => ['imagenes_apoyo', 'base_interna_skc', 'campo_analista'],
            '13' => ['epa_cartagena', 'gestion_riesgo', 'investigacion_mercado', 'campo_analista'],
            '14' => ['registro_fotografico', 'imagenes_apoyo', 'campo_analista'],
            '15' => ['investigacion_mercado', 'campo_analista'],
            '16' => ['campo_analista', 'base_interna_skc'],
        ][$code] ?? ['campo_analista'];
    }

    public static function photoSupports(string $code): array
    {
        return [
            '02' => ['Figura 1 · mapa delimitado', 'Figura 2 · imagen satelital'],
            '06' => ['Figura 3 · mapa vial'],
            '14' => ['Figura 4 · registro de campo'],
        ][$code] ?? [];
    }

    public static function helps(): array
    {
        return [
            'microsector' => 'Escribe la zona precisa que estás analizando dentro del barrio. Ejemplo: residencial y servicios aeroportuarios.',
            'fuente_base_delimitacion' => 'Coloca de dónde sale el límite del barrio: Datos Abiertos, Geoportal, POT/MIDAS, plano oficial, Google Maps provisional o visita de campo.',
            'fuente_base_satelital' => 'Pega el enlace o identifica la imagen usada para mirar el barrio desde arriba: Google Maps, Google Earth, geoportal o captura satelital.',
            'observacion_localizacion' => 'Redacta en lenguaje de informe: dónde queda el barrio, qué lo rodea y qué falta confirmar en campo o cartografía.',
            'mapa_delimitacion_url' => 'Pega el enlace al mapa que permita abrir o revisar el límite usado para la caracterización.',
            'imagen_satelital_url' => 'Pega el enlace o describe la imagen satelital que soporta la lectura espacial.',
            'medicion_source' => 'Indica quién da el área o perímetro. Si no hay dato oficial, escribe medición manual pendiente de validar.',
            'fuente_servicios' => 'Indica si la información viene de observación de campo, empresa de servicios, ficha interna o consulta pública.',
            'descripcion_general_sector' => 'Describe el carácter del barrio: residencial, comercial, mixto, turístico, institucional o de transición.',
            'fuente_normativa' => 'Indica la fuente normativa consultada: POT, MIDAS, Secretaría de Planeación, norma especial o pendiente de consulta.',
            'midas_lectura_manual' => 'Resume lo que encontraste en MIDAS/POT: tratamiento, uso permitido, restricciones o pendientes.',
            'vias_detalle' => 'Describe accesos, estado vial, señalización, jerarquía de vías y facilidad de llegada al inmueble.',
            'comentario_vias_senalizacion' => 'Convierte la revisión vial en un párrafo técnico listo para el informe.',
            'fuente_estratificacion' => 'Indica de dónde sale el estrato: recibo, consulta pública, visita, ficha del inmueble o dato pendiente.',
            'observacion_externalidades' => 'Registra factores externos que suben o bajan valor: parques, comercio, ruido, tráfico, riesgo o deterioro.',
            'soportes_fotograficos_plan' => 'Define qué evidencias vas a subir: mapa, satelital, vías, entorno inmediato, equipamientos y externalidades.',
            'comentario_conclusion_sectorial' => 'Cierra con criterio profesional: cómo el sector incide en el valor y qué debe validar el analista.',
        ];
    }

    public static function options(string $key): array
    {
        return [
            'yesno' => ['SI' => 'Sí', 'NO' => 'No', 'PENDIENTE' => 'Pendiente de validar'],
            'yespartial' => ['SI' => 'Sí', 'PARCIAL' => 'Parcial', 'NO' => 'No', 'PENDIENTE' => 'Pendiente de validar'],
            'status' => ['MANUAL' => 'Manual', 'AUTOMATICO' => 'Automático', 'VALIDADO' => 'Validado', 'PENDIENTE' => 'Pendiente'],
            'uses' => ['Residencial' => 'Residencial', 'Comercial' => 'Comercial', 'Mixto' => 'Mixto', 'Industrial' => 'Industrial', 'Institucional' => 'Institucional', 'Turístico' => 'Turístico'],
            'activity' => ['Residencial' => 'Residencial', 'Comercial' => 'Comercial', 'Mixta' => 'Mixta', 'Industrial' => 'Industrial', 'Servicios' => 'Servicios', 'Turística' => 'Turística'],
            'corridor' => ['Comercial' => 'Comercial', 'Mixto' => 'Mixto', 'Servicios' => 'Servicios', 'Institucional' => 'Institucional', 'Turístico' => 'Turístico'],
            'internet' => ['Claro' => 'Claro', 'Movistar' => 'Movistar', 'Tigo' => 'Tigo', 'DirecTV' => 'DirecTV', 'ETB' => 'ETB', 'Otros' => 'Otros', 'No verificado' => 'No verificado'],
            'aseo' => ['Pacaribe' => 'Pacaribe', 'Veolia' => 'Veolia', 'No verificado' => 'No verificado'],
            'furniture' => ['Parque' => 'Parque', 'Parque infantil' => 'Parque infantil', 'Cancha múltiple' => 'Cancha múltiple', 'Cicloruta' => 'Cicloruta', 'Paradero de transporte' => 'Paradero de transporte', 'Mobiliario urbano' => 'Mobiliario urbano', 'Zona verde' => 'Zona verde', 'Iluminación peatonal' => 'Iluminación peatonal'],
            'facilities' => ['Educativo' => 'Educativo', 'Sanitario' => 'Sanitario', 'Institucional' => 'Institucional', 'Religioso' => 'Religioso', 'Financiero' => 'Financiero', 'Recreativo' => 'Recreativo', 'Deportivo' => 'Deportivo', 'Turístico' => 'Turístico'],
            'stratum' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', 'Mixto' => 'Mixto', 'No aplica' => 'No aplica'],
            'homogeneity' => ['Homogéneo' => 'Homogéneo', 'Predominio con transición' => 'Predominio con transición', 'Mixto' => 'Mixto', 'Pendiente' => 'Pendiente'],
            'legality' => ['No verificable a escala sectorial' => 'No verificable a escala sectorial', 'Consolidado sin verificación individual' => 'Consolidado sin verificación individual', 'Pendiente de validar' => 'Pendiente de validar'],
            'transport' => ['Transporte público colectivo' => 'Transporte público colectivo', 'Transporte masivo y colectivo' => 'Transporte masivo y colectivo', 'Taxi y rutas complementarias' => 'Taxi y rutas complementarias', 'Pendiente de validar' => 'Pendiente de validar'],
            'transport_types' => ['Transcaribe troncal' => 'Transcaribe troncal', 'Transcaribe alimentador' => 'Transcaribe alimentador', 'Bus urbano' => 'Bus urbano', 'Taxi' => 'Taxi', 'Mototaxi' => 'Mototaxi', 'Bicicleta / micromovilidad' => 'Bicicleta / micromovilidad', 'Peatonal' => 'Peatonal'],
            'building_categories' => ['Salud' => 'Salud', 'Educación' => 'Educación', 'Comercio ancla' => 'Comercio ancla', 'Institucional' => 'Institucional', 'Recreativo / deportivo' => 'Recreativo / deportivo', 'Religioso' => 'Religioso', 'Transporte' => 'Transporte', 'Servicios' => 'Servicios'],
            'positive_externalities' => ['Cercanía a parques' => 'Cercanía a parques', 'Buena conectividad urbana' => 'Buena conectividad urbana', 'Proximidad a equipamientos' => 'Proximidad a equipamientos', 'Comercio y servicios consolidados' => 'Comercio y servicios consolidados', 'Entorno residencial consolidado' => 'Entorno residencial consolidado'],
            'negative_externalities' => ['Ruido alto' => 'Ruido alto', 'Congestión vehicular' => 'Congestión vehicular', 'Contaminación visual' => 'Contaminación visual', 'Inseguridad percibida' => 'Inseguridad percibida', 'Riesgo de inundación' => 'Riesgo de inundación', 'Tráfico pesado' => 'Tráfico pesado'],
            'aptitude' => ['Favorable con validación puntual' => 'Favorable con validación puntual', 'Favorable' => 'Favorable', 'Condicionado' => 'Condicionado', 'Requiere verificación adicional' => 'Requiere verificación adicional'],
        ][$key] ?? [];
    }
}
