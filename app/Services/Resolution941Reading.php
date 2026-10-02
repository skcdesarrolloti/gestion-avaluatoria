<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\IgacDocumentLibrary;

final class Resolution941Reading
{
    private const PAGES = [15 => [5, 5], 16 => [17, 17], 17 => [18, 19], 18 => [19, 20],
        19 => [20, 21], 20 => [21, 22], 21 => [22, 22],
        22 => [23, 23], 23 => [23, 24], 24 => [24, 24], 25 => [24, 24], 26 => [25, 26],
        27 => [26, 26], 28 => [26, 27], 29 => [27, 27], 30 => [28, 29],
        31 => [29, 30], 32 => [30, 30], 33 => [30, 32], 34 => [32, 34], 36 => [9, 9]];

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
            ...(in_array($number, [15, 36], true) ? [
                'source' => 'Diario Oficial · reproducción publicada por Camacol',
                'url' => 'https://camacol.co/sites/default/files/descargables/IGAC-Resolucion-2026-N0000941_20260731_Diario_Oficial-N053573_20260801.pdf#page=' . $pages[0],
            ] : []),
        ];
    }
}
