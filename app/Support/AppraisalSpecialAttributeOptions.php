<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalSpecialAttributeOptions
{
    public static function level(): array { return ['' => 'No verificado', 'bajo' => 'Bajo', 'medio' => 'Medio', 'alto' => 'Alto', 'superior' => 'Superior']; }
    public static function quality(): array { return ['' => 'No verificado', 'deficiente' => 'Deficiente', 'normal' => 'Normal', 'bueno' => 'Bueno', 'superior' => 'Superior']; }
    public static function condition(): array { return ['' => 'No verificado', 'malo' => 'Malo', 'regular' => 'Regular', 'bueno' => 'Bueno', 'excelente' => 'Excelente']; }
    public static function yesPartial(): array { return ['' => 'No verificado', 'no' => 'No', 'parcial' => 'Parcial', 'si' => 'Sí']; }
    public static function relevance(): array { return ['' => 'No verificado', 'ninguna' => 'Ninguna', 'menor' => 'Menor', 'relevante' => 'Relevante', 'superior' => 'Superior']; }
    public static function risk(): array { return ['' => 'No verificado', 'sin_evidencia' => 'Sin evidencia', 'bajo' => 'Bajo', 'medio' => 'Medio', 'alto' => 'Alto']; }
    public static function evidenceNeed(): array { return ['' => 'No verificado', 'no_requiere' => 'No requiere', 'requiere_foto' => 'Requiere foto', 'foto_cargada' => 'Foto cargada']; }
    public static function marketImpact(): array { return ['' => 'No definido', 'positivo' => 'Positivo', 'neutro' => 'Neutro', 'negativo' => 'Negativo', 'critico' => 'Crítico']; }
    public static function other(): array { return ['' => 'No aplica', 'registrar_en_observacion' => 'Registrar en observación', 'relevante' => 'Relevante']; }
    public static function location(): array { return ['' => 'No verificado', 'normal' => 'Normal', 'parque' => 'Frente a parque', 'mar' => 'Frente al mar', 'principal' => 'Sobre vía principal', 'comercial' => 'Zona comercial']; }
    public static function view(): array { return ['' => 'No verificado', 'interior' => 'Interior', 'calle' => 'Calle', 'paisajistica' => 'Paisajística', 'mar' => 'Mar', 'parque' => 'Parque', 'obstruida' => 'Obstruida']; }
    public static function privacy(): array { return ['' => 'No verificado', 'baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta']; }
    public static function amenity(): array { return ['' => 'No verificado', 'no_tiene' => 'No tiene', 'balcon' => 'Balcón', 'terraza' => 'Terraza', 'patio_jardin' => 'Patio / jardín', 'varios' => 'Varios']; }
    public static function finish(): array { return ['' => 'No verificado', 'basico' => 'Básico', 'medio' => 'Medio', 'bueno' => 'Bueno', 'superior' => 'Superior', 'lujo' => 'Lujo']; }
    public static function annex(): array { return ['' => 'No verificado', 'no_tiene' => 'No tiene', 'parqueadero' => 'Parqueadero', 'deposito' => 'Depósito', 'ambos' => 'Ambos']; }
    public static function comfortRisk(): array { return ['' => 'No verificado', 'sin_afectacion' => 'Sin afectación', 'moderado' => 'Moderado', 'alto' => 'Alto']; }
    public static function front(): array { return ['' => 'No verificado', 'reducido' => 'Reducido', 'normal' => 'Normal', 'amplio' => 'Amplio', 'superior' => 'Superior']; }
    public static function corner(): array { return ['' => 'No verificado', 'medianero' => 'Medianero', 'esquinero' => 'Esquinero', 'doble_frente' => 'Doble frente']; }
    public static function height(): array { return ['' => 'No verificado', 'baja' => 'Baja', 'convencional' => 'Convencional', 'alta' => 'Alta', 'doble_altura' => 'Doble altura']; }
    public static function compatibility(): array { return ['' => 'No verificado', 'baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta']; }
    public static function restriction(): array { return ['' => 'No verificado', 'sin_restriccion' => 'Sin restricción visible', 'leve' => 'Leve', 'relevante' => 'Relevante', 'critica' => 'Crítica']; }
    public static function floor(): array { return ['' => 'No verificado', 'bajo' => 'Piso bajo', 'medio' => 'Piso medio', 'alto' => 'Piso alto', 'premium' => 'Piso premium']; }
    public static function flexibility(): array { return ['' => 'No verificado', 'rigida' => 'Rígida', 'media' => 'Media', 'flexible' => 'Flexible']; }
    public static function division(): array { return ['' => 'No verificado', 'abierta' => 'Planta abierta', 'mixta' => 'Mixta', 'dividida' => 'Dividida']; }
    public static function floorStrength(): array { return ['' => 'No verificado', 'basica' => 'Básica', 'media' => 'Media', 'industrial' => 'Industrial reforzada']; }
    public static function shape(): array { return ['' => 'No verificado', 'irregular' => 'Irregular', 'regular' => 'Regular', 'optima' => 'Óptima']; }
    public static function topography(): array { return ['' => 'No verificado', 'plana' => 'Plana', 'ondulada' => 'Ondulada', 'pendiente' => 'Pendiente', 'mixta' => 'Mixta']; }
    public static function services(): array { return ['' => 'No verificado', 'sin_servicios' => 'Sin servicios', 'parcial' => 'Parcial', 'completa' => 'Completa']; }
    public static function potential(): array { return ['' => 'No verificado', 'bajo' => 'Bajo', 'medio' => 'Medio', 'alto' => 'Alto', 'superior' => 'Superior']; }
}
