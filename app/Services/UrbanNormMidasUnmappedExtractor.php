<?php
declare(strict_types=1);
namespace App\Services;

final class UrbanNormMidasUnmappedExtractor
{
    public function from(string $text): array
    {
        $items = array_merge($this->unknownFields($text), $this->unknownSections($text));
        $seen = [];
        return array_values(array_filter($items, static function (array $item) use (&$seen): bool {
            $key = mb_strtolower(($item['section'] ?? '') . '|' . ($item['label'] ?? ''));
            if (isset($seen[$key])) return false;
            $seen[$key] = true;
            return trim((string) ($item['value'] ?? '')) !== '';
        }));
    }

    private function unknownFields(string $text): array
    {
        preg_match_all('/(?:^|\n)\s*(?:\d{1,2}\s+)?([\p{L}ÁÉÍÓÚÜÑáéíóúüñ][^\n:]{1,80}):\s*\n?\s*(.*?)(?=\n\s*(?:\d{1,2}\s+)?[\p{L}ÁÉÍÓÚÜÑáéíóúüñ][^\n:]{1,80}:|\n\s*USO\s+[A-ZÁÉÍÓÚÜÑ]+|\z)/su',
            $text, $matches, PREG_SET_ORDER);
        $known = array_flip(array_map([$this, 'key'], $this->knownFieldLabels()));
        $items = [];
        foreach ($matches as $match) {
            $label = trim((string) $match[1]);
            $value = $this->value((string) $match[2]);
            if ($value === '' || isset($known[$this->key($label)])) continue;
            $items[] = ['section' => 'Dato MIDAS sin destino', 'label' => $label, 'value' => $value];
        }
        return $items;
    }

    private function unknownSections(string $text): array
    {
        $known = array_flip(array_map([$this, 'key'], $this->knownSectionLabels()));
        preg_match_all('/(?:^|\n)\s*([A-ZÁÉÍÓÚÜÑ][A-ZÁÉÍÓÚÜÑ0-9 \/().,-]{2,70})\s*\n(.*?)(?=\n\s*[A-ZÁÉÍÓÚÜÑ][A-ZÁÉÍÓÚÜÑ0-9 \/().,-]{2,70}\s*\n|\n\s*(?:\d{1,2}\s+)?[\p{L}ÁÉÍÓÚÜÑáéíóúüñ][^\n:]{1,80}:|\z)/su',
            $text, $matches, PREG_SET_ORDER);
        $items = [];
        foreach ($matches as $match) {
            $label = trim((string) $match[1]);
            $value = $this->value((string) $match[2]);
            if ($value === '' || isset($known[$this->key($label)])) continue;
            $items[] = ['section' => 'Sección MIDAS no actualizada', 'label' => $label, 'value' => $value];
        }
        return $items;
    }

    private function knownFieldLabels(): array
    {
        return ['Número Predial Nacional', 'Matrícula Inmobiliaria', 'Dirección', 'Territorio',
            'Localidad', 'Unidad Comunera De Gobierno', 'Uso De Suelo', 'Tratamiento', 'Riesgos',
            'Clasificación Del Suelo', 'Código Manzana Dane', 'Lado Manzana Dane', 'Número De Manzana',
            'Número De Predio', 'Estrato Socioeconómico', 'Acta Estratificación',
            'Atipicidad Estratificación', 'Observación Estratificación', 'Nombre Edificación',
            'Área Terreno (M2)', 'Área Construida (M2)', 'Referencia Catastral', 'Fecha Actualización',
            'Cuadro de reglamentación de usos del suelo', 'Predio'];
    }

    private function knownSectionLabels(): array
    {
        return ['USO PRINCIPAL', 'USO COMPATIBLE', 'USO COMPLEMENTARIO', 'USO RESTRINGIDO',
            'USO PROHIBIDO', 'UNIDAD BÁSICA', 'USOS', 'AREA LIBRE', 'ÁREA LIBRE',
            'AREA Y FRENTE MÍNIMOS', 'ÁREA Y FRENTE MÍNIMOS', 'ALTURA MÁXIMA',
            'ÍNDICE DE CONSTRUCCIÓN', 'INDICE DE CONSTRUCCION', 'ÍNDICE DE OCUPACIÓN',
            'INDICE DE OCUPACION', 'ÁREA DE OCUPACIÓN', 'AREA DE OCUPACION', 'AISLAMIENTOS'];
    }

    private function key(string $label): string
    {
        $label = strtr(mb_strtolower($label), ['á' => 'a', 'é' => 'e', 'í' => 'i',
            'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
        return preg_replace('/[^a-z0-9]+/', '', $label) ?? $label;
    }

    private function value(string $value): string
    {
        $value = trim(preg_replace('/[ \t]+/', ' ', $value) ?? $value);
        $value = trim(preg_replace('/\n{3,}/', "\n\n", $value) ?? $value);
        return preg_match('/^-+$/', $value) ? '' : mb_substr($value, 0, 1200);
    }
}
