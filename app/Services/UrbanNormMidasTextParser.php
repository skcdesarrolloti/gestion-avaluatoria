<?php
declare(strict_types=1);
namespace App\Services;

final class UrbanNormMidasTextParser
{
    public function parse(string $text): array
    {
        $text = $this->clean($text);
        return ['predio' => $this->predio($text), 'usage' => $this->usage($text), 'raw' => $text];
    }

    private function predio(string $text): array
    {
        $fields = [
            'national_cadastral_reference' => 'Número Predial Nacional', 'property_registry' => 'Matrícula Inmobiliaria',
            'address' => 'Dirección', 'territory' => 'Territorio', 'locality' => 'Localidad', 'commune_ucg' => 'Unidad Comunera De Gobierno',
            'land_use' => 'Uso De Suelo', 'urban_treatment' => 'Tratamiento', 'risk' => 'Riesgos',
            'land_classification' => 'Clasificación Del Suelo', 'dane_block_code' => 'Código Manzana Dane',
            'dane_block_side' => 'Lado Manzana Dane', 'block_number' => 'Número De Manzana', 'property_number' => 'Número De Predio',
            'stratum' => 'Estrato Socioeconómico', 'stratum_record' => 'Acta Estratificación',
            'stratum_atypical' => 'Atipicidad Estratificación', 'stratum_observation' => 'Observación Estratificación',
            'building_name' => 'Nombre Edificación',
            'land_area_m2' => 'Área Terreno (M2)', 'built_area_m2' => 'Área Construida (M2)',
            'cadastral_reference' => 'Referencia Catastral', 'updated_on' => 'Fecha Actualización',
        ];
        $out = [];
        foreach ($fields as $key => $label) $out[$key] = $this->field($text, $label);
        return array_filter($out, static fn (string $v): bool => $v !== '');
    }

    private function usage(string $text): array
    {
        $out = [];
        $out['use_principal_text'] = $this->section($text, 'USO PRINCIPAL', 'USO COMPATIBLE');
        $out['use_compatible_text'] = $this->section($text, 'USO COMPATIBLE', 'USO COMPLEMENTARIO');
        $out['use_complementary_text'] = $this->section($text, 'USO COMPLEMENTARIO', 'USO RESTRINGIDO');
        $out['use_restricted_text'] = $this->section($text, 'USO RESTRINGIDO', 'USO PROHIBIDO');
        $out['use_prohibited_text'] = $this->sectionAny($text, ['USO PROHIBIDO'], ['UNIDAD BÁSICA', 'AREA LIBRE', 'ÁREA LIBRE'])
            ?: $this->section($text, 'USO PROHIBIDO', null);
        $out['use_regulation_table'] = $this->field($text, 'Cuadro de reglamentación de usos del suelo');
        $out['norm_unit_basic_text'] = $this->section($text, 'UNIDAD BÁSICA', 'USOS');
        $out['norm_free_area_text'] = $this->sectionAny($text, ['AREA LIBRE', 'ÁREA LIBRE'], ['AREA Y FRENTE MÍNIMOS', 'ÁREA Y FRENTE MÍNIMOS']);
        $out['norm_min_lot_front_text'] = $this->sectionAny($text, ['AREA Y FRENTE MÍNIMOS', 'ÁREA Y FRENTE MÍNIMOS'], ['ALTURA MÁXIMA']);
        $out['norm_max_height_text'] = $this->section($text, 'ALTURA MÁXIMA', 'ÍNDICE DE CONSTRUCCIÓN');
        $out['norm_construction_index_text'] = $this->section($text, 'ÍNDICE DE CONSTRUCCIÓN', 'AISLAMIENTOS');
        $occupancy = $this->sectionAny($text, ['ÍNDICE DE OCUPACIÓN', 'INDICE DE OCUPACION', 'ÁREA DE OCUPACIÓN', 'AREA DE OCUPACION'],
            ['ALTURA MÁXIMA', 'ÍNDICE DE CONSTRUCCIÓN', 'INDICE DE CONSTRUCCION', 'AISLAMIENTOS', 'ESTACIONAMIENTOS']);
        if ($occupancy !== '') {
            $out['occupancy_index'] = $this->ratio($occupancy);
            $out['norm_other_potential_text'] = trim("Índice / área de ocupación:\n" . $occupancy);
        }
        $out['norm_isolation_text'] = $this->section($text, 'AISLAMIENTOS', null);
        $out = array_replace($out, $this->simpleUnavailableUsage($text));
        return array_filter($out, static fn (string $v): bool => $v !== '');
    }

    private function simpleUnavailableUsage(string $text): array
    {
        if (!preg_match('/\bNO\s+DISPONIBLE\b/iu', $text)) return [];
        if (!preg_match('/(?:Consulta\s+uso\s+de\s+suelo|Predio\s*:)/iu', $text)) return [];
        $predio = $this->field($text, 'Predio');
        if ($predio === '' && preg_match('/Predio\s*:\s*([0-9]+)/iu', $text, $match)) $predio = trim($match[1]);
        $message = trim(preg_replace('/\s+/', ' ', $text) ?? $text);
        return ['midas_activity' => 'NO DISPONIBLE', 'current_use' => 'NO DISPONIBLE',
            'use_regulation_table' => 'NO DISPONIBLE', 'midas_usage_result' => $message,
            'source_status' => 'no_disponible', 'midas_support_reference' => trim('Consulta MIDAS Uso Suelo' . ($predio !== '' ? ' predio ' . $predio : ''))];
    }

    private function field(string $text, string $label): string
    {
        $pattern = '/(?:^|\n)\s*(?:\d{1,2}\s+)?' . preg_quote($label, '/') . '\s*:\s*\n?\s*(.*?)(?=\n\s*(?:\d{1,2}\s+)?[\p{L}ÁÉÍÓÚÜÑáéíóúüñ][^\n:]{1,80}:|\n\s*USO\s+[A-ZÁÉÍÓÚÜÑ]+|\z)/su';
        if (!preg_match($pattern, $text, $match)) return '';
        $value = trim(preg_replace('/\s+/', ' ', (string) $match[1]) ?? '');
        return preg_match('/^-+$/', $value) ? '' : $value;
    }

    private function section(string $text, string $start, ?string $end): string
    {
        $pattern = '/(?:^|\n)\s*' . preg_quote($start, '/') . '\s*\n(.*)' . ($end ? '(?=\n\s*' . preg_quote($end, '/') . '\s*\n)' : '\z') . '/su';
        if (!preg_match($pattern, $text, $match)) return '';
        return trim(preg_replace('/\n{3,}/', "\n\n", (string) $match[1]) ?? '');
    }

    private function sectionAny(string $text, array $starts, ?array $ends): string
    {
        foreach ($starts as $start) foreach (($ends ?: [null]) as $end) {
            $value = $this->section($text, $start, $end);
            if ($value !== '') return $value;
        }
        return '';
    }

    private function clean(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[\t ]+/', ' ', $text) ?? $text;
        return trim($text);
    }

    private function ratio(string $text): string
    {
        if (!preg_match('/([0-9]+(?:[,.][0-9]+)?)\s*%?/u', $text, $match)) return '';
        $number = (float) str_replace(',', '.', $match[1]);
        if ($number > 1) $number /= 100;
        return rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');
    }
}
