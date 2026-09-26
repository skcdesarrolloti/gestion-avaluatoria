<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalObsolescenceCatalog;

final class AppraisalSubjectChapterReport
{
    public function build(array $record, array $subject, array $units, array $ph, array $obs): array
    {
        $sections = [
            ['3. Descripción general del activo objeto de la medición', $this->intro($record, $subject, $units)],
            ['3.1 Identificación, características y tipo de propiedad', $this->identification($record, $subject, $ph)],
            ['3.2 Terreno, superficies y linderos', $this->surface($units)],
            ['3.3 Descripción de las construcciones y mejoras', $this->constructionDescription($units)],
            ['3.3.1 Construcciones y descripción documental', $this->constructionLegal($units)],
            ['3.3.2 Aspectos generales de la construcción', $this->constructionGeneral($record, $units)],
            ['3.3.3 Materiales de construcción y estado de conservación', $this->materials($units)],
            ['3.3.4 Áreas construidas', $this->builtAreas($units)],
            ['3.4 Diferenciales valuatorios del sujeto', $this->differentials($units)],
        ];
        if (trim((string) ($ph['report_text'] ?? '')) !== '') $sections[] = ['3.5 Propiedad horizontal', (string) $ph['report_text']];
        $sections[] = ['3.6 Obsolescencias', $this->obsolescence($obs)];
        $sections[] = ['3.7 Registro fotográfico y soportes', $this->photos()];
        $sections[] = ['Soporte normativo aplicado al capítulo 3', $this->normative($ph)];
        return ['sections' => $sections, 'text' => $this->plainText($sections)];
    }

    private function intro(array $record, array $subject, array $units): string
    {
        $names = $this->unitNames($units); $asset = $names ? $this->join($names, ', ') : $this->first($subject['subject_title'] ?? '', $record['titulo'] ?? 'el activo objeto de medición');
        $place = $this->first($subject['adopted_address'] ?? '', $subject['address'] ?? '', $record['direccion'] ?? '');
        $city = $this->first($subject['city_name'] ?? '', $record['municipio'] ?? '');
        $use = $this->first($subject['current_use'] ?? '', $record['destinacion'] ?? '', $record['tipo_inmueble'] ?? '');
        $text = 'El activo objeto de medición está conformado por ' . $asset;
        if ($place !== '') $text .= ', localizado en ' . $place;
        if ($city !== '') $text .= ', ' . $city;
        $text .= '.';
        if ($use !== '') $text .= ' La destinación o uso considerado para la lectura técnica es ' . $this->label($use) . '.';
        return $text . ' La descripción integra información documental, visita, superficies, construcción, propiedad horizontal cuando aplique, obsolescencias y registro fotográfico.';
    }

    private function identification(array $record, array $subject, array $ph): string
    {
        $name = $this->first($subject['subject_title'] ?? '', $record['titulo'] ?? 'inmueble objeto de medición'); $parts = ["El inmueble objeto de medición corresponde a {$name}"];
        foreach ([['adopted_address','ubicado en'], ['property_registry','matrícula inmobiliaria'], ['cadastral_reference','referencia catastral'], ['registry_office','oficina de registro']] as [$key,$label]) if ($this->text($subject[$key] ?? '') !== '') $parts[] = $label . ' ' . $this->text($subject[$key]);
        $base = implode(', ', $parts) . '.';
        if (($record['regimen_ph'] ?? '') === 'si') $base .= ' Se registra como inmueble sometido al régimen de propiedad horizontal; los bienes comunes, servicios, restricciones y soportes específicos se depuran en el numeral 3.5.';
        if ($this->text($ph['ph_name'] ?? '') !== '') $base .= ' Copropiedad o agrupación reportada: ' . $this->end($this->text($ph['ph_name']));
        return $base;
    }

