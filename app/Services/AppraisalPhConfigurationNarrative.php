<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhConfigurationNarrative
{
    public function build(array $technical, array $core): string
    {
        $name = $this->clean($core['ph_name'] ?? '') ?: 'la copropiedad analizada';
        $age = $this->presentValues(['fecha o año de registro/constitución PH' => $technical['fecha_reglamento_ph'] ?? '',
            'edad aproximada de la copropiedad' => $technical['edad_aproximada_ph'] ?? '']);
        $scale = $this->presentValues(['lote matriz' => $technical['lotes_por_etapa'] ?? '',
            'área del lote matriz' => $technical['area_lote_matriz'] ?? '',
            'área construida o total' => ($technical['area_construida_total'] ?? '') ?: ($technical['resumen_areas_conjunto'] ?? '')]);
        $inventory = $this->presentRaw([$technical['numero_unidades'] ?? '', $technical['numero_oficinas'] ?? '',
            $technical['numero_locales'] ?? '', $technical['numero_parqueaderos'] ?? '', $technical['numero_depositos'] ?? '',
            $technical['numero_edificios'] ?? '', $technical['numero_pisos'] ?? '', $technical['numero_sotanos'] ?? '',
            $technical['numero_ascensores'] ?? '']);
        $organization = $this->presentValues(['etapas, sectores o manzanas' => $technical['etapas_copropiedad'] ?? '',
            'distribución funcional interna' => $technical['organizacion_interna'] ?? '',
            'desenglobes o antecedentes prediales' => $technical['desarrollos_relevantes'] ?? '']);
        $summary = "La copropiedad {$name} presenta una configuración predial que permite relacionar el bien sujeto con la estructura física y jurídica de la propiedad horizontal.";
        if ($age !== '') $summary .= " Como antecedente temporal se registra {$age}.";
        if ($scale !== '') $summary .= " En escala predial se identifica {$scale}.";
        if ($inventory !== '') $summary .= " La composición reportada incluye {$inventory}.";
        if ($organization !== '') $summary .= " La organización interna registra {$organization}.";
        if ($scale === '' && $inventory === '' && $organization === '') $summary .= ' La escala, organización interna y cuadro de áreas deben completarse antes de trasladar la descripción al informe.';
        return $summary . ($this->has($technical['ubicacion_unidad'] ?? '')
            ? ' La unidad objeto cuenta con referencia de ubicación dentro de la copropiedad.'
            : ' Falta precisar la ubicación de la unidad objeto dentro de la copropiedad.');
    }
    private function presentValues(array $values): string
    {
        $items = [];
        foreach ($values as $label => $value) if (($value = $this->usable($value)) !== '') $items[] = $label . ': ' . $value;
        return implode('; ', $items);
    }
    private function presentRaw(array $values): string
    {
        $items = [];
        foreach ($values as $value) if (($value = $this->usable($value)) !== '') $items[] = $value;
        return implode(', ', $items);
    }
    private function usable(mixed $value): string
    {
        $value = $this->clean($value);
        return $value !== '' && !str_contains($value, '[') && mb_strlen($value) <= 90 ? $value : '';
    }
    private function has(mixed $value): bool { return trim((string) $value) !== ''; }
    private function clean(mixed $value): string { return trim(preg_replace('/\s+/u', ' ', (string) $value) ?? ''); }
}
