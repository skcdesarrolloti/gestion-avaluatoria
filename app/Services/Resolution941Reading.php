<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\IgacDocumentLibrary;

final class Resolution941Reading
{
    private const PAGES = [1 => [2, 2], 2 => [2, 2], 3 => [2, 2], 4 => [2, 2], 5 => [2, 2], 6 => [2, 2], 7 => [2, 2], 8 => [2, 2], 9 => [2, 2], 10 => [2, 2], 11 => [2, 3], 12 => [3, 3], 13 => [3, 4], 14 => [4, 5], 35 => [9, 9], 39 => [9, 9], 57 => [13, 14], 58 => [14, 14], 59 => [14, 14], 60 => [14, 14], 61 => [14, 14],
        15 => [5, 5], 16 => [17, 17], 17 => [18, 19], 18 => [19, 20],
        19 => [20, 21], 20 => [21, 22], 21 => [22, 22],
        22 => [23, 23], 23 => [23, 24], 24 => [24, 24], 25 => [24, 24], 26 => [25, 26],
        27 => [26, 26], 28 => [26, 27], 29 => [27, 27], 30 => [28, 29],
        31 => [29, 30], 32 => [30, 30], 33 => [30, 32], 34 => [32, 34], 36 => [9, 9], 37 => [9, 9], 38 => [9, 9], 40 => [9, 10], 41 => [10, 10], 42 => [10, 10]];

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
            ...(in_array($number, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 35, 39, 57, 58, 59, 60, 61, 15, 36, 37, 38, 40, 41, 42], true) ? [
                'source' => 'Diario Oficial · reproducción publicada por Camacol',
                'url' => 'https://camacol.co/sites/default/files/descargables/IGAC-Resolucion-2026-N0000941_20260731_Diario_Oficial-N053573_20260801.pdf#page=' . $pages[0],
            ] : []),
        ];
    }
}
