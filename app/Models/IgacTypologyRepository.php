<?php
declare(strict_types=1);
namespace App\Models;

final class IgacTypologyRepository
{
    private const DATA_FILE = BASE_PATH . '/resources/data/tipologias_igac_catalogo.json';
    private const LABELS = [
        'RESIDENCIALES' => 'Residenciales',
        'COMERCIALES' => 'Comerciales',
        'INDUSTRIALES' => 'Industriales',
        'INSTITUCIONALES' => 'Institucionales',
        'EDIFICIOS' => 'Edificios',
        'ANEXOS' => 'Anexos',
    ];

    public function categories(): array
    {
        $counts = [];
        foreach ($this->all() as $item) {
            $counts[$item['category_code']] = ($counts[$item['category_code']] ?? 0) + 1;
        }
        $categories = [];
        foreach (self::LABELS as $code => $label) {
            $categories[] = ['code' => $code, 'name' => $label, 'count' => $counts[$code] ?? 0];
        }
        return $categories;
    }

    public function byCategory(string $categoryCode): array
    {
        return array_values(array_filter($this->all(), static fn (array $item): bool =>
            $item['category_code'] === $categoryCode
        ));
    }

    public function candidates(array $record, int $limit = 6): array
    {
        $text = $this->normalize(implode(' ', [
            $record['titulo'] ?? '', $record['tipo_inmueble'] ?? '', $record['destinacion'] ?? '',
            $record['subtipo_funcional'] ?? '', $record['igac_typology_hint'] ?? '',
            $record['inspection_notes'] ?? '', $record['observaciones'] ?? '',
        ]));
        $preferred = $this->preferredCategories($record, $text);
        $items = [];
        foreach ($this->all() as $item) {
            $haystack = $this->normalize(implode(' ', [$item['denomination'], $item['description'],
                $item['specifications'], $item['category_name']]));
            $score = in_array($item['category_code'], $preferred, true) ? 25 : 0;
            if ($item['category_code'] === 'RESIDENCIALES' && str_contains($text, 'lote')) {
                if (preg_match('/tipo\s+(0|1|2)\b/', $haystack)) $score += 15;
            }
            if ($item['category_code'] === 'ANEXOS' && str_contains($text, 'lote')) {
                if (preg_match('/(cimientos|cerramiento|urbanismo|cocina|bano|deposito)/', $haystack)) $score += 10;
            }
            foreach ($this->tokens($text) as $token) {
                if (mb_strlen($token) >= 4 && str_contains($haystack, $token)) $score += 8;
            }
            if ($score === 0 && $preferred !== []) continue;
            $item['match_score'] = $score;
            $items[] = $item;
        }
        usort($items, static fn (array $a, array $b): int =>
            ($b['match_score'] <=> $a['match_score']) ?: strcmp($a['denomination'], $b['denomination']));
        return array_slice($items, 0, $limit);
    }

    public function stats(): array
    {
        $items = $this->all();
        return ['total' => count($items), 'categories' => count(array_filter($this->categories(),
            static fn (array $category): bool => $category['count'] > 0))];
    }

    private function all(): array
    {
        static $items = null;
        if ($items !== null) return $items;
        if (!is_file(self::DATA_FILE)) {
            throw new \RuntimeException('No se encontró el catálogo IGAC de tipologías.');
        }
        $decoded = json_decode((string) file_get_contents(self::DATA_FILE), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw new \RuntimeException('El catálogo IGAC no tiene un formato válido.');
        }
        $items = array_map(fn (array $item): array => $this->hydrate($item), $decoded);
        return $items;
    }

    private function hydrate(array $item): array
    {
        $category = (string) ($item['categoria'] ?? '');
        $image = basename(str_replace('\\', '/', (string) ($item['imagen'] ?? '')));
        return [
            'category_code' => $category,
            'category_name' => self::LABELS[$category] ?? ucfirst(mb_strtolower($category)),
            'denomination' => (string) ($item['denominacion'] ?? ''),
            'description' => (string) ($item['descripcion'] ?? ''),
            'specifications' => (string) ($item['especificaciones'] ?? ''),
            'useful_life' => (string) ($item['vidaUtil'] ?? ''),
            'unit' => (string) ($item['unidad'] ?? ''),
            'image_filename' => $image,
        ];
    }

    private function preferredCategories(array $record, string $text): array
    {
        $selected = (string) ($record['igac_category'] ?? '');
        if ($selected !== '') return [$selected];
        $rules = [
            'RESIDENCIALES' => ['casa', 'apartamento', 'residencial', 'vivienda', 'lote urbano'],
            'COMERCIALES' => ['local', 'oficina', 'comercial', 'hotel', 'hospedaje'],
            'INDUSTRIALES' => ['industrial', 'bodega', 'planta', 'fabrica'],
            'INSTITUCIONALES' => ['institucional', 'salud', 'educacion', 'religioso'],
            'EDIFICIOS' => ['edificio', 'multifamiliar'],
            'ANEXOS' => ['anexo', 'ramada', 'cobertizo', 'caney', 'galpon', 'caseta', 'kiosco', 'cerca'],
        ];
        foreach ($rules as $category => $words) {
            foreach ($words as $word) if (str_contains($text, $this->normalize($word))) return [$category];
        }
        if (str_contains($text, 'lote')) return ['RESIDENCIALES', 'ANEXOS'];
        return ['RESIDENCIALES', 'ANEXOS'];
    }

    private function tokens(string $text): array
    {
        return array_values(array_unique(array_filter(preg_split('/\s+/', $text) ?: [])));
    }

    private function normalize(string $text): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', mb_strtolower($text)) ?: mb_strtolower($text);
        return preg_replace('/[^a-z0-9]+/', ' ', $value) ?? $value;
    }
}
