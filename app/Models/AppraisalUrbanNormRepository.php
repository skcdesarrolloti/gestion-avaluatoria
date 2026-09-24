<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use PDO;

final class AppraisalUrbanNormRepository
{
    public function __construct(private PDO $db) {}

    public function profile(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_urban_norm_profiles
            WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        $row = $query->fetch();
        return $row ? array_replace($this->defaults($appraisalId, $owner), $row)
            : $this->defaults($appraisalId, $owner);
    }

    public function save(string $appraisalId, int $owner, int $version, array $input): int
    {
        $current = $this->profile($appraisalId, $owner);
        if ((int) $current['version'] !== $version) {
            throw new HttpException(409, 'La normatividad urbana fue modificada en otra pestaña. Recarga antes de guardar.');
        }
        $data = $this->normalized($input);
        $now = gmdate('Y-m-d H:i:s');
        $next = $version + 1;
        if ($version === 0 && !$this->exists($appraisalId, $owner)) {
            $this->insert($appraisalId, $owner, $data, $next, $now);
            return $next;
        }
        $this->update($appraisalId, $owner, $data, $next, $now);
        return $next;
    }

    public function references(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT r.*, d.title document_title, t.title table_title, c.name category_name
            FROM appraisal_urban_norm_references r
            LEFT JOIN urban_norm_documents d ON d.slug = r.document_slug
            LEFT JOIN urban_norm_tables t ON t.slug = r.table_slug
            LEFT JOIN urban_norm_use_categories c ON c.slug = r.category_slug
            WHERE r.appraisal_id = ? AND r.owner_id = ? ORDER BY r.created_at DESC, r.id DESC');
        $query->execute([$appraisalId, $owner]);
        return $query->fetchAll();
    }

