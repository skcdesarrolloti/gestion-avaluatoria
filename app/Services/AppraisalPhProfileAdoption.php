<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhProfileAdoption
{
    public function adopt(array $current, array $source): array
    {
        $data = array_replace($current, $source);
        foreach (['private_unit', 'coefficient', 'monthly_fee', 'fee_status'] as $key) {
            $data[$key] = (string) ($current[$key] ?? '');
        }
        $data['linkage'] = $this->linkage(
            is_array($current['linkage'] ?? null) ? $current['linkage'] : [],
            is_array($source['linkage'] ?? null) ? $source['linkage'] : [],
            (string) ($source['ph_name'] ?? '')
        );
        $data['technical'] = $this->technical(is_array($source['technical'] ?? null) ? $source['technical'] : []);
        foreach (['diagnosis_text', 'report_text'] as $key) $data[$key] = '';
        foreach (['appraisal_id', 'owner_id', 'created_at', 'updated_at', 'version'] as $key) unset($data[$key]);
        return $data;
    }

    private function linkage(array $current, array $source, string $name): array
    {
        $linkage = $source;
        foreach (['legal_registration', 'sector_neighborhood'] as $key) {
            $linkage[$key] = (string) ($current[$key] ?? '');
        }
        if ($name !== '') $linkage['coproperty_name'] = $name;
        return array_filter($linkage, static fn (mixed $value): bool => trim((string) $value) !== '');
    }

    private function technical(array $source): array
    {
        foreach (['parqueadero_relacion_sujeto', 'parqueadero_identificacion_sujeto',
            'ubicacion_unidad'] as $key) {
            unset($source[$key]);
        }
        return $source;
    }
}
