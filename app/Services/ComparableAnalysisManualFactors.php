<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class ComparableAnalysisManualFactors
{
    public static function normalize(string $value): string
    {
        if ($value === '') return '';
        $data = json_decode($value, true, 5);
        if (!is_array($data) || !is_object(json_decode($value)) || count($data) > 160)
            throw new HttpException(422, 'Datos manuales de factores inválidos.');
        $out = [];
        foreach ($data as $key => $entry) {
            if (!is_string($key) || mb_strlen($key) > 180 || !preg_match('/^(?:published:.+|bathrooms|parking_spaces|bedrooms|floor_level|admin_fee)$/uD', $key) || !is_array($entry))
                throw new HttpException(422, 'Factor manual inválido.');
            foreach (['label'=>180, 'value'=>500, 'source'=>1600] as $field => $limit) {
                if (!is_string($entry[$field] ?? '') || mb_strlen($entry[$field] ?? '') > $limit)
                    throw new HttpException(422, "Factor manual: formato o longitud inválida en $field.");
                $out[$key][$field] = trim($entry[$field] ?? '');
            }
            if ($out[$key]['label'] === '') throw new HttpException(422, 'El factor manual necesita una etiqueta.');
        }
        return json_encode((object) $out, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