    public function addReference(string $appraisalId, int $owner, array $input): string
    {
        $id = bin2hex(random_bytes(16));
        $now = gmdate('Y-m-d H:i:s');
        $data = $this->referenceData($input);
        $query = $this->db->prepare('INSERT INTO appraisal_urban_norm_references
            (id, appraisal_id, owner_id, document_slug, table_slug, category_slug, reference_type,
            source_label, source_date, extracted_text, support_filename, storage_filename, file_size_bytes,
            pdf_blob, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $query->execute([$id, $appraisalId, $owner, ...array_values($data), $now]);
        return $id;
    }

    private function normalized(array $input): array
    {
        $keys = ['cadastral_reference', 'cadastral_reference_short', 'cadastral_reference_long',
            'document_slug', 'table_slug', 'category_slug', 'source_status',
            'pot_state', 'midas_query_option', 'midas_layers', 'midas_usage_result', 'midas_activity',
            'midas_support_reference', 'midas_predio_raw', 'midas_usage_raw', 'use_regulation_table',
            'use_principal_text', 'use_compatible_text', 'use_complementary_text', 'use_restricted_text',
            'use_prohibited_text', 'norm_unit_basic_text', 'norm_free_area_text', 'norm_min_lot_front_text',
            'norm_max_height_text', 'norm_construction_index_text', 'norm_isolation_text', 'norm_other_potential_text',
            'planning_concept_number', 'official_concept_scope',
            'land_classification', 'activity_area', 'normative_zone', 'urban_treatment', 'urban_license',
            'permitted_use', 'current_use', 'intended_use', 'applicable_activity', 'urban_norms_applied', 'heritage_context',
            'environmental_context', 'risk_context', 'use_cross_result', 'midas_result', 'restrictions',
            'legal_urban_affectations', 'conclusion', 'support_summary', 'analyst_notes', 'source_limitations'];
        $limits = ['cadastral_reference' => 80, 'cadastral_reference_short' => 80,
            'cadastral_reference_long' => 120, 'document_slug' => 100, 'table_slug' => 120,
            'category_slug' => 140, 'source_status' => 60, 'pot_state' => 80, 'midas_query_option' => 80,
            'midas_layers' => 500, 'midas_usage_result' => 5000, 'midas_activity' => 180,
            'midas_support_reference' => 220, 'midas_predio_raw' => 70000, 'midas_usage_raw' => 70000,
            'use_regulation_table' => 120, 'use_principal_text' => 70000, 'use_compatible_text' => 70000,
            'use_complementary_text' => 70000, 'use_restricted_text' => 70000, 'use_prohibited_text' => 70000,
            'norm_unit_basic_text' => 70000, 'norm_free_area_text' => 70000, 'norm_min_lot_front_text' => 70000,
            'norm_max_height_text' => 70000, 'norm_construction_index_text' => 70000,
            'norm_isolation_text' => 70000, 'norm_other_potential_text' => 70000,
            'planning_concept_number' => 120,
            'official_concept_scope' => 5000, 'land_classification' => 120, 'activity_area' => 160,
            'normative_zone' => 160, 'urban_treatment' => 160, 'urban_license' => 220,
            'permitted_use' => 5000, 'current_use' => 160, 'intended_use' => 220, 'applicable_activity' => 160, 'urban_norms_applied' => 5000,
            'heritage_context' => 5000, 'environmental_context' => 5000, 'risk_context' => 5000,
            'use_cross_result' => 40, 'midas_result' => 5000, 'restrictions' => 5000,
            'legal_urban_affectations' => 5000, 'conclusion' => 5000, 'support_summary' => 5000, 'analyst_notes' => 5000,
            'source_limitations' => 5000];
        $data = [];
        foreach ($keys as $key) $data[$key] = mb_substr(trim((string) ($input[$key] ?? '')), 0, $limits[$key]);
        foreach (['document_slug', 'table_slug', 'category_slug'] as $key) {
            $data[$key] = $data[$key] === '' ? null : $data[$key];
        }
        $data['midas_consulted'] = !empty($input['midas_consulted']) ? 1 : 0;
        $data['midas_consulted_on'] = $this->date($input['midas_consulted_on'] ?? null);
        $data['planning_concept_date'] = $this->date($input['planning_concept_date'] ?? null);
        return $data;
    }

    private function referenceData(array $input): array
    {
        return ['document_slug' => $this->nullableText($input, 'document_slug', 100),
            'table_slug' => $this->nullableText($input, 'table_slug', 120),
            'category_slug' => $this->nullableText($input, 'category_slug', 140),
            'reference_type' => $this->text($input, 'reference_type', 40),
            'source_label' => $this->text($input, 'source_label', 220),
            'source_date' => $this->date($input['source_date'] ?? null),
            'extracted_text' => $this->text($input, 'extracted_text', 70000),
            'support_filename' => $this->text($input, 'support_filename', 220),
            'storage_filename' => $this->text($input, 'storage_filename', 220),
            'file_size_bytes' => isset($input['file_size_bytes']) ? max(0, (int) $input['file_size_bytes']) : null,
            'pdf_blob' => is_string($input['pdf_blob'] ?? null) ? $input['pdf_blob'] : null];
    }

    private function insert(string $appraisalId, int $owner, array $data, int $version, string $now): void
    {
        $columns = array_keys($data);
        $marks = implode(', ', array_fill(0, count($columns) + 4, '?'));
        $sql = 'INSERT INTO appraisal_urban_norm_profiles (appraisal_id, owner_id, '
            . implode(', ', $columns) . ', version, updated_at) VALUES (' . $marks . ')';
        $this->db->prepare($sql)->execute([$appraisalId, $owner, ...array_values($data), $version, $now]);
    }

    private function update(string $appraisalId, int $owner, array $data, int $version, string $now): void
    {
        $assignments = implode(', ', array_map(static fn (string $key): string => $key . ' = ?', array_keys($data)));
        $query = $this->db->prepare('UPDATE appraisal_urban_norm_profiles SET ' . $assignments
            . ', version = ?, updated_at = ? WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([...array_values($data), $version, $now, $appraisalId, $owner]);
    }

    private function exists(string $appraisalId, int $owner): bool
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM appraisal_urban_norm_profiles WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        return (int) $query->fetchColumn() > 0;
    }

    private function defaults(string $appraisalId, int $owner): array
    {
        return ['appraisal_id' => $appraisalId, 'owner_id' => $owner, 'cadastral_reference' => '',
            'cadastral_reference_short' => '', 'cadastral_reference_long' => '', 'document_slug' => '',
            'table_slug' => '', 'category_slug' => '', 'source_status' => 'pendiente',
            'pot_state' => '', 'midas_consulted' => 0, 'midas_query_option' => 'Uso del suelo',
            'midas_consulted_on' => null, 'midas_layers' => '', 'midas_usage_result' => '',
            'midas_activity' => '', 'midas_support_reference' => '', 'midas_result' => '',
            'midas_predio_raw' => '', 'midas_usage_raw' => '', 'use_regulation_table' => '',
            'use_principal_text' => '', 'use_compatible_text' => '', 'use_complementary_text' => '',
            'use_restricted_text' => '', 'use_prohibited_text' => '',
            'norm_unit_basic_text' => '', 'norm_free_area_text' => '', 'norm_min_lot_front_text' => '',
            'norm_max_height_text' => '', 'norm_construction_index_text' => '',
            'norm_isolation_text' => '', 'norm_other_potential_text' => '',
            'planning_concept_number' => '', 'planning_concept_date' => null, 'official_concept_scope' => '',
            'land_classification' => '', 'activity_area' => '', 'normative_zone' => '', 'urban_treatment' => '',
            'urban_license' => '', 'permitted_use' => '', 'current_use' => '', 'intended_use' => '',
            'applicable_activity' => '', 'urban_norms_applied' => '',
            'heritage_context' => '', 'environmental_context' => '', 'risk_context' => '',
            'use_cross_result' => '', 'restrictions' => '', 'legal_urban_affectations' => '',
            'conclusion' => '', 'support_summary' => '', 'analyst_notes' => '',
            'source_limitations' => '', 'version' => 0, 'updated_at' => null];
    }

    private function date(mixed $value): ?string
    { $date = trim((string) $value); return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? $date : null; }

    private function nullableText(array $input, string $key, int $limit): ?string
    { $value = $this->text($input, $key, $limit); return $value === '' ? null : $value; }

    private function text(array $input, string $key, int $limit): string
    { return mb_substr(trim((string) ($input[$key] ?? '')), 0, $limit); }
}

