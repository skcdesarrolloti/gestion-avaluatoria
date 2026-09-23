<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalSectorCatalog;

final class AppraisalSectorChapterReport
{
    public function build(array $record, array $subject, array $sector, array $advancedRows): array
    {
        $advanced = $this->advanced($advancedRows); $details = new AppraisalSectorChapterDetails();
        $sections = [
            ['2.1 Localización', $this->location($subject, $sector, $advanced)],
            ['2.2 Delimitación y soporte cartográfico', $this->cartography($sector, $advanced)],
            ['2.3 Servicios públicos', $this->services($sector, $advanced)],
            ['2.4 Usos predominantes', $this->use($sector, $advanced)],
            ['2.5 Normatividad urbanística del sector', $this->urbanNorm($sector, $advanced)],
            ['2.6 Vías de acceso', $this->roads($sector, $advanced)],
            ['2.6.1 Elementos de las vías', $this->roadElements($sector, $advanced)],
            ['2.6.2 Estado de conservación', $details->roadState($sector)],
            ['2.7 Amoblamiento urbano', $details->urbanFurniture($sector, $advanced)],
            ['2.8 Estratificación socioeconómica', $details->stratum($sector, $advanced)],
            ['2.9 Legalidad de la urbanización', $details->legality($advanced)],
            ['2.10 Topografía', $details->topography($sector, $advanced)],
            ['2.11 Servicio de transporte público', $details->transport($sector, $advanced)],
            ['2.11.1 Tipo de transporte público', $details->transportType($advanced)],
            ['2.11.2 Cubrimiento', $details->transportCoverage($sector, $advanced)],
            ['2.11.3 Frecuencia', $details->transportFrequency($advanced)],
            ['2.11.4 Calidad del servicio', $details->transportQuality($sector)],
            ['2.12 Edificaciones importantes del sector', $details->importantBuildings($sector, $advanced)],
            ['2.13 Tipos de edificación', $details->buildingTypes($sector, $advanced)],
            ['Soporte normativo aplicado al capítulo 2', $details->normative()],
        ];
        return ['sections' => $sections, 'text' => $this->plainText($sections)];
    }

    private function location(array $subject, array $sector, array $advanced): string
    {
        $a = $advanced['01'] ?? [];
        $place = $this->join(array_filter([
            $this->first($a['barrio'] ?? '', $sector['sector_neighborhood'] ?? '', $subject['neighborhood_name'] ?? ''),
            $this->first($a['localidad'] ?? '', $sector['sector_locality'] ?? '', $subject['locality_name'] ?? ''),
            $this->first($a['comuna'] ?? '', $sector['sector_commune'] ?? '', $subject['commune_ucg'] ?? ''),
            $this->first($a['municipio_distrito'] ?? '', $sector['sector_city'] ?? '', $subject['city_name'] ?? ''),
        ]), ', ');
        $obs = $this->first($a['observacion_localizacion'] ?? '', $sector['influence_area'] ?? '');
        $base = $place !== '' ? 'El inmueble se ubica en el sector ' . $place . '.' : 'La localización sectorial queda pendiente de precisar con la ficha del bien sujeto y el barrio de trabajo.';
        return $obs !== '' ? $base . ' ' . $this->end($obs) : $base;
    }

    private function cartography(array $sector, array $advanced): string
    {
        $a = $advanced['02'] ?? []; $b = $advanced['01'] ?? []; $bounds = $this->boundaries($sector, $b); $parts = [];
        if ($bounds !== '') $parts[] = 'La delimitación de referencia registra ' . $bounds . '.';
        if ($this->first($a['mapa_delimitacion_url'] ?? '', $b['mapa_barrio_url'] ?? '', $sector['sector_map_url'] ?? '') !== '') $parts[] = 'El soporte cartográfico se conserva como enlace o referencia de mapa para revisión del analista.';
        if ($this->first($a['imagen_satelital_url'] ?? '', $sector['support_notes'] ?? '') !== '') $parts[] = 'La imagen satelital o soporte gráfico queda registrado como ayuda visual, sin sustituir la verificación de campo.';
        return $parts ? implode(' ', $parts) : 'La delimitación y el soporte cartográfico quedan pendientes de completar con mapa, fuente, fecha o validación de campo.';
    }

    private function services(array $sector, array $advanced): string
    {
        $a = $advanced['03'] ?? []; $items = [];
        foreach (['acueducto' => 'acueducto', 'alcantarillado' => 'alcantarillado', 'energia' => 'energía', 'gas' => 'gas natural'] as $key => $label) if ($this->yes($a[$key] ?? '')) $items[] = $label;
        $base = $items ? 'El sector cuenta con servicios públicos identificados de ' . implode(', ', $items) . '.' : $this->selectSentence('La disponibilidad de servicios públicos del sector se califica como ', 'services_status', $sector['services_status'] ?? '', '.');
        foreach ([['Prestadores o soportes registrados: ', $this->join([$a['acueducto_detalle'] ?? '', $a['alcantarillado_detalle'] ?? '', $a['energia_detalle'] ?? '', $a['gas_detalle'] ?? ''], '; ')], ['Servicios complementarios de conectividad: ', $this->list($a['internet_operadores'] ?? [])], ['Recolección de residuos o aseo: ', $this->list($a['aseo_prestadores'] ?? [])]] as [$label, $value]) if ($value !== '') $base .= ' ' . $label . $this->end($value);
        $notes = $this->first($a['aguas_lluvias_detalle'] ?? '', $a['aseo_detalle'] ?? '', $sector['infrastructure_notes'] ?? '');
        return $notes !== '' ? $base . ' ' . $this->end($notes) : $base;
    }

