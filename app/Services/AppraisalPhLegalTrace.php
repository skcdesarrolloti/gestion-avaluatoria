<?php
declare(strict_types=1);
namespace App\Services;

use PDO;

final class AppraisalPhLegalTrace
{
    public function __construct(private PDO $db) {}

    public function build(string $appraisalId, int $owner): string
    {
        if ($appraisalId === '' || $owner <= 0) return '';
        $query = $this->db->prepare('SELECT data_json, annotations_json FROM appraisal_legal_profiles WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        $row = $query->fetch();
        if (!$row) return '';
        $data = $this->json((string) ($row['data_json'] ?? ''));
        $annotations = array_values(array_filter($this->json((string) ($row['annotations_json'] ?? '')),
            static fn (array $item): bool => ($item['categoria'] ?? '') === 'propiedad_horizontal'));
        return $this->fromLegalData($data, $annotations);
    }

    public function fromLegalData(array $data, array $annotations): string
    {
        $parts = [];
        foreach (array_slice($annotations, 0, 3) as $row) {
            $line = $this->annotationLine($row);
            if ($line !== '') $parts[] = $line;
        }
        if (!$parts && trim((string) ($data['reporte_constitucion_ph'] ?? '')) !== '') {
            $parts[] = trim((string) $data['reporte_constitucion_ph']);
        }
        if (!$parts) return '';
        return implode(' ', $parts) . ' En el análisis de propiedad horizontal, esta referencia se trata como condición especial: permite relacionar el régimen de copropiedad con reglamento, bienes comunes, administración, coeficientes y reglas de uso aplicables al bien sujeto.';
    }

    private function annotationLine(array $row): string
    {
        $parts = [];
        if (($row['orden'] ?? '') !== '') $parts[] = 'anotación No. ' . $row['orden'];
        if (($row['fecha'] ?? '') !== '') $parts[] = 'de fecha ' . $row['fecha'];
        $lead = $parts ? 'De acuerdo con la ' . implode(', ', $parts) : 'De acuerdo con el folio jurídico';
        $act = trim((string) (($row['descripcion_acto'] ?? '') ?: 'acto relacionado con propiedad horizontal'));
        $support = trim((string) ($row['documento'] ?? ''));
        $impact = trim((string) ($row['impacto_resumen'] ?? ''));
        return $lead . ', se registra ' . mb_strtolower($act) . ($support !== '' ? ', con soporte en ' . $support : '')
            . '. ' . ($impact !== '' ? $impact : 'Debe complementarse con el reglamento de propiedad horizontal.');
    }

    private function json(string $json): array
    {
        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }
}
