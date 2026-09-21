<?php
declare(strict_types=1);
namespace App\Services;
use PDO;

final class AppraisalPhSubjectPrefill
{
    public function __construct(private PDO $db) {}

    public function data(string $appraisalId, int $owner): array
    {
        $registry = $this->registry($appraisalId, $owner);
        return ['property_registration' => $registry, 'private_unit' => $this->unit($appraisalId, $owner)];
    }

    private function registry(string $appraisalId, int $owner): string
    {
        try {
            $q = $this->db->prepare('SELECT property_registry FROM appraisal_subjects WHERE appraisal_id = ? AND owner_id = ?');
            $q->execute([$appraisalId, $owner]);
            return (string) ($q->fetchColumn() ?: '');
        } catch (\PDOException) { return ''; }
    }

    private function unit(string $appraisalId, int $owner): string
    {
        try {
            $q = $this->db->prepare('SELECT unit_kind, unit_index, label FROM appraisal_units
                WHERE appraisal_id = ? AND owner_id = ? AND unit_kind <> "common" ORDER BY unit_kind, unit_index');
            $q->execute([$appraisalId, $owner]);
            $labels = array_map(static function (array $row): string {
                $label = trim((string) ($row['label'] ?? ''));
                $default = ((string) ($row['unit_kind'] ?? '') === 'annex' ? 'Anexo ' : 'Unidad ') . (int) ($row['unit_index'] ?? 0);
                return $label !== '' && $label !== $default ? $label : '';
            }, $q->fetchAll());
            return implode(', ', array_values(array_filter($labels)));
        } catch (\PDOException) { return ''; }
    }
}
