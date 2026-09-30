<?php
declare(strict_types=1);
namespace App\Models;
use PDO;

final class AppraisalComparableRepository
{
    public function __construct(private PDO $db) {}

    public function forAppraisal(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_comparables
            WHERE appraisal_id = ? AND owner_id = ? ORDER BY sample_index');
        $query->execute([$appraisalId, $owner]);
        return $query->fetchAll();
    }

    public function saveAll(string $appraisalId, int $owner, array $rows): void
    {
        $this->db->beginTransaction();
        try {
            $this->db->prepare('DELETE FROM appraisal_comparables WHERE appraisal_id = ? AND owner_id = ?')
                ->execute([$appraisalId, $owner]);
            foreach (array_slice(array_values($rows), 0, 60) as $index => $row) {
                if (!is_array($row) || !$this->meaningful($row)) continue;
                $this->insert($appraisalId, $owner, $index + 1, $row);
            }
            $this->db->commit();
        } catch (\Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    private function insert(string $appraisalId, int $owner, int $index, array $row): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $sql = 'INSERT INTO appraisal_comparables
            (id, appraisal_id, owner_id, sample_index, active, status, source_type, source_name,
            source_url, query_used, operation, property_type, neighborhood, address_hint,
            project_name, price_amount, price_unit, area_m2, admin_fee, vat_applies, bedrooms,
            bathrooms, parking_spaces, floor_level, contact_name, contact_phone, listing_code,
            listing_date, consulted_at, stratum, age_years, building_condition, conservation_state,
            view_quality, finish_quality, elevator, amenities, security_features, power_plant,
            parking_relation, balcony_terrace, noise_humidity_sun, legal_relation_notes,
            analysis_factor, latitude, longitude, location_precision, map_notes,
            comparability_notes, rejection_reason, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $this->db->prepare($sql)->execute([
            $this->id($row['id'] ?? ''), $appraisalId, $owner, $index, $this->choice($row['active'] ?? '', ['si', 'no'], 'si'),
            $this->text($row['status'] ?? 'por_verificar', 40), $this->text($row['source_type'] ?? '', 40),
            $this->text($row['source_name'] ?? '', 140), $this->text($row['source_url'] ?? '', 700),
            $this->text($row['query_used'] ?? '', 500), $this->text($row['operation'] ?? '', 40),
            $this->text($row['property_type'] ?? '', 80), $this->text($row['neighborhood'] ?? '', 140),
            $this->text($row['address_hint'] ?? '', 180), $this->text($row['project_name'] ?? '', 180),
            $this->decimal($row['price_amount'] ?? null), $this->text($row['price_unit'] ?? '', 40),
            $this->decimal($row['area_m2'] ?? null), $this->decimal($row['admin_fee'] ?? null),
            $this->text($row['vat_applies'] ?? '', 20), $this->int($row['bedrooms'] ?? null),
            $this->int($row['bathrooms'] ?? null), $this->int($row['parking_spaces'] ?? null),
            $this->text($row['floor_level'] ?? '', 40), $this->text($row['contact_name'] ?? '', 120),
            $this->text($row['contact_phone'] ?? '', 80), $this->text($row['listing_code'] ?? '', 120),
            $this->date($row['listing_date'] ?? null), $this->date($row['consulted_at'] ?? null),
            $this->text($row['stratum'] ?? '', 20), $this->int($row['age_years'] ?? null),
            $this->text($row['building_condition'] ?? '', 80), $this->text($row['conservation_state'] ?? '', 80),
            $this->text($row['view_quality'] ?? '', 80), $this->text($row['finish_quality'] ?? '', 80),
            $this->text($row['elevator'] ?? '', 20), $this->text($row['amenities'] ?? '', 240),
            $this->text($row['security_features'] ?? '', 180), $this->text($row['power_plant'] ?? '', 80),
            $this->text($row['parking_relation'] ?? '', 120), $this->text($row['balcony_terrace'] ?? '', 120),
            $this->text($row['noise_humidity_sun'] ?? '', 180), $this->text($row['legal_relation_notes'] ?? '', 300),
            $this->text($row['analysis_factor'] ?? '', 100), $this->coordinate($row['latitude'] ?? null, -90, 90),
            $this->coordinate($row['longitude'] ?? null, -180, 180), $this->text($row['location_precision'] ?? '', 40),
            $this->text($row['map_notes'] ?? '', 300),
            $this->body($row['comparability_notes'] ?? ''), $this->body($row['rejection_reason'] ?? ''),
            $now, $now,
        ]);
    }

    private function meaningful(array $row): bool
    {
        foreach (['source_name', 'source_url', 'price_amount', 'area_m2', 'neighborhood', 'project_name', 'comparability_notes', 'analysis_factor', 'latitude', 'longitude'] as $field) {
            if (trim((string) ($row[$field] ?? '')) !== '') return true;
        }
        return false;
    }

    private function id(mixed $value): string
    { $text = strtolower(trim((string) $value)); return preg_match('/^[a-f0-9]{32}$/', $text) ? $text : bin2hex(random_bytes(16)); }
    private function text(mixed $value, int $max): string
    { return mb_substr(trim(preg_replace('/\s+/u', ' ', (string) $value) ?? ''), 0, $max); }
    private function body(mixed $value): string
    { return mb_substr(trim((string) $value), 0, 1600); }
    private function choice(mixed $value, array $allowed, string $default): string
    { $text = strtolower($this->text($value, 20)); return in_array($text, $allowed, true) ? $text : $default; }
    private function int(mixed $value): ?int
    { $text = preg_replace('/\D+/', '', (string) $value) ?? ''; return $text === '' ? null : min(999, (int) $text); }
    private function date(mixed $value): ?string
    { $text = trim((string) $value); return preg_match('/^\d{4}-\d{2}-\d{2}$/', $text) ? $text : null; }
    private function decimal(mixed $value): ?string
    {
        $text = trim((string) $value);
        if ($text === '') return null;
        $text = preg_replace('/[^\d,.-]/', '', $text) ?? '';
        if (strrpos($text, ',') > strrpos($text, '.')) {
            $text = str_replace('.', '', $text);
            $text = str_replace(',', '.', $text);
        } else {
            $text = preg_match('/^\d{1,3}(\.\d{3})+$/', $text) ? str_replace('.', '', $text) : str_replace(',', '', $text);
        }
        return is_numeric($text) ? number_format((float) $text, 2, '.', '') : null;
    }
    private function coordinate(mixed $value, float $min, float $max): ?string
    {
        $text = str_replace(',', '.', trim((string) $value));
        if ($text === '' || !is_numeric($text)) return null;
        $number = (float) $text;
        return $number >= $min && $number <= $max ? number_format($number, 7, '.', '') : null;
    }
}
