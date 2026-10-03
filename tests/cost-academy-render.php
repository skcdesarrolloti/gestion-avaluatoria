<?php
declare(strict_types=1);
(static function(): void {
    $method='costo'; $methods=\App\Services\MethodologyWorkflow::METHODS;
    ob_start(); require BASE_PATH.'/app/Views/appraisals/methodology-cost-academy-reading.php'; $html=ob_get_clean();
    expect(str_contains($html,'Guía amplia para el analista') && str_contains($html,'C1 Academia · Costo')
        && substr_count($html,'Leer artículo 27 completo')===1 && substr_count($html,'Leer artículo 30 completo')===1,
        'Costo comparte presentación y tarjetas normativas sin duplicar lectores completos');
    foreach (\App\Services\CostMethodAcademy::topics() as $topic) {
        expect(substr_count($html,'id="costo-academia-'.$topic['id'].'"')===1,
            'academia conserva el tema '.$topic['id'].' una sola vez');
        foreach ($topic['items'] as $bullet) expect(str_contains($html,e($bullet)),
            'academia conserva contenido de '.$topic['id']);
        foreach ($topic['rows'] ?? [] as $row) foreach ($row as $cell) expect(str_contains($html,e($cell)),
            'academia conserva tabla de '.$topic['id']);
    }
    expect(str_contains($html,'aria-label="Apartados del costo"') && str_contains($html,'aria-label="Temas de Vidas útiles"')
        && str_contains($html,'aria-label="Temas de Depreciación"') && str_contains($html,"costTopic = 'remanente'")
        && str_contains($html,"costTopic = 'retiro'"),'vidas, depreciación y retiro accesibles desde subtemas dependientes');
    expect(str_contains($html,'assets/normativa/igac-941-anexo-costo.pdf') && str_contains($html,'Descargar apartado completo')
        && !preg_match('/<details[^>]*\sopen(?:\s|>)/',$html),'anexo íntegro y descarga conservados, lectores cerrados');
})();
