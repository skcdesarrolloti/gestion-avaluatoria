<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalPhCatalog;

final class AppraisalPhInput
{
    public static function data(): array
    {
        $posted = is_array($_POST['ph'] ?? null) ? $_POST['ph'] : [];
        return [
            'ph_key' => self::short($posted['ph_key'] ?? '', 190),
            'ph_name' => self::short($posted['ph_name'] ?? '', 190),
            'administration_name' => self::short($posted['administration_name'] ?? '', 190),
            'administration_contact' => self::short($posted['administration_contact'] ?? '', 190),
            'administration_phone' => self::short($posted['administration_phone'] ?? '', 80),
            'administration_email' => self::short($posted['administration_email'] ?? '', 190),
            'matrix_registration' => self::short($posted['matrix_registration'] ?? '', 120),
            'private_unit' => self::short($posted['private_unit'] ?? '', 190),
            'coefficient' => self::short($posted['coefficient'] ?? '', 80),
            'regulation_document' => self::text($posted['regulation_document'] ?? '', 1500),
            'reform_documents' => self::text($posted['reform_documents'] ?? '', 2000),
            'monthly_fee' => self::short($posted['monthly_fee'] ?? '', 80),
            'fee_status' => self::short($posted['fee_status'] ?? '', 60),
            'reserve_fund' => self::short($posted['reserve_fund'] ?? '', 120),
            'insurance_status' => self::short($posted['insurance_status'] ?? '', 120),
            'restrictions_text' => self::text($posted['restrictions_text'] ?? '', 2000),
            'common_areas' => self::statusMap($posted['common_areas'] ?? [], AppraisalPhCatalog::commonAreas()),
            'documents' => self::statusMap($posted['documents'] ?? [], AppraisalPhCatalog::documents()),
            'risks' => self::statusMap($posted['risks'] ?? [], AppraisalPhCatalog::risks()),
            'photos' => self::statusMap($posted['photos'] ?? [], AppraisalPhCatalog::photos()),
            'diagnosis_text' => self::text($posted['diagnosis_text'] ?? '', 2500),
            'report_text' => self::text($posted['report_text'] ?? '', 3000),
        ];
    }

    private static function statusMap(mixed $values, array $allowed): array
    {
        if (!is_array($values)) return [];
        $validStatuses = array_keys(AppraisalPhCatalog::statusOptions());
        $clean = [];
        foreach ($allowed as $key => $label) {
            $row = is_array($values[$key] ?? null) ? $values[$key] : [];
            $status = self::short($row['status'] ?? '', 20);
            if (!in_array($status, $validStatuses, true)) $status = '';
            $notes = self::text($row['notes'] ?? '', 500);
            if ($status !== '' || $notes !== '') $clean[$key] = ['status' => $status, 'notes' => $notes];
        }
        return $clean;
    }

    private static function short(mixed $value, int $limit): string
    {
        return mb_substr(trim((string) $value), 0, $limit);
    }

    private static function text(mixed $value, int $limit): string
    {
        return mb_substr(trim((string) $value), 0, $limit);
    }
}
