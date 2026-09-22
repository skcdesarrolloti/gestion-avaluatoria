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