    private function use(array $sector, array $advanced): string
    {
        $a = $advanced['04'] ?? []; $parts = [];
        if (($use = $this->first($a['uso_predominante'] ?? '', $sector['predominant_use'] ?? '')) !== '') $parts[] = 'En el sector predomina el uso ' . mb_strtolower($this->label('predominant_use', $use)) . '.';
        if (($activity = $this->first($a['actividad_economica_predominante'] ?? '', $sector['commercial_activity'] ?? '')) !== '') $parts[] = 'La actividad económica observada se orienta a ' . mb_strtolower($this->value($activity)) . '.';
        if (($description = $this->first($a['descripcion_general_sector'] ?? '', $sector['daily_dynamics'] ?? '')) !== '') $parts[] = $this->end($description);
        return $parts ? implode(' ', $parts) : 'El uso predominante del sector queda pendiente de validar con norma urbana, visita y lectura de mercado.';
    }

    private function urbanNorm(array $sector, array $advanced): string
    {
        $a = $advanced['05'] ?? []; $parts = [];
        foreach ([['clasificacion_suelo', 'Clasificación del suelo'], ['norma_base', 'Norma base'], ['acto_complementario', 'Acto complementario'], ['fuente_normativa', 'Fuente normativa']] as [$key, $label]) if ($this->text($a[$key] ?? '') !== '') $parts[] = $label . ': ' . $this->text($a[$key]);
        foreach ([$sector['urban_norm'] ?? '', $a['midas_lectura_manual'] ?? '', $a['soporte_normativo_sector'] ?? ''] as $text) if ($this->text($text) !== '') $parts[] = $this->text($text);
        return $parts ? implode(' ', array_map(fn (string $p): string => $this->end($p), $parts)) : 'La normatividad urbanística del sector queda pendiente de contraste con MIDAS, POT, acto aplicable o fuente oficial vigente.';
    }

    private function roads(array $sector, array $advanced): string
    {
        $a = $advanced['06'] ?? []; $via = $this->first($a['via_principal'] ?? '', $sector['access_roads'] ?? ''); $text = $this->first($a['comentario_vias_senalizacion'] ?? '', $a['vias_detalle'] ?? '', $sector['mobility_notes'] ?? '');
        if ($via !== '' && $text !== '') return 'La principal referencia vial corresponde a ' . $this->end($via) . ' ' . $this->end($text);
        if ($via !== '') return 'La principal referencia vial corresponde a ' . $this->end($via);
        return $text !== '' ? $this->end($text) : 'Las vías de acceso quedan pendientes de caracterización con jerarquía, estado, señalización y validación de campo.';
    }
    private function roadElements(array $sector, array $advanced): string { $a = $advanced['06'] ?? []; $elements = $this->first($a['vias_detalle'] ?? '', $sector['access_roads'] ?? ''); return $elements !== '' ? $this->end($elements) : 'Los elementos de las vías deben completarse con calzada, carriles, sentido de circulación, superficie, separadores, andenes o señalización, según aplique.'; }

    private function advanced(array $rows): array { $out = []; foreach ($rows as $code => $row) { $data = json_decode((string) ($row['data_json'] ?? ''), true); $out[(string) $code] = is_array($data) ? $data : []; } return $out; }
    private function boundaries(array $sector, array $data): string { $parts = []; foreach (['norte' => 'sector_north_boundary', 'este' => 'sector_east_boundary', 'sur' => 'sector_south_boundary', 'oeste' => 'sector_west_boundary'] as $side => $key) if (($value = $this->first($data[$side] ?? '', $sector[$key] ?? '')) !== '') $parts[] = 'por el ' . $side . ' ' . $value; return implode('; ', $parts); }
    private function selectSentence(string $prefix, string $field, mixed $value, string $suffix): string { $label = $this->label($field, $value); return $label !== '' ? $prefix . mb_strtolower($label) . $suffix : 'Dato pendiente de completar o validar.'; }
    private function label(string $field, mixed $value): string { $key = $this->text($value); return $key === '' ? '' : (AppraisalSectorCatalog::options()[$field][$key] ?? $this->value($key)); }
    private function yes(mixed $value): bool { return in_array(mb_strtoupper($this->text($value)), ['SI', 'SÍ'], true); }
    private function list(mixed $value): string { return is_array($value) ? implode(', ', array_map([$this, 'value'], array_filter(array_map('strval', $value)))) : $this->value($value); }
    private function value(mixed $value): string { return str_replace('_', ' ', $this->text($value)); }
    private function join(array $values, string $separator): string { return implode($separator, array_values(array_filter(array_map([$this, 'text'], $values), static fn (string $v): bool => $v !== ''))); }
    private function first(mixed ...$values): string { foreach ($values as $v) if ($this->text($v) !== '') return $this->text($v); return ''; }
    private function text(mixed $value): string { return trim(preg_replace('/\s+/u', ' ', (string) $value) ?? ''); }
    private function end(string $text): string { return rtrim($text, ' .') . '.'; }
    private function plainText(array $sections): string { return implode("\n\n", array_map(static fn (array $s): string => $s[0] . "\n" . $s[1], $sections)); }
}
