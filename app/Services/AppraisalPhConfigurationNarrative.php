<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhConfigurationNarrative
{
    public function build(array $technical, array $core): string
    {
        $name = $this->clean($core['ph_name'] ?? '') ?: 'la copropiedad analizada';
        $parts = $this->present(['lote matriz' => $technical['lotes_por_etapa'] ?? '',
            'área del lote matriz' => $technical['area_lote_matriz'] ?? '',
            'área construida o total' => ($technical['area_construida_total'] ?? '') ?: ($technical['resumen_areas_conjunto'] ?? ''),
            'unidades privadas' => $technical['numero_unidades'] ?? '', 'oficinas' => $technical['numero_oficinas'] ?? '',
            'locales' => $technical['numero_locales'] ?? '', 'parqueaderos' => $technical['numero_parqueaderos'] ?? '',
            'depósitos' => $technical['numero_depositos'] ?? '', 'bloques, torres, edificios o naves' => $technical['numero_edificios'] ?? '',
            'pisos o niveles' => $technical['numero_pisos'] ?? '', 'sótanos' => $technical['numero_sotanos'] ?? '',
            'ascensores' => $technical['numero_ascensores'] ?? '', 'etapas, sectores o manzanas' => $technical['etapas_copropiedad'] ?? '',
            'organización interna' => $technical['organizacion_interna'] ?? '',
            'desenglobes o antecedentes prediales' => $technical['desarrollos_relevantes'] ?? '']);
        $summary = "La copropiedad {$name} presenta una configuración predial que permite relacionar el bien sujeto con la estructura física y jurídica de la propiedad horizontal.";
        $summary .= $parts !== '' ? " El soporte disponible identifica {$parts}, información útil para entender escala, organización interna y relación entre áreas privadas y bienes comunes."
            : ' La escala, organización interna y cuadro de áreas deben completarse antes de trasladar la descripción al informe.';
        return $summary . ($this->has($technical['ubicacion_unidad'] ?? '')
            ? ' La unidad objeto cuenta con referencia de ubicación dentro de la copropiedad.'
            : ' Falta precisar la ubicación de la unidad objeto dentro de la copropiedad.');
    }
    private function present(array $values): string
    {
        $names = [];
        foreach ($values as $label => $value) if ($this->has($value)) $names[] = $label;
        return implode(', ', $names);
    }
    private function has(mixed $value): bool { return trim((string) $value) !== ''; }
    private function clean(mixed $value): string { return trim(preg_replace('/\s+/u', ' ', (string) $value) ?? ''); }
}
