<?php
declare(strict_types=1);
namespace App\Services;

final class MetrocuadradoAreaSearch
{
    public static function url(string $neighborhood): string
    {
        $slug = strtolower((string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', trim($neighborhood)));
        $slug = trim(preg_replace('/[^a-z0-9]+/', '-', $slug), '-');
        if (strlen($slug) < 3 || strlen($slug) > 100) throw new \InvalidArgumentException('Selecciona un barrio válido del catálogo.');
        if ($slug === 'boca-grande') $slug = 'bocagrande';
        return 'https://www.metrocuadrado.com/oficinas/venta/cartagena-de-indias/' . $slug . '/';
    }

    public function search(string $neighborhood): array
    {
        $url = self::url($neighborhood);
        return (new MetrocuadradoResultsParser())->parse((new MetrocuadradoReader())->fetch($url), $url);
    }
}
