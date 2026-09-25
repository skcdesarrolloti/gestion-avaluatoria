<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\UrbanNormativeRepository;

final class AppraisalUrbanNormMidasFields
{
    public function __construct(private UrbanNormativeRepository $library) {}

    public function usageReference(array $predio, string $fallback): string
    {
        foreach (['usage_reference', 'cadastral_reference', 'national_cadastral_reference'] as $key) {
            $digits = preg_replace('/\D+/', '', (string) ($predio[$key] ?? '')) ?? '';
            if ($digits !== '') return $digits;
        }
        return $fallback;
    }

    public function withMatchedCategory(array $fields): array
    {
        $slug = (new UrbanNormMidasCategoryMatcher())->slug(implode(' ', [$fields['midas_activity'] ?? '',
            $fields['current_use'] ?? '', $fields['midas_usage_result'] ?? '', $fields['use_regulation_table'] ?? '']));
        if ($slug === '') return $fields;
        $nonEmpty = array_filter($fields, static fn ($value): bool => trim((string) $value) !== '');
        try { return array_replace((new UrbanNormCategoryAdoption())->fields($this->library->categoryWithRules($slug)), $nonEmpty, ['category_slug' => $slug]); }
        catch (\Throwable $error) { error_log('Gestion avaluatoria match cuadro urbano ' . get_class($error)); return $fields; }
    }

    public function profileFieldsFromPredio(array $predio): array
    {
        $fields = [];
        foreach (['cadastral_reference_long' => 'national_cadastral_reference', 'cadastral_reference_short' => 'cadastral_reference',
            'current_use' => 'land_use', 'land_classification' => 'land_classification', 'urban_treatment' => 'urban_treatment'] as $target => $source) {
            if (($predio[$source] ?? '') !== '') $fields[$target] = (string) $predio[$source];
        }
        if (($predio['risk'] ?? '') !== '') $fields += ['risk_context' => (string) $predio['risk'],
            'restrictions' => (string) $predio['risk'], 'legal_urban_affectations' => (string) $predio['risk']];
        if (($predio['_raw'] ?? '') !== '') $fields['midas_predio_raw'] = (string) $predio['_raw'];
        if (($predio['land_use'] ?? '') !== '') {
            $fields['midas_activity'] = (string) $predio['land_use'];
            $fields['midas_usage_result'] = $this->predioSummary($predio);
        }
        return $fields;
    }

    public function consultMessage(array $predio, array $usage, array $fields): string
    {
        $messages = [];
        if (($predio['ok'] ?? false)) $messages[] = 'Predios actualizado en el numeral 3';
        elseif (($predio['message'] ?? '') !== '') $messages[] = (string) $predio['message'];
        if (($usage['ok'] ?? false)) $messages[] = 'Uso Suelo guardado en el numeral 5';
        elseif (($usage['message'] ?? '') !== '') $messages[] = (string) $usage['message'];
        if (str_contains(implode(' ', $messages), 'Cloudflare')) {
            return 'Consulta MIDAS: MIDAS bloqueó la lectura automática desde Hostinger. Usa el respaldo abierto en 5.1 para pegar la ficha Predios o Uso Suelo.';
        }
        if (($fields['category_slug'] ?? '') !== '') $messages[] = 'cuadro POT aplicado automáticamente';
        return $messages ? 'Consulta MIDAS: ' . implode('; ', $messages) . '.' : 'MIDAS no devolvió información para guardar.';
    }

    private function predioSummary(array $predio): string
    {
        $labels = ['land_use' => 'Uso de suelo', 'urban_treatment' => 'Tratamiento', 'risk' => 'Riesgos',
            'land_classification' => 'Clasificación', 'updated_on' => 'Actualización'];
        $parts = [];
        foreach ($labels as $key => $label) if (($predio[$key] ?? '') !== '') $parts[] = $label . ': ' . $predio[$key];
        return implode(' | ', $parts);
    }
}
