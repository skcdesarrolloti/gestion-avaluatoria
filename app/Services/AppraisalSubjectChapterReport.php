<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalObsolescenceCatalog;

final class AppraisalSubjectChapterReport
{
    public function build(array $record, array $subject, array $units, array $ph, array $obs): array
    {
        $sections = [
            ['3.1 Identificación y características', $this->identification($record, $subject)],
            ['3.2 Áreas y superficies adoptadas', $this->areas($units)],
            ['3.3 Aspectos generales de la construcción', $this->constructionGeneral($units)],
            ['3.3.1 Materiales y estado de conservación', $this->materials($units)],
        ];
        if (trim((string) ($ph['report_text'] ?? '')) !== '') $sections[] = ['3.5 Propiedad horizontal', (string) $ph['report_text']];
        $sections[] = ['3.6 Obsolescencias', $this->obsolescence($obs)];
        $sections[] = ['3.7 Registro fotográfico y soportes', 'El registro fotográfico se incorpora como soporte visual de existencia, estado, acceso, entorno, construcción, diferenciales y, cuando aplique, bienes comunes de la copropiedad. Las fotografías no sustituyen certificados, pruebas especializadas ni estudio de títulos; documentan la observación técnica disponible para el avalúo.'];
        $sections[] = ['Soporte normativo aplicado al capítulo', $this->normative($ph)];
        return ['sections' => $sections, 'text' => $this->plainText($sections)];
    }

    private function identification(array $record, array $subject): string
    {
        $name = $this->first($subject['subject_title'] ?? '', $record['titulo'] ?? 'inmueble objeto de medición');
        $parts = ["El inmueble objeto de medición corresponde a {$name}"];
        if ($this->text($subject['adopted_address'] ?? '') !== '') $parts[] = 'ubicado en ' . $this->text($subject['adopted_address']);
        if ($this->text($subject['city_name'] ?? $record['municipio'] ?? '') !== '') $parts[] = 'municipio o distrito de ' . $this->text($subject['city_name'] ?: $record['municipio']);
        if ($this->text($subject['property_registry'] ?? '') !== '') $parts[] = 'identificado con matrícula inmobiliaria ' . $this->text($subject['property_registry']);
        if ($this->text($subject['cadastral_reference'] ?? '') !== '') $parts[] = 'referencia catastral ' . $this->text($subject['cadastral_reference']);
        $base = implode(', ', $parts) . '.';
        $use = $this->first($subject['current_use'] ?? '', $record['destinacion'] ?? '', $record['tipo_inmueble'] ?? '');
        if ($use !== '') $base .= ' El uso o destinación considerada para la lectura técnica es ' . $this->label($use) . '.';
        return $base;
    }

    private function areas(array $units): string
    {
        $rows = [];
        foreach ($units as $unit) {
            if (($unit['unit_kind'] ?? '') === 'common') continue;
            $sources = $this->areaSources($unit, 'area_');
            $built = $this->areaSources($unit, 'built_area_');
            $line = $this->unitName($unit) . ': área adoptada ' . $this->m2($unit['area_adopted_m2'] ?? '') . $this->source($unit['area_adopted_source'] ?? '');
            if (($unit['built_area_adopted_m2'] ?? '') !== null && (string) ($unit['built_area_adopted_m2'] ?? '') !== '') $line .= '; área construida adoptada ' . $this->m2($unit['built_area_adopted_m2']) . $this->source($unit['built_area_adopted_source'] ?? '');
            $support = array_filter(array_merge($sources, $built));
            if ($support) $line .= '. Fuentes registradas: ' . implode('; ', array_slice($support, 0, 8)) . '.';
            $rows[] = $line;
        }
        return $rows ? implode("\n", $rows) : 'Las áreas quedan pendientes de adopción o de contraste documental en las fuentes disponibles.';
    }

    private function constructionGeneral(array $units): string
    {
        $rows = [];
        foreach ($units as $unit) {
            if (($unit['unit_kind'] ?? '') === 'common') continue;
            $facts = [];
            foreach ([['construction_floors','niveles'], ['construction_age_years','edad aproximada'], ['construction_remaining_life_years','vida útil remanente'], ['construction_rentable_units','unidades rentables']] as [$key,$label]) {
                if ($this->text($unit[$key] ?? '') !== '') $facts[] = $label . ': ' . $this->text($unit[$key]);
            }
            if ($this->text($unit['construction_state'] ?? '') !== '') $facts[] = 'estado de obra: ' . $this->label($unit['construction_state']);
            if ($this->text($unit['construction_general_aspects'] ?? '') !== '') $facts[] = $this->text($unit['construction_general_aspects']);
            $rows[] = $this->unitName($unit) . ($facts ? ': ' . implode('; ', $facts) . '.' : ': aspectos generales pendientes de completar.');
        }
        return $rows ? implode("\n", $rows) : 'No se han definido unidades constructivas para describir aspectos generales.';
    }

