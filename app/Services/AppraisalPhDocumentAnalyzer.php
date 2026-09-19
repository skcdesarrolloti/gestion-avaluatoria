<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalPhCatalog;

final class AppraisalPhDocumentAnalyzer
{
    public function analyze(string $text, array $fileNames, string $typology): array
    {
        $hasText = trim($text) !== '';
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
            'private_unit' => $this->match('/(?:unidad\s+privada|bodega|local|oficina)\s*(?:nro\.?|no\.?|n[°º])?\s*([A-Z0-9 -]{1,30})/iu', $text),
            'coefficient' => $this->match('/coeficiente(?:\s+de\s+copropiedad)?[^\d%]*(\d+(?:[.,]\d+)?\s*%?)/iu', $text),
            'regulation_document' => $this->snippet($text, ['reglamento de propiedad horizontal', 'constitucion de propiedad horizontal']),
            'reform_documents' => $this->snippet($text, ['reforma', 'modificacion al reglamento', 'aclaratoria']),
            'monthly_fee' => $this->match('/(?:cuota|expensa)(?:\s+de\s+administraci[oó]n|\s+com[uú]n)?[^\d$]*(\$?\s*\d[\d.,]*)/iu', $text),
            'restrictions_text' => $this->snippet($text, ['prohibido', 'restriccion', 'limitacion de uso', 'usos restringidos']),
        ];
        $city = $this->snippet($text, ['cartagena', 'municipio', 'ciudad']);
        $linkage = ['coproperty_name' => $core['ph_name'], 'legal_registration' => $core['matrix_registration']];
        if ($city !== '') $technical['ciudad_municipio'] = $city;
        $documents = $this->statusMap($text, [
            'reglamento' => ['reglamento de propiedad horizontal', 'constitucion de propiedad horizontal'],
            'reformas' => ['reforma', 'modificacion al reglamento', 'aclaratoria'],
            'planos_coeficientes' => ['plano', 'coeficiente', 'cuadro de areas'],
            'actas' => ['acta de asamblea', 'asamblea'],
            'polizas' => ['poliza', 'seguro'],
        ]);
        $common = $this->statusMap($text, [
            'porteria' => ['porteria', 'control de acceso', 'vigilancia'],
            'vias_internas' => ['vias internas', 'circulacion vehicular'],
            'red_incendio' => ['red contra incendios', 'hidrante', 'gabinete contra incendio'],
            'parqueaderos_visitantes' => ['parqueaderos visitantes', 'estacionamientos visitantes'],
            'planta_electrica' => ['planta electrica', 'subestacion'],
            'cerramiento' => ['cerramiento', 'perimetral'],
        ]);
        $risks = $this->statusMap($text, [
            'restricciones_uso' => ['prohibido', 'usos restringidos', 'restriccion de uso'],
            'mora_expensas' => ['mora', 'expensas pendientes', 'cuotas pendientes'],
            'cuotas_extraordinarias' => ['cuota extraordinaria', 'expensa extraordinaria'],
            'deterioro_comunes' => ['deterioro', 'mantenimiento diferido'],
            'seguros' => ['seguro', 'poliza'],
        ]);
        $core['diagnosis_text'] = $this->diagnosis($core, $technical, $risks);
        $core['report_text'] = $this->reportText($core, $technical, $typology);
        $filled = count(array_filter($technical, static fn (string $v): bool => trim($v) !== ''));
        $total = count($this->fieldKeys());
        return ['core' => array_filter($core), 'linkage' => array_filter($linkage),
            'technical' => $technical, 'common_areas' => $common, 'documents' => $documents, 'risks' => $risks,
            'summary' => $hasText
                ? "$filled de $total campos técnicos sugeridos desde " . count($fileNames) . ' archivo(s). Revisa contra el documento original antes del entregable.'
                : 'No se extrajo texto útil del soporte PH. El archivo quedó registrado, pero debes diligenciar manualmente o subir un PDF con texto/OCR, DOCX o TXT.',
            'findings' => array_values(array_filter([
                !$hasText ? 'Sin texto extraíble: el PDF puede estar escaneado, protegido o no tener OCR.' : '',
                $core['ph_name'] ? 'Copropiedad probable: ' . $core['ph_name'] : '',
                $core['matrix_registration'] ? 'Matrícula matriz probable: ' . $core['matrix_registration'] : '',
                $core['coefficient'] ? 'Coeficiente probable: ' . $core['coefficient'] : '',
                $documents ? 'Soportes identificados: ' . implode(', ', array_keys($documents)) : '',
                $risks ? 'Alertas o restricciones por validar: ' . implode(', ', array_keys($risks)) : '',
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
            'relacion_funcional_usos' => ['relacion funcional', 'usos mixtos', 'integracion de usos'],
            'etapas_copropiedad' => ['etapa', 'sector', 'manzana'],
            'numero_edificios' => ['bloques', 'torres', 'naves', 'edificios'],
            'numero_unidades' => ['unidades privadas', 'unidades inmobiliarias'],
            'resumen_areas_conjunto' => ['area privada', 'area comun', 'area construida', 'cuadro de areas'],
            'desarrollos_relevantes' => ['desenglobe', 'subdivision', 'ampliacion'],
            'lotes_por_etapa' => ['lote matriz', 'lotes resultantes', 'lotes por etapa'],
            'organizacion_interna' => ['manzana', 'organizacion interna', 'sectores internos'],
            'ubicacion_unidad' => ['ubicacion de la unidad', 'unidad privada'],
            'subdivisiones_futuras' => ['futura subdivision', 'integracion futura'],
            'vias_internas' => ['vias internas', 'circulacion vehicular'],
            'red_contra_incendios' => ['red contra incendios', 'hidrante', 'incendio'],
            'equipamiento_tecnico' => ['subestacion', 'planta electrica', 'bascula', 'cuarto tecnico'],
            'porteria_administracion_vigilancia' => ['porteria', 'administracion', 'vigilancia'],
            'cctv_control_acceso' => ['cctv', 'control de acceso'],
            'apoyo_logistico_aduanero' => ['apoyo logistico', 'aduanero', 'zona franca'],
            'muelles' => ['muelle', 'bahia', 'rampa'],
            'patios_maniobra' => ['patio de maniobra', 'maniobra', 'radio de giro'],
            'circulacion_pesada' => ['tractomula', 'camion', 'vehiculo pesado'],
            'zonas_espera' => ['zona de espera', 'espera de vehiculos'],
            'reglas_cargue_descargue' => ['reglas de cargue', 'reglas de descargue'],
            'cargue_descargue' => ['cargue', 'descargue', 'flujo logistico'],
            'usos_permitidos' => ['usos permitidos', 'destinacion permitida'],
            'usos_restringidos' => ['prohibido', 'restriccion', 'limitacion'],
            'reglas_constructivas' => ['reglas constructivas', 'licencia', 'cerramiento'],
            'condiciones_normativas_operativas' => ['residuos', 'aislamiento', 'normas de funcionamiento'],
            'condiciones_usuario_operador' => ['usuario operador', 'administracion', 'zona franca'],
            'expensas_cuotas' => ['expensas', 'cuota de administracion'],
            'coeficientes_copropiedad' => ['coeficiente de copropiedad', 'coeficientes'],
            'responsabilidades_bienes_comunes' => ['responsabilidad', 'bienes comunes'],
            'cargas_comercializacion' => ['comercializacion', 'restricciones de venta', 'arriendo'],
            'incidencia_operacion_bodegas' => ['operacion de bodegas', 'logistica', 'maniobra'],
            'incidencia_valor_soporte_comun' => ['soporte comun', 'aporte de valor'],
            'incidencia_restricciones_regimen' => ['regimen especial', 'restricciones del regimen'],
            'incidencia_comercializacion_interna' => ['comercializacion interna', 'organizacion interna'],
            'lectura_valuatoria' => ['valor', 'valuacion', 'avaluo'],
            'salvedades_reglamento' => ['salvedad', 'validar', 'pendiente'],
            'salvedades_visita' => ['visita', 'inspeccion'],
            'salvedades_validacion' => ['validacion documental', 'certificado', 'plano'],
            'observaciones_extraccion' => ['observacion', 'nota'],
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
        return '';
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

    private function statusMap(string $text, array $rules): array
    {
        $data = [];
        foreach ($rules as $key => $needles) {
            $snippet = $this->snippet($text, $needles);
            if ($snippet !== '') $data[$key] = ['status' => 'warn', 'notes' => $snippet];
        }
        return $data;
    }

    private function diagnosis(array $core, array $technical, array $risks): string
    {
        $parts = [];
        if (($core['ph_name'] ?? '') !== '') $parts[] = 'Copropiedad identificada preliminarmente: ' . $core['ph_name'] . '.';
        if (($core['matrix_registration'] ?? '') !== '') $parts[] = 'Matrícula matriz probable: ' . $core['matrix_registration'] . '.';
        if (($technical['regimen_especial'] ?? '') !== '') $parts[] = 'Se observan menciones a régimen especial o condiciones operativas que deben validarse.';
        if ($risks) $parts[] = 'Existen restricciones, cargas o riesgos PH detectados automáticamente que requieren confirmación documental y de visita.';
        if (!$parts) $parts[] = 'Lectura preliminar PH sin hallazgos suficientes; completar con reglamento, administración y visita.';
        return implode(' ', $parts);
    }

    private function reportText(array $core, array $technical, string $typology): string
    {
        $name = (string) ($core['ph_name'] ?? 'la copropiedad analizada');
        $type = AppraisalPhCatalog::typologies()[$typology] ?? 'propiedad horizontal';
        $parts = ["Se revisa preliminarmente $name como $type."];
        foreach (['tipo_propiedad_horizontal', 'naturaleza_conjunto', 'uso_dominante',
            'resumen_areas_conjunto', 'vias_internas', 'equipamiento_tecnico',
            'coeficientes_copropiedad', 'expensas_cuotas', 'lectura_valuatoria'] as $key) {
            if (($technical[$key] ?? '') !== '') $parts[] = $technical[$key];
        }
        $parts[] = 'Esta lectura es apoyo técnico para el avalúo y no reemplaza estudio de títulos, reglamento completo, certificación de administración ni verificación física en visita.';
        return preg_replace('/\s+/', ' ', implode(' ', $parts)) ?? implode(' ', $parts);
    }

    private function match(string $pattern, string $text): string
    {
        return preg_match($pattern, $text, $match) ? mb_substr(trim((string) ($match[1] ?? '')), 0, 220) : '';
    }
}
