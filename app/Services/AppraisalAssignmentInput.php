<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalCatalog;

final class AppraisalAssignmentInput
{
    public static function data(): array
    {
        $data = [];
        foreach (AppraisalCatalog::assignmentFields() as $field => $limit) {
            if (in_array($field, ['requester_email', 'requester_phone', 'requester_municipality', 'value_date_notes'], true) && !array_key_exists($field, $_POST)) continue;
            $data[$field] = mb_substr(trim((string) ($_POST[$field] ?? '')), 0, $limit);
        }
        if (($data['requester_email'] ?? '') !== '' && !filter_var($data['requester_email'], FILTER_VALIDATE_EMAIL)) {
            throw new \App\Core\HttpException(422, 'Revisa el correo del solicitante.', ['requester_email' => 'Escribe un correo válido.']);
        }
        $data['income_producing'] = self::select($_POST['income_producing'] ?? '', ['', 'si', 'no', 'pendiente']);
        $data['rent_period'] = self::select($_POST['rent_period'] ?? '', ['', 'mensual', 'trimestral', 'anual', 'otro']);
        $data['rent_charges_vat'] = self::select($_POST['rent_charges_vat'] ?? '', ['', 'si', 'no', 'no_aplica', 'pendiente']);
        $data['income_notes'] = mb_substr(trim((string) ($_POST['income_notes'] ?? '')), 0, 1500);
        $data['rent_amount'] = self::moneyOrNull($_POST['rent_amount'] ?? '');
        $data['ph_admin_fee_amount'] = self::moneyOrNull($_POST['ph_admin_fee_amount'] ?? '');
        foreach (['request_date', 'visit_date', 'value_date', 'report_date'] as $field) {
            $value = trim((string) ($_POST[$field] ?? ''));
            $data[$field] = preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null;
        }
        $posted = $_POST['source_documents_selected'] ?? [];
        $allowed = array_keys(AppraisalCatalog::sourceDocumentOptions());
        $selected = is_array($posted) ? array_intersect(array_map('strval', $posted), $allowed) : [];
        $data['source_documents_json'] = json_encode(array_values(array_unique($selected)), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        return $data;
    }

    private static function select(mixed $value, array $allowed): string
    {
        $value = trim((string) $value);
        return in_array($value, $allowed, true) ? $value : '';
    }

    private static function moneyOrNull(mixed $value): ?string
    {
        $text = trim((string) $value);
        if ($text === '') return null;
        $clean = preg_replace('/[^\d,.]/', '', $text) ?? '';
        $lastComma = strrpos($clean, ',');
        $lastDot = strrpos($clean, '.');
        if ($lastComma !== false && ($lastDot === false || $lastComma > $lastDot)) {
            $normalized = str_replace('.', '', $clean);
            $normalized = str_replace(',', '.', $normalized);
        } elseif ($lastDot !== false && preg_match('/^\d{1,3}(\.\d{3})+$/', $clean)) {
            $normalized = str_replace('.', '', $clean);
        } else {
            $normalized = str_replace(',', '', $clean);
        }
        if (!preg_match('/^\d{1,12}(\.\d{1,2})?$/', $normalized)) return null;
        return number_format((float) $normalized, 2, '.', '');
    }
}
