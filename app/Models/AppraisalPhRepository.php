<?php
declare(strict_types=1);
namespace App\Models;
use App\Support\AppraisalPhCatalog;
use PDO;

final class AppraisalPhRepository
{
    public function __construct(private PDO $db) {}

    public function profile(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_ph_profiles WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        $row = $query->fetch();
        if (!$row) return AppraisalPhCatalog::defaults();
        return array_replace(AppraisalPhCatalog::defaults(), $row, [
            'common_areas' => $this->json((string) ($row['common_areas_json'] ?? '')),
            'documents' => $this->json((string) ($row['documents_json'] ?? '')),
            'risks' => $this->json((string) ($row['risks_json'] ?? '')),
            'photos' => $this->json((string) ($row['photos_json'] ?? '')),
        ]);
    }

    public function save(string $appraisalId, int $owner, array $data): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $fields = ['ph_key', 'ph_name', 'administration_name', 'administration_contact',
            'administration_phone', 'administration_email', 'matrix_registration', 'private_unit',
            'coefficient', 'regulation_document', 'reform_documents', 'monthly_fee', 'fee_status',
            'reserve_fund', 'insurance_status', 'restrictions_text', 'diagnosis_text', 'report_text'];
        $json = [
            'common_areas_json' => $data['common_areas'] ?? [],
            'documents_json' => $data['documents'] ?? [],
            'risks_json' => $data['risks'] ?? [],
            'photos_json' => $data['photos'] ?? [],
        ];
        if ($this->exists($appraisalId, $owner)) {
            $assignments = implode(', ', array_map(static fn (string $field): string => $field . ' = ?',
                array_merge($fields, array_keys($json))));
            $query = $this->db->prepare('UPDATE appraisal_ph_profiles SET ' . $assignments . ',
                updated_at = ? WHERE appraisal_id = ? AND owner_id = ?');
            $query->execute([...$this->values($fields, $data), ...$this->jsonValues($json),
                $now, $appraisalId, $owner]);
            return;
        }
        $columns = array_merge(['appraisal_id', 'owner_id'], $fields, array_keys($json), ['created_at', 'updated_at']);
        $marks = implode(', ', array_fill(0, count($columns), '?'));
        $query = $this->db->prepare('INSERT INTO appraisal_ph_profiles (' . implode(', ', $columns) . ')
            VALUES (' . $marks . ')');
        $query->execute([$appraisalId, $owner, ...$this->values($fields, $data),
            ...$this->jsonValues($json), $now, $now]);
    }

    public function legalPrefill(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT data_json FROM appraisal_legal_profiles WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        $data = $this->json((string) ($query->fetchColumn() ?: ''));
        return [
            'matrix_registration' => (string) ($data['matricula_matriz'] ?? ''),
            'private_unit' => (string) ($data['unidad_privada'] ?? ''),
            'coefficient' => (string) ($data['coeficiente_ph'] ?? ''),
            'regulation_document' => (string) ($data['reporte_constitucion_ph'] ?? ($data['reglamento_ph'] ?? '')),
            'reform_documents' => (string) ($data['reformas_ph'] ?? ''),
        ];
    }

    private function exists(string $appraisalId, int $owner): bool
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM appraisal_ph_profiles WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        return (int) $query->fetchColumn() > 0;
    }

    private function values(array $fields, array $data): array
    {
        return array_map(static fn (string $field): mixed => $data[$field] ?? '', $fields);
    }

    private function jsonValues(array $data): array
    {
        return array_values(array_map(static fn (array $value): string =>
            json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), $data));
    }

    private function json(string $json): array
    {
        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }
}