    private function materials(array $units): string
    {
        $labels = ['estructura'=>'Estructura','fachada'=>'Fachada','cubierta'=>'Cubierta','dependencias'=>'Dependencias','iluminacion'=>'Iluminación','ventilacion'=>'Ventilación','acabados'=>'Acabados','pisos'=>'Pisos','paredes'=>'Paredes','cielorraso'=>'Cielo raso','puertas'=>'Puertas','ventanas'=>'Ventanas','banos'=>'Baños','cocina'=>'Cocina','instalaciones'=>'Instalaciones','cerramiento'=>'Cerramiento','porton'=>'Portón','equipos'=>'Equipos'];
        $state = ['B'=>'bueno','R'=>'regular','M'=>'malo','NA'=>'no aplica']; $rows = [];
        foreach ($units as $unit) {
            if (($unit['unit_kind'] ?? '') === 'common') continue;
            $specifics = $this->json($unit['construction_specifics_json'] ?? '{}');
            $conservation = $this->json($unit['construction_conservation_json'] ?? '{}');
            $items = [];
            foreach ($labels as $key => $label) {
                $material = $this->text($specifics['material_' . $key] ?? ''); $status = $this->text($conservation[$key] ?? '');
                if ($material === '' && $status === '') continue;
                $items[] = $label . ': ' . ($material ?: 'material por confirmar') . ($status !== '' ? ' (' . ($state[$status] ?? $status) . ')' : '');
            }
            $rows[] = $this->unitName($unit) . ($items ? ': ' . implode('; ', $items) . '.' : ': materiales y estado pendientes de completar o verificar en visita.');
        }
        return $rows ? implode("\n", $rows) : 'No se han registrado materiales constructivos por unidad.';
    }

    private function obsolescence(array $obs): string
    {
        $summary = $this->text($obs['summary_text'] ?? '');
        if ($summary !== '') return $summary;
        $guidance = AppraisalObsolescenceCatalog::readerGuidance();
        return $guidance['fisica']['definition'] . ' ' . $guidance['fisica']['no_finding'] . "\n"
            . $guidance['funcional']['definition'] . ' ' . $guidance['funcional']['no_finding'] . "\n"
            . $guidance['externa']['definition'] . ' ' . $guidance['externa']['no_finding'] . "\n"
            . 'La lectura de obsolescencias no constituye descuento automático; cualquier incidencia económica debe sustentarse de forma separada mediante mercado, costos, comparables, soporte documental, visita o criterio técnico verificable.';
    }

    private function normative(array $ph): string
    {
        $extra = trim((string) ($ph['report_text'] ?? '')) !== '' ? ' Para propiedad horizontal se incorpora además la Ley 675 de 2001 respecto de régimen, bienes comunes, coeficientes, expensas y administración.' : '';
        return 'La estructura del capítulo se soporta en NTS S 03 y NTS I 01 para suficiencia del informe, identificación de información examinada, metodología, soportes, hipótesis y salvedades; en NTS M 01 para que el método quede soportado en información pertinente, verificable y comparable; en el Decreto 1420 de 1998, artículos 21 y 22, para la lectura de localización, áreas, características físicas, jurídicas y económicas del inmueble; y en IVS 104 e IVS 106 como criterios de datos relevantes, trazabilidad, supuestos, limitaciones y conclusiones.' . $extra . ' Esta descripción no constituye estudio de títulos ni certificación administrativa; organiza los soportes revisados para sustentar la incidencia técnica en el avalúo.';
    }

    private function areaSources(array $unit, string $prefix): array
    {
        $map = ['manual_m2'=>'manual', 'midas_m2'=>'MIDAS', 'tax_m2'=>'predial', 'deed_m2'=>'escritura pública', 'certificate_m2'=>'certificado de tradición', 'other_m2'=>'otra fuente']; $out = [];
        foreach ($map as $suffix => $label) if ($this->text($unit[$prefix . $suffix] ?? '') !== '') $out[] = $label . ' ' . $this->m2($unit[$prefix . $suffix]);
        return $out;
    }
    private function plainText(array $sections): string { return implode("\n\n", array_map(static fn (array $s): string => $s[0] . "\n" . $s[1], $sections)); }
    private function unitName(array $unit): string { return $this->first($unit['label'] ?? '', (($unit['unit_kind'] ?? '') === 'annex' ? 'Anexo ' : 'Unidad ') . (int) ($unit['unit_index'] ?? 0)); }
    private function m2(mixed $value): string { return $this->text($value) !== '' ? rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',') . ' m²' : 'pendiente'; }
    private function source(mixed $value): string { $labels = ['deed'=>'escritura pública','tax'=>'impuesto predial','certificate'=>'certificado de tradición','manual'=>'fuente manual','midas'=>'MIDAS','other'=>'otra fuente']; $key = $this->text($value); return $key !== '' ? ' según ' . ($labels[$key] ?? $this->label($key)) : ''; }
    private function label(string $value): string { return str_replace('_', ' ', $value); }
    private function first(mixed ...$values): string { foreach ($values as $v) if ($this->text($v) !== '') return $this->text($v); return ''; }
    private function text(mixed $value): string { return trim(preg_replace('/\s+/u', ' ', (string) $value) ?? ''); }
    private function json(mixed $json): array { $data = json_decode((string) $json, true); return is_array($data) ? $data : []; }
}
