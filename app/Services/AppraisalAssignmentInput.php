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
            $data[$field] = mb_substr(trim((string) ($_POST[$field] ?? '')), 0, $limit);
        }
        if ($data['requester_name'] === '') {
            $data['requester_name'] = $data['client_name'];
        }
        foreach (['visit_date', 'value_date', 'report_date'] as $field) {
            $value = trim((string) ($_POST[$field] ?? ''));
            $data[$field] = preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null;
        }
        return $data;
    }
}
