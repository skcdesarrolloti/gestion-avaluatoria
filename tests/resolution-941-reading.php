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
    $required = [23 => ['A = r / i', 'Parágrafo'], 25 => ['Tasa de descuento', 'Tasa terminal'],
        27 => ['VC = (CT − D) + VT'], 28 => ['Parágrafo 1', 'Parágrafo 2'],
        30 => ['VA = Vn', 'no estarán sujetas', 'intervenciones'],
        33 => ['9. Presentación', 'Parágrafo', 'no se le deberá adicionar'],
        34 => ['ni resulte aplicable', '%AU = (AT − AF − C) / AT', 'Cu = Costos']];
    foreach ($required as $number => $phrases) {
        $text = implode("\n", \App\Services\Resolution941Reading::article($number)['paragraphs']);
        foreach ($phrases as $phrase) expect(str_contains($text, $phrase), "Art. $number conserva condición o fórmula: $phrase");
    }
    $methodologyGuides = (new \App\Services\AppraisalMethodologyResolution941Guide())->methodGuides();
    ob_start(); require BASE_PATH . '/app/Views/appraisals/valuation-methodology-method-guides.php'; $html = ob_get_clean();
    foreach (range(16, 34) as $number) expect(str_contains($html, "Leer artículo $number completo"), "Guías muestran lectura íntegra art. $number");
    foreach (['Mercado', 'Renta', 'Costo', 'Residual'] as $method)
        expect(str_contains($html, "Revisión de $method · requisitos y pendientes"), "Revisión diferenciada de $method");
})();
