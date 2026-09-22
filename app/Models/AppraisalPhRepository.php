<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\{AppraisalPhAgeExtractor, AppraisalPhDocumentStorage, AppraisalPhLegalTrace, AppraisalPhReportBuilder, AppraisalPhSubjectPrefill};
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
        return $this->withGeneratedReport(array_replace(AppraisalPhCatalog::defaults(), $row, [
            'linkage' => $this->json((string) ($row['linkage_json'] ?? '')),
            'common_areas' => $this->json((string) ($row['common_areas_json'] ?? '')),
            'documents' => $this->json((string) ($row['documents_json'] ?? '')),
            'risks' => $this->json((string) ($row['risks_json'] ?? '')),
            'photos' => $this->json((string) ($row['photos_json'] ?? '')),
            'technical' => $this->json((string) ($row['technical_json'] ?? '')),
            'findings' => $this->json((string) ($row['findings_json'] ?? '')),
        ]));
    }
    public function save(string $appraisalId, int $owner, array $data, ?int $expected = null): void
    {
        (new AppraisalPhProfileWriter($this->db))->save($appraisalId, $owner, $data, $expected);
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
            'property_registration' => (string) ($data['matricula_inmobiliaria'] ?? ''),
            'matrix_registration' => (string) ($data['matricula_matriz'] ?? ''),
            'private_unit' => (string) ($data['unidad_privada'] ?? ''),
            'coefficient' => (string) (($data['coeficiente_ph'] ?? '') ?: ($data['coeficiente'] ?? '')),
            'regulation_document' => (string) ($data['reporte_constitucion_ph'] ?? ($data['reglamento_ph'] ?? '')),
            'reform_documents' => (string) ($data['reformas_ph'] ?? ''),
        ];
    }
    public function documents(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare("SELECT id, source_filename, storage_filename, mime_type, file_size_bytes,
            extracted_chars, extracted_text IS NOT NULL AND extracted_text <> '' AS has_extracted_text,
            analysis_status, analysis_message, file_blob IS NOT NULL AS has_blob, created_at
            FROM appraisal_ph_documents WHERE appraisal_id = ? AND owner_id = ?
            ORDER BY created_at DESC, id DESC");
        $query->execute([$appraisalId, $owner]);
        return array_map(static fn (array $row): array => $row + [
            'file_available' => is_file(AppraisalPhDocumentStorage::path((string) $row['storage_filename'])),
        ], $query->fetchAll());
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
        // Deleting a source must preserve the analyst's saved work.
        return ['filename' => $filename, 'cleared' => $cleared];
    }
    public function mergeAnalysis(string $appraisalId, int $owner, array $analysis, ?int $expected = null): void
    {
        $current = $this->profile($appraisalId, $owner);
        $data = array_replace($current, $this->mergeEmpty($current, $analysis['core'] ?? []));
        foreach (['linkage', 'technical', 'common_areas', 'documents', 'risks', 'photos'] as $key) {
            $data[$key] = array_replace($current[$key] ?? [], $this->mergeEmpty($current[$key] ?? [], $analysis[$key] ?? []));
        }
        $data['source_summary'] = (string) ($analysis['summary'] ?? $current['source_summary'] ?? '');
        $data['findings'] = $analysis['findings'] ?? $current['findings'] ?? [];
        $this->save($appraisalId, $owner, $data, $expected ?? (int) ($current['version'] ?? 0));
    }
    private function hasDocuments(string $appraisalId, int $owner): bool
    { $query = $this->db->prepare('SELECT COUNT(*) FROM appraisal_ph_documents WHERE appraisal_id = ? AND owner_id = ?'); $query->execute([$appraisalId, $owner]); return (int) $query->fetchColumn() > 0; }
    private function mergeEmpty(array $current, array $incoming): array
    {
        $merged = [];
        foreach ($incoming as $key => $value) {
            if (($this->emptyValue($current[$key] ?? '') || (is_array($current[$key] ?? null)
                && ($current[$key]['status'] ?? '') === ''
                && ($current[$key]['notes'] ?? '') === 'No identificado en el texto leído. Pendiente de soporte.'))
                && !$this->emptyValue($value)) $merged[$key] = $value;
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
    private function json(string $json): array
    { $decoded = json_decode($json, true); return is_array($decoded) ? $decoded : []; }

    private function withGeneratedReport(array $profile): array
    {
        $profile['technical'] = is_array($profile['technical'] ?? null) ? $profile['technical'] : [];
        $profile['linkage'] = is_array($profile['linkage'] ?? null) ? $profile['linkage'] : [];
        $legal = $this->legalPrefill((string) ($profile['appraisal_id'] ?? ''), (int) ($profile['owner_id'] ?? 0));
        $subject = (new AppraisalPhSubjectPrefill($this->db))->data((string) ($profile['appraisal_id'] ?? ''), (int) ($profile['owner_id'] ?? 0));
        $valuationYear = $this->valuationYear((string) ($profile['appraisal_id'] ?? ''), (int) ($profile['owner_id'] ?? 0));
        if ($valuationYear > 0) $profile['valuation_year'] = $valuationYear;
        if (trim((string) ($profile['linkage']['legal_registration'] ?? '')) === '')
            $profile['linkage']['legal_registration'] = $subject['property_registration'] ?: ($legal['property_registration'] ?? '');
        foreach (['matrix_registration', 'private_unit', 'coefficient'] as $key) {
            if (trim((string) ($profile[$key] ?? '')) === '') $profile[$key] = (string) (($legal[$key] ?? '') ?: ($subject[$key] ?? ''));
        }
        $legalTrace = (new AppraisalPhLegalTrace($this->db))->build((string) ($profile['appraisal_id'] ?? ''), (int) ($profile['owner_id'] ?? 0));
        if (trim((string) ($profile['ph_name'] ?? '')) === ''
            && empty($profile['common_areas']) && empty($profile['technical']) && $legalTrace === '') return $profile;
        if ($legalTrace !== '') $profile['technical']['trazabilidad_juridica_ph'] = $legalTrace;
        foreach ((new AppraisalPhAgeExtractor())->extract($legalTrace, $valuationYear ?: null) as $key => $value) {
            if (trim((string) ($profile['technical'][$key] ?? '')) === '') $profile['technical'][$key] = $value;
        }
        $city = $this->cleanCity((string) ($profile['technical']['ciudad_municipio'] ?? ''));
        if ($city !== '') $profile['technical']['ciudad_municipio'] = $city;
        $built = (new AppraisalPhReportBuilder())->build($profile, $profile['technical'] ?? [],
            $profile['common_areas'] ?? [], $profile['documents'] ?? [], $profile['risks'] ?? [],
            $profile['photos'] ?? [], (string) ($profile['ph_typology'] ?? ''),
            (string) ($profile['source_summary'] ?? ''), $profile['findings'] ?? []);
        foreach (['diagnosis_text', 'report_text'] as $key) {
            if ($this->replaceableReport((string) ($profile[$key] ?? ''))) $profile[$key] = $built[$key] ?? '';
        }
        foreach (($built['technical'] ?? []) as $key => $value) {
            if ($this->replaceableReport((string) ($profile['technical'][$key] ?? ''))) $profile['technical'][$key] = $value;
        }
        return $profile;
    }
    private function replaceableReport(string $text): bool
    {
        $text = trim($text); if ($text === '') return true;
        foreach (['Base comparativa:', 'Trazabilidad:', 'Identificación:', 'Tipología y régimen:',
            'Configuración predial:', 'Bienes comunes y soporte:', 'Reglas de uso y operación:', 'Las reglas de uso y operación de',
            'Administración y cargas:', 'Incidencia valuatoria:', 'Notas y salvedades:', 'Notas normativas y salvedades:',
            'La copropiedad corresponde preliminarmente', 'Se revisa preliminarmente como',
            'Lectura preliminar PH sin hallazgos suficientes', 'Para el análisis de propiedad horizontal se tuvo como soporte',
            'Condición especial PH:', 'Trazabilidad documental:', 'Lectura comparativa:',
            'El inmueble objeto de análisis forma parte de', 'La copropiedad ', 'Se verifican ',
            'Quedan por confirmar ', 'Se registran alertas o salvedades en ', 'Los bienes comunes específicos deben confirmarse',
            'Bienes comunes esenciales:', 'Bienes comunes no esenciales', 'Áreas comunes de uso exclusivo:', 'Soporte operativo y técnico común:', 'No se han marcado bienes comunes verificados', 'No se han identificado bienes comunes', 'Para la tipología '] as $prefix) {
            if (str_starts_with($text, $prefix)) return true;
        }
        return false;
    }

    private function valuationYear(string $appraisalId, int $owner): int
    {
        try {
            $query = $this->db->prepare('SELECT value_date, report_date, visit_date FROM appraisals WHERE id = ? AND owner_id = ?');
            $query->execute([$appraisalId, $owner]); $row = $query->fetch() ?: [];
        } catch (\PDOException) { return 0; }
        foreach (['value_date', 'report_date', 'visit_date'] as $key) if (preg_match('/^(\d{4})-\d{2}-\d{2}$/', (string) ($row[$key] ?? ''), $m)) return (int) $m[1];
        return 0;
    }
    private function cleanCity(string $text): string
    { return $text === '' || mb_strlen($text) > 80 || str_contains($text, '[') ? '' : (preg_match('/\bCartagena(?: de Indias)?\b/iu', $text) ? 'Cartagena de Indias' : trim($text)); }
}
