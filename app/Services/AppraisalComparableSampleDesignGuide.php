<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalComparableSampleDesignGuide
{
    public function build(array $factorGroups): array
    {
        return [
            'target_per_factor' => 15,
            'minimum_message' => 'Meta técnica: procurar mínimo 15 muestras por cada factor que se decida analizar. Si el mercado no lo permite, dejar constancia de la búsqueda, las fuentes agotadas y la limitación de la muestra.',
            'factor_targets' => $this->factorTargets($factorGroups),
            'protocol' => [
                'Definir primero los factores que realmente inciden en el bien sujeto; no abrir variables que no serán analizadas.',
                'Buscar muestras lo más parecidas posible antes de ampliar a microsectores, tipologías o condiciones alternas.',
                'Asignar cada muestra al factor que soporta: área, ubicación, PH, estado, parqueaderos, renta u otro diferencial relevante.',
                'Separar duplicados, datos incompletos, ofertas no verificables y muestras con derecho o uso no comparable.',
            ],
            'statistics' => [
                'Usar medidas robustas de tendencia central: mediana, media recortada o media depurada según dispersión.',
                'Revisar dispersión con desviación estándar, coeficiente de variación, rango intercuartílico y concentración de la muestra.',
                'Aplicar intervalo con distribución t de Student cuando el tamaño muestral y la calidad de datos lo permitan.',
                'Calcular MAPE u otra métrica de error cuando exista modelo, backtesting o contraste entre estimado y observado.',
                'Documentar outliers, puntos influyentes y motivos de exclusión sin hablar de homologación automática.',
            ],
            'next_84' => [
                '8.4 debe recibir muestras depuradas, factor asignado, unidad de comparación y observación técnica.',
                'Allí se calcula dispersión, tendencia central robusta, intervalo, sensibilidad y validación del modelo.',
            ],
        ];
    }

    private function factorTargets(array $factorGroups): array
    {
        $targets = [];
        foreach ($factorGroups as $group => $items) {
            $targets[] = [
                'key' => $this->key((string) $group),
                'label' => (string) $group,
                'variables' => array_values(array_map('strval', is_array($items) ? $items : [])),
                'target' => 15,
            ];
        }
        if ($targets === []) {
            $targets[] = [
                'key' => 'factor_base',
                'label' => 'Factor base',
                'variables' => ['Tipología, operación, ubicación, área y soporte verificable.'],
                'target' => 15,
            ];
        }
        return $targets;
    }

    private function key(string $label): string
    {
        $label = mb_strtolower($label);
        $label = strtr($label, ['á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u', 'ñ'=>'n']);
        $label = preg_replace('/[^a-z0-9]+/u', '_', $label) ?? '';
        $label = trim($label, '_');
        return $label !== '' ? mb_substr($label, 0, 80) : 'factor';
    }
}
