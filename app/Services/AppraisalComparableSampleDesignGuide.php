<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalComparableSampleDesignGuide
{
    public function build(array $factorGroups, string $type = ''): array
    {
        return [
            'target_per_factor' => 15,
            'minimum_message' => 'La referencia de 15 muestras por factor es una meta interna de organización, no un mínimo exigido por la Resolución 941. El avaluador debe justificar la suficiencia y comparabilidad de los datos y consignar sus limitaciones (art. 20).',
            'factor_targets' => $this->factorTargets($factorGroups),
            'priority_factors' => $this->priorityFactors($type),
            'compliance_articles' => $this->complianceArticles(),
            'sample_plan' => [
                'Base comparable' => 'Arranca con muestras de la misma operación, ciudad, barrio o microsector, tipología, derecho y unidad de comparación.',
                'Ampliación controlada' => 'Si no hay datos suficientes, amplía por anillos: mismo barrio, barrios sustitutos, misma ciudad y fuente regional, dejando trazabilidad.',
                'Depuración' => 'Clasifica cada dato como preseleccionado, usado o descartado. No mezcles ofertas sin verificar con transacciones o fuentes confirmadas.',
                'Cierre para M4' => 'Entrega registros depurados con sus soportes y salvedades. La cantidad de filas no acredita suficiencia por sí sola.',
            ],
            'protocol' => [
                'Definir primero los factores que realmente inciden en el bien sujeto; no abrir variables que no serán analizadas.',
                'Buscar muestras lo más parecidas posible antes de ampliar a microsectores, tipologías o condiciones alternas.',
                'Asignar cada muestra al factor que soporta: área, ubicación, PH, estado, parqueaderos, renta u otro diferencial relevante.',
                'Separar duplicados, datos incompletos, ofertas no verificables y muestras con derecho o uso no comparable.',
                'Georreferenciar las muestras usadas o preseleccionadas para revisar concentración espacial, dispersión y comparabilidad del sector.',
            ],
            'statistics' => [
                'Elegir y sustentar las medidas pertinentes: el art. 20 las presenta como optativas y complementarias, no como reglas automáticas.',
                'Revisar dispersión con desviación estándar, coeficiente de variación, rango intercuartílico y concentración de la muestra.',
                'Aplicar intervalo con distribución t de Student cuando el tamaño muestral y la calidad de datos lo permitan.',
                'Calcular MAPE u otra métrica de error cuando exista modelo, backtesting o contraste entre estimado y observado.',
                'Documentar valores atípicos y motivos de exclusión. El anexo 2.1 excluye la homologación mediante factores, sea manual o automática.',
            ],
            'next_84' => [
                'M4 debe recibir muestras depuradas, factor asignado, unidad de comparación y observación técnica.',
                'Desarrollo pendiente: memoria reproducible, estadísticos pertinentes y justificación del valor conforme a los arts. 17–21.',
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

    private function complianceArticles(): array
    {
        return [
            ['Art. 16', 'La muestra debe provenir de mercado comparable: ofertas o transacciones recientes de bienes similares.'],
            ['Art. 17', 'Cada dato requiere ubicación, precio, área, fuente, fecha, contacto o evidencia de consulta verificable.'],
            ['Art. 18', 'En NPH el análisis integral es complementario: exige documentar y justificar relaciones comparables entre áreas de terreno y construcción.'],
            ['Art. 19', 'En PH se analiza el valor por m² privado tras descontar las unidades complementarias del dato negociado; revisar las excepciones y la liquidación del art. 36.'],
            ['Art. 20', 'La depuración debe preparar medidas robustas, dispersión, outliers y modelos auditables cuando aplique.'],
            ['Art. 21', 'La adopción posterior debe justificar el estadístico usado y la dispersión observada; M3 deja trazabilidad para esa decisión.'],
        ];
    }

    private function priorityFactors(string $type): array
    {
        return [
            'apartamento' => ['Área privada/adoptada', 'Barrio y edificio comparable', 'Piso, vista y estado', 'PH, administración, amenidades y parqueaderos'],
            'casa' => ['Área de lote y construcción', 'Microsector y norma urbana', 'Estado, vetustez y acabados', 'Anexos, patios, terraza o mejoras relevantes'],
            'lote' => ['Área de terreno', 'Frente, fondo, forma y topografía', 'Uso permitido y tratamiento', 'Servicios, vía de acceso y afectaciones'],
            'local' => ['Corredor o vitrina comercial', 'Área útil y frente', 'Flujo, esquina y exposición', 'Administración, parqueaderos y soporte común'],
            'oficina' => ['Área privada verificada', 'Edificio, piso e imagen corporativa', 'Parqueaderos y administración', 'Ascensor, seguridad y planta eléctrica'],
            'consultorio' => ['Área y edificio de servicios', 'Acceso de usuarios y parqueaderos', 'Recepción, ascensor y baños', 'Compatibilidad con uso médico o profesional'],
            'bodega' => ['Área operativa y altura libre', 'Acceso de carga, muelles y patio', 'Norma industrial o logística', 'Capacidad eléctrica, pisos y seguridad'],
        ][$type] ?? ['Tipología y operación', 'Ubicación y área', 'Estado y atributos diferenciales', 'Soporte jurídico, fuente y trazabilidad'];
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
