<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalSpecialAttributeCatalog
{
    public static function groups(): array
    {
        return [
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
                'amenidades' => ['Amenidades privadas', 'Elementos de disfrute o servicio que diferencian la unidad.',
                    ['' => 'No verificado', 'basicas' => 'Básicas', 'varias' => 'Varias', 'premium' => 'Premium']],
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
        ];
    }

    public static function flatKeys(): array
    {
        $keys = [];
        foreach (self::groups() as $group) $keys = array_merge($keys, array_keys($group[1]));
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
        ];
    }
}
