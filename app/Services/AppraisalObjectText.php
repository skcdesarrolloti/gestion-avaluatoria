<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalCatalog;

final class AppraisalObjectText
{
    public static function build(array $record): string
    {
        $fields = AppraisalCatalog::selectFields();
        $basis = $fields['base_valor'][4][$record['base_valor'] ?? ''] ?? '';
        $purpose = $fields['finalidad'][4][$record['finalidad'] ?? ''] ?? '';
        $type = $fields['tipo_inmueble'][4][$record['tipo_inmueble'] ?? ''] ?? 'inmueble';
        $address = trim((string) ($record['direccion'] ?? ''));
        $municipality = trim((string) ($record['municipio'] ?? ''));
        $location = implode(', ', array_filter([$address, $municipality]));
        if ($basis === '' || $purpose === '') return 'El objeto del avalÃºo estÃ¡ pendiente de definiciÃ³n: falta precisar la base de valor y/o la finalidad del encargo.';
        return 'El objeto del presente avalÃºo es estimar el ' . mb_strtolower($basis)
            . ' del bien identificado en este informe como ' . mb_strtolower($type)
            . ($location !== '' ? ', ubicado en ' . $location : '')
            . ', con finalidad ' . mb_strtolower($purpose) . ' establecida en el encargo valuatorio.';
    }
}