    private function surface(array $units): string
    {
        $rows = [];
        foreach ($this->privateUnits($units) as $unit) {
            $facts = ['área adoptada ' . $this->m2($unit['area_adopted_m2'] ?? '') . $this->source($unit['area_adopted_source'] ?? '')];
            foreach ([['lot_shape','forma geométrica'], ['topography','topografía'], ['front_length_m','frente'], ['equivalent_depth_m','fondo equivalente'], ['front_depth_ratio','relación frente-fondo'], ['enclosure','cerramiento']] as [$key,$label]) if ($this->text($unit[$key] ?? '') !== '') $facts[] = $label . ': ' . ($key === 'front_length_m' || $key === 'equivalent_depth_m' ? $this->meters($unit[$key]) : $this->label($unit[$key]));
            $line = $this->unitName($unit) . ': ' . implode('; ', $facts) . '.';
            if (($boundaries = $this->boundaries($unit)) !== '') $line .= ' Linderos registrados: ' . $boundaries . '.';
            if ($this->text($unit['surface_report_text'] ?? '') !== '') $line .= ' ' . $this->end($this->text($unit['surface_report_text']));
            $rows[] = $line;
        }
        return $rows ? implode("\n", $rows) : 'Las superficies, linderos, forma, frente, fondo, topografía y cerramiento quedan pendientes de adopción o contraste documental.';
    }

    private function constructionDescription(array $units): string
    {
        $rows = [];
        foreach ($this->privateUnits($units) as $unit) {
            $type = $this->first($unit['construction_type'] ?? '', $unit['unit_type'] ?? 'unidad construida');
            $text = $this->first($unit['construction_report_text'] ?? '', $unit['construction_general_aspects'] ?? '');
            $rows[] = $this->unitName($unit) . ': ' . $this->label($type) . ($text !== '' ? '. ' . $this->end($text) : ', con descripción de construcción pendiente de ampliar según visita y soporte documental.');
        }
        return $rows ? implode("\n", $rows) : 'No se han registrado construcciones o mejoras para describir en el entregable.';
    }

    private function constructionLegal(array $units): string
    {
        $rows = [];
        foreach ($this->privateUnits($units) as $unit) {
            $parts = [];
            foreach ([['area_private_m2','área privada'], ['area_common_m2','área común'], ['construction_quantity','cantidad'], ['construction_measure_unit','unidad de medida']] as [$key,$label]) if ($this->text($unit[$key] ?? '') !== '') $parts[] = $label . ': ' . (str_ends_with($key, '_m2') ? $this->m2($unit[$key]) : $this->text($unit[$key]));
            if (($boundaries = $this->boundaries($unit)) !== '') $parts[] = 'cabida y linderos: ' . $boundaries;
            $rows[] = $this->unitName($unit) . ($parts ? ': ' . implode('; ', $parts) . '.' : ': descripción documental pendiente de completar con escritura, certificado, reglamento PH, plano o soporte aportado.');
        }
        return $rows ? implode("\n", $rows) : 'La descripción documental por unidad queda pendiente de diligenciamiento.';
    }

    private function constructionGeneral(array $record, array $units): string
    {
        return (new AppraisalSubjectConstructionNarrator())->general($units, (string) ($record['tipo_inmueble'] ?? ''));
    }

