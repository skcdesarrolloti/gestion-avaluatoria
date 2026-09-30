<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalMethodologyResolution941Guide
{
    public function __construct(private ?AppraisalMethodologyResolution941Articles $articles = null) {}

    public function methodGuides(): array
    {
        return [
            $this->market(),
            $this->income(),
            $this->cost(),
            $this->residual(),
        ];
    }

    public function inputsForMethod(string $method): array
    {
        $key = $this->keyForMethod($method);
        $guide = array_values(array_filter($this->methodGuides(),
            static fn (array $item): bool => ($item['key'] ?? '') === $key))[0] ?? $this->market();
        return [
            'method' => (string) ($guide['label'] ?? 'Método'),
            'articles' => (string) ($guide['articles'] ?? ''),
            'items' => $guide['decision_inputs'] ?? [],
            'article_cards' => $this->articles()->for($key),
        ];
    }

    public function selectionNotice(): string
    {
        return 'La selección del método parte del artículo 15 de la Resolución IGAC 941 de 2026: mercado, renta, costo o técnica residual. En 8.2 no se calcula el valor; se verifica si existen los insumos mínimos para desarrollar el método escogido y se deja trazabilidad de fuentes, soportes y limitaciones.';
    }

    private function market(): array
    {
        return [
            'key' => 'mercado',
            'label' => 'Mercado',
            'articles' => 'Arts. 16 a 21',
            'summary' => 'Compara ofertas o transacciones recientes de inmuebles similares; exige clasificar, analizar, interpretar y soportar cada dato.',
            'article_cards' => $this->articles()->for('mercado'),
            'decision_inputs' => [
                'Ofertas o transacciones recientes, comparables y verificables.',
                'Ubicación, valor pedido o transado, áreas, fuente, URL o contacto y fecha de captura.',
                'Separación PH/NPH: área privada integral en PH; terreno, construcción y anexos en NPH cuando aplique.',
                'Depuración estadística, criterio de adopción del valor y soportes de pantalla, fotos o campo.',
            ],
            'parts' => [
                $this->part('comprende', 'Qué comprende', [
                    'Identificar el mercado relevante del bien sujeto: ciudad, barrio, uso, tipología, derecho y fecha.',
                    'Recopilar datos de ofertas o transacciones recientes y comparables, no inmuebles lejanos o de uso distinto sin justificación.',
                    'Analizar la muestra como evidencia de mercado; no presentar homologaciones automáticas.',
                ]),
                $this->part('insumos', 'Insumos mínimos', [
                    'Ubicación precisa o la mayor localización disponible, con fuente expresa.',
                    'Valor de oferta o transacción, áreas de terreno, construcción, anexos, garajes, depósitos y áreas libres.',
                    'Fuente verificable: portal, inmobiliaria, contacto, captura de pantalla, fotografía, URL y fecha de consulta.',
                ]),
                $this->part('desarrollo', 'Desarrollo técnico', [
                    'Distinguir inmuebles PH y no PH antes de calcular valores unitarios.',
                    'Aplicar depuración, medidas estadísticas y lectura de mercado; si se usan modelos, documentar supuestos, error y validación.',
                    'Adoptar media, mediana u otro estadístico solo con sustento en comparabilidad, dispersión y comportamiento del mercado.',
                ]),
                $this->part('cierre', 'Cierre en informe', [
                    'Dejar trazabilidad de cada comparable aceptado o descartado.',
                    'Explicar ajustes por ubicación, área, estado, uso, PH, parqueaderos, depósitos y demás diferenciales relevantes.',
                    'Conservar anexos probatorios suficientes para que el lector pueda verificar la muestra.',
                ]),
            ],
        ];
    }

    private function income(): array
    {
        return [
            'key' => 'renta',
            'label' => 'Renta',
            'articles' => 'Arts. 22 a 26',
            'summary' => 'Estima valor desde rentas o beneficios económicos; puede usar capitalización directa o flujo de caja descontado.',
            'article_cards' => $this->articles()->for('renta'),
            'decision_inputs' => [
                'Canon real o canon de mercado, periodicidad, ocupación, vacancia y soportes de contrato u oferta.',
                'Gastos y deducciones: administración, predial, seguros, mantenimiento, IVA y costos operativos.',
                'Tasa de capitalización o tasa de descuento sustentada con mercado o construcción técnica.',
                'Coherencia bruta/bruta o neta/neta; excluir rentas de intangibles no inmobiliarios.',
            ],
            'parts' => [
                $this->part('comprende', 'Qué comprende', [
                    'Aplicar cuando el inmueble produce o puede producir ingresos inmobiliarios verificables.',
                    'Distinguir capitalización directa para una renta estabilizada y FCD para flujos variables o proyectos con etapas.',
                    'Mantener separada la renta del negocio o intangible frente a la renta atribuible al inmueble.',
                ]),
                $this->part('insumos', 'Insumos mínimos', [
                    'Canon, periodo de pago, administración, IVA, gastos, vacancia, fecha y soporte documental.',
                    'Rentas comparables y precios de venta comparables cuando se derive tasa por observación directa.',
                    'Supuestos de crecimiento, egresos, horizonte, valor terminal y tasa si se usa FCD.',
                ]),
                $this->part('desarrollo', 'Desarrollo técnico', [
                    'Usar renta neta con tasa neta o renta bruta con tasa bruta; no mezclar magnitudes.',
                    'Verificar topes legales cuando se trate de vivienda urbana arrendada.',
                    'Contrastar la renta efectiva informada con evidencia de mercado cuando exista.',
                ]),
                $this->part('cierre', 'Cierre en informe', [
                    'Documentar fuente de canon, gastos, tasa y ocupación.',
                    'Explicar si la renta se adopta como método principal o como antecedente económico.',
                    'Declarar salvedades cuando falten contratos, estados de pago o soportes verificables.',
                ]),
            ],
        ];
    }

    private function cost(): array
    {
        return [
            'key' => 'costo',
            'label' => 'Costo',
            'articles' => 'Arts. 27 a 30',
            'summary' => 'Suma valor del terreno y valor actual de construcciones o anexos, descontando depreciación y obsolescencia.',
            'article_cards' => $this->articles()->for('costo'),
            'decision_inputs' => [
                'Valor del terreno por método viable y soporte de mercado o norma aplicable.',
                'Costo nuevo de reposición o reproducción, con costos directos e indirectos verificables.',
                'Edad, vida útil, estado de conservación, depreciación física, funcional y económica.',
                'Planos, tipologías IGAC, presupuestos, bases técnicas y fotografías de soporte.',
            ],
            'parts' => [
                $this->part('comprende', 'Qué comprende', [
                    'Aplicar cuando la construcción, mejora o anexo requiere lectura propia: piscina, cubierta, cerramiento, equipo fijo o edificación especial.',
                    'Distinguir reposición con materiales actuales y reproducción cuando deba conservarse una réplica, por ejemplo en BIC.',
                    'No sustituye el mercado del bien principal si el anexo solo se integra al comparable por decisión del analista.',
                ]),
                $this->part('insumos', 'Insumos mínimos', [
                    'Medición del componente, unidad de costo, cantidades, especificaciones y fecha de precios.',
                    'Fuentes de costo: presupuesto, publicaciones técnicas, bases de datos, tipología IGAC o soporte de obra.',
                    'Edad aparente, vida útil, mantenimiento, estado de obra y obsolescencias observadas.',
                ]),
                $this->part('desarrollo', 'Desarrollo técnico', [
                    'Calcular costo total a nuevo, depreciación acumulada y valor actual del componente.',
                    'Relacionar el terreno por el método que corresponda y evitar doble conteo con el método de mercado.',
                    'Usar modelos continuos de depreciación y dejar constancia cuando falten planos o mediciones completas.',
                ]),
                $this->part('cierre', 'Cierre en informe', [
                    'Explicar por qué el componente se valora por reposición/costo y no integrado al comparable.',
                    'Conservar memoria de cantidades, precios unitarios, depreciación y fotografía.',
                    'Separar valor de terreno, construcciones y anexos cuando esa apertura sea necesaria.',
                ]),
            ],
        ];
    }

    private function residual(): array
    {
        return [
            'key' => 'residual',
            'label' => 'Residual',
            'articles' => 'Arts. 31 a 34',
            'summary' => 'Estima el valor desde el producto inmobiliario factible, descontando costos, cargas, utilidad y tiempos de desarrollo.',
            'article_cards' => $this->articles()->for('residual'),
            'decision_inputs' => [
                'Norma urbana, afectaciones, cesiones, área neta, área útil, edificabilidad y usos.',
                'Producto inmobiliario vendible y precios de venta sustentados por mercado.',
                'Costos de urbanismo, construcción, indirectos, financieros, gerencia, comercialización y cargas.',
                'Técnica estática o dinámica, tiempos, utilidad esperada, TIR sectorial y VPN no negativo.',
            ],
            'parts' => [
                $this->part('comprende', 'Qué comprende', [
                    'Aplicar en lotes, suelos de desarrollo o inmuebles cuyo valor depende del aprovechamiento posible.',
                    'Trabajar bajo mayor y mejor uso: legalmente permitido, físicamente posible, financieramente factible y demandado por el mercado.',
                    'Distinguir residual estático para ciclos cortos y residual dinámico cuando el tiempo cambia ingresos y egresos.',
                ]),
                $this->part('insumos', 'Insumos mínimos', [
                    'POT o norma urbana, tratamientos, usos, alturas, aislamientos, cargas, cesiones y restricciones.',
                    'Áreas vendibles, mezcla de producto, precios esperados y velocidad de venta.',
                    'Presupuesto de urbanismo, construcción, costos indirectos, financieros, utilidad y cronograma.',
                ]),
                $this->part('desarrollo', 'Desarrollo técnico', [
                    'Calcular ingresos totales del producto final con soporte de mercado.',
                    'Restar costos, cargas y utilidad esperada; en dinámico descontar flujos a una tasa sustentada.',
                    'Usar valor de terreno en bruto cuando corresponda y la información permita aplicar la fórmula.',
                ]),
                $this->part('cierre', 'Cierre en informe', [
                    'Justificar la factibilidad normativa y económica del escenario adoptado.',
                    'Mostrar memoria de áreas, ingresos, costos, tiempos, utilidad, tasa y sensibilidad relevante.',
                    'Aclarar que el resultado depende de supuestos de desarrollo y condiciones de mercado a la fecha.',
                ]),
            ],
        ];
    }

    private function part(string $key, string $label, array $bullets): array
    {
        return ['key' => $key, 'label' => $label, 'bullets' => $bullets];
    }

    private function keyForMethod(string $method): string
    {
        $method = mb_strtolower($method);
        if (str_contains($method, 'renta') || str_contains($method, 'capitalizacion') || str_contains($method, 'capitalización')) return 'renta';
        if (str_contains($method, 'residual')) return 'residual';
        if (str_contains($method, 'costo') || str_contains($method, 'reposicion') || str_contains($method, 'reposición')) return 'costo';
        return 'mercado';
    }

    private function articles(): AppraisalMethodologyResolution941Articles
    {
        return $this->articles ??= new AppraisalMethodologyResolution941Articles();
    }
}
