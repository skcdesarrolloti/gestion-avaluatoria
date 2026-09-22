<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalPhCatalog;

final class AppraisalPhOperationsNarrative
{
    public function rules(string $name, array $technical, array $core, string $typology): string
    {
        $labels = ['usos_permitidos' => 'usos permitidos', 'usos_restringidos' => 'restricciones o prohibiciones',
            'reglas_constructivas' => 'reglas constructivas y adecuaciones',
            'condiciones_normativas_operativas' => 'condiciones operativas',
            'condiciones_usuario_operador' => 'usuario operador o administración',
            'cargue_descargue' => 'cargue, descargue y movilidad'];
        $directKeys = array_fill_keys(AppraisalPhCatalog::technicalApplicability()[$typology] ?? [], true);
        $direct = $extra = [];
        if ($this->has($core['restrictions_text'] ?? '')) $direct[] = $this->item('restricciones generales de uso u operación', $core['restrictions_text']);
        foreach ($labels as $key => $label) {
            if (!$this->has($technical[$key] ?? '')) continue;
            $row = $this->item($label, $technical[$key]);
            (isset($directKeys[$key]) || in_array($key, ['usos_permitidos', 'usos_restringidos', 'reglas_constructivas'], true)) ? $direct[] = $row : $extra[] = $row;
        }
        if (!$direct && !$extra) return "Las reglas de uso y operación de {$name} aún requieren depuración del reglamento, la visita o los soportes del encargo antes de incorporarse al Entregable.";
        $summary = "Las reglas de uso y operación de {$name} registran " . implode('; ', array_unique($direct ?: $extra)) . '.';
        if ($extra) $summary .= ' Como condiciones complementarias por confirmar o aplicar según la unidad se registran ' . implode('; ', array_unique($extra)) . '.';
        return $summary . ' Estos hallazgos orientan el uso admisible, las adecuaciones, la operación cotidiana, la imagen del inmueble y sus condiciones de comercialización dentro de la copropiedad.';
    }

    public function administration(string $name, array $core, array $technical): string
    {
        $rows = ['administración' => $core['administration_name'] ?? '', 'cuota de administración' => $core['monthly_fee'] ?? '',
            'estado de expensas o paz y salvo' => $core['fee_status'] ?? '', 'fondo o imprevistos' => $core['reserve_fund'] ?? '',
            'seguros comunes' => $core['insurance_status'] ?? '', 'coeficientes' => $technical['coeficientes_copropiedad'] ?? '',
            'expensas, cuotas y cargas' => $technical['expensas_cuotas'] ?? '', 'responsabilidades sobre bienes comunes' => $technical['responsabilidades_bienes_comunes'] ?? '',
            'cargas que afectan operación o comercialización' => $technical['cargas_comercializacion'] ?? ''];
        $parts = [];
        foreach ($rows as $label => $value) if ($this->has($value)) $parts[] = $this->item($label, $value, 180);
        if (!$parts) return "La administración, expensas y cargas de {$name} requieren soporte vigente de administración, paz y salvo, pólizas y estado actual antes de incorporarse al Entregable.";
        return "Para {$name} se registran " . implode('; ', array_slice($parts, 0, 7)) . '. Estos datos permiten depurar obligaciones económicas, coeficientes, seguros y cargas comunes que pueden incidir en valor, liquidez, negociación y cierre del avalúo.';
    }

    public function incidence(string $name, string $label, array $core, array $technical,
        string $assets, string $support, string $level, string $limits): array
    {
        $rows = ['incidencia funcional' => $technical['incidencia_funcional_ph'] ?? '',
            'incidencia comercial' => $technical['incidencia_comercial_ph'] ?? '',
            'incidencia operativa' => $technical['incidencia_operativa_ph'] ?? '',
            'incidencia por restricciones' => $technical['incidencia_restricciones_regimen'] ?? '',
            'incidencia por cargas económicas' => $technical['incidencia_cargas_ph'] ?? '',
            'comparación con PH similares' => $technical['comparacion_mercado_ph'] ?? '',
            'conclusión para valor' => $technical['conclusion_valor_ph'] ?? ''];
        $parts = [];
        foreach ($rows as $labelRow => $value) if ($this->has($value)) $parts[] = $this->item($labelRow, $value, 180);
        $fallback = "La ubicación del bien dentro de {$name} aporta soporte común {$level}, con efectos posibles en funcionalidad, deseabilidad, operación y comparación frente a copropiedades similares.";
        $summary = $parts ? "La incidencia valuatoria de {$name} registra " . implode('; ', array_slice($parts, 0, 6)) . '.' : $fallback;
        $report = "El inmueble objeto de medición se localiza en {$name}, copropiedad analizada como {$label}. {$assets} "
            . ($parts ? $summary : $fallback) . " La copropiedad cuenta con una dotación común {$level}; {$support} {$limits}";
        return [$summary, $report];
    }

    public function notes(array $technical): string
    {
        $rows = ['marco normativo PH' => $technical['notas_normativas_ph'] ?? '',
            'salvedades del reglamento' => $technical['salvedades_reglamento'] ?? '',
            'salvedades de visita' => $technical['salvedades_visita'] ?? '',
            'salvedades de validación documental' => $technical['salvedades_validacion'] ?? '',
            'observaciones de lectura OCR' => $technical['observaciones_extraccion'] ?? ''];
        $parts = [];
        foreach ($rows as $label => $value) if ($this->has($value)) $parts[] = $this->item($label, $value, 170);
        if (!$parts) return 'Notas normativas: completar normas pertinentes, salvedades de reglamento, visita, soportes pendientes y límites del análisis técnico antes de cerrar el Entregable.';
        return 'Notas normativas y salvedades: ' . implode('; ', array_slice($parts, 0, 5)) . '. Estas notas delimitan el alcance técnico del avalúo y evitan presentar la lectura PH como estudio de títulos o certificación administrativa.';
    }

    private function item(string $label, mixed $value, int $limit = 210): string
    { $text = $this->evidence($value, $limit); return $label . ($text !== '' ? ': ' . $text : ''); }
    private function evidence(mixed $value, int $limit): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', preg_replace('/\[[^\]]+\]/u', ' ', (string) $value) ?? '') ?? '');
        $text = trim(preg_replace('/[-_=]{2,}|\s+\|\s+|\bcontin[uú]a\b/iu', ' ', $text) ?? '', ' .;:-—');
        return mb_strlen($text) > $limit ? mb_substr($text, 0, max(0, $limit - 3)) . '…' : $text;
    }
    private function has(mixed $value): bool { return trim((string) $value) !== ''; }
}
