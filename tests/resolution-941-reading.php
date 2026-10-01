<?php
declare(strict_types=1);

(static function (): void {
    $reading = \App\Services\Resolution941Reading::article(21);
    $text = implode("\n", $reading['paragraphs']);
    expect(str_contains($text, '7.50%') && str_contains($text, '10.0%')
        && str_contains($text, 'Parágrafo 2.') && str_contains($text, 'No se presumirá'),
        'consulta del art. 21 conserva límites y salvedades completas');
    expect(str_ends_with($reading['url'], '#page=22')
        && \App\Services\Resolution941Reading::article(999) === null,
        'consulta normativa enlaza página original y limita artículos disponibles');
    $readingNumber = 16;
    ob_start();
    require BASE_PATH . '/app/Views/appraisals/valuation-methodology-article-reading.php';
    $html = ob_get_clean();
    expect(str_contains($html, 'clasificadas, analizadas e interpretadas')
        && str_contains($html, 'tabindex="0"') && !preg_match('/<details[^>]*\bopen\b/', $html),
        'artículo íntegro inicia cerrado y admite lectura por teclado');
})();
