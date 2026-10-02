<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class MarketSubjectEvidence
{
    public static function fields(): array
    {
        return [
            'registry' => ['Matrícula de esta unidad', 'Ej. 060-123456'],
            'legal_source' => ['Soporte de la identificación y naturaleza jurídica', 'Ej. CTL, escritura o reglamento, fecha y página'],
            'observed_use' => ['Uso observado de esta unidad', 'Ej. Oficina, parqueadero, depósito'],
            'approved_use' => ['Uso aprobado documentado', 'Ej. Oficina según licencia o reglamento'],
            'use_source' => ['Soporte del uso aprobado', 'Ej. Licencia o reglamento, fecha y página'],
            'use_reconciliation' => ['Explicación del contraste de usos, si difieren', 'Explica compatibilidad, restricción o pendiente; no implica aprobación legal'],
            'coefficient' => ['Coeficiente de esta unidad (%)', 'Ej. 1,25'],
            'coefficient_source' => ['Soporte del coeficiente', 'Ej. Reglamento, cuadro de coeficientes y página'],
            'included_components' => ['Componentes comprendidos en esta unidad para Mercado', 'Ej. Oficina 301 con garaje 12 y depósito 8; identifica exclusiones'],
            'scope_source' => ['Soporte de los componentes incluidos', 'Ej. Escritura, reglamento, identificación de anexos y alcance del encargo'],
        ];
    }

    public static function decode(array $unit): array
    {
        $data = json_decode((string) ($unit['market_evidence_json'] ?? '{}'), true);
        return is_array($data) ? $data : [];
    }

    public static function input(mixed $posted): array
    {
        if (!is_array($posted)) throw new HttpException(422, 'Diligencia el soporte de Mercado de la unidad.');
        $data = [];
        foreach (self::fields() as $key => $definition) {
            if (isset($posted[$key]) && !is_string($posted[$key])) throw new HttpException(422, 'Revisa el campo ' . $definition[0] . '.');
            $data[$key] = mb_substr(trim((string) ($posted[$key] ?? '')), 0, 1200);
        }
        foreach (['use_contrast' => ['', 'compatible', 'condicionado', 'incompatible', 'pendiente'], 'identity_scope' => ['', 'propia', 'sujeto'],
            'legal_nature' => ['', 'privada', 'integrada', 'comun_exclusivo']] as $key => $allowed) {
            if (!is_string($posted[$key] ?? '') || !in_array($posted[$key] ?? '', $allowed, true)) throw new HttpException(422, 'Revisa la identificación jurídica de esta unidad.');
            $data[$key] = $posted[$key] ?? '';
        }
        if ($data['coefficient'] !== '' && (self::number($data['coefficient']) === null
            || self::number($data['coefficient']) < 0 || self::number($data['coefficient']) > 100)) {
            throw new HttpException(422, 'El coeficiente debe estar entre 0 y 100 %, sin texto adicional.');
        }
        return $data;
    }

    public static function number(mixed $value): ?float
    {
        $text = str_replace(',', '.', trim((string) $value));
        return preg_match('/^\d+(?:\.\d+)?$/', $text) ? (float) $text : null;
    }

    public static function normalized(string $value): string
    {
        $text = strtr(mb_strtolower($value), ['á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u', 'ñ'=>'n']);
        return preg_replace('/[^a-z0-9]+/', '', $text) ?? '';
    }

    public static function effective(array $unit, array $subject, array $ph): array
    {
        $data = self::decode($unit);
        if (($data['identity_scope'] ?? '') !== 'sujeto') return $data;
        // The analyst explicitly links this unit; a global profile never applies by default.
        $inherit = ['registry' => $subject['property_registry'] ?? '', 'observed_use' => $subject['current_use'] ?? '',
            'approved_use' => $ph['technical']['usos_permitidos'] ?? '', 'use_source' => $ph['regulation_document'] ?? '',
            'coefficient' => $ph['coefficient'] ?? '', 'coefficient_source' => $ph['regulation_document'] ?? ''];
        foreach ($inherit as $key => $value) if (trim((string) ($data[$key] ?? '')) === '') $data[$key] = $value;
        return $data;
    }
}
