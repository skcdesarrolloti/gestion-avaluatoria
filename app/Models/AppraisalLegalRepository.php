<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\AppraisalLegalCertificateStorage;
use App\Services\AppraisalLegalInput;
use App\Support\AppraisalLegalCatalog;
use PDO;

final class AppraisalLegalRepository
{
    public function __construct(private PDO $db) {}

    public function profile(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_legal_profiles WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        $row = $query->fetch();
        if (!$row) {
            return ['appraisal_id' => $appraisalId, 'owner_id' => $owner, 'source_certificate_id' => '',
                'status' => 'Pendiente de revisión', 'data' => AppraisalLegalCatalog::defaults(),
                'annotations' => [], 'alerts' => [], 'extracted_text' => '', 'updated_at' => null];
        }
        return $row + ['data' => $this->json((string) ($row['data_json'] ?? ''), AppraisalLegalCatalog::defaults()),
            'annotations' => $this->json((string) ($row['annotations_json'] ?? ''), []),
            'alerts' => $this->json((string) ($row['alerts_json'] ?? ''), []),
            'extracted_text' => (string) ($row['extracted_text'] ?? '')];
    }

    public function certificates(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT id, appraisal_id, owner_id, source_filename, storage_filename,
            mime_type, file_size_bytes, extracted_chars, analysis_status, analysis_message, created_at,
            file_blob IS NOT NULL AS has_blob FROM appraisal_legal_certificates
            WHERE appraisal_id = ? AND owner_id = ? ORDER BY created_at DESC, id DESC');
        $query->execute([$appraisalId, $owner]);
        return array_map(static fn (array $row): array => $row + [
            'file_available' => is_file(AppraisalLegalCertificateStorage::path((string) $row['storage_filename'])),
        ], $query->fetchAll());
    }

    public function addCertificate(string $appraisalId, int $owner, array $file): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('INSERT INTO appraisal_legal_certificates
            (id, appraisal_id, owner_id, source_filename, storage_filename, mime_type, file_size_bytes,
            extracted_chars, analysis_status, analysis_message, file_blob, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $query->execute([$file['id'], $appraisalId, $owner, $file['source_filename'], $file['storage_filename'],
            $file['mime_type'], $file['file_size_bytes'], $file['extracted_chars'], $file['analysis_status'],
            $file['analysis_message'], $file['file_blob'], $now]);
    }

    public function findCertificate(string $id, string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_legal_certificates WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
        $query->execute([$id, $appraisalId, $owner]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró el certificado.');
        return $row;
    }

    public function deleteCertificate(string $id, string $appraisalId, int $owner): ?string
    {
        $file = $this->findCertificate($id, $appraisalId, $owner);
        $query = $this->db->prepare('DELETE FROM appraisal_legal_certificates
            WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
        $query->execute([$id, $appraisalId, $owner]);
        $this->clearSourceIfMatches($appraisalId, $owner, $id);
        $path = self::path((string) $file['storage_filename']);
        return is_file($path) ? $path : null;
    }

    public function latestCertificate(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_legal_certificates
            WHERE appraisal_id = ? AND owner_id = ? ORDER BY created_at DESC, id DESC LIMIT 1');
        $query->execute([$appraisalId, $owner]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'Primero carga un certificado.');
        return $row;
    }

    public function updateCertificateAnalysis(string $id, string $appraisalId, int $owner,
        int $chars, string $status, string $message): void
    {
        $query = $this->db->prepare('UPDATE appraisal_legal_certificates SET extracted_chars = ?,
            analysis_status = ?, analysis_message = ? WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
        $query->execute([$chars, $status, $message, $id, $appraisalId, $owner]);
    }

    public function mergeAnalysis(string $appraisalId, int $owner, string $certificateId, array $data,
        array $annotations, array $alerts, string $text): void
    {
        $current = $this->profile($appraisalId, $owner);
        $merged = AppraisalLegalInput::mergeEmpty($current['data'] ?? [], $data);
        foreach (['semaforo_manual', 'clasificacion_manual', 'revision_analista',
            'salvedad_final', 'reporte_conclusion_entregable',
            'reporte_profesional_entregable'] as $key) {
            if (trim((string) ($data[$key] ?? '')) !== '') $merged[$key] = (string) $data[$key];
        }
        $this->upsert($appraisalId, $owner, $certificateId, 'Lectura preliminar', $merged, $annotations, $alerts, $text);
    }

    public function saveManual(string $appraisalId, int $owner, array $data): void
    {
        $current = $this->profile($appraisalId, $owner);
        $this->upsert($appraisalId, $owner, (string) ($current['source_certificate_id'] ?? ''),
            'Revisado por analista', $data, $current['annotations'] ?? [], $current['alerts'] ?? [],
            (string) ($current['extracted_text'] ?? ''));
    }

    private function upsert(string $appraisalId, int $owner, string $certificateId, string $status,
        array $data, array $annotations, array $alerts, string $text): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $payload = [json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            json_encode($annotations, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            json_encode($alerts, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            mb_substr($text, 0, 70000), $now];
        $exists = $this->profileExists($appraisalId, $owner);
        if ($exists) {
            $query = $this->db->prepare('UPDATE appraisal_legal_profiles SET source_certificate_id = ?,
                status = ?, data_json = ?, annotations_json = ?, alerts_json = ?, extracted_text = ?,
                updated_at = ? WHERE appraisal_id = ? AND owner_id = ?');
            $query->execute([$certificateId, $status, ...$payload, $appraisalId, $owner]);
            return;
        }
        $query = $this->db->prepare('INSERT INTO appraisal_legal_profiles
            (appraisal_id, owner_id, source_certificate_id, status, data_json, annotations_json,
            alerts_json, extracted_text, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $query->execute([$appraisalId, $owner, $certificateId, $status, ...$payload]);
    }

    private function profileExists(string $appraisalId, int $owner): bool
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM appraisal_legal_profiles WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        return (int) $query->fetchColumn() > 0;
    }

    private function clearSourceIfMatches(string $appraisalId, int $owner, string $certificateId): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('UPDATE appraisal_legal_profiles
            SET source_certificate_id = NULL, status = ?, updated_at = ?
            WHERE appraisal_id = ? AND owner_id = ? AND source_certificate_id = ?');
        $query->execute(['Soporte retirado; revisar datos conservados', $now, $appraisalId, $owner, $certificateId]);
    }

    private function json(string $json, array $default): array
    {
        if (trim($json) === '') return $default;
        $decoded = json_decode($json, true);
        return is_array($decoded) ? array_replace($default, $decoded) : $default;
    }

    public static function path(string $filename): string { return AppraisalLegalCertificateStorage::path($filename); }
}
