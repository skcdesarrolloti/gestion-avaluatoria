<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\IgacDocumentLibrary;

final class Resolution941Reading
{
    private const PAGES = [16 => [17, 17], 17 => [18, 19], 18 => [19, 20],
        19 => [20, 21], 20 => [21, 22], 21 => [22, 22]];

    public static function article(int $number): ?array
    {
        if (!isset(self::PAGES[$number])) return null;
        $pages = self::PAGES[$number];
        $text = file_get_contents(BASE_PATH . '/resources/data/resolution-941/article-' . $number . '.txt');
        if ($text === false) throw new \RuntimeException('No se encontró el artículo de consulta.');
        $document = IgacDocumentLibrary::find('resolucion-igac-941-2026');
        return [
            'number' => $number,
            'paragraphs' => explode("\n\n", trim($text)),
            'pages' => $pages[0] === $pages[1] ? (string) $pages[0] : implode('–', $pages),
            'url' => $document['archivo_descarga'] . '#page=' . $pages[0],
        ];
    }
}