    private function materials(array $units): string
    {
        $labels = ['estructura'=>'Estructura','fachada'=>'Fachada','cubierta'=>'Cubierta','dependencias'=>'Dependencias','iluminacion'=>'Iluminación','ventilacion'=>'Ventilación','acabados'=>'Acabados','pisos'=>'Pisos','paredes'=>'Paredes','cielorraso'=>'Cielo raso','puertas'=>'Puertas','ventanas'=>'Ventanas','banos'=>'Baños','cocina'=>'Cocina','instalaciones'=>'Instalaciones','cerramiento'=>'Cerramiento','porton'=>'Portón','equipos'=>'Equipos'];
        $state = ['B'=>'bueno','R'=>'regular','M'=>'malo','NA'=>'no aplica']; $rows = [];
        foreach ($this->privateUnits($units) as $unit) {
            $specifics = $this->json($unit['construction_specifics_json'] ?? '{}'); $conservation = $this->json($unit['construction_conservation_json'] ?? '{}'); $items = [];
            foreach ($labels as $key => $label) { $material = $this->text($specifics['material_' . $key] ?? $specifics[$key] ?? ''); $status = $this->text($conservation[$key] ?? ''); if ($material !== '' || $status !== '') $items[] = $label . ': ' . ($material ?: 'material por confirmar') . ($status !== '' ? ' (' . ($state[$status] ?? $status) . ')' : ''); }
            $rows[] = $this->unitName($unit) . ($items ? ': ' . implode('; ', $items) . '.' : ': materiales y estado pendientes de completar o verificar en visita.');
        }
        return $rows ? implode("\n", $rows) : 'No se han registrado materiales constructivos por unidad.';
    }

    private function builtAreas(array $units): string
    {
        $rows = [];
        foreach ($this->privateUnits($units) as $unit) {
            $sources = $this->areaSources($unit, 'built_area_'); $line = $this->unitName($unit) . ': área construida adoptada ' . $this->m2($unit['built_area_adopted_m2'] ?? '') . $this->source($unit['built_area_adopted_source'] ?? '');
            if ($sources) $line .= '. Fuentes registradas: ' . implode('; ', array_slice($sources, 0, 6)) . '.';
            $rows[] = $line;
        }
        return $rows ? implode("\n", $rows) : 'Las áreas construidas quedan pendientes de contraste entre escritura, predial, certificado, plano, MIDAS u otro soporte aplicable.';
    }

    private function differentials(array $units): string
    {
        $rows = [];
        foreach ($this->privateUnits($units) as $unit) {
            if ($this->text($unit['special_attributes_report_text'] ?? '') !== '') { $rows[] = $this->unitName($unit) . ': ' . $this->end($this->text($unit['special_attributes_report_text'])); continue; }
            $data = $this->json($unit['special_attributes_json'] ?? '{}'); $items = [];
            foreach ($data as $key => $item) if (is_array($item) && $this->text($item['value'] ?? '') !== '') $items[] = $this->label((string) $key) . ': ' . $this->label($item['value']);
            if ($items) $rows[] = $this->unitName($unit) . ': ' . implode('; ', array_slice($items, 0, 8)) . '.';
        }
        return $rows ? implode("\n", $rows) : 'No se registran diferenciales valuatorios específicos; si se identifican atributos, deméritos o evidencias, deben quedar soportados en visita, fotografías, mercado, norma o documento verificable.';
    }

    private function obsolescence(array $obs): string
    {
        $summary = $this->text($obs['summary_text'] ?? ''); if ($summary !== '') return $summary;
        $guidance = AppraisalObsolescenceCatalog::readerGuidance();
        return $guidance['fisica']['definition'] . ' ' . $guidance['fisica']['no_finding'] . "\n" . $guidance['funcional']['definition'] . ' ' . $guidance['funcional']['no_finding'] . "\n" . $guidance['externa']['definition'] . ' ' . $guidance['externa']['no_finding'] . "\n" . 'La lectura de obsolescencias no constituye descuento automático; cualquier incidencia económica debe sustentarse de forma separada mediante mercado, costos, comparables, soporte documental, visita o criterio técnico verificable.';
    }

