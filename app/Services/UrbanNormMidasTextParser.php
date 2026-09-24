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
        $out['norm_isolation_text'] = $this->section($text, 'AISLAMIENTOS', null);
        return array_filter($out, static fn (string $v): bool => $v !== '');
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
}
