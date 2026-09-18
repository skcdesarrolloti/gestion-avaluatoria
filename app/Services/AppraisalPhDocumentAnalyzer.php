<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalPhCatalog;

final class AppraisalPhDocumentAnalyzer
{
    public function analyze(string $text, array $fileNames, string $typology): array
    {
        $technical = [];
        foreach ($this->rules() as $key => $needles) {
            $snippet = $this->snippet($text, $needles);
            if ($snippet !== '') $technical[$key] = $snippet;
        }
        $core = [
            'ph_typology' => $typology,
            'ph_name' => $this->name($text, $fileNames),
            'ph_key' => $this->name($text, $fileNames),
            'matrix_registration' => $this->match('/matr[ií]cula\s+(?:matriz|base)[^\d]*(\d{2,4}-\d{3,9})/iu', $text),
            'coefficient' => $this->match('/coeficiente(?:\s+de\s+copropiedad)?[^\d%]*(\d+(?:[.,]\d+)?\s*%?)/iu', $text),
            'regulation_document' => $this->snippet($text, ['reglamento de propiedad horizontal', 'constitucion de propiedad horizontal']),
            'reform_documents' => $this->snippet($text, ['reforma', 'modificacion al reglamento', 'aclaratoria']),
        ];
        $city = $this->snippet($text, ['cartagena', 'municipio', 'ciudad']);
        $linkage = ['coproperty_name' => $core['ph_name'], 'legal_registration' => $core['matrix_registration']];
        if ($city !== '') $technical['ciudad_municipio'] = $city;
        $filled = count(array_filter($technical, static fn (string $v): bool => trim($v) !== ''));
        $total = count($this->fieldKeys());
        return ['core' => array_filter($core), 'linkage' => array_filter($linkage),
            'technical' => $technical, 'summary' => "$filled de $total campos técnicos sugeridos desde "
                . count($fileNames) . ' archivo(s). Revisa contra el documento original antes del entregable.',
            'findings' => array_values(array_filter([
                $core['ph_name'] ? 'Copropiedad probable: ' . $core['ph_name'] : '',
                $core['matrix_registration'] ? 'Matrícula matriz probable: ' . $core['matrix_registration'] : '',
                $typology ? 'Tipología PH seleccionada: ' . (AppraisalPhCatalog::typologies()[$typology] ?? $typology) : '',
                'Archivos leídos: ' . implode(', ', array_slice($fileNames, 0, 12)),
            ]))];
    }

    private function rules(): array
    {
        return [
            'fuente_documental' => ['escritura publica', 'reglamento de propiedad horizontal', 'documento'],
            'escritura_reforma' => ['escritura', 'notaria', 'acto de referencia'],
            'direccion_referencia' => ['direccion', 'ubicado', 'localizado', 'kilometro'],
            'tipo_propiedad_horizontal' => ['propiedad horizontal', 'sometimiento'],
            'regimen_especial' => ['zona franca', 'usuario operador', 'regimen especial'],
            'naturaleza_conjunto' => ['parque industrial', 'centro logistico', 'conjunto', 'agrupacion'],
            'uso_dominante' => ['uso dominante', 'destinacion', 'actividad principal'],
            'usos_complementarios' => ['usos complementarios', 'usos permitidos'],
            'etapas_copropiedad' => ['etapa', 'sector', 'manzana'],
            'numero_edificios' => ['bloques', 'torres', 'naves', 'edificios'],
            'numero_unidades' => ['unidades privadas', 'unidades inmobiliarias'],
            'resumen_areas_conjunto' => ['area privada', 'area comun', 'area construida', 'cuadro de areas'],
            'vias_internas' => ['vias internas', 'circulacion vehicular'],
            'red_contra_incendios' => ['red contra incendios', 'hidrante', 'incendio'],
            'equipamiento_tecnico' => ['subestacion', 'planta electrica', 'bascula', 'cuarto tecnico'],
            'muelles' => ['muelle', 'bahia', 'rampa'],
            'patios_maniobra' => ['patio de maniobra', 'maniobra', 'radio de giro'],
            'usos_restringidos' => ['prohibido', 'restriccion', 'limitacion'],
            'expensas_cuotas' => ['expensas', 'cuota de administracion'],
            'coeficientes_copropiedad' => ['coeficiente de copropiedad', 'coeficientes'],
        ];
    }

    private function fieldKeys(): array
    {
        $keys = [];
        foreach (AppraisalPhCatalog::technicalGroups() as $group) $keys = array_merge($keys, array_keys($group[1]));
        return $keys;
    }

    private function name(string $text, array $fileNames): string
    {
        foreach (['/copropiedad\s+([A-Z0-9ÁÉÍÓÚÑ ._-]{5,80})/u', '/conjunto\s+([A-Z0-9ÁÉÍÓÚÑ ._-]{5,80})/u'] as $pattern) {
            $value = $this->match($pattern, $text);
            if ($value !== '') return mb_substr(trim($value, " .,\n\r\t"), 0, 190);
        }
        $base = pathinfo($fileNames[0] ?? '', PATHINFO_FILENAME);
        return mb_substr(trim((string) preg_replace('/[_-]+/', ' ', $base)), 0, 190);
    }

    private function snippet(string $text, array $needles): string
    {
        $plain = preg_replace('/\s+/', ' ', $text) ?? $text;
        foreach ($needles as $needle) {
            $pos = mb_stripos($plain, $needle);
            if ($pos === false) continue;
            return mb_substr(trim($plain), max(0, $pos - 80), 420);
        }
        return '';
    }

    private function match(string $pattern, string $text): string
    {
        return preg_match($pattern, $text, $match) ? mb_substr(trim((string) ($match[1] ?? '')), 0, 220) : '';
    }
}