    private function photos(): string
    { return 'El registro fotográfico se incorpora como soporte visual de existencia, estado, acceso, entorno, construcción, diferenciales y, cuando aplique, bienes comunes de la copropiedad. Las fotografías no sustituyen certificados, pruebas especializadas ni estudio de títulos; documentan la observación técnica disponible para el avalúo.'; }
    private function normative(array $ph): string
    { $extra = trim((string) ($ph['report_text'] ?? '')) !== '' ? ' Para propiedad horizontal se incorpora además la Ley 675 de 2001 respecto de régimen, bienes comunes, coeficientes, expensas y administración.' : ''; return 'La estructura del capítulo se soporta en NTS S 03 y NTS I 01 para suficiencia del informe, identificación del activo, áreas, linderos, construcción, información examinada, metodología, soportes, hipótesis y salvedades; en NTS M 01 para que el método quede soportado en información pertinente, verificable y comparable; en el Decreto 1420 de 1998, artículos 21 y 22, para la lectura de localización, áreas, características físicas, jurídicas y económicas del inmueble; y en IVS 104 e IVS 106 como criterios de datos relevantes, trazabilidad, supuestos, limitaciones y conclusiones.' . $extra . ' Esta descripción no constituye estudio de títulos ni certificación administrativa; organiza los soportes revisados para sustentar la incidencia técnica en el avalúo.'; }

    private function privateUnits(array $units): array { return array_values(array_filter($units, static fn (array $u): bool => ($u['unit_kind'] ?? '') !== 'common')); }
    private function boundaries(array $unit): string { $parts = []; foreach (['boundary_front'=>'frente', 'boundary_right'=>'derecha entrando', 'boundary_left'=>'izquierda entrando', 'boundary_back'=>'fondo', 'boundary_nadir'=>'nadir', 'boundary_zenith'=>'cenit'] as $key=>$label) if ($this->text($unit[$key] ?? '') !== '') $parts[] = $label . ': ' . $this->text($unit[$key]); return implode('; ', $parts); }
    private function areaSources(array $unit, string $prefix): array { $map = ['manual_m2'=>'manual', 'midas_m2'=>'MIDAS', 'tax_m2'=>'predial', 'deed_m2'=>'escritura pública', 'certificate_m2'=>'certificado de tradición', 'other_m2'=>'otra fuente']; $out = []; foreach ($map as $suffix => $label) if ($this->text($unit[$prefix . $suffix] ?? '') !== '') $out[] = $label . ' ' . $this->m2($unit[$prefix . $suffix]); return $out; }
    private function plainText(array $sections): string { return implode("\n\n", array_map(static fn (array $s): string => $s[0] . "\n" . $s[1], $sections)); }
    private function unitNames(array $units): array { $out = []; foreach ($this->privateUnits($units) as $u) if ($this->text($u['label'] ?? '') !== '') $out[] = $this->text($u['label']); return array_slice($out, 0, 12); }
    private function unitName(array $unit): string { return $this->first($unit['label'] ?? '', (($unit['unit_kind'] ?? '') === 'annex' ? 'Anexo ' : 'Unidad ') . (int) ($unit['unit_index'] ?? 0)); }
    private function m2(mixed $value): string { return $this->text($value) !== '' ? rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',') . ' m²' : 'pendiente'; }
    private function meters(mixed $value): string { return $this->text($value) !== '' ? rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',') . ' m' : 'pendiente'; }
    private function source(mixed $value): string { $labels = ['deed'=>'escritura pública','tax'=>'impuesto predial','certificate'=>'certificado de tradición','manual'=>'fuente manual','midas'=>'MIDAS','other'=>'otra fuente']; $key = $this->text($value); return $key !== '' ? ' según ' . ($labels[$key] ?? $this->label($key)) : ''; }
    private function label(mixed $value): string { return str_replace('_', ' ', $this->text($value)); }
    private function join(array $values, string $separator): string { return implode($separator, array_values(array_filter(array_map([$this, 'text'], $values), static fn (string $v): bool => $v !== ''))); }
    private function first(mixed ...$values): string { foreach ($values as $v) if ($this->text($v) !== '') return $this->text($v); return ''; }
    private function text(mixed $value): string { return trim(preg_replace('/\s+/u', ' ', (string) $value) ?? ''); }
    private function end(string $text): string { return rtrim($text, ' .') . '.'; }
    private function json(mixed $json): array { $data = json_decode((string) $json, true); return is_array($data) ? $data : []; }
}
