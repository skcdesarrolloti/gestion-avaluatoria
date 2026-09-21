<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use PDO;

final class AppraisalPhProfileWriter
{
    public function __construct(private PDO $db) {}

    public function save(string $id, int $owner, array $data, ?int $expected = null): void
    {
        $fields = ['ph_key', 'ph_name', 'ph_typology', 'administration_name', 'administration_contact',
            'administration_phone', 'administration_email', 'matrix_registration', 'private_unit',
            'coefficient', 'regulation_document', 'reform_documents', 'monthly_fee', 'fee_status',
            'reserve_fund', 'insurance_status', 'restrictions_text', 'diagnosis_text', 'report_text'];
        $values = array_map(static fn ($key) => $data[$key] ?? '', $fields);
        foreach (['linkage', 'common_areas', 'documents', 'risks', 'photos', 'technical'] as $key) {
            $fields[] = $key . '_json';
            $values[] = json_encode($data[$key] ?? [], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }
        if (array_key_exists('source_summary', $data)) {
            $fields[] = 'source_summary'; $values[] = $data['source_summary'];
            $fields[] = 'findings_json'; $values[] = json_encode($data['findings'] ?? [], JSON_THROW_ON_ERROR);
        }
        $q = $this->db->prepare('SELECT version FROM appraisal_ph_profiles WHERE appraisal_id = ? AND owner_id = ?');
        $q->execute([$id, $owner]); $current = $q->fetchColumn();
        $expected ??= $current === false ? 0 : (int) $current;
        if ($current !== false) {
            $sql = implode(', ', array_map(static fn ($field) => "$field = ?", $fields));
            $q = $this->db->prepare("UPDATE appraisal_ph_profiles SET $sql, updated_at = ?, version = version + 1
                WHERE appraisal_id = ? AND owner_id = ? AND version = ?");
            $q->execute([...$values, gmdate('Y-m-d H:i:s'), $id, $owner, $expected]);
            if ($q->rowCount() !== 1) $this->conflict();
            return;
        }
        if ($expected !== 0) $this->conflict();
        $columns = implode(', ', array_merge(['appraisal_id', 'owner_id'], $fields, ['created_at', 'updated_at', 'version']));
        $marks = implode(', ', array_fill(0, count($values) + 5, '?'));
        try {
            $q = $this->db->prepare("INSERT INTO appraisal_ph_profiles ($columns) VALUES ($marks)");
            $q->execute([$id, $owner, ...$values, gmdate('Y-m-d H:i:s'), gmdate('Y-m-d H:i:s'), 1]);
        } catch (\PDOException $e) {
            if (in_array((string) $e->getCode(), ['23000', '23505'])) $this->conflict();
            throw $e;
        }
    }

    private function conflict(): never
    {
        throw new HttpException(409, 'Conflicto: la ficha PH cambió en otra edición. Conserva tus cambios y recarga antes de reintentar.');
    }
}
