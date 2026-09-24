<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalLegalView;

final class AppraisalLegalChapterReport
{
    public function build(array $profile, array $certificates = []): array
    {
        $data = is_array($profile['data'] ?? null) ? $profile['data'] : [];
        $annotations = AppraisalLegalView::resolvedAnnotations(is_array($profile['annotations'] ?? null) ? $profile['annotations'] : []);
        $alerts = AppraisalLegalView::activeAlerts(is_array($profile['alerts'] ?? null) ? $profile['alerts'] : [], $annotations);
        $groups = AppraisalLegalView::groupedAnnotations($annotations);
        $counts = AppraisalLegalView::trafficCounts($annotations);
        $latest = $certificates[0] ?? [];
        $sections = [
            ['4. Identificación de las Características Jurídicas', $this->intro($profile, $latest)],
            ['4.1 Certificado, folio y titularidad', $this->identity($data)],
            ['4.2 Identificación registral, catastral y física', $this->physical($data)],
            ['4.3 Propiedad horizontal y derechos vinculados', $this->ph($data, $groups['ph'] ?? [])],
            ['4.4 Tradición, gravámenes, limitaciones y medidas cautelares', $this->charges($annotations, $groups, $counts, $alerts)],
            ['4.5 Salvedades y conclusión jurídica para el avalúo', $this->conclusion($data)],
        ];
        return ['sections' => $sections, 'text' => implode("\n\n", array_map(static fn (array $s): string => $s[0] . "\n" . $s[1], $sections))];
    }

    private function intro(array $profile, array $latest): string
    {
        $file = trim((string) ($latest['source_filename'] ?? '')) ?: 'certificado o soporte jurídico aportado';
        $status = trim((string) ($profile['status'] ?? 'Pendiente de revisión'));
        return 'La identificación jurídica se estructura con base en el certificado de tradición y libertad, los datos registrales y los soportes aportados al expediente. Soporte normativo: NTS S 03 y NTS I 01 exigen identificar derechos, información examinada, supuestos y salvedades; Decreto 1420 de 1998, arts. 21 y 22, incorpora las características jurídicas dentro del análisis del inmueble; IVS 104, IVS 106 e IVS 400 exigen trazabilidad de datos, limitaciones y derechos inmobiliarios. Fuente revisada: ' . $file . '. Estado interno de lectura: ' . $status . '. Esta lectura no constituye estudio de títulos.';
    }

    private function identity(array $d): string
    {
        return $this->lines([
            'Matrícula inmobiliaria' => $d['matricula_inmobiliaria'] ?? '', 'Círculo registral / ORIP' => trim(($d['circulo_registral'] ?? '') . ' ' . ($d['orip'] ?? '')),
            'Municipio y departamento' => trim(($d['municipio'] ?? '') . ', ' . ($d['departamento'] ?? ''), ' ,'), 'Fecha de expedición' => $d['fecha_expedicion'] ?? '',
            'Titular actual' => $this->value($d, 'reporte_titular_actual') ?: $this->value($d, 'titular_actual'), 'Documento soporte actual' => $d['documento_soporte_actual'] ?? '',
            'Escritura o título para informe' => $d['reporte_escritura_propiedad'] ?? '',
        ], 'Pendiente completar certificado, titularidad y título registrado. Soporte: NTS S 03 / NTS I 01 e IVS 400.');
    }

    private function physical(array $d): string
    {
        return $this->lines([
            'Cédula o código catastral' => $this->value($d, 'reporte_cedula_catastral') ?: $this->value($d, 'codigo_catastral_actual'), 'Código catastral anterior' => $d['codigo_catastral_anterior'] ?? '',
            'NUPRE' => $d['nupre'] ?? '', 'Dirección registral o del certificado' => $d['direccion'] ?? '', 'Tipo de predio' => $d['tipo_predio'] ?? '',
            'Área registral' => $d['area'] ?? '', 'Área privada' => $d['area_privada'] ?? '', 'Área construida' => $d['area_construida'] ?? '',
            'Cabida y linderos' => $d['cabida_linderos'] ?? '',
        ], 'Pendiente cruzar datos de certificado, catastro, escritura, predial o soporte físico. Soporte: Decreto 1420, arts. 21 y 22; NTS I 01.');
    }

