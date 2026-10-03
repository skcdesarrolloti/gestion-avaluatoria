<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\IgacDocumentLibrary;

final class Resolution941Reading
{
    private const PURPOSES = [
        1 => 'Precisar el objeto de la resolución.',
        2 => 'Identificar cuándo es obligatoria su aplicación.',
        3 => 'Consultar el soporte del anexo técnico.',
        4 => 'Aclarar los términos utilizados en el avalúo.',
        5 => 'Orientar el avalúo con sus principios generales.',
        6 => 'Verificar quién solicita y realiza el avalúo.',
        7 => 'Definir el marco jurídico del encargo.',
        8 => 'Comprobar los documentos necesarios para la solicitud.',
        9 => 'Consultar el plazo de entrega del avalúo.',
        10 => 'Orientar la visita e inspección del inmueble.',
        11 => 'Verificar los parámetros mínimos de valoración.',
        12 => 'Identificar el inmueble física, jurídica y normativamente.',
        13 => 'Ordenar las etapas de elaboración del avalúo.',
        14 => 'Comprobar el contenido mínimo del informe.',
        15 => 'Identificar los métodos valuatorios aplicables.',
        16 => 'Comprender cómo se valora por mercado.',
        17 => 'Identificar los datos necesarios de cada comparable.',
        18 => 'Analizar valores integrales en inmuebles sin PH.',
        19 => 'Evaluar y depurar los datos de mercado.',
        20 => 'Orientar el análisis estadístico de los comparables.',
        21 => 'Sustentar la adopción del valor de mercado.',
        22 => 'Comprender la valoración mediante ingresos.',
        23 => 'Estimar el valor mediante capitalización directa.',
        24 => 'Verificar los insumos de capitalización directa.',
        25 => 'Comprender la valoración mediante flujos descontados.',
        26 => 'Ordenar la aplicación del flujo de caja descontado.',
        27 => 'Distinguir terreno, construcciones y anexos en Costo.',
        28 => 'Determinar costos de reposición o reproducción a nuevo.',
        29 => 'Sustentar la vida útil y remanente.',
        30 => 'Calcular la depreciación con Ross–Heideck.',
        31 => 'Comprender la valoración residual del terreno.',
        32 => 'Elegir entre residual estático y dinámico.',
        33 => 'Verificar los insumos del método residual.',
        34 => 'Orientar la estimación de terrenos en bruto.',
        35 => 'Orientar avalúos de uso dotacional o institucional.',
        36 => 'Definir el tratamiento de áreas y anexos en PH.',
        37 => 'Orientar la valoración de inmuebles sin PH.',
        38 => 'Orientar la determinación del canon de arrendamiento.',
        39 => 'Verificar si el inmueble corresponde a VIS.',
        40 => 'Orientar la valoración de bienes de interés cultural.',
        41 => 'Valorar terrenos en áreas de expansión urbana.',
        42 => 'Orientar la valoración de maquinaria y equipos.',
        57 => 'Consultar la revisión e impugnación del avalúo.',
        58 => 'Identificar la entrega de información al OIC.',
        59 => 'Determinar el régimen de los trámites anteriores.',
        60 => 'Consultar la vigencia y las normas derogadas.',
        61 => 'Identificar la publicación oficial de la resolución.',
    ];
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
            'purpose' => self::PURPOSES[$number],
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
