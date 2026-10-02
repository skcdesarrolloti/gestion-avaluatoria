<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
final class ComparableCaptureDetail
{
    public static function fields(): array
    {
        return [
            'component_key' => ['Componente de la muestra', 'text', 'internal'],
            'area_basis' => ['Qué área publica la fuente', 'text', 'shared'],
            'private_built_m2' => ['Área privada construida (m²)', 'number', 'ph'],
            'private_free_m2' => ['Área privada libre (m²)', 'number', 'ph'],
            'ph_units_detail' => ['Garajes, depósitos y otras unidades: áreas y naturaleza jurídica', 'text', 'ph'],
            'land_m2' => ['Área del terreno (m²)', 'number', 'nph'],
            'built_m2' => ['Área de construcción (m²)', 'number', 'nph'],
            'annexes_detail' => ['Anexos constructivos: tipo y áreas', 'text', 'nph'],
            'crops_detail' => ['Cultivos permanentes: tipo, área y unidad, si aplica', 'text', 'nph'],
            'areas_source' => ['Fuente y salvedades de las áreas', 'text', 'shared'],
            'location_source' => ['Fuente de las coordenadas y fecha', 'text', 'map'],
            'evidence_detail' => ['Evidencia del aviso: referencia y contenido conservado', 'text', 'map'],
            'verification_detail' => ['Corroboración: responsable, fecha y resultado', 'text', 'map'],
        ] + ComparablePhCapture::fields();
    }
    public static function normalize(array $row): array
    {
        $out = [];
        foreach (self::fields() as $key => [$label, $type]) {
            $value = $row[$key] ?? '';
            if (!is_scalar($value) && $value !== null) throw new HttpException(422, "Formato inválido: $label.");
            $value = trim((string) $value);
            if (mb_strlen($value) > 1600) throw new HttpException(422, "$label: máximo 1600 caracteres.");
            if ($type === 'choice' && !array_key_exists($value, ComparablePhCapture::options($key)))
                throw new HttpException(422, "$label: selecciona una opción válida.");
            if ($type === 'integer' && $value !== '' && (!preg_match('/^\d{1,3}$/D', $value)))
                throw new HttpException(422, "$label: usa una cantidad entera entre 0 y 999.");
            if ($type === 'number' && $value !== '') {
                $value = str_replace(',', '.', $value);
                if (!preg_match('/^\d+(\.\d{1,4})?$/D', $value) || (float) $value > 100000000000)
                    throw new HttpException(422, "$label: usa un número positivo, sin separadores de miles, y hasta cuatro decimales.");
            }
            $out[$key] = $value;
        }
        $out['ph_special'] = ($row['ph_special'] ?? '') === 'condominio' ? 'condominio' : '';
        return $out;
    }
}