    private function ph(array $d, array $rows): string
    {
        $text = $this->lines([
            'Reglamento PH' => $this->value($d, 'reporte_constitucion_ph') ?: $this->value($d, 'reglamento_ph'), 'Matrícula matriz' => $d['matricula_matriz'] ?? '',
            'Matrículas derivadas' => $d['matriculas_derivadas'] ?? '', 'Unidad privada' => $d['unidad_privada'] ?? '',
            'Coeficiente de propiedad' => $this->value($d, 'reporte_coeficiente_propiedad') ?: $this->value($d, 'coeficiente_ph'), 'Reformas PH' => $d['reformas_ph'] ?? '',
        ], 'Si aplica propiedad horizontal, validar escritura de constitución, matrícula matriz, unidad privada, coeficiente y reformas. Soporte: Ley 675 de 2001, Decreto 1420 e IVS 400.');
        if ($rows) $text .= "\nActos PH identificados: " . implode('; ', array_slice(array_map([$this, 'annotationLine'], $rows), 0, 4)) . '.';
        return $text;
    }

    private function charges(array $annotations, array $groups, array $counts, array $alerts): string
    {
        $parts = ['Anotaciones leídas: ' . count($annotations) . '. Semáforo interno: rojo ' . $counts['Rojo'] . ', amarillo ' . $counts['Amarillo'] . ', verde ' . $counts['Verde'] . '.'];
        foreach (['tradicion' => 'Tradición', 'gravamen' => 'Gravámenes', 'limitacion_dominio' => 'Limitaciones al dominio', 'medida_cautelar' => 'Medidas cautelares', 'otras' => 'Otras anotaciones'] as $key => $label) {
            $rows = array_slice($groups[$key] ?? [], 0, 3); if (!$rows) continue;
            $parts[] = $label . ': ' . implode('; ', array_map([$this, 'annotationLine'], $rows)) . '.';
        }
        if ($alerts) $parts[] = 'Alertas para validar: ' . implode('; ', array_slice($alerts, 0, 4)) . '.';
        $parts[] = 'Soporte normativo: NTS S 03, Decreto 1420 e IVS 104/106/400. Las afectaciones vigentes se expresan como salvedad valuatoria hasta contar con validación jurídica suficiente.';
        return implode("\n", $parts);
    }

    private function conclusion(array $d): string
    {
        return $this->lines([
            'Nivel de atención pericial' => $d['semaforo_manual'] ?? '', 'Clasificación preliminar' => $d['clasificacion_manual'] ?? '',
            'Revisión humana' => $d['revision_analista'] ?? '', 'Salvedad para informe' => $d['salvedad_final'] ?? '',
            'Conclusión jurídica preliminar' => $d['reporte_conclusion_entregable'] ?? '', 'Reporte profesional integrado' => $d['reporte_profesional_entregable'] ?? '',
        ], 'Pendiente cierre del analista. El informe debe conservar salvedades, soportes y limitaciones conforme NTS S 03 e IVS 106.');
    }

    private function annotationLine(array $row): string
    {
        [, $traffic] = AppraisalLegalView::trafficLight($row);
        return trim(($row['orden'] ?? '') . ' ' . ($row['descripcion_acto'] ?? '') . ' (' . $traffic . ')');
    }

    private function value(array $data, string $key): string
    {
        return trim((string) ($data[$key] ?? ''));
    }

    private function lines(array $items, string $fallback): string
    {
        $rows = [];
        foreach ($items as $label => $value) if (trim((string) $value) !== '') $rows[] = $label . ': ' . trim((string) $value) . '.';
        return $rows ? implode("\n", $rows) : $fallback;
    }
}