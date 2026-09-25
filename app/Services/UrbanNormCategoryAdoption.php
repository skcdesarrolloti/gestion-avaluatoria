<?php
declare(strict_types=1);
namespace App\Services;

final class UrbanNormCategoryAdoption
{
    public function fields(array $category): array
    {
        $rules = is_array($category['rules'] ?? null) ? $category['rules'] : [];
        $params = is_array($category['parameters'] ?? null) ? $category['parameters'] : [];
        $table = trim((string) ($category['table_code'] ?? '') . ' · ' . (string) ($category['table_title'] ?? ''), ' ·');
        $activity = trim((string) ($category['code'] ?? '') . ' · ' . (string) ($category['name'] ?? ''), ' ·');
        $fields = [
            'document_slug' => (string) ($category['document_slug'] ?? ''),
            'table_slug' => (string) ($category['table_slug'] ?? ''),
            'category_slug' => (string) ($category['slug'] ?? ''),
            'source_status' => 'soportado',
            'use_regulation_table' => $table,
            'applicable_activity' => $activity,
            'use_principal_text' => (string) ($rules['principal'] ?? ''),
            'use_compatible_text' => (string) ($rules['compatible'] ?? ''),
            'use_complementary_text' => (string) ($rules['complementario'] ?? ''),
            'use_restricted_text' => (string) ($rules['restringido'] ?? ''),
            'use_prohibited_text' => (string) ($rules['prohibido'] ?? ''),
            'norm_unit_basic_text' => $this->param($params, 'unidad_basica'),
            'norm_free_area_text' => $this->param($params, 'area_libre'),
            'norm_min_lot_front_text' => $this->param($params, 'area_frente_minimos'),
            'norm_max_height_text' => $this->param($params, 'altura_maxima'),
            'norm_construction_index_text' => $this->param($params, 'indice_construccion'),
            'norm_isolation_text' => $this->param($params, 'aislamientos'),
            'urban_norms_applied' => trim((string) ($category['document_title'] ?? '') . "\n" . $table),
            'permitted_use' => $this->permittedUse($rules),
        ];
        return array_filter($fields, static fn (string $value): bool => trim($value) !== '');
    }

    private function param(array $params, string $key): string
    {
        $row = is_array($params[$key] ?? null) ? $params[$key] : [];
        return (string) ($row['value'] ?? '');
    }

    private function permittedUse(array $rules): string
    {
        $labels = ['principal' => 'Principal', 'compatible' => 'Compatible',
            'complementario' => 'Complementario', 'restringido' => 'Restringido', 'prohibido' => 'Prohibido'];
        $lines = [];
        foreach ($labels as $key => $label) {
            $value = trim((string) ($rules[$key] ?? ''));
            if ($value !== '') $lines[] = $label . ': ' . $value;
        }
        return implode("\n", $lines);
    }
}
