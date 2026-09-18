<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalSpecialAttributeCatalog
{
    public static function groups(string $propertyType = ''): array
    {
        $groups = self::allGroups();
        $selected = ['vista', 'confort', 'urbanistica', 'suelo', 'constructivos', 'diferenciales'];
        $typeGroups = match ($propertyType) {
            'casa', 'apartamento', 'hotel' => ['residencial'],
            'local' => ['comercial'],
            'oficina', 'consultorio' => ['corporativo'],
            'bodega' => ['industrial'],
            'lote', 'finca' => ['lote'],
            'edificio' => ['residencial', 'comercial', 'corporativo'],
            'parqueadero' => ['parqueadero'],
            default => [],
        };
        return array_intersect_key($groups, array_flip(array_merge($selected, $typeGroups)));
    }

    public static function allGroups(): array
    {
        return [
            'vista' => ['Vista y relación visual', [
                'vista_tipo' => ['Tipo de vista', 'Diferencia visual que el mercado puede reconocer positiva o negativamente.',
                    ['' => 'No verificado', 'interior' => 'Interior', 'calle' => 'Exterior a calle', 'paisajistica' => 'Exterior paisajística', 'parque' => 'Zona verde / parque', 'agua' => 'Mar, río, bahía o lago', 'obstruida' => 'Obstruida', 'negativa' => 'Negativa']],
                'privacidad_visual' => ['Privacidad visual', 'Nivel de exposición frente a vecinos, vías o zonas comunes.',
                    ['' => 'No verificado', 'baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta']],
            ]],
            'confort' => ['Iluminación, ventilación y confort', [
                'iluminacion_natural' => ['Iluminación natural', 'Entrada de luz natural observable en espacios principales.',
                    ['' => 'No verificado', 'deficiente' => 'Deficiente', 'normal' => 'Normal', 'buena' => 'Buena', 'superior' => 'Superior']],
                'ventilacion' => ['Ventilación', 'Circulación de aire natural o mecánica suficiente para el uso.',
                    ['' => 'No verificado', 'deficiente' => 'Deficiente', 'normal' => 'Normal', 'cruzada' => 'Cruzada / superior']],
                'ruido_olores' => ['Ruido u olores', 'Afectaciones sensoriales que pueden reducir deseabilidad.',
                    ['' => 'No verificado', 'ninguno' => 'No relevantes', 'moderados' => 'Moderados', 'altos' => 'Altos']],
            ]],
            'urbanistica' => ['Condición urbanística', [
                'esquina' => ['Esquinero o medianero', 'Lee exposición comercial, accesibilidad y frente útil.',
                    ['' => 'No verificado', 'doble_frente' => 'Doble frente', 'esquinero' => 'Esquinero', 'medianero' => 'Medianero']],
                'servicios' => ['Disponibilidad de servicios', 'Verifica disponibilidad real, no solo promesa del sector.',
                    ['' => 'No verificado', 'completa' => 'Completa', 'parcial' => 'Parcial', 'limitada' => 'Limitada']],
                'licencia' => ['Licencia o factibilidad', 'Soporte urbanístico que puede cambiar la lectura de mercado.',
                    ['' => 'No verificado', 'no_tiene' => 'No tiene', 'factibilidad' => 'Factibilidad', 'tramite' => 'En trámite', 'aprobada' => 'Aprobada']],
                'via' => ['Frente sobre vía', 'Jerarquía vial que afecta visibilidad, acceso y comparabilidad.',
                    ['' => 'No verificado', 'arterial' => 'Vía arterial', 'colectora' => 'Vía colectora', 'local' => 'Vía local', 'restringida' => 'Acceso restringido']],
            ]],
            'suelo' => ['Condición física del suelo', [
                'cerramiento' => ['Cerramiento', 'Elemento de control físico y seguridad del predio.',
                    ['' => 'No verificado', 'si' => 'Sí', 'parcial' => 'Parcial', 'no' => 'No']],
                'inundacion' => ['Riesgo de inundación', 'Riesgo físico observable o documentado que afecta uso y deseabilidad.',
                    ['' => 'No verificado', 'bajo' => 'Bajo', 'medio' => 'Medio', 'alto' => 'Alto']],
                'mejoras' => ['Mejoras o adecuaciones', 'Rellenos, nivelación, placa, muros u obras útiles existentes.',
                    ['' => 'No verificado', 'ninguna' => 'Ninguna', 'menores' => 'Menores', 'relevantes' => 'Relevantes']],
                'remocion' => ['Amenaza por remoción en masa', 'Condición de amenaza que exige soporte técnico o cartográfico.',
                    ['' => 'No verificado', 'nula' => 'Nula', 'baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta']],
            ]],
            'diferenciales' => ['Diferenciales de mercado', [
                'ubicacion_especial' => ['Ubicación especial', 'Rasgo de localización que el mercado podría reconocer.',
                    ['' => 'No verificado', 'parque' => 'Frente a parque', 'mar' => 'Frente al mar', 'principal' => 'Sobre vía principal', 'turistica' => 'Zona turística', 'comercial' => 'Zona comercial']],
                'amenidades_privadas' => ['Amenidades privadas', 'Elementos privativos de disfrute o servicio que diferencian la unidad.',
                    ['' => 'No verificado', 'balcon' => 'Balcón', 'terraza' => 'Terraza', 'patio' => 'Patio / jardín privado', 'varias' => 'Varias', 'premium' => 'Premium']],
                'seguridad' => ['Seguridad especial', 'Control de acceso, vigilancia, cerramiento o seguridad complementaria.',
                    ['' => 'No verificado', 'basica' => 'Básica', 'controlada' => 'Controlada', 'alta' => 'Alta']],
                'exclusividad' => ['Exclusividad / prestigio', 'Reconocimiento o posicionamiento diferencial del sector o activo.',
                    ['' => 'No verificado', 'baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'muy_alta' => 'Muy alta']],
            ]],
            'constructivos' => ['Rasgos constructivos diferenciales', [
                'uso_especifico' => ['Uso específico', 'Uso observado de la unidad cuando no basta la categoría general.',
                    ['' => 'No verificado', 'residencial' => 'Residencial', 'comercial' => 'Comercial', 'industrial' => 'Industrial', 'servicios' => 'Servicios', 'mixto' => 'Mixto']],
                'altura_libre' => ['Altura libre', 'Altura funcional relevante para bodegas, locales o usos especiales.',
                    ['' => 'No verificado', 'convencional' => 'Convencional', 'alta' => 'Alta', 'doble_altura' => 'Doble altura']],
                'cubierta' => ['Tipo de cubierta', 'Cubierta visible o relevante para reposición y funcionalidad.',
                    ['' => 'No verificado', 'placa' => 'Placa', 'fibrocemento' => 'Fibrocemento', 'metalica' => 'Metálica', 'barro' => 'Barro', 'otra' => 'Otra']],
                'estructura' => ['Material estructura', 'Sistema estructural principal observado o documentado.',
                    ['' => 'No verificado', 'concreto' => 'Concreto', 'acero' => 'Acero', 'mamposteria' => 'Mampostería', 'madera' => 'Madera', 'mixta' => 'Mixta']],
            ]],
            'residencial' => ['Vivienda: atributos interiores', [
                'acabados_residenciales' => ['Acabados interiores', 'Calidad de cocina, baños, pisos, carpintería y detalles interiores.',
                    ['' => 'No verificado', 'basicos' => 'Básicos', 'buenos' => 'Buenos', 'superiores' => 'Superiores', 'lujo' => 'De lujo']],
                'distribucion_residencial' => ['Distribución funcional', 'Eficiencia, independencia y aprovechamiento de espacios.',
                    ['' => 'No verificado', 'deficiente' => 'Deficiente', 'normal' => 'Normal', 'eficiente' => 'Eficiente', 'superior' => 'Superior']],
            ]],
            'comercial' => ['Local: exposición comercial', [
                'vitrina_comercial' => ['Vitrina comercial', 'Visibilidad y frente útil para comercio.',
                    ['' => 'No verificado', 'baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'superior' => 'Superior']],
                'flujo_comercial' => ['Flujo peatonal / vehicular', 'Exposición a clientes potenciales.',
                    ['' => 'No verificado', 'bajo' => 'Bajo', 'medio' => 'Medio', 'alto' => 'Alto']],
                'cargue_local' => ['Cargue, descargue o parqueo', 'Facilidad de operación para abastecimiento o clientes.',
                    ['' => 'No verificado', 'no_tiene' => 'No tiene', 'limitado' => 'Limitado', 'adecuado' => 'Adecuado']],
            ]],
            'corporativo' => ['Oficina/consultorio: funcionalidad', [
                'imagen_corporativa' => ['Imagen corporativa', 'Presentación del inmueble para uso empresarial o profesional.',
                    ['' => 'No verificado', 'basica' => 'Básica', 'buena' => 'Buena', 'superior' => 'Superior']],
                'modularidad' => ['Modularidad', 'Capacidad de adaptar espacios a puestos, consultorios o salas.',
                    ['' => 'No verificado', 'rigida' => 'Rígida', 'media' => 'Media', 'flexible' => 'Flexible']],
                'redes_tecnicas' => ['Redes y soporte técnico', 'Cableado, climatización, conectividad o instalaciones especiales.',
                    ['' => 'No verificado', 'basicas' => 'Básicas', 'adecuadas' => 'Adecuadas', 'superiores' => 'Superiores']],
            ]],
            'industrial' => ['Bodega/industrial: operación', [
                'altura_libre_operativa' => ['Altura libre operativa', 'Altura útil para almacenamiento, estantería o procesos.',
                    ['' => 'No verificado', 'baja' => 'Baja', 'normal' => 'Normal', 'alta' => 'Alta', 'doble_altura' => 'Doble altura']],
                'piso_resistencia' => ['Piso y resistencia', 'Capacidad aparente del piso para carga o uso industrial.',
                    ['' => 'No verificado', 'basico' => 'Básico', 'adecuado' => 'Adecuado', 'industrial' => 'Industrial reforzado']],
                'maniobra_cargue' => ['Maniobra y cargue privado', 'Puertas, muelles, bahías, patio o acceso de vehículos de carga.',
                    ['' => 'No verificado', 'limitado' => 'Limitado', 'adecuado' => 'Adecuado', 'superior' => 'Superior']],
            ]],
            'lote' => ['Lote: potencial físico', [
                'forma_lote' => ['Forma del lote', 'Regularidad y aprovechamiento del terreno.',
                    ['' => 'No verificado', 'irregular' => 'Irregular', 'regular' => 'Regular', 'optima' => 'Óptima']],
                'frente_lote' => ['Frente y exposición', 'Relación de frente, fondo y visibilidad.',
                    ['' => 'No verificado', 'reducido' => 'Reducido', 'normal' => 'Normal', 'amplio' => 'Amplio']],
                'potencial_normativo' => ['Potencial normativo', 'Capacidad normativa o de desarrollo observable/documentada.',
                    ['' => 'No verificado', 'bajo' => 'Bajo', 'medio' => 'Medio', 'alto' => 'Alto']],
            ]],
            'parqueadero' => ['Parqueadero: funcionalidad', [
                'facilidad_maniobra' => ['Facilidad de maniobra', 'Acceso, giro y uso cómodo del cupo.',
                    ['' => 'No verificado', 'limitada' => 'Limitada', 'normal' => 'Normal', 'amplia' => 'Amplia']],
                'cobertura_parqueadero' => ['Cobertura', 'Condición cubierta o descubierta del parqueadero.',
                    ['' => 'No verificado', 'descubierto' => 'Descubierto', 'cubierto' => 'Cubierto']],
            ]],
        ];
    }

    public static function flatKeys(): array
    {
        $keys = [];
        foreach (self::allGroups() as $group) $keys = array_merge($keys, array_keys($group[1]));
        return $keys;
    }

    public static function selectOptions(): array
    {
        return [
            'state' => ['' => 'No verificado', 'bueno' => 'Bueno', 'regular' => 'Regular', 'malo' => 'Malo', 'no_aplica' => 'No aplica'],
            'impact' => ['' => 'No definido', 'positivo_alto' => 'Positivo alto', 'positivo_medio' => 'Positivo medio',
                'neutro' => 'Neutro', 'negativo_medio' => 'Negativo medio', 'negativo_alto' => 'Negativo alto'],
            'evidence' => ['' => 'No verificado', 'foto' => 'Foto', 'visita' => 'Visita', 'documento' => 'Documento',
                'anuncio' => 'Anuncio', 'declaracion' => 'Declaración'],
            'rating' => ['' => 'Sin calificar', '1' => '1 Muy desfavorable', '2' => '2 Desfavorable',
                '3' => '3 Normal', '4' => '4 Favorable', '5' => '5 Muy favorable'],
            'weight' => ['' => 'Sin peso', '1' => 'Bajo', '2' => 'Medio', '3' => 'Alto'],
        ];
    }
}
