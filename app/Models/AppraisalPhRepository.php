<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
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
            'linkage' => $this->json((string) ($row['linkage_json'] ?? '')),
            'common_areas' => $this->json((string) ($row['common_areas_json'] ?? '')),
            'documents' => $this->json((string) ($row['documents_json'] ?? '')),
            'risks' => $this->json((string) ($row['risks_json'] ?? '')),
            'photos' => $this->json((string) ($row['photos_json'] ?? '')),
            'technical' => $this->json((string) ($row['technical_json'] ?? '')),
            'findings' => $this->json((string) ($row['findings_json'] ?? '')),
        ]);
    }
    public function save(string $appraisalId, int $owner, array $data): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $fields = ['ph_key', 'ph_name', 'ph_typology', 'administration_name', 'administration_contact',
            'administration_phone', 'administration_email', 'matrix_registration', 'private_unit',
            'coefficient', 'regulation_document', 'reform_documents', 'monthly_fee', 'fee_status',
            'reserve_fund', 'insurance_status', 'restrictions_text', 'diagnosis_text', 'report_text'];
        $json = [
            'linkage_json' => $data['linkage'] ?? [],
            'common_areas_json' => $data['common_areas'] ?? [],
            'documents_json' => $data['documents'] ?? [],
            'risks_json' => $data['risks'] ?? [],
            'photos_json' => $data['photos'] ?? [],
            'technical_json' => $data['technical'] ?? [],
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
    public function searchByCoproperty(string $term, int $owner, string $excludeId = '', int $limit = 8): array
    {
        $needle = '%' . trim($term) . '%';
        if ($needle === '%%') return [];
        $query = $this->db->prepare('SELECT p.appraisal_id AS id, p.ph_name, p.ph_key, p.ph_typology,
                p.matrix_registration, p.updated_at, a.titulo, a.direccion, a.municipio,
                a.client_name, a.property_owner_name, s.subject_title, s.address,
                s.neighborhood_name, s.property_registry, s.cadastral_reference
            FROM appraisal_ph_profiles p
            JOIN appraisals a ON a.id = p.appraisal_id AND a.owner_id = p.owner_id
            LEFT JOIN appraisal_subjects s ON s.appraisal_id = p.appraisal_id AND s.owner_id = p.owner_id
            WHERE p.owner_id = ? AND p.appraisal_id <> ?
                AND (p.ph_name LIKE ? OR p.ph_key LIKE ? OR p.matrix_registration LIKE ?)
            ORDER BY p.updated_at DESC, p.appraisal_id DESC LIMIT ' . max(1, min(12, $limit)));
        $query->execute([$owner, $excludeId, $needle, $needle, $needle]);
        return $query->fetchAll();
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
    public function documents(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT id, source_filename, mime_type, file_size_bytes,
            extracted_chars, analysis_status, analysis_message, created_at
            FROM appraisal_ph_documents WHERE appraisal_id = ? AND owner_id = ?
            ORDER BY created_at DESC, id DESC');
        $query->execute([$appraisalId, $owner]);
        return $query->fetchAll();
    }
    public function addDocument(string $appraisalId, int $owner, array $file): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('INSERT INTO appraisal_ph_documents
            (id, appraisal_id, owner_id, source_filename, storage_filename, mime_type, file_size_bytes,
            extracted_chars, extracted_text, analysis_status, analysis_message, file_blob, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $query->execute([$file['id'], $appraisalId, $owner, $file['source_filename'], $file['storage_filename'],
            $file['mime_type'], $file['file_size_bytes'], $file['extracted_chars'], $file['extracted_text'] ?? '', $file['analysis_status'],
            $file['analysis_message'], $file['file_blob'], $now]);
    }
    public function hasDocumentFile(string $appraisalId, int $owner, string $sourceFilename, int $bytes): bool
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM appraisal_ph_documents
            WHERE appraisal_id = ? AND owner_id = ? AND source_filename = ? AND file_size_bytes = ?');
        $query->execute([$appraisalId, $owner, $sourceFilename, $bytes]);
        return (int) $query->fetchColumn() > 0;
    }
    public function documentForAnalysis(string $id, string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT id, source_filename, storage_filename, mime_type,
            file_size_bytes, extracted_text, file_blob FROM appraisal_ph_documents
            WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
        $query->execute([$id, $appraisalId, $owner]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró el soporte PH.');
        return $row;
    }
    public function updateDocumentAnalysis(string $id, string $appraisalId, int $owner, int $chars, string $message, string $text = ''): void
    {
        $query = $this->db->prepare('UPDATE appraisal_ph_documents SET extracted_chars = ?,
            extracted_text = ?, analysis_status = ?, analysis_message = ? WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
        $query->execute([$chars, $text, 'Lectura preliminar', $message, $id, $appraisalId, $owner]);
    }
    public function deleteDocument(string $id, string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT storage_filename FROM appraisal_ph_documents
            WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
        $query->execute([$id, $appraisalId, $owner]);
        $filename = (string) ($query->fetchColumn() ?: '');
        if ($filename === '') throw new HttpException(404, 'No se encontró el soporte PH.');
        $delete = $this->db->prepare('DELETE FROM appraisal_ph_documents
            WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
        $delete->execute([$id, $appraisalId, $owner]);
        $cleared = !$this->hasDocuments($appraisalId, $owner);
        if ($cleared) $this->clearDocumentAnalysis($appraisalId, $owner);
        return ['filename' => $filename, 'cleared' => $cleared];
    }
    public function mergeAnalysis(string $appraisalId, int $owner, array $analysis): void
    {
        if (array_key_exists('has_text', $analysis) && $analysis['has_text'] === false) {
            $this->clearDocumentAnalysis($appraisalId, $owner);
        }
        $current = $this->profile($appraisalId, $owner);
        $data = array_replace($current, $this->mergeEmpty($current, $analysis['core'] ?? []));
        foreach (['linkage', 'technical', 'common_areas', 'documents', 'risks', 'photos'] as $key) {
            $data[$key] = array_replace($current[$key] ?? [], $this->mergeEmpty($current[$key] ?? [], $analysis[$key] ?? []));
        }
        $data['source_summary'] = (string) ($analysis['summary'] ?? $current['source_summary'] ?? '');
        $data['findings'] = $analysis['findings'] ?? $current['findings'] ?? [];
        $this->save($appraisalId, $owner, $data);
        $query = $this->db->prepare('UPDATE appraisal_ph_profiles SET source_summary = ?, findings_json = ?
            WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$data['source_summary'],
            json_encode($data['findings'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), $appraisalId, $owner]);
    }
    private function exists(string $appraisalId, int $owner): bool
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM appraisal_ph_profiles WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        return (int) $query->fetchColumn() > 0;
    }
    private function hasDocuments(string $appraisalId, int $owner): bool
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM appraisal_ph_documents WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        return (int) $query->fetchColumn() > 0;
    }
    private function clearDocumentAnalysis(string $appraisalId, int $owner): void
    {
        $query = $this->db->prepare("UPDATE appraisal_ph_profiles SET ph_key = '', ph_name = '',
            matrix_registration = '', private_unit = '', coefficient = '', regulation_document = '',
            reform_documents = '', monthly_fee = '', restrictions_text = '', diagnosis_text = '',
            report_text = '', linkage_json = '[]', common_areas_json = '[]', documents_json = '[]',
            risks_json = '[]', technical_json = '[]', source_summary = '', findings_json = '[]',
            updated_at = ? WHERE appraisal_id = ? AND owner_id = ?");
        $query->execute([gmdate('Y-m-d H:i:s'), $appraisalId, $owner]);
    }
    private function values(array $fields, array $data): array
    {
        return array_map(static fn (string $field): mixed => $data[$field] ?? '', $fields);
    }
    private function mergeEmpty(array $current, array $incoming): array
    {
        $merged = [];
        foreach ($incoming as $key => $value) {
            if ($this->emptyValue($current[$key] ?? '') && !$this->emptyValue($value)) $merged[$key] = $value;
        }
        return $merged;
    }
    private function emptyValue(mixed $value): bool
    {
        if (is_array($value)) {
            foreach ($value as $item) if (!$this->emptyValue($item)) return false;
            return true;
        }
        return trim((string) $value) === '';
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
