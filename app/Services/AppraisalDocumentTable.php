<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalCatalog;
use App\Core\HttpException;

final class AppraisalDocumentTable
{
    public static function labels(): array
    {
        return [
            'escritura_publica' => 'Escritura pública', 'tipo_adquisicion' => 'Tipo de adquisición',
            'impuesto_predial' => 'Factura del predial', 'certificado_tradicion' => 'Certificado de tradición y libertad',
            'reglamento_ph' => 'Reglamento de propiedad horizontal', 'planos' => 'Planos',
            'licencia_construccion' => 'Licencia de construcción',
        ] + AppraisalCatalog::sourceDocumentOptions();
    }

    public static function rows(array $record): array
    {
        $details = json_decode((string) ($record['source_document_details'] ?? ''), true);
        $selected = json_decode((string) ($record['source_documents_json'] ?? ''), true);
        $details = is_array($details) ? $details : [];
        $selected = is_array($selected) ? $selected : [];
        $rows = [];
        foreach (self::labels() as $key => $label) {
            $rows[$key] = ['label' => $label, 'text' => (string) ($details[$key] ??
                (in_array($key, $selected, true) ? 'Marcado como aportado o revisado en el registro anterior.' : ''))];
        }
        return $rows;
    }

    public static function input(mixed $posted): string
    {
        if (!is_array($posted)) throw new HttpException(422, 'Revisa la tabla de documentos.');
        $clean = [];
        foreach (self::labels() as $key => $label) {
            $value = $posted[$key] ?? '';
            if (!is_string($value) || mb_strlen($value) > 1000) throw new HttpException(422, $label . ': máximo 1000 caracteres.');
            $clean[$key] = trim($value);
        }
        return json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    public static function text(array $record): string
    {
        $lines = [];
        foreach (self::rows($record) as $row) {
            if ($row['text'] !== '') $lines[] = $row['label'] . ': ' . $row['text'];
        }
        return implode("\n", $lines);
    }
}
