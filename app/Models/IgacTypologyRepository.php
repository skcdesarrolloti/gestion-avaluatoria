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
}
